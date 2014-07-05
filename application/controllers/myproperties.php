<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Myproperties extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Myproperties()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("property_model");
        $this->load->model("area_model");
        $this->load->model("project_model");
        $this->load->model("area_model");
        $this->load->model("resources_model");
        $this->load->model("document_model");
        $this->load->model("favourites_model");
        
        $this->load->helper("image");
    }
    
    function index()
    {
        $this->data["meta_title"] = "Stocklist"; 
        
        $this->data["property_data"] = $this->property_model->get_property_min_max();
        $this->data["projects"] = $this->project_model->get_list(1, "", "", $count_all, "", "p.project_name ASC");
        $this->data["areas"] = $this->area_model->get_list(1, "", "", $count_all, "", "area_name ASC");
        $this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
        $this->data["property_types"] = $this->resources_model->get_list($resource_type = "property_type");
        $this->data["contract_types"] = $this->resources_model->get_list($resource_type = "contract_type");
        $this->data["status_options"] = $this->property_model->get_property_status();

        $this->load->view('member/header', $this->data);
        $this->load->view('member/myproperties/prebody.php', $this->data); 
        $this->load->view('member/myproperties/main.php', $this->data);
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
            case "load_my_properties":   // User is trying to update their own account
                $this->handle_load_my_properties();
                break;
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the handle_load_my_properties action
    * Send a html listing of my properties items back if successful
    */
   private function handle_load_my_properties()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        $this->form_validation->set_rules('list_type', 'List Type', 'required');
        /*
        $this->form_validation->set_rules('min_bedrooms', 'Min Bedrooms', 'required|number');
        $this->form_validation->set_rules('max_bedrooms', 'Max Bedrooms', 'required|number');
        $this->form_validation->set_rules('min_bathrooms', 'Min Bathrooms', 'required|number');
        $this->form_validation->set_rules('max_bathrooms', 'Max Bathrooms', 'required|number');  
        $this->form_validation->set_rules('min_garage', 'Min Garage', 'required|number');
        $this->form_validation->set_rules('max_garage', 'Max Garage', 'required|number');               
        $this->form_validation->set_rules('min_total_price', 'Min Total Price', 'required|number');
        $this->form_validation->set_rules('max_total_price', 'Max Total Price', 'required|number');         
        $this->form_validation->set_rules('min_land', 'Min Land', 'required|number');
        $this->form_validation->set_rules('max_land', 'Max Land', 'required|number'); 
        $this->form_validation->set_rules('min_yield', 'Min Yield', 'required|number');
        $this->form_validation->set_rules('max_yield', 'Max Yield', 'required|number');                              
        $this->form_validation->set_rules('nras', 'NRAS', 'number');
        $this->form_validation->set_rules('smsf', 'SMSF', 'number');
        $this->form_validation->set_rules('project_id', 'Project ID', 'number');
        $this->form_validation->set_rules('area_id', 'Area ID', 'number');
        $this->form_validation->set_rules('state_id', 'State ID', 'number');
        $this->form_validation->set_rules('property_type_id', 'Property Type ID', 'number');
        $this->form_validation->set_rules('contract_type_id', 'Contract Type ID', 'number');
        */
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        // Sort By Columns
        $valid_columns = array(
        						"p.address",
        						"p.total_price",
        						"r1.name",
        						"p.house_area",
        						"p.land",
                                "p.featured DESC, p.rent_yield",
        						"p.rent_yield",
        						"p.nras",
        						"p.smsf",
        						"area.area_name",
        						"st.name",
        						"proj.project_name"
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
        
        /*
        $search_fields = array("min_bedrooms", "max_bedrooms", "min_bathrooms", "max_bathrooms", "min_garage", 
            "max_garage", "min_total_price", "max_total_price", "min_land", "max_land", "min_house", "max_house", 
            "min_yield", "max_yield", "nras", "smsf", "project_id", "area_id", "state_id", "property_type_id", 
            "contract_type_id", "status");
        */         
        
        $filters = array();
        //$filters["user_id"] = $this->user_id;
        $filters["enabled"] = 1;
        $filters["archived"] = 0;   
        $filters["keysearch"] = $this->input->post("keysearch");
        $filters["limit"] = STOCKLIST_PER_PAGE;
        $filters["offset"] = ($current_page - 1) * STOCKLIST_PER_PAGE;
        /*
        foreach($search_fields as $field)
        {
            $filters[$field] = $this->input->post($field);    
        }
        */

        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        $user_logged = $this->users_model->get_details($this->user_id);
        $filters['exclusive_to'] = $user_logged->user_id;
       switch ($list_type) {
        	case 'favourite':
        		$filters['favourite_user_id'] = $this->user_id;
                
                if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
                {
                    $filters['permissions_user_id'] = $this->user_id;
                }                
                
        		break;
        	case 'reserved':
        		$filters['status'] = array('EOI Payment Pending', 'reserved', 'contracts executed');
        		switch ($this->user_type_id) {
		        	case USER_TYPE_ADVISOR:
		        		$filters['advisor_id'] = $this->user_id;
		        		break;
		        	case USER_TYPE_PARTNER:
		        		$filters['partner_id'] = $this->user_id;
		        		break;
		    		case USER_TYPE_INVESTOR:
		        		$filters['investor_id'] = $this->user_id;
		        		break;
                    case USER_TYPE_LEAD:
                        $filters['investor_id'] = $this->user_id;
                        break;                          
		        	default:
		        		break;
		        }
        		break;
    		case 'current_purchases':
        		$filters['status'] = array('unconditional approval','settlement land','slab payment complete','frame payment complete','settlement completed property');
                $filters['stage_type'] = 'current_purchase';
                //$filters['stage_status'] = '-1';
                
        		switch ($this->user_type_id) {
		        	case USER_TYPE_ADVISOR:
		        		$filters['advisor_id'] = $this->user_id;
		        		break;
		        	case USER_TYPE_PARTNER:
		        		$filters['partner_id'] = $this->user_id;
		        		break;
		    		case USER_TYPE_INVESTOR:
		        		$filters['investor_id'] = $this->user_id;
		        		break;
                    case USER_TYPE_LEAD:
                        $filters['investor_id'] = $this->user_id;
                        break;                          
		        	default:
		        		break;
		        }
        		break;
    		case 'completed_purchases':
        		$filters['status'] = 'completed purchase';
                $filters['stage_type'] = 'completed_purchase';
                
        		switch ($this->user_type_id) {
		        	case USER_TYPE_ADVISOR:
		        		$filters['advisor_id'] = $this->user_id;
		        		break;
		        	case USER_TYPE_PARTNER:
		        		$filters['partner_id'] = $this->user_id;
		        		break;
		    		case USER_TYPE_INVESTOR:
		        		$filters['investor_id'] = $this->user_id;
		        		break;
                    case USER_TYPE_LEAD:
                        $filters['investor_id'] = $this->user_id;
                        break;                        
		        	default:
		        		break;
		        }            
        		break;
        	default:
        		$filters['favourite_user_id'] = $this->user_id;
        		break;
        }
        
        $properties = $this->property_model->get_list($filters, $count_all, $order_by);
				
		if($list_type != 'favourites')
		{
			$this->data["message"] = $this->load->view("member/myproperties/list", array("properties" => $properties), true);
		}
		else
		{
			$this->data["message"] = $this->load->view("member/myproperties/grid", array("properties" => $properties), true);
        }
        
        /*
        switch($list)
        {
            case "favourites":
                $this->data["message"] = $this->load->view("member/stocklist/list/list", array("properties" => $properties), true);
                // $this->data["message"] = $this->load->view("member/myproperties/favourites", array("properties" => $properties), true);
                break;
                
            case "reserved":
                $this->data["message"] = $this->load->view("member/myproperties/reserved", array("properties" => $properties), true);
                break;
                
            case "current_purchases":
                $this->data["message"] = $this->load->view("member/myproperties/current_purchases", array("properties" => $properties), true);
                break;
                
            case "completed_purchases":
                $this->data["message"] = $this->load->view("member/myproperties/completed_purchases", array("properties" => $properties), true);
                break; 
                
            default:
                $this->data["message"] = "Invalid List Type";
                send($this->data);
                break;                
        }
        */
        $this->data["status"] = "OK";
        $this->data["count_all"] = $count_all;
        send($this->data);
		
    }
}