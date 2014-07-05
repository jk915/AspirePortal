<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Projects extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Projects()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("project_model");
        $this->load->model("area_model");
        $this->load->model("property_model");
        $this->load->model("resources_model");
        $this->load->model("document_model");
        $this->load->model("link_model");
        $this->load->model("project_meta_model");
        
        $this->load->helper("image");
    }
    
    function index()
    {
        $this->data["meta_title"] = "Project List";
        
        $this->data["project_data"] = $this->project_model->get_project_min_max();
        $this->data["areas"] = $this->area_model->get_list(1, "", "", $count_all, "", "area_name ASC");
        $this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
                
        $this->load->view('member/header', $this->data);
        $this->load->view('member/projects/list/prebody.php', $this->data); 
        $this->load->view('member/projects/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function detail($project_id = "")
    {
    	$this->load->model('property_permissions_model');
    	
        if(!is_numeric($project_id))
        {
            redirect("/projects");    
        }
                
    	// Load the project object
        $project = $this->project_model->get_details($project_id);
		$project->project_id = $project_id;
		$project_min_price = $this->property_model->get_min_total_price($project_id);
		
		foreach($project_min_price->result() AS $project_min_price);
		$this->data["project_min_price"] = $project_min_price->min_total_price;		
        //By Ajay TasksEveryday
		$this->data["property_data"] = $this->property_model->get_property_min_max();
				
        //END
        if(!$project)
        {
            redirect("/projects");    
        }
        
        $user_logged = $this->users_model->get_details($this->user_id);
        
        if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
        {
			$exists_project = $this->property_permissions_model->exists_project($project_id, $this->user_id);
			if (!$exists_project)
			{
				redirect("/projects");
			}
        }
        
        $this->data["project"] = $project;

        // Load the photo gallery for this area
        $this->data["gallery"] = $this->document_model->get_list($doc_type = "project_gallery", $foreign_id = $project_id);
        
        // Load project documents
        $this->data["docs"] = $this->document_model->get_list($doc_type = "project_document", $foreign_id = $project_id);	
        
        // Load project metadata
        $this->data["metadata"] = $this->project_meta_model->get_list(array("project_id" => $project_id)); 
        
        // Load project properties
        $filters = array();
        $filters["enabled"] = 1;
        $filters["archived"] = 0;
        $filters["project_id"] = $project_id;
        $filters["status"] = "available";
        
        $user_logged = $this->users_model->get_details($this->user_id);
        if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
        {
        	$filters['permissions_user_id'] = $this->user_id;
        }
        
        $this->data["properties"] = $this->property_model->get_list($filters);         

        // Check if we need to create a static map image using Google Maps for this project
		$this->project_model->create_map_image($project_id);
		
        // Google maps image
        $this->data["map"] = "";
        $map_image = "project_files/" . $project_id . "/map.png";
        $map_image_abs = ABSOLUTE_PATH . "project_files/" . $project_id . "/map.png";
        
        if(file_exists($map_image_abs)) {
            $this->data["map"] = $map_image;      
        }
        

        $this->data["user_type_id"] = $this->user_type_id;
        $this->data["meta_title"] = "Project " . $project->project_name;
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/projects/detail/prebody.php', $this->data); 
        $this->load->view('member/projects/detail/main.php', $this->data);
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
            case "load_projectlist":   // User is trying to update their own account
                $this->handle_load_projectlist();
                break;
                
            case "set_latlng":   // User is trying to update their own account
                $this->handle_set_latlng();
                break;                
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the load_projectlist action
    * Send a html listing of stock items back if successful
    */
    private function handle_load_projectlist()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        $this->form_validation->set_rules('list_type', 'List Type', 'required');            
        $this->form_validation->set_rules('min_total_price', 'Min Total Price', 'required|number');
        $this->form_validation->set_rules('max_total_price', 'Max Total Price', 'required|number');         
        $this->form_validation->set_rules('area_id', 'Area ID', 'number');
        $this->form_validation->set_rules('state_id', 'State ID', 'number');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        // Sort By Columns
        $valid_columns = array(
                                "p.project_name",
                                "a.area_name",
                                "s.name",
                                "p.prices_from",
                                "p.rate"
                            );
                            
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }
        
        $current_page = $this->input->post("current_page");
        $list_type = $this->input->post("list_type");
        
        $search_fields = array("min_total_price", "max_total_price", "area_id", "state_id");
        
        //$page_no = ($current_page - 1) * STOCKLIST_PER_PAGE;
        $page_no = $current_page;
        $limit = STOCKLIST_PER_PAGE;
        
        // On the map view, load all properties.
        if($list_type == "map")
        {
            $limit = 9999;   
            $page_no = 1;
        }         
        
        $filters = array();
        $filters["user_id"] = $this->user_id;
        $filters["archived"] = 0;   
        $filters["has_available"] = 1;

        foreach($search_fields as $field)
        {
            $filters[$field] = $this->input->post($field);    
        }
        
        $user_logged = $this->users_model->get_details($this->user_id);
        if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
        {
        	$filters['permissions_user_id'] = $this->user_id;
        }
        
        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");

        $projects = $this->project_model->get_list(1, $limit, $page_no, $count_all, $this->input->post("keysearch"), $order_by, $filters);

		
        switch($list_type)
        {
            case "list":
                $this->data["message"] = $this->load->view("member/projects/list/list", array("projects" => $projects), true);
                break;
                
            case "grid":
                $this->data["message"] = $this->load->view("member/projects/list/grid", array("projects" => $projects), true);
                break;
                
            case "map":
                // For the map view, just send back the raw property data
                $array = array();

                if($projects)
                {
                    foreach($projects->result() as $project)
                    {
                        $item = array();
                        $item["project_id"] = $project->project_id;
                        $item["project_name"] = $project->project_name;
                        $item["area_name"] = $project->area_name;
                        $item["state"] = $project->state;
                        $item["prices_from"] = number_format($project->prices_from, 0, ".", ",");
                        $item["rate"] = $project->rate;
                        $item["url"] = base_url() . "projects/detail/" . $project->project_id;
                        $item["lat"] = "";
                        $item["lng"] = "";
                        
                        // Get the lat/lng pairs out of the embed url
                        $found = preg_match_all("/ll=[-\d\.]*,[-\d\.]*/", $project->google_map_code, $matches);
                        
                        if($found > 0)
                        {
                            $matched = $matches[0];
                            $num_matches = count($matched);
                            $latlng = str_replace("ll=", "", $matched[$num_matches - 1]);    
                            
                            $latlng = explode(",", $latlng);
                            if(count($latlng) == 2)
                            {                     
                                $item["lat"] = $latlng[0];
                                $item["lng"] = $latlng[1];                                
                            }
                        }                        

                        $item["image"] = null;
                        
                        if($project->logo != "")
                        {
                            $src = $project->logo;
                            $item["image"] = image_resize($src, 196, 130);                
                        }          
                        
                        // Add the item to the property array
                        $array[] = $item;
                    }
                }        
            
                $this->data["message"] = $array;
                break;                                
                
            default:
                $this->data["message"] = "Invalid List Type";
                send($this->data);
                break;                
        }
        
        $this->data["status"] = "OK";
        $this->data["count_all"] = $count_all;
		
        send($this->data);
    }
	
	function downloads($type,$foreign_id,$doc_type,$filename)
	{

		echo $path = $type.'/'.$foreign_id.'/'.$doc_type.'/'.$filename;
		redirect($path);
	}


	
}