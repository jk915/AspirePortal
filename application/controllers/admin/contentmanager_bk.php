<?php
class Contentmanager extends CI_Controller 
{
	public $data;        // Will be an array used to hold data to pass to the views.
	private $records_per_page = ITEMS_PER_PAGE;

	function __construct()
	{
		parent::__construct();
        
		// Create the data array.
		$this->data = array();            

		// Load models etc
		$this->load->model("article_category_model");
		$this->load->model("blocks_model");      
		$this->load->model("history_model");         
		$this->load->model("document_model");
		$this->load->model("product_model");
		$this->load->model("area_model");
		$this->load->model("article_area_model");
        $this->load->model("article_states_model");
        $this->load->model("article_usertypes_model");
		
		// Remove all items from the cache to ensure any content changes are reflected on the site
		cache_flush();                   
	}
   
	/***
	* @method index
	* @desc The index method shows the articles listing.
	* 
	*/
	function index($website_id = "")
	{
		$website_id = '1';//(is_numeric($website_id) ? $website_id : $this->utilities->get_session_website_id());
		
        // Get the user credentials.    
        $user_id = $this->login_model->getSessionData("id");
        $user_type_id = $this->login_model->getSessionData("user_type_id");	

        // Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "Content Manager";
		$this->data["page_heading"] = "Content Manager";
		$this->data["name"] = $this->login_model->getSessionData("firstname");
		$this->data['message'] = "";

		// Load all available categories
		// if($website_id != "")
		// {
			$categories = $this->article_model->get_article_categories(-1, '1', -1);
		// }
		// else
			// $categories = false;
		
		$this->data['website_id'] = $website_id;
		$this->data['category_id'] = -1; //default category
		$this->data["categories"] = $categories;
		$this->data["articles"] = $this->article_model->get_list($this->data["category_id"], FALSE, false, "article_order", "ASC", 0, 0, "", $count_all = 0);
		 
		// Load Views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/contentmanager/prebody', $this->data); 
		$this->load->view('admin/contentmanager/main', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data); 
	}
   
    /***
    * @method add
    * Handles showing an article page to the user.
    * 
    * @param bool $category_id
    */
	function add($category_id = -1)
	{
		$this->data["article_id"] = "";

		$category_id = (is_numeric($category_id)) ? $category_id : -1;

		$website_id = $this->utilities->get_session_website_id(); 

		$this->data["categories"] = $this->article_model->get_article_categories(-1, $website_id);
		$this->data["websites"] = $this->website_model->get_list(array());
		$this->data["category_id"] = $category_id;

		//load article page
		$this->_load_article_page();
	}
    
    function new_article($category_id = -1)
    {
    	$this->article("",$category_id);
    }
    
	/**
	* @method: article
	* @desc: The article method shows an article with the specified article id.
	* If no id code is given, it means the user is creating a new article
	* 
	* @param mixed $article_id - The article id of the page to load.
	*/
	function article($article_id = "", $category_id = -1)
	{
	    $this->load->helper('form');
		$website_id = $this->utilities->get_session_website_id();

		$this->data["page_heading"] = "Article Details";
		$this->data["message"] = "";
		$this->data["article_id"] = $article_id;
		$this->data["category_id"] = $category_id;
		$this->data["areas"] = $this->area_model->get_list(1,'','',$count_all);
		$this->data["websites"] = $this->website_model->get_list(array());
		
        $this->data["parent_category_code"] = "";
        
		// Check if the user has submitted the form back.
		$postback = $this->tools_model->isPost();

		if ($postback)
		{
			// The user has submitted the form back.  Where they adding a new article?
			$add_new = ($article_id == "");

			// Handle the post and get the id of the article if it's new.
			$article_id = $this->_handlePost($article_id, $missing_fields);

			if(!$article_id)
			{
				// An error occured : The article could not be loaded.  Report and log the error.
				$this->error_model->report_error("Sorry, the article could not be saved.", "articlemanager/article - the article with an id of '$article_id' could not be saved");
				return;
			}
			else
			{
				// Load article details                                      
				$article = $this->article_model->get_details($article_id, FALSE);
				redirect("/admin/contentmanager/article/$article_id/" . $article->category_id);
				exit();
			}
		}
     
		if($article_id != "") //edit
		{
			// Load article details
			$article = $this->article_model->get_details($article_id);

			if(!$article)
			{
				// The page could not be loaded.  Report and log the error.
				$this->error_model->report_error("Sorry, the article could not be loaded.", "articlemanager/article - the article with an id of '$article_id' could not be loaded");
				return;            
			}
			else
			{
				//pass page details
				$this->data["article"] = $article;
				$this->data["category_id"] = $article->category_id;
			}
            
            if($category_id > 0)
            {
                $this->data["category"] = $this->article_category_model->get_details($category_id);
                
                if($this->data["category"] && $this->data["category"]->parent_id != -1)
                {
                    $parent_category = $this->article_category_model->get_details($this->data["category"]->parent_id);
                    $this->data["parent_category_code"] = ($parent_category) ? $parent_category->category_code : "";
                }
                
                if($this->data["category"] && $this->data["category"]->enable_tab_area)
                {
                    $article_areas = $this->article_area_model->get_list(array('article_id' => $article->article_id));
                    $article_area_arr = array();
                    
                    if ($article_areas) 
                    {
                        foreach ($article_areas->result() AS $article_area)
                        {
                            $article_area_arr[] = $article_area->area_id;
                        }
                    }
                    
                    $this->data['article_areas'] = $article_area_arr;  
                }              
                
                // Load in the states recordset if needed
                $this->data["states"] = false;
                
                if($this->data["category"] && $this->data["category"]->enable_tab_states)
                {
                    $this->data["states"] = $this->db->get_where("states", array("country_id" => 1));
                    
                    // Load the states that this article is associated with
                    $article_states = $this->article_states_model->get_list(array('article_id' => $article->article_id));
                    $article_states_arr = array();
                    
                    if ($article_states) 
                    {
                        foreach ($article_states->result() AS $article_state)
                        {
                            $article_states_arr[] = $article_state->state_id;
                        }
                    }
                    
                    $this->data['article_states'] = $article_states_arr;                
                }
                
                // Load in the user types if needed
                $this->data["usertypes"] = false;
                
                if($this->data["category"] && $this->data["category"]->enable_tab_usertypes)
                {
                    $this->data["usertypes"] = $this->db->get_where("user_types", array("user_type_id >" => 2));
                    
                    // Load the states that this article is associated with
                    $article_usertypes = $this->article_usertypes_model->get_list(array('article_id' => $article->article_id));
                    $article_usertypes_arr = array();
                    
                    if ($article_usertypes) 
                    {
                        foreach ($article_usertypes->result() AS $type)
                        {
                            $article_usertypes_arr[] = $type->user_type_id;
                        }
                    }
                    
                    $this->data['article_usertypes'] = $article_usertypes_arr;                
                }                
            }
            

            $this->data["categories"] = $this->article_model->get_article_categories(-1, $website_id);                

			// Get all available page blocks
			$this->data["blocks"] = $this->blocks_model->get_list('','',$count,1,1);

			// Get the blocks that are assigned to this page.
			$this->data["blocks_right"] = $this->blocks_model->get_assigned_blocks($article_id, "article", "right");
			$this->data["blocks_left"] = $this->blocks_model->get_assigned_blocks($article_id, "article", "left");
			
			$this->data["article_icon"] = $this->article_model->get_article_icon();            

			$website_articles = array();
			$website_articles_result = $this->website_model->get_website_assm($article_id, "article");

			if($website_articles_result)
			{
				foreach($website_articles_result->result() as $row)
				{
					$website_articles[] = $row->website_id;
				}
			}

			$this->data["website_articles"] = $website_articles;

			if(!is_dir(FCPATH.ARTICLE_FILES_FOLDER))
			{
				@mkdir(FCPATH.ARTICLE_FILES_FOLDER, DIR_WRITE_MODE);
			}

			if(!is_dir(FCPATH.ARTICLE_FILES_FOLDER.$article_id))
			{
				@mkdir(FCPATH.ARTICLE_FILES_FOLDER.$article_id, DIR_WRITE_MODE);
			}
		                                
			$this->data['images'] = $this->document_model->get_files( "article_image", $article_id, 'order', $count_all );
			$this->data["pages_no"] = $count_all / $this->records_per_page; 
			$this->data['count_all'] = $count_all;

		}

		if (!$postback)
		{    
			$this->data['message'] = ($article_id == "") ? "To create a new article, enter the article details below." : "You are editing the article &lsquo;<b>$article->article_title</b>&rsquo;";
		}  
      
        $this->data["cat_items"] = $this->article_model->get_articles_from_category($category_id, 0, null, "art.article_id != $article_id");
        
        // get related items
        $rs = $this->db->select("IF(article_id=$article_id,related_article_id,article_id) AS item_id", false)
                ->where("article_id = $article_id OR related_article_id = $article_id")
                ->get('articles_related')
                ->result();
        $this->data["related_items"] = array();
        foreach ( $rs as $row )
        {
            $this->data["related_items"][] = $row->item_id;
        }
      
	  	$this->_load_article_page();  
	} 
   
   
    function _load_article_page()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Article Manager";
        $this->data["robots"] = $this->utilities->get_robots();

        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/article/prebody.php', $this->data); 
        $this->load->view('admin/article/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);    
    }
    
