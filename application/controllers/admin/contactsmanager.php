<?php
/** lawyer
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
* @property Tools_model $tools_model
* @property property_model $property_model
*/

class Contactsmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    private $documents_records_per_page = 3;	
    private $images_records_per_page = 3;  
    private $doc_type = "lawyer_document";
    public $count = 0;
    function __construct()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("lawyer_model");
        $this->load->model("document_model");
        $this->load->model("contact_model");
        $this->load->model("comment_model","commentmd");
		$this->load->model('property_contact_model');
        $this->load->library("utilities");    
        $this->load->library("image");
                
        //if the $ci_session is passed in post, it means the swfupload has made the POST, don't check for login
        $ci_session = $this->tools_model->get_value("ci_session","","post",0,false);
      
        if ($ci_session == "")
        {

           // Check for a valid session
            if (!$this->login_model->getSessionData("logged_in"))            
                redirect("admin/login");       
        }    
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Contacts Manager";
        $this->data["page_heading"] = "Contacts Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data["builders"] = $this->lawyer_model->get_list(-1,$this->records_per_page,1,$count_all);

		$this->data["pages_no"] = $count_all / $this->records_per_page;

		       
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/lawyermanager/prebody', $this->data); 
        $this->load->view('admin/lawyermanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function contact($contacts_id='')
    {
        $this->load->model("property_model");
        
        $this->data['message'] = "";
        $postback = $this->tools_model->isPost();    
        
        if ($postback) {
            $this->_handlePost($contacts_id);
        }
        
        if($contacts_id != "") { //edit
            // Load page details
            $builder = $this->lawyer_model->get_details($contacts_id);

            $documents = $this->document_model->get_list($this->doc_type, $contacts_id);
            
            if(!$builder) {
                // The page could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the builder could not be loaded.", "builder/show - the builder with an id of '$contacts_id' could not be loaded");
                return;            
            } else {
                //pass page details
                $this->data["builder"] = $builder; 
					
                $this->data["documents"] = $documents;
				$this->data["documents_records_per_page"] = $this->documents_records_per_page;
                $this->data["states"] = $this->property_model->get_states(1);
				
				
                $this->data['contacts'] = $this->contact_model->get_list(array('foreign_id'=>$contacts_id, 'type' => "lawyer_contact"));

                $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$contacts_id, 'type' => "lawyer_comment"));
				
				$this->data["contact_types"] = $this->lawyer_model->get_contact_types();
				
            }
        }
        
        if(!$postback)    
            $this->data['message'] = ($contacts_id == "") ? "To create a new builder, enter the lawyer details below." : "You are editing the &lsquo;<b>$builder->company_name</b>&rsquo;";
        
        // Define page variables
        
        $this->data["meta_keywords"] = "Lawyer Manager";
        $this->data["meta_description"] = "Lawyer Manager";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data["page_heading"] = ($contacts_id != "" && isset($builder)) ? $builder->company_name : "Panel Solicitor Details";
        
        $this->data['contacts_id'] = $contacts_id;
        $this->data["robots"] = $this->utilities->get_robots();
		$this->data["states"] = $this->property_model->get_states(1);
		
        if($contacts_id != "") { //edit
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER)) //FCPATH
                @mkdir(FCPATH.BUILDER_FILES_FOLDER,DIR_WRITE_MODE);   
             
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id))
                @mkdir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id, DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id."/documents"))
                @mkdir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id."/documents",DIR_WRITE_MODE);

			
			$this->data['property_lawyers'] = $this->property_contact_model->get_property_panel_contatc($contacts_id);

			
			if($this->data['property_lawyers'])
			{
				foreach($this->data['property_lawyers']->result() as $property_lawyers)
				{
					if($property_lawyers->status == 'reserved' || $property_lawyers->status == 'EOI Payment Pending' || $property_lawyers->status == 'signed' || $property_lawyers->status == 'sold')
					{
						$this->count++;			
					}
				}
				if($this->count>0)
				{
					$this->data['count'] = $this->count;
				}
				else
				{
					$this->data['count'] = 0;
				}
			}
			$this->lawyer_model->update_lawyer_transaction_count($contacts_id,$this->count);
			
		}
        
		// Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/lawyer/prebody.php', $this->data); 
        $this->load->view('admin/lawyer/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function _handlePost($contacts_id)
    {
        
				
		$data = array(   
						"first_name"	            => '',
						"last_name"                 => '',
                        "company_name"             => '',
                        "contacts_content"          => '',
                        "mobile"					=> '',
                        "history"                   => '',
                        "summary"                   => '',
                        "email_1"					=> '',
						"email_2"					=> '',	
                        "count_number_transactions" => 0,
                        "enabled"                   => '0',
						"contact_type"		        => '',
						"billing_address1"  		=> '',
						"billing_address2"   		=> '',
						"billing_suburb"            => '',
						"billing_postcode"          => '',
						"state_id"          		=> '',
						"billing_country_id"        => '',
						"billing_phone"          	=> '',
						"billing_fax"				=> '',
						"postal_address"            => '',
						"postal_suburb"             => '',
						"postal_postcode"           => '',
						"postal_state_id"           => ''
                   );
				   
				   
							
                    
        $required_fields = array("first_name");
        $missing_fields = false;
        
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
            // Ensure that all required fields are present    
            if(in_array($key,$required_fields) && $data[$key] == "") {
                $missing_fields = true;
                break;
            }
        }
        
        if ($missing_fields) {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "Builder Manager/HandlerPost update - the builder with an id of '$contacts_id' could not be saved");
            return;
        }
        
        $edit_builder = false;
        
        if(is_numeric($contacts_id)) {
            $edit_builder = true;
        }
        
        $data["last_modified"] = date("Y-m-d H:i:s");
        $data["last_modified_by"] = $this->login_model->getSessionData("id");
        
		
		$string = ',';
 
		 if(isset($_POST['approved_state_id']) && is_array($_POST['approved_state_id'])){
		  
		  foreach($_POST['approved_state_id'] as $val){
		   $string .= $val.",";
		  }
		  	 
		  $data['approved_state_id'] = $string;
		 }
		 #echo $string;exit;
		
		
		//depeding on the $contacts_id do the update or insert
        $contacts_id = $this->lawyer_model->save($contacts_id,$data);

        if(!$contacts_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the builder could not be saved/updated.", "Builder Manager/builder save");
           return;
        }
        
        if(!$edit_builder) {
            $a = $this->document_model->add_default_documents($this->doc_type, $contacts_id);
        } 
		/*
		else {
            //save documents
            $documents = $this->document_model->get_list($this->doc_type,$contacts_id);

			
            if($documents)
            {
                foreach($documents->result() as $doc)
                {
                   $doc_name = $this->tools_model->get_value("doc_".$doc->id."_name","","post",0,false); 
                   $extra_data = $this->tools_model->get_value("doc_".$doc->id."_extra_data","","post",0,false); 
                  
                   $doc_data = array(
                        "document_name" => $doc_name,
                        "extra_data" => $extra_data,
                   );
				   
                   $this->document_model->save($doc->id,$doc_data, $contacts_id);
                }
            }			
        }*/
        
        redirect("/admin/contactsmanager/contact/$contacts_id");
    }
    
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
       
        switch($type)
        {
            //delete builders
            case 1:             
                //get builder ids separated with ";"
                $contacts_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($contacts_ids!="") {
                    $arr_ids = explode(";",$contacts_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id)) {
                            if ($where_in != "") $where_in.=",";
                            $where_in .= $id;
                        }
                    }
                    
                    if ($where_in!="") {
                        $this->lawyer_model->delete($where_in);
                    }                                        
                }
                
                //get list of builders                       
                $builders = $this->lawyer_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                //load view 
                $this->load->view('admin/lawyermanager/lawyer_listing',array('builders'=>$builders,'pages_no' => $count_all / $this->records_per_page));
                
                
            break;
            
            //page number changed
            case 2:
                
                //get list of builders                       
                $builders = $this->lawyer_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                //load view 
                $this->load->view('admin/lawyermanager/lawyer_listing',array('builders'=>$builders,'pages_no' => $count_all / $this->records_per_page));
                
            break;
            
            //search for a builder
            case 3:
               
                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                $current_page = 1;
                //get list of builders
                $builders = $this->lawyer_model->get_list(-1,$this->records_per_page,$current_page,$count_all,$search_terms);
                //load view 
                $this->load->view('admin/lawyermanager/builder_listing',array('builders'=>$builders,'pages_no' => $count_all / $this->records_per_page));
                
            break; 
            
            case 5: //delete logo
            
                $contacts_id = intval($this->tools_model->get_value("contacts_id","","post",0,false));
                //do we have a valid contacts_id ?
                if (is_numeric($contacts_id)) {
                    
                    $builder_folder = FCPATH;
                    echo $contacts_id;
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
					echo $contacts_id;
                        die("Error: Not a valid contacts id");
            break;
            
           case 7: //refresh document path
                $doc_id = intval($this->tools_model->get_value("doc_id",0,"post",0,false));
                
                $doc_details = $this->document_model->get_details($doc_id); 
                
                $document_path = $doc_details->document_path;          
                
                $return_data = array();
                $return_data["doc_id"] = $doc_id;
                $return_data["document_path"] = $document_path;
                
                echo json_encode($return_data);                                                              
            break;    
            
            case 8: //delete document path
                // $doc_id = intval($this->tools_model->get_value("doc_id","","post",0,false));
                // $builder_id = $this->tools_model->get_value("todelete","","post",0,false);
                
                // $this->utilities->add_to_debug("Doc ID: $doc_id, $builder_id");
                
                // if(($doc_id == "") || ($builder_id == "")) {
                	// $this->utilities->add_to_debug("lawyermanager.php - Missing variables in case 12 - delete: $doc_id, $builder_id");            
				// }
               
                // //do we have a valid builder_id ?
                // if (is_numeric($builder_id)) {
                    // $doc_data = array(    
                            // "document_path"   => ""
                    // ); 
                    
                    // $this->document_model->save($doc_id,$doc_data,$builder_id);
                // }
                     
                // $return_data = array();
                // $return_data["doc_id"] = json_encode($doc_id);
                
                // echo json_encode($return_data);                                                              
            // break;
			
			$files_id = $this->tools_model->get_value("todelete","","post",0,false);
                $user_id = intval($this->tools_model->get_value("contacts_id",0,"post",0,false));

                $file_names = array();

                if ($files_id!="") {

                    $arr_id_files = explode(";",$files_id);

                    $removed_userfiles = $this->db->where_in('id',$arr_id_files)
                                            ->get('documents');

                    //delete from documents table

                    $this->document_model->delete($arr_id_files, '');

                    //delete images from folders           

                    if($removed_userfiles) {
                        foreach($removed_userfiles->result() as $row) {
                            $file_names[] = $row->document_name;
                        }
                        $this->utilities->remove_file(STAGE_FILES_FOLDER.$stage_id."/documents",$file_names,"");
                    }

                }
                echo 'OK';
            break;  
            
            case 9: // Load Contacts
            
                $contacts_id = isset($_POST['contacts_id']) ? $_POST['contacts_id'] : 0;
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
                
                $name = isset($_POST['contact_name']) ? $_POST['contact_name'] : '';
                $position = isset($_POST['contact_position']) ? $_POST['contact_position'] : '';
                $address = isset($_POST['contact_address']) ? $_POST['contact_address'] : '';
                $suburb = isset($_POST['contact_suburb']) ? $_POST['contact_suburb'] : '';
                $postcode = isset($_POST['contact_postcode']) ? $_POST['contact_postcode'] : '';
                $state_id = isset($_POST['contact_state_id']) ? $_POST['contact_state_id'] : null;
                $phone = isset($_POST['contact_phone']) ? $_POST['contact_phone'] : '';
                $mobile = isset($_POST['contact_mobile']) ? $_POST['contact_mobile'] : '';
                $fax = isset($_POST['contact_fax']) ? $_POST['contact_fax'] : '';
                $email = isset($_POST['contact_email']) ? $_POST['contact_email'] : '';
                $comment = isset($_POST['contact_comment']) ? $_POST['contact_comment'] : '';
                $contact_id = isset($_POST['contact_id']) ? $_POST['contact_id'] : '';
                $contacts_id = isset($_POST['contacts_id']) ? $_POST['contacts_id'] : 0;
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
                $contact_id = isset($_POST['contact_id']) ? $_POST['contact_id'] : '';
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
                $contact_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
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
            
            case 14:
                // Load Comments
                $contacts_id = isset($_POST['contacts_id']) ? $_POST['contacts_id'] : 0;
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
                $contacts_id = isset($_POST['contacts_id']) ? $_POST['contacts_id'] : 0;
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
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
                $user_id = $this->tools_model->get_value("user_id","","post",0,false);
                $this->_refresh_images( $user_id );
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
                $contacts_id = isset($_POST['contacts_id']) ? $_POST['contacts_id'] : 0;
                $description_id = isset($_POST['description_id']) ? $_POST['description_id'] : 0;
                $description = isset($_POST['description']) ? $_POST['description'] : '';
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
    
	function _refresh_images($contacts_id)
	{
		//get files
		$files = $this->document_model->get_list("lawyer_document", $contacts_id); 
		$count_all = count($files);

		//load view 
		$this->load->view('admin/lawyer/document_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));
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
}