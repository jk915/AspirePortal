<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Construction extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = 50;
	
    function Construction()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("property_model");
        $this->load->model("resources_model");
        $this->load->model("document_model");
        $this->load->model("property_stages_model");
        $this->load->model('comment_model', 'commentmd'); 
		$this->load->model('keydate_model', 'keydatemd');
        $this->load->model("lawyer_model");
		$this->load->model("users_model");
		$this->load->model("email_model");
        $this->load->helper("image");
        
        $this->load->library("utilities");        
    }
    
    function index()
    {
        redirect("/myproperties"); 
        
    }
    
    function detail($property_id="")
    {
        if(!is_numeric($property_id))
        {
            redirect("/myproperties");    
        }
                
    	// Load the property object
        $property = $this->property_model->get_details($property_id);
		$filters = array();
        if(!$property)
        {
            redirect("/myproperties");              
        }
        $property_stages = $this->property_stages_model->get_list($completed = -1,$property->property_id,"","",$count_all,"Yes");
        $comments = $this->commentmd->get_list(array('type'=>'property_comment','foreign_id'=>$property->property_id),$order_by = "datetime_added DESC");
        
        $this->data["property"] = $property;
        $this->data["property_stages"] = $property_stages;
        $this->data["comments"] = $comments;
 		
        $this->data['user'] = $this->users_model->get_details($this->user_id);
        $this->data['keydates'] = $this->keydatemd->get_list(array('type'=>'property_key_date','foreign_id'=>$property_id) );
		$this->data["builders"] = $this->lawyer_model->get_list(-1,$this->records_per_page,1, $count_all, $search_term = "",$filters, $order_by = "l.company_name ASC");

        $this->data["meta_title"] = "Lot $property->lot , $property->address - Construction Tracker";
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/construction/prebody.php', $this->data); 
		$this->load->view('member/construction/main.php', $this->data);
        $this->load->view('member/footer', $this->data);           
    }

	function update_contacts($property_id)
	{
		if(!is_numeric($property_id))
        {
            redirect("/myproperties");    
        }
		else
		{
			if($this->input->post('submit'))
			{
				$save["financier_id"] = $this->input->post('contact_id_Financier');
				$save["solicitor_id"] = $this->input->post('contact_id_Solicitor');
				$save["other_contact_id"] = $this->input->post('contact_id_others');
				
				$property = $this->property_model->save($property_id, $save);
				if($property)
				{
					redirect("/construction/detail/".$property_id);
				}
			}
		}
	}
	
	
    function ajax()
    {
        // Prepare the return array
        $this->data = get_return_array();   // Defined in strings helper
        
        // Get the action that the user is trying to perform.
        $type = $this->input->post("type");
        
        // Handle the action.
        switch($type)
        {
            case 1:
                $return = array();
                $stage_id = $this->tools_model->get_value("stage_id","","post",0,false);
                
                $stage = $this->property_stages_model->get_details($stage_id);
                if(!$stage)
                    $return["status"] = "ERROR";
                else
                {
                    
                    $documents = $this->document_model->get_list('stage_document', $stage->id);
                    $images = $this->document_model->get_list("stage_gallery", $stage->id);
                    
                    $status = $stage->status;        
                    if($status == "inprogress") {
                        $status = "In Progress";
                    }
                    $status = ucwords($status);

                    $date = (($stage->datetime_completed != null) && (!empty($stage->datetime_completed))) ? date('d/m/Y',$stage->ts_date) : ""; 

                    $html = "<h3 class='stage_name'>" . $stage->stage_name . " - " . $status;
                    if($date != "") {
                        $html .= " - " . $date;
                    }
                    $html .= "</h3>";   
                    $html.= "<p>".nl2br($stage->comments)."</p>"; 
                    
                    if($images) 
                    {
                        foreach($images->result() as $img)     
                        {
                            $html .= "<a id='single_image' rel='gallery1' href='".base_url().$img->document_path."'><img class='stage_thumb' src='".base_url().$img->document_path."_thumb.jpg'></a>";
                        }                         
                    }

                    if($documents)
                    {
                        $html .= "<h3 class='stage_doc'>Documents</h3>";
                           
                        foreach($documents->result() as $doc)     
                        {
                            $caption = $doc->document_path;
                            if(!empty($doc->extra_data)) $caption = $doc->extra_data;
                            $html .= "<p><a class='s_doc' href='".base_url().$doc->document_path."'>". $caption ."</a></p>";
                        }                        
                    }
 
                    $return["status"] = "OK";
                    $return["html"] = $html;
                                             
                }
                echo json_encode($return);
                
                
                break;
                                
            default:
                $this->data["message"] = "Unhandled";
                send($this->data);
                break;    
        }  
    }
  
  
	function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"get",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"get",0,false));
		
		switch($type)
        {
		
			case 28: // Add note
                
                $error_message = '';
                $data = array();
                $property_id = isset($_GET['property_id']) ? $_GET['property_id'] : 0;
                $comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : 0;
                $comment = isset($_GET['comment']) ? $_GET['comment'] : '';
                $user_id = $this->session->userdata("user_id");
                $note_date = isset($_GET['note_date']) ? $_GET['note_date'] :date('Y-m-d H:i:s');
                $note_date = $this->utilities->uk_to_isodate($note_date);
                $user_name = $this->session->userdata("first_name").' '.$this->session->userdata("last_name");
				$user_phone = $this->session->userdata("phone");
				$views = isset($_GET['view']) ? $_GET['view'] : '';
				$date = date('Y-m-d');
				$date_entered = date("d-m-Y", strtotime($date));
				
                $data = array(
                                'type' => 'property_comment',
                                'comment' => $comment,
                                'user_id' => $user_id,
                                'foreign_id' => $property_id,
                                'datetime_added' => $note_date,
								'permission' => $views
                            );
                
                $comments = $this->commentmd->save('',$data);
                
					if($comments)
					{
						//send mail
						$property = $this->property_model->get_details($property_id);
							
						if($property)
						{
							$advisor_email = '';
							$partner_email = '';
							$investor_email = '';
							$bcc = array();
							
							$advisor_email = $property->advisor_email;
							$partner_email = $property->partner_email;
							$investor_email = $property->investor_email;
									
							$property_address = $property->lot.', '.$property->address.', '.$property->suburb;
							$advisor_fullname = $property->advisor_fullname;
							$advisor_mobile = $property->advisor_mobile;
								
							$email_data = array();
							$email_data['property_address'] = $property->address;
							$email_data['note'] = $comment;
							$email_data['user_name'] = $user_name;
							$email_data['user_phone'] = $user_phone;
							$email_data['date_entered'] = $date_entered;
							$email_data['advisor'] = $advisor_fullname;
							$email_data['advisor_mobile'] = $advisor_mobile;
								
							$admin_mails = $this->users_model->get_email_notification_admins();	
							
							$bcc = array(
								'partner_email' => $partner_email,
								'investor_email' => $investor_email
							);
							
							if($admin_mails)
							{		
								foreach($admin_mails as $admin_mail)
								{
									$admin_mail = $admin_mail->email;
									array_push($bcc, $admin_mail);
								
								}
							}
							
							$this->email_model->send_email($advisor_email, "new_file_note", $email_data, $attach = "", $bcc);
						}
					}
				
				
                if (isset($_POST['getlist'])) {
                    $this->load->model('comment_model','commentmd');
                    $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$property_id, 'type' => "property_comment"));
                	$this->load->view('admin/property/note_list', $this->data);
                } else {
                    echo 'OK';
                    exit();
                }
                
            break;       
            
            case 29:
                // Load Comments
                $property_id = isset($_GET['property_id']) ? $_GET['property_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'property_comment','foreign_id'=>$property_id) );
                
				$html ='<tr>';
                $html.='    <th width="20%">Date</th>';
                $html.='    <th align="left">Comment</th>';
				$html.='</tr>';
                
                if ($comments) {
                    foreach ($comments->result() AS $index=>$comment)
                    {
                        $html.='<tr id="acomment_'.$comment->id.'" class="'.$index%2 ? 'admintablerowalt' : 'admintablerow'.'">';
						$html.='    <td class="admintabletextcell" align="center">'.date('d/m/Y', $comment->ts_added).'</td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;">';
                        $html.='        '.$comment->comment.'';
                        $html.='    </td>';
                        $html.='</tr>';
                    }
                }
				
				echo $html;
                exit();
            break;
            
            case 30: // Delete note
            
                $status = 'FAILED';
                $ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($ids != "") {
                    $arr_ids = explode(";",$ids);
                    if ( sizeof($arr_ids) ) {
                        $this->commentmd->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;            
            
            case 31: // Edit Note
                
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $note = $this->commentmd->get_details($comment_id);
                $advisor = "";
                $partner = "";
                $investor = "";
				
                if ($note) {
                    $views = explode(',',$note->permission);
                     if ( sizeof($views) ) 
                     {
                        if(in_array(USER_TYPE_ADVISOR,$views))
                           $advisor = USER_TYPE_ADVISOR;
                           
                        if(in_array(USER_TYPE_PARTNER,$views))
                           $partner = USER_TYPE_PARTNER;
                           
                        if(in_array(USER_TYPE_INVESTOR,$views))
                           $investor = USER_TYPE_INVESTOR;    
                     }
					 
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'comment' => $note->comment,
                	                       'note_date' => date('d/m/Y',$note->ts_added),
                	                       'comment_id' => $note->id,
                                           'advisor' => $advisor,
                                           'partner' => $partner,
                                           'investor' => $investor										   
                	                   );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                	                       'status' => 'FAILED',
                	                   );
                    echo json_encode($return_data);
                }
            
            break;
		
			case 32: // Add Key date
                
                $error_message = '';
                $data = array();
                $property_id = isset($_GET['property_id']) ? $_GET['property_id'] : 0;
                $keydate_id = isset($_GET['keydate_id']) ? $_GET['keydate_id'] : 0;
                $description = isset($_GET['description']) ? $_GET['description'] : '';
                $cms_user = $this->session->userdata("user_id");
                $estimate_date = isset($_GET['estimate_date']) ? $_GET['estimate_date'] :date('Y-m-d H:i:s');
				$estimate_date = $this->utilities->uk_to_isodate($estimate_date);
                $date = date('Y-m-d');
				$followup_date = isset($_GET['followup_date']) ? $_GET['followup_date'] :date('Y-m-d H:i:s');
				$followup_date = $this->utilities->uk_to_isodate($followup_date);
                //$followup_date = date('Y-m-d');
				//$views = isset($_POST['view']) ? $_POST['view'] : '';
				
                $data = array(
                                'type' => 'property_key_date',
                                'description' => $description,
                                'user_id' => $cms_user,
                                'foreign_id' => $property_id,
								'estimated_date' => $estimate_date,
                                'datetime_added' => $date,
								'followup_date' => $followup_date
								
                            );
				//print_r($data);	
                if (!empty($keydate_id)) {
                    $this->keydatemd->save($keydate_id,$data);
					//echo $this->db->last_query();
                } else {
                    $this->keydatemd->save('',$data);
					//echo $this->db->last_query();
                }

                if (isset($_GET['getlist'])) {
                    $this->load->model('keydate_model','keydatemd');
                    $this->data['keydates'] = $this->keydatemd->get_list(array('foreign_id'=>$property_id, 'type' => "property_key_date"));
                	$this->load->view('admin/property/construnction_tracker', $this->data);
                } else {
                    echo 'OK';
				}
                
            break;
		
			case 33:
                // Load keydates
                $property_id = isset($_GET['property_id']) ? $_GET['property_id'] : 0;
                $keydates = $this->keydatemd->get_list(array('type'=>'property_key_date','foreign_id'=>$property_id) );
				
                $html ='<tr>';
                $html.='    <th width="10%">ID</th>';
                $html.='    <th align="30%">Description</th>';
				$html.='    <th width="20%">Actual Date</th>';
				$html.='    <th align="20%">Estimated Date</th>';
				$html.='    <th align="20%">Follow Up Date</th>';
                $html.='    <th width="10%">Delete</th>';
                $html.='</tr>';
                
                if ($keydates) {
                    foreach ($keydates->result() AS $index=>$keydate)
                    {
                        $html.='<tr id="acomment_'.$keydate->id.'" class="'.$index%2 ? 'admintablerowalt' : 'admintablerow'.'">';
                        $html.='    <td class="admintabletextcell" align="center"><a href="javascript:;" rel="'.$keydate->id.'" class="editkeydate" >'.$keydate->id.'</a></td>';
						$html.='    <td class="admintabletextcell" align="center">'.$keydate->description.'</td>';
						$html.='    <td class="admintabletextcell" align="center">'.date("d-m-Y", strtotime($keydate->datetime_added)).'</td>';
						$html.='    <td class="admintabletextcell" align="center">'.date("d-m-Y", strtotime($keydate->estimated_date)).'</td>';
						$html.='    <td class="admintabletextcell" align="center">'.date("d-m-Y", strtotime($keydate->followup_date)).'</td>';
                        $html.='    <td class="center" align="center"><input type="checkbox" class="keydatetodelete" value="'.$keydate->id.'" /></td>';
                        $html.='</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;  

			case 34: // Edit Keydate
                
                $keydate_id = isset($_GET['keydate_id']) ? $_GET['keydate_id'] : 0;
                $keydates = $this->keydatemd->get_details($keydate_id);
				
				$return_data = array(
                	                       'status' => 'OK',
                	                       'description' => $keydates->description,
                	                       'estimate_date' => $keydates->estimated_date,
										   'followup_date' => $keydates->followup_date,
                	                       'keydate_id' => $keydates->id,
                                           									   
                	                   );
                    echo json_encode($return_data);
                             
            break;
            
			case 35: // Delete keydate
            
                $status = 'FAILED';
                $ids = $this->tools_model->get_value("todelete","","get",0,false);
                
                if ($ids != "") {
                    $arr_ids = explode(";",$ids);
                    if ( sizeof($arr_ids) ) {
                        $this->keydatemd->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;
			
		}
	}
		

}