	function category_edit($category_id)
	{
		if(($category_id == "") || (!is_numeric($category_id)))
			show_error("Invalid category ID");

		// Check for a postback
		$postback = $this->tools_model->isPost();

		if ($postback)
		{
			// The user has submitted the form, handle the post
			$category_id = $this->_handleCategoryPost($category_id);	

			// Redirect make to the article category manager
			redirect("/admin/contentmanager/category_edit/$category_id");  
			exit();      	
		}			

		$this->data["category_id"] = $category_id;

		// Load the category
		$this->data["category"] = $this->article_category_model->get_details($category_id);
		if(!$this->data["category"])
			show_error("Sorry, the category could not be loaded");
			
		// Load all available websites
		$websites = $this->website_model->get_list();
		$this->data["websites"] = $websites;

		// Get all available page blocks
	    $this->data["blocks"] = $this->blocks_model->get_list('','',$count,1,1);
		
	    // Get the blocks that are assigned to this category.
	    $this->data["blocks_right"] = $this->blocks_model->get_assigned_blocks($this->data["category_id"], "category", "right");
        $this->data["blocks_left"] = $this->blocks_model->get_assigned_blocks($this->data["category_id"], "category", "left");  				
		// Load all top level categories
		$categories = $this->article_model->get_article_categories(-1, $this->data["category"]->website_id);
		$this->data["categories"] = $categories;  

		$this->data["message"] = "You are editing the category '" . $this->data["category"]->name . "'";

		// Load resources needed for the page
		$this->data["order_by_options"] = array("article_id", "article_date", "article_order", "article_title");

		// Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "Edit Article Category";
		$this->data["robots"] = $this->utilities->get_robots();

		// Load views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/articlecategory/prebody.php', $this->data); 
		$this->load->view('admin/articlecategory/main.php', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data);
	}     
   
	function category($category_id = "")
	{
		if (!is_numeric($category_id))
		{
			$this->error_model->report_error("Sorry, the page could not be loaded.", "Contentmanager : Category - the category with a id of '$category_id' could not be loaded"); 
			exit();
		}		
		
		// Load the category that has been selected
		$category = $this->article_category_model->get_details($category_id);
		if(!$category)
			show_error("Couldn't load selected category");		
			
		$this->data['category'] = $category;
		
		//load sub categories
		$categories = $this->article_model->get_article_categories(-1, $this->data['category']->website_id, $category_id);
		        
		$this->data["website_id"] = '1';//$this->session->userdata("website_id"); 
		$this->data['categories'] = $categories;                 
		$this->data["articles"] = $this->article_model->get_list($category_id, FALSE, false, "article_order", "ASC", 0, 0, "", $count_all = 0);
		$this->data["websites"] = $this->website_model->get_list(array());                    
		$this->data['category_id'] = $category_id;
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "Content Manager";
		$this->data["page_heading"] = "Content Manager";
		$this->data['message'] = "";

		//load views         
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/contentmanager/prebody', $this->data); 
		$this->load->view('admin/contentmanager/main', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data); 
	}
   
