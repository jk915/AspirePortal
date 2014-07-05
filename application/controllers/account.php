<?php
// NOTE: Check application/core/MY_Controller to see how user permissions are enforced.

// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Account extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Account()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("Users_model");
    }
    
    function detail()
    {
        $this->data["meta_title"] = "My Account"; 

        // Load the user object
        $user = $this->Users_model->get_details($this->user_id);
        
        // If the user could not be loaded forbid this action.
        if(!$user)
        {
            redirect("/dashboard");    
        }
        
        $this->data["user"] = $user; 
        
        // Load the user statistics
        $this->data["stats"] = $this->Users_model->get_advisor_stats($this->user_id);
        
        // Load the state options for Australia
        $this->data["states"] = $this->tools_model->get_states(1);
    
        $this->load->view('member/header', $this->data);
        $this->load->view('member/account/detail/prebody.php', $this->data); 
        $this->load->view('member/account/detail/main.php', $this->data);
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
            case "update_account":   // User is trying to update their own account
                $this->handle_update_account();
                break;
                
            case "change_password": // Change Password
            	$this->handle_change_password();
            	break;
            	
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the update_account action
    * Send an OK status back if the user was updated/added successfully, error if not.
    */
    private function handle_update_account()
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
            $this->data["message"] = validation_errors('- ', '/n');
            send($this->data);
        }
        
        // We're updating an existing user.  Load their details.
        $user = $this->Users_model->get_details($this->user_id);
        
        // If the user could not be loaded forbid this action.
        if(!$user)
        {
            $this->data["message"] = "Sorry, you do not have permission to perform this action.";
            send($this->data);     
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
        
        $user_id = $this->Users_model->save($this->user_id, $save);
        if(!$user_id)
        {
            $this->data["message"] = "Sorry, someone went wrong whilst trying to save your details.";
            send($this->data);             
        }
        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
                
        send($this->data);
    }
    
    /***
    * Handles the change_password action
    * Send an OK status back if the user was changed successfully, error if not.
    */
    private function handle_change_password()
    {
    	// Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('new_password', 'New Password', 'required');
        $this->form_validation->set_rules('re_new_password', 'New Password Repeat', 'required');
        $this->form_validation->set_rules('re_new_password', 'New Password Repeat', 'trim|required|matches[new_password]');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        // We're updating an existing user.  Load their details.
        $user = $this->Users_model->get_details($this->user_id);
        
        // If the user could not be loaded forbid this action.
        if(!$user)
        {
            $this->data["message"] = "Sorry, you do not have permission to perform this action.";
            send($this->data);     
        }
        
        $new_password = $this->input->post('new_password');
        
        $this->users_model->change_password($this->user_id,$new_password);
        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
                
        send($this->data);
    }
}