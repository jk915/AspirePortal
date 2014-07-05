<?php if  (!defined('BASEPATH')) exit('No direct script access allowed');

class Check_website_select extends CI_Controller
{      
	private $CI;

	function __construct()
	{
		$this->CI = &get_instance(); //grab a reference to the controller
	}

	/* check */         
	function check()
	{ 
   		//$website_id = $this->input->post("website_id")   
	}
}