	/***
	* @method _handlePost
	* @desc This method is fired when the user submits the article detail form
	* If the article already exists, the article is updated, otherwise a new article is created.
	* 
	* @param mixed $article_id The article being updated (if applicable)
	* @param mixed $form_values The post data
	*/
	function _handlePost($article_id, &$form_values)
	{
    	// Get the details of the logged in user
    	$first_name = $this->login_model->getSessionData("firstname");
    	$last_name = $this->login_model->getSessionData("lastname");
    	
    	// Determine the default author name (the logged in user's first name and last name)
    	$author = $first_name;
    	if(($author != "") && ($last_name != ""))
    		$author .= " " . $last_name;
    	
		// Define the update array with default values
		$data = array( 
		             "article_title"         => '',
		             //"article_heading"       => $_POST["article_title"],
		             "article_date"          => date("y-m-d"),
		             "enabled"               => '0',
		             "category_id"           => '',                     
		             "meta_title"            => $_POST["article_title"],
		             "meta_keywords"         => '',
		             "meta_description"      => '',
		             "view"                  => '',
		             "meta_robots"           => '',
		             "content"               => '',
		             "short_description"     => '',
		             "promoted"              => '0',
		             "article_order"         => '0',
		             "author"                => $author,
		             "video_id"              => '',
		             "hero_image"            => '',
                     "alt_hero_image"        => '',
		             "article_code"          => '',
		             "tags"                  => '',
		             "www"                   => '',
		             "prices_form"      => '0',
		             "wholesale_price"      => '0',
		             "number_of_bedrooms"      => '0',
		             "number_of_bathrooms"      => '0',
		             "number_of_car"      => '0',
		             "agent_login"      => '0',
		             "video_code"      => '',
		             "status"      => '',
		             "source"      => '',
		             "download_caption"      => '',
		             "is_featured"      => '0',
                     "comments"      => '',
                     "article_icon"      => ''
        );

        //-----upload background photo start-----

        $this->load->helper(array('form', 'url'));

        $article = $this->article_model->get_details($article_id);
        
        if($article)
        {
        	$_POST['hero_image'] = $article->hero_image;
		}
        	
        $config['upload_path'] = FCPATH.'article_files/' . $article_id;
        $config['allowed_types'] = 'jpg|jpeg|gif|png';
        $config['max_size']	= '0';
        $config['max_width']  = '0';
        $config['max_height']  = '0';
        $config['overwrite'] = true;

        // Make sure the upload directory exists
        if(!is_dir($config['upload_path']))
        {
        	// The upload directory does not exist, create it.
			@mkdir($config['upload_path']);
			chmod( $config['upload_path'], DIR_WRITE_MODE );
			if(!is_dir($config['upload_path']))
			{
				$this->tools_model->report_error("Sorry, the upload directory could not be created.", "Articlemanager/_handlePost - the directory '" . $config['upload_path'] . "' could not be created");
				exit();
			}
        }
        	
        $this->load->library('upload', $config);

        if ($this->upload->do_upload('hero_image'))
        {
            $image_data = $this->upload->data();
            $image_name = $image_data['orig_name'];

            $_POST['hero_image'] = ARTICLE_FILES_FOLDER. $article_id. "/" . $image_name;
            
            // Create banner, hero, thumnail versions of images.
            $this->image->create_image_set($config['upload_path'] . "/" . $image_name);
            //$this->utilities->add_to_debug($config['upload_path'] . "/" . $image_name. " was uploaded.");
        }
        //-----upload background photo end-----
        
        // Transform the article date from UK format to ISO if one was present  
        if(isset($_POST["article_date"]))
            $_POST["article_date"] = $this->utilities->uk_to_isodate($_POST["article_date"]);
        
        // Transform tags so they have a starting and ending comma
        if((isset($_POST["tags"])) && ($_POST["tags"] != ""))
			$_POST["tags"] = tags_add_commas($_POST["tags"]);	// Defined in strings_helper
        

        /*** Save Assigned AREAS ****/
        if (isset($_POST['areas']) && $article_id != '') 
        {
            $this->article_area_model->delete_by_article($article_id);
            
        	foreach ($_POST['areas'] AS $area_id)
        	{
        	    $this->article_area_model->save('',array('article_id' => $article_id, 'area_id' => $area_id));
        	}
        }
        
        /*** Save Assigned States ****/
        if (isset($_POST['states']) && $article_id != '') 
        {
            $this->article_states_model->delete_by_article($article_id);
            
            foreach ($_POST['states'] AS $state_id)
            {
                $this->article_states_model->save('', array('article_id' => $article_id, 'state_id' => $state_id));
            }
        }
        
        /*** Save Assigned User Types ****/
        if (isset($_POST['usertypes']) && $article_id != '') 
        {
            $this->article_usertypes_model->delete_by_article($article_id);
            
            foreach ($_POST['usertypes'] AS $user_type_id)
            {
                $this->article_usertypes_model->save('', array('article_id' => $article_id, 'user_type_id' => $user_type_id));
            }
        }                 
			 
        // Define the required fields for validation
        $required_fields = array("article_title", "category_id");
        $missing_fields = false; // Set to true if fields are missing

        //fill in data array from post values
        foreach($data as $key => $value)
        {
			$data[$key] = trim($this->tools_model->get_value($key,$data[$key], "post", 0, true));

			if($key == "article_code")   
			{
				$data[$key] = strtolower(str_replace(" ", "-", $data[$key]));

				if($data[$key] != "")
				{
					//the article_code must be unique
					if($this->article_model->exists_article_code($data[$key], $article_id))
					{
						$this->error_model->report_error("Sorry, an article already exists with the article code '" . $data[$key] . "'.  Please press back and enter a different article_code to continue.", "ArticleManager/HandlerPost update - the article with an id of ".$data["article_code"]." already exists.");
						return; 
					}
				}
			}

			// Ensure that all required fields are present    
			if(in_array($key,$required_fields) && $data[$key] == "")
			{
				$missing_fields = true;
				break;
			}
        }

        // If there are missing fields, report the error
        if ($missing_fields)
        {
        	$this->error_model->report_error("Sorry, please fill in all required fields to continue.", "articlemanager/_HandlerPost update - the article with an id '$article_id' could not be saved");
        	return false;
        }

        if($article_id == "")
        {
	        // Get the next available article_order for an article in this category
	        $data["article_order"] = $this->article_model->get_next_article_order($data["category_id"]);
        }
        
        // If an $article_id is present, do an update, otherwise do an insert
        $article_id = $this->article_model->save($article_id, $data);
        
        if(!$article_id)
        {
             // Something went wrong whilst saving the user data.
             $this->error_model->report_error("Sorry, the article could not be saved/updated.", "articlemanager/article save");
             return false;
        }
        
        //save the page content in history 
        $this->history_model->save_history($table = "articles", $article_id);
        
        // Update assigned blocks left
        if(isset($_POST["assigned_blocks_left"]))
        {
            $array_blocks = explode(",", $_POST["assigned_blocks_left"]);
            $this->blocks_model->update_assigned_blocks($article_id, $array_blocks, "article", "left");
        }        
        
        // Update assigned blocks right
        if(isset($_POST["assigned_blocks_right"]))
        {
            $array_blocks = explode(",", $_POST["assigned_blocks_right"]);
            $this->blocks_model->update_assigned_blocks($article_id, $array_blocks, "article", "right");            
        }

        
        //Update website_pages
        if(isset($_POST["website_articles"]))
        {
            $this->website_model->add_website_assm($article_id, $_POST["website_articles"], "article");
        }
        
        // Update related items
        if ( isset($_POST['related']) AND is_array($_POST['related']) ) {
            $this->db->where("article_id = $article_id OR related_article_id = $article_id")
                ->delete('articles_related');
        	foreach ( $_POST['related'] as $relatedItem )
        	{
        	    $this->db->insert('articles_related', array('article_id'=>$article_id, 'related_article_id'=>intval($relatedItem)));
        	}
        }

        // All done - return the article id to the caller
        return $article_id;  
   } 
   
