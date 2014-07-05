<?php if  (!defined('BASEPATH')) exit('No direct script access allowed');

class Check_user extends CI_Controller
{      
	private $CI;

	function __construct()
	{
		$this->CI = &get_instance(); //grab a reference to the controller
		
		$this->CI->utilities->set_admin_website_id();
	}

	/* check */         
	function check()
	{ 
		// If there's not enough segments in the URL then there's nothing to do
		$num_segments = $this->CI->uri->total_segments();
		
		if($num_segments < 2)
			return true;     
			
		// Scan the segments for the "admin" zone
		$seg_no = 1;
		$found = false;
		
		while((!$found) && ($seg_no <= $num_segments))
		{
			if($this->CI->uri->segment($seg_no) == "admin")
			{
				$found = true;
			}
			else
			{
				$seg_no++;
			}
		}
        
		
		// Check if this is NOT an admin controller
		if(!$found)
		{
			// Nothing to do.
			return true;	
		}
        
		// Get the controller name from the URI (will fall 1 position after the admin zone designator)
		$controller_name = strtolower($this->CI->uri->segment($seg_no + 1)); 
		
		// All users can see the login, logout and preview controllers
		$bypass_controllers = array("login", "logout", "preview");

		if(in_array($controller_name, $bypass_controllers))
			return true;	

        $fetch = $this->CI->input->post("sifrFetch");
        if($fetch) {
            return true;
        }	
		
		// Get the id of the logged in user
		$user_id = $this->CI->login_model->getSessionData("id");
		$user_type_id = $this->CI->login_model->getSessionData("user_type_id");

		if((!$user_id > 0) || (!$user_type_id > 0))
		{
			// We have no user id in the session, redirect to the login screen.	
			redirect("/admin/login");
			exit();
		}

		// Load the modules that this user has permission to view.
		$this->CI->data["modules"] = $this->CI->modules_model->get_user_modules($user_type_id);
		
		if(!$this->CI->data["modules"])
		{
			//  This user has access to NO modules
			show_error("Sorry, your user type does not have permission to view any CMS modules");
		}
		
		// Assign the user type and user id to the data array so all admin controllers can access them.
		$this->CI->data["user_type_id"] = $user_type_id;
		$this->CI->data["user_id"] = $user_id;
		
		// Make sure the user has permission to be viewing the current controller
		$found = false;
		
		// All users can see the menu controller
		if($controller_name == "menu")
			return true;
			
		foreach($this->CI->data["modules"]->result() as $module)
		{
			if($module->controller == $controller_name)
			{
				$found = true;
				break;
			}
		}
		
		if(!$found)
		{
			// Redirect back to the admin menu
			redirect("/admin/menu");
			exit();				
		}

		return true;
	}
}
