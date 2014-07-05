<?php
class Settingsmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    public $settings_fields = array(  
                "company_name"      => '',
                "address1"          => '',
                "address2"          => '',
                "suburb"            => '',
                "postcode"          => '',
                "state"             => '',
                "country"           => '',
                "skype"          => '',
                "facebook"          => '',
                "twitter"           => '',
                "vimeo"           => '',
                "youtube"           => '',
                "linkedin"          => '',
                "email"             => '',
                "enabled"           => '',
    			"tax_id"			=> '',
    			"tax_gst"			=> '',
    			"payment_gateway_id"=> '',
                "analytics_id" => '',
    			"gateway_id"		=> '',
    			"phone"				=> '',
    			"fax"				=> '',
    			"fax"				=> '',
    			"mailchimp_api_key"				=> '',
    			"mailchimp_list_id"		=> ''
            );
    
    function Settingsmanager()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("settings_model");            
        $this->load->model("website_model");            
        
        $this->load->library("utilities");    
      
        // Check for a valid session
        if (!$this->login_model->getSessionData("logged_in"))
           redirect("login");       
    }
    
    function index()
    {
        
        $postback = $this->tools_model->isPost();
        
        if ($postback)
        {
            $this->_handlePost();
        }
        else
        {
            //check if thde owner details fields are in database else insert them
            $this->settings_model->add_fields("owner_details", $this->settings_fields);
        }    
        
        $this->data["owner_details"] = $this->settings_model->get_details_array("owner_details");        
        $this->data["contacts"] = $this->settings_model->get_contacts();
        $this->data["states"] = $this->tools_model->get_states();
        $this->data["countries"] = $this->tools_model->get_countries();
        $this->data["websites"] = $this->website_model->get_list(array());
        $this->data['payment_gateways'] = $this->tools_model->get_payment_gateways();
        
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Global Settings Manager";
        $this->data["page_heading"] = "Global Settings Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");        
        
        // Load Views        
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/settingsmanager/prebody', $this->data); 
        $this->load->view('admin/settingsmanager/main', $this->data);        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    function _handlePost()
    {
            $data = $this->settings_fields;
            
            $required_fields = array();
            $missing_fields = false;
            
            //fill in data array from post values
            foreach($data as $key=>$value)
            {
                $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
                    
                // Ensure that all required fields are present    
                if(in_array($key,$required_fields) && $data[$key] == "")
                {
                    $missing_fields = true;
                    break;
                }
            }
            
            if ($missing_fields)
            {
                $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "SettingsManager/HandlerPost update");
                return;
            }
            
            //depeding on the $page_id do the update or insert
            $is_success = $this->settings_model->save("owner_details", $data);
            
            if(!$is_success)
            {
               // Something went wrong whilst saving the user data.
               $this->error_model->report_error("Sorry, the settings could not be saved/updated.", "Settings Manager/settings save");
               return;
            }
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        
        switch($type)
        {
            //delete contacts
            case 1:
            
                //get contact ids separated with ";"
                $contact_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($contact_ids!="")
                {
                    $arr_ids = explode(";",$contact_ids);
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
                        $this->settings_model->delete_contacts($where_in);
                    }
                }
                
                //get list of contact
                $contacts = $this->settings_model->get_contacts();
                
                //load view 
                 $return_data['html'] = $this->load->view('admin/settingsmanager/contact_listing',array('contacts'=>$contacts),true);
                
                // return the page
                echo json_encode( $return_data );
                
            break;
            //list contact settings
            case 3:
                $return_data = array();
                $first_name = $this->tools_model->get_value("first_name","","post",0,false);
                $last_name = $this->tools_model->get_value("last_name","","post",0,false);
                $email = $this->tools_model->get_value("email","","post",0,false);
                //$website_id = intval($this->tools_model->get_value("website_id",-1,"post",0, false));
                
                /*if($website_id != -1)
                {*/
                    $data_contact =  array(
                        "first_name" => $first_name,
                        "last_name" => $last_name,
                        "email" => $email//,
                        //"website_id" => $website_id
                    );
                    $this->settings_model->save_contact("",$data_contact);
                //}
                
                //get list of contact
                $contacts = $this->settings_model->get_contacts();
                
                //load view 
                 $return_data['html'] = $this->load->view('admin/settingsmanager/contact_listing',array('contacts'=>$contacts),true);
                
                // return the page
                echo json_encode( $return_data );
            break;
            //save notifications
            case 4:
                $contact_notification = $this->tools_model->get_value("contact_notification","","post",0,false);
                $order_notification = $this->tools_model->get_value("order_notification","","post",0,false);
                
                $this->settings_model->delete_all_notification();
                
                //save contact notification
                if ($contact_notification != "")
                {
                    $arr_ids = explode(";",$contact_notification);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id))
                        {
                            if ($where_in != "") $where_in.=",";
                            
                            $where_in .= $id;
                        }
                    }
                    
                    $this->settings_model->update_notification($where_in, "contact_notification");                
                }
                /*else
                {
                	// The user did not check any checkboxes so clear all contact notifications
                	$this->settings_model->update_notification("", "contact_notification"); 
				}*/
                
                //save order notification
                if ($order_notification != "")
                {
                    $arr_ids = explode(";",$order_notification);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id))
                        {
                            if ($where_in != "") $where_in.=",";
                            
                            $where_in .= $id;
                        }
                    }
                    
                    $this->settings_model->update_notification($where_in, "order_notification");                
                }
                
                //get list of contact
                $contacts = $this->settings_model->get_contacts();
                
                //load view 
                $return_data['html'] = $this->load->view('admin/settingsmanager/contact_listing',array('contacts'=>$contacts),true);
                
                // return the page
                echo json_encode( $return_data );
            break;
        }
    }
}
?>