	/**
	* @method _handleCategoryPost
	* @desc This method is fired when the user submits the article category detail form.
	* If the article category already exists, the article is updated, otherwise a new article is created.
	* 
	* @param mixed $category_id The article category id being updated (if applicable)
	*/
	function _handleCategoryPost($category_id)
	{
		// Define the update array with default values
		$data = array( "name"        => '',
		     "enabled"               => '0',
		     //"sidebar_position"      => '0',
		     "seq_no"		         => '0',
		     "website_id"		     => '1',
		     "parent_id"		     => '',
		     "meta_title"            => '',
		     "meta_keywords"         => '',
		     "meta_description"      => '',
		     "meta_robots"           => '',
		     "view"                  => '',                     
		     "order_by"              => '',
		     "order_dir" 	         => '',
		     "short_description"     => '',
		     "long_description"      => '',
		     "hero_image"	         => '',
		     "hero_image_alt"	         => '',
		     "category_code"         => '',
		     'generate_rss_feed'	 => '0',
		     'enable_tab_seo'		 => '0',
		     'enable_tab_content'	 => '0',
		     'enable_tab_blocksleft' => '0',
		     'enable_tab_blocksright' => '0',
		     'enable_tab_gallery'	  => '0',
		     'enable_tab_documents'	  => '0',
		     'enable_tab_relation'	  => '0',
		     'enable_tab_area'	  => '0',
             'enable_tab_states'      => '0',
             'enable_tab_usertypes'      => '0',
		     
		     'enable_field_publicationdate'	  => '0',
		     'enable_field_author'	  => '0',
		     'enable_field_tags'	  => '0',
		     'enable_field_heroimage'	  => '0',
		     'enable_field_heroimagealt'	  => '0',
		     'enable_field_shortdescription'	  => '0',
		     'enable_field_content'	  => '0',
		     'enable_field_view'	  => '0',
		     'enable_field_www'	  => '0',
		     'enable_field_featured'	  => '0',
		     'enable_field_prices_form'	  => '0',
		     'enable_field_wholesale_price'	  => '0',
		     'enable_field_number_of_bedrooms'	  => '0',
		     'enable_field_number_of_bathrooms'	  => '0',
		     'enable_field_number_of_car'	  => '0',
		     'enable_field_agent_login'	  => '0',
		     'enable_field_video_code'	  => '0',
		     'enable_field_status'	  => '0',
		     'enable_field_source'	  => '0',
             'enable_field_document_attachment'      => '0',
             'enable_field_comments'      => '0',
             'enable_field_article_icon'      => '0',
		     
		     'image_hero_thumb_width'	  => '0',
		     'image_hero_thumb_height'	  => '0',
		     'image_hero_detail_width'	  => '0',
		     'image_hero_detail_height'	  => '0',
		     'image_hero_zoom_width'	  => '0',
		     'image_hero_zoom_height'	  => '0',
		     'image_gallery_thumb_width'	  => '0',
		     'image_gallery_thumb_height'	  => '0',
		     'image_gallery_detail_width'	  => '0',
		     'image_gallery_detail_height'	  => '0',
		     'image_gallery_zoom_width'	  => '0',
		     'image_gallery_zoom_height'	  => '0'   
		);


		// Define the required fields for validation
		$required_fields = array("name", "category_code", "website_id");
		$missing_fields = false; // Set to true if fields are missing

		//fill in data array from post values
		foreach($data as $key=>$value)
		{
			$data[$key] = $this->tools_model->get_value($key,$data[$key], "post", 0, true);

			// Ensure that all required fields are present    
			if(in_array($key,$required_fields) && $data[$key] == "")
			{
				$missing_fields = true;
				break;
			}
		}

		// If there are missing fields, report the error
		if ($missing_fields)
		{
			$this->error_model->report_error("Sorry, please fill in all required fields to continue.", "articlemanager/_HandlerPost update - the article with an id '$article_id' could not be saved");
			return false;
		}
		
		// Make sure that the category_code and website_id combination is unique
		$category_code = $this->input->post("category_code");
		$website_id = $this->input->post("website_id");

		// Is there a category that matches this code for this website?		
		$cat = $this->article_model->category_exists_bycode($category_code, $website_id);
		if($cat)
		{
			// A matching category was found.  Is it the current category?
			if($category_id != $cat->category_id)
 				show_error("Sorry, an article category with code '" . $category_code . "' already exists.  Please press back and alter the code is it's unique");
		}

		// If an $article_id is present, do an update, otherwise do an insert
		$category_id = $this->article_category_model->save($category_id, $data);

		if(!$category_id)
		{
			// Something went wrong whilst saving the user data.
			$this->error_model->report_error("Sorry, the category could not be saved/updated.", "articlecategorymanager/category save");
			return false;
		}

		// Update assigned blocks right
		if(isset($_POST["assigned_blocks_right"]))
		{
			$array_blocks = explode(",", $_POST["assigned_blocks_right"]);
			$this->blocks_model->update_assigned_blocks($category_id, $array_blocks, "category", "right");
		}

		// Update assigned blocks left
		if(isset($_POST["assigned_blocks_left"]))
		{
			$array_blocks = explode(",", $_POST["assigned_blocks_left"]);
			$this->blocks_model->update_assigned_blocks($category_id, $array_blocks, "category", "left");
		}      

		// All done - return the category id to the caller
		return $category_id;  
	}     
      
    
   	//handles all ajax requests within this page
	function ajaxwork()
	{
		// Load required libs
		$this->load->library('form_validation');
		
		$type = intval($this->tools_model->get_value("type", 0, "post", 0, false));


      switch($type)
      {
         case 1: //delete selected article(s)
            //get article ids separated with ";"
            $article_ids = $this->tools_model->get_value("todelete", "", "post", 0, false);

            if ($article_ids!="")
            {
               $arr_ids = explode(";",$article_ids);

               $where_in = "";

               foreach($arr_ids as $id)
               {
                  if (is_numeric($id))
                  {
                     if ($where_in != "") $where_in.=",";

                     $where_in .= $id;
                  }
               }

               if ($where_in!="")
               {
                  $this->article_model->delete_articles($where_in);
                  echo "ok";
               }
            }
            
            break;
            
         case 3: //add new category

            $return_data = array();
            $category_name = $this->tools_model->get_value("category_name", 0, "post", 0, false);
            $category_code = $this->tools_model->get_value("category_code", 0, "post", 0, false);
            $parent_id = $this->tools_model->get_value("parent_id", -1, "post", 6, false);
    		$parent_category = $this->article_category_model->get_details($parent_id);
            
            // Get the selected website id from the session
            //$website_id = $this->session->userdata("website_id");
            $website_id = '1';
            // A website id must be provided
            if($website_id <= 0)
            	return false;
            	
			// Is there a category that matches this code for this website?		
			$cat = $this->article_model->category_exists_bycode($category_code, $website_id, $parent_id);
			if($cat)
			{
				// A matching category was found.
 				$return_data["message"] = "This category code already exists.  Please try another.";
	            echo json_encode($return_data);
	            break;  				
			}            	
            
			if ( $parent_category ) {
                $this->article_model->add_category_clone_parent_data($category_name, $category_code, $website_id, $parent_id, $parent_category);
			} else {
                $this->article_model->add_category($category_name, $category_code, $website_id, $parent_id);
			}
            $return_data["message"] = "Category created";                          
            $return_data["html"] = $this->get_categorylist($parent_id);

            echo json_encode($return_data);
            break;  

         case 4: //delete category

            $return_data = array();

            $categories = $this->tools_model->get_value("todelete","","post",0,false);
            $parent_category_id = $this->tools_model->get_value("category_id",-1,"post",0,false);
            
            $string = "";
            $spy = false;
                                                                                       
            if ($categories!="")
            {
                $arr_categories = explode(";",$categories);
                for( $i=0; $i< count( $arr_categories ); $i++ )
                {
                	//get the article category
                	$article_category = $this->article_category_model->get_details($arr_categories[$i]);
					
                	//check first if there is a product
                	$check = $this->product_model->product_exists($article_category->product_id);
                	if( $check )
                	{
                		$spy = true;
                		$string .= $article_category->name."  "; 	
                	}
                	else
                	{
                		$this->article_model->remove_category($arr_categories[$i]);
                	}
                }
                
                if( $spy )
                {
                	$string .= " couldn't be deleted because the product(s) are still active.";
                }

                $return_data["message"] = "ok";
            }
            else
                $return_data["message"] = "Error deleteing categories";

            $return_data["products"] = $string;
                                    
            //refresh category list                            
            $return_data["html"] = $this->get_categorylist($parent_category_id);

            echo json_encode($return_data);

            break;   

         case 5: //refresh categories
            $return_data["html"] = $this->get_categorylist();

            echo json_encode($return_data);
            break;

         case 6: //refresh articles list

            $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
            $website_id = $this->tools_model->get_value("website","","post",0,false);                
            $category_id = $this->tools_model->get_value("category_id",0,"post",0,false);
             
			// Load the category that has been selected 
			$category = $this->article_category_model->get_details($category_id);
			  
            $articles = $this->article_model->get_list($category_id,FALSE, false, "article_order", "ASC", 0, 0, "article_title like '%".$search_terms."%'", $count_all = 0);
            $return_data["html"] = $this->load->view('admin/contentmanager/content_listing',array('articles' => $articles, 'category_id' => $category_id),true); 

            echo json_encode($return_data);
            break;

         case 7: //get main content of the article manager page, used for add new article
            $category_id = $this->tools_model->get_value("category_id",0,"post",0,false);
            $article_id = $this->tools_model->get_value("article_id",0,"post",0,false);

            $this->data["article_title"] = $this->tools_model->get_value("article_title",0,"post",0,false);
            $this->data["article_id"] = $article_id;

            if($article_id >0)
            {
               $this->data["article"] = $this->article_model->get_details($article_id);                    
            }

            $html = $this->load->view("admin/contentmanager/article",$this->data,true);

            $return_data = array();
            $return_data["html"] = $html;
            echo json_encode($return_data);
            
            break;
            
         case 8:    //save article
            $article_id = $this->tools_model->get_value("article_id",0,"post",0,false);                

            $data = array(
               "article_title"         => "",
               "article_date"          => date("y-m-d H:i:s"),
               "enabled"               => "1"
            );

            $required_fields = array("article_title");
            $html_encode_fields = array("article_title");
            $missing_fields = false;

            //fill in data array from post values
            foreach($data as $key=>$value)
            {
               $data[$key] = $this->tools_model->get_value($key,"","post",0,true);

               if(in_array($key,$html_encode_fields) && $data[$key] == "")
               $data[$key] = htmlspecialchars($data[$key]);

               // Ensure that all required fields are present    
               if(in_array($key,$required_fields) && $data[$key] == "")
               {
                  $missing_fields = true;
                  break;
               }
            }
            
            if ($missing_fields)
            {
               $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "ArticleManager/Ajaxworker update - the article with an id of '$article_id' could not be saved");
               return;
            }

            //depeding on the $article_id do the update or insert
            $article_id = $this->article_model->save($article_id, $data);
            if(!$article_id)
            {
               // Something went wrong whilst saving the user data.
               $this->error_model->report_error("Sorry, the Article could not be saved/updated.", "ArticleManager/Article save");
               return;
            }

            $this->utilities->updateHtaccess($this);

            break;
            
         case 9:
         	$message = "OK";
         	$article_id = $this->tools_model->get_value("article_id", 0, "post", 0, false);
         	$direction = $this->tools_model->get_value("direction", 0, "post", 0, false);
         	
         	if((($article_id == "") || (!is_numeric($article_id))) || (($direction != "up") && ($direction != "down")))
         	{
					$message = "ERROR";
         	}
         	else
				{
					$article = $this->article_model->get_details($article_id, FALSE);
					
					if(!$article)
					{
						$message = "ERROR";	
					}
					else
					{
						$category_id = $article->category_id;
						
						// Load all of the articles in this category, in article_order order.
						$articles = $this->article_model->get_list($category_id, FALSE, false, "article_order", "ASC", 0, 0, "", $count_all = 0);
						
						// Create an array to hold the re-ordered items
 						$items_array = array();
						
						$seqno = 10;	// Starting sequence number
						
						// Loop through all articles in the category
						foreach($articles->result() as $row)
						{
							// If this article is the article the user is trying to reorder,
							// modify the sequence number +- 15 according to direction.
							if($row->article_id == $article_id)
							{
								if($direction == "up")
									$items_array[$seqno - 15] = $row->article_id;
								else
									$items_array[$seqno + 15] = $row->article_id;  	
							}
							else
							{
								// This is not the article the user is trying to reorder.
								$items_array[$seqno] = $row->article_id; 	
							}
							
							// Increment the sequence number
							$seqno = $seqno + 10;
						}
						
						// Sort the array by the keys (the new sequence numbers)
						ksort($items_array);
						
						// Now loop through the articles, updating their sequence numbers
						$seqno = 1;
						
						foreach($items_array as $s=>$item_id)
						{
							$this->article_model->save($item_id, array("article_order" => $seqno));
							$seqno++;
						}
					}
							
				}
         	
            $return_data = array();
            $return_data["message"] = $message;
            echo json_encode($return_data);         	
         	
         	break;
            //search for articles
            case 10:
            
                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                $website_id = $this->tools_model->get_value("website","","post",0,false);                
                $category_id = $this->tools_model->get_value("category_id",0,"post",0,false);
                
                $articles = $this->article_model->get_list($category_id,FALSE, false, "article_order", "ASC", 0, 0, "article_title like '%".$search_terms."%'", $count_all = 0);
                $return_data["html"] = $this->load->view('admin/contentmanager/content_listing',array('articles' => $articles, 'category_id' => $category_id),true); 

            echo json_encode($return_data);
            
            break;

			case 11: 
      			// Gallery Image Upload
      			
      			// Read in the details of the file that has been uploaded
				$tmp_name = $_FILES["Filedata"]["tmp_name"];
				$name = $_FILES["Filedata"]["name"];

				// Read in the article
				$article_id = $this->tools_model->get_value("article_id","","post",0,false);
				
                // Load the article object
                $article = $this->article_model->get_details($article_id);
                if(!$article)
                	die();
                	
                // Load in the article category
                $category = $this->article_category_model->get_details($article->category_id);
                if(!$category)
                	die();				
				
				// Determine file path and move the temporary file to the final destination.
				$file_path = FCPATH . ARTICLE_FILES_FOLDER . $article_id . "/" . $name;
				move_uploaded_file($tmp_name, $file_path);
				chmod($file_path, 0666); 

				// Make sure the upload worked OK.
				if(!file_exists($file_path))
				{
					echo "error";
					exit();
				}

      			// Create the thumbnail, detail and zoom images for this image (these are determined by the parent category)
   				$images = array("image_gallery_thumb", "image_gallery_detail", "image_gallery_zoom");
   				$suffixes = array("_gallerythumb", "_gallerydetail", "_galleryzoom");
   				
 				if(!$this->image->create_image_set($category, $images, $suffixes, $file_path))
 				{
					$this->utilities->add_to_debug("ContentManager::Ajaxwork case 11 - Error whilst creating imageset");
					exit();
 				}

                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "article_image",
					"foreign_id" => $article_id,
					"document_name" => $name,
					"document_path" => ARTICLE_FILES_FOLDER . $article_id . "/" . $name
				);

