<?php
class Login extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Login()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();
        
        $this->load->model("Users_model");
        $this->load->model('menu_model');
		$this->load->model('email_model');
		$this->load->model('log_model');
    }
   
    function index()
    {
        $this->data["meta_title"] = "Member Login";
        
        $pro_sub_menu = $this->menu_model->get_menu_list('Properties Sub Menu', false, true);

        $sub_menus = array();    
        $i = 0;
        foreach($pro_sub_menu->result() as $row)
        {
            $title = $row->menu_item_name;
            $title = str_replace("&", "&amp;", $title); 	// If the user has entered any ambersands in the title, make them valid XHTML
			$menu_url = $row->link;
			$css_id = $row->class; 
            $category_id = $row->category_id;       
                 
            $sub_menus[$i]['link'] = $menu_url;
            $sub_menus[$i]['title'] = $title;
            $sub_menus[$i]['class'] = $css_id;
            $i++;
        }         
        
        $this->session->set_userdata("sub_menu", $sub_menus);        
                               
        
        // If the user is already logged in, redirect to the dashboard
        if ($this->login_model->is_logged_in("user"))
        {
            if( $this->session->userdata('user_type_id') == 6 || $this->session->userdata('user_type_id') == 7 )
			{
				redirect("/stocklist");
			}
			
			redirect("/dashboard");
        }

        
        $this->load->view('member/header', $this->data);
        
        $this->load->view('member/login/prebody.php', $this->data); 
        $this->load->view('member/login/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    public function logout()
    {
		$logged_in = '0';
		$user_id = $this->session->userdata["user_id"];
		
		$this->login_model->update_user($logged_in, $user_id);
		
		$log_id = $this->session->userdata('log_id');
		$data = array(
				'foreign_id'=>$user_id,
				'logout_date_time'=> date('Y-m-d H:i:s')
			);
			
		$this->log_model->save($log_id,$data);
		
		$this->session->sess_destroy();
		redirect("/login");    
    }
    
    /***
    * The user wants to confirm a password reset request
    * 
    * @param string $hash The new password hash
    */
    public function reset($hash = "")
    {
        // Make sure we have a valid hash value
        if (strlen($hash) < 30)
        {
            show_error("Invalid password hash value");    
        }
        
        // Find the user record with a matching hash value
        $user = $this->Users_model->get_user_with_hash($hash);
        
        if(!$user)
        {
            // No matching user account.
            $this->session->set_flashdata("user_message", "Sorry, we could not process your password reset request.  Please try again.");
            redirect("/login");    
        }
        
        // Hash value matches, update the password with the new hash.
        $data = array("password" => $user->hash, "hash" => '', "new_password" => '');
        $this->Users_model->save($user->user_id, $data);
        
        // Let the user know about the successful update and redirect to the login screen.
        $this->session->set_flashdata('user_message', "Thank you.  Your password was reset successfully.<br/>You may now login.");
        redirect(base_url() . "login");
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
            case "login":   // User is trying to login.
                $this->handle_login();
                break;
                
            case "register":   // User is trying to register a new account
                $this->handle_register();
                break;    
                
            case "reset_password":   // User is trying to reset their password
                $this->handle_reset_password();
                break;                          
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the user registration action
    * Send an OK status back if the registration was successful, error if not.
    */
    private function handle_register()
    {
        // Validate the form submission
        $this->load->library('form_validation');
        $this->load->model("email_model");
        $this->load->model("settings_model");
        
        $this->form_validation->set_rules('register_first_name', 'First Name', 'required');
        $this->form_validation->set_rules('register_last_name', 'Last Name', 'required');
        $this->form_validation->set_rules('register_company_name', 'Company Name', 'required');
        $this->form_validation->set_rules('register_email', 'Email Address', 'required|email');
        $this->form_validation->set_rules('register_user_type', 'Aspire Role', 'required|integer');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }     
        
        // Make sure this user doesn't already exist
        // Check to see if a user already exists with this email address
        if($this->Users_model->exists($this->input->post("register_email")))
        {
            $this->data["message"] = "Sorry, someone with that email address is already registered with The Aspire Network.";
            send($this->data);            
        } 
        
        // Ensure the registration user type is valid
        $register_user_type = intval($this->input->post("register_user_type"));
        if($register_user_type < 3)
        {
            $this->data["message"] = "Sorry, you do not have permission to perform this action.";
            send($this->data);             
        }
        

        // Create the user account
        $save = array();
        $save["enabled"] = 0;   // New user accounts must NOT be enabled - they must be approved manually.
        $save["email"] = $this->input->post("register_email");
        $save["first_name"] = $this->input->post("register_first_name");    
        $save["last_name"] = $this->input->post("register_last_name");
        $save["company_name"] = $this->input->post("register_company_name");
        $save["phone"] = $this->input->post("register_phone");

        $save["username"] = $this->input->post("register_email");    
        $save["user_type_id"] = $register_user_type;
        
        /*
        $save["salt"] = random_string("alnum", 15);
        
        $password = random_string("alnum", 8);
        $save["password"] = hash("SHA256", $password . $save["salt"]);        
        */

        $user_id = $this->Users_model->save("", $save);
        if(!$user_id)
        {
            $this->data["message"] = "Sorry, something went wrong whilst trying to create your account.";
            send($this->data);             
        }
        
        // Load a list of admin contacts with contact notification turned on.
        $bcc = $this->settings_model->get_contact_notification_recipients();
		
        // Send the welcome email to the user, bcc to admins. 
        $email_data = array();
        $email_data["first_name"] = $this->input->post("register_first_name");
        $email_data["email"] = $this->input->post("register_email");
        
        $this->email_model->send_email($this->input->post("register_email"), "new_user_registration", $email_data,  $attach = "", $bcc);                

        // Send the OK back to the client
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        send($this->data);
    }    
    
    /***
    * Handles the user login action
    * Send an OK status back if the login was successful, error if not.
    */
    private function handle_login()
    {
        // Validate the form submission
        $this->load->library('form_validation');
        
        $this->form_validation->set_rules('email', 'Email Address', 'required|email');
        $this->form_validation->set_rules('password', 'Password', 'required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }  


        // Attempt to login
        $username = $this->input->post("email");
        $password = $this->input->post("password");
		
		if(!$this->session->userdata('login_attempt'))
		{
			$session_data = array();
			$session_data = array(
						'login_attempt' => 0
			);
            
			$this->session->set_userdata($session_data);
		}
	
		
		$block_until = $this->login_model->check_blocked_username($username);
		$current_time = date('Y-m-d H:i:s');
		
		if((!empty($block_until)) && ($current_time < $block_until )) {
		
			$diff = abs(strtotime($block_until) - strtotime($current_time));
			$diff = $diff/60;
			$total = sprintf("%02dh %02dm", floor($diff/60), $diff%60);
			
			$this->data["message"]="Your account is currently blocked due to too many incorrect login attempts.  Please try logging in again after the next $total. ";
			send($this->data);
		
		} else {
            /*
			$check_user = $this->login_model->user_logged($username);
			
			if($check_user == '1')
			{
				$user_data = $this->users_model->get_user_id($username);
				
				$user_id = $user_data[0]->user_id;
				$user_type_id = $user_data[0]->user_type_id;
				$first_name = $user_data[0]->first_name;
				$admin_mails = $this->users_model->get_email_notification_admins();
				$email_data = array();
				if($user_type_id != '1' || $user_type_id != '2' || $user_type_id != '3')
				{
					$advisor = $this->users_model->get_user_advisor($user_id, $user_type_id);
					
					$adv_firstname = $advisor[0]->first_name;
					$adv_lastname = $advisor[0]->last_name;
										
					$adv_email = $advisor[0]->adv_email;
					
					if($advisor)
					{	
						$email_data["adv_firstname"] = $adv_firstname;
						$email_data["adv_lastname"] = $adv_lastname;
						$bcc = array(
								'adv_email' => $adv_email
						);
                        
                        if($admin_mails) {
						    foreach($admin_mails as $admin_mail)
						    {
							    $admin_mail = $admin_mail->email;
							    array_push($bcc, $admin_mail);
						    }
                        }
					}
				}	
				else
				{
					$bcc = array();
                    if($admin_mails) {
					    foreach($admin_mails as $admin_mail)
					    {
						    $admin_mail = $admin_mail->email;
						    array_push($bcc, $admin_mail);
					    }	
                    }
				}
				
				
				$email_data["first_name"] = $first_name;
				$email_data["email"] = $username;

				$this->email_model->send_email($username, "multiple_user_disability", $email_data, $attach = "", $bcc); 
				
				$this->login_model->disable_user($username);
				$this->data["message"]="Sorry, multiple concurrent logins have been detected on your account.  We have disabled your account - please contact your Advisor.";
				send($this->data);
				
			}
            */
			
			$user = $this->login_model->check_username_password($username, $password);
			if(!$user)
			{
				$login_attempt = $this->session->userdata('login_attempt');
				$login_attempt++;
				$login_attempt = $this->session->set_userdata('login_attempt',$login_attempt);
				$final_login_attempt = $this->session->userdata('login_attempt');
                
				if(($block_until !== false) && ($final_login_attempt >= 3)) {
				
					$this->login_model->lockUser($username);
					$this->session->sess_destroy();
					$this->data["message"]="You have reached the maximum number of incorrect login attempts. You will be able to login again in 4 hours time. ";
					send($this->data);
				}
				
				// By Mayur - TasksEveryday
				
				// The login failed.
				$this->data["message"]="Sorry your login failed.  Please check your username and password.";
				send($this->data);
			}
        
        // Set session data
        $this->login_model->setSessionData($user);

		//Set user logged in to true
	
			$user_id = $user['user_id'];
			
			$logged_in = '1';
			$this->login_model->update_user($logged_in,$user_id);
			
			$data = array(
				'foreign_id'=>$user_id,
				'login_date_time'=> date('Y-m-d H:i:s')
			);
			
			$this->log_model->save("",$data);
			$log_id	= $this->db->insert_id();
			
			$this->session->set_userdata('log_id', $log_id);
	
        // Send the OK back to the client
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        if($user["user_type_id"] == USER_TYPE_SUPPLIER) {
            $this->data["redirect_url"] = "stocklist";    
        } else {
            $this->data["redirect_url"] = "dashboard";    
        }
        
        send($this->data);
    }
	}
    
    /***
    * Handles the password reset action
    * Send an OK status back if the reset request was successful, error if not.
    */
    private function handle_reset_password()
    {
        // Validate the form submission
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        $this->form_validation->set_rules('reset_email', 'Email Address', 'required|email');
        $this->form_validation->set_rules('reset_password', 'New Password', 'required');
        $this->form_validation->set_rules('reset_password_confirm', 'New Password', 'required|matches[reset_password]');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }     
        
        // Find the users account
        $username = $this->input->post("reset_email");
        
        $user = $this->Users_model->get_details($username, true);
        if(!$user)
        {
            $this->data["message"] = "Sorry, there is no user account with that email address in our system.  Please try again.";
            send($this->data);            
        }
        
        // Update the users account with the hash of the new password.
        $password = $this->input->post("reset_password");
        $hash = hash("SHA256", $password . $user->salt);
        
        $user_id = $this->Users_model->save($user->user_id, array("hash" => $hash));
        if(!$user_id)
        {
            send($this->data);    
        }
        
        // Send the email advising of the password reset
        $email_data = array();
        $email_data["first_name"] = $user->first_name;
        $email_data["new_password"] = $password;
        $email_data["link_reset"] = base_url() . "login/reset/" . $hash;
        
        $this->email_model->send_email($username, "reset_password", $email_data);
        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        send($this->data);
    }
}