<?php
die("OFFLINE");
/***
* Articles Controller
* Myndie CMS
* @author: SIMB Pty Ltd 2009-2010
* 
*/
class Articles extends CI_Controller 
{
   // Define variables
   public $data;    // Array to hold data to pass to pages.  
   public $website_code; 
   
	/***
	* @desc The Articles constructor checks that the user has logged in
	* and loads any needed views, libraries, etc.
	*/
	function Articles()
	{
		parent::__construct();

		global $website;	// Read the website code from the globals
		$this->website_code = $website;      

		// Create the data array.
		$this->data = array();

		// Load models etc
		$this->load->model("article_model");
		$this->load->model("article_category_model"); 
		$this->load->model("menu_model");
		$this->load->model("blocks_model");
		$this->load->model("product_model");
		$this->load->model("document_model");
		$this->load->library("Image");
		$this->load->model("region_model");
	} 
	
	/***
	* Shows a blog article
	* @param string $article_code - The article to load
	*/	
	function blog($article_code = "")
	{
		$this->load->model("article_posts_model");
		
		$article = $this->article_model->get_details($article_code, true); 

		if(!$article)
		{
			// The page could not be loaded. Redirect the user to the page-not-found page.			
			redirect("/page/page-not-found");
			exit();
						
			//$this->tools_model->report_error("Sorry, that article could not be found", "The article with code $article_code could not be loaded");
			//exit();
		}

		// Load any posts/comments for this article.
		//$this->data["posts"] = $this->article_posts_model->get_list($article->article_id, POSTS_PER_PAGE, 1, false, "created_dtm ASC", $count_all);
		$this->data["posts"] = $this->article_posts_model->get_list($article->article_id, "", 1, false, "created_dtm ASC", $count_all);
		$this->data["num_posts"] = $count_all;  
		
		$category = $this->article_category_model->get_details($article->category_id);
		
		$this->data["nav_main"] = $this->menu_model->get_menu_html_extended(1, 11, false, false, -1, 'blog');
		
        // Load the article.
 		$this->article($article_code);
	}
 
	function article($article_code)
	{
		// Make sure an article code was passed
		if($article_code == "")
		{
			$this->tools_model->report_error("Sorry, that article looks invalid", "Invalid article code passed: $article_code");
			exit();
		}
		
		$article = $this->article_model->get_details($article_code, true); 
		if(!$article)
		{
			$this->tools_model->report_error("Sorry, that article could not be found", "The article with code $article_code could not be loaded");
			exit();
		}
		
        $category_id = $article->category_id; 	
		// Load the article category
		$category = $this->article_category_model->get_details($category_id);
		if(!$category)
		{
			$this->tools_model->report_error("Sorry, the article category could not be loaded", "The article category for article with code $article_code could not be loaded");
			exit();
		}
        
        $this->data["parent_category_code"] = "";
        
        if($category && $category->parent_id != -1)
        {
            $parent_category = $this->article_category_model->get_details($category->parent_id);
            $this->data["parent_category_code"] = ($parent_category) ? $parent_category->category_code : "";
        }
        
		
		// Substitute video tags etc.
		$article->content = $this->utilities->replaceTags($this, $article->content, "fullwidth");
		
		$this->data["article"] = $article;
		$this->data["meta_keywords"] = $article->meta_keywords;
		$this->data["meta_description"] = $article->meta_description;
		$this->data["meta_title"] = $article->meta_title;		
		$this->data["meta_robots"] = $article->meta_robots;
		$this->data["category"] = $category;
		
		$this->data["blocks"] = $this->blocks_model->get_assigned_blocks($article->article_id, $assignment_type = "article", $position = "left");

		$this->load->view('header', $this->data);

		// See if there a view override
		$view = $article->view;
		/*if($view == "")
			//$view = $category->view;
			$view = $this->data['view'];*/
		
		if($view != "")
		{
			// Articles in this category have a view override.  Use it.
			$this->load->view('article/prebody_' . $view, $this->data); 
			$this->load->view('article/main_' . $view, $this->data);			
		}
		else
		{
			// Use the default article view
			$this->load->view('article/prebody', $this->data); 
			$this->load->view('article/main', $this->data); 
		}

		$this->load->view('pre_footer', $this->data); 
		$this->load->view('footer', $this->data); 			
	}
	
	function generate_archive_stats()
    {
        $this->article_model->calc_post_stats();
    } 
	

	function archives(  $month = '', $year = '')
	{
		
		if( $month == '' || $year == '' )
		{
			$this->tools_model->report_error("Sorry, some parameters are missing.", "The archives could not be loaded");
        	return;
		}
		
		$category_code = BLOG_ARTICLE_CATEGORY_CODE;
   		$article_category = $this->article_category_model->get_details( $category_code );
   		
		$ids = array($article_category->category_id);		
		$sub_categories = $this->article_model->get_subcategories( $article_category->category_id );
		
		if( $sub_categories )
		{
			foreach ( $sub_categories as $cat )
			{
				$ids[] = $cat->category_id;
			}
		}
		
		$articles = $this->article_model->get_articles_from_date( $ids, $month, $year );
		$title = 'Archive for: '.date( 'F', mktime(0, 0, 0, $month, 1, 2000)).' '.$year;
		
		$this->load_blog($articles, $title);
	}
	
