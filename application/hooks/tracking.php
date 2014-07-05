<?php if  (!defined('BASEPATH')) exit('No direct script access allowed');

/***
* The Tracking class loads the google analytics tracking code for the current website if possible.
* If it is successful, the controllers data array will contain a tracking element that can be used
* in the appropriate view. 
*/
class Tracking extends Controller  
{      
	var $CI;

	function Tracking()
	{
		$this->CI = &get_instance(); //grab a reference to the controller
	}

	/* check */         
	function get_tracking()
	{ 
		$controller_name = $this->CI->uri->segment(1);
		$function_name = $this->CI->uri->segment(2);
        $preview = $this->CI->session->flashdata('preview');
        
        // Ignore tracking hook on admin controllers or when admin press "preview" on admin_frontpage or admin_collective
		if(stristr($controller_name, "admin_") || ($preview == 1))
			return true;
			
        $this->CI->utilities->get_session_website_id(TRUE);
        $website_id = $this->CI->session->userdata("website_id"); 
        $website_code = $this->CI->session->userdata("website_code"); 
        
        if($website_id == "")
        	return true;
        	
        
		$website = $this->CI->website_model->get_details($website_id);
		if(!$website)
			return true;

        
        if(isset($this->CI->data))
        {
			$this->CI->data["tracking"] = $website->tracking;
			$this->CI->data["obj_website"] = $website;
        }   
	} 
}