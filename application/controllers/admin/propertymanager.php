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

class Propertymanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = 50;
    private $images_records_per_page = 3;
    private $documents_records_per_page = 3;
    private $doc_type = "property_document";
    private $stage_type = "stage";
    private $user_id = 0;    

    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("property_model");
        $this->load->model("project_model");
        $this->load->model("document_model");
        $this->load->model("resources_model");
        $this->load->model("area_model");
		$this->load->model("state_model");
		$this->load->model("region_model");
        $this->load->model('builder_model');
		$this->load->model("lawyer_model");
		$this->load->model("email_model");
		$this->load->model("property_stages_model");
        $this->load->model('comment_model', 'commentmd');        
        $this->load->model('keydate_model', 'keydatemd'); 
        
		
        $this->load->library("utilities");                    
        $this->load->helper("form");                    
                
        //if the $ci_session is passed in post, it means the swfupload has made the POST, don't check for login
        $ci_session = $this->tools_model->get_value("ci_session","","post",0,false);
      
        if ($ci_session == "")
        {
            // Check for a valid session
            if (!$this->login_model->getSessionData("logged_in"))            
                redirect("admin/login");       
        } 

        $this->user_id = $this->login_model->getSessionData("id");
    }
        
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Property Manager";
        $this->data["page_heading"] = "Listings";
        $this->data["name"] = $this->login_model->getSessionData("firstname");

        $properties_data = array(
            'enabled' => -1,
            'limit' => $this->records_per_page,
            'page_no' => 1,
            'archived' => 0
        );
        
        $this->data["properties"] = $this->property_model->get_list($properties_data, $count_all);

        $this->data["pages_no"] = $count_all / $this->records_per_page;        
        $this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
        $this->data["areas"] = $this->area_model->get_list(1,'','',$count_all);
        $this->data["builders"] = $this->builder_model->get_list(1,'','',$count_all);
        $this->data['status'] = $this->property_model->get_property_status();
		
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/propertymanager/prebody', $this->data); 
        $this->load->view('admin/propertymanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    /**
    * @method: property
    * @desc: The property method shows a page with the specified property id.
    * If no id is given, it means it is a new property
    * 
    * @param mixed $property_id - The property id of the page to load.
    */
    function property($property_id = "")
    {
        $this->data['message'] = "";
        $postback = $this->tools_model->isPost();    
       
        if ($postback) {
            $this->_handlePost($property_id);
        }
        
        if($property_id != "") //edit
        {
            // Load page details
            $property = $this->property_model->get_details($property_id);
			
            if(!$property)
            {
                show_error("Invalid property ID");
            } 

            $documents = $this->document_model->get_list($this->doc_type, $property_id);
            $selected_projects = $this->property_model->get_projects($property_id);
            
            
            // If the property has been sold, make sure the default property stages are setup and uptodate.
            if($property->status != "available")
            {

                $this->property_stages_model->add_default_property_stages($property_id);            
            }
            
            $this->data['stages'] = $this->property_stages_model->get_list(-1,$property_id,'','',$count_all);
            
            if(!$property) {
                // The page could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the property could not be loaded.", "Property/show - the roperty with an id of '$property_id' could not be loaded");
                return;            
            } else {
                //pass page details
                $this->data["property"] = $property;                       
                $this->data["documents"] = $documents;
                $this->data["selected_projects"] = $selected_projects;
                $this->data['property_types'] = $this->resources_model->get_list('property_type','id asc');
                $this->data['contract_types'] = $this->resources_model->get_list('contract_type','id asc');
				$this->data['title_types'] = $this->resources_model->get_list('title_type','id asc');
				$this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$property_id, 'type' => "property_comment"));
				$this->data['keydates'] = $this->keydatemd->get_list(array('type'=>'property_key_date','foreign_id'=>$property_id) );
				
				$this->load->model("item_history_model");
                $this->data['histories'] = $this->item_history_model->get_list('', '', $count_all, '', $property_id);
                
                $advisors = $this->users_model->get_list(1,'','',$count_all,'',USER_TYPE_ADVISOR);
                $partners = $this->users_model->get_partners_by_advisor_id($property->advisor_id);
                //$investors = $this->users_model->get_investor_by_advisor_id($property->advisor_id);
                
                $investors = $this->users_model->get_list(1, $limit = "", $page_no = "", $count_all, $search_term = "", 
                    $user_type = array(USER_TYPE_INVESTOR, USER_TYPE_LEAD), $filters = array(), $select_sql = "");
                
                $this->data['advisors'] = $advisors;
                $this->data['partners'] = $partners;
                $this->data['investors'] = $investors;
                
                // If the property has been approved, load in who approved it
                $this->data["approved_by_user"] = false;
                if(($property->status != "pending") && (is_numeric($property->approved_by_id)))
                {
                    $this->data["approved_by_user"] = $this->users_model->get_details($property->approved_by_id);   
                }
                
            }
        }
        else 
        {
            $this->data["selected_projects"] = "";
        }
        
        if(!$postback)    
		    $this->data['message'] = ($property_id == "") ? "To create a new property, enter the property details below." : "You are editing the &lsquo;<b>$property->address</b>&rsquo;";
            //By mayur-Taskseveryday
			$this->data['property_types'] = $this->resources_model->get_list('property_type','id asc');
		    $this->data['contract_types'] = $this->resources_model->get_list('contract_type','id asc');
			$this->data['title_types'] = $this->resources_model->get_list('title_type','id asc');
            
        $status = $this->property_model->get_property_status();
       
	   //Load Stages
		
		$pro_stages = $this->property_model->get_property_stages();
	   
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data["page_heading"] = ($property_id != "" && isset($property)) ? $property->lot. " ".$property->address : "Property Details";         
       
        $this->data["builder"] = $this->builder_model->get_list(1,'','',$count_all);
		$this->data["builders"] = $this->lawyer_model->get_list(-1,$this->records_per_page,1, $count_all, $search_term = "", "", $order_by = "l.company_name ASC");
		 
        $this->data['property_id'] = $property_id;
        $this->data["states"] = $this->property_model->get_states(1);        
        $this->data["projects"] = $this->project_model->get_projects('p.project_name ASC');
        $this->data["status"] = $status;
        $this->data["pro_stages"] = $pro_stages;
        $this->data["suburbs"] = $this->resources_model->get_list("suburb");
        $this->data["areas"] = $this->area_model->get_list(1,'','',$count_all);
		$this->data["region_states"] = $this->state_model->get_list(1,'','',$count_all);
		$this->data["regions"] = $this->region_model->get_list(1,'','',$count_all);
        
        if($property_id != "") { //edit
            if(!is_dir(ABSOLUTE_PATH."property"))
                @mkdir(ABSOLUTE_PATH."property" ,DIR_WRITE_MODE);   
             
            if(!is_dir(ABSOLUTE_PATH.PROPERTY_FILES_FOLDER.$property_id))
                @mkdir(ABSOLUTE_PATH.PROPERTY_FILES_FOLDER.$property_id, DIR_WRITE_MODE);
                
            if(!is_dir(ABSOLUTE_PATH.PROPERTY_FILES_FOLDER.$property_id."/documents"))
                @mkdir(ABSOLUTE_PATH.PROPERTY_FILES_FOLDER.$property_id."/documents",DIR_WRITE_MODE);       
            
            if(!is_dir(ABSOLUTE_PATH.PROPERTY_FILES_FOLDER.$property_id."/images"))
                @mkdir(ABSOLUTE_PATH.PROPERTY_FILES_FOLDER.$property_id."/images",DIR_WRITE_MODE);           
                
            //$this->data["images"] = $this->utilities->get_files(PROPERTY_FILES_FOLDER.$property_id."/images",false,false);                                
            $this->data["images"] = $this->document_model->get_list("property_gallery", $property_id); 
            
            $this->data["files_no"] = count($this->data["images"]);
            $this->data["images_records_per_page"] = $this->images_records_per_page;            
        }
		 		
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/property/prebody.php', $this->data); 
        $this->load->view('admin/property/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);      
    }
    /*

    "#7#8#" will be an array("7","8") 

    */

    function get_category_ids($category_string)
    {

        $arr_categories = explode("#",$category_string);        
        $selected_categories = array();

        foreach($arr_categories as $row)
        {
            if(is_numeric($row))
                $selected_categories[] = $row;
        }

        return $selected_categories;                
    }

    /*

    array("7","8") will be a string like "#7#8#"

    */

    function set_category_ids($category_array)
    {
        $category_string = implode("#",$category_array);

        if($category_string!="")
            $category_string = "#".$category_string."#";

        return $category_string;    
    }

    function _handlePost($property_id)
    {  

        $data = array(      
                        "title"                 => '',
                        "lot"                   => '-1',
                        "address"               => '',
                        "suburb"                => '',
                        "state_id"              => 0,
                        "area_id"               => 0,
						"region_state_id"		=> 0,
						"region_id" 			=> 0,
                        "postcode"              => '',
                        "design"                => '',
                        "bathrooms"             => '-1',
                        "bedrooms"              => '-1',
                        "garage"                => '-1',                                                                        
                        "frontage"              => '-1',                                                                        
                        "house_area"            => '-1',
                        "land"                  => '-1',
                        "land_price"            => '-1',
                        "house_price"           => '-1',
                        "total_price"           => '-1',
                        "council_rates"         => '-1',
                        "other_fee_amount"         => '0',
                        "other_fee_text"         => '',
                        "est_stampduty_on_purchase"         => '0',
                        "estimated_gov_transfer_fee"         => '0',
                        "approx_rent"           => '-1',
                        "rent_yield"            => '-1',
                        "facade"                => '-1',
                        "hero_image"            => '',
                        "enabled"               => '1',
//                        "status"                => 'available',                        
                        "user_id"               => '-1',
                        "title_due_date"        => null,
                        "overview"              => '',
                        "page_body"             => '',
                        "internal_comments"     => '',
                        "misc_comments"         => '',
                        "special_features"      => '',
        				"featured"				=> '0',
        				"archived"				=> '0',
        				"nras"				    => '0',
        				"titled"				=> '0',
        				"study"				    => '0',
        				"estimated_date"		=> '',
        				"nras_provider"			=> '-1',
        				"nras_rent"				=> '-1',
        				"nras_fee"				=> '-1',
        				// "smsf"				    => '0',
        				"builder_id"			=> '0',
				        "owner_corp"  			=> '',
				        "property_type_id"  	=> 0,
				        "title_type_id"  	    => 0,
						"contract_type_id"  	=> 0,
                        "tracker_type"      => 'Construction',
                        "total_commission"      => '0',
                        "stage1_payment"      => '0',
                        "stage1_percentage"      => '0',
                        "stage1_payable"      => '',
                        "stage2_payment"      => '0',
                        "stage2_percentage"      => '0',
                        "stage2_payable"      => '',                        
                        "stage3_payment"      => '0',
                        "stage3_percentage"      => '0',
                        "stage3_payable"      => '',  
                        "advisor_comments"         => '',                    
                        "image_print1"         => '',                     
                        "image_print2"         => '',                     
                    );
        
        $project_id = intval($this->tools_model->get_value("project_id",0,"post",0,false));
       
        if($project_id == 0) {
            // Something went wrong whilst saving the user data.
            $this->error_model->report_error("Sorry, please select a project.", "Propertymanager/property project is not selected");
            return;
        }
        
        /* ADD REQUIRED FIELDS */

        $required_fields = array("state_id", "address", "lot", "postcode","title","project_id",'builder_id');

        if($property_id != "") {
             array_push($required_fields, "lot");  
        }

        /* END REQUIRED FIELDS */

        $missing_fields = false;

        //fill in data array from post values

        foreach($data as $key=>$value)
        {

            $sanitise = true;
            if(($key == "page_body") || ($key == "overview")) $sanitise = false;
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,$sanitise);

            // Ensure that all required fields are present    

            if(in_array($key,$required_fields) && $data[$key] == "") {
                $missing_fields = true;
                break;
            }
        }

        if ($missing_fields) {

            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "PropertyManager/HandlerPost update - the property with an id of '$property_id' could not be saved");
            return;
        }  

        $old_property = false;

        if(is_numeric($property_id))
            $old_property = true;
			
		//depeding on the $property_id do the update or insert

        $data['smsf'] = $this->property_model->set_smsf($data['contract_type_id']);
        
        //die("SMSF: " . $data['smsf']);
        
		$property_id = $this->property_model->save($property_id,$data);

        if(!$property_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the property could not be saved/updated.", "Propertymanager/property save");
           return;
        }

        if(!$old_property) { //new property
            
            $this->document_model->add_default_documents($this->doc_type, $property_id);
            //$this->property_stages_model->add_default_property_stages($property_id);
            
        } else {    //save documents

            $documents = $this->document_model->get_list($this->doc_type,$property_id);            
            if($documents) {
                foreach($documents->result() as $doc)
                {
                   $doc_name = $this->tools_model->get_value("doc_".$doc->id."_name","","post",0,false); 
				   $extra_data = $this->tools_model->get_value("doc_".$doc->id."_extra_data","","post",0,false); 
                  
                   $doc_data = array(
                        "document_name" => $doc_name,
                        "extra_data" => $extra_data,
                   );

                   $this->document_model->save($doc->id,$doc_data, $property_id);
                }
            }
        }
		
        // save projects

        $this->property_model->delete_projects($property_id);
        $projects = array();
        $project_id = intval($this->tools_model->get_value("project_id",-1,"post",0,false));
        if($project_id != -1) {
            array_push($projects,$project_id);                
            if(count($projects)) {
                foreach( $projects as $proj)
                    $this->property_model->save_projects($property_id, $proj);
            }                

        }    
        
        if((is_numeric($project_id)) && ($project_id > 0)) {
            $project = $this->project_model->get_details($project_id);
            
            if(($project) && ($project->area_id > 0)) {
                $this->property_model->save($property_id, array("area_id" => $project->area_id));     
            }
        }
		
		//$this->property_model->set_project_prices_from();
		
		if(!$old_property)
        {
            //Add history : When the property is first created and the status is set as pending
            $this->load->model("item_history_model");
            $history_date = array(
                'created_dtm'   => date('Y-m-d H:i:s'),
                'foreign_type'  => 'property',
                'foreign_id'    => $property_id,
                'change_type'   => 'status',
                'user_id'       => $this->user_id,
                'old_value'       => '',
                'new_value'       => DEFAULT_PROPERTY_STATUS,
            );
            $this->item_history_model->save($history_date);
        }
		
       redirect("/admin/propertymanager/property/".$property_id);
    }
    
    function clone_property($property_id)
    {
        if(!is_numeric($property_id)) show_error("Invalid property id");
        
        if(!file_exists(COPYPATH)) show_error("Couldn't find copy!");
        
        $path_old = ABSOLUTE_PATH . "property/$property_id/";
        
        if(!file_exists($path_old)) {
            mkdir($path_old);  
            
            if(!file_exists($path_old)) show_error("Invalid source folder path: $path_old");  
        }
        
        // Load the property in question
        $sp = $this->property_model->get_details($property_id);
        if(!$sp) show_error("Couldn't load source property");   
        
        $this->db->trans_start();
        
        // Get a list of the field names in the properties table
        $fields = $this->db->list_fields('properties');
        
        // Copy the fields form the source property into an array ready for inserting.
        $data = array();
        
        foreach ($fields as $field)
        {
            // ignore some fields
            if($field == "property_id") continue;
            
            // Manipulate others fields
            if($field == "title") {
                //$data[$field] = $sp->$field . "_" . time();        
                $data[$field] = $sp->$field;
            } else {
                // Straight copy
                $data[$field] = $sp->$field;
            }
        } 
        
        // Insert the new property
        $new_id = $this->property_model->save("", $data);
        if(!$new_id) show_error("An error occured whilst trying to create the new property");    
        
        // Copy the property project records
        $property_projects = $this->db->get_where("property_project", array("property_id" => $property_id));
        if(!$property_projects) show_error("No property project records");
        
        foreach($property_projects->result() as $pp)
        {
            $data = array();
            $data["property_id"] = $new_id;   
            $data["project_id"] = $pp->project_id;
            
            if(!$this->db->insert("property_project", $data)) show_error("Couldn't insert property project record");
        }
        
        $doc_types = array("property_gallery", "property_document");
        
        foreach($doc_types as $dt)
        {
            // Copy the gallery documents for this property to the next
            $docs = $this->document_model->get_list($doc_type = $dt, $property_id); 
            if($docs)
            {
                foreach($docs->result() as $doc)
                {
                    $data = array();
                    $data["document_type"] = $doc->document_type;
                    $data["foreign_id"] = $new_id;
                    $data["document_name"] = $doc->document_name;
                    $data["document_path"] = str_replace("/$property_id/", "/$new_id/", $doc->document_path);
                    $data["order"] = $doc->order;
                    $data["document_no"] = $doc->document_no;
                    $data["extra_data"] = $doc->extra_data;
                    $data["document_description"] = $doc->document_description;
                    $data["broadcast_access_level_id"] = $doc->broadcast_access_level_id;
                    $data["is_exact"] = $doc->is_exact;
                    $data["icon"] = $doc->icon;
                    
                    $this->document_model->save("", $data);
                }  
            }
        }
        
        // Create the property folder
        
        $path_new = ABSOLUTE_PATH . "property/$new_id/";
        mkdir($path_new);
        
        if(!file_exists($path_new)) show_error("Invalid dest folder path: $path_new");
        
        // Copy all of the subfolders and files
        $cmd = COPYPATH . " -rfp " . $path_old . "/* " . $path_new . "/";
        shell_exec($cmd);
        
        // Complete the transaction
        $this->db->trans_complete();
        
        // Redirect the user to the edit screen
        redirect("/admin/propertymanager/property/$new_id");
    }
    
    function brochure($property_id = "")
    {
        if(empty($property_id)) {
            show_error("Invalid property ID");
        }

        $this->session->set_userdata("allow_admin", true);
        $this->session->set_userdata("frontend_user_id", 119);
        
        header("Location: " . site_url("brochure/property/" . $property_id));
    }

    //handles all ajax requests within this page

    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));

        switch($type)
        {
            //delete property
            case 1:
            //get property ids separated with ";"

                $property_ids = $this->tools_model->get_value("todelete","","post",0,false);
                $property_ids =rtrim( $property_ids ,";");
				$arr_ids = explode(";",$property_ids);
				
				foreach($arr_ids as $property_id)
				{
                    if(!is_numeric($property_id))
                        continue;
					$property_id = $property_id;
					
					$property = $this->property_model->get_details($property_id);
					
					
					if($property->status == 'pending' || $property->status == 'available')
					{
								
						if ($property_id!="") {

							$where_in = "";

							if (is_numeric($property_id)) {
									if ($where_in != "") $where_in.=",";
									$where_in .= $property_id;
									$this->property_stages_model->delete_by_property($property_id);
								}
						  
							if ($where_in != "") {                    
								$this->property_model->delete($where_in);
							}
						}
					}
					else
					{
						$message = 'You cannot delete the property when its '.$property->status.' !!!';
						echo "<script> alert('$message'); </script>";
					}
				}	
                //get list of properties
                $search_terms = $this->tools_model->get_value("kw","","post",0,false);
                $archived = $this->tools_model->get_value("archived","","post",0,false);
                $state_id = $this->tools_model->get_value("state_id","","post",0,false);
                $area_id = $this->tools_model->get_value("area_id","","post",0,false);
                $status = $this->tools_model->get_value("status","","post",0,false);
                
                $properties_data = array(
                    'enabled' => -1,
                    'limit' => $this->records_per_page,
                    'page_no' => $current_page,'search_terms' => $search_terms,
                    'archived' => $archived,
                    'state_id' => $state_id,
                    'area_id' => $area_id,
                    'status' => $status
                );

                $properties = $this->property_model->get_list($properties_data, $count_all);
                
                //load view 
                $this->load->view('admin/propertymanager/property_listing',array('properties'=>$properties,'pages_no' => $count_all / $this->records_per_page));

            break;

            case 2:
                //get list of properties
                $offset = "";
                if($current_page > 1) {
                    $offset = intval(($current_page - 1) * $this->records_per_page);                    
                }
                
                $properties_data = array(
                    'enabled' => -1,
                    'limit' => $this->records_per_page,                    
                    'page_no' => $current_page,
                    'offset' => $offset       
                     
                );

                $properties_data += $this->session->userdata("profilters");
                
                $properties = $this->property_model->get_list($properties_data, $count_all);
                
                $this->utilities->add_to_debug("CP: $current_page");

                //load view 
                $this->load->view('admin/propertymanager/property_listing',array('properties'=>$properties,'pages_no' => $count_all / $this->records_per_page));

            break;

            case 3:
                //search for a property    
                $current_page = 1;
                
                $offset = "";
                if($current_page > 1) {
                    $offset = intval(($current_page - 1) * $this->records_per_page);                    
                }
                
                $field = $this->tools_model->get_value("col","","post",0,false);
                $order = $this->tools_model->get_value("order","","post",0,false);
                if ($field) {
                    switch ($field) {
                        case 'lot':
                            $sort_column_view = "lot";
                            $sort_column = "lot";
                            $sort_order = $order;
                            break;
                        case 'address':
                            $sort_column_view = "address";
                            $sort_column = "address";
                            $sort_order = $order;
                            break;
                        case 'state':
                            $sort_column_view = "state";
                            $sort_column = "state.name";
                            $sort_order = $order;
                            break;                            
                        case 'area':
                            $sort_column_view = "area";
                            $sort_column = "area.area_name";
                            $sort_order = $order;
                            break;
                        case 'project':
                            $sort_column_view = "project";
                            $sort_column = "proj.project_name";
                            $sort_order = $order;
                            break;
                        case 'builder':
                            $sort_column_view = "builder";
                            $sort_column = "bd.builder_id";
                            $sort_order = $order;
                            break;
                        case 'investor':
                            $sort_column_view = "investor";
                            $sort_column = "u4.first_name";
                            $sort_order = $order;
                            break;                            
                        case 's.name':
                            $sort_column_view = "s.name";
                            $sort_column = "s.name";
                            $sort_order = $order;
                            break;
                        case 'total_price':
                            $sort_column_view = "total_price";
                            $sort_column = "total_price";
                            $sort_order = $order;
                            break;
                        case 'featured':
                            $sort_column_view = "featured";
                            $sort_column = "featured";
                            $sort_order = $order;
                            break;
                        case 'status':
                            $sort_column_view = "status";
                            $sort_column = "status";
                            $sort_order = $order;
                            break;
                        case 'advisor':
                            $sort_column_view = "advisor";
                            $sort_column = "u.first_name, u.last_name";
                            $sort_order = $order;
                            break;
                    }
                    $orderby = "$sort_column $sort_order";
                    
                    $this->firephp->log("ORDERBY: $orderby");
                    
        	    } else {
        	        $orderby = "p.status, p.lot, proj.project_name";
        	        $sort_column = "lot";
        	        $sort_column_view = "lot";
                    $sort_order = "asc";
        	    }
                $keysearch = $this->tools_model->get_value("kw","","post",0,false);
                $archived = $this->tools_model->get_value("archived","","post",0,false);
                $area_id = $this->tools_model->get_value("area_id","","post",0,false);
                $state_id = $this->tools_model->get_value("state_id","","post",0,false);
                $builder_id = $this->tools_model->get_value("builder_id","","post",0,false);
                $status = $this->tools_model->get_value("status","","post",0,false);
                
                $inprogress = false;
                if($status == "inprogress") {
                    $status = "";
                    $inprogress = true;    
                }
                
                //get list of properties
                $properties_data = array(
                    'keysearch' => $keysearch,
                    'archived' => $archived,
                    'state_id' => $state_id,
                    'area_id' => $area_id,
                    'builder_id' => $builder_id,
                    'status' => $status,
                    'inprogress' => $inprogress,
                );
                
                $this->session->set_userdata("profilters",$properties_data);
                
                $properties_data['enabled'] = -1;
                $properties_data['limit'] = $this->records_per_page;
                $properties_data['page_no'] = $current_page;
                $properties_data['offset'] = $offset;
                
                $properties = $this->property_model->get_list($properties_data, $count_all, $orderby);

                $states = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
                $areas = $this->area_model->get_list(1,'','',$count_all2);
                $builders = $this->builder_model->get_list(1,'','',$count_all3);
                $status = $this->property_model->get_property_status();
                //load view 
                $this->load->view('admin/propertymanager/property_listing',array(
                                                                                    'properties'=>$properties,
                                                                                    'states'=>$states,
                                                                                    'areas'=>$areas,
                                                                                    'builders'=>$builders,
                                                                                    'status'=>$status,
                                                                                    'sort_column' => $sort_column_view,
                                                                                    'sort_order' => $sort_order,
                                                                                    'pages_no' => $count_all / $this->records_per_page)
                                                                                );

            break; 

            case 5: //list images                               

                $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false));
                $this->_refresh_files($property_id);

                break;

            break;

            case 6: //download image

                 $file = trim(urldecode($this->tools_model->get_value("file",0,"post",0,false)));
                 $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false)); 
                 
                 $path = FCPATH. PROPERTY_FILES_FOLDER.$property_id."/images/".$file;
                 $path = trim($path);
                 
                 $this->load->helper('file');
                 write_file('text.txt', "path:" . $path, 'a+');

                 if(file_exists($path)) {
                    $this->utilities->download_file($path);
                 }

            break;

            case 7: //delete image from property page

                //get files names separated with ";"

                $folder = $this->tools_model->get_value("folder","","post",0,false);

                $files_id = $this->tools_model->get_value("todelete","","post",0,false);

                $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false));

                $file_names = array();

                $doc_type = "property_gallery";

                if ($files_id!="") {

                    $arr_id_files = explode(";",$files_id);

                    $removed_properties = $this->db->where_in('id',$arr_id_files)
                                            ->get('documents');

                    //delete from documents table

                    $this->document_model->delete($arr_id_files, '');

                    //delete images from folders           

                    if($removed_properties) {
                        foreach($removed_properties->result() as $row) {
                            $file_names[] = $row->document_name;
                            $file_names[] = $row->document_name.'_thumb.jpg';
                        }
                            
                        $this->utilities->remove_file(PROPERTY_FILES_FOLDER.$folder."/images",$file_names,"");
                    }

                }
                $this->_refresh_files($property_id);

            break;

            case 8:

                $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false));

                $return_data = array();

                $return_data["reservation_dates"] = json_encode($this->property_model->get_reservation_dates($property_id));

                echo json_encode($return_data);                                              

            break;
            
            case 9:
                $advisor_id = intval($this->input->post('advisor_id'));
                $partners = $this->users_model->get_partners_by_advisor_id($advisor_id);
                $investors = $this->users_model->get_investor_by_advisor_id($advisor_id);
                $partner_options_html = '<option value="">None</option>';
                $investor_options_html = '<option value="">None</option>';
                
                if ($partners) {
                	foreach ($partners->result() AS $partner)
                	{
                	    $partner_options_html.= '<option value="'.$partner->user_id.'">'.trim($partner->first_name.' '.$partner->last_name).'</option>';
                	}
                }
                if ($investors) {
                	foreach ($investors->result() AS $investor)
                	{
                	    $investor_options_html.= '<option value="'.$investor->user_id.'">'.trim($investor->first_name.' '.$investor->last_name).'</option>';
                	}
                }
                $return_data = array(
                                        'partner_options_html' => $partner_options_html,
                                        'investor_options_html' => $investor_options_html
                                    );
                echo json_encode($return_data);
                
            break;

            case 10: //refresh document path

                $doc_id = intval($this->tools_model->get_value("doc_id",0,"post",0,false));

                $doc_details = $this->document_model->get_details($doc_id); 

                $document_path = $doc_details->document_path;          

                $return_data = array();

                $return_data["doc_id"] = $doc_id;

                $return_data["document_path"] = $document_path;

                echo json_encode($return_data);                                                              

            break;

            case 11: //delete document path

                $doc_id = intval($this->tools_model->get_value("doc_id",0,"post",0,false));

                $folder = intval($this->tools_model->get_value("folder",0,"post",0,false));

                $property_id = $folder;

                $doc_data = array(    
                        "document_path"   => ""
                ); 

                $this->document_model->save($doc_id,$doc_data,$property_id);

                $return_data = array();

                $return_data["doc_id"] = json_encode($doc_id);

                echo json_encode($return_data);                                                              

            break;

            case 12: //property type changed

                $project_id = intval($this->tools_model->get_value("project_id",0,"post",0,false));

                $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false));

                //update project id

                $data = array(
                    "project_id" => $project_id
                );

                $this->property_model->save_property_project($property_id, $data); 

                $property = $this->property_model->get_details($property_id);

                $this->data["property"] = $property;

                $this->data["property_id"] = $property_id;

                $this->load->view("admin/property/specifications",$this->data);

            break;
            
            case 13:        
                $return = array("status" => "ERROR", "message" => "An unspecified error occured");
                
                $project_id = $this->input->post("project_id");
                if(!is_numeric($project_id))
                {
                    $return["message"] = "Project ID invalid";
                    echo json_encode($return);
                    break;                    
                }
                
                $this->load->model("project_model");
                $project = $this->project_model->get_details($project_id);
                
                if(!$project)
                {
                    $return["message"] = "Couldn't load project";
                    echo json_encode($return);
                    break;                     
                }
                
                $return["title"] = $project->project_name;
                $return["area_id"] = $project->area_id;
                
                $return["status"] = "OK";
                
                echo json_encode($return);
                break;

            case 14:
                $element_id = intval($this->input->post('element_id'));
                $update_value = $this->input->post('update_value',true);

                $data = array(
                    "extra_data" => $update_value
                );

                $this->document_model->save($element_id,$data);            

                echo $update_value;

            break;
            
            case 15:
                // Change Status
                $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false));
                $status = trim($this->tools_model->get_value("status",0,"post",0,false));
                
                $advisor_id = intval($this->tools_model->get_value("advisor_id",0,"post",0,false));
                $partner_id = intval($this->tools_model->get_value("partner_id",0,"post",0,false));
                $investor_id = intval($this->tools_model->get_value("investor_id",0,"post",0,false));
                $reason = $this->input->post("reason");
                $property = $this->property_model->get_details($property_id);
                if ($property) {
					 $old_status = $property->status;	
                    $data = array(
                                    'status' => $status,
                                    'advisor_id' => $advisor_id,
                                    'partner_id' => $partner_id,
                                    'investor_id' => $investor_id
                                );
                    if ($status == 'sold') {
                    	$data['sold_date'] = date('Y-m-d H:i:s');
                    } elseif ($status == 'reserved') {
                        $data['reserved_date'] = date('Y-m-d H:i:s');
                    } elseif ($status == 'signed') {
                        $data['signed_date'] = date('Y-m-d H:i:s');
                    } elseif ($status == 'available') {
                        $data['approved_date'] = date('Y-m-d H:i:s');                        
                        $data['approved_by_id'] = $this->user_id;                     
                    } else {
                        $data['sold_date'] = null;
                        $data['reserved_date'] = null;
                        $data['signed_date'] = null;
                    }
					
                    $update_property = $this->property_model->save($property_id,$data);
                    if($update_property && $status != "available")
                    {
                        $this->property_stages_model->add_default_property_stages($property_id);            
                    }
					
					if($old_status != $status)
                    {
                        //Add history : When the property is APPROVED by a metricon manager in the CMS and set to AVAILABLE.
                        $this->load->model("item_history_model");
                        $history_date = array(
                            'created_dtm'   => date('Y-m-d H:i:s'),
                            'foreign_type'  => 'property',
                            'foreign_id'    => $property_id,
                            'change_type'   => 'status',
                            'user_id'       => $this->user_id,
                            'old_value'       => ucfirst($old_status),
                            'new_value'       => ucfirst($status),
                            'reason'        => $reason,
                        );
                        
						//By Ajay Taskseveryday
                        $property_change_status = $this->item_history_model->save($history_date);
						
						if($property_change_status)
						{
							if(($status != 'available') && ($status != 'pending'))
							{
                                
								$change_status = $this->property_model->change_status_email($property_id);
								
                                if($change_status) {
                                    $change_status = $change_status->row();
                                
									$advisor_email = $change_status->advisor_email;
									$partner_email = $change_status->partner_email;
									$investor_email = $change_status->investor_email;
									$property_address = $change_status->lot.', '.$change_status->address.', '.$change_status->suburb;
									$property_status = $change_status->status;
									$advisor_name = $change_status->advisor_full_name;
									$advisor_mobile = $change_status->advisor_mobile;
									$investor_name = $change_status->investor_full_name;

                                    $email_data = array();
                                    $email_data["property_id"] = $property_id;
                                    $email_data["property_address"] = $property_address;
                                    $email_data["property_status"] = ucfirst($property_status);
                                    $email_data["advisor_name"] = $advisor_name;
                                    $email_data["advisor_mobile"] = $advisor_mobile;
                                    $email_data["investor_name"] = ucfirst($investor_name);
                                    
                                    $admin_mails = $this->users_model->get_email_notification_admins();    
                                    
                                    $bcc = array();
                                    if(!empty($partner_email)) {
                                        $bcc[] = $partner_email;  
                                    }
                                    
                                    if(!empty($investor_email)) {
                                        $bcc[] = $investor_email;    
                                    }                                    
                                    
                                    if($admin_mails) {
                                        foreach($admin_mails as $admin_mail) {
                                            $admin_mail = $admin_mail->email;
                                            if(!empty($admin_mail)) {
                                                array_push($bcc, $admin_mail);
                                                $this->firephp->log("ADMIN");
                                            }
                                        }
                                    }

                                    $this->email_model->send_email($advisor_email, "property_status_change", $email_data, $attach = "", $bcc);                                     
                                }
							}
							
						}
                    }
					
					//update featured property
                     if($status != 'available')
                     {
                         $this->property_model->save($property_id, array('featured' => 0));	
                     }
					
                    $return_data = array(
                                            'success' => 1,
                                            'status' => ucfirst($status)
                                        );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                                            'success' => 0
                                        );
                    echo json_encode($return_data);
                }
                
            break;
            
            case 16:
                // add stage
                $status = 'FAILED';
                $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false));
                $order = intval($this->tools_model->get_value("order",0,"post",0,false));
                $stage_name = trim($this->tools_model->get_value("stage_name",0,"post",0,false));
                $data = array(
                                'stage_name' => $stage_name,
                                'order' => $order,
                                'property_id' => $property_id
                            );
                $stage_id = $this->property_stages_model->save('', $data);
                if ($stage_id) {
                    $this->document_model->add_default_documents('stage_document', $stage_id);
                	$status = 'OK';
                }
                
                echo $status;
                exit();
            break;
            
            case 17:
                // Delete stage
                $status = 'FAILED';
                $cst_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($cst_ids != "") {
                    $arr_ids = explode(";",$cst_ids);
                    if (sizeof($arr_ids)) {
                        foreach($arr_ids as $id)
                        {
                            if (is_numeric($id)) {
                                if ($where_in != "") $where_in.=",";
                                $where_in .= $id;
                            }
                        }
                        if ($where_in != "") $this->property_stages_model->delete($where_in);
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;
            
            case 18:
                // Complete stage
                $status = 'FAILED';
                $cst_ids = $this->tools_model->get_value("tocomplete","","post",0,false);
                
                if ($cst_ids != "") {
                    $arr_ids = explode(";",$cst_ids);
                    if (sizeof($arr_ids)) {
                        foreach ($arr_ids AS $id)
                        {
                            if (is_numeric($id)) {
                                $this->property_stages_model->save($id, array('completed' => 1, 'datetime_completed' => date('Y-m-d H:i:s')));	
                            }
                        }
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;
            
            case 19:
                // Load stage
                $property_id = intval($this->tools_model->get_value("property_id",0,"post",0,false));
                $this->data['stages'] = $this->property_stages_model->get_list(-1,$property_id,'','',$count_all);
                $this->load->view("admin/property/construction_tracker",$this->data);
            break;
            
            case 20:
                // Change Order
             	$message = "OK";
             	$stage_id = $this->tools_model->get_value("stage_id", 0, "post", 0, false);
             	$property_id = $this->tools_model->get_value("property_id", 0, "post", 0, false);
             	$direction = $this->tools_model->get_value("direction", 0, "post", 0, false);
             	
             	if((($stage_id == "") || (!is_numeric($stage_id))) || (($direction != "up") && ($direction != "down"))) {
                    $message = "ERROR";
             	} else {
					$stage_detail = $this->property_stages_model->get_details($stage_id, FALSE);
					
					if(!$stage_detail) {
						$message = "ERROR";	
					}
					else {
						$stages = $this->property_stages_model->get_list(-1,$property_id,'','',$count_all);
						
						// Create an array to hold the re-ordered items
 						$items_array = array();
						
						$seqno = 10;	// Starting sequence number
						
						foreach($stages->result() as $row)
						{
							// If this article is the article the user is trying to reorder,
							// modify the sequence number +- 15 according to direction.
							if($row->id == $stage_id) {
								if($direction == "up")
									$items_array[$seqno - 15] = $row->id;
								else
									$items_array[$seqno + 15] = $row->id;  	
							} else {
								// This is not the article the user is trying to reorder.
								$items_array[$seqno] = $row->id; 	
							}
							// Increment the sequence number
							$seqno = $seqno + 10;
						}
						
						// Sort the array by the keys (the new sequence numbers)
						ksort($items_array);
						
						// Now loop through the articles, updating their sequence numbers
						$seqno = 1;
						
						foreach($items_array as $s=>$item_id)
						{
							$this->property_stages_model->save($item_id, array("order" => $seqno));
							$seqno++;
						}
					}
                }
             	
                $return_data = array();
                $return_data["message"] = $message;
                echo json_encode($return_data);         	
         	
         	break;
         	
            case 21:
                //get files names separated with ";"

                $files_id = $this->tools_model->get_value("todelete","","post",0,false);

                $stage_id = intval($this->tools_model->get_value("stage_id",0,"post",0,false));
                $document_type = $this->tools_model->get_value("document_type",0,"post",0,false);

                $file_names = array();

                $doc_type = "stage_gallery";

                if ($files_id!="") {

                    $arr_id_files = explode(";",$files_id);

                    $removed_stages = $this->db->where_in('id',$arr_id_files)
                                            ->get('documents');

                    //delete from documents table

                    $this->document_model->delete($arr_id_files, '');

                    //delete images from folders           

                    if($removed_stages) {
                        foreach($removed_stages->result() as $row) {
                            $file_names[] = $row->document_name;
                            if ($document_type == 'images') {
                                $file_names[] = $row->document_name.'_thumb.jpg';	
                            }
                        }
                        if ($document_type == 'images') {
                            $this->utilities->remove_file(STAGE_FILES_FOLDER.$stage_id."/images",$file_names,"");
                        } else {
                            $this->utilities->remove_file(STAGE_FILES_FOLDER.$stage_id."/documents",$file_names,"");
                        }
                    }

                }
                echo 'OK';
            break;
         	
         	case 22: //download stage image

                 $file = trim(urldecode($this->tools_model->get_value("file",0,"post",0,false)));
                 $stage_id = intval($this->tools_model->get_value("stage_id",0,"post",0,false)); 
                 $document_type = $this->tools_model->get_value("document_type",0,"post",0,false); 
                 
                 $path = FCPATH. STAGE_FILES_FOLDER.$stage_id."/$document_type/".$file;
                 $path = trim($path);
                 
                 $this->load->helper('file');
                 write_file('text.txt', "path:" . $path, 'a+');

                 if(file_exists($path)) {
                    $this->utilities->download_file($path);
                 }

            break;
            
            case 23: //delete stage document path

                $doc_id = intval($this->tools_model->get_value("doc_id",0,"post",0,false));

                $folder = intval($this->tools_model->get_value("folder",0,"post",0,false));

                $property_id = $folder;

                $doc_data = array(    
                        "document_path"   => ""
                ); 

                $this->document_model->save($doc_id,$doc_data,$property_id);

                $return_data = array();

                $return_data["doc_id"] = json_encode($doc_id);

                echo json_encode($return_data);                                                              

            break;
            
            case 24: // refresh stage file
                $stage_id = intval($this->tools_model->get_value("stage_id",0,"post",0,false));
                $document_type = $this->tools_model->get_value("document_type",0,"post",0,false);
                if ($document_type == 'images') {
                    $this->_refresh_stage_files($stage_id);	
                } else {
                    $this->_refresh_stage_doc_files($stage_id);
                }
            break;
            
            case 25: // edit caption stage file
                $element_id = intval($this->input->post('element_id'));
                $update_value = $this->input->post('update_value',true);

                $data = array(
                    "extra_data" => $update_value
                );

                $this->document_model->save($element_id,$data);            

                echo $update_value;

            break;
            
            case 26: // Archive property checked
            	$property_ids = $this->tools_model->get_value("toarchive","","post",0,false);
				$action = $this->input->post('action');
				
                if ($property_ids!="") {

                    $arr_ids = explode(";",$property_ids);
                    
                	switch ($action) {
                		case 'archive':
                			foreach($arr_ids as $id)
		                    {
		                    	if (!empty($id))
		                    		$this->property_model->save($id, array('archived' => 1));	
		                    }
            			break;
                			
            			case 'unarchive':
                			foreach($arr_ids as $id)
		                    {
		                        if (!empty($id))
		                    		$this->property_model->save($id, array('archived' => 0));
		                    }
            			break;
                	
                		default:
            			break;
                	}
                }

                //get list of properties
                $search_terms = $this->tools_model->get_value("kw","","post",0,false);
                $archived = $this->tools_model->get_value("archived","","post",0,false);
                $state_id = $this->tools_model->get_value("state_id","","post",0,false);
                $area_id = $this->tools_model->get_value("area_id","","post",0,false);
                $builder_id = $this->tools_model->get_value("builder_id","","post",0,false);
                $status = $this->tools_model->get_value("status","","post",0,false);
                
                $properties_data = array(
                    'enabled' => -1,
                    'limit' => $this->records_per_page,
                    'page_no' => $current_page,'search_terms' => $search_terms,
                    'archived' => $archived,
                    'state_id' => $state_id,
                    'area_id' => $area_id,
                    'builder_id' => $builder_id,
                    'status' => $status
                );

                $properties = $this->property_model->get_list($properties_data, $count_all);
                
                //load view 
                $this->load->view('admin/propertymanager/property_listing',array('properties'=>$properties,'pages_no' => $count_all / $this->records_per_page));
            break;
            
            case 27: // Feature property checked
            	$property_ids = $this->tools_model->get_value("tofeature","","post",0,false);
				$action = $this->input->post('action');
				
                if ($property_ids!="") {

                    $arr_ids = explode(";",$property_ids);
                    
                	switch ($action) {
                		case 'feature':
                			foreach($arr_ids as $id)
		                    {
		                    	if (!empty($id))
		                    		$this->property_model->save($id, array('featured' => 1));	
		                    }
            			break;
                			
            			case 'unfeature':
                			foreach($arr_ids as $id)
		                    {
		                        if (!empty($id))
		                    		$this->property_model->save($id, array('featured' => 0));
		                    }
            			break;
                	
                		default:
            			break;
                	}
                }

                //get list of properties
                $search_terms = $this->tools_model->get_value("kw","","post",0,false);
                $archived = $this->tools_model->get_value("archived","","post",0,false);
                $state_id = $this->tools_model->get_value("state_id","","post",0,false);
                $area_id = $this->tools_model->get_value("area_id","","post",0,false);
                $builder_id = $this->tools_model->get_value("builder_id","","post",0,false);
                $status = $this->tools_model->get_value("status","","post",0,false);
                
                $properties_data = array(
                    'enabled' => -1,
                    'limit' => $this->records_per_page,
                    'page_no' => $current_page,'search_terms' => $search_terms,
                    'archived' => $archived,
                    'state_id' => $state_id,
                    'area_id' => $area_id,
                    'builder_id' => $builder_id,
                    'status' => $status
                );

                $properties = $this->property_model->get_list($properties_data, $count_all);
                
                //load view 
                $this->load->view('admin/propertymanager/property_listing',array('properties'=>$properties,'pages_no' => $count_all / $this->records_per_page));
            break;   
            
            case 28: // Add note
                
                $error_message = '';
                $data = array();
                $property_id = isset($_POST['property_id']) ? $_POST['property_id'] : 0;
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                $cms_user = $this->session->userdata("cms_user");
				
                $note_date = isset($_POST['note_date']) ? $_POST['note_date'] :date('Y-m-d H:i:s');
                $note_date = $this->utilities->uk_to_isodate($note_date);
                $date = date('Y-m-d');
				$date_entered = date("d-m-Y", strtotime($date));
				$user_name = $cms_user['firstname'].' '.$cms_user['lastname'];
				$user_phone = $cms_user['phone'];
				
				
				$views = isset($_POST['view']) ? $_POST['view'] : '';
				$private_note = isset($_POST['private_note']) ? $_POST['private_note'] : '';

                $data = array(
                                'type' => 'property_comment',
                                'comment' => $comment,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $property_id,
                                'datetime_added' => $note_date,
								'permission' => $views
                            );
                
                if (!empty($comment_id)) {
                    $this->commentmd->save($comment_id,$data);
                } else {
                    $comments = $this->commentmd->save('',$data);
					if($comments)
					{
						if($private_note != 1)
						{
							//send mail
							$property = $this->property_model->get_details($property_id);
							
							if($property)
							{
								$advisor_email = '';
								$partner_email = '';
								$investor_email = '';
								$bcc = array();
								$single_arrays = explode(",",$views);
								foreach($single_arrays as $single_array)
								{
									$user_type = $single_array;
									switch($user_type)
									{
										case 3:
											$advisor_email = $property->advisor_email;
										break;
										
										case 5:
											$partner_email = $property->partner_email;
										break;
										
										case 6:
											$investor_email = $property->investor_email;
										break;
									}	
									
								}

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
								
                                $bcc = array();
								$admin_mails = $this->users_model->get_email_notification_admins();	
                                
								if(($partner_email != "") || ($investor_email != ""))
								{
									$bcc = array(
										'partner_email' => $partner_email,
										'investor_email' => $investor_email
									);
								}  
								
                                if($admin_mails) {
								    foreach($admin_mails as $admin_mail)
								    {
									    $admin_mail = $admin_mail->email;
									    if($bcc != "")
									    {
										    array_push($bcc, $admin_mail);
									    }
									    else
									    {
										    $bcc = $admin_mail;
									    }
								    }

                                }
                                
								$this->email_model->send_email($advisor_email, "new_file_note", $email_data, $attach = "", $bcc);
							}
						}
						
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
                $property_id = isset($_POST['property_id']) ? $_POST['property_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'property_comment','foreign_id'=>$property_id) );
                
                $html ='<tr>';
                $html.='    <th width="10%">ID</th>';
                $html.='    <th align="left">Note</th>';
                $html.='    <th width="20%">Date</th>';
                $html.='    <th width="10%">Delete</th>';
                $html.='</tr>';
                
                if ($comments) {
                    foreach ($comments->result() AS $index=>$comment)
                    {
                        $html.='<tr id="acomment_'.$comment->id.'" class="'.$index%2 ? 'admintablerowalt' : 'admintablerow'.'">';
                        $html.='    <td class="admintabletextcell" align="center"><a href="javascript:;" rel="'.$comment->id.'" class="editComment" >'.$comment->id.'</a></td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;">';
                        $html.='        <span style="font-weight:bold">'.trim("$comment->first_name $comment->last_name").':</span>';
                        $html.='        <br />';
                        $html.='        "'.nl2br($comment->comment).'"';
                        $html.='    </td>';
                        $html.='    <td class="admintabletextcell" align="center">'.date('d/m/Y', $comment->ts_added).'</td>';
                        $html.='    <td class="center"><input type="checkbox" class="commenttodelete" value="'.$comment->id.'" /></td>';
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
                $property_id = isset($_POST['property_id']) ? $_POST['property_id'] : 0;
				
                $keydate_id = isset($_POST['keydate_id']) ? $_POST['keydate_id'] : 0;
                $description = isset($_POST['description']) ? $_POST['description'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $estimate_date = isset($_POST['estimate_date']) ? $_POST['estimate_date'] :date('Y-m-d H:i:s');
                $estimate_date = date("Y/m/d", strtotime($estimate_date));
                $date = date('Y/m/d');
				$followup_date = isset($_POST['followup_date']) ? $_POST['followup_date'] :date('Y-m-d H:i:s');
				
                $followup_date = date("Y-m-d", strtotime($followup_date));
				$actual_date = isset($_POST['actual_date']) ? $_POST['actual_date'] :date('Y-m-d H:i:s');
								
				$actual_date = date("Y-m-d", strtotime($actual_date));
                $data = array(
                                'type' => 'property_key_date',
                                'description' => $description,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $property_id,
								'estimated_date' => $estimate_date,
                                'datetime_added' => $date,
								'actual_date' => $actual_date,
								'followup_date' => $followup_date
								
                            );
				
                if (!empty($keydate_id)) {
                    $this->keydatemd->save($keydate_id,$data);

				} else {
                    $this->keydatemd->save('',$data);

                }

                if (isset($_POST['getlist'])) {
                    $this->load->model('keydate_model','keydatemd');
                    $this->data['keydates'] = $this->keydatemd->get_list(array('type'=>'property_key_date','foreign_id'=>$property_id) );
										
                	$this->load->view('admin/property/construnction_tracker', $this->data);
                } else {
                    echo 'OK';
                    exit();
                }
                
            break;	
            
            case 33:
                // Load keydates
                $property_id = isset($_POST['property_id']) ? $_POST['property_id'] : 0;
                	
				$keydates = $this->keydatemd->get_list(array('type'=>'property_key_date','foreign_id'=>$property_id) );
				
                $html ='<tr>';
                $html.='    <th width="10%">ID</th>';
                $html.='    <th align="30%">Description</th>';
				$html.='    <th align="20%">Estimated Date</th>';
				$html.='    <th width="20%">Actual Date</th>';
				$html.='    <th align="20%">Follow Up Date</th>';
                $html.='    <th width="10%">Delete</th>';
                $html.='</tr>';
                
                if ($keydates) {
                    foreach ($keydates->result() AS $index=>$keydate)
                    {
                        $html.='<tr id="acomment_'.$keydate->id.'" class="'.$index%2 ? 'admintablerowalt' : 'admintablerow'.'">';
                        $html.='    <td class="admintabletextcell" align="center"><a href="javascript:;" rel="'.$keydate->id.'" class="editkeydate" >'.$keydate->id.'</a></td>';
						$html.='    <td class="admintabletextcell" align="center">'.$keydate->description.'</td>';
						$html.='    <td class="admintabletextcell" align="center">'.date("d-m-Y", strtotime($keydate->estimated_date)).'</td>';
						$html.='    <td class="admintabletextcell" align="center">'.date("d-m-Y", strtotime($keydate->actual_date)).'</td>';
						$html.='    <td class="admintabletextcell" align="center">'.date("d-m-Y", strtotime($keydate->followup_date)).'</td>';
                        $html.='    <td class="center" align="center"><input type="checkbox" class="keydatetodelete" value="'.$keydate->id.'" /></td>';
                        $html.='</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;  

			case 34: // Edit Keydate
                
                $keydate_id = isset($_POST['keydate_id']) ? $_POST['keydate_id'] : 0;
                $keydates = $this->keydatemd->get_details($keydate_id);
				
				$estimated_date1 = $keydates->estimated_date;
				$estimated_date = date("m/d/Y", strtotime($estimated_date1));
				$followup_date1 = $keydates->followup_date;
				$followup_date = date("m/d/Y", strtotime($followup_date1));
				$actual_date1 = $keydates->actual_date;
				$actual_date = date("m/d/Y", strtotime($actual_date1));
				
				$return_data = array(
                	                       'status' => 'OK',
                	                       'description' => $keydates->description,
                	                       'estimate_date' => $estimated_date,
										   'followup_date' => $followup_date,
										   'actual_date' => $actual_date,
                	                       'keydate_id' => $keydates->id,
                                           									   
                	                   );
                    echo json_encode($return_data);
                             
            break;
            
			case 35: // Delete keydate
            
                $status = 'FAILED';
                $ids = $this->tools_model->get_value("todelete","","post",0,false);
                
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

    function upload_file($upload_type, $property_id, $doc_id='', $doc_name='', $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($property_id))) {
			die ('{error: "Invalid upload type $upload_type or article id $property_id"}');
		}
		
		// Handle a hero image upload
		if(($upload_type == "gallery_image") || ($upload_type == "documents")) {
            // Load the article in question
            $property = $this->property_model->get_details($property_id);
            if(!$property) {
				die ('{error: "Invalid property"}');	
            }
            
			// Determine the path for where to store the original image and the image set
            $path = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id . "/images/";
            if ( !is_dir($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id . "/documents/";
            	if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            }
            
         	$result = $this->qqfileuploader->handleUpload($path, $filename, true);

         	if($filename == "") {
				$filename = $this->qqfileuploader->file->getName();
				
         		if($filename == "") {
         			die ('{error: "Could not determine file name"}');
				} 				
         	}
         	
         	$file_path =  $path . $filename;
         	if(!file_exists($file_path)) {
				die ('{error: "File did not upload correctly"}');	
         	}
         	
			// Move the temporary file to the final path.
  			chmod($file_path, 0666);
  			
  			$return_path = '';
  			
  			if($upload_type == "documents") {
  			    
				$doc_name = str_replace('+',' ',$doc_name);
				
                // Save the document into the documents table in the database.
				$doc_data =  array(
					"document_type" => "property_document",
					"foreign_id" => $property_id,
					"document_name" => $doc_name,
					"document_path" => PROPERTY_FILES_FOLDER . $property_id . "/documents/" . $filename
				);
				
                $return_path = PROPERTY_FILES_FOLDER . $property_id . "/documents/" . $filename;
				$this->document_model->save($doc_id, $doc_data, $property_id, "property_document", $use_order = TRUE);				
				
			} elseif ($upload_type == "gallery_image") {

  			    $property_folder = FCPATH.PROPERTY_FILES_FOLDER.$property_id."/images/";
            	$thumb_path = $property_folder . $filename . "_thumb.jpg";
            	$standard_path = $property_folder . $filename . "_standard.jpg";
            	$zoom_path = $property_folder . $filename . "_zoom.jpg";
            	
            	//resize
			    $this->image->create_thumbnail($property_folder.$filename, $thumb_path, $error_message,THUMB_PROPERTY_WIDTH,THUMB_PROPERTY_HEIGHT);
			    $this->image->create_thumbnail($property_folder.$filename, $standard_path, $error_message,STANDARD_PROPERTY_WIDTH,STANDARD_PROPERTY_HEIGHT);
			    $this->image->create_thumbnail($property_folder.$filename, $zoom_path, $error_message,ZOOM_PROPERTY_WIDTH,ZOOM_PROPERTY_HEIGHT);
  			    
                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "property_gallery",
					"foreign_id" => $property_id,
					"document_name" => $filename,
					"document_path" => PROPERTY_FILES_FOLDER . $property_id . "/images/" . $filename
				);
                
				$return_path = PROPERTY_FILES_FOLDER . $property_id . "/images/" . $filename;
				
				$this->document_model->save("", $img_data, $property_id, "property_gallery", $use_order = TRUE);				
			}

			$return = array();
			$return["status"] = "OK";
			$return["fileName"] = $return_path;
			$return["success"] = true;	
			
			echo json_encode($return);	

		} else {
			die ('{error: "Invalid file type"}');
		}	
    }
    
    function stage_upload_file($upload_type, $stage_id, $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($stage_id))) {
			die ('{error: "Invalid upload type $upload_type or article id $property_id"}');
		}
		
		// Handle a hero image upload
		if(($upload_type == "gallery_image") || ($upload_type == "documents")) 
		{
            // Load the article in question
            
            $stage = $this->property_stages_model->get_details($stage_id);
            $property = $this->property_model->get_details($stage->property_id);
            
            if(!$stage && !$property) {
				die ('{error: "Invalid stage"}');	
            }
            
			// Determine the path for where to store the original image and the image set
            $path = ABSOLUTE_PATH . STAGE_FILES_FOLDER . $stage_id . "/images/";
            if ( !is_dir($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . STAGE_FILES_FOLDER . $stage_id . "/documents/";
            	if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            }
            
         	$result = $this->qqfileuploader->handleUpload($path, $filename, true);

         	if($filename == "") {
				$filename = $this->qqfileuploader->file->getName();
				
         		if($filename == "") {
         			die ('{error: "Could not determine file name"}');
				} 				
         	}
         	
         	$file_path =  $path . $filename;
         	if(!file_exists($file_path)) {
				die ('{error: "File did not upload correctly"}');	
         	}
         	
			// Move the temporary file to the final path.
  			chmod($file_path, 0666);
  			
  			$return_path = '';
  			
  			if($upload_type == "documents") {
  			    
                // Save the document files into the documents table in the database.
				$doc_data =  array(
					"document_type" => "stage_document",
					"foreign_id" => $stage_id,
					"document_name" => $filename,
					"document_path" => STAGE_FILES_FOLDER . $stage_id . "/documents/" . $filename
				);
                
				$return_path = STAGE_FILES_FOLDER . $stage_id . "/documents/" . $filename;
				
				$this->document_model->save("", $doc_data, $stage_id, "stage_document", $use_order = TRUE);			
				
			} elseif ($upload_type == "gallery_image") {

  			    $stage_folder = FCPATH.STAGE_FILES_FOLDER.$stage_id."/images/";
            	$thumb_path = $stage_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($stage_folder.$filename, $thumb_path, $error_message,THUMB_STAGE_WIDTH,THUMB_STAGE_HEIGHT);
  			    
                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "stage_gallery",
					"foreign_id" => $stage_id,
					"document_name" => $filename,
					"document_path" => STAGE_FILES_FOLDER . $stage_id . "/images/" . $filename
				);
                
				$return_path = STAGE_FILES_FOLDER . $stage_id . "/images/" . $filename;
				
				$this->document_model->save("", $img_data, $stage_id, "stage_gallery", $use_order = TRUE);				
			}

			$return = array();
			$return["status"] = "OK";
			$return["fileName"] = $return_path;
			$return["success"] = true;	
			
			echo json_encode($return);	
		} else {
			die ('{error: "Invalid file type"}');
		}	
    }
    
    function stage($stage_id='')
    {
        if ($stage_id != '') {
        	$stage = $this->property_stages_model->get_details($stage_id);
        	if ($stage) {
            	$property_id = $stage->property_id;
                $this->data["status_arr"] = $this->tools_model->get_stage_status();
                $this->data["public_arr"] = $this->tools_model->get_stage_status("public");
                    
            	$this->data['stage'] = $stage;
            	$this->data['property_id'] = $property_id;
            	$this->data['stage_id'] = $stage_id;
            	$this->data['documents'] = $this->document_model->get_list('stage_document', intval($stage_id));
                $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$property_id, 'type' => "property_comment"));
                
            	if(!is_dir(ABSOLUTE_PATH.STAGE_FILES_FOLDER))
                    @mkdir(ABSOLUTE_PATH.STAGE_FILES_FOLDER ,DIR_WRITE_MODE);   
                 
                if(!is_dir(ABSOLUTE_PATH.STAGE_FILES_FOLDER.$stage_id))
                    @mkdir(ABSOLUTE_PATH.STAGE_FILES_FOLDER.$stage_id, DIR_WRITE_MODE);
                    
                if(!is_dir(ABSOLUTE_PATH.STAGE_FILES_FOLDER.$stage_id."/documents"))
                    @mkdir(ABSOLUTE_PATH.STAGE_FILES_FOLDER.$stage_id."/documents",DIR_WRITE_MODE);       
                
                if(!is_dir(ABSOLUTE_PATH.STAGE_FILES_FOLDER.$stage_id."/images"))
                    @mkdir(ABSOLUTE_PATH.STAGE_FILES_FOLDER.$stage_id."/images",DIR_WRITE_MODE);
                    
                $this->data["images"] = $this->document_model->get_list("stage_gallery", intval($stage_id)); 
                $this->data["files_no"] = count($this->data["images"]);
                $this->data["images_records_per_page"] = $this->images_records_per_page;
                $this->data["documents_records_per_page"] = $this->documents_records_per_page;
                
            	if (isset($_POST['postback'])) {
            	   
            		$datetime_completed = $this->input->post('datetime_completed',true);
            		
            		if (!empty($datetime_completed)) {
                        $datetime_completed = $this->utilities->uk_to_isodate($datetime_completed);
            		} else {
            		    $datetime_completed = null;
            		}

                    $next_followup_date = $this->input->post('next_followup_date',true);
                    
                    if (!empty($next_followup_date)) {
                        $arr = explode('/', $next_followup_date);
                        $next_followup_date = $arr[2].'-'.$arr[1].'-'.$arr[0];
                    } else {
                        $next_followup_date = null;
                    }                    

            		$stage_status = $this->input->post('stage_status',true);
                    $stage_public = $this->input->post('stage_public',true);
                    $stage_note = $this->input->post('stage_note',true);
                    $next_followup_comments = $this->input->post('next_followup_comments',true);
            		
            		$data = array(
                                    "status" => $stage_status,
                                    "public" => $stage_public,
                                    "comments" => $stage_note,
                                    "datetime_completed" => $datetime_completed
                    );
                    
                    if($stage->status != "completed")
                    {
                        $data["next_followup_date"] = $next_followup_date;     
                        $data["next_followup_comments"] = $next_followup_comments;
                    }
            	
                    $this->property_stages_model->save($stage_id, $data);
                    redirect("/admin/propertymanager/stage/".$stage_id);
            	}
            } else {
                $this->error_model->report_error("Sorry, the stage could not be loaded.", "Stage/show - the stage with an id of '$stage_id' could not be loaded");
                return;
            }
        } else {
            $this->error_model->report_error("Sorry, the stage could not be loaded.", "Stage/show - the stage with an id of '$stage_id' could not be loaded");
            return;
        }
        
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Website Administration Stages";
        $this->data["page_heading"] = "Stage Details";
        
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/stage/prebody.php', $this->data); 
        $this->load->view('admin/stage/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);      
    }
    
    function change_status($property_id='')
    {
        $property = $this->property_model->get_details($property_id);
        $advisors = $this->users_model->get_list(1,'','',$count_all,'',USER_TYPE_ADVISOR);
        $partners = $this->users_model->get_partners_by_advisor_id($property->advisor_id);
        $investors = $this->users_model->get_investor_by_advisor_id($property->advisor_id);
        
        $this->data['property'] = $property;
        $this->data['status'] = $this->property_model->get_property_status();
        $this->data['advisors'] = $advisors;
        $this->data['partners'] = $partners;
        $this->data['investors'] = $investors;
        
        $this->load->view('admin/propertymanager/frm_change_status.php', $this->data);
    }
    
    function _refresh_files($property_id)
    {
        //get files

        $files = $this->document_model->get_list("property_gallery", $property_id); 
        $count_all = count($files);

        //get hero image
        $hero_image = $this->property_model->get_hero_image($property_id);                

        //load view 
        $this->load->view('admin/property/file_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page, 'hero_image'=>$hero_image));        
    }
    
    function _refresh_stage_files($stage_id)
    {
        //get files
        $files = $this->document_model->get_list("stage_gallery", $stage_id); 
        $count_all = count($files);

        //load view 
        $this->load->view('admin/stage/stage_file_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));
    }
    
    function _refresh_stage_doc_files($stage_id)
    {
        //get files
        $files = $this->document_model->get_list("stage_document", $stage_id); 
        $count_all = count($files);

        //load view 
        $this->load->view('admin/stage/document_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));
    }
	
	//By Mayur-Taskseveryday
	
	function update_existingproperty(){
		$numrows=$this->property_model->update_existing_property();
		echo "Updated:".$numrows;
	}
	
	function generate()
    {
        // Load neccessary libs and models
        // $this->load->library('form_validation');
        
        // // Validate the form submission
        // $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        // $this->form_validation->set_rules('list_type', 'List Type', 'required');
        // $this->form_validation->set_rules('min_bedrooms', 'Min Bedrooms', 'required|number');
        // $this->form_validation->set_rules('max_bedrooms', 'Max Bedrooms', 'required|number');
        // $this->form_validation->set_rules('min_bathrooms', 'Min Bathrooms', 'required|number');
        // $this->form_validation->set_rules('max_bathrooms', 'Max Bathrooms', 'required|number');  
        // $this->form_validation->set_rules('min_garage', 'Min Garage', 'required|number');
        // $this->form_validation->set_rules('max_garage', 'Max Garage', 'required|number');               
        // $this->form_validation->set_rules('min_total_price', 'Min Total Price', 'required|number');
        // $this->form_validation->set_rules('max_total_price', 'Max Total Price', 'required|number');         
        // $this->form_validation->set_rules('min_land', 'Min Land', 'required|number');
        // $this->form_validation->set_rules('max_land', 'Max Land', 'required|number'); 
        // $this->form_validation->set_rules('min_yield', 'Min Yield', 'required|number');
        // $this->form_validation->set_rules('max_yield', 'Max Yield', 'required|number');                              
        // $this->form_validation->set_rules('nras', 'NRAS', 'number');
        // $this->form_validation->set_rules('smsf', 'SMSF', 'number');
        // $this->form_validation->set_rules('featured', 'Featured', 'number');
        // $this->form_validation->set_rules('new', 'New', 'number');
        // $this->form_validation->set_rules('project_id', 'Project ID', 'number');
        // $this->form_validation->set_rules('area_id', 'Area ID', 'number');
        // $this->form_validation->set_rules('state_id', 'State ID', 'number');
        // $this->form_validation->set_rules('property_type_id', 'Property Type ID', 'number');
        // $this->form_validation->set_rules('contract_type_id', 'Contract Type ID', 'number');
        
        // if ($this->form_validation->run() == FALSE)
        // {
            // $this->data["message"] = validation_errors('- ', '\n');
            // send($this->data);
        // }
        
        // $status = $this->input->post("status");
        // if(strtolower($status) == "pending")
        // {
            // $this->data["message"] = "You may not load properties in a pending status.";
            // send($this->data);            
        // }
        
        // Sort By Columns
        // $valid_columns = array(
                                // "p.address",
                                // "p.total_price",
                                // "r1.name",
                                // "p.house_area",
                                // "p.land",
                                // "p.featured DESC, p.rent_yield",
                                // "p.rent_yield",
                                // "p.nras",
                                // "p.smsf",
                                // "area.area_name",
                                // "st.name",
                                // "proj.project_name"
                            // );
                            
        // $valid_dirs = array("ASC", "DESC");
        
        // if((!in_array($this->input->post("sort_col"), $valid_columns))
            // || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        // {
            // $this->data["message"] = "Invalid sort parameters";
            // send($this->data);            
        // }
        
        // $search_fields = array("min_bedrooms", "max_bedrooms", "min_bathrooms", "max_bathrooms", "min_garage", 
            // "max_garage", "min_total_price", "max_total_price", "min_land", "max_land", "min_house", "max_house", 
            // "min_yield", "max_yield", "nras", "smsf", "project_id", "area_id", "state_id", "property_type_id", 
            // "contract_type_id", "status", "featured", "new");
        
        $filters = array();
        //$filters["user_id"] = $this->user_id;
        $filters["enabled"] = 1;
        $filters["archived"] = 0;   
        $filters["keysearch"] = $this->input->post("keysearch");
        $filters["limit"] = 999999;
        $filters["offset"] = 0;
        
        // // On the map view, load all properties.
        
        // foreach($search_fields as $field)
        // {
            // $filters[$field] = $this->input->post($field);    
        // }

        //$order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        // $user_logged = $this->users_model->get_details($this->user_id);
        // if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id != USER_TYPE_ADVISOR))
        // {
            // $filters['permissions_user_id'] = $this->user_id;
        // }
        
        // $filters['user_type_id'] = $user_logged->user_type_id;
        // $filters['user_builder_id'] = $user_logged->builder_id;
		
        $properties = $this->property_model->get_list($filters, $count_all);

        if ($properties) {
            $columns = $this->input->post('columns');
            if (!is_array($columns) OR sizeof($columns)==0) {
                show_error('Please select at least one column for the generated document.');
            }
            // headings
            $aHeadings = array();
            $defaultHeadings = array(
                'lot' => 'Lot',
                'address' => 'Address',
                'area' => 'Area',
                'state' => 'State',
                'estate' => 'Estate',
                'price' => 'Price',
                'type' => 'Type',
                'size' => 'Size',
                'land' => 'Land',
                'yield' => 'Yield',
                'nras' => 'NRAS',
                'smsf' => 'SMSF',
				'date_added' => 'Date Added',
				'status' => 'Status',
				'builder' => 'Builder',
				'advisor_full_name' => 'Advisor',
				'partner_full_name' => 'Partner',
				'purchaser_full_name' => 'Enquiry / Purchaser',
				'ts_reserved_date' => 'Date reserved'
            );
			
			
            foreach ($columns as $column)
            {
                $aHeadings[] = $defaultHeadings[$column];
            }
            
            // start generating
            if ($this->input->post('type')=='csv') {
                $delim = ",";
                $newline = "\n";
                $enclosure = '"';
                
                $out = '';
                
                foreach ($aHeadings as $heading)
                {
					$out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $heading).$enclosure.$delim;
                }
                $out = rtrim($out);
                $out .= $newline;
                
                foreach ($properties->result() as $record)
                {
                    foreach ($columns as $column)
                    {
                        switch ($column)
                        {
                            case 'area':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->area_name).$enclosure.$delim;
                                break;
                            case 'state':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->state_code).$enclosure.$delim;
                                break;
                            case 'estate':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->project_name).$enclosure.$delim;
                                break;
                            case 'price':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, '$'.number_format($record->total_price, 0, ".", ",")).$enclosure.$delim;
                                break;
                            case 'type':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->property_type).$enclosure.$delim;
                                break;
                            case 'size':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->house_area).$enclosure.$delim;
                                break;
                            case 'land':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->land).$enclosure.$delim;
                                break;
                            case 'yield':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, number_format($record->rent_yield, 2)).$enclosure.$delim;
                                break;
                            case 'nras':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, ($record->nras) ? "Yes" : "No").$enclosure.$delim;
                                break;
                            case 'smsf':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, ($record->smsf) ? "Yes" : "No").$enclosure.$delim;
                                break;
							case 'date_added':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->reserved_date).$enclosure.$delim;
                                break;
                            case 'status':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->status).$enclosure.$delim;
                                break;
                            case 'builder':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->builder_name).$enclosure.$delim;
                                break;		
							case 'ts_reserved_date':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, isset($record->$column) ? date("m/d/y g:i A", $record->ts_reserved_date) : '').$enclosure.$delim;
                                break;	
                            default:
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, isset($record->$column) ? $record->$column : '').$enclosure.$delim;
                                break;
                        }
                    }
                    $out = rtrim($out);
                    $out .= $newline;
                }
                
                header("Content-type: text/csv");
                header("Content-Disposition: inline; filename=stocklist-".date('YmdHis').".csv");
                header("Pragma: public");
                header("Expires: 0");
                ini_set('zlib.output_compression','0');
                echo $out;
            } else {
                $columnsWidth = array(
                    'lot' => 20,
                    'address' => 40,
                    'area' => 40,
                    'state' => 15,
                    'estate' => 25,
                    'price' => 20,
                    'type' => 25,
                    'size' => 25,
                    'land' => 15,
                    'yield' => 15,
                    'nras' => 15,
                    'smsf' => 15,
                );
                $totalWidthDefault = 0;
                foreach ($columnsWidth as $width) $totalWidthDefault += $width;
                
                }
        } else {
            show_error('No properties found.');
        }
    }
	
	
}