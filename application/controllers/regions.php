<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Regions extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Regions()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("area_model");
        $this->load->model("property_model");
        $this->load->model("project_model");
        $this->load->model("resources_model");
        $this->load->model("document_model");
        $this->load->model("link_model");
        $this->load->model("area_meta_model");
        $this->load->model("region_model");
        $this->load->model("state_model");
        
        $this->load->helper("image");
    }
    
    function index()
    {
        $this->data["meta_title"] = "Region List";
        
        $this->data["area_data"] = $this->area_model->get_area_min_max();
		
        $this->data["regions"] = $this->region_model->get_list(1, "", "", $count_all, "", "region_name ASC");
	
        $this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
               
        $this->load->view('member/header', $this->data);
        $this->load->view('member/region/list/prebody.php', $this->data); 
		$this->load->view('member/region/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    
	}
    
    function detail($region_id = "")
    {
        if(!is_numeric($region_id))
        {
            redirect("/stocklist");    
        }
                
    	// Load the area object
        $regions = $this->region_model->get_details($region_id);

    	// If the user could not be loaded, OR if the user was created by someone else
        // Forbid this action.
        if(!$regions)
        {
            redirect("/stocklist");    
        }
        
        $this->data["region"] = $regions;

        // Load the photo gallery for this area
        //$this->data["gallery"] = $this->document_model->get_list($doc_type = "area_gallery", $foreign_id = $area_id);
        
        // Load area documents
        //$this->data["docs"] = $this->document_model->get_list($doc_type = "area_document", $foreign_id = $area_id);
        
        // Load area links
        //$this->data["links"] = $this->link_model->get_list($link_type = "area_link", $foreign_id = $area_id);  
        
        // Load area metadata
        //$this->data["metadata"] = $this->area_meta_model->get_list(array("area_id" => $area_id)); 
        
        // Google maps image
        $this->data["map"] = "";
        
        //$area_folder = ABSOLUTE_PATH . "area_files/" . $area_id;
        // if(!is_dir($area_folder)) {
            // mkdir($area_folder);
            // chmod($area_folder, 0777);    
        // }
        
        //$map_image = "area_files/" . $area_id . "/map.png";
        //$map_image_abs = ABSOLUTE_PATH . $map_image;
        
        //if((!file_exists($map_image_abs)) && ($area->googlemap != ""))
        //{
            // Get the lat/lng pairs out of the embed url
            //$found = preg_match_all("/ll=[-\d\.]*,[-\d\.]*/", $area->googlemap, $matches);
            
            //if($found > 0)
            //{
                //$matched = $matches[0];
                // $num_matches = count($matched);
                // $latlng = str_replace("ll=", "", $matched[$num_matches - 1]);    
                // $url = "http://maps.googleapis.com/maps/api/staticmap?center=" . $latlng . "&zoom=12&size=640x452&sensor=true&key=" . GOOGLE_APIKEY;             
            // }
            
            // $map = file_get_contents($url);
            // if(strlen($map) > 100)
            // {
                // file_put_contents($map_image_abs, $map);    
            // }
        // }
        
        // if(file_exists($map_image_abs))
        // {
            // $this->data["map"] = $map_image;      
        // }
        
        $this->data["user_type_id"] = $this->user_type_id;
        $this->data["meta_title"] = "Region " . $regions->region_name;
        
        $this->load->model('Comment_model');
        //$this->data['comments'] = $this->Comment_model->get_list(array('foreign_id'=>$area_id,'type'=>'area_comment'));
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/region/detail/prebody.php', $this->data); 
        $this->load->view('member/region/detail/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function ajax()
    {
        // Prepare the return array
        $this->data = get_return_array();   // Defined in strings helper
        
        // Get the action that the user is trying to perform.
        $action = $this->input->post("action");
        
        // Handle the action.
        switch($action)
        {
            case "load_regionlist":   // User is trying to update their own account
                $this->handle_load_regionlist();
                break;
			
			case 'submit_comment':
                $this->handle_submit_comment();
                break;

			
                
            case "set_latlng":   // User is trying to update their own account
                $this->handle_set_latlng();
                break;	
				
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    private function handle_submit_comment()
    {
        $comment = $this->input->post('comment');
        $areaID = $this->input->post('area_id');
        $area = $this->area_model->get_details($areaID);
        if (!$area) {
        	send(array(
        	   'status' => 'FAILED',
        	   'message' => 'Unknown area.'
        	));
        } elseif (empty($comment)) {
        	send(array(
        	   'status' => 'FAILED',
        	   'message' => 'Comment is required.'
        	));
        } else {
            $this->load->model('Comment_model');
            $commentData = array(
                'foreign_id' => $areaID,
                'type' => 'area_comment',
                'user_id' => $this->user_id,
                'comment' => $comment,
                'datetime_added' => date('Y-m-d H:i:s'),
            );
            $commentID = $this->Comment_model->save(false, $commentData);
            if (!$commentID) {
            	send(array(
            	   'status' => 'FAILED',
            	   'message' => 'Error occurred while trying to add your comment. Please try again later.'
            	));
            } else {
                $comment = $this->Comment_model->get_details($commentID);
                $html = '<li>
                            <div class="ct">'.nl2br($comment->comment).'</div>
                            <div class="meta"><strong>'.trim("$comment->first_name $comment->last_name").'</strong> @ <em>'.date('d-m-Y h:ia', $comment->ts_added).'</em></div>
                        </li>';
            	send(array(
            	   'status' => 'SUCCESS',
            	   'html' => $html
            	));
            }
        }
    }
	
	private function handle_load_regionlist()
    {
	
		// Load neccessary libs and models
        $this->load->library('form_validation');
       
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        $this->form_validation->set_rules('list_type', 'List Type', 'required');            

        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
                       
        $current_page = $this->input->post("current_page");
        $list_type = $this->input->post("list_type");
        
        $search_fields = array("min_total_price", "max_total_price", "state_id");
        
        //$page_no = ($current_page - 1) * STOCKLIST_PER_PAGE;
        $page_no = $current_page;
		$limit = STOCKLIST_PER_PAGE;
        //$limit = $this->input->post('state_id');
        
        // On the map view, load all properties.
        if($list_type == "map")
        {
            $limit = 9999;   
            $page_no = 1;
        }         

		$regions = $this->region_model->get_list(1, "", "", $count_all, "", "region_name ASC");

        switch($list_type)
        {
            case "list":
                $this->data["message"] = $this->load->view("member/region/list/list", array("regions" => $regions), true);
                break;
                
            case "grid":
                $this->data["message"] = $this->load->view("member/region/list/grid", array("regions" => $regions), true);
                break;
                
            case "map":
                // For the map view, just send back the raw region data
                $array = array();

                if($regions)
                {
                    foreach($regions->result() as $region)
                    {
                        $item = array();
                        //$item["project_id"] = $project->project_id;
                        
                        $item["area_name"] = $region->region_name;
                        $item["state"] = $region->region_name;
                        $item["median_house_price"] = $region->median_house_price;
                        $item["rate"] = 'medium';
                        $item["url"] = base_url() . "region/detail/" . $region->region_id;
                        $item["lat"] = "";
                        $item["lng"] = "";
                        
                        // Get the lat/lng pairs out of the embed url
                        $found = preg_match_all("/ll=[-\d\.]*,[-\d\.]*/", $region->googlemap, $matches);
                        
                        if($found > 0)
                        {
                            $matched = $matches[0];
                            $num_matches = count($matched);
                            $latlng = str_replace("ll=", "", $matched[$num_matches - 1]);    
                            
                            $latlng = explode(",", $latlng);
                            if(count($latlng) == 2)
                            {                     
                                $item["lat"] = $latlng[0];
                                $item["lng"] = $latlng[1];                                
                            }
                        }                        

                        $item["image"] = null;
                        
                        if($region->region_hero_image != "")
                        {
                            $src = $region->region_hero_image;
                            $item["image"] = image_resize($src, 196, 130);                
                        }          
                        
                        // Add the item to the property array
                        $array[] = $item;
                    }
                }        
            
                $this->data["message"] = $array;
                break;                                
                
            default:
                $this->data["message"] = "Invalid List Type";
                send($this->data);
                break;                
        }
        
        $this->data["status"] = "OK";
        $this->data["count_all"] = $count_all;
		
        send($this->data);
    } 
    
}