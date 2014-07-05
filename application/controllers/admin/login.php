<?php
/***
* The login controller handles showing the user the CMS login
* form and also logging the user in after a login submission.
*/
class Login extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	
	function __construct()
	{
		// Call the parent constructor.
		parent::__construct(); 
		
		// Create the data array.
		$this->data = array();	
		
		// Load additional models etc		
		$this->load->model("menu_model");
	}
    
    function index()
    {
		// Define meta tags for the views
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "CMS Login";
      
		//if we are already logged in redirect to home page
		if ($this->login_model->is_logged_in("cms_user"))
		{
			redirect("/admin/menu");
			exit();
		}  
        
		$this->data["message"] = "";
		
		// handle a login post
		$this->_handlePost();                   
        
        // Load teh relevant views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/login/prebody', $this->data); 
		$this->load->view('admin/login/main', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data); 
    }     
	
	function _handlePost()
	{
		// Has the user submitted the login form back
		if ($this->tools_model->isPost())
		{
			// Read in the username and password.
			$username = $this->tools_model->get_value("username", "", "post", 50, true);
			$password = $this->tools_model->get_value("password", "", "post", 50, true);
			
            $ok = true;  
            
            if($ok)
            {
            	// If the username and password fields aren't blank, check the login credentials
			    if($username != "" && $password != "")
			    {
				    $checkUser = $this->login_model->check_username_password($username, $password);
				    
				    if(!$checkUser)
				    {
				    	// The login was NOT successful.
					    $this->data["message"] =" Invalid username or password.";
					}
				    else
				    {
	                 	// Make sure the user is either of type ADMIN or EDITOR
	                 	if($checkUser["user_type_id"] != USER_TYPE_ADMIN)
	                 	{
							$this->data["message"] = "Sorry, you are not allowed to access the administration zone.";	
	                 	}
	                 	else
	                 	{
	                 		// The login was successful.  Write the users details
	                 		// to the session.
		                    $userData = array(
									'id' => $checkUser["user_id"],
									'username' => $checkUser["username"],
									'firstname' => $checkUser["first_name"],
									'lastname' => $checkUser["last_name"],
									'logged_in' => TRUE,
									'phone' => $checkUser["phone"],
		                            'user_type_id' => $checkUser["user_type_id"]
									);
							
							$this->session->set_userdata("cms_user",$userData);
							
							// Update last login date and ip
							$this->login_model->logLogin($checkUser["user_id"], $this->input->ip_address());						
							
							redirect("/admin/menu");
							exit();
						}		
					}		
			    }
			    else
			    {
				    $this->data["message"]="Invalid username or password.";
			    }
            }
		}
	}
}