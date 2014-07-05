<?php
class Pagemanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("page_model");            
        $this->load->model("blocks_model");
        $this->load->model("document_model");
        $this->load->model("article_model");        
        $this->load->model("history_model");
        $this->load->model("website_model");

        $this->load->helper('form_helper');
        
        $this->load->library("utilities");    
      
        // Check for a valid session
        if (!$this->login_model->getSessionData("logged_in"))
           redirect("login");       
    }
   
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Page Manager";
        $this->data["page_heading"] = "Page Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");        
        $this->data["pages"] = $this->page_model->get_list(true,false,$this->records_per_page,1,$count_all);
        //die($this->data["pages"]);
        
        $this->data["pages_no"] = $count_all / $this->records_per_page;
        
        $this->data["websites"] = FALSE;//$this->website_model->get_list(false,"","",$count_website = 0);
        
        // Load Views        
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/pagemanager/prebody', $this->data); 
        $this->load->view('admin/pagemanager/main', $this->data);        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }    
   
    /**
    * @method: page
    * @desc: The page method shows a page with the specified page id.
    * If no id code is given, it means it is a new page
    * 
    * @param mixed $page_code - The page id of the page to load.
    */
    function page($page_id = "")
    {
		$this->data["page_heading"] = "Page Details";
		$this->data['message'] = "";
      
		$postback = $this->tools_model->isPost();
            
        if ($postback)
        {
            $this->_handlePost($page_id,$missing_fields);
        }
        
        if($page_id != "") //edit
        { 	 
            // Load page details
            $page = $this->page_model->get_details($page_id,TRUE);
            if(!$page)
            {
                // The page could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the page could not be loaded.", "Page/show - the page with a code of '$page_id' could not be loaded");
                return;            
            }
            else
            {
                //pass page details
                $this->data["page"] = $page;
            }
            
	        // Get all available page blocks
	        $this->data["blocks"] = $this->blocks_model->get_list('','',$count,1,1);
            
	        // Get the blocks that are assigned to this page.
	        $this->data["blocks_right"] = $this->blocks_model->get_assigned_blocks($page_id, "page", "right");
            $this->data["blocks_left"] = $this->blocks_model->get_assigned_blocks($page_id, "page", "left");            
            
            $website_pages = array();
            $website_pages_result = $this->website_model->get_website_assm($page_id);
            if($website_pages_result)
            {
                foreach($website_pages_result->result() as $row)
                {
                    $website_pages[] = $row->website_id;
                }
            }
            
            $this->data["website_pages"] = $website_pages;
        }
        
        $this->data["websites"] = $this->website_model->get_list(false,"","",$count_website = 0);            
        
        if (!$postback)    
            $this->data['message'] = ($page_id == "") ? "To create a new page, enter the page details below." : "You are editing the page &lsquo;<b>$page->page_code</b>&rsquo;";
        
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data['page_id'] = $page_id;
        $this->data["robots"] = $this->utilities->get_robots();
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/page/prebody.php', $this->data); 
        $this->load->view('admin/page/main.php', $this->data);        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);              
    }
    
    function _handlePost($page_id, &$form_values)
    {
    	            	
		$data = array(  "page_code"         => '',
		                "page_title"       	=> '',
		                "enabled"           => '0',
		                "meta_title"        => '',
		                "meta_keywords"     => '',
		                "meta_description"  => '',
		                "page_body"         => '',
		                "view"              => '',
		                "meta_robots"       => '',
                        /*"background"        => '',
                        "image1"  	        => '',
                        "image1_caption"    => '',
                        "text_color"        => '',
                        "edit_page"         => '0',
                        //"flash_movie"       => ''*/
						"video_id"          => ''
		            );

        //-----upload background photo start-----
        
//        $this->load->helper(array('form', 'url'));
//        $config['upload_path'] = FCPATH.'images\admin\background';
//        $config['allowed_types'] = 'gif|jpg|png|jpeg';
//        $config['max_size']	= '0';
//        $config['max_width']  = '0';
//        $config['max_height']  = '0';
//
//        $page = $this->page_model->get_details($page_id,TRUE);
//        $_POST['background'] = $page->background;
//
//        $this->load->library('upload', $config);
//
//        if ( $this->upload->do_upload('background_img'))
//        {
//            $image_data = $this->upload->data();
//            $image_name = $image_data['orig_name'];
//
//            $_POST['background'] = $image_name;
//        }

        //-----upload background photo end-----
            
        $required_fields = array("page_code","page_title");
        $missing_fields = false;

        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0, false);

            if($key == "page_code")
            {
                $data[$key] = strtolower(str_replace(" ","-",$data[$key]));

	            //the page_code should be unique
	            if($this->page_model->exists_page_code($data[$key],$page_id))
	            {
	               $this->error_model->report_error("Sorry, please select an other page_code to continue.", "PageManager/HandlerPost update - the project with an id of '$data[$key]' allready exists.");
	               return;
	            }
			}

            // Ensure that all required fields are present
            if(in_array($key,$required_fields) && $data[$key] == "")
            {
                $missing_fields = true;
                break;
            }
        }

        if ($missing_fields)
        {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "PageManager/HandlerPost update - the page with a code of '$page_id' could not be saved");
            return;
        }

        //depeding on the $page_id do the update or insert
        $page_id = $this->page_model->save($page_id,$data);

        if(!$page_id)
        {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the page could not be saved/updated.", "Pagemanager/page save");
           return;
        }

        //save the page content in history
        $this->history_model->save_history($table = "pages", $page_id);

        // Update assigned blocks right
        /*if(isset($_POST["assigned_blocks_right"]))
        {
            $array_blocks = explode(",", $_POST["assigned_blocks_right"]);
            $this->blocks_model->update_assigned_blocks($page_id, $array_blocks, "page", "right");
        }*/

        // Update assigned blocks left
        if(isset($_POST["assigned_blocks_left"]))
        {
            $array_blocks = explode(",", $_POST["assigned_blocks_left"]);
            $this->blocks_model->update_assigned_blocks($page_id, $array_blocks, "page", "left");
        }

        //update website_pages
        /*if(isset($_POST["website_pages"]))
        {
           $this->website_model->add_website_assm($page_id, $_POST["website_pages"]);
        }
        
        if($_POST["page_code"] == "splash")
        {
        	// Check if there's a flash movie specified in image1
        	if((isset($_POST["flash_movie"])) && ($_POST["flash_movie"] != ""))
        	{
        		// Check if the specified file exists
        		$source = FCPATH . "files/" . $_POST["flash_movie"];
        		if(is_file($source))
        		{	
        			// Define dest path
        			$dest = FCPATH . "flash/video/video.flv";
        			
        			// Check file sizes of both
        			$source_size = filesize($source);
        			$dest_size = filesize($dest);

        			// If the files sizes are different, copy the flash movie to the proper destination.
        			if($source_size != $dest_size)
        			{
						copy($source, $dest);
        			}
				}
			}
        }*/

        redirect("/pagemanager/page/".$page_id);
            
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        
        switch($type)
        {
            //delete pages
            case 1:
            
                
                //get page ids separated with ";"
                $page_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($page_ids!="")
                {
                    $arr_ids = explode(";",$page_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id))
                        {
                            if ($where_in != "") $where_in.=",";
                            
                            $where_in .= $id;
                        }
                    }
                    
                    //$where_in = "(".$where_in.")";
                    if ($where_in!="")
                    {
                        $this->page_model->delete($where_in);
                    }
                }
                
                //get list of pages
                $pages = $this->page_model->get_list(true,false,$this->records_per_page,$current_page,$count_all);
                
                //load view 
                $this->load->view('admin/pagemanager/page_listing',array('pages'=>$pages,'pages_no' => $count_all / $this->records_per_page));
                
                
            break;
            
            //page number changed
            case 2:
                
                //get list of pages
                $pages = $this->page_model->get_list(true,false,$this->records_per_page,$current_page,$count_all);
                
                //load view 
                $this->load->view('admin/pagemanager/page_listing',array('pages'=>$pages,'pages_no' => $count_all / $this->records_per_page));
                
            break;
            
            //list all history time
            case 3:
                $return_data = array();
                
                $table = $this->tools_model->get_value("table","","post",0,false);
                $history_type = $this->tools_model->get_value("history_type","","post",0,false);
                $foreign_id = $this->tools_model->get_value("id","","post",0,false);
                
                $history = $this->history_model->get_list($table, $history_type, "", "", $count_all = 0,"", $foreign_id);                
                //echo $this->db->last_query();   
                $this->data["history"] = $history;
                
                //load view 
                $return_data['html'] = $this->load->view('admin/history_popup', array('history' => $history, 'history_type' => $history_type), true);
                
                // return the page
                echo json_encode( $return_data );
            break;
            
            case 4:
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
            
            case 5:
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
            // 3 - Search for pages
            case 6:
                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                $website_id = $this->tools_model->get_value("website","","post",0,false);
                
                //get list of pages
                $pages = $this->page_model->get_list(true,false,$this->records_per_page,$current_page,$count_all, $search_terms, $website_id);

                //load view 
                $this->load->view('admin/pagemanager/page_listing',array('pages'=>$pages,'pages_no' => $count_all / $this->records_per_page));
                
            break;
        }
    }
    

}
?>
