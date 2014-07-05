<?php
die("OFFLINE");
class Xml extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views. 
	
	function Xml()
	{
		parent::__construct();
		
        // Create the data array.
        $this->data = array(); 			
	}
	
	function splash()
	{
		// Load required models
		$this->load->model('region_model');
		
		// Load all enabled regions
		$regions = $this->region_model->get_list();                   	
        $this->data["regions"] = $regions;
                    	
		$this->load->view('xml/splash', $this->data);
	}
	
	function blog($category_id)
	{
		if(($category_id == "") || (!is_numeric($category_id)))
			show_error("Missing blog category");
		
		$this->load->helper('date');	
		$this->load->model('article_model');
		$this->load->model('article_category_model');
		
		$category = $this->article_category_model->get_details($category_id);
		if(!$category)
			show_error("Invalid blog cateogry");
			
        // Get the current website id
	    $this->utilities->get_session_website_id(TRUE);
	    $website_id = $this->session->userdata("website_id"); 
	    $website_code = $this->session->userdata("website_code");
	    
	    $blog_url = base_url() . $website_code . "/page/" . strtolower($category->name);
	    $detail_url = base_url() . $website_code . "/articles/";
	    
	    if(strtolower($category->name) == "events")
	    	$detail_url .= "event/";
			
		$this->data["rss_title"] = "Rusty " . $category->name;
		$this->data["rss_url"] = $_SERVER["REQUEST_URI"];
		$this->data["rss_description"] = $category->short_description;
		$this->data["rss_language"] = "English";
		$this->data["rss_creator"] = "Rusty.com";
		$this->data["website_code"] = $website_code;
		$this->data["blog_url"] = $blog_url;
		$this->data["detail_url"] = $detail_url;

		// Load articles published in the last 60 days
		$stamp = time() - (86400 * 60);
		$date_from = date("Y-m-d", $stamp);
		$where = "created_dtm >= '" . $date_from . "' ";
		
		$articles = $this->article_model->get_list($category_id, $show_enabled_only = TRUE, $isRSS = TRUE, $order_by = "created_dtm", $order_direction = "DESC", $items_per_page = 0, $offset = 0, $where, $count_all);
		$this->data["articles"] = $articles;
		$this->load->view('xml/blog', $this->data);		
	}
}

/* End of file welcome.php */
/* Location: ./system/application/controllers/welcome.php */