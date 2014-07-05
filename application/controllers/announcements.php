<?php
// NOTE: Check application/core/MY_Controller to see how user permissions are enforced.

// Restrict access to this controller to specific user types
//define("RESTRICT_ACCESS", USER_TYPE_COMPANY . "," . USER_TYPE_STAFF);

class announcements extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function announcements()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("Users_model");
    }
   
    function index()
    {
        $this->data["meta_title"] = "announcements";                        

        $this->load->view('member/header', $this->data);
        $this->load->view('member/announcements/list/prebody.php', $this->data); 
        $this->load->view('member/announcements/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function ajax()
    {
        // Prepare the return array
        $this->data = get_return_array();   // Defined in strings announcementser
        
        // Get the action that the user is trying to perform.
        $action = $this->input->post("action");
        
        // Handle the action.
        switch($action)
        {
            case "load_media":   // Load announcements item
                $this->handle_load_media();
                break;
                
            case "download_media":   // User is trying to download a announcements media item.
                $this->handle_download_media();
                break;
                
            case "email_resource":   // User is trying to email the announcements item to someone.
                $this->handle_email_resource();
                break;
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    /***
    * Handles the handle_download_media action
    * Loads the details of the article
    */
    private function handle_download_media()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('article_id', 'Article ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $article_id = $this->input->post("article_id");
        $article = $this->article_model->get_details($article_id, true);
        
        if(!$article)
        {
            $this->data["message"] = "Sorry, the file could not be downloaded";
            send($this->data);                
        }
        
        // Make sure the user has permissions to download this article.
        if(!$this->article_model->user_type_has_article_permissions($this->user_type_id, $article_id))
        {
            $this->data["message"] = "Sorry, you do not have permission to download this file";
            send($this->data);            
        }
        
        $article->link_www = false;
        
        if($article->attachment != "")
        {
            // Craete a temporary symlink to the download
            $target = ABSOLUTE_PATH . "article_files/" . $article->article_id . "/" . $article->attachment;
            if(!file_exists($target))
            {
                $this->data["message"] = "Source file doesn't exist";     
                send($this->data);                      
            }
            
            $ext = pathinfo($target, PATHINFO_EXTENSION);
            
            $link_file_name = $article->attachment . "_" . random_string("alnum", 8) . "." . $ext;
            $link = ABSOLUTE_PATH . "downloads/" . $link_file_name;
            $link_www = base_url() . "downloads/" . $link_file_name;
            
            if(!link($target, $link))
            {
                $this->data["message"] = "Couldn't create link to source file";     
                send($this->data);                 
            } 
            
            $article->link_www = $link_www;   
        } 
        
        $this->data["height"] = 400;
        $this->data["width"] = 450;
        
        if($article->video_code != "")
        {
            $this->data["height"] = 610;    
            $this->data["width"] = 860;
        }
        else
        {
        	if ($article->hero_image != "")
        	{
        		$this->data["height"] = 400;    
            	$this->data["width"] = 850;
        	}
        }
        
        // Load assign users
        $filters = array();
        
        $user_ids = array();
    	$user_logged = $this->users_model->get_details($this->user_id);
    	$user_ids[] = $user_logged->company_id;
    	$user_ids[] = $this->user_id;
    	$filters['in_arr_ids'] = $user_ids;
        
        if (in_array($this->user_type_id, array(USER_TYPE_COMPANY, USER_TYPE_STAFF)))
        {
        	$filters['created_by'] = $this->user_id;
        }
        	
        $filters["deleted"] = 0;
        $filters["order_by"] = "u.first_name ASC";
        
        $assign_client_select_sql = ", CASE " .
            "WHEN (length(u.company_name) > 0) THEN CONCAT(u.first_name, ' ', u.last_name, ' (', u.company_name, ')') " .
            "ELSE CONCAT(u.first_name, ' ', u.last_name) " .
            "END as client_name";
            
        $users = $this->users_model->get_list(1, '', '', $count_all, "", USER_TYPE_STAFF, $filters, $assign_client_select_sql);

        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/announcements/list/modal", array("article" => $article, "users" => $users, 'email_logged' => $user_logged->email), true);
        
        send($this->data);    
    }
    
    /***
    * Handles the load_media action
    * Loads a list of media articles, taking into account search filters and user permissions.
    */
    private function handle_load_media()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        $this->form_validation->set_rules('sort_col', 'Sort Column', 'required');
        $this->form_validation->set_rules('sort_dir', 'Sort Dir', 'required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $valid_columns = array("a.article_date", "a.article_title", "a.source", "ac.name");
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }
        
        $current_page = $this->input->post("current_page");
        
        $filters = array();     
        $filters["search_term"] = $this->input->post("search_term");
        $filters["user_type_id"] = $this->user_type_id;
        
        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        $media = $this->article_model->get_announcements($filters, $order_by, MEDIA_PER_PAGE, $current_page, $count_all);

        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/announcements/list/list", array("media" => $media), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
    
    /***
    * Handles the email_resource action
    * Loads a list of media articles, taking into account search filters and user permissions.
    */
    private function handle_email_resource()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        // Validate the form submission
        $this->form_validation->set_rules('article_id', 'Article ID', 'required|number'); 
        $this->form_validation->set_rules('email_resource', 'Email Resource', 'required|valid_email'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $article_id = $this->input->post("article_id");
        $article = $this->article_model->get_details($article_id, true);
        $user_logged = $this->users_model->get_details($this->user_id);
        
        if(!$article)
        {
            $this->data["message"] = "Sorry, the file could not be sent";
            send($this->data);                
        }
        
        // Make sure the user has permissions to download this article.
        if(!$this->article_model->user_type_has_article_permissions($this->user_type_id, $article_id))
        {
            $this->data["message"] = "Sorry, you do not have permission to send email resource";
            send($this->data);            
        }
        
        
        
        // Send resource
        $email_data = array();
        $name = $this->input->post('name') ? $this->input->post('name') : '';
		$email_data["name"] = !empty($name) ? $name : 'there';
        $email_data["first_name"] = $user_logged->first_name;
        $email_data["last_name"] = $user_logged->last_name;
        $email_data["resource_name"] = $article->article_title;
        $email_data["publication_date"] = $this->utilities->iso_to_ukdate($article->article_date);
        $email_data["last_modified"] = date('d/m/Y', strtotime($article->last_modification_dtm));
        $email_data["tags"] = substr($article->tags, 1, strlen($article->tags) - 2);
        $email_data["author"] = $article->author;
        $email_data["source"] = $article->source;
        $email_data["document_type"] = $article->category_name;
        $email_data["comments"] = $article->comments;
        if($article->www != "")
        {
        	$email_data["web_link"] = '<a href="'.addhttp($article->www).'" target="_blank">Click here to view</a>';
        }
        else
        {
        	$email_data["web_link"] = '';
        }
    		
        $attach = '';
        $target = ABSOLUTE_PATH . "article_files/" . $article->article_id . "/" . $article->attachment;
        if(file_exists($target))
        {
			$attach = $target;
        }
        
        $this->email_model->send_email($this->input->post("email_resource"), "email_resource", $email_data, $attach, $bcc = array());    

        // Delete worked correctly.   Send the OK back.
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        send($this->data);  
    }
}