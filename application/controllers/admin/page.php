<?php
class Page extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	
	function __construct()
	{
		parent::__construct();
		
		// Create the data array.
		$this->data = array();			
		
		// Load models etc
		$this->load->model("Page_model");
		
	}
	
	/**
	* @method index
	* @author: Andrew Chapman
	* @desc The index method simply calls the show method.
	* 
	* @param mixed $page_code
	*/
	function index($page_code = DEFAULT_PAGE)
	{
		$this->show($page_code);	
	}
	
	/**
	* @method: show
	* @author: Andrew Chapman
	* @desc: The show method shows a page with the specified page code.
	* If no page code is given, the default page is loaded. 
	* 
	* @param mixed $page_code - The page code of the page to load.
	*/
	function show($page_code = DEFAULT_PAGE, $param = "")
	{
		// Load the page
		$page = $this->Page_model->get_details($page_code);
		if(!$page)
		{
			// The page could not be loaded.  Report and log the error.
			$this->Tools_model->report_error("Sorry, the page could not be loaded.", "Page/show - the page with a code of '$page_code' could not be loaded");
			return;			
		}
		
		// If the page is disabled, redirect to the front page.
		if(!$page->enabled)
		{
			header("Location: " . site_url());
			exit;
		}		
		/*
		// If the page is not available to everyone, check for the appropriate permission level
		if($page->user_types_all == 0)
		{
			if(!$this->utilities->is_logged_in($this->session))
			{
				// The user does not have permission to view this page.
				//$this->Tools_model->report_error("Sorry, this page is for registered users only.  <a href=\"" . site_url() . "/user/profile\">Click here</a> to register now.", "Page/show - the user did not have permission to view the page with a code of '$page_code'");
				// return
				header("Location: " . site_url() . "/user/login");
				exit;
			}	
			else if(!$this->Page_model->check_user_permission($page->page_id, $this->session->userdata("user_type_id")))
			{
				//$this->Tools_model->report_error("Sorry, you do not have permission to view this page.", "Page/show - the logged in user did not have permission to view the page with a code of '$page_code'");
				//return;	
				header("Location: " . site_url() . "/user/login");
				exit;							
			}		
		}
		*/
        
		$this->data["page_code"] = $page_code; 	
		$this->data["page"] = $page;
		$this->data["param"] = $param; 
		$this->data["meta_keywords"] = $page->meta_keywords;
		$this->data["meta_description"] = $page->meta_description;
		$this->data["meta_title"] = $page->meta_title;
		$this->data["message"] = $this->session->flashdata('message');		
		$this->load->view('header', $this->data);
		
		// Check to see if a custom view has been defined for this page.
		// If it has, use that view instead of the normal page views.
		if($page->view != "")
		{         
			// Check to see if there is a page helper function defined for this view.
			// If there is, run it.
			if(function_exists($page->view))
			{
				$function_name = $page->view;
				$function_name($this);		
			}
			
			$this->load->view('page/prebody_' . $page->view, $this->data); 
			$this->load->view('page/main_' . $page->view, $this->data);
		}
		else
		{		
			$this->load->view('page/prebody', $this->data); 
			$this->load->view('page/main', $this->data);	
		}
		
		$this->load->view('pre_footer', $this->data); 
		$this->load->view('footer', $this->data); 
	}	
}

/* End of file page.php */
/* Location: ./system/application/controllers/page.php */
?>