				$this->document_model->save("", $img_data, $article_id, "article_image", $use_order = TRUE);

				echo "done";

				break;
            
            case 12: //up and down order
                $return_data = array();
                
                //get id of product
                $direction = $this->tools_model->get_value("direction","","post",0,false);
                $article_id = $this->tools_model->get_value("article_id","","post",0,false);
                $doc_id = $this->tools_model->get_value("doc_id","","post",0,false);
                
                if($doc_id != "")
                {
                    $return_data["message"] = "ok";
                    $this->document_model->move_file( $doc_id, $direction );
                }
                else
                    $return_data["message"] = "No selected image";
                    
                //reresh category list
                $return_data["html"] = $this->_refresh_images( $article_id );
                
                echo json_encode($return_data); 
            break;
            
            case 13:
            	// Delete gallery files
                $return_data = array();
                
                $article_id = intval($this->tools_model->get_value("article_id", 0, "post", 0, false));
                $file_names = $this->tools_model->get_value("todelete", "", "post", 0, false);

                if ($file_names!="")
                {
                    $arr_files = explode(";",$file_names);
                    
                    $this->document_model->delete_files($arr_files, $article_id, "article_image");
                    
                    $suffixes = array("_gallerythumb", "_gallerydetail", "_galleryzoom");
                    
                    // Loop through each file and delete the full image set associated with each one.
                    foreach($arr_files as $file_to_delete)
                    {
                    	if($file_to_delete != "")
                    	{
                    		$full_path = FCPATH . ARTICLE_FILES_FOLDER . $article_id . "/$file_to_delete";
                    		
                    		if(file_exists($full_path))
							{
								$this->image->remove_image_set($suffixes, $full_path); 	
							}		
						}
                    }
                }
                                
