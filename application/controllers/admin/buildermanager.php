<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
* @property Tools_model $tools_model
* @property property_model $property_model
*/

class Buildermanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;  
    private $images_records_per_page = 3;  
    private $doc_type = "builder_document";
    
    function __construct()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("builder_model");
        $this->load->model("document_model");
        $this->load->model("contact_model");
        $this->load->model("comment_model","commentmd");
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
        $this->data["meta_title"] = "Builder Manager";
        $this->data["page_heading"] = "Builder Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data["builders"] = $this->builder_model->get_list(-1,$this->records_per_page,1,$count_all);
        $this->data["pages_no"] = $count_all / $this->records_per_page;                                       
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/buildermanager/prebody', $this->data); 
        $this->load->view('admin/buildermanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function builder($builder_id='')
    {
        $this->load->model("property_model");
        
        $this->data['message'] = "";
        $postback = $this->tools_model->isPost();    
        
        if ($postback) {
            $this->_handlePost($builder_id);
        }
        
        if($builder_id != "") { //edit
            // Load page details
            $builder = $this->builder_model->get_details($builder_id);
            $documents = $this->document_model->get_list($this->doc_type, $builder_id);
            
            if(!$builder) {
                // The page could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the builder could not be loaded.", "builder/show - the builder with an id of '$builder_id' could not be loaded");
                return;            
            } else {
                //pass page details
                $this->data["builder"] = $builder; 
                $this->data["documents"] = $documents;
                $this->data["states"] = $this->property_model->get_states(1);
                $this->data['contacts'] = $this->contact_model->get_list(array('foreign_id'=>$builder_id, 'type' => "builder_contact"));
                $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$builder_id, 'type' => "builder_comment"));
            }
        }
        
        if(!$postback)    
            $this->data['message'] = ($builder_id == "") ? "To create a new builder, enter the builder details below." : "You are editing the &lsquo;<b>$builder->builder_name</b>&rsquo;";
        
        // Define page variables
        
        $this->data["meta_keywords"] = "Builder Manager";
        $this->data["meta_description"] = "Builder Manager";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data["page_heading"] = ($builder_id != "" && isset($builder)) ? $builder->builder_name : "Builder Details";
        
        $this->data['builder_id'] = $builder_id;
        $this->data["robots"] = $this->utilities->get_robots();
        
        if($builder_id != "") { //edit
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER)) //FCPATH
                @mkdir(FCPATH.BUILDER_FILES_FOLDER,DIR_WRITE_MODE);   
             
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER.$builder_id))
                @mkdir(FCPATH.BUILDER_FILES_FOLDER.$builder_id, DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER.$builder_id."/documents"))
                @mkdir(FCPATH.BUILDER_FILES_FOLDER.$builder_id."/documents",DIR_WRITE_MODE);       
        }
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/builder/prebody.php', $this->data); 
        $this->load->view('admin/builder/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function _handlePost($builder_id)
    {
        $data = array(    
                        "builder_name"              => '',
                        "builder_content"           => '',
                        "acn"                       => '',
                        "abn"                       => '',
                        "history"                   => '',
                        "summary"                   => '',
                        "year_established"          => 0,
                        "number_builds_per_year"    => 0,
                        "enabled"                   => '0',
						"display_on_front_end"		=> '0'
                   );
                    
        $required_fields = array("builder_name");
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
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "Builder Manager/HandlerPost update - the builder with an id of '$builder_id' could not be saved");
            return;
        }
        
        $edit_builder = false;
        
        if(is_numeric($builder_id)) {
            $edit_builder = true;
        }
        
        $data["last_modified"] = date("Y-m-d H:i:s");
        $data["last_modified_by"] = $this->login_model->getSessionData("id");
        
        //depeding on the $builder_id do the update or insert
        $builder_id = $this->builder_model->save($builder_id,$data);
        
        if(!$builder_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the builder could not be saved/updated.", "Builder Manager/builder save");
           return;
        }
        
        if(!$edit_builder) {
            $a = $this->document_model->add_default_documents($this->doc_type, $builder_id);
        } else {
            //save documents
            $documents = $this->document_model->get_list($this->doc_type,$builder_id);            
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
                   $this->document_model->save($doc->id,$doc_data, $builder_id);
                }
            }			
        }
        
        redirect("/admin/buildermanager/builder/$builder_id");
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
                $builder_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($builder_ids!="") {
                    $arr_ids = explode(";",$builder_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id)) {
                            if ($where_in != "") $where_in.=",";
                            $where_in .= $id;
                        }
                    }
                    
                    if ($where_in!="") {
                        $this->builder_model->delete($where_in);
                    }                                        
                }
                
                //get list of builders                       
                $builders = $this->builder_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                //load view 
                $this->load->view('admin/buildermanager/builder_listing',array('builders'=>$builders,'pages_no' => $count_all / $this->records_per_page));
                
                
            break;
            
            //page number changed
            case 2:
                
                //get list of builders                       
                $builders = $this->builder_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                //load view 
                $this->load->view('admin/buildermanager/builder_listing',array('builders'=>$builders,'pages_no' => $count_all / $this->records_per_page));
                
            break;
            
            //search for a builder
            case 3:
               
                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                $current_page = 1;
                //get list of builders
                $builders = $this->builder_model->get_list(-1,$this->records_per_page,$current_page,$count_all,$search_terms);
                //load view 
                $this->load->view('admin/buildermanager/builder_listing',array('builders'=>$builders,'pages_no' => $count_all / $this->records_per_page));
                
            break; 
            
            case 5: //delete logo
            
                $builder_id = $this->tools_model->get_value("builder_id","","post",0,false);
                //do we have a valid builder_id ?
                if (is_numeric($builder_id)) {
                    
                    $builder_folder = FCPATH;
                    
                    $builder_details = $this->builder_model->get_details($builder_id);
                    
                    if ($builder_details) {
                            $logo_name = $builder_details->builder_logo;
                            //delete files
                            if (file_exists($builder_folder.$logo_name)) unlink($builder_folder.$logo_name);
                            if (file_exists($builder_folder.$logo_name . "_thumb.jpg")) unlink($builder_folder . $logo_name . "_thumb.jpg");
                            $this->builder_model->save($builder_id,array( "builder_logo"=> "" ));      
                            die("done");
                    }
                    else
                        die("Error: Builder id not found");
                }
                else
                        die("Error: Not a valid builder_id");
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
                $doc_id = intval($this->tools_model->get_value("doc_id","","post",0,false));
                $builder_id = $this->tools_model->get_value("builder_id","","post",0,false);
                
                $this->utilities->add_to_debug("Doc ID: $doc_id, $builder_id");
                
                if(($doc_id == "") || ($builder_id == "")) {
                	$this->utilities->add_to_debug("buildermanager.php - Missing variables in case 12 - delete: $doc_id, $builder_id");            
				}
               
                //do we have a valid builder_id ?
                if (is_numeric($builder_id)) {
                    $doc_data = array(    
                            "document_path"   => ""
                    ); 
                    
                    $this->document_model->save($doc_id,$doc_data,$builder_id);
                }
                     
                $return_data = array();
                $return_data["doc_id"] = json_encode($doc_id);
                
                echo json_encode($return_data);                                                              
            break;
            
            case 9: // Load Contacts
            
                $builder_id = isset($_POST['builder_id']) ? $_POST['builder_id'] : 0;
                $contacts = $this->contact_model->get_list(array('type' => 'builder_contact','foreign_id'=>$builder_id));
                
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
                
                $name = isset($_POST['name']) ? $_POST['name'] : '';
                $position = isset($_POST['position']) ? $_POST['position'] : '';
                $address = isset($_POST['address']) ? $_POST['address'] : '';
                $suburb = isset($_POST['suburb']) ? $_POST['suburb'] : '';
                $postcode = isset($_POST['postcode']) ? $_POST['postcode'] : '';
                $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : null;
                $phone = isset($_POST['phone']) ? $_POST['phone'] : '';
                $mobile = isset($_POST['mobile']) ? $_POST['mobile'] : '';
                $fax = isset($_POST['fax']) ? $_POST['fax'] : '';
                $email = isset($_POST['email']) ? $_POST['email'] : '';
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                $contact_id = isset($_POST['contact_id']) ? $_POST['contact_id'] : '';
                $builder_id = isset($_POST['builder_id']) ? $_POST['builder_id'] : 0;
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
                                'foreign_id' => $builder_id,
                                'type' => 'builder_contact'
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
                $builder_id = isset($_POST['builder_id']) ? $_POST['builder_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'builder_comment','foreign_id'=>$builder_id) );
                
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
                $builder_id = isset($_POST['builder_id']) ? $_POST['builder_id'] : 0;
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $data = array(
                                'type' => 'builder_comment',
                                'comment' => $comment,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $builder_id,
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
        }
    }
    
    function upload_file($upload_type, $builder_id, $doc_id='', $doc_name='', $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($builder_id)))
		{
			die ('{error: "Invalid upload type $upload_type or builder id $builder_id"}');
		}
        
		
		// Handle a hero image upload
		if(($upload_type == "hero_image") || ($upload_type == "documents"))
		{
            // Load the builder in question
            $builder = $this->builder_model->get_details($builder_id);
            if(!$builder)
            {
				die ('{error: "Invalid builder"}');	
            }
            
			// Determine the path for where to store the original image and the image set
            $path = ABSOLUTE_PATH . BUILDER_FILES_FOLDER . $builder_id . "/";
            if ( !is_dir($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . BUILDER_FILES_FOLDER . $builder_id . "/documents/";
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
  			    
				$doc_name = str_replace('+',' ',$doc_name);
				
                // Save the document into the documents table in the database.
				$doc_data =  array(
					"document_type" => "builder_document",
					"foreign_id" => $builder_id,
					"document_name" => $doc_name,
					"document_path" => BUILDER_FILES_FOLDER . $builder_id . "/documents/" . $filename
				);
				
                $return_path = BUILDER_FILES_FOLDER . $builder_id . "/documents/" . $filename;
				$this->document_model->save($doc_id, $doc_data, $builder_id, "builder_document", $use_order = TRUE);				
				
			} elseif ($upload_type == 'hero_image') {
			    
			    $builder_folder = FCPATH.BUILDER_FILES_FOLDER.$builder_id."/";
            	$thumb_path = $builder_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($builder_folder.$filename, $thumb_path, $error_message,THUMB_BUILDER_WIDTH,THUMB_BUILDER_HEIGHT);
			    
			    // Update the article with the hero name.
	        	$update_data = array("builder_logo" => BUILDER_FILES_FOLDER . $builder_id. "/" . $filename);
				$this->builder_model->save($builder_id, $update_data);
				
				$return_path = site_url(BUILDER_FILES_FOLDER . $builder_id. "/" . $filename);
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