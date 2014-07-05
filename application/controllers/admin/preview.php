<?php
/**
* The preview controller allows content managers to view previews of HTML WYSIWYG editor windows
* within the context of a real page.
*/
class Preview extends CI_Controller 
{
	public $data;        // Will be an array used to hold data to pass to the views.
    
	function __construct()
	{
		parent::__construct();

		// Create the data array.
		$this->data = array();            
		$this->load->model('menu_model');

		// Check for a valid session
		if (!$this->login_model->getSessionData("logged_in"))
			redirect("login");       
	}
	
	function index()
	{
		// Get the preview html
		$this->data["html"] = $this->utilities->replaceTags($this, $this->session->userdata("preview_html"));
		//$this->data["html"] = $this->utilities->replaceTags($this, $this->session->userdata("preview_html"));
		$this->data["meta_keywords"] = "Preview Only";
		$this->data["meta_description"] = "Preview Only";
		$this->data["meta_title"] = "Preview Only";
		$this->data["nav_main"] = $this->menu_model->get_menu_html_extended(1, 11);
		
		// Load the views
		$this->load->view('header', $this->data);
		$this->load->view('preview/prebody', $this->data); 
		$this->load->view('preview/main', $this->data);
		$this->load->view('pre_footer', $this->data); 
		$this->load->view('footer', $this->data);						
	}
	
	function article()
	{
		$this->data["html"] = $this->utilities->replaceTags($this, $this->session->userdata("preview_html"));
		//$this->data["html"] = $this->utilities->replaceTags($this, $this->session->userdata("preview_html"));
		$this->data["meta_keywords"] = "Preview Only";
		$this->data["meta_description"] = "Preview Only";
		$this->data["meta_title"] = "Preview Only";
		$this->data["nav_main"] = $this->menu_model->get_menu_html_extended(1, 11);
		
		$ids = array( 1 );
		$sub_categories = $this->article_model->get_subcategories( 1 );
		
		if( $sub_categories )
		{
			foreach ( $sub_categories as $cat )
			{
				$ids[] = $cat->category_id;
			}
		}
		
		$article_id = $this->session->userdata("preview_article_id");
		$article = $this->article_model->get_details($article_id, true);
		
		$this->data["article"] = $article;
		$this->data["is_blog"] = in_array( $article->category_id, $ids );
		// Load the views
		$this->load->view('header', $this->data);
		$this->load->view('preview/prebody_article', $this->data); 
		$this->load->view('preview/main_article', $this->data);
		$this->load->view('pre_footer', $this->data); 
		$this->load->view('footer', $this->data);
	}
    
    // Handles all ajax requests within this controller
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        
        switch($type)
        {
            // Save preview data
            case 1:
            	// Get the HTML
            	$html = $this->tools_model->get_value("html", 0, "post", 0, false);
            	$article_id = $this->tools_model->get_value("article_id", 0, "post", 0, false);
            	
            	// Save it to session
            	$this->session->set_userdata("preview_html", $html);
            	$this->session->set_userdata("preview_article_id", $article_id);
            	
            	$return_data = array();
            	$return_data["message"] = "OK";
            	echo json_encode($return_data); 
            break;
        }
    }
}
?>
