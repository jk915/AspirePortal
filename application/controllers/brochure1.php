<?php
class Brochure extends CI_Controller 
{
    public  $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    private $agent_id = 0;
    
    function Brochure()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("property_model");
        $this->load->model("project_model");
        $this->load->model("reservation_model");
        $this->load->model("document_model");
        $this->load->model("users_model");
        $this->load->helper("pdf");
        
        //if(!$this->utilities->is_agent_logged_in($this))
        	//show_error("Sorry, your session has expired. Please login again.");
        
        // Get the details of the logged in agent
        
        //$this->agent_id = $this->login_model->getSessionData("id", "user");
        $this->agent_id = $this->session->userdata('agent_id');
        
    }
	
    function show($property_id)
    {
    	
		$pdf_data = array();
		
		// Load the relevant property
        $property = $this->property_model->get_details($property_id);
        
        if(!$property)
            die("Invalid property");
            
    	$agent = $this->users_model->get_details($this->agent_id);
        if(!$agent)
            die("Invalid agent");    	
    	
    	
        $selected_project = $this->property_model->get_projects($property_id);
        
        if($selected_project)
        {
            $project_id = $selected_project[0];   
            $project = $this->project_model->get_details($project_id);
        }   

            
        //fodler name exits ?
        $folder_path = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id . "/brochure";
        
        if (!is_dir($folder_path))
        {
            //create folder
            mkdir($folder_path);
            chmod($folder_path,0777);
        }
        
        $user_id = $property->user_id;
            
		// Paths
		$pdf_data["SAVE_PATH"] = $folder_path."/brochure.pdf";
		$pdf_data["LINK_PATH"] = base_url() . PROPERTY_FILES_FOLDER . $property_id . "/brochure/brochure.pdf";
		
		// Images
		$pdf_data["HERO_IMAGE"]	= ($property && $property->hero_image !="") ? ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id . "/images/" . $property->hero_image : "";
        $pdf_data["PROJECTLOGO_IMAGE"]	= ($project && $project->logo_print != "") ? ABSOLUTE_PATH . PROJECT_FILES_FOLDER . $project_id. "/" . $project->logo_print : "";
        $plan_images = $this->document_model->get_floorplan_list("property_document", $property_id);
        if ($plan_images) {
            $pdf_data["PLAN_IMAGE"] = $plan_images["0"];
        } else {
            $pdf_data["PLAN_IMAGE"] = $plan_images;
        }
        if ($this->agent_id !="") 
		{
			$logo_path_abs = ABSOLUTE_PATH . "uploads/logos/" . $this->agent_id . ".jpg"; 

			if(file_exists($logo_path_abs)) 
			{
            	$pdf_data["AGENT_LOGO"] = $logo_path_abs;
			}
            else
            	$pdf_data["AGENT_LOGO"] = "";	
		}
        else
            $pdf_data["AGENT_LOGO"]	= "";
            
		$pdf_data["COMPANY_LOGO"]	= ABSOLUTE_PATH . "images/PropertyFocus_logo.jpg";
        
		// Text strings
		$pdf_data["HEADER_TEXT"] = strtoupper("Lot ".$property->lot.", ".$property->address.", ".$property->suburb." ".$property->state);
		$pdf_data["PROJECT_TYPE"] = $project->project_type;
		// Specifications area
		$pdf_data["PROPERTY_SHORT_DESCRIPTION"] = preg_replace("/[\n\r]/","", html_entity_decode(strip_tags( $property->overview ), ENT_QUOTES));
		
		$due_date = $property->title_due_date;
        if(strlen($due_date) != 6) {
            $due_date = "";
        } else {
			$year = substr($due_date, 0, 4);
			$month = substr($due_date, 4, 2);
			$due_date = $month . "/" . $year;
        }
		$pdf_data["PROPERTY_TITLED_DATE"] = "$due_date";
		$pdf_data["PROPERTY_LAND_AREA"] = ($property->internal_area) ? "$property->internal_area"." sqm" : "-" ;
		$pdf_data["PROPERTY_HOUSE_AREA"] = ($property->total_area) ? "$property->total_area"." sqm" : "-" ;
		$pdf_data["PROPERTY_PRICE_FROM"] = ($property->total_price) ? "$". number_format($property->total_price,0) : "-" ;
		$pdf_data["PROPERTY_PRICE_FROM"] = ($property->total_price) ? "$". number_format($property->total_price,0) : "-" ;
		$pdf_data["PROPERTY_HOUSE_DESIGN"] = ($property->design) ? "$property->design" : "-" ;
		
		$pdf_data["PROPERTY_NOBATHROOMS"] = ($property->bathrooms != -1) ? "$property->bathrooms" : "-";
		$pdf_data["PROPERTY_NOBEDROOMS"] = ($property->bedrooms != -1) ? "$property->bedrooms" : "-";
		$pdf_data["PROPERTY_NOGARAGES"] = ($property->garage != -1) ? "$property->garage" : "-" ;
		$disclaimer = "Copyright M Homes ".date("Y").". All figures and dimensions are approximate only. Please refer to building plans for exact dimensions. House dimensions may change depending on facade chosen and developers or councils approval. ";
		$pdf_data["PROPERTY_DISCLAIMER"] = preg_replace("/[\n\r]/","", html_entity_decode(strip_tags( $disclaimer ), ENT_QUOTES));

        //create the pdf
		if (make_brochure($pdf_data))
        {
            $this->utilities->download_file($pdf_data["SAVE_PATH"]);
        }
    }
}  