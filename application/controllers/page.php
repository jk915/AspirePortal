<?php
class Page extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	private $use_cache;

	function Page()
	{
		// Invoke parent constructor
		parent::__construct();
		
		$this->load->model('menu_model');
		$this->load->model('article_model');
		$this->load->model('article_category_model');
		$this->load->model('document_model');
		$this->load->model('settings_model');
		
		// Create the data array.
		$this->data = array();	
		
		// Determine if page level caching is to be used
		$this->use_cache = false;
		$this->data["use_cache"] = $this->use_cache;
		$this->data["settings"] = $this->settings_model->get_details_array();
	}
	
	/**
	* @method index
	* @author: Andrew Chapman
	* @desc The index method simply calls the show method.
	* 
	* @param string $article_code
	*/
	function index($article_code = DEFAULT_PAGE)
	{
        die("PAGE CONTROLLER OFFLINE");
		$this->show($article_code);	
	}
	
	/**
	* @method: show
	* @author: Andrew Chapman
	* @desc: The show method shows a page with the specified article code.
	* If no article code is given, the default page is loaded.   The default page is defined in config/constants
	* 
	* @param mixed $page_code - The page code of the page to load.
	*/
	function show($article_code = DEFAULT_PAGE, $param = "")
	{    
        // Load the article object from the cache if possible.
        $start = microtime();
        
        $cache_key = "PAGE_" . $article_code;
        
		if(($this->use_cache) && (cache_exists($cache_key)))
		{
			$article = unserialize(cache_read($cache_key));
		}
		else
		{        
			// Load the article from the database.
			$article = $this->article_model->get_details($article_code, true);
			
			if($this->use_cache)
			{
				cache_write($cache_key, serialize($article));
			}
		}
     
        // If the page could not be loaded, show an error.          
		if(!$article)
		{
			show_error("The specified page could not be loaded");
			
			// The page could not be loaded. Redirect the user to the page-not-found page.
			redirect("/page/page-not-found");
			exit();
		}
        		
		// If the page is disabled, redirect to the front page.
		if(!$article->enabled)
		{  
   			show_error("Sorry, this page is not available for viewing at this time");
		}	
		
		// Load the privacy policy page
        // $this->data["privacypolicy"] = $this->article_model->get_details(PRIVACY_PAGEID);
        
        $this->data["meta_keywords"] = htmlspecialchars($article->meta_keywords);
		$this->data["meta_description"] = htmlspecialchars($article->meta_description);
		$this->data["meta_title"] = htmlspecialchars($article->meta_title);
        
        // Make sure the page/article contents has had any [[ ]] tags replaced with appropriate content.
        $article->short_description = $this->utilities->replaceTags($this, $article->short_description , "fullwidth");
		$article->content = $this->utilities->replaceTags($this, $article->content , "fullwidth");      
		
		// Check agent logged
		$agent = $this->session->userdata("agent_id");
		if (!$agent && $article->agent_login == 1) {
			redirect('');
		}
		
		$this->data["article"] = $article;
		$this->data["param"] = $param; 
		
		
		// Load the article category if possible
		$category = false;
		if($article->category_id > 0)
		{
			$category = $this->article_category_model->get_details($article->category_id);
		}
		
		$this->data["category"] = $category;
		
		$agent_id = $this->session->userdata("agent_id");
		if ($agent_id) {
            /*************** GET TOP MENU HTML *****************/
    		$cache_key = "LOGGEDMENU";
    		
    		if(($this->use_cache) && (cache_exists($cache_key)))
    		{
    			$menuHTML = cache_read($cache_key);				
    		}
    		else
    		{
    			// Dynamically load the cache entry
    			$menuHTML = $this->menu_model->buildMainMenuHTML(MENU_LOGGEDIN);
    			
    			if($this->use_cache)
    			{
    				cache_write($cache_key, $menuHTML);
    			}
    		}
    		$this->data["menuHTML"] = $menuHTML;
		} else {
		    /*************** GET TOP MENU HTML *****************/
    		$cache_key = "MAINMENU";
    		
    		if(($this->use_cache) && (cache_exists($cache_key)))
    		{
    			$menuHTML = cache_read($cache_key);				
    		}
    		else
    		{
    			// Dynamically load the cache entry
    			$menuHTML = $this->menu_model->buildMainMenuHTML(MENU_MAIN);
    			
    			if($this->use_cache)
    			{
    				cache_write($cache_key, $menuHTML);
    			}
    		}
    		$this->data["menuHTML"] = $menuHTML;
		}
		
		/*************** GET FOOTER MENU HTML *****************/    
		$cache_key = "FOOTERMENU";
		
		if(($this->use_cache) && (cache_exists($cache_key)))
		{
			$menuHTML = cache_read($cache_key);				
		}
		else
		{
			// Dynamically load the cache entry
			$menuHTML = $this->menu_model->buildFooterMenuHTML(MENU_FOOTER);
			
			if($this->use_cache)
			{
				cache_write($cache_key, $menuHTML);
			}
		}
		$this->data["footerMenuHTML"] = $menuHTML;	
		
		// Load the services list - shown in the footer
//		$this->data["services"] = $this->article_model->get_list(CATEGORY_SERVICES); 		
		
		/***************** FOOTER BLOCKS *********************/
//		$cache_key = "BLOCK_copyright";
//		
//		if(($this->use_cache) && (cache_exists($cache_key)))
//		{
//			$blockHTML = cache_read($cache_key);				
//		}
//		else
//		{
//			// Dynamically load the cache entry
//			$block = $this->block_model->get_details(BLOCK_COPYRIGHT); 
//			$blockHTML = strip_tags($this->utilities->replaceTags($this, $block->block_content, $hint = ""));
//			
//			if($this->use_cache)
//			{
//				cache_write($cache_key, $blockHTML);
//			}
//		}		
//  		$this->data["footer_copyright"] = $blockHTML;
  		
  		/* SIGNUP FORM */
//		$cache_key = "BLOCK_subscribe";
//		
//		if(($this->use_cache) && (cache_exists($cache_key)))
//		{
//			$blockHTML = cache_read($cache_key);				
//		}
//		else
//		{
//			// Dynamically load the cache entry
//			$block = $this->block_model->get_details(BLOCK_SUBSCRIBE); 
//			$blockHTML = $this->utilities->replaceTags($this, $block->block_content, $hint = "");
//			
//			if($this->use_cache)
//			{
//				cache_write($cache_key, $blockHTML);
//			}
//		}		
//  		$this->data["footer_subscribe"] = $blockHTML;  		
		
		// If there is a flash data message, pass it through.
		$this->data["message"] = $this->session->flashdata('message');	
		
		// If a function exists with the same name as the article code, invoke it.
		// Eg. The home page might have a code of "home", so if there is helper function 
		// called "home" it will be invoked.
        if(function_exists($article->article_code))
        {
            $function_name = $article->article_code;
            $function_name($this);
        }
        
		// Check to see if a custom view has been defined for this article.
		// If it has, use that view instead of the normal article views.
		if($article->view != "")
		{         
			// Check to see if there is a page helper function defined for this view.
			// If there is, run it.
        	if(function_exists($article->view))
			{
				$function_name = $article->view;
				$function_name($this);		
			}
			
    		// Load views	        
    		$this->load->view('header', $this->data);
            $this->load->view('page/prebody_' . $article->view, $this->data); 
            $this->load->view('page/main_' . $article->view, $this->data);            
		}
		else
		{		
    		// Load views	        
    		$this->load->view('header', $this->data);
    		
			// See if a view has been defined for articles in this category.
			if($this->data["category"]->view != "")
			{
				// Yes there is a view, use it.
				$this->load->view('page/prebody_' . $this->data["category"]->view, $this->data); 
				$this->load->view('page/main_' . $this->data["category"]->view, $this->data);				
			}
			else
			{
				// There is no article view defined.  Just include the standard prebody and main views.
				$this->load->view('page/prebody', $this->data); 
				$this->load->view('page/main', $this->data);	
			}
		}
		
		$this->load->view('pre_footer', $this->data); 
		$this->load->view('footer', $this->data); 
		
		$finish = microtime();
		$time = $finish - $start;
	}
	
}

/* End of file page.php */
/* Location: ./system/application/controllers/page.php */
