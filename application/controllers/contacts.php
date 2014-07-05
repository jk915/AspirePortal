<?php
// NOTE: Check application/core/MY_Controller to see how user permissions are enforced.

// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR.','.USER_TYPE_PARTNER);

class Contacts extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
	private $doc_type = "lawyer_document";
    
    function Contacts()
    {
        $this->data = array();
        $this->load_number_per_page = 5;
		 
        
        parent::__construct();
        
        redirect("/dashboard");
        exit();
        
        $this->load->model("Users_model");
		$this->load->model("lawyer_model");
		$this->load->model("contact_model");
		$this->load->model("comment_model","commentmd");
		$this->load->model("document_model");
    }
   
    function index()
    {
        $this->data["meta_title"] = "My Contacts";
        $this->data["contact_types"] = $this->lawyer_model->get_contact_types();
		//print_r($this->data["contact_types"]);
        $this->load->view('member/header', $this->data);
        $this->load->view('member/contacts/list/prebody.php', $this->data); 
        $this->load->view('member/contacts/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function detail($contacts_id = "")
    {
    	$this->load->model("Tasks_model");
    	$this->load->model("Notes_model");
    	
        $add_mode = !is_numeric($contacts_id);
        $this->data["builder"] = false;
        
		if($add_mode)
        {
            $this->data["meta_title"] = "Add New Contact"; 
					
        }
        else
        {
            // Load the user statistics
            $this->data["meta_title"] = "Contacts Detail";
			$builder = $this->lawyer_model->get_details($contacts_id);
			$this->data["builder"] = $builder;
		}
        
        $this->data["add_mode"] = $add_mode; 
        $user_id = $this->session->userdata["user_id"];
		
        // Load the state options for Australia
        $this->data["states"] = $this->tools_model->get_states(1);
		
		
		$this->data["user"] = $this->Users_model->get_details($user_id);
		$this->data['contacts'] = $this->contact_model->get_list(array('foreign_id'=>$contacts_id, 'type' => "lawyer_contact"));
		
        $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$contacts_id, 'type' => "lawyer_comment"));

		$this->data["documents"] = $this->document_model->get_list($this->doc_type, $contacts_id);
		
		$this->data["contact_types"] = $this->lawyer_model->get_contact_types();
        $this->load->view('member/header', $this->data);
        $this->load->view('member/contacts/detail/prebody.php', $this->data); 
        $this->load->view('member/contacts/detail/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }    
    
    function ajax()
    {
        // Prepare the return array
        $this->data = get_return_array();   // Defined in strings helper
        
        // Get the action that the user is trying to perform.
        $action = $this->input->post("action");
        
        // Handle the action.
        switch($action)
        {
            case "update_contact":   // User is trying to login.
                $this->handle_update_contact();
                break;
                
            case "delete_contact":   // User is trying to login.
                $this->handle_delete_contact();
                break;
			
            case "load_contacts":
                $this->handle_load_contacts();
                break;
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
  
  public  function ajaxwork()
	{
		$type = intval($this->tools_model->get_value("type",0,"get",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"get",0,false));
		
         switch($type)
        {
		
			case 5: //delete logo
            
                $contacts_id = intval($this->tools_model->get_value("contacts_id",0,"get",0,false));
                //do we have a valid contacts_id ?
                if (is_numeric($contacts_id)) {
                    
                    $builder_folder = FCPATH;
                    
                    $builder_details = $this->lawyer_model->get_details($contacts_id);
                    
                    if ($builder_details) {
                            $logo_name = $builder_details->contacts_logo;
                            //delete files
                            if (file_exists($builder_folder.$logo_name)) unlink($builder_folder.$logo_name);
                            if (file_exists($builder_folder.$logo_name . "_thumb.jpg")) unlink($builder_folder . $logo_name . "_thumb.jpg");
                            $this->lawyer_model->save($contacts_id,array( "contacts_logo"=> "" ));      
                            die("done");
                    }
                    else
                        die("Error: Contact id not found");
                }
                else
					
                        die("Error: Not a valid contacts id");
            break;
		
			case 9: // Load Contacts
            
                $contacts_id = isset($_GET['contacts_id']) ? $_GET['contacts_id'] : 0;
                $contacts = $this->contact_model->get_list(array('type' => 'lawyer_contact','foreign_id'=>$contacts_id));
                
                $html = '<tr>';
                $html.='    <th width="10%">ID</th>';
                $html.='    <th align="left">Contact Name</th>';
                $html.='    <th align="left">Position</th>';
                $html.='    <th align="left">Phone</th>';
                $html.='    <th width="10%">Delete</th>';
                $html.='</tr>';
                
                if ($contacts) {
                    foreach ($contacts->result() AS $contact)
                    {
                        $html.='<tr>';
                        $html.='    <td class="admintabletextcell" align="center">'.$contact->contact_id.'</td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="'.$contact->contact_id.'" class="editcontact">'.$contact->name.'</a></td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;">'.$contact->position.'</td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;">'.$contact->phone.'</td>';
                        $html.='    <td class="center"><input type="checkbox" class="contacttodelete" value="'.$contact->contact_id.'" /></td>';
                        $html.='</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;	
		
			case 10: // Save (Update) Contact
                $error_message = '';
                $data = array();
                
                $name = isset($_GET['contact_name']) ? $_GET['contact_name'] : '';
                $position = isset($_GET['contact_position']) ? $_GET['contact_position'] : '';
                $address = isset($_GET['contact_address']) ? $_GET['contact_address'] : '';
                $suburb = isset($_GET['contact_suburb']) ? $_GET['contact_suburb'] : '';
                $postcode = isset($_GET['contact_postcode']) ? $_GET['contact_postcode'] : '';
                $state_id = isset($_GET['contact_state_id']) ? $_GET['contact_state_id'] : null;
                $phone = isset($_GET['contact_phone']) ? $_GET['contact_phone'] : '';
                $mobile = isset($_GET['contact_mobile']) ? $_GET['contact_mobile'] : '';
                $fax = isset($_GET['contact_fax']) ? $_GET['contact_fax'] : '';
                $email = isset($_GET['contact_email']) ? $_GET['contact_email'] : '';
                $comment = isset($_GET['contact_comment']) ? $_GET['contact_comment'] : '';
                $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : '';
                $contacts_id = isset($_GET['contacts_id']) ? $_GET['contacts_id'] : 0;
                $data = array(
                                'name' => $name,
                                'position' => $position,
                                'address' => $address,
                                'suburb' => $suburb,
                                'postcode' => $postcode,
                                'state_id' => $state_id,
                                'phone' => $phone,
                                'mobile' => $mobile,
                                'fax' => $fax,
                                'email' => $email,
                                'comment' => $comment,
                                'foreign_id' => $contacts_id,
                                'type' => 'lawyer_contact'
                            );
                
                if (!empty($contact_id)) {
                    $this->contact_model->save($contact_id,$data);
					
					
                } else {
                    $this->contact_model->save('',$data);
					
                }
                
                echo 'OK';
                exit();
            break;
			
			case 11: // Form Edit Contact
                $contact_id = isset($_GET['contact_id']) ? $_GET['contact_id'] : '';
                $contact = $this->contact_model->get_details($contact_id);
                
                if ($contact) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'name' => $contact->name,
                                           'position' => $contact->position,
                	                       'address' => $contact->address,
                                           'suburb' => $contact->suburb,
                	                       'postcode' => $contact->postcode,
                                           'state_id' => $contact->state_id,
                	                       'phone' => $contact->phone,
                	                       'mobile' => $contact->mobile,
                	                       'fax' => $contact->fax,
                	                       'email' => $contact->email,
                                           'comment' => $contact->comment,
                	                       'contact_id' => $contact->contact_id
                	                   );                                   
                                       
                    echo json_encode($return_data);
                    exit();
                } else {
                    $return_data = array(
                	                       'status' => 'FAILED',
                	                   );
                    echo json_encode($return_data);
                    exit();
                }
                
            break;
			
			case 12: // Delete Contact
                $status = 'FAILED';
                $contact_ids = $this->tools_model->get_value("todelete","","get",0,false);
                
                if ($contact_ids != "") {
                    $arr_ids = explode(";",$contact_ids);
                    if (sizeof($arr_ids)) {
                        $this->contact_model->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;
		
			case 13: // Delete comments
            
                $status = 'FAILED';
                $ids = $this->tools_model->get_value("todelete","","get",0,false);
                
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
			
			case 14:
                // Load Comments
                $contacts_id = isset($_GET['contacts_id']) ? $_GET['contacts_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'lawyer_comment','foreign_id'=>$contacts_id) );
                
                $html ='<tr>';
                $html.='    <th width="10%">ID</th>';
                $html.='    <th align="left">Comment</th>';
                $html.='    <th width="10%">Delete</th>';
                $html.='</tr>';
                
                if ($comments) {
                    foreach ($comments->result() AS $index=>$comment)
                    {
                        $html.='<tr id="acomment_'.$comment->id.'" class="'.$index%2 ? 'admintablerowalt' : 'admintablerow'.'">';
                        $html.='    <td class="admintabletextcell" align="center">'.$comment->id.'</td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;">';
                        $html.='        <span style="font-weight:bold">'.trim("$comment->first_name $comment->last_name").'</span>';
                        $html.='        @ <em style="font-style:italic;">'.date('d/m/Y h:i A', $comment->ts_added).'</em>:<br />';
                        $html.='        "'.nl2br($comment->comment).'"';
                        $html.='    </td>';
                        $html.='    <td class="center"><input type="checkbox" class="commenttodelete" value="'.$comment->id.'" /></td>';
                        $html.='</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;
            
            case 15: // Add comment
                
                $error_message = '';
                $data = array();
                $contacts_id = isset($_GET['contacts_id']) ? $_GET['contacts_id'] : 0;
                $comment_id = isset($_GET['comment_id']) ? $_GET['comment_id'] : 0;
                $comment = isset($_GET['comment']) ? $_GET['comment'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $data = array(
                                'type' => 'lawyer_comment',
                                'comment' => $comment,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $contacts_id,
                                'datetime_added' => date('Y-m-d H:i:s')
                            );
              
                if (!empty($comment_id)) {
                    $this->commentmd->save($comment_id,$data);
                } else {
                    $this->commentmd->save('',$data);
                }
                
                echo 'OK';
                exit();
                
            break;
			
			case 16: //up and down order
                $return_data = array();
                
                //get id 
                $contacts_id = $this->tools_model->get_value("user_id","","post",0,false);
                $this->_refresh_images($contacts_id );
            break;
			
			case 17: //download user document

                 $file = trim(urldecode($this->tools_model->get_value("file",0,"post",0,false)));
                 $contacts_id = intval($this->tools_model->get_value("contacts_id",0,"post",0,false)); 
                 $document_type = $this->tools_model->get_value("document_type",0,"post",0,false); 
                 
                 $path = FCPATH. USER_FILES_FOLDER.$contacts_id."/$document_type/".$file;
                 $path = trim($path);
                 
                 $this->load->helper('file');
                 write_file('text.txt', "path:" . $path, 'a+');

                 if(file_exists($path)) {
                    $this->utilities->download_file($path);
                 }

            break;
			
			case 18: // Add Description
                
                $error_message = '';
                $data = array();
                $contacts_id = isset($_GET['contacts_id']) ? $_GET['contacts_id'] : 0;
                $description_id = isset($_GET['description_id']) ? $_GET['description_id'] : 0;
                $description = isset($_GET['description']) ? $_GET['description'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $data = array(
                                'document_description' => $description,
                                'foreign_id' => $contacts_id,
                                
                            );
                
                if (!empty($description_id)) {
                    $this->document_model->save($description_id,$data);
                } else {
                    $this->document_model->save('',$data);
                }
               
                echo 'OK';
                exit();
                
            break;
		}
	}
    /***
    * Handles the handle_load_leads action
    * Loads a list of leads in accordance with search params
    */
    private function handle_load_contacts()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $current_page = $this->input->post("current_page");
        
        $filters = array();
        $filters["created_by_user_id"] = $this->user_id;
        //$filters["created_by_only"] = true;
        $filters["owner_id"] = $this->user_id;
        $filters["deleted"] = 0;
        $filters["order_by"] = "company_name ASC";        
        $filters["search_term"] = $this->input->post("search_term");
		$filters["contact_type"] = $this->input->post("contact_type");
        //$filters["status"] = $this->input->post("lead_status");
                
        
        $extra_sql = ", get_last_note_date(u.user_id) as notes_last_created";
        
        // Sort By Columns
        $valid_columns = array(
                                "u.first_name",
                                'u.company_name',
                                'u.mobile',
                                'u.status',
                                
                            );
                            
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }  
        
        $sort_col = $this->input->post("sort_col");
        
        $filters["order_by"] = $sort_col . " " . $this->input->post("sort_dir");      
        
        $users = $this->Users_model->get_list(-1, $limit = PARTNERS_PER_PAGE, $page_no = $current_page, $count_all, "", $user_type = USER_TYPE_LEAD, $filters, $extra_sql);        
      
	  
		$builders = $this->lawyer_model->get_list(-1,$this->records_per_page,1, $count_all, $search_term = "",$filters, $order_by = "l.company_name ASC");
	
			
        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/contacts/list/list", array("builders" => $builders, "users" => $users), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
                   
    
    /***
    * Handles the handle_update_lead action
    * Send an OK status back if the user was updated/added successfully, error if not.
    */
    private function handle_update_contact()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        // Validate the form submission
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $contacts_id = $this->input->post("contacts_id");
		
        $add_mode = false;
        $builder = false;
        

        if(is_numeric($contacts_id))
        {
		
			// We're updating an existing user.  Load their details.
			$builder = $this->lawyer_model->get_details($contacts_id);
		}
        else
        {
            // We are inserting a new user.
            $add_mode = true;
            
        }
        
        
        $save = array();
		$save["first_name"] = $this->input->post("first_name");    
        $save["last_name"] = $this->input->post("last_name");
		$save["company_name"] = $this->input->post("company_name");
		$save["billing_address1"] = $this->input->post("billing_address1");
        $save["billing_address2"] = $this->input->post("billing_address2");
		$save["billing_suburb"] = $this->input->post("billing_suburb");
        $save["billing_postcode"] = $this->input->post("billing_postcode");
        $save["mobile"] = $this->input->post("mobile");
        $save["billing_phone"] = $this->input->post("billing_phone");
		$save["billing_fax"] = $this->input->post("billing_fax");
		$save["email_1"] = $this->input->post("email_1");
		$save["email_2"] = $this->input->post("email_2");
        $save["state_id"] = $this->input->post("state");
        $save["billing_country_id"] = $this->input->post("country");
		$save["postal_address"] = $this->input->post("postal_address");
		$save["postal_suburb"] = $this->input->post("postal_suburb");
		$save["postal_postcode"] = $this->input->post("postal_postcode");
		$save["postal_state_id"] = $this->input->post("postal_state_id");
		$save["contact_type"] = $this->input->post("contact_type");
        $save["enabled"] = 1;           

        $contacts_id = $this->lawyer_model->save($contacts_id, $save);
			
        if(!$contacts_id)
        {
            $this->data["message"] = "Sorry, someone went wrong whilst trying to save this partner.";
            send($this->data);             
        }
        
        redirect('contacts');        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
                
        send($this->data);
    }
    
    /***
    * Handles the handle_delete_lead action
    * Delete lead
    */
    private function handle_delete_contact()
    {
        $contacts_id = $this->input->post("contacts_id");
        if($contacts_id)
        {
            // We're updating an existing user.  Load their details.
            $builder = $this->lawyer_model->get_details($contacts_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            // if(!has_permission($builder))
            // {
                // $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                // send($this->data);     
            // }
            // else
            // {
            	//$this->lawyer_model->save($contacts_id, array('deleted' => 1));
            	$this->lawyer_model->delete($contacts_id);
				
           // }
        }
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        send($this->data);
    }
    
	function _refresh_images($contacts_id)
	{
		//get files
		$files = $this->document_model->get_list("lawyer_document", $contacts_id); 
		$count_all = count($files);

		$this->load->view('member/contacts/list/document_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));
	}
		
    function upload_file($upload_type, $contacts_id, $doc_id='', $doc_name='', $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($contacts_id)))
		{
			die ('{error: "Invalid upload type $upload_type or builder id $contacts_id"}');
		}
        
		
		// Handle a hero image upload
		if(($upload_type == "hero_image") || ($upload_type == "documents"))
		{
            // Load the builder in question
            $builder = $this->lawyer_model->get_details($contacts_id);
            if(!$builder)
            {
				die ('{error: "Invalid builder"}');	
            }
            
			// Determine the path for where to store the original image and the image set
            $path = ABSOLUTE_PATH . BUILDER_FILES_FOLDER . $contacts_id . "/";
            if ( !is_dir($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . BUILDER_FILES_FOLDER . $contacts_id . "/documents/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            }
            
         	// Hero Image Upload
         	$result = $this->qqfileuploader->handleUpload($path, $filename, true);
         	
         	if($filename == "")
         	{
				$filename = $this->qqfileuploader->file->getName();
				
         		if($filename == "")
         		{
         			die ('{error: "Could not determine file name"}');
				} 				
         	}
         	
         	$file_path =  $path . $filename;
         	if(!file_exists($file_path))
         	{
				die ('{error: "File did not upload correctly"}');	
         	}
         	
			// Move the temporary file to the final path.
  			chmod($file_path, 0666);
  			$return_path = '';
  			
  			if($upload_type == "documents") {
  			    
				$doc_name = str_replace('+',' ',$filename);
				
                // Save the document into the documents table in the database.
				$doc_data =  array(
					"document_type" => "lawyer_document",
					"foreign_id" => $contacts_id,
					"document_name" => $doc_name,
					"document_path" => BUILDER_FILES_FOLDER . $contacts_id . "/documents/" . $filename
				);
				
                $return_path = BUILDER_FILES_FOLDER . $contacts_id . "/documents/" . $filename;
				$this->document_model->save($doc_id, $doc_data, $contacts_id, "lawyer_document", $use_order = TRUE);	

				
			} elseif ($upload_type == 'hero_image') {
			    
			    $builder_folder = FCPATH.BUILDER_FILES_FOLDER.$contacts_id."/";
            	$thumb_path = $builder_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($builder_folder.$filename, $thumb_path, $error_message,THUMB_BUILDER_WIDTH,THUMB_BUILDER_HEIGHT);
			    
			    // Update the article with the hero name.
	        	$update_data = array("contacts_logo" => BUILDER_FILES_FOLDER . $contacts_id. "/" . $filename);
				$this->lawyer_model->save($contacts_id, $update_data);
				
				$return_path = site_url(BUILDER_FILES_FOLDER . $contacts_id. "/" . $filename);
			}

			$return = array();
			$return["status"] = "OK";
			$return["fileName"] = $return_path;
			$return["success"] = true;	
			
			echo json_encode($return);	
            exit();
		}
		else
		{
			die ('{error: "Invalid file type"}');
		}	
    }
    
    /***
    * Handles the handle_update_to_investor action
    * Update Lead to Investor
    */
     
  }