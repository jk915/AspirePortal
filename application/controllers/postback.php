<?php
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Postback extends MY_Controller 
{
	private $data;        // Will be an array used to hold data to pass to the views.
	private $session_id;

	function Postback()
	{
		parent::__construct();

		// Create the data array.
		$this->data = array();
	}
    
    public function get_support_form()
    {
        $return = get_return_array();
        $return["message"] = $this->load->view("member/misc/support", array(), true);    
        $return["status"] = "OK";
        
        send($return);
    }
    
    public function submit_support_form()
    {
        $return = get_return_array();  
        
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->helper('email');
        $this->load->model('settings_model');
        $this->load->model('email_model');        
        
        // Validate the form submission
        $this->form_validation->set_rules('support_type', 'Support Type', 'required|xss_clean'); 
        $this->form_validation->set_rules('priority', 'Priority', 'required|xss_clean'); 
        $this->form_validation->set_rules('description', 'Description', 'required|xss_clean'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $return["message"] = validation_errors('- ', '\n');
            send($return);
        }
        
        // Get the user object for the user that is currently logged in
        $user = $this->users_model->get_details($this->user_id);
        if(!$user) {
            $return["message"] = "Sorry, you do not have permission to perform this action";
            send($return);    
        }
        
        $email_data = array(
            'first_name' => $user->first_name,
            'last_name' => $user->last_name,
            'user_type' => $user->type,
            'email' => $user->email,
            'phone' => $user->phone,
            'support_type' => ucwords($this->input->post("support_type")),
            'priority' => ucwords($this->input->post("priority")),
            'description' => $this->input->post("description")
        );
        
        $aBcc = array();
        $contacts = $this->settings_model->get_contacts("contact_notification = 1"); 
        if ($contacts) {
            foreach ($contacts->result() as $index=>$row)
            {
                if ($index==0) {
                    $toEmail = $row->email;
                } else {
                    $aBcc[] = $row->email;
                }
            }
        }
        
        if (!empty($toEmail)) {
            $this->email_model->send_email($toEmail, "Support Request", $email_data, '', $aBcc);
        }                
        
        
        $return["status"] = "OK";
        $return["message"] = $toEmail;
        
        send($return);        
    }
	
}
/* End of file postback.php */
/* Location: ./system/application/controllers/postback.php */