<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Australia extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    public $australia_id = '1';
    function Australia()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("australia_model");
        $this->load->model("property_model");
        $this->load->model("project_model");
        $this->load->model("resources_model");
        $this->load->model("document_model");
        $this->load->model("link_model");
        $this->load->model("area_meta_model");
        $this->load->model("state_model");
        
        $this->load->helper("image");
    }
    
    function index()
    {
        
		$australia_id = $this->australia_id;
		if(!is_numeric($australia_id))
        {
            redirect("/stocklist");    
        }
                
    	// Load the area object
        $australia = $this->australia_model->get_details();
        
    	// If the user could not be loaded, OR if the user was created by someone else
        // Forbid this action.
        if(!$australia)
        {
            redirect("/stocklist");    
        }
        
        $this->data["australia"] = $australia;

        // Load the photo gallery for this area
        $this->data["gallery"] = $this->document_model->get_list($doc_type = "australia_gallery", $foreign_id = $australia_id);
        
        // Load area documents
        //$this->data["docs"] = $this->document_model->get_list($doc_type = "area_document", $foreign_id = $area_id);
        
        // Load area links
        //$this->data["links"] = $this->link_model->get_list($link_type = "area_link", $foreign_id = $area_id);  
        
        // Load area metadata
        //$this->data["metadata"] = $this->area_meta_model->get_list(array("area_id" => $area_id)); 
        
        // Google maps image
        $this->data["map"] = "";
        
        $australia_folder = ABSOLUTE_PATH . "australia_files/" . $australia_id;
        if(!is_dir($australia_folder)) {
            mkdir($australia_folder);
            chmod($australia_folder, 0777);    
        }
        
        $this->data["user_type_id"] = $this->user_type_id;
        $this->data["meta_title"] = "Australia " . $australia->australia_name;
        
        $this->load->model('Comment_model');
        //$this->data['comments'] = $this->Comment_model->get_list(array('foreign_id'=>$area_id,'type'=>'area_comment'));
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/australia/detail/prebody.php', $this->data); 
        $this->load->view('member/australia/detail/main.php', $this->data);
        $this->load->view('member/footer', $this->data);
		
		
	}

}
