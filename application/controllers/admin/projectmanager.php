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

class Projectmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;  
    private $images_records_per_page = 3;  
    private $doc_type = "project_document";

   
    function __construct()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("project_model");
        $this->load->model('project_meta_model');
        $this->load->model("document_model");
        $this->load->model("property_model");
        $this->load->model("builder_model");
        $this->load->model("area_model");
        $this->load->model("project_model");
        $this->load->model("state_model");
        $this->load->model("region_model");
        $this->load->model("tools_model");
		$this->load->model('comment_model', 'commentmd');
        $this->load->model('project_brochure_model');
        $this->load->model('article_model');
        
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
        
        /*
        $projects = $this->project_model->get_list(-1, "", "", $count_all);
        if($projects) {

            foreach($projects->result() as $p) {
                print "Creating default for ID: " . $p->project_id . "<br>";
                $this->project_brochure_model->get_list_default($p->project_id, true);        
            }
        }  
        */
    }
    
    function index()
    {
        // Define page variables

        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Project Manager";
        $this->data["page_heading"] = "Project Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data["projects"] = $this->project_model->get_list(-1,$this->records_per_page,1,$count_all);
        $this->data["pages_no"] = $count_all / $this->records_per_page;

		$this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
        $this->data["areas"] = $this->area_model->get_list(1,'','',$count_all);	
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/projectmanager/prebody', $this->data); 
        $this->load->view('admin/projectmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    function brochure($project_id = "")
    {
        if(empty($project_id)) {
            show_error("Invalid property ID");
        }

        $this->session->set_userdata("allow_admin", true);
        $this->session->set_userdata("frontend_user_id", 119);
        
        header("Location: " . site_url("brochure/project/" . $project_id));
    }
    /**
    * @method: project
    * @desc: The project method shows a page with the specified project id.
    * If no id is given, it means it is a new project
    * 
    * @param mixed $project_id - The project id of the page to load.
    */
    function project($project_id="")
    {
        $this->data['message'] = "";
        $postback = $this->tools_model->isPost();    
        
        if ($postback) {
            $this->_handlePost($project_id);
        }
        
        if($project_id != "") {
            
            // Load page details
            $project = $this->project_model->get_details($project_id);

            if(!$project)
            {
                // The page could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the project could not be loaded.", "Project/show - the project with an id of '$project_id' could not be loaded");
                return;            
            }
            
            $documents = $this->document_model->get_list($this->doc_type, $project_id);
            
            // Load a list of disclaimers
            $disclaimers = $this->article_model->get_list(CATEGORY_DISCLAIMERS);

            //pass project details
            $this->data["project"] = $project; 
            $this->data["metas"] = $this->project_meta_model->get_list(array('project_id'=>$project_id));
            $this->data["documents"] = $documents;                                      
            $this->data["disclaimers"] = $disclaimers;

        }
        
        if(!$postback)    
            $this->data['message'] = ($project_id == "") ? "To create a new project, enter the project details below." : "You are editing the &lsquo;<b>$project->project_name</b>&rsquo;";
        
        // Define page variables
        $this->data["meta_keywords"] = "Project Manager";
        $this->data["meta_description"] = "Project Manager";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data["page_heading"] = ($project_id != "" && isset($project)) ? $project->project_name : "Project Details";         
        
        $this->data['project_id'] = $project_id;
        $this->data["robots"] = $this->utilities->get_robots();
        $this->data["areas"] = $this->area_model->get_list(1,'','',$count_all);
        $this->data["builder"] = $this->builder_model->get_list(1,'','',$count_all);
        //$this->data["property_types"] = $this->property_model->get_property_types();
		$this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$project_id, 'type' => "project_comment"));
        $this->data["states"] = $this->tools_model->get_states(1);
        $this->data["regions"] = $this->region_model->get_list(-1,$this->records_per_page,1,$count_all);
        $this->data["rates"] = $this->project_model->get_project_rates();  
        
        // $this->load->model('article_model');
        // $this->data["asset_categories"] = $this->article_model->get_category_by_parent("Assets");
        
        if($project_id != "") //edit
        {
            if(!is_dir(FCPATH.PROJECT_FILES_FOLDER)) //FCPATH
                @mkdir(FCPATH.PROJECT_FILES_FOLDER ,DIR_WRITE_MODE);   
             
            if(!is_dir(FCPATH.PROJECT_FILES_FOLDER.$project_id))
                @mkdir(FCPATH.PROJECT_FILES_FOLDER.$project_id, DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH.PROJECT_FILES_FOLDER.$project_id."/documents"))
                @mkdir(FCPATH.PROJECT_FILES_FOLDER.$project_id."/documents",DIR_WRITE_MODE);       
            
            if(!is_dir(FCPATH.PROJECT_FILES_FOLDER.$project_id."/images"))
                @mkdir(FCPATH.PROJECT_FILES_FOLDER.$project_id."/images",DIR_WRITE_MODE);
            
            //$this->data["images"] = $this->utilities->get_files(PROJECT_FILES_FOLDER.$project_id."/images",false,false);                                
			$this->data["images"] = $this->document_model->get_list("project_gallery", $project_id);            
            $this->data["files_no"] = count($this->data["images"]);
            $this->data["images_records_per_page"] = $this->images_records_per_page; 
            
            // Load the list of brochure pages for this project
            $this->data["brochures"] = $this->project_brochure_model->get_list(array('project_id' => $project_id));
            $this->data["default_brochures"] = $this->project_brochure_model->get_list_default($project_id);
        }
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/project/prebody.php', $this->data); 
        $this->load->view('admin/project/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);      
        
    }
    
    function _handlePost($project_id)
    {
        $data = array(    
                        "project_code"          	=> '',
                        "project_name"          	=> '',                        
                        "page_body"             	=> '',
                        "quick_facts"             	=> '',
                        "project_order"         	=> '0',
                       // "prices_from"           	=> '0',
                        "meta_title"            	=> '',
                        "meta_keywords"         	=> '',
                        "meta_description"      	=> '',
                        "meta_robots"           	=> '',
                        "gallery1_caption"      	=> '',
                        "website"               	=> '',
						"google_map_code"       	=> '',
                        "enabled"               	=> '0',
						"area_id"		        	=> '0',
						"rate"		            	=> '0',						
						"ck_newsletter"		    	=> '0',
						"is_featured"		    	=> '0',
						"eoi_deposit"				=> '',
						"credit_card"				=> '',
						"account_name"				=> '',
						"BSB"						=> '',
						"account_number"			=> '',
						"reference"					=> '',
						"payment_terms_conditions" 	=> '',
                        "disclaimer_id"             => null,
                        "image_print1"             => '',
                        "image_print2"             => '',
                   );
                    
        $required_fields = array("project_code","project_name");
        $missing_fields = false;
        
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $sanitise = true;
            if($key == "page_body") $sanitise = false;
            if($key == "quick_facts") $sanitise = false;

            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,false);
            
            if($key == "project_code")
 		    {
				$data[$key] = strtolower(str_replace(" ","-",$data[$key]));
            
	            //the project_code should be unique
	            if($this->project_model->exists_project_code($data[$key],$project_id))
	            {
	               $this->error_model->report_error("Sorry, please select an other project_code to continue.", "ProjectManager/HandlerPost update - the project with an id of '$data[$key]' allready exists.");
	               return; 
	            }
        	}
        	
            // Ensure that all required fields are present    
            if(in_array($key,$required_fields) && $data[$key] == "")
            {
                $missing_fields = true;
                break;
            }
        }
        
        if ($missing_fields)
        {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "ProjectManager/HandlerPost update - the project with an id of '$project_id' could not be saved");
            return;
        }
        
        // Update Gallery Document Description
        $document_description = $this->input->post('document_description');
        $fileids = $this->input->post('fileid');
        if ($fileids && sizeof($fileids))
        {
            foreach ($fileids AS $index=>$fileid)
            {
                $doc_data = array(
                    "document_description" => $document_description[$index]
                   );
                   
                   $this->document_model->save($fileid, $doc_data, $project_id);
            }
            
        }        
        
        $edit_property = false;
        
        if(is_numeric($project_id)) {
            $edit_property = true;
        }
                
				//print_r($data);exit;
        //depeding on the $project_id do the update or insert
        $project_id = $this->project_model->save($project_id,$data);
        if(!$project_id)
        {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the project could not be saved/updated.", "ProjectManager/project save");
           return;
        }
        
        if(!$edit_property) {
            $this->document_model->add_default_documents($this->doc_type, $project_id);
            
            // Add the default brochure pages.
            $this->project_brochure_model->get_list_default($project_id, true);
        }
        else
        {
            //save documents
            $documents = $this->document_model->get_list($this->doc_type,$project_id);            
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
                   
                   $this->document_model->save($doc->id,$doc_data, $project_id);
                }
            }			
        }
        
        // Check for the delete map request
        if($this->input->post("deletemap") == 1) {
            $this->project_model->delete_map_image($project_id);
        }
        
        // Check if we need to create a static map image using Google Maps for this project
        $this->project_model->create_map_image($project_id);
        
        redirect("/admin/projectmanager/project/$project_id");
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
       $type = intval($this->tools_model->get_value("type",0,"post",0,false));
       $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
       
        switch($type)
        {
            //delete projects
            case 1:             
                
                //get project ids separated with ";"
                $project_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($project_ids!="") {
                    $arr_ids = explode(";",$project_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id)) {
                            if ($where_in != "") $where_in.=",";
                            $where_in .= $id;
                        }
                    }
                    if ($where_in!="") {
                        $this->project_model->delete($where_in);
                    }                                        
                }
                
                //get list of projects                       
                $projects = $this->project_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                //load view 
                $this->load->view('admin/projectmanager/project_listing',array('projects'=>$projects,'pages_no' => $count_all / $this->records_per_page));
            break;
            
            //page number changed
            case 2:
                
                //get list of projects                       
                $projects = $this->project_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                //load view 
                $this->load->view('admin/projectmanager/project_listing',array('projects'=>$projects,'pages_no' => $count_all / $this->records_per_page));                
                
            break;
            
            //search for a project
            case 3:
				
				$state_id = $this->input->post('state_id');
				$area_id = $this->input->post('area_id');
				
				$filters['state_id'] = $state_id;
				$filters['area_id'] = $area_id;
               
                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                
                if ($this->input->post('sort_col') && $this->input->post('sort_dir'))
                {
                	$order_by = $this->input->post('sort_col'). ' ' . $this->input->post('sort_dir');
                }
                else
                {
                	$order_by = 'p.project_name ASC';
                }
                
                $current_page = 1;
                //get list of projects
                $projects = $this->project_model->get_list(-1,$this->records_per_page, $current_page, $count_all, $search_terms, $order_by,$filters);
                
                //load view 
                $this->load->view('admin/projectmanager/project_listing',array('projects'=>$projects,'pages_no' => $count_all / $this->records_per_page));
                
            break; 
            
            case 5: //delete logo
            
                $project_id = $this->tools_model->get_value("project_id","","post",0,false);
                    
                //do we have a valid property_id ?
                if (is_numeric($project_id))
                {
                    
                    $project_folder = FCPATH;
                    
                    $project_details = $this->project_model->get_details($project_id);
                    
                    if ($project_details)
                    {
                            $logo_name = $project_details->logo;
                            
                            
                            //delete files
                            if (file_exists($project_folder.$logo_name)) unlink($project_folder.$logo_name);
                            if (file_exists($project_folder.$logo_name . "_thumb.jpg")) unlink($project_folder . $logo_name . "_thumb.jpg");
                            $this->project_model->save($project_id,array( "logo"=> "" ));      
                            
                            die("done");
                        
                        //$this->project_model->save($project_id,array( "logo"=>$prefix.$name ));      
                    }
                    else
                        die("Error: Project id not found");
                    
                   
                }
                else
                        die("Error: Not a valid project id");
            
            break;
            
            case 7: //list images
                $project_id = $this->tools_model->get_value("project_id","","post",0,false);
                $current_swfu = $this->tools_model->get_value("current_swfu_id","","post",0,false);
                $folder = "/images";
                
                //do we have a valid project_id ?
                if (is_numeric($project_id)) {
                    $project_folder = PROJECT_FILES_FOLDER.$project_id.$folder;
                    
                    //get files
                    $files = $this->document_model->get_list("project_gallery", $project_id); 
                    $count_all = $files ? $files->num_rows() : 0;
                    
                    //load view 
                    $this->load->view('admin/project/file_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));
                    
                }                
                
            break;
            
            case 8: //donwload image
                
                $file = urldecode($this->tools_model->get_value("file",0,"post",0,false));
                $project_id = $this->tools_model->get_value("project_id","","post",0,false);
                $current_swfu = $this->tools_model->get_value("current_swfu","","post",0,false);
                $folder = "/images/";
                
                $path = FCPATH.PROJECT_FILES_FOLDER.$project_id.$folder.$file;                                 
                $this->utilities->download_file($path);
           break;  
           
           case 9: //delete images
           
                //get files names separated with ";"
                $file_names = $this->tools_model->get_value("todelete","","post",0,false);
                $project_id = $this->tools_model->get_value("project_id","","post",0,false);
                $current_swfu = $this->tools_model->get_value("current_swfu_id","","post",0,false);
                $folder = "/images";
                
                if ($file_names!="") {
                    $arr_files = explode(";",$file_names);
                    
                    $this->utilities->remove_file(PROJECT_FILES_FOLDER.$project_id.$folder,$arr_files,"");                                                            
                }                    
                
           break;     
           
           case 11: //refresh document path
                $doc_id = intval($this->tools_model->get_value("doc_id",0,"post",0,false));
                
                $doc_details = $this->document_model->get_details($doc_id); 
                
                $document_path = $doc_details->document_path;          
                
                $return_data = array();
                $return_data["doc_id"] = $doc_id;
                $return_data["document_path"] = $document_path;
                
                echo json_encode($return_data);                                                              
            break;    
            
            case 12: //delete document path
                $doc_id = intval($this->tools_model->get_value("doc_id","","post",0,false));
                $project_id = $this->tools_model->get_value("project_id","","post",0,false);
                
                $this->utilities->add_to_debug("Doc ID: $doc_id, $project_id");
                
                if(($doc_id == "") || ($project_id == "")) {
                	$this->utilities->add_to_debug("projectmanager.php - Missing variables in case 12 - delete: $doc_id, $project_id");            
				}
               
                //do we have a valid project_id ?
                if (is_numeric($project_id)) {     
                    $doc_data = array(    
                            "document_path"   => ""
                    ); 
                    
                    $this->document_model->save($doc_id,$doc_data,$project_id);
                }
                     
                $return_data = array();
                $return_data["doc_id"] = json_encode($doc_id);
                
                echo json_encode($return_data);                                                              
            break; 
            
            case 14: //delete print logo
            
                $project_id = $this->tools_model->get_value("project_id","","post",0,false);
                    
                //do we have a valid property_id ?
                if (is_numeric($project_id)) {
                    $project_folder = FCPATH;
                    
                    $project_details = $this->project_model->get_details($project_id);
                    
                    if ($project_details) {
                            $logo_name = $project_details->logo_print;
                            
                            //delete files
                            if (file_exists($project_folder.$logo_name)) unlink($project_folder.$logo_name);
                            if (file_exists($project_folder.$logo_name . "_thumb.jpg")) unlink($project_folder . $logo_name . "_thumb.jpg");
                            $this->project_model->save($project_id,array( "logo_print"=> "" ));      
                            
                            die("done");
                        
                        //$this->project_model->save($project_id,array( "logo"=>$prefix.$name ));      
                    } else {
                        die("Error: Project id not found");
                    }
                } else {
                    die("Error: Not a valid project id");
                }
                
            break;        	                        
            
            case 15: // Add new Meta data
            
                $error_message = '';
                $data = array();
                
                $name = isset($_POST['title']) ? $_POST['title'] : '';
                $value = isset($_POST['content']) ? $_POST['content'] : '';
                $meta_id = isset($_POST['meta_id']) ? $_POST['meta_id'] : '';
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : 0;
                $icon_image = isset($_POST['icon_image']) ? $_POST['icon_image'] : 0;

                $data = array(
                                'name' => $name,
                                'value' => $value,
                                'project_id' => $project_id,
                                'icon_path' => $icon_image
                            );
                
                if (!empty($meta_id)) {
                    $this->project_meta_model->save($meta_id,$data);
                } else {
                    $this->project_meta_model->save('',$data);
                }
                
                echo 'OK';
                exit();

            case 16: // Load Meta Data
            
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : 0;
                $metas = $this->project_meta_model->get_list(array('project_id'=>$project_id));
                
                $html  = '<tr>';
                $html .= '    <th width="10%">ID</th>';
                $html .= '    <th align="left">Section Name</th>';
                $html .= '    <th>Action</th>';            
                $html .= '</tr>';
                
                if ($metas) {
                    foreach ($metas->result() AS $meta)
                    {
                        $html .= '<tr>';
                        $html .= '    <td class="admintabletextcell" align="center">'.$meta->id.'</td>';
                        $html .= '    <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="'.$meta->id.'" class="btnedit">'.$meta->name.'</a></td>';
                        $html .= '    <td class="center"><input type="checkbox" class="metatodelete" value="'.$meta->id.'" /></td>';
                        $html .= '</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;
            
            case 17: // Delete Meta Data
            
                $status = 'FAILED';
                $meta_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($meta_ids != "") {
                    $arr_ids = explode(";",$meta_ids);
                    if (sizeof($arr_ids)) {
                        $this->project_meta_model->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;
            
            case 18: // Load form edit meta data
            
                $meta_id = isset($_POST['meta_id']) ? $_POST['meta_id'] : '';
                $meta = $this->project_meta_model->get_details($meta_id);
                if ($meta) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'name' => $meta->name,
                	                       'value' => $meta->value,
                	                       'meta_id' => $meta->id,
                	                       'icon_path' => $meta->icon_path
                	                   );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                	                       'status' => 'FAILED',
                	                   );
                    echo json_encode($return_data);
                }
                
            break;

			case 19: // save file desc
	            $ids = isset($_POST['ids']) ? (array) $_POST['ids'] : array();
	            $aDesc = isset($_POST['desc']) ? (array) $_POST['desc'] : array();
	            foreach ($ids as $index=>$id)
	            {
	                $desc = $aDesc[$index];
	                $this->db->update('documents', array('document_description'=>$desc), array('id'=>$id));
	            }
	            echo 'OK';
            break;
            
			case 20: // sfeature project
            	$project_ids = $this->tools_model->get_value("tofeature","","post",0,false);
				$action = $this->input->post('action');
				
                if ($project_ids!="") {

                    $arr_ids = explode(";",$project_ids);
                    
                	switch ($action) {
                		case 'feature':
                			foreach($arr_ids as $id)
		                    {
		                    	if (!empty($id))
		                    		$this->project_model->save($id, array('is_featured' => 1));	
		                    }
            			break;
                			
            			case 'unfeature':
                			foreach($arr_ids as $id)
		                    {
		                        if (!empty($id))
		                    		$this->project_model->save($id, array('is_featured' => 0));
		                    }
            			break;
                	
                		default:
            			break;
                	}
                }
                $projects = $this->project_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                //load view 
                $this->load->view('admin/projectmanager/project_listing',array('projects'=>$projects,'pages_no' => $count_all / $this->records_per_page));
               
            break;      
            
			case 21: // newsletter project
            	$project_ids = $this->tools_model->get_value("tonewsletter","","post",0,false);
				$action = $this->input->post('action');
				
                if ($project_ids!="") {

                    $arr_ids = explode(";",$project_ids);
                    
                	switch ($action) {
                		case 'newsletter':
                			foreach($arr_ids as $id)
		                    {
		                    	if (!empty($id))
		                    		$this->project_model->save($id, array('ck_newsletter' => 1));	
		                    }
            			break;
                			
            			case 'unnewsletter':
                			foreach($arr_ids as $id)
		                    {
		                        if (!empty($id))
		                    		$this->project_model->save($id, array('ck_newsletter' => 0));
		                    }
            			break;
                	
                		default:
            			break;
                	}
                }
                $projects = $this->project_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                //load view 
                $this->load->view('admin/projectmanager/project_listing',array('projects'=>$projects,'pages_no' => $count_all / $this->records_per_page));
               
            break;        
            
			case 22: // website project
            	$project_ids = $this->tools_model->get_value("towebsite","","post",0,false);
				$action = $this->input->post('action');
				
                if ($project_ids!="") {

                    $arr_ids = explode(";",$project_ids);
                    
                	switch ($action) {
                		case 'website':
                			foreach($arr_ids as $id)
		                    {
		                    	if (!empty($id))
		                    		$this->project_model->save($id, array('enabled' => 1));	
		                    }
            			break;
                			
            			case 'unwebsite':
                			foreach($arr_ids as $id)
		                    {
		                        if (!empty($id))
		                    		$this->project_model->save($id, array('enabled' => 0));
		                    }
            			break;
                	
                		default:
            			break;
                	}
                }
                $projects = $this->project_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                //load view 
                $this->load->view('admin/projectmanager/project_listing',array('projects'=>$projects,'pages_no' => $count_all / $this->records_per_page));
               
            break;

			case 23: // Add note
                
                $error_message = '';
                $data = array();
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : 0;
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $note_date = isset($_POST['note_date']) ? $_POST['note_date'] :date('Y-m-d H:i:s');
                $note_date = $this->utilities->uk_to_isodate($note_date);
                
				$views = isset($_POST['view']) ? $_POST['view'] : '';
				
                $data = array(
                                'type' => 'project_comment',
                                'comment' => $comment,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $project_id,
                                'datetime_added' => $note_date,
								'permission' => $views
                            );
                
                if (!empty($comment_id)) {
                    $this->commentmd->save($comment_id,$data);
                } else {
                    $this->commentmd->save('',$data);
                }
                
                if (isset($_POST['getlist'])) {
                    $this->load->model('comment_model','commentmd');
                    $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$project_id, 'type' => "project_comment"));
                	$this->load->view('admin/property/note_list', $this->data);
                } else {
                    echo 'OK';
                    exit();
                }
                
            break;       
            
            case 24:
                // Load Comments
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'project_comment','foreign_id'=>$project_id) );
                
                $html ='<tr>';
                $html.='    <th width="10%">ID</th>';
                $html.='    <th align="left">Note</th>';
                $html.='    <th width="10%">Delete</th>';
                $html.='</tr>';
                
                if ($comments) {
                    foreach ($comments->result() AS $index=>$comment)
                    {
                        $html.='<tr id="acomment_'.$comment->id.'" class="'.$index%2 ? 'admintablerowalt' : 'admintablerow'.'">';
                        $html.='    <td class="admintabletextcell" align="center">'.$comment->id.'</td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;">';
                        $html.='        <span style="font-weight:bold">'.trim("$comment->first_name $comment->last_name").':</span>';
                        $html.='        <br />';
                        $html.='        "'.nl2br($comment->comment).'"';
                        $html.='    </td>';
                        $html.='    <td class="center"><input type="checkbox" class="commenttodelete" value="'.$comment->id.'" /></td>';
                        $html.='</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;
            
            case 25: // Delete note
            
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
            
            case 26: // Edit Note
                
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
            
            case 27: // Load asset of catelogy
                
                $asset_category_id = isset($_POST['asset_category_id']) ? $_POST['asset_category_id'] : '';
                $asset_items = $this->article_model->get_articles_from_category($asset_category_id);
                if ($asset_items) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'html' => $this->utilities->print_select_options($asset_items,"article_id","article_title"),
                	                   );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                	                       'status' => 'FAILED',
                	                   );
                    echo json_encode($return_data);
                }
            
            break;
            
            case 28: // add a page of brochure
                
                $type_brochure = isset($_POST['type_brochure']) ? $_POST['type_brochure'] : '';
                // $asset_category_id = isset($_POST['asset_category_id']) ? $_POST['asset_category_id'] : '';
                // $asset_id = isset($_POST['asset_id']) ? $_POST['asset_id'] : '';
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
                
                $data = array(
                              'type'         => $type_brochure
                            );
                $brochure_id = $this->project_brochure_model->save($project_id, $data);
                
                if ($brochure_id !== false && is_numeric($brochure_id)) {
                    echo json_encode(array('status' => 'OK'));
                } else {
                    echo json_encode(array('status' => 'FAILED'));
                }
            
            break;
            
            case 29: // load list page of brochure
                
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
                
                if (is_numeric($project_id)) {
                
                    // $this->project_brochure_model->checkDynamicBrochure($project_id);
                    
                    $filters['project_id'] = $project_id;
                            
                    $brochures = $this->project_brochure_model->get_list($filters);
                    
                    if(!$brochures)
                    {
                        $brochures = $this->project_brochure_model->get_list_default($project_id, true);
                    }
                    $return_data = array();
                    $return_data['status'] = 'OK';
                    $return_data['html'] = '';
                    if ($brochures) {
                        $html = '';
                        $count = $brochures->num_rows();
                        foreach ($brochures->result() AS $key => $brochure)
                        {
                            $html .= '<tr brochureid="'.$brochure->brochure_id.'">';
                            $html .= '    <td class="center" page="'.$brochure->page.'">'.$brochure->page.($key != 0 ? '<span class="moveup">&#x25B2;</span>' : '').($key != ($count - 1) ? '<span class="movedown">&#x25BC;</span>' : '').'</td>';
                            $html .= '    <td class="center">'.$brochure->type.'</td>';
                            $html .= '    <td class="center"><input type="text" class="brochure_header" value="' . $brochure->heading . '"/></td>';
                            // $html .= '    <td class="center"><a href="'.base_url().'admin/contentmanager/category/'.$brochure->asset_category.'">'.$brochure->category_name.'</a></td>';
                            // $html .= '    <td class="center"><a href="'.base_url().'admin/contentmanager/article/'.$brochure->asset.'/'.$brochure->asset_category.'">'.$brochure->article_title.'</a></td>';
                            $html .= '    <td class="center">'.substr($brochure->image, strrpos($brochure->image, '/') + 1).'</td>';
                            $html .= '    <td class="center"><input type="checkbox" class="deletebrochure" value="'.$brochure->brochure_id.'" /></td>';
                            $html .= '</tr>';
                        }
                        
                        $return_data['html'] = $html;
                    }
                    echo json_encode($return_data);
                } else {
                    echo json_encode(array('status' => 'FAILED'));
                }
            
            break;
            
             case 30: // delete brochure
                
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
                $selectedvalues = isset($_POST['selectedvalues']) ? $_POST['selectedvalues'] : '';
                
                $arr_bruchures = explode(';', trim($selectedvalues, ';'));
                
                $result = true;
                foreach($arr_bruchures as $bruchure)
                {
                    $result = $this->project_brochure_model->delete($bruchure, $project_id);
                }
                
                if ($result) {
                    echo json_encode(array('status' => 'OK'));
                } else {
                    echo json_encode(array('status' => 'FAILED'));
                }
            
            break;
            
            case 31: // update page brochure
                
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
                $brochure_id = isset($_POST['brochure_id']) ? $_POST['brochure_id'] : '';
                $page = isset($_POST['page']) ? $_POST['page'] : '';
                $change = isset($_POST['change']) ? $_POST['change'] : '';
                
                $result = $this->project_brochure_model->updatePage($project_id, $brochure_id, $page, $change);
                
                
                if ($result) {
                    echo json_encode(array('status' => 'OK'));
                } else {
                    echo json_encode(array('status' => 'FAILED'));
                }
            
            break;
            
            case 32: // update brochure header
                
                $brochure_id = isset($_POST['brochure_id']) ? $_POST['brochure_id'] : '';
                $heading = isset($_POST['heading']) ? $_POST['heading'] : '';
                $project_id = isset($_POST['project_id']) ? $_POST['project_id'] : '';
                
                $data = array('heading'      => $heading,
                              'brochure_id'  => $brochure_id,
                            );
                            
                $brochure_id = $this->project_brochure_model->save($project_id, $data);
                
                if ($brochure_id !== false && is_numeric($brochure_id)) {
                    echo json_encode(array('status' => 'OK'));
                } else {
                    echo json_encode(array('status' => 'FAILED'));
                }
            
            break;    
                        
        }
    }
    
    function upload_file($upload_type, $project_id, $doc_id='', $doc_name='', $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($project_id)))
		{
			die ('{error: "Invalid upload type $upload_type or project id $project_id"}');
		}
		
		// Handle a logo upload
		if(($upload_type == "hero_image1") || ($upload_type == "hero_image2") || ($upload_type == "documents") || ($upload_type == "gallery_image") || ($upload_type == "brochure_image"))
		{
            // Load the project in question
            $project = $this->project_model->get_details($project_id);
            if(!$project)
            {
				die ('{error: "Invalid project"}');	
            }
            
            
            $path = ABSOLUTE_PATH . PROJECT_FILES_FOLDER . $project_id . "/";
            if ( !is_dir($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
                
            	$path = ABSOLUTE_PATH . PROJECT_FILES_FOLDER . $project_id . "/documents/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
                
            } else if ($upload_type == 'gallery_image') {
                
            	$path = ABSOLUTE_PATH . PROJECT_FILES_FOLDER . $project_id . "/images/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            } else if ($upload_type == 'brochure_image') {
                
            	$path = ABSOLUTE_PATH . PROJECT_FILES_FOLDER . $project_id . "/brochure_images/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            } 
            
            if ($upload_type == 'hero_image2') {
                $filename = $filename.'_print.jpg';
            }
            
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
					"document_type" => "project_document",
					"foreign_id" => $project_id,
					"document_name" => $doc_name,
					"document_path" => PROJECT_FILES_FOLDER . $project_id . "/documents/" . $filename
				);
				
                $return_path = PROJECT_FILES_FOLDER . $project_id . "/documents/" . $filename;
				$this->document_model->save($doc_id, $doc_data, $project_id, "project_document", $use_order = TRUE);				
				
			} elseif ($upload_type == 'hero_image1') {
			    
			    $logo_folder1 = FCPATH.PROJECT_FILES_FOLDER.$project_id."/";
            	$thumb_path = $logo_folder1 . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($logo_folder1.$filename, $thumb_path, $error_message,THUMB_PROJECT_WIDTH,THUMB_PROJECT_HEIGHT);
			    
			    // Update the article with the logo.
	        	$update_data = array("logo" => PROJECT_FILES_FOLDER . $project_id. "/" . $filename);
				$this->project_model->save($project_id, $update_data);
				
				$return_path = site_url(PROJECT_FILES_FOLDER . $project_id. "/" . $filename);
				
			} elseif ($upload_type == 'hero_image2') {
			    
			    $logo_folder2 = FCPATH.PROJECT_FILES_FOLDER.$project_id."/";
            	$thumb_path = $logo_folder2 . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($logo_folder2.$filename, $thumb_path, $error_message,THUMB_PROJECT_WIDTH,THUMB_PROJECT_HEIGHT);
			    
			    // Update the article with the logo name.
	        	$update_data = array("logo_print" => PROJECT_FILES_FOLDER . $project_id. "/" . $filename);
				$this->project_model->save($project_id, $update_data);
				
				$return_path = site_url(PROJECT_FILES_FOLDER . $project_id. "/" . $filename);
				
			}  elseif ($upload_type == "gallery_image") {

  			    $gallery_folder = FCPATH.PROJECT_FILES_FOLDER.$project_id."/images/";
            	$thumb_path = $gallery_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($gallery_folder.$filename, $thumb_path, $error_message,THUMB_PROJECT_WIDTH,THUMB_PROJECT_HEIGHT);
  			    
                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "project_gallery",
					"foreign_id" => $project_id,
					"document_name" => $filename,
					"document_path" => PROJECT_FILES_FOLDER . $project_id . "/images/" . $filename
				);
                
				$return_path = PROJECT_FILES_FOLDER . $project_id . "/images/" . $filename;
				
				$this->document_model->save("", $img_data, $project_id, "project_gallery", $use_order = TRUE);				
			} elseif ($upload_type == "brochure_image") {

  			    $brochure_folder = FCPATH.PROJECT_FILES_FOLDER.$project_id."/brochure_images/";
            	$thumb_path = $brochure_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($brochure_folder.$filename, $thumb_path, $error_message,THUMB_PROJECT_WIDTH,THUMB_PROJECT_HEIGHT);
  			    
				$return_path = PROJECT_FILES_FOLDER . $project_id . "/brochure_images/" . $filename;
				
                $data = array('image'   => $return_path, 'type' => 'manual');
                            
                $brochure_id = $this->project_brochure_model->save($project_id, $data);
            }

			$return = array();
			$return["status"] = "OK";
			$return["fileName"] = $return_path;
			$return["success"] = true;	
			
			echo json_encode($return);	
		}
		else
		{
			die ('{error: "Invalid file type"}');
		}	
    }
}