<?php
class Testimonialmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("testimonials_model","testmd");            
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Testimonial Manager";
        $this->data["page_heading"] = "Testimonial Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        $this->data["testimonials"] = $this->testmd->get_list(array());
        $this->data["pages_no"] = 1;
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/testimonialmanager/prebody', $this->data); 
        $this->load->view('admin/testimonialmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    function testimonial($test_id = "")
    {
        $this->data["page_heading"] = "Testimonial Details";
        $this->data['message'] = "";
      
        $postback = $this->tools_model->isPost();
            
        if ($postback) {
            $this->_handlePost($test_id,$missing_fields);
        }
        
        if($test_id != "") //edit
        {      
            $testimonial = $this->testmd->get_details($test_id);
            if(!$testimonial) {
                $this->error_model->report_error("Sorry, the testimonial could not be loaded.", "Testimonial/show - the testimonial with a code of '$test_id' could not be loaded");
                return;            
            } else {
                //pass page details
                $this->data["testimonial"] = $testimonial;
            } 
        }
        
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Testimonial Administration Menu";
        $this->data['testimonial_id'] = $test_id;
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/testimonial/prebody.php', $this->data); 
        $this->load->view('admin/testimonial/main.php', $this->data);        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);              
    }
    
    function _handlePost($test_id,&$form_values)
    {
                        
        $data = array(  
                        "author"        => '',
                        "company"              => '',
                        "quote"        => '',
                        "order"             => '0',
                        "enabled"             => ''
                    );
            
        $required_fields = array("author","quote","company");
        $missing_fields = false;
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
            
            if(in_array($key,$required_fields) && $data[$key] == "") {
                $missing_fields = true;
                break;
            }
        }
        
        if ($missing_fields) {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "Testimonial/HandlerPost update - the testimonial with a code of '$test_id' could not be saved");
            return;
        }
        $test_id = $this->testmd->save($test_id,$data);

        if(!$test_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the testimonial could not be saved/updated.", "TestimonialManager/testimonial save");
           return;
        }
                        
        redirect("/admin/testimonialmanager");
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        
        switch($type)
        {
            case 1:
                
                $message = 0;
                $test_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($test_ids!="") {
                    $arr_ids = explode(";",$test_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id)) {
                            if ($where_in != "") $where_in.=",";
                            $where_in .= $id;
                        }
                    }
                    
                    if ($where_in!="") {
                        $this->testmd->delete($where_in);
                        $message = 1;
                    }
                }
                echo $message;
            break;
            
            case 2:
             	$message = "OK";
             	$testimonial_id = $this->tools_model->get_value("testimonial_id", 0, "post", 0, false);
             	$direction = $this->tools_model->get_value("direction", 0, "post", 0, false);
             	
             	if((($testimonial_id == "") || (!is_numeric($testimonial_id))) || (($direction != "up") && ($direction != "down")))
             	{
    					$message = "ERROR";
             	}
             	else
    				{
    					$testimonial = $this->testmd->get_details($testimonial_id, FALSE);
    					
    					if(!$testimonial)
    					{
    						$message = "ERROR";	
    					}
    					else
    					{
    						// Load all of the articles in this category, in article_order order.
    						$testimonials = $this->testmd->get_list(array());
    						
    						// Create an array to hold the re-ordered items
     						$items_array = array();
    						
    						$seqno = 10;	// Starting sequence number
    						
    						// Loop through all articles in the category
    						foreach($testimonials->result() as $row)
    						{
    							// If this article is the article the user is trying to reorder,
    							// modify the sequence number +- 15 according to direction.
    							if($row->id == $testimonial_id)
    							{
    								if($direction == "up")
    									$items_array[$seqno - 15] = $row->id;
    								else
    									$items_array[$seqno + 15] = $row->id;  	
    							}
    							else
    							{
    								// This is not the article the user is trying to reorder.
    								$items_array[$seqno] = $row->id; 	
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
    							$this->testmd->save($item_id, array("order" => $seqno));
    							$seqno++;
    						}
    					}
    							
    				}
             	
                $return_data = array();
                $return_data["message"] = $message;
                echo json_encode($return_data);         	
             	
         	break;
         	
         	case 3: //refresh testimonials list

                $testimonials = $this->testmd->get_list(array());
                $return_data["html"] = $this->load->view('admin/testimonialmanager/testimonial_listing',array('testimonials' => $testimonials),true); 
                echo json_encode($return_data);
            break;
        }
    }    
}  
