<?php
// NOTE: Check application/core/MY_Controller to see how user permissions are enforced.

// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Terms extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Media()
    {
        $this->data = array();
        
        parent::__construct();
    }
   
    function index()
    {
        $this->data["meta_title"] = "Terms &amp; Conditions";
        
        // Load the terms for this user type
        $blocks = array();
        $blocks[USER_TYPE_ADVISOR] = 8; 
        $blocks[USER_TYPE_PARTNER] = 9;
        $blocks[USER_TYPE_INVESTOR] = 10;
        $blocks[USER_TYPE_LEAD] = 11;
        
        $this->data["terms"] = $this->block_model->get_details($blocks[$this->user_type_id]);
        if(!$this->data["terms"])
        {
            show_error("Couldn't load the terms block");    
        }

        $this->load->view('member/header', $this->data);
        $this->load->view('member/terms/prebody.php', $this->data); 
        $this->load->view('member/terms/main.php', $this->data);
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
            case "agree":   // User is agreeing to terms and conditions.
                $this->handle_agree();
                break;              
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the agree action
    * Sets the session variable to say that the user has agreed to the terms and conditions.
    */
    private function handle_agree()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('agree_to_terms', 'Agree To Terms', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }

        $this->session->set_userdata("agreed_to_terms", true);
        
        $this->data["status"] = "OK";
        
        send($this->data);    
    }
}