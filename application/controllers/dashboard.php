<?php
// NOTE: Check application/core/MY_Controller to see available user data and methods.
// MY_Controller makes sure the user is logged in.
class Dashboard extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Dashboard()
    {
        $this->data = array();
        
        parent::__construct();
        
        // $this->output->enable_profiler(TRUE);
		$this->load->model("Users_model");
        $this->load->model("Tasks_model");
		$this->load->model("Leads_model");
		$this->load->model("Article_model");
        $this->load->helper("image");
    }
   
    function index()
    {
		$advisor_id = $this->user_id;
		$this->load->model('property_model');
    	
        $this->data["meta_title"] = "Member Dashboard";
        
        // Load tasks
        $filters = array();
        $filters["created_by"] = $this->user_id;     
        $filters["assign_to"] = $this->user_id;
        $filters["search_term"] = $this->input->post("search_term");
        $filters["status"] = 0; // Active tasks only
        
        $assign_client_select_sql = ", CASE " .
            "WHEN (length(u2.company_name) > 0) THEN CONCAT(u2.first_name, ' ', u2.last_name, ' (', u2.company_name, ')') " .
            "ELSE CONCAT(u2.first_name, ' ', u2.last_name) " .
            "END as assign_client_name";
        
        $this->data["tasks"] = $this->Tasks_model->get_list($filters, "t.due_date ASC", 4, 1, $count_all, $assign_client_select_sql);
		
		
		/////////////////////////////////////////////Enquiry Leads////////////////////////////////////////////////--By Mayur
		
		
		 
		$filters = array();
        $filters["created_by_user_id"] = $this->user_id;
        $filters["owner_id"] = $this->user_id;
        $filters["order_by"] = "u.status ASC";        
        $filters["status"] = array('HOT');

      // $extra_sql = ", get_last_note_date(u.user_id) as notes_last_created  ";
			$extra_sql="";		  
        // Sort By Columns
        $valid_columns = array(
                                "u.first_name",
                                'u.company_name',
                                'u.mobile',
                                'notes_last_created',
                                'days_since_login'
                            );
                            
        $valid_dirs = array("ASC", "DESC");
        
       
            
        $this->data["leads"] = $this->Users_model->get_list(-1,$limit= 5, $page_no =1, $count_all, "", $user_type = USER_TYPE_LEAD, $filters, $extra_sql); 
		
		////////////////////////////////////////End/////////////////////////////////////////////////////////////////
		
		
		
		// $this->data["leads"] = $this->Leads_model->get_list($filters, "id DESC", 4, 1);
        
        $properties_data = array(
            'enabled' => 1,
            'featured' => 1,
            'archived' => 0
        );
        
        $user_logged = $this->users_model->get_details($this->user_id);
        if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
        {
        	$properties_data['permissions_user_id'] = $this->user_id;
        }
        $this->data["featured_properties"] = $this->property_model->get_list($properties_data, $count_all);
		
		// By Mayur - TasksEveryday 
		
		$where = array('is_featured' =>1);
		$this->data["article_data"] = $this->Article_model->get_list($category_id = "", $show_enabled_only = FALSE, $isRSS = false, $order_by = "article_order", $order_direction = "ASC", $items_per_page = 0, $offset = 0, $where, $count_all = 0 );
		
		// By Mayur - TasksEveryday
		
		$this->data["user_type_id"] = $this->user_type_id;
        
        if (in_array($this->user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))) {
        	// get recent reservations
        	$count = 0;
        	$data = array(
        	   'status' => 'reserved',
			   'offset' => 0,
			   'limit' => 5,
        	);
        	if ($this->user_type_id == USER_TYPE_ADVISOR) {
        		$data['advisor_id'] = $this->user_id;
        	} else {
        	    $data['partner_id'] = $this->user_id;
        	}
        	$orderby = 'ts_reserved_date DESC';
        	$this->data['recent_reservations'] = $this->property_model->get_list($data, $count, $orderby);
			//print_r($this->db->last_query());exit();
			
			$this->data['user_details'] = $this->users_model->number_of_reports($advisor_id);

        }

        $this->load->view('member/header', $this->data);
        $this->load->view('member/dashboard/prebody.php', $this->data); 
        $this->load->view('member/dashboard/main.php', $this->data);
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
            //case "login":   // User is trying to login.
            //    $this->handle_login();
            //    break;
			//Changed the name load_task to load_tasks -- By Mayur 
        	case "load_task":
                $this->handle_load_tasks();
                break;
				
			case "load_leads":
                $this->handle_load_leads();
                break;
				
		    case "load_reservation":
                $this->handle_load_reservation();
                break;
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the handle_load_tasks action
    * Loads a list of tasks
    */
    private function handle_load_tasks()
    {
    	$this->load->model('Tasks_model');
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Sort By Columns
        $valid_columns = array(
        						"t.title",
        						't.due_date',
        						'u2.first_name',
        						't.priority'
    						);
    						
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }
        
        $filters = array();
        $filters["created_by"] = $this->user_id;
        $filters["assign_to"] = $this->user_id;
        $filters["status"] = 0;
        
        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        $assign_client_select_sql = ", CASE " .
            "WHEN (length(u2.company_name) > 0) THEN CONCAT(u2.first_name, ' ', u2.last_name, ' (', u2.company_name, ')') " .
            "ELSE CONCAT(u2.first_name, ' ', u2.last_name) " .
            "END as assign_client_name";
        
        $tasks = $this->Tasks_model->get_list($filters, $order_by, 4, 1, $count_all,$assign_client_select_sql);

        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/dashboard/task_listing", array("tasks" => $tasks), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
	
	private function handle_load_reservation()
	
	{
		$this->load->model('property_model');
        // Load neccessary libs and models
        $this->load->library('form_validation');
		$count = 0;
        	$data = array(
        	   'status' => 'reserved'
        	);
        	if ($this->user_type_id == USER_TYPE_ADVISOR) {
        		$data['advisor_id'] = $this->user_id;
        	} else {
        	    $data['partner_id'] = $this->user_id;
        	}
			
			
			if (in_array($this->user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))) {
        	
				$data['offset'] = 0;
				$data['limit'] = 5;   
				    
			}
			
			
			
			
			$valid_dirs = array("ASC", "DESC");
			
			$orderby=$this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        	$recent_reservations = $this->property_model->get_list($data, $count, $orderby);
		    $this->data["status"] = "OK";
            $this->data["message"] = $this->load->view("member/dashboard/reservation_listing", array("recent_reservations" =>     $recent_reservations), true);
           // $this->data["count_all"] = $count_all;
		    send($this->data);
	}
	
	/***
    * Handles the handle_load_leads action
    * Loads a list of leads in accordance with search params
    */
    private function handle_load_leads()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        $filters = array();
        $filters["created_by_user_id"] = $this->user_id;
        $filters["owner_id"] = $this->user_id;
     
        $filters["status"] = array('HOT');

        //$extra_sql = ",(select n.created_date from nc_notes n where n.foreign_id=u.user_id  ORDER BY  n.`created_date` DESC LIMIT 0 , 1) as notes_last_created ";
        // $extra_sql = ",  get_last_note_date(u.user_id) as notes_last_created ";

		$extra_sql="";  
        
        // Sort By Columns
        $valid_columns = array(
                                "u.first_name",
                                'u.company_name',
                                'u.mobile',
                                'notes_last_created',
                                'days_since_login'
                            );
                            
        $valid_dirs = array("ASC", "DESC");
        
        $sort_col = $this->input->post("sort_col");
        if($sort_col == "days_since_login") $sort_col = "Floor(TIMESTAMPDIFF(SECOND, u.last_logged_dtm, Now()) / 86400)";
        if($sort_col == "notes_last_created") $sort_col = "get_user_note_last_created(u.user_id)";
		
        $filters["order_by"] = $sort_col . " " . $this->input->post("sort_dir");      

        $users = $this->Users_model->get_list(-1,$limit= 5, $page_no =1, $count_all, "", $user_type = USER_TYPE_LEAD, $filters, $extra_sql);        

        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/dashboard/leads_listing", array("leads" => $users), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
}