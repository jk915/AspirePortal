<?php
// NOTE: Check application/core/MY_Controller to see how user permissions are enforced.

// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER);

class Advisors extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Advisors()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("Users_model");
    }
   
    function index()
    {
        $this->data["meta_title"] = "My Advisors";
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/advisors/list/prebody.php', $this->data); 
        $this->load->view('member/advisors/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function detail($user_id = "")
    {
        $add_mode = !is_numeric($user_id);
        $this->data["user"] = false;
        
        if($add_mode)
        {
            $this->data["meta_title"] = "Add New Advisor";    
        }
        else
        {
            // Load the user object
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if((!$user) || ($user->created_by_user_id != $this->user_id))
            {
                redirect("/advisors");    
            }
            
            $this->data["user"] = $user; 
            
            // Load the user statistics
            $this->data["stats"] = $this->Users_model->get_advisor_stats($user_id);
            
            $this->data["meta_title"] = "Advisor Detail";
        }
        
        $this->data["add_mode"] = $add_mode; 
        
        // Load the state options for Australia
        $this->data["states"] = $this->tools_model->get_states(1);
    
        $this->load->view('member/header', $this->data);
        $this->load->view('member/advisors/detail/prebody.php', $this->data); 
        $this->load->view('member/advisors/detail/main.php', $this->data);
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
            case "update_advisor":   // User is trying to login.
                $this->handle_update_advisor();
                break;
                
            case "load_advisors":
                $this->handle_load_advisors();
                break;
                
            case "login_as_this_user":
            	$this->handle_login_as_this_user();
            	break;
            	
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the update_advisor action
    * Send an OK status back if the user was updated/added successfully, error if not.
    */
    private function handle_load_advisors()
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
        $filters["deleted"] = 0;
        $filters["order_by"] = "company_name ASC";        
        $filters["search_term"] = $this->input->post("search_term");
        
        $extra_sql = ", get_advisor_status_count(u.user_id, 'sold') as num_sold, get_advisor_last_status_date(u.user_id, 'sold') as last_sold_date ";
        
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
        
        $users = $this->Users_model->get_list(1, $limit = PARTNERS_PER_PAGE, $page_no = $current_page, $count_all, "", $user_type = USER_TYPE_ADVISOR, $filters, $extra_sql);        
        
        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/advisors/list/list", array("users" => $users), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
                   
    /***
    * Handles the update_advisor action
    * Send an OK status back if the user was updated/added successfully, error if not.
    */
    private function handle_update_advisor()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        // Validate the form submission
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('email', 'Email Address', 'required|email');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $user_id = $this->input->post("user_id");
        $add_mode = false;
        
        if(is_numeric($user_id))
        {
            // We're updating an existing user.  Load their details.
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if((!$user) || ($user->created_by_user_id != $this->user_id))
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
        $save["first_name"] = $this->input->post("first_name");    
        $save["last_name"] = $this->input->post("last_name");
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
        
        if($add_mode)
        {
            // When adding a new record, set the username to the email
            $save["username"] = $this->input->post("email");    
            $save["user_type_id"] = USER_TYPE_ADVISOR;
            $save["salt"] = random_string("alnum", 15);
            $save["created_by_user_id"] = $this->user_id;
            $save["owner_id"] = $this->user_id;
            
            if($this->user_type_id == USER_TYPE_ADVISOR)
            {
                $save["advisor_id"] = $this->user_id;    
            }
            
            // Create a random password for the user.
            $password = random_string("alnum", 8);
            $save["password"] = hash("SHA256", $password . $save["salt"]);
        }
        
        $user_id = $this->Users_model->save($user_id, $save);
        if(!$user_id)
        {
            $this->data["message"] = "Sorry, someone went wrong whilst trying to save this advisor.";
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
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if((!$user) || ($user['created_by_user_id'] != $this->user_id))
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
}