	function load_blog( $articles, $title )
	{
		$this->data["meta_keywords"] = '';
		$this->data["meta_description"] = '';
		$this->data["meta_title"] = '';		
		$this->data["meta_robots"] = '';
		$this->data["articles"] = $articles;
		$this->data["nav_main"] = $this->menu_model->get_menu_html_extended(1, 11, false, false, -1, 'blog');
		$this->data["title"] = $title;
		
		$this->load->view('header', $this->data);
		$this->load->view('article/prebody_blog', $this->data); 
		$this->load->view('article/main_blog', $this->data);			
		$this->load->view('pre_footer', $this->data); 
		$this->load->view('footer', $this->data);
	}
	
	function category(  $category_code = '' )
	{
		
		if( $category_code == '' )
		{
			$this->tools_model->report_error("Sorry, the category code is missing.", "The category could not be loaded, because the category code is missing.");
        	return;
		}
		
		$article_category = $this->article_category_model->get_details( $category_code );
   		$articles = $this->article_model->get_articles_from_category( $article_category->category_id );
   		$title = 'Latest News ( '.$article_category->name.' )';
   		
		$this->load_blog($articles, $title);
	}
	
	function user(  $author = '' )
	{
		
		if( $author == '' )
		{
			$this->tools_model->report_error("Sorry, the author you tried to rich doesn't exists.", "The author doesn' exists.");
        	return;
		}
		
		$category_code = BLOG_ARTICLE_CATEGORY_CODE;
		$article_category = $this->article_category_model->get_details( $category_code );
		
		$ids = array($article_category->category_id);		
		$sub_categories = $this->article_model->get_subcategories( $article_category->category_id );
		
		if( $sub_categories )
		{
			foreach ( $sub_categories as $cat )
			{
				$ids[] = $cat->category_id;
			}
		}
   		
		$articles = $this->article_model->get_articles_from_author( $ids, $author );
		$title = 'Post by '.$author;
		
		$this->load_blog($articles, $title);
	}
	
	function ajaxwork()
	{
		// FUDGE DATA
		//$_POST["type"] = 1;
		//$_POST["article_id"] = 28;
		//$_POST["page_no"] = 2;
		
		$type = $this->input->post("type");
		if(($type == "") || (!is_numeric($type)))
			die();
			
		$return_data = array();  
			
		switch($type)
		{
			case 1: // Load article posts with a particular page number offset.
				$article_id = $this->input->post("article_id");
				$page_no = $this->input->post("page_no");
				
				if(($article_id == "") || (!is_numeric($article_id)))
				{
            		$return_data["message"] = "Sorry, there are missing parameters.";
            		echo json_encode($return_data);
            		break; 						
				}
				
				if(($page_no == "") || (!is_numeric($page_no)))
				{
            		$return_data["message"] = "Sorry, there are missing parameters.";
            		echo json_encode($return_data);
            		break; 						
				}
				
				$this->load->model("article_posts_model");
				
				$posts = $this->article_posts_model->get_list($article_id, POSTS_PER_PAGE, $page_no, $notify_only = false, $order_by = "created_dtm ASC", $count_all = 0);
				if(!$posts)
				{
            		$return_data["message"] = "NO_POSTS";
            		echo json_encode($return_data);
            		break; 					
				}
				
				$view_data = array();
				$view_data["posts"] = $posts;
				$html = $this->load->view("misc/posts", $view_data, true);
				
				$return_data["message"] = "OK"; 
				$return_data["html"] = $html; 
				
				echo json_encode($return_data);
				break;
				
			case 2: // Load article posts with a particular page number offset.
				$post_id = $this->input->post("post_id");
				
				if(($post_id == "") || (!is_numeric($post_id)))
				{
					$return_data["message"] = "ERROR";	
					echo json_encode($return_data);
					break;					
				}
				
				// Make sure the user is logged in
				if (!$this->login_model->getSessionData("logged_in"))
				{
					$return_data["message"] = "ERROR";	
					echo json_encode($return_data);
					break;						
				}
				
				$this->load->model("article_posts_model");
				$update_data = array("deleted" => 1);
				$this->article_posts_model->save($post_id, $update_data);
				
				$this->article_posts_model->update_post_stats($post_id);

				$return_data["message"] = "OK";		
				echo json_encode($return_data); 
				break;		
				
			default:
            	$return_data["message"] = "Sorry, no valid type was defined";
            	echo json_encode($return_data);
            	break; 				
		}
	}
}

/* End of file articles.php */
/* Location: ./system/application/controllers/articles.php */
