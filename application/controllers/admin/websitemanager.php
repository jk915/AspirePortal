<?php
class Websitemanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("website_model");            
        $this->load->model("resources_model");                              
        $this->load->model("languages_model");  
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Website Manager";
        $this->data["page_heading"] = "Website Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        $this->data["websites"] = $this->website_model->get_list(array());
        $this->data["pages_no"] = 1;
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/websitemanager/prebody', $this->data); 
        $this->load->view('admin/websitemanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    /**
    * @method: website
    * @desc: The website method shows a website with the specified website id.
    * If no id code is given, it means it is a new website
    * 
    * @param mixed $website_id - The website id of the page to load.
    */
    function website($website_id = "")
    {
        $this->data["page_heading"] = "Website Details";
        $this->data['message'] = "";
      
        $postback = $this->tools_model->isPost();
            
        if ($postback)
        {
            $this->_handlePost($website_id,$missing_fields);
        }
        
        if($website_id != "") //edit
        {      
            // Load website details
            $website = $this->website_model->get_details($website_id);
            if(!$website)
            {
                // The website could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the website could not be loaded.", "Website/show - the website with a code of '$website_id' could not be loaded");
                return;            
            }
            else
            {
                //pass page details
                $this->data["website"] = $website;
            } 
            
            if(!is_dir(FCPATH."files/website"))
                @mkdir(FCPATH."files/website", DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH."files/website/".$website_id))
                @mkdir(FCPATH."files/website/".$website_id, DIR_WRITE_MODE);                                  
                        
        }
        
        $this->data["languages"] = $this->languages_model->get_list("language");                      
        $this->data["fonts"] = $this->resources_model->get_list("font");
        $this->data["font_sizes"] = $this->resources_model->get_list("font-size");
            
        
       /* if (!$postback)    
            $this->data['message'] = ($website_id == "") ? "To create a new website, enter the website details below." : "You are editing the page &lsquo;<b>$page->page_code</b>&rsquo;";
        */
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data['website_id'] = $website_id;
        $this->data["robots"] = $this->utilities->get_robots();
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/website/prebody.php', $this->data); 
        $this->load->view('admin/website/main.php', $this->data);        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);              
    }
    
    function _handlePost($website_id,&$form_values)
    {
                        
        $data = array(  
                        "website_name"        => '',
                        "url_id"              => '',
                        "external_url"        => '',
                        "lang_id"             => '1',
                        "start_date"          => '',
                        "expiry_date"         => '',
                        "enabled"             => ''
                    );
            
        $required_fields = array("website_name","url_id");
        $missing_fields = false;
        
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
            
            if($key == "start_date" || $key == "expiry_date")
            {
                if($data[$key] != "")                           
                    $data[$key] = $this->utilities->uk_to_isodate($data[$key]);                    
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
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "WebsiiteManager/HandlerPost update - the website with a code of '$website_id' could not be saved");
            return;
        }
        
        $website_id = $this->website_model->save($website_id,$data);

        if(!$website_id)
        {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the website could not be saved/updated.", "WebsiteManager/website save");
           return;
        }
                        
        redirect("/admin/websitemanager");
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        
        switch($type)
        {
            //delete websites
            case 1:
                             
                //get website ids separated with ";"
                $website_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($website_ids!="")
                {
                    $arr_ids = explode(";",$website_ids);
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
                        $this->website_model->delete($where_in);
                    }
                }
                                
                //get list of websites
                $websites = $this->website_model->get_list(array());                            
                
                //load view 
                $this->load->view('admin/websitemanager/website_listing',array('websites'=>$websites,'pages_no' => 1));                
                
            break;
            
            //page number changed
            case 2:
                
                //get list of websites
                $websites = $this->website_model->get_list(true, $this->records_per_page, $current_page, $count_all);                            
                
                //load view 
                $this->load->view('admin/websitemanager/website_listing',array('websites'=>$websites,'pages_no' => $count_all / $this->records_per_page));                
                
            break;
        }
    }    
}  
