<?php if  (!defined('BASEPATH')) exit('No direct script access allowed');

class Settings
{      
	private $CI;

	function __construct()
	{
		$this->CI = &get_instance(); //grab a reference to the controller
	}
      
	function load()
	{ 
		// Load the owner details settings into an array
		$this->CI->load->model("settings_model");
    	$settings = $this->CI->settings_model->get_details_array("owner_details");
    	
    	// Define all settings
    	foreach($settings as $key => $val)
    	{
			define("OWNERDETAILS_" . $key, $val);
    	}

        define("CONTROLLER_NAME", $this->CI->router->fetch_class()); 
        
		return true;
	}
}