                //reresh category list
                $return_data["html"] = $this->_refresh_images( $article_id );
                
                echo json_encode($return_data);
                
                break;
                
            case 14:	// Delete article hero image (s)

                $return_data = array();
                
                // Read in the article id and load the associated article
                $article_id = intval($this->tools_model->get_value("article_id", 0, "post", 0, false));
                $article = $this->article_model->get_details($article_id); 
                
                if(!$article)
                {
	                $return_data["message"] = "ERROR 1";
	                echo json_encode($return_data);  
	                exit(); 					
                }
                
                // Ensure the article currently has a hero image defined.
                if($article->hero_image != "")
                {
                	// Remove all images.
                	$suffixes = array("_thumb", "_detail", "_zoom");
                	
					if(!$this->image->remove_image_set($suffixes, FCPATH . $article->hero_image))
					{
		                $return_data["message"] = "ERROR 2";
		                echo json_encode($return_data);  
		                exit(); 						
					}
					
					// Update the article in the DB to remove the hero image
					$update_data = array("hero_image" => "");
					$this->article_model->save($article_id, $update_data);
				}
              	
              	// All done - return OK back.            
                $return_data["message"] = "OK";
                echo json_encode($return_data); 
                break;   

            case 15:
         		// Hero Image Upload
            	// Read in the file temp name and name.
            	$tmp_name = $_FILES["Filedata"]["tmp_name"];
				$name = $_FILES["Filedata"]["name"];

				// Read in the article id
                $article_id = intval($this->tools_model->get_value("article_id", 0, "post", 0, false));
                
                // Load the article in question
                $article = $this->article_model->get_details($article_id);
                if(!$article)
                	die();
                	
                // Load in the article category
                $category = $this->article_category_model->get_details($article->category_id);
                if(!$category)
                	die();
                
				// Determine the path for where to store the original image and the image set
                $path = ABSOLUTE_PATH . ARTICLE_FILES_FOLDER . $article_id;
				$file_path = $path . "/" . $name;	
				
				// Move the temporary file to the final path.
				move_uploaded_file($tmp_name, $file_path);
  			    chmod($file_path, 0666);
   
   				// Create the thumbnail, detail and zoom images for this image (these are determined by the parent category)
   				$images = array("image_hero_thumb", "image_hero_detail", "image_hero_zoom");
   				$suffixes = array("_thumb", "_detail", "_zoom");
   				
 				if(!$this->image->create_image_set($category, $images, $suffixes, $file_path))
 				{
					$this->utilities->add_to_debug("ContentManager::Ajaxwork case 15 - Error whilst creating imageset");
					exit();
 				}
             
				// Update the article with the hero name.
	            $update_data = array("hero_image" => ARTICLE_FILES_FOLDER . $article_id. "/" . $name);
				$this->article_model->save($article_id, $update_data);
				
				echo "done";
            break;
            
            //list all history time
            case 16:
                $return_data = array();
                
                $table = $this->tools_model->get_value("table","","post",0,false);
                $history_type = $this->tools_model->get_value("history_type","","post",0,false);
                $foreign_id = $this->tools_model->get_value("id","","post",0,false);
                
                $history = $this->history_model->get_list($table, $history_type, "", "", $count_all = 0,"", $foreign_id);                
                //echo $this->db->last_query();   
                $this->data["history"] = $history;
                
                //load view 
                $return_data['html'] = $this->load->view('admin/ckeditor/history_popup', array('history' => $history, 'history_type' => $history_type), true);
                
                // return the page
                echo json_encode( $return_data );
            break;  
            
            case 17:
            	// CKEditor history view previous item
                $return_data = array();
                $return_data['html'] = "";
                
                $table = $this->tools_model->get_value("table","","post",0,false);
                $history_type = $this->tools_model->get_value("history_type","","post",0,false);
                $id = $this->tools_model->get_value("id","","post",0,false);
                
                $history = $this->history_model->get_list($table,$history_type,"","", $count_all = 0, $id);                
                
                if($history)
                {
                    $result = $history->first_row();                
                    $return_data['html'] = $result->content;
                }
                
                // return the page
                echo json_encode( $return_data );
            break;            
            
            case 18:
            	// CKEditor history rollback
                $return_data = array();
                $return_data["error"] = "";
                
                $table 			= $this->tools_model->get_value("table","","post",0,false);
                $history_type	= $this->tools_model->get_value("history_type","","post",0,false);
                $id 			= $this->tools_model->get_value("id","","post",0,false);
                
                $history = $this->history_model->get_list($table,$history_type,"","", $count_all = 0, $id);                
                
                if($history)
                {
                    $result = $history->first_row();
                    
	                $content = $result->content;    
	                $foreign_id = $result->foreign_id;
	                
	                $return_data["error"] = $this->history_model->update($table, $foreign_id, $history_type, $content);
	                $return_data["error"] = ($return_data["error"] == true) ? "1" : "0";
                }
                else
                {
                	$return_data["error"] = "0";
                }
                                 
                // return the page
                echo json_encode( $return_data );
            break; 
            
