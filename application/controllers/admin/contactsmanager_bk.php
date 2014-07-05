<?php
/** contacts
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
* @property Tools_model $tools_model
* @property property_model $property_model
*/

class Contactsmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    private $documents_records_per_page = 3;	
    private $images_records_per_page = 3;  
    private $doc_type = "lawyer_document";
    public $count = 0;
    function __construct()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("panel_contacts_model");
        $this->load->model("document_model");
        $this->load->model("contact_model");
        $this->load->model("comment_model","commentmd");
		//$this->load->model('property_lawyer_model');
        $this->load->library("utilities");    
        $this->load->library("image");
                
        //if the $ci_session is passed in post, it means the swfupload has made the POST, don't check for login
        $ci_session = $this->tools_model->get_value("ci_session","","post",0,false);
      
        if ($ci_session == "")
        {

           // Check for a valid session
            if (!$this->login_model->getSessionData("logged_in"))            
                redirect("admin/login");       
        }    
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Contacts Manager";
        $this->data["page_heading"] = "Contacts Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");

        $this->data["panel_contacts"] = $this->panel_contacts_model->get_list(-1,$this->records_per_page,1,$count_all);
		
		$this->data["pages_no"] = $count_all / $this->records_per_page;

		       
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/contactsmanager/prebody', $this->data); 
        $this->load->view('admin/contactsmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
	
	function contact($contacts_id='')
    {
        $this->load->model("property_model");
        
        $this->data['message'] = "";
        $postback = $this->tools_model->isPost();    
        
        if ($postback) {
            $this->_handlePost($contacts_id);
        }
        
        if($contacts_id != "") { //edit
            // Load page details
            $builder = $this->panel_contacts_model->get_details($contacts_id);
			print_r($builder);
            $documents = $this->document_model->get_list($this->doc_type, $contacts_id);
            
            if(!$builder) {
                // The page could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the contacts could not be loaded.", "contacts/show - the contacts with an id of '$contacts_id' could not be loaded");
                return;            
            } else {
                //pass page details
                $this->data["builder"] = $builder; 
				
                $this->data["documents"] = $documents;
				$this->data["documents_records_per_page"] = $this->documents_records_per_page;
                $this->data["states"] = $this->property_model->get_states(1);
				
                $this->data['contacts'] = $this->contact_model->get_list(array('foreign_id'=>$contacts_id, 'type' => "lawyer_contact"));
                $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$contacts_id, 'type' => "lawyer_comment"));
            }
        }
        
        if(!$postback)    
            $this->data['message'] = ($contacts_id == "") ? "To create a new contacts, enter the contacts details below." : "You are editing the &lsquo;<b>$builder->contacts_name</b>&rsquo;";
        
        // Define page variables
        
        $this->data["meta_keywords"] = "Contacts Manager";
        $this->data["meta_description"] = "Contacts Manager";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data["page_heading"] = ($contacts_id != "" && isset($builder)) ? $builder->contacts_name : "Contacts Details";
        
        $this->data['contacts_id'] = $contacts_id;
        $this->data["robots"] = $this->utilities->get_robots();
		$this->data["states"] = $this->property_model->get_states(1);
        
        if($contacts_id != "") { //edit
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER)) //FCPATH
                @mkdir(FCPATH.BUILDER_FILES_FOLDER,DIR_WRITE_MODE);   
             
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id))
                @mkdir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id, DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id."/documents"))
                @mkdir(FCPATH.BUILDER_FILES_FOLDER.$contacts_id."/documents",DIR_WRITE_MODE);

			//$this->data['property_lawyers'] = $this->property_lawyer_model->get_property_lawyer($contacts_id);
/*
			if($this->data['property_lawyers'])
			{
				foreach($this->data['property_lawyers']->result() as $property_lawyers)
				{
					if($property_lawyers->status == 'reserved' || $property_lawyers->status == 'EOI Payment Pending' || $property_lawyers->status == 'signed' || $property_lawyers->status == 'sold')
					{
						$this->count++;			
					}
				}
				if($this->count>0)
				{
					$this->data['count'] = $this->count;
				}
				else
				{
					$this->data['count'] = 0;
				}
			}
			//$this->lawyer_model->update_lawyer_transaction_count($contacts_id,$this->count);	
*/			
        }
        
		// Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/contacts/prebody.php', $this->data); 
        $this->load->view('admin/contacts/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function _handlePost($contacts_id)
    {
        
		// echo "<pre>";print_r($_POST);
		
		$data = array(    
                        "contacts_name"               => '',
                        "contacts_content"            => '',
                        "acn"                       => '',
                        "abn"                       => '',
                        "history"                   => '',
                        "summary"                   => '',
                        "year_established"          => 0,
                        "count_number_transactions" => 0,
                        "enabled"                   => '0',
						"approved_state_id"         => '',
						"billing_address1"  		=> '',
						"billing_address2"   		=> '',
						"billing_suburb"            => '',
						"billing_postcode"          => '',
						"billing_state_id"          => '',
						"billing_country_id"        => '',
						"billing_phone"          	=> '',
						"billing_fax"				=> '',
						"postal_address"            => '',
						"postal_suburb"             => '',
						"postal_postcode"           => '',
						"postal_state_id"           => ''
                   );
				   
				   
							
                    
        $required_fields = array("contacts_name");
        $missing_fields = false;
        
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
            // Ensure that all required fields are present    
            if(in_array($key,$required_fields) && $data[$key] == "") {
                $missing_fields = true;
                break;
            }
        }
        
        if ($missing_fields) {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "Contacts Manager/HandlerPost update - the builder with an id of '$contacts_id' could not be saved");
            return;
        }
        
        $edit_contacts = false;
        
        if(is_numeric($contacts_id)) {
            $edit_contacts = true;
        }
        
        $data["last_modified"] = date("Y-m-d H:i:s");
        $data["last_modified_by"] = $this->login_model->getSessionData("id");
        
		
		$string = ',';
 
		 if(isset($_POST['approved_state_id']) && is_array($_POST['approved_state_id'])){
		  
		  foreach($_POST['approved_state_id'] as $val){
		   $string .= $val.",";
		  }
		  	 
		  $data['approved_state_id'] = $string;
		 }
		 #echo $string;exit;
		
		
		//depeding on the $contacts_id do the update or insert
        $contacts_id = $this->contacts_model->save($contacts_id,$data);
        
        if(!$contacts_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the contact could not be saved/updated.", "Contacts Manager/contact save");
           return;
        }
        
        if(!$edit_contacts) {
            $a = $this->document_model->add_default_documents($this->doc_type, $contacts_id);
        } 
		/*
		else {
            //save documents
            $documents = $this->document_model->get_list($this->doc_type,$contacts_id);

			
            if($documents)
            {
                foreach($documents->result() as $doc)
                {
                   $doc_name = $this->tools_model->get_value("doc_".$doc->id."_name","","post",0,false); 
                   $extra_data = $this->tools_model->get_value("doc_".$doc->id."_extra_data","","post",0,false); 
                  
                   $doc_data = array(
                        "document_name" => $doc_name,
                        "extra_data" => $extra_data,
                   );
				   
                   $this->document_model->save($doc->id,$doc_data, $contacts_id);
                }
            }			
        }*/
        
        redirect("/admin/panelmanager/panel/$contacts_id");
    }
    
    
}

?>
