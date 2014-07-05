<?php
// NOTE: Check application/core/MY_Controller to see how user permissions are enforced.

// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR.','.USER_TYPE_PARTNER);

class Investors extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Investors()
    {
        $this->data = array();
        $this->load_number_per_page = 5;
        
        parent::__construct();
        
        $this->load->model("Users_model");
    }
   
    function index()
    {
        $this->data["meta_title"] = "My Investors";
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/investors/list/prebody.php', $this->data); 
        $this->load->view('member/investors/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function detail($user_id = "")
    {
    	$this->load->model("Tasks_model");
    	$this->load->model("Notes_model");
    	
        $add_mode = !is_numeric($user_id);
        $this->data["user"] = false;
        
        if($add_mode)
        {
            $this->data["meta_title"] = "Add New INvestor";    
        }
        else
        {
            // Load the user object
            $user = $this->Users_model->get_details($user_id);
            if(!$user)
            {
                redirect("/investors");     
            }
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                redirect("/investors");    
            }            
            
            // Load tasks
            $taskFilters = array();
	        $taskFilters["created_by"] = $this->user_id;     
	        
	        $tasks = $this->Tasks_model->get_list($taskFilters, "t.due_date ASC", $this->load_number_per_page, 1, $count_all);
	        $this->data['tasks'] = $tasks;
	        
	        // Load notes
	        $noteFilters = array();
	        $noteFilters['created_by'] = $this->user_id;
	        $noteFilters['note_type'] = 'user';
	        $noteFilters['foreign_id'] = $user_id;
	        
	        $notes = $this->Notes_model->get_list($noteFilters, 'note_date DESC', $this->load_number_per_page, 0);
	        $this->data['notes'] = $notes;
            
	        // Load all the data that we need for the views
	        $this->data["priorities"] = $this->Tasks_model->get_priorities();
	        $this->data["statuses"] = $this->Tasks_model->get_statuses();
	        
            $this->data["user"] = $user; 
            
            // Load the user statistics
            // @todo Make Stats function for investors
            $this->data["stats"] = $this->Users_model->get_investor_stats($user_id);
            
            $this->data["meta_title"] = "Investor Detail";
        }
        
        $this->data["add_mode"] = $add_mode; 
        
        // Load the state options for Australia
        $this->data["states"] = $this->tools_model->get_states(1);
    	
        // Load owners
        $filters = array();
        $filters["created_by_user_id"] = $this->user_id;
        $filters["deleted"] = 0;
        $filters["order_by"] = "u.first_name ASC";
        $user_type = array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER);
        $owner_select_sql = ", CASE " .
            "WHEN (length(u.company_name) > 0) THEN CONCAT(u.first_name, ' ', u.last_name, ' (', u.company_name, ')') " .
            "ELSE CONCAT(u.first_name, ' ', u.last_name) " .
            "END as owner_name";
        
        $owners = $this->Users_model->get_list(1, '', '', $count_all, "", $user_type, $filters, $owner_select_sql);
        $this->data['owners'] = $owners;
        
        // Load advisors
        $filters = array();
        $filters["created_by_user_id"] = $this->user_id;
        $filters["deleted"] = 0;
        $filters["order_by"] = "u.first_name ASC";
        $user_type = array(USER_TYPE_ADVISOR);
        $owner_select_sql = ", CASE " .
            "WHEN (length(u.company_name) > 0) THEN CONCAT(u.first_name, ' ', u.last_name, ' (', u.company_name, ')') " .
            "ELSE CONCAT(u.first_name, ' ', u.last_name) " .
            "END as owner_name";
        
        $advisors = $this->Users_model->get_list(1, '', '', $count_all, "", $user_type, $filters, $owner_select_sql);
        $this->data['advisors'] = $advisors;         
        
        $this->data["relationship_types"] = array(
            "Single" => "Single",      
            "Married" => "Married",
            "Defacto" => "Defacto",
            "Divorce" => "Divorce"
            );         
        
        $assign_filters = array();
        $user_ids = array();
    	$user_logged = $this->users_model->get_details($this->user_id);
    	$user_ids[] = $user_logged->created_by_user_id;
    	$user_ids[] = $user_logged->advisor_id;
    	$user_ids[] = $this->user_id;
    	$assign_filters['in_arr_ids'] = $user_ids;
        
        if (in_array($this->user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER)))
        {
        	$assign_filters['created_by'] = $this->user_id;
        }
        
        $user_type = array(USER_TYPE_ADVISOR, USER_TYPE_INVESTOR, USER_TYPE_PARTNER);
        $assign_filters["deleted"] = 0;
        $assign_filters["order_by"] = "u.first_name ASC";
        $assign_client_select_sql = ", CASE " .
            "WHEN (length(u.company_name) > 0) THEN CONCAT(u.first_name, ' ', u.last_name, ' (', u.company_name, ')') " .
            "ELSE CONCAT(u.first_name, ' ', u.last_name) " .
            "END as assign_client_name";
        
        $assign_users = $this->users_model->get_list(1, '', '', $count_all, "", $user_type, $assign_filters, $assign_client_select_sql);
        $this->data['assign_users'] = $assign_users;
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/investors/detail/prebody.php', $this->data); 
        $this->load->view('member/investors/detail/main.php', $this->data);
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
            case "update_investor":   // User is trying to login.
                $this->handle_update_investor();
                break;
                
            case "load_investors":
                $this->handle_load_investors();
                break;
                
            case "load_tasks":
                $this->handle_load_tasks();
                break;
                
            case "load_notes":
                $this->handle_load_notes();
                break;
                
            case "login_as_this_user":
            	$this->handle_login_as_this_user();
            	break;
            	
        	case "load_stock_project_permissions":
            	$this->handle_load_stock_project_permissions();
            	break;
            	
        	case "assign_project":
            	$this->handle_assign_project();
            	break;
            	
        	case "load_property_permissions":
            	$this->handle_load_property_permissions();
            	break;
            	
        	case "assign_property":
            	$this->handle_assign_property();
            	break;
            	
        	case "remove_project":
            	$this->handle_remove_project();
            	break;
            	
        	case "remove_property":
            	$this->handle_remove_property();
            	break;
            	
        	case "update_view_all_property":
            	$this->handle_update_view_all_property();
            	break;
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the handle_load_investors action
    * Loads a list of investors in accordance with search params
    */
    private function handle_load_investors()
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
        $filters["owner_id"] = $this->user_id;
        $filters["deleted"] = 0;
        $filters["order_by"] = "u.first_name ASC";        
        $filters["search_term"] = $this->input->post("search_term");
        $filters["user_status"] = $this->input->post("user_status");
        $extra_sql = ", get_investor_status_count(u.user_id, 'sold') as num_sold, get_investor_last_status_date(u.user_id, 'sold') as last_sold_date , get_last_note_date(u.user_id) as notes_last_created ";
        
        // Sort By Columns
        $valid_columns = array(
                                "u.first_name",
                                'u.company_name',
                                'u.mobile',
                                'u.status',
                                'num_sold',
                                'last_sold_date',
                                'days_since_login'
                            );
                            
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }  
        
        $sort_col = $this->input->post("sort_col");
        if($sort_col == "days_since_login") $sort_col = "Floor(TIMESTAMPDIFF(SECOND, u.last_logged_dtm, Now()) / 86400)";
        $filters["order_by"] = $sort_col . " " . $this->input->post("sort_dir"); 
        
        $users = $this->Users_model->get_list(-1, $limit = PARTNERS_PER_PAGE, $page_no = $current_page, $count_all, "", $user_type = USER_TYPE_INVESTOR, $filters, $extra_sql);        
        
        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/investors/list/list", array("users" => $users), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
                   
    
    /***
    * Handles the handle_update_investor action
    * Send an OK status back if the user was updated/added successfully, error if not.
    */
    private function handle_update_investor()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        // Validate the form submission
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|email');
        $this->form_validation->set_rules('secondary_email', 'Secondrary Email', 'email');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $user_id = $this->input->post("user_id");
        $add_mode = false;
        
        // We're updating an existing user.  Load their details.
        $user = $this->Users_model->get_details($user_id);
        
        if(is_numeric($user_id))
        {
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }            
        }
        else
        {
            // We are inserting a new user.
            $add_mode = true;
            
            // Check to see if a user already exists with this email address
            if($this->Users_model->exists($this->input->post("email")))
            {
                $this->data["message"] = "Sorry, someone with that email address is already registered with The Aspire Network.";
                send($this->data);            
            }
        }
        
        
        $save = array();
        $save["email"] = $this->input->post("email");
        $save["secondary_email"] = $this->input->post("secondary_email");
        $save["first_name"] = $this->input->post("first_name");    
        $save["middle_name"] = $this->input->post("middle_name");
        $save["last_name"] = $this->input->post("last_name");
        $save["status"] = $this->input->post("status");
        $save["company_name"] = $this->input->post("company_name");
        $save["phone"] = $this->input->post("phone");
        $save["mobile"] = $this->input->post("mobile");
        $save["home_phone"] = $this->input->post("home_phone");
        $save["fax"] = $this->input->post("fax");
        $save["billing_address1"] = $this->input->post("billing_address1");
        $save["billing_address2"] = $this->input->post("billing_address2");
        $save["billing_suburb"] = $this->input->post("billing_suburb");
        $save["billing_postcode"] = $this->input->post("billing_postcode");
        $save["billing_state_id"] = ifEmptyNull($this->input->post("billing_state_id"));
        $save["billing_country_id"] = ifEmptyNull($this->input->post("billing_country_id"));
        $save["comment"] = $this->input->post("comment");
        $save["login_expiry_date"] = $this->input->post("login_expiry_date");
		
        // Postal address
        $save["delivery_address1"] = $this->input->post("delivery_address1");
        $save["delivery_address2"] = $this->input->post("delivery_address2");
        $save["delivery_suburb"] = $this->input->post("delivery_suburb");
        $save["delivery_postcode"] = $this->input->post("delivery_postcode");
        $save["delivery_state_id"] = ifEmptyNull($this->input->post("delivery_state_id"));
        $save["delivery_country_id"] = ifEmptyNull($this->input->post("delivery_country_id"));              
        
        $save["legal_purchase_entity"] = $this->input->post("legal_purchase_entity");
        $save["purchase_comments"] = $this->input->post("purchase_comments");
        $save["acn"] = $this->input->post("acn");
        $save["smsf_purchase"] = $this->input->post("smsf_purchase");
        
        // Additional contact
        $save["additional_contact_first_name"] = $this->input->post("additional_contact_first_name");
        $save["additional_contact_middle_name"] = $this->input->post("additional_contact_middle_name");
        $save["additional_contact_last_name"] = $this->input->post("additional_contact_last_name");
        $save["additional_contact_relationships"] = $this->input->post("additional_contact_relationships");
        $save["additional_contact_phone"] = $this->input->post("additional_contact_phone");
        $save["additional_contact_mobile"] = $this->input->post("additional_contact_mobile");
        $save["additional_contact_email"] = $this->input->post("additional_contact_email");
        $save["additional_contact_comment"] = $this->input->post("additional_contact_comment");        
        
        $save["enabled"] = $this->input->post("enabled") ? $this->input->post("enabled") : 0;
        
        // Only the user that created the lead can set the owner and advisor and lead source
        if(($user) && ($user->created_by_user_id == $this->user_id))
        {
            $save["owner_id"] = ifEmptyNull($this->input->post("owner_id"));
            $save["other_lead_source"] = $this->input->post("other_lead_source");            
        }        
        
        if($add_mode)
        {
            // When adding a new record, set the username to the email
            $save["username"] = $this->input->post("email");    
            $save["user_type_id"] = USER_TYPE_INVESTOR;
            $save["salt"] = random_string("alnum", 15);
            $save["created_by_user_id"] = $this->user_id;
            $save["owner_id"] = ifEmptyNull($this->input->post("owner_id"));
            $save["other_lead_source"] = $this->input->post("other_lead_source");              
            
            // Create a random password for the user.
            $password = random_string("alnum", 8);
            $save["password"] = hash("SHA256", $password . $save["salt"]);
        }
        
        if ($this->input->post("advisor_id") != "")
        {
        	$save["advisor_id"] = $this->input->post("advisor_id");
        }
        else
        {
        	if ($this->user_type_id == USER_TYPE_ADVISOR)
        		$save["advisor_id"] = $this->user_id;
        	else
        		$save["advisor_id"] = null;
        }
        
        $user_id = $this->Users_model->save($user_id, $save);
        
        if(!$user_id)
        {
            $this->data["message"] = "Sorry, someone went wrong whilst trying to save this partner.";
            send($this->data);             
        }
        
        if($add_mode)
        {
            // Send the welcome email
            $email_data = array();
            $email_data["first_name"] = $this->input->post("first_name");
            $email_data["email"] = $this->input->post("email");
            $email_data["password"] = $password;
            $email_data["added_by"] = $this->session->userdata["first_name"] . " " . $this->session->userdata["last_name"];
            $email_data["login_link"] = base_url() . "login";
            
            $this->email_model->send_email($this->input->post("email"), "welcome_to_aspire", $email_data,  $attach = "", $bcc = array());    
        }
        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
                
        send($this->data);
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
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        // Sort By Columns
        $valid_columns = array(
        						"t.title",
        						't.due_date',
        						't.priority'
    						);
    						
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }
        
        $current_page = $this->input->post("current_page");
        
        if ($current_page)
        	$limit = $current_page * $this->load_number_per_page;
        else
        	$limit = $this->load_number_per_page;
        
        $filters = array();
        $filters["created_by"] = $this->user_id;     
        $filters["assign_to"] = $this->user_id;
        
        if($this->input->post("status") != "")
        {
            $filters["status"] = $this->input->post("status");
        }
        
        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        $tasks = $this->Tasks_model->get_list($filters, $order_by, $limit, 1, $count_all);        

        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/investors/detail/task_listing", array("tasks" => $tasks), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
    
    /***
    * Handles the handle_load_notes action
    * Loads a list of notes
    */
    private function handle_load_notes()
    {
    	$this->load->model('Notes_model');
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
        
        if ($current_page)
        {
        	$limit = $current_page * $this->load_number_per_page;
        }
        else
        {
        	$limit = $this->load_number_per_page;
        }
        
        $filters = array();
        $filters["created_by"] = $this->user_id;     
        $filters["note_type"] = 'user';
        $filters["foreign_id"] = $this->input->post('user_id');     
        
		$notes = $this->Notes_model->get_list($filters, 'note_date DESC', $limit, 0);
		
		$count_all = $this->Notes_model->get_list($filters, 'note_date DESC', 0, 0, true);
		$count_all = $count_all ? $count_all : 0;
		
        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/investors/detail/note_listing", array("notes" => $notes), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
    
    /***
    * Handles the login_as_this_user action
    * Send an OK status back if the user was login as this user successfully, error if not.
    */
    private function handle_login_as_this_user()
    {
       	$this->load->library('form_validation');
        $this->load->model('login_model');
        
    	$user_id = $this->input->post("user_id");
		$email = $this->input->post('email');
		
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_array_details($user_id);
            $objUser = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($objUser, $this->user_id))            
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
            
            $array_items = array(
	            'user_id' => '',
	            'username' => '',
	            'first_name' => '',
	            'last_name' => '',
	            'company' => '',
	            'logged_in' => '',
	            'user_type_id' => '',
	            'logo' => '',
	            'advisor_first_name' => '',
	            'advisor_last_name' => '',
	            'advisor_email' => '',
	            'advisor_phone' => '',
	            'advisor_logo' => ''
	        );
	                        
	        $this->session->unset_userdata($array_items);
            
            $this->login_model->setSessionData($user);
            
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        send($this->data);
    }
    
    /***
    * Handles the load_stock_project_permissions action
    * Send an OK status back if load stock permissions successfully, error if not.
    */
    private function handle_load_stock_project_permissions()
    {
        $this->load->model('project_model');
        $this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$project_id = $this->input->post("project_id");
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
            
            $filters_assign_projects = array();
	        $filters_assign_projects["permission_type"] = 'Project';
	        $filters_assign_projects["user_id"] = $user_id;
	        
			$assign_projects = $this->ppmd->get_list($filters_assign_projects, 'created_dtm ASC', 0, 0);
			
			if ($this->user_type_id == USER_TYPE_ADVISOR)
			{
				// Get all Project
				$projects = $this->project_model->get_projects('p.project_name ASC','',true,false,'',-1);
			}
			else // User is partner
			{
				$partner = $this->users_model->get_details($this->user_id);
				if ($partner->view_all_property == 1)
				{
					// If view_all_property == 1 -> Get all Project
					$projects = $this->project_model->get_projects('p.project_name ASC','',true,false,'',-1);
				}
				else
				{
					$filters_projects = array();
			        $filters_projects["permission_type"] = 'Project';
			        $filters_projects["user_id"] = $user->created_by_user_id;
			        
					$projects = $this->ppmd->get_list($filters_projects, 'created_dtm ASC', 0, 0);
				}
			}
			
			$data = array(
							'assign_projects' => $assign_projects,
							'project_id' => $project_id,
							'projects' => $projects
						);
			
            $this->data["status"] = "OK";
            $this->data["message"] = $this->load->view("member/investors/detail/stock_project_permissions", $data, true);
            send($this->data);
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
    }
    
    /***
    * Handles the handle_assign_project action
    * Send an OK status back if the user was assign project permisson successfully, error if not.
    */
    private function handle_assign_project()
    {
        $this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$project_id = $this->input->post("project_id");
		
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
            
            if ($project_id)
            {
				$exists_project = $this->ppmd->exists_project($project_id,$user_id);
				
				if (!$exists_project) {
					$data = array(
            					'permission_type' => 'Project',
            					'foreign_id' => $project_id,
            					'user_id' => $user_id,
            					'created_dtm' => date("Y-m-d %H:%i:%s")
            				);
    				$property_permissions_id = $this->ppmd->save('',$data);
				}
				
				$this->data["status"] = "OK";
            	send($this->data);
            }
            else 
            {
            	$this->data['message'] = 'Project id is required';
        		send($this->data);
            }
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
    }
    
    /***
    * Handles the load_property_permissions action
    * Send an OK status back if load property permissions successfully, error if not.
    */
    private function handle_load_property_permissions()
    {
        $this->load->model('property_model');
        $this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$project_id = $this->input->post("project_id");
    	$property_id = $this->input->post("property_id");
    	
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
            
            $filters_assign_projects = array();
	        $filters_assign_projects["permission_type"] = 'Project';
	        $filters_assign_projects["user_id"] = $user_id;
            $assign_projects = $this->ppmd->get_list($filters_assign_projects, 'created_dtm ASC', 0, 0);
            
            $project_ids = array('');
			if($assign_projects)
			{
				foreach ($assign_projects->result() AS $assign_project)
				{
					$project_ids[] = $assign_project->foreign_id;
				}
			}
            
            $filters_assign_properties = array();
	        $filters_assign_properties["permission_type"] = 'Property';
	        $filters_assign_properties["user_id"] = $user_id;
	        //$filters_property["project_id"] = !empty($project_id) ? $project_id : -1;
	        
			$assign_properties = $this->ppmd->get_list($filters_assign_properties, 'created_dtm ASC', 0, 0);
			
			$arr_not_in_property = array();
			if($assign_properties)
			{
				foreach ($assign_properties->result() AS $assign_property)
				{
					$arr_not_in_property[] = $assign_property->foreign_id;
				}
			}
			$properties_data = array(
	            'enabled' => 1,
	            'limit' => 0,
	            'page_no' => 0,
	            'archived' => 0,
	            'not_in_property' => $arr_not_in_property,
	            'project_id' => $project_ids
	        );
	        
	        if ($this->user_type_id == USER_TYPE_ADVISOR)
			{
				// Get all Property
				$properties = $this->property_model->get_list($properties_data, $count_all);
			}
			else // User is partner
			{
				$partner = $this->users_model->get_details($this->user_id);
				if ($partner->view_all_property == 1)
				{
					// If view_all_property == 1 Get all Property
					$properties = $this->property_model->get_list($properties_data, $count_all);
				}
				else
				{
					$filters_properties = array();
			        $filters_properties["permission_type"] = 'Property';
			        $filters_properties["not_in_property"] = $arr_not_in_property;
			        $filters_properties["user_id"] = $user->created_by_user_id;
			        $filters_properties["project_id"] = $project_ids;
					$properties = $this->ppmd->get_list($filters_properties, 'created_dtm ASC', 0, 0);
				}
			}
	        
			$data = array(
							'assign_properties' => $assign_properties,
							'property_id' => $property_id,
							'properties' => $properties
						);
					
            $this->data["status"] = "OK";
            $this->data["message"] = $this->load->view("member/investors/detail/stock_property_permissions", $data, true);
            send($this->data);
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
    }
    
    /***
    * Handles the handle_assign_property action
    * Send an OK status back if the user was assign property permisson successfully, error if not.
    */
    private function handle_assign_property()
    {
        $this->load->model('property_model');
        $this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$property_id = $this->input->post("property_id");
		
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
            
            if ($property_id)
            {
				$exists_property = $this->ppmd->exists_property($property_id,$user_id);
				
				if (!$exists_property) {
					$data = array(
            					'permission_type' => 'Property',
            					'foreign_id' => $property_id,
            					'user_id' => $user_id,
            					'created_dtm' => date("Y-m-d %H:%i:%s")
            				);
    				$property_permissions_id = $this->ppmd->save('',$data);
				}
				
				$this->data["status"] = "OK";
            	send($this->data);
            }
            else 
            {
            	$this->data['message'] = 'Property id is required';
        		send($this->data);
            }
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
    }
    
    /***
    * Handles the handle_remove_project action
    * Send an OK status back if the user was remove project permisson successfully, error if not.
    */
    private function handle_remove_project()
    {
        $this->load->model('project_model');
        $this->load->model('property_model');
        $this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$project_id = $this->input->post("project_id");
    	$property_permission_id = $this->input->post("property_permission_id");
		
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
            
            if ($property_permission_id)
            {
            	$this->ppmd->delete_project($property_permission_id, $project_id, $user_id);
				$this->data["status"] = "OK";
            	send($this->data);
            }
            else 
            {
            	$this->data['message'] = 'Property Permisson id is required';
        		send($this->data);
            }
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
    }
    
    /***
    * Handles the handle_remove_property action
    * Send an OK status back if the user was remove property permisson successfully, error if not.
    */
    private function handle_remove_property()
    {
        $this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$property_permission_id = $this->input->post("property_permission_id");
		
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
            
            if ($property_permission_id)
            {
				$this->ppmd->delete($property_permission_id);
				$this->data["status"] = "OK";
            	send($this->data);
            }
            else 
            {
            	$this->data['message'] = 'Property Permisson id is required';
        		send($this->data);
            }
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
    }
    
    /***
    * Handles the handle_update_view_all_property action
    * Send an OK status back if the user was update view all property successfully, error if not.
    */
    private function handle_update_view_all_property()
    {
    	$this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$value = $this->input->post("value");
		
        if($user_id)
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if(!has_permission($user, $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }
        
			$this->Users_model->save($user->user_id, array('view_all_property' => $value));
			$this->data["status"] = "OK";
        	send($this->data);
        }
        else
        {
        	$this->data['message'] = 'User id is required';
        	send($this->data);
        }
    }
}