            case 19:
            	$return_data = array();
            	$return_data["status"] = "OK";
            	$return_data["message"] = "";
            	
			    $this->form_validation->set_rules('order', 'Document Order', 'required|xss_clean|trim|integer');
			    $this->form_validation->set_rules('foreign_id', 'Foreign ID', 'required|xss_clean|trim|integer');
			    $this->form_validation->set_rules('document_type', 'Document Type', 'required|xss_clean|trim');
			    $this->form_validation->set_rules('document_name', 'Document Name', 'required|xss_clean|trim');
			    $this->form_validation->set_rules('document_path', 'Document Path', 'required|xss_clean|trim');
			    $this->form_validation->set_rules('icon', 'Document Icon', 'xss_clean|trim');
			    
			    // Check form submission against validation rules
			    if ($this->form_validation->run() == FALSE)
			    {
			        // Validation failed
			        $return_data["status"] = "ERROR";
			        $return_data["message"] = validation_errors();
			        echo json_encode($return_data);
			        return;
				}
				
				// Read in post vars into an array
				$data = array();
				$data["order"] = $this->input->post("order");
				$data["foreign_id"] = $this->input->post("foreign_id");
				$data["document_type"] = $this->input->post("document_type");
				$data["document_name"] = $this->input->post("document_name");
				$data["document_path"] = $this->input->post("document_path");
				$data["icon"] = $this->input->post("icon");
				
				// See if this document already exists
				$document_id = "";
				$params = array();
				$params["order"] = $data["order"];
				
				$docs = $this->document_model->get_list($data["document_type"], $data["foreign_id"], $params);  
				if($docs)
				{
					$row = $docs->row();
					$document_id = $row->id;
				}
            
            	$document_id = $this->document_model->save($document_id, $data);
            	
            	echo json_encode($return_data);
            	break;                     
            	
            case 20: // upload video
            	$tmp_name = $_FILES["Filedata"]["tmp_name"];
				$name = $_FILES["Filedata"]["name"];

				// Read in the article id
                $article_id = intval($this->tools_model->get_value("article_id", 0, "post", 0, false));
                
                // Load the article in question
                $article = $this->article_model->get_details($article_id);
                if(!$article) die();
                	
                // Load in the article category
                $category = $this->article_category_model->get_details($article->category_id);
                if(!$category) die();
                
				// Determine the path for where to store the original image and the image set
                $path = ABSOLUTE_PATH . ARTICLE_FILES_FOLDER . $article_id;
				$file_path = $path . "/" . $name;	
				
				// Move the temporary file to the final path.
				move_uploaded_file($tmp_name, $file_path);
  			    chmod($file_path, 0666);
   
   				// Create the thumbnail, detail and zoom images for this image (these are determined by the parent category)
   				$images = array("image_hero_thumb", "image_hero_detail", "image_hero_zoom");
   				$suffixes = array("_thumb", "_detail", "_zoom");

            	// convert to flv
            	$filePath = FCPATH . ARTICLE_FILES_FOLDER . $article_id. "/" . $name;
//                $shell_exec = escapeshellcmd("ffmpeg -i $filePath -deinterlace -ar 22050 -ab 56 -r 25 -f mp4 -s 500x280 $filePath.mp4");
//            	$error = system( $shell_exec, $return_value );
//            	echo $error;
//                $shell_exec = escapeshellcmd("/usr/local/bin/ffmpeg -i $filePath -deinterlace -ar 22050 -ab 56 -r 25 -f flv -s 500x280 $filePath.flv");
//            	$error = system( $shell_exec, $return_value );
//            	echo $error;
                $shell_exec = escapeshellcmd("/usr/local/bin/ffmpeg -y -i $filePath -vcodec libx264 -cqp 1 -intra -coder ac -an -s 500x280 $filePath.mp4");
            	$error = system( $shell_exec, $return_value );
            	echo $error;
//                $shell_exec = escapeshellcmd("/usr/local/bin/ffmpeg -y -i $filePath -vcodec libvpx -r 10 -b 1800 -f webm $filePath.webm");
                $shell_exec = escapeshellcmd("/usr/local/bin/ffmpeg -y -i $filePath -threads 8 -f webm -aspect 16:9 -vcodec libvpx -deinterlace -g 120 -level 216 -profile 0 -qmax 42 -qmin 10 -rc_buf_aggressivity 0.95 -vb 2M -acodec libvorbis -aq 90 -ac 2 $filePath.webm");
            	$error = system( $shell_exec, $return_value );
//            	echo $error;
            
            	// create thumbnail
            	$shell_exec	= '/usr/local/bin/ffmpeg -ss 2 -i '.$filePath.' -f image2 -vframes 1 -s 500x280 -y '.$filePath.'.jpg';
            	$error = system( $shell_exec );
            	echo $error;
   				
 				if(!$this->image->create_image_set($category, $images, $suffixes, "$filePath.jpg"))
 				{
					$this->utilities->add_to_debug("ContentManager::Ajaxwork case 15 - Error whilst creating imageset");
					exit();
 				}
             
	            $update_data = array(
	                               "video" => ARTICLE_FILES_FOLDER . $article_id. "/" . $name,
	                               "hero_image" => ARTICLE_FILES_FOLDER . $article_id. "/" . "$name.jpg"
                               );
				$this->article_model->save($article_id, $update_data);
				
				echo "done";
                break;
                
