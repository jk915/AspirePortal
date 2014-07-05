<?php
class Blockmanager extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	private $records_per_page = ITEMS_PER_PAGE;
	
	function __construct()
	{
		parent::__construct();
		
		// Create the data array.
		$this->data = array();			
		
		// Load models etc
		$this->load->model("block_model");
        $this->load->model("history_model");				
		
		// Check for a valid session
		if (!$this->login_model->getSessionData("logged_in"))
			redirect("login");
			
		// Remove all items from the cache to ensure any content changes are reflected on the site
		cache_flush();			       
	}
	
	function index()
	{
		// Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "Block Manager";
		$this->data["page_heading"] = "Block Manager";
		$this->data["name"] = $this->login_model->getSessionData("firstname");

        $this->data["blocks"] = $this->block_model->get_list($this->records_per_page,1,$count_all);
		$this->data["pages_no"] = $count_all / $this->records_per_page;
		
		// Load Views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/blockmanager/prebody', $this->data); 
		$this->load->view('admin/blockmanager/main', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data); 
	}
	
	/**
	* @method: user
    * @version 1.0  
    * 
	* @desc: The user method shows a user with the specified user id.
	* If no id code is given, it means it a new user is going to be created.
	* 
	* @param integer $user_id - The user id of the user to load.
	*/
	function block($block_id = "")
	{
		$this->data["page_heading"] = "Block Details";
		$this->data['message'] = "";
		
		$postback = $this->tools_model->isPost();
		
		if ($postback)
		{
			$this->_handlePost($block_id);
		}
		
		if($block_id != "") //edit
		{
			// Load page details
			$block = $this->block_model->get_details($block_id);
			if(!$block)
			{
				// The page could not be loaded.  Report and log the error.
				$this->error_model->report_error("Sorry, the block could not be loaded.", "Block/block - the block with an id of '$block_id' could not be loaded");
				return;			
			}
			else
			{
				//pass page details
				$this->data["block"] = $block;
			}
		}
		
		if (!$postback)	
			$this->data['message'] = ($block_id == "") ? "To create a new block, enter the block details below." : "You are editing the page &lsquo;<b>$block->block_name</b>&rsquo;";
			
		// Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "Website Administration Menu";
		$this->data['block_id'] = $block_id;
		
		// Load views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/block/prebody.php', $this->data); 
		$this->load->view('admin/block/main.php', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data);  		
		
	}
	
	
	function _handlePost($block_id)
	{
		
			$data = array(	"block_name"		=> '',
							"block_description"	=> '',
							"block_content"		=> '',
							"enabled"			=> '0',
                            "show_on_sidebar"   => '0'//,
                            //"hide_heading"	    => '0'
						);
			
			$required_fields = array("block_name");
			$missing_fields = false;
			
			//fill in data array from post values
			foreach($data as $key=>$value)
			{
				$data[$key] = $this->tools_model->get_value($key, "", "post", 0, false);
				
				// Ensure that all required fields are present	
				if(in_array($key,$required_fields) && $data[$key] == "")
				{
					$missing_fields = true;
					break;
				}
			}
			
			if(isset($data["block_content"]))
			{
				$data["block_content"] = str_replace("&lt;", "<", $data["block_content"]);
				$data["block_content"] = str_replace("&gt;", ">", $data["block_content"]);
			}
			
			if ($missing_fields)
			{
				$this->error_model->report_error("Sorry, please fill in all required fields to continue.", "BlockManager/HandlerPost update - the page with a code of '$page_id' could not be saved");
				return;
			}
			
			//depeding on the $page_id do the update or insert
			$block_id = $this->block_model->save($block_id,$data);
			if(!$block_id)
            {
               // Something went wrong whilst saving the user data.
               $this->error_model->report_error("Sorry, the block could not be saved/updated.", "Blockmanager/page save");
               return;
            }
            
             //save the page content in history 
            $this->history_model->save_history($table = "custom_blocks", $block_id);
				
			redirect("/admin/blockmanager/block/$block_id");
			
	}
	
	//handles all ajax requests within this page
	function ajaxwork()
	{
		$type = intval($this->tools_model->get_value("type",0,"post",0,false));
		$current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
		
		switch($type)
		{
			//delete blocks
			case 1:
			
				
				//get block ids separated with ";"
				$block_ids = $this->tools_model->get_value("todelete","","post",0,false);
				
				if ($block_ids!="")
				{
					$arr_ids = explode(";",$block_ids);
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
						$this->block_model->delete($where_in);
					}
				}
				
				//get list of blocks
				$blocks = $this->block_model->get_list($this->records_per_page,$current_page,$count_all);
				
				//load view 
				$this->load->view('admin/blockmanager/block_listing',array('blocks'=>$blocks,'pages_no' => $count_all / $this->records_per_page));
				
				
			break;
			
			//page number changed
			case 2:
				
				//get list of pages
				$blocks = $this->block_model->get_list($this->records_per_page,$current_page,$count_all);
				
				//load view 
				$this->load->view('admin/blockmanager/block_listing',array('blocks'=>$blocks,'pages_no' => $count_all / $this->records_per_page));
				
			break;
            
            case 3: // return the block page
                $return_data = array();
                
                // get list of blockes
                $blocks = $this->block_model->get_list($this->records_per_page, $current_page, $count_all);
                
                $this->data['blocks'] = $blocks;
                $this->data['pages_no'] = $count_all / $this->records_per_page;
                
                $return_data['html'] = $this->load->view( 'admin/blockmanager/new_block_popup', $this->data, TRUE );
                
                // return the page
                echo json_encode( $return_data );
            break;
            
            case 4: //return the video page where the user can enter a Video Id
                $return_data = array();
                
                $return_data['html'] = $this->load->view( 'admin/blockmanager/new_video_popup', $this->data, TRUE );
                
                // return the page
                echo json_encode( $return_data );
            break;
		}
	}

}
?>
