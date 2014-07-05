<?php
class Cron extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Cron()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();
        
        $this->load->model("Users_model");
        $this->load->model("email_model");
        $this->load->model("property_model");
		$this->load->model("keydate_model");
		
    }
   
    function send_users_to_mailchimp()
    {
        // Get a list of users that need sending to mail chimp
        $sql = "SELECT u1.user_id, u1.first_name, u1.last_name, u1.email, u1.company_name, s.name as state, " .
            "CASE(u1.user_type_id) WHEN 1 Then 'Admin' WHEN 2 Then 'Staff' WHEN 3 Then 'Advisor' WHEN 4 Then 'Suppplier' WHEN 5 Then 'Partner' WHEN 6 Then 'Investor' WHEN 7 Then 'Enquiry' END as user_type, " .
            "u2.user_type_id as parent_user_type_id, u2.first_name as parent_first_name, u2.last_name as parent_last_name, u2.company_name as parent_company_name, ".
            "u3.user_type_id as grandfather_user_type_id, u3.first_name as grandfather_first_name, u3.last_name as grandfather_last_name, u3.company_name as grandfather_company_name ".
            "FROM nc_users u1 " .
            "LEFT OUTER JOIN nc_states s ON u1.billing_state_id = s.state_id  " .
            "LEFT OUTER JOIN nc_users u2 ON u1.created_by_user_id = u2.user_id " .
            "LEFT OUTER JOIN nc_users u3 ON u2.created_by_user_id = u3.user_id " .
            "WHERE u1.email <> '' " .
            "AND (SELECT COUNT(user_id) FROM nc_users WHERE email = u1.email) = 1"; // If the same email address is in the database more than once, ignore that email.
        
        
        $users = $this->db->query($sql);
        if(!$users) return;
        
        require_once("classes/mailchimp.class.php");
        
        $objMC = new MailChimp(MAILCHIMP_APIKEY, MAILCHIMP_LISTID);
        
        $counter = 0;
        
        foreach($users->result() as $user)
        {
            $email = $user->email;
            
            $data["FNAME"] = $user->first_name;    
            $data["LNAME"] = $user->last_name;
            $data["MMERGE3"] = $user->user_type;
            $data["MMERGE6"] = $user->state;
            $data["MMERGE7"] = $user->company_name;
            
            if($user->parent_user_type_id == USER_TYPE_ADVISOR)
            {
                // Set MailChimp Advisor Name
                $data["MMERGE4"] = $user->parent_first_name . " " . $user->parent_last_name;    
            }
            else if($user->parent_user_type_id == USER_TYPE_PARTNER)
            {
                // Set MailChimp Partner Name
                $data["MMERGE5"] = $user->parent_first_name . " " . $user->parent_last_name;    
            } 
            
            if($user->grandfather_user_type_id == USER_TYPE_ADVISOR)
            {
                // Set MailChimp Advisor Name
                $data["MMERGE4"] = $user->grandfather_first_name . " " . $user->grandfather_last_name;    
            } 
            
            $objMC->sendToMailChimp($email, $data, $error_code);
            
            if(($error_code == "") || ($error_code == MCERROR_ALREADYSUBSCRIBED))
            {
                // Update the database to say this user is subscribed
                $data = array("subscribed" => 1);
                $this->db->where("user_id", $user->user_id);
                $this->db->update("users", $data);
                
                echo "Subscribed $email<br>";
            }  
            
            $counter++;                 
        }
        
        mail("andy@simb.com.au", "Aspire - MailChimp Cron Task", "$counter Aspire Network users have been sent to MailChimp OK"); 
    }
	
	function check_expired_user()
	{
		$check_user = $this->users_model->expired_users();
		
		$user_id_array = array();
		if($check_user != "")
		{
			foreach($check_user as $user)
			{
				
				$user_id = $user->user_id;
				$user_type_id = $user->user_type_id;
				$email = $user->email;
				$first_name = $user->first_name;	
					
					
					$advisor = $this->users_model->get_user_advisor($user_id, $user_type_id);
								
						$adv_email = $advisor[0]->adv_email;
						$email_data = array();
						$email_data["first_name"] = $first_name;
						$email_data["email"] = $email;
							
							$bcc = array(
									'adv_email' => $adv_email
							);
				
				$this->email_model->send_email($email, "account_suspended", $email_data, $attach = "", $bcc);
				
				$user_ids = array(
							'user_id' => $user_id,
							'user_type_id' => $user_type_id
				);
				array_push($user_id_array, $user_ids);
			}
			
			foreach($user_id_array as $user_id_array)
			{
				$update_user_id[] = $user_id_array['user_id'];
				
			}
			
			if($check_user)
			{
				$this->users_model->update_user_expired($update_user_id);
				
			}
	
		}
	}
	
	function new_listing_notification()
	{
		$properties = $this->property_model->listing_notification();
		$advisors = $this->users_model->get_new_listing_advisor();

		if($properties)
		{
			$i=0;
			$email_data = array();
		
			$email_data=$this->generate_td($properties);
				
			foreach($advisors->result() as $advisor_email)
			{
				$email = $advisor_email->email;
				$this->email_model->send_email($email, "new_listing_notification",array('tr' => $email_data));
				
			}
		}
		
	}
	
	function weekly_sales_notification()
	{
		$properties = $this->property_model->weekly_notification();
		$advisors = $this->users_model->get_weekly_sales_advisor();
		
		if(($properties) && ($advisors)) {
			$i=0;
			$email_data = array();
		
			$email_data=$this->generate_week_sale_data($properties);
			
			foreach($advisors->result() as $advisor_email) {
				$email = $advisor_email->email;
				$this->email_model->send_email($email, "weekly_sales_notification",array('weekly_data' => $email_data));
			}
		}
	}
	
	private function generate_week_sale_data($properties)
	{
	$txt='<html><head><!-- CSS goes in the document HEAD or added to your external stylesheet -->
<style type="text/css">
table.altrowstable {
 width:80%;
 font-family: verdana,arial,sans-serif;
 font-size:11px;
 color:#333333;
 border-width: 1px;
 border-color: #a9c6c9;
 border-collapse: collapse;
}
table.altrowstable th {
 border-width: 1px;
 padding: 8px;
 border-style: solid;
 border-color: #a9c6c9;
 background-color:#F0F1F1;
}
table.altrowstable td {
 border-width: 1px;
 padding: 8px;
 border-style: solid;
 border-color: #a9c6c9;
}
.oddrowcolor{

 background-color:#F0F1F1;

}
.evenrowcolor{

 background-color:#DEE4E8;
}
</style></head><body><table border="1" class="altrowstable" id="alternatecolor">	<tbody>		<tr>			<th>				Date Reserved</th>
			<th>				Property Address</th>			<th>				Property Price</th>
			<th>				NRAS</th>			<th>				Contract Type</th>
			<th>				Advisor</th>		</tr>';
		$i = 0;
		foreach($properties->result() as $property)
		{
			
				$lot = $property->lot;
				$address = $property->address;
				$suburb = $property->suburb;
				$property_id = $property->property_id;
				$property_name = $property->title;
				$reserved_date = date('d/m/Y', strtotime($property->reserved_date));
				$contract_type = $property->contract_type;
				$nras = $property->nras ? "Yes" : "No";
				$smsf = $property->smsf ? "Yes" : "No";
				$titled = $property->titled ? "Yes" : "No";
				$advisor = $property->advisor_fullname;
				$total_price = $property->total_price;
				
				$propertyURL = site_url("stocklist/detail/$property_id");
				
			
				if($i % 2 == 0)
				{
					$txt.='<tr class="evenrowcolor"><td width="150">'.$reserved_date.'</td><td width="250">Lot'.$lot.', '.$address.', '.$suburb.'</td><td width="50">'.$total_price.'</td><td width="50">'.$nras.'</td><td width="100">'.$contract_type.'</td><td width="100">'.$advisor.'</td></tr>';
				}
				else
				{
					$txt.='<tr class="oddrowcolor"><td width="150">'.$reserved_date.'</td><td width="250">Lot'.$lot.', '.$address.', '.$suburb.'</td><td width="50">'.$total_price.'</td><td width="50">'.$nras.'</td><td width="100">'.$contract_type.'</td><td width="100">'.$advisor.'</td></tr>';
				}
			$i++;	
		}
		
		$txt.='	</tbody></table> </body></html>';
		
		return $txt;
	}
	
	private function generate_td($properties)
	{
	
	$txt="";
	$i=0;
	foreach($properties->result() as $property)
			{
			$txt.= ($i==0)?"<tr>":"";
			
			    $image = $property->hero_image;
				$lot = $property->lot;
				$property_id = $property->property_id;
				$property_name = $property->title;
				$bedrooms = $property->bedrooms;
				$bathrooms = $property->bathrooms;
				$garage = $property->garage;
				$nras = $property->nras ? "Yes" : "No";
				$smsf = $property->smsf ? "Yes" : "No";
				$titled = $property->titled ? "Yes" : "No";
				$house_area = $property->house_area;
                $rent_yield = $property->rent_yield;
				$state = $property->state;
				$total_price = $property->total_price;
				$area_name = $property->area_name;
				$hero_image = FCPATH . 'property/'.$property_id.'/images/'.$image;
                if($image != '' && file_exists($hero_image))
                    $hero_image = base_url('property/'.$property_id.'/images/'.$image);
				else
                    $hero_image = base_url('images/member/home_default.jpg');
                $propertyURL = site_url("stocklist/detail/$property_id");
				
			
				
	$txt.='<td class="itemcell" valign="top"><a href="' . base_url() . 'stocklist/detail/'.$property_id.'"><img alt="" border="0" height="130" src="'.$hero_image.'" width="195" /></a><table align="center" cellpadding="10" cellspacing="0" class="iteminfo" width="195"><tbody><tr><td><h2><a href="' . base_url() . 'stocklist/detail/'.$property_id.'">'.$lot.','.$property_name.'</a></h2><h3><a href="' . base_url() . 'stocklist/detail/'.$property_id.'"> '.$area_name.', '.$state.'</a></h3>
									<table align="center" cellpadding="0" cellspacing="0" class="specs" width="175">
										<tbody>
											<tr>
												<td valign="middle">
													<img border="0" height="14" src="' . base_url() . 'images/member/icon-bedrooms-drk.png" width="24" /></td>
												<td class="padding-right" valign="middle">
													'.$bedrooms.'</td>
												<td valign="middle">
													<img border="0" height="19" src="' . base_url() . 'images/member/icon-bathrooms-drk.png" width="21" /></td>
												<td class="padding-right" valign="middle">
													'.$bathrooms.'</td>
												<td valign="middle">
													<img border="0" height="21" src="' . base_url() . 'images/member/icon-garage-drk.png" width="25" /></td>
												<td valign="middle">
													'.$garage.'</td>
											</tr>
										</tbody>
									</table>
									<h4>
										$'.$total_price.'</h4>
									<p class="additionalInfo">
										<b>NRAS:</b> '.$nras.'<br />
										<b>SMSF:</b> '.$smsf.'<br />
										<b>Titled:</b> '.$titled.'<br />
										<b>House area:</b> '.$house_area.'sqm.<br />
										<b>Rent Yield:</b> '.$rent_yield.'%</p>
								</td>
							</tr>
						</tbody>
					</table>
				</td>
				<td class="cellspacer">
					&nbsp;</td>';
					
				$i++;
					if($i==3){
						$txt.= '</tr><tr><td class="cellspacer">&nbsp;</td></tr>';$i=0;
						}
					}
					return $txt;
	
	}
    
	
	function key_date_followup()
	{
		$key_dates = $this->keydate_model->get_all_keydates();
		
		if($key_dates)
		{
			foreach($key_dates->result() as $key_date)
			{
				$followup_date = $key_date->followup_date;
				
				$today = date('Y-m-d');
				$email_data = array();
				if($followup_date == $today)
				{
					$property_id = $key_date->foreign_id;
					
					$keydate_id = $key_date->id;
					$followup_date = $key_date->followup_date;
					$followup_date = date('d/m/Y', strtotime($followup_date));
					$estimated_date = $key_date->estimated_date;
					$estimated_date = date('d/m/Y', strtotime($estimated_date));
					$actual_date = $key_date->actual_date;
					$actual_date = date('d/m/Y', strtotime($actual_date));
					$user_id = $key_date->user_id;
					$description = $key_date->description;
					
					$admin_mails = $this->users_model->get_email_notification_admins();
					
					$property = $this->property_model->change_status_email($property_id);
					if($property)
					{
						foreach($property->result() as $property)
						{
							
							$property_address = $property->lot.', '.$property->address;
							$advisor_name = $property->advisor_full_name;
							$partner_name = $property->partner_full_name;
							$investor_name = $property->investor_full_name;
							$advisor_email = $property->advisor_email;
							$partner_email = $property->partner_email;
							
							$email_data['estimated_date'] = $estimated_date;
							$email_data['description'] = $description;
							$email_data['property_address'] = $property_address;
							$email_data['advisor_name'] = $advisor_name;
							$email_data['partner_name'] = $partner_name;
							$email_data['investor_name'] = $investor_name;
							
							$bcc = array(
								'partner_email' => $partner_email
							);
							
							if($admin_mails) 
							{
								foreach($admin_mails as $admin_mail)
								{
									$admin_mail = $admin_mail->email;
									array_push($bcc, $admin_mail);
								}
							}
							
							$this->email_model->send_email($advisor_email, "key_date_followup", $email_data, $attach = "", $bcc);
						}
					}
				}
			}
		
		}
	}
	
    /***
    * Cleans up unused files and directories in the project_files, property, area_files etc directories
    */
    function cleanup()
    {
        $this->load->model("property_model");
        $this->load->model("project_model");
        $this->load->model("area_model");
        $this->load->model("article_model");
        
        // Cleanup property files
        $path = ABSOLUTE_PATH . "property";
        
        $dirs = $this->utilities->get_directories($path);
        if(!$dirs) {
            die("Couldn't load directories in path: $path");    
        }
        
        $pattern = '/^.*\/([0-9]+$)/';
        
        foreach($dirs as $directory) {
            preg_match($pattern, $directory, $matches); 
            $num_matches = count($matches);   
            
            if($num_matches > 1) {
                $property_id = $matches[1];
                
                if(!is_numeric($property_id)) {
                    continue;
                }    
                
                // Does this property exist
                $property = $this->property_model->get_details($property_id);
                if(!$property) {
                    // The property is not on the system
                    print "Delete Property ID: $property_id<br>";  
                    
                    $cmd = "/bin/rm -rf $directory";
                    shell_exec($cmd);  
                }
            }
        }
        
        // Cleanup project files
        $path = ABSOLUTE_PATH . "project_files";
        
        $dirs = $this->utilities->get_directories($path);
        if(!$dirs) {
            die("Couldn't load directories in path: $path");    
        }
        
        $pattern = '/^.*\/([0-9]+$)/';
        
        foreach($dirs as $directory) {
            preg_match($pattern, $directory, $matches); 
            $num_matches = count($matches);   
            
            if($num_matches > 1) {
                $project_id = $matches[1];
                
                if(!is_numeric($project_id)) {
                    continue;
                }    
                
                // Does this property exist
                $project = $this->project_model->get_details($project_id);
                if(!$project) {
                    // The project is not on the system
                    print "Delete Project ID: $project_id<br>";  
                    
                    $cmd = "/bin/rm -rf $directory";
                    shell_exec($cmd);  
                }
            }
        } 
        
        // Cleanup area files
        $path = ABSOLUTE_PATH . "area_files";
        
        $dirs = $this->utilities->get_directories($path);
        if(!$dirs) {
            die("Couldn't load directories in path: $path");    
        }
        
        $pattern = '/^.*\/([0-9]+$)/';
        
        foreach($dirs as $directory) {
            preg_match($pattern, $directory, $matches); 
            $num_matches = count($matches);   
            
            if($num_matches > 1) {
                $area_id = $matches[1];
                
                if(!is_numeric($area_id)) {
                    continue;
                }    
                
                // Does this property exist
                $area = $this->area_model->get_details($area_id);
                if(!$area) {
                    // The project is not on the system
                    print "Delete Area ID: $area_id<br>";  
                    
                    $cmd = "/bin/rm -rf $directory";
                    shell_exec($cmd);  
                }
            }
        } 
        
        // Cleanup article files
        $path = ABSOLUTE_PATH . "article_files";
        
        $dirs = $this->utilities->get_directories($path);
        if(!$dirs) {
            die("Couldn't load directories in path: $path");    
        }
        
        $pattern = '/^.*\/([0-9]+$)/';
        
        foreach($dirs as $directory) {
            preg_match($pattern, $directory, $matches); 
            $num_matches = count($matches);   
            
            if($num_matches > 1) {
                $article_id = $matches[1];
                
                if(!is_numeric($article_id)) {
                    continue;
                }    
                
                // Does this property exist
                $article = $this->article_model->get_details($article_id);
                if(!$article) {
                    // The project is not on the system
                    print "Delete Article ID: $article_id<br>";  
                    
                    $cmd = "rm -rf $directory";
                    shell_exec($cmd);  
                }
            }
        }    
        
        // Cleanup stage files
        $path = ABSOLUTE_PATH . "stage_files";
        
        $dirs = $this->utilities->get_directories($path);
        if(!$dirs) {
            die("Couldn't load directories in path: $path");    
        }
        
        $pattern = '/^.*\/([0-9]+$)/';
        
        foreach($dirs as $directory) {
            preg_match($pattern, $directory, $matches); 
            $num_matches = count($matches);   
            
            if($num_matches > 1) {
                $stage_id = $matches[1];
                
                if(!is_numeric($stage_id)) {
                    continue;
                }    
                
                // Does this property exist
                $stage = $this->db->get_where("property_stages", array("id" => $stage_id))->row();
                if(!$stage) {
                    // The stage is not on the system
                    print "Delete Stage ID: $stage_id<br>";  
                    
                    $cmd = "rm -rf $directory";
                    shell_exec($cmd);  
                } else {
                    $property_id = $stage->property_id;
                    
                    $property = $this->property_model->get_details($property_id);
                    if(!$property) {
                        // The project is not on the system
                        print "Delete Stage $stage_id for Property ID: $property_id<br>";  
                        
                        $cmd = "/bin/rm -rf $directory";
                        shell_exec($cmd);  
                    }                         
                }
            }
        }                                  
    }    
}