            case 21: // delete attachment
                $article_id = isset($_POST['article_id']) ? intval($_POST['article_id']) : 0;
                $this->article_model->save($article_id, array('attachment'=>''));
                echo 'done';
                break;
      }
   }
   
    function _refresh_images( $article_id )
    {
        $this->data['article_id'] = $article_id;
        
        $this->data['images'] = $this->document_model->get_files( "article_image", $article_id, 'order', $count_all );
          
        $this->data["pages_no"] = $count_all / $this->records_per_page; 
        $this->data['count_all'] = $count_all;
        
        $images = $this->load->view('admin/article/file_listing.php',NULL,true);
        return $images;
    }
    
    /*return options HTML */
    function get_categorylist($parent_id = -1)     
    {
        // Get the selected website id from the session
        $website_id = $this->session->userdata("website_id");
        
        // A website id must be provided
        if($website_id <= 0)
            return false;    	
    	
        $categories = $this->article_model->get_article_categories(-1, $website_id, $parent_id);
        $selected_category = "";

        if($categories && count($categories)>0)
        {
            $category_result = $categories->result();
            $selected_category = $category_result[0]->category_id;            
        }
       // $this->data["category_id"] = $selected_category;
        $this->data["category_id"] = $parent_id;
        $this->data["categories"] = $categories; 
        
        $html = $this->load->view('admin/contentmanager/category_listing.php',NULL,true); 
        
        return $html;
    } 
    
    function upload_file($upload_type, $article_id, $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($article_id)))
		{
			die ('{error: "Invalid upload type $upload_type or article id $article_id"}');
		}
		
		// Handle variation image uploads
		if($upload_type == "variation_image")
		{
			$variation_item_id = $article_id;
			
			// Load the variation item
			$result = $this->db->get_where("category_variation_items", array("id" => $variation_item_id));
			if($result->num_rows() != 1)
			{
				die ('{error: "Invalid variation item id"}');	
			}
			
			$variation_item = $result->row();
			
			// Get the id of the related article.
			$article_id = $variation_item->item_id;
		}
		
		// Handle variation image uploads
		if($upload_type == "attachment")
		{
            // Load the article in question
            $article = $this->article_model->get_details($article_id);
            if(!$article) {
				die ('{error: "Invalid article"}');	
            } else {
    			// Determine the path for where to store attachment
                $path = ABSOLUTE_PATH . ARTICLE_FILES_FOLDER . $article_id . "/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                } else {
                 	$result = $this->qqfileuploader->handleUpload($path, $filename, true);
                 	if($filename == "")
                 	{
        				$filename = $this->qqfileuploader->file->getName();
        				
                 		if($filename == "")
                 		{
                 			die ('{error: "Could not determine file name"}');
        				} 				
                 	}
                 	
                 	$file_path =  $path . $filename;
                 	if(!file_exists($file_path))
                 	{
        				die ('{error: "File did not upload correctly"}');	
                 	} else {
                 	    $this->article_model->save($article_id, array('attachment'=>$filename));
                 	    echo json_encode(array(
                 	                          'success' => 'OK',
                 	                          'filename' => $filename,
                 	                          'url' => base_url() . ARTICLE_FILES_FOLDER . $article_id . '/' . $filename
                 	                      ));
 	                    exit();
                 	}
                 	
                }
            }
		}
		
		// Handle a hero image upload
		if(($upload_type == "hero_image") || ($upload_type == "gallery_image") || ($upload_type == "variation_image"))
		{
            // Load the article in question
            $article = $this->article_model->get_details($article_id);
            if(!$article)
            {
				die ('{error: "Invalid article"}');	
            }
            // Load in the article category
            $category = $this->article_category_model->get_details($article->category_id);
            if(!$category)
            {
            	die ('{error: "Invalid article category"}');
			}
            
			// Determine the path for where to store the original image and the image set
            $path = ABSOLUTE_PATH . ARTICLE_FILES_FOLDER . $article_id . "/";
            if ( !is_dir($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            // If this is a variation image, append the variation id in the path
			if($upload_type == "variation_image")
			{
				$path .= $variation_item_id . "/";
                
				if(!is_dir($path))
				{
					@mkdir($path, DIR_WRITE_MODE);
					
					if(!is_dir($path))
					{
						die ('{error: "Could not create variation image folder"}');	
					}
				}
			}
            
         	// Hero Image Upload
         	$result = $this->qqfileuploader->handleUpload($path, $filename, true);

         	if($filename == "")
         	{
				$filename = $this->qqfileuploader->file->getName();
				
         		if($filename == "")
         		{
         			die ('{error: "Could not determine file name"}');
				} 				
         	}
         	
         	$file_path =  $path . $filename;
         	if(!file_exists($file_path))
         	{
				die ('{error: "File did not upload correctly"}');	
         	}
         	
         	
         	if($upload_type == "variation_image")
         	{
         		// Load the variation item in question
         		$variation_item = $this->article_category_model->get_variation_item($variation_item_id);
         		
         		// Delete any previously uploaded images.
         		if(($variation_item) && ($variation_item->hero_path != ""))
         		{
					@unlink(ABSOLUTE_PATH . $variation_item->hero_path);
					@unlink(ABSOLUTE_PATH . $variation_item->hero_path . "_thumb.jpg");
					@unlink(ABSOLUTE_PATH . $variation_item->hero_path . "_detail.jpg");
					@unlink(ABSOLUTE_PATH . $variation_item->hero_path . "_zoom.jpg");
         		}
         		
				$file_path_www = base_url() . ARTICLE_FILES_FOLDER . $article_id . "/" . $variation_item_id . "/" . $filename;	
         	}
         	else
         	{
         		$file_path_www = base_url() . ARTICLE_FILES_FOLDER . $article_id . "/" . $filename;
			}
			
			// Move the temporary file to the final path.
  			chmod($file_path, 0666);
  			
  			if(($upload_type == "hero_image") || ($upload_type == "variation_image"))
  			{
  				// The user is uploading a hero image.
  				
   				// Create the thumbnail, detail and zoom images for this image (these are determined by the parent category)
   				$images = array("image_hero_thumb", "image_hero_detail", "image_hero_zoom");
   				$suffixes = array("_thumb", "_detail", "_zoom");
   			
   				// Create all required images (resize and crop as necssary)
 				if(!$this->image->create_image_set($category, $images, $suffixes, $file_path))
 				{
					die ('{error: "Error creating image set.  Have the gallery image sizes for the article category been set?"}');
 				}
	         
	         	if($upload_type == "hero_image")
	         	{
					// Update the article with the hero name.
		        	$update_data = array("hero_image" => ARTICLE_FILES_FOLDER . $article_id. "/" . $filename);
					$this->article_model->save($article_id, $update_data);
				}
				else
				{
					// Update the variation item with the hero path
		        	$update_data = array("hero_path" => ARTICLE_FILES_FOLDER . $article_id. "/" . $variation_item_id . '/' . $filename);
		        	
		        	$this->db->where("id", $variation_item_id);
		        	$this->db->update("category_variation_items", $update_data);
				}
			}
			else if($upload_type == "gallery_image")
			{
				// The user is uploading a gallery image
				
				// Define the images that we need to create and their associated suffixes
   				$images = array("image_gallery_thumb", "image_gallery_detail", "image_gallery_zoom");
   				$suffixes = array("_gallerythumb", "_gallerydetail", "_galleryzoom");
   				
   				// Create all required images (resize and crop as necssary)
 				if(!$this->image->create_image_set($category, $images, $suffixes, $file_path))
 				{
					$this->utilities->add_to_debug("ContentManager::Ajaxwork case 11 - Error whilst creating imageset");
					exit();
 				}

                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "article_image",
					"foreign_id" => $article_id,
					"document_name" => $filename,
					"document_path" => ARTICLE_FILES_FOLDER . $article_id . "/" . $filename
				);

				$this->document_model->save("", $img_data, $article_id, "article_image", $use_order = TRUE);				
			}

			$return = array();
			$return["status"] = "OK";
			$return["fileName"] = $file_path_www;
			$return["success"] = true;	
			
			echo json_encode($return);	
		}
		else
		{
			die ('{error: "Invalid file type"}');
		}	
    }
    
    /*********************** AJAX FUNCTIONS */
    function update_document()
    {
		$return = array();
		$return["status"] = "ERROR";
		$return["message"] = "";
			    	
		$id = $this->input->post("id");
		
		if(!is_numeric($id))
		{
			$return["message"] = "Invalid document id";
			echo json_encode($return);
			exit();
		}
		
		$data = array();
		$this->db->where("id", $id);
		
		if(isset($_POST["document_description"]))
		{
			$data["document_description"] = $this->input->post("document_description");
		}
		
		if(isset($_POST["link"]))
		{
			$data["link"] = $this->input->post("link");
		}		
		
		if(count($data) > 0)
		{
			$this->db->update("documents", $data);
			$return["status"] = "OK";
		}
		
		echo json_encode($return);
    }
}

