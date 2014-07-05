<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD. "," . USER_TYPE_ADMIN);

class Brochure extends MY_Controller 
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
        $this->load->model("area_model");
        $this->load->model("australia_model");
        $this->load->model("reservation_model");
        $this->load->model("document_model");
        $this->load->model("users_model");
        $this->load->model("state_model");
        $this->load->model("region_model");
        $this->load->helper("pdf");
    }
	
    /***
    * Generate a PDF brochure for a specific property
    * 
    * @param int $property_id The id of the property to generate the brochure for.
    */
    function property($property_id=0)
    {
		$pdf_data = array();
        
        // Load the user object for the logged in user
        $agent = false;
        
        $this_user = $this->users_model->get_details($this->user_id);
        if(!$this_user)
        {
            die("Invalid user");        
        }          
        
        // If this user is an agent / advisor, set the agent object directly, otherwise find the agent for this user
        if($this->user_type_id == USER_TYPE_ADVISOR)
        {
            $agent = $this_user;    
        }
        else
        {
            $agent = $this->users_model->get_details($this_user->advisor_id);
                
            if(!$agent)
            {
                die("Couldn't load agent");    
            }
        }

		// Load the relevant property
        $property = $this->property_model->get_details($property_id);
		$this->data["property_data"] = $this->property_model->get_property_min_max();
        //END
        if(!$property)
        {
            die("Invalid property");
        }
        
        // Load the area associated with this property
        $property_area = $this->area_model->get_details($property->area_id);
        if(!$property_area)
        {
            die("Invalid property area");
        } 

        // Load the project associated with this property
        $property_project = $this->property_model->get_projects($property_id);
        
        if(!$property_project)
        {
            die("Invalid property project");
        } 
        
        $project_id = $property_project[0];   
        $project = $this->project_model->get_details($project_id); 
        
		$project->project_id = $project_id;
		$project_min_price = $this->property_model->get_min_total_price($project_id);
		
        foreach($project_min_price->result() AS $project_min_price);
		$this->data["project_min_price"] = $project_min_price->min_total_price;	
		
        if(!$project)
        {
            die("Invalid project");
        }   
        
        /*****************************
        * Load the default disclaimer text from the disclaimer category 
        */
        
        $disclaimer_cat = $this->article_category_model->get_details(CATEGORY_DISCLAIMERS);
        if(!$disclaimer_cat) {
            die("Couldn't load the disclaimer category");
        }              
        
        $disclaimer_text = $disclaimer_cat->short_description;
        
        // If a disclaimer has been specifically loaded against the project for this project, use that instead.
        if(!empty($project->disclaimer_id)) {
            $disclaimer_article = $this->article_model->get_details($project->disclaimer_id);
            
            if(($disclaimer_article) && (strlen($disclaimer_article->content) > 15)) {
                $disclaimer_text = $disclaimer_article->content;
            }
        }
        
        // Transform content.
        $disclaimer_text = str_replace("<br>", "\n", $disclaimer_text); 
        $disclaimer_text = str_replace("<br/>", "\n", $disclaimer_text);
        $disclaimer_text = str_replace("<br />", "\n", $disclaimer_text);
        $disclaimer_text = str_replace("&nbsp;", " ", $disclaimer_text);
        $disclaimer_text = trim(strip_tags($disclaimer_text));
        
        define("DISCLAIMER", $disclaimer_text); 
        
        // End load Disclaimer 
        
        // Make sure the brochure folder for this property exists (we will store the PDF there)
        if(!is_dir($folder_path = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id)) {
            mkdir($folder_path = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id);
            chmod($folder_path = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id, 0777);    
        }
        
        $folder_path = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property_id . "/brochure";
        
        if (!is_dir($folder_path))
        {
            //create folder
            mkdir($folder_path);
            chmod($folder_path,0777);
        }

            
		// Paths
		$pdf_data["SAVE_PATH"] = $folder_path . "/brochure.pdf";
		$pdf_data["LINK_PATH"] = base_url() . PROPERTY_FILES_FOLDER . $property_id . "/brochure/brochure.pdf";
		   
        // Images
        $pdf_data["PROJECTLOGO_IMAGE"]	= ($project && $project->logo_print != "") ? ABSOLUTE_PATH . PROJECT_FILES_FOLDER . $property->project_id. "/" . $project->logo_print : "";
           
        // Text strings
		$pdf_data["HEADER_TEXT"] = "Lot ". $property->lot . ", " . $property->address;
		$pdf_data["SECONDARY_HEADER_TEXT"] = trim("$property_area->area_name, $project->project_name");
        $pdf_data["PROPERTY_TOTAL_PRICE"] = '$' . number_format($property->total_price, 0, ".", ",");
		$pdf_data["PROJECT_TYPE"] = $project->project_type;
           
        // footer
        $pdf_data["AGENT_LOGO"] = "";
        if($agent->logo != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . $agent->logo;
		    if(file_exists($logo_path_abs)) {
                $pdf_data["AGENT_LOGO"] = $logo_path_abs;
		    }
        }

        $pdf_data['CONTACT_INFO'] = "For more information about this property, please contact" . "\n" . trim("$agent->first_name $agent->last_name") . ".";
        $pdf_data['CONTACT_PHONE'] = $agent->phone;
        $pdf_data['CONTACT_MOBILE'] = $agent->mobile;
        $pdf_data['CONTACT_EMAIL'] = $agent->email;
        $pdf_data['CONTACT_NAME'] = $agent->first_name . " " . $agent->last_name;
        $pdf_data['CONTACT_COMPANY_NAME'] = $agent->company_name;
        $pdf_data['FULL_ADDRESS'] = "Lot ". $property->lot . ", " . $property->address . " " . $property->area_name . " " . $property->pstate . " " . $property->postcode;
        $this->load->model('project_brochure_model');
        $pdf_data['BROCHURE'] = $this->project_brochure_model->get_list(array('project_id' => $project_id));
        
        if(!$pdf_data['BROCHURE'])
        {
            $pdf_data['BROCHURE'] = $this->project_brochure_model->get_list_default($project_id);
        }
        
        // var_dump($pdf_data['BROCHURE']->result());die;
        $manual_type = $this->input->post("manual_type");
        if($manual_type == 'on')
            $prepared_for = $this->input->post("prepared_for_manual");
        else
            $prepared_for = $this->input->post("prepared_for");
        
        if($pdf_data['BROCHURE'])
            foreach($pdf_data['BROCHURE']->result() as $page)
            {
                if($page->type == 'title')
                {
                    $pdf_data['TITLE_DATA'] = array();
                    $pdf_data['TITLE_DATA']['PREPARED_FOR'] = $prepared_for;
                    $this->_title_property_pdf_data($agent, $property, $pdf_data['TITLE_DATA']);
                }
                if($page->type == 'property')
                {
                    $pdf_data['PROPERTY_DATA'] = array();
                    $this->_property_pdf_data($property, $project, $pdf_data['PROPERTY_DATA']);
                }
                else if($page->type == 'project')
                {
                    $pdf_data['PROJECT_DATA'] = array();
                    $this->_project_pdf_data($project_id, $pdf_data['PROJECT_DATA']);
                }
                else if($page->type == 'area')
                {
                    $pdf_data['AREA_DATA'] = array();
                    $this->_area_pdf_data($property_area->area_id, $pdf_data['AREA_DATA']);
                }
                else if($page->type == 'country')
                {
                    $pdf_data['COUNTRY_DATA'] = array();
                    $this->_country_pdf_data($property_area->state_id, $pdf_data['COUNTRY_DATA']);
                
                    $pdf_data['COUNTRY_DATA']["HEADER_TEXT"] = $page->heading;
                }
                else if($page->type == 'region')
                {
                    $pdf_data['REGION_DATA'] = array();
                    // $this->_region_pdf_data($property->region_id, $pdf_data['REGION_DATA']);
                    $this->_property_region_pdf_data($property_area->region_id, $pdf_data['REGION_DATA']);
                
                    $pdf_data['REGION_DATA']["HEADER_TEXT"] = $page->heading;
                }
                else if($page->type == 'summary')
                {
                    $pdf_data['SUMMARY_DATA'] = array();
                    $this->_summary_pdf_data($pdf_data['SUMMARY_DATA']);
                
                    $pdf_data['SUMMARY_DATA']["HEADER_TEXT"] = $page->heading;
                }
                else if($page->type == 'floorplan')
                {
                    $pdf_data['FLOORPLAN_DATA'] = array();
                    $this->_floorplan_pdf_data($property_id, $pdf_data['FLOORPLAN_DATA']);
                
                    $pdf_data['FLOORPLAN_DATA']["HEADER_TEXT"] = $page->heading;
                }
            }

        //create the pdf
        require_once('classes/fpdf.php');
        $pdf = new MY_FPDF('P', 'mm');
        
		$pdf_data["SAVE_PATH"] = "property-$property_id.pdf";
		$pdf_data["PDF_DEST"] = "I";
		
		if (make_property_brochure($pdf_data, $pdf))
        {
            exit();
            echo '<a href="' . str_replace(ABSOLUTE_PATH, base_url(), $pdf_data["SAVE_PATH"]) . '?r=' . rand(9999, 9999999) . '" target="_blank">Click here</a>'; 
        } else {
            echo 'Cannot generate brochure.';
        }
    }
    
    function _country_pdf_data($state_id, &$pdf_data)
    {
        $this->load->model("australia_model");
        $this->load->model("state_model");
        
        $pdf_data["AUSTRALIA"] = $this->australia_model->get_details();
        $pdf_data["STATE"] = $this->state_model->get_details2($state_id);
        $pdf_data["SUB_HEADER_TEXT"] = "Australia";
        $pdf_data["SUB_HEADER_TEXT2"] = $pdf_data["STATE"]->state_name;
    }
    
    function _property_pdf_data($property, $project, &$pdf_data)
    {
        // Images
        $pdf_data["HERO_IMAGE"]	= ($property && $property->hero_image !="") ? ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property->property_id . "/images/" . $property->hero_image : "";
        $plan_images = $this->document_model->get_floorplan("property_gallery", $property->property_id);
        
        if ($plan_images) {
            $pdf_data["PLAN_IMAGE"] = $plan_images;
        } else {
            $pdf_data["PLAN_IMAGE"] = false;
        }
        /*
        $gallery = $this->document_model->get_list($doc_type = "property_gallery", $foreign_id = $property->property_id);
        if ($gallery) {
            $counter = 1;
            foreach ($gallery->result() as $doc)
            {
                if($doc->document_path == "") continue;
                if(stristr($doc->extra_data, "floorplan")) continue;
                $pdf_data['PROPERTY_PHOTO_' . $counter] = ABSOLUTE_PATH . $doc->document_path;
                $counter++;
                if ($counter>2) break;
            }
        }
        */
        $pdf_data["PROPERTY_PHOTO_1"] = ($property->image_print1 != "") ? ABSOLUTE_PATH . $property->image_print1 : "" ;
        $pdf_data["PROPERTY_PHOTO_2"] = ($property->image_print2 != "") ? ABSOLUTE_PATH . $property->image_print2 : "" ;
        
		// Specifications area
		$pdf_data["PROPERTY_SHORT_DESCRIPTION"] = preg_replace("/[\t]/","", trim(strip_tags(html_entity_decode( $property->page_body, ENT_QUOTES) )));
		$pdf_data["PROPERTY_PRICE_FROM"] = ($property->total_price != -1) ? "$". number_format($property->total_price,0) : "-" ;
		$pdf_data["PROPERTY_NOBATHROOMS"] = ($property->bathrooms != -1) ? "$property->bathrooms" : "-";
		$pdf_data["PROPERTY_NOBEDROOMS"] = ($property->bedrooms != -1) ? "$property->bedrooms" : "-";
		$pdf_data["PROPERTY_NOGARAGES"] = ($property->garage != -1) ? "$property->garage" : "-" ;
		$pdf_data["PROPERTY_NRAS"] = ($property->nras==1) ? true : false;
		$pdf_data["PROPERTY_SMSF"] = ($property->smsf==1) ? true : false;
		$pdf_data["PROPERTY_RISK"] = "risk-".strtolower($project->rate);
		
		$pdf_data["PROPERTY_SPECIFICATIONS"] = array();
		if($property->property_type != "")
			$pdf_data["PROPERTY_SPECIFICATIONS"]['Property Type'] = $property->property_type;
		if($property->status != "")
			$pdf_data["PROPERTY_SPECIFICATIONS"]['Status'] = ucfirst($property->status);
		if($property->contract_type != "")
			$pdf_data["PROPERTY_SPECIFICATIONS"]['Contract Type'] = $property->contract_type;
		$pdf_data["PROPERTY_SPECIFICATIONS"]['Titled'] = ($property->titled == 1) ? "Yes" : "No";
        if(($property->titled == 0) && ($property->estimated_date != "") && ($property->estimated_date != "0"))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Estimated Date'] = $property->estimated_date;
        if((is_numeric($property->rent_yield)) && ($property->rent_yield > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Rent Yield'] = $property->rent_yield . '%';
        if((is_numeric($property->approx_rent)) && ($property->approx_rent > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Market Rent'] = '$' . $property->approx_rent;
        if((is_numeric($property->land)) && ($property->land > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Land Area'] = $property->land . ' sqm';
        if((is_numeric($property->house_area)) && ($property->house_area > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['House Area'] = $property->house_area . ' sqm';
        if((is_numeric($property->land_price)) && ($property->land_price > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Land Price'] = '$' . number_format($property->land_price, 0, ".", ",");
        if((is_numeric($property->house_price)) && ($property->house_price > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['House Price'] = '$' . number_format($property->house_price, 0, ".", ",");
        if($property->design != "")
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Design'] = $property->design;
        if((is_numeric($property->frontage)) && ($property->frontage > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Frontage'] = $property->frontage . ' sqm';
        $pdf_data["PROPERTY_SPECIFICATIONS"]['Study'] = ($property->study == 1) ? "Yes" : "No";
        if(($property->facade != "") && ($property->facade != "-1"))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Facade'] = $property->facade;
        if((is_numeric($property->est_stampduty_on_purchase)) && ($property->est_stampduty_on_purchase > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Stamp duty Est.'] = '$' . number_format($property->est_stampduty_on_purchase, 0, ".", ",");
        if((is_numeric($property->estimated_gov_transfer_fee)) && ($property->estimated_gov_transfer_fee > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Gov. Transfer Fee'] = '$' . number_format($property->estimated_gov_transfer_fee, 0, ".", ",");
        if((is_numeric($property->council_rates)) && ($property->council_rates > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Council rates'] = '$' . number_format($property->council_rates, 0, ".", ",");
        if((is_numeric($property->owner_corp)) && ($property->owner_corp > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Owners Corp Fee'] = '$' . number_format($property->owner_corp, 0, ".", ",");
        if(($property->other_fee_text != "") && (is_numeric($property->other_fee_amount)) && ($property->other_fee_amount > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"][$property->other_fee_text] = '$' . $property->other_fee_amount;
        if(($property->nras == 1) && ($property->nras_provider != ""))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['NRAS Provider'] = $property->nras_provider;
        if(($property->nras == 1) && (is_numeric($property->nras_rent)) && ($property->nras_rent > 0))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['NRAS Discount'] = $property->nras_rent . '%';
        if(($property->nras == 1) && ($property->nras_fee != "") && ($property->nras_fee != "-1"))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['NRAS Fee Summary'] = $property->nras_fee;
        if($property->special_features != "")
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Special Features'] = $property->special_features;
        if((in_array($this->user_type_id, array(USER_TYPE_ADVISOR))) && ($property->internal_comments != ""))
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Internal Comments'] = $property->internal_comments;
        if($property->misc_comments != "")
            $pdf_data["PROPERTY_SPECIFICATIONS"]['Misc Comments'] = $property->misc_comments;
    }
    
    function _area_pdf_data($area_id, &$pdf_data)
    {
        $this->load->model('area_meta_model');
        $this_user = $this->users_model->get_details($this->user_id);
        if(!$this_user)
            show_error("Invalid user");
        // If this user is an agent / advisor, set the agent object directly, otherwise find the agent for this user
        if($this->user_type_id == USER_TYPE_ADVISOR) {
            $agent = $this_user;    
        } else {
            $agent = $this->users_model->get_details($this_user->advisor_id);
            if(!$agent) {
                show_error("Couldn't load agent");
            }
        }
        $area = $this->area_model->get_details($area_id);
        if(!$area) redirect("/stocklist");
		// header 
        $pdf_data["HEADER_TEXT"] = strtoupper($area->area_name);
        if((is_numeric($area->median_house_price)) && ($area->median_house_price > 0))
            $pdf_data["MEDIAN_HOUSE_PRICE"] = '$' . number_format($area->median_house_price, 0, ".", ",");
            
        // main
        $gallery = $this->document_model->get_list($doc_type = "area_gallery", $foreign_id = $area_id);
        if ($gallery) {
            $counter = 1;
            foreach ($gallery->result() as $index=>$doc)
            {
                if (!empty($doc->document_path)) {
                    if ($counter==1) {
                		$pdf_data["HERO_IMAGE"]	= ABSOLUTE_PATH . $doc->document_path;
                		$counter++;
                    } elseif ($counter==2) {
                		$pdf_data["AREA_PHOTO_1"] = ABSOLUTE_PATH . $doc->document_path;
                		$counter++;
                    } elseif ($counter==3) {
                		$pdf_data["AREA_PHOTO_2"] = ABSOLUTE_PATH . $doc->document_path;
                		$counter++;
                    }
                }
                if ($counter > 3) {
                	break;
                }
            }
        } else {
    		$pdf_data["HERO_IMAGE"]	= "";
        }
        
        // Google maps image
        $map_image = "area_files/" . $area_id . "/map.png";
        $pdf_data['AREA_MAP_IMAGE'] = ABSOLUTE_PATH . $map_image;
        
		$pdf_data["AREA_OVERVIEW"] = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $area->overview ), ENT_QUOTES)));
		$metadata = $this->area_meta_model->get_list(array("area_id" => $area_id));
		if($metadata) {
		    $pdf_data["AREA_MOREINFO"] = $metadata->result();
		} else {
		    $pdf_data["AREA_MOREINFO"] = array();
		}
        
        // sidebar
		$pdf_data["AREA_SHORT_DESCRIPTION"] = preg_replace("/[\n\r]/","", html_entity_decode(strip_tags( $area->short_description ), ENT_QUOTES));
		$pdf_data["AREA_SPECIFICATIONS"] = array();
        
        
        if(is_numeric($area->median_house_price))
            $pdf_data['AREA_SPECIFICATIONS']['Median House Price'] = '$' . number_format($area->median_house_price, 0, ".", ",");         
		if((is_numeric($area->median_unit_price)) && ($area->median_unit_price > 0))
            $pdf_data['AREA_SPECIFICATIONS']['Median Unit Price'] = '$' . number_format($area->median_unit_price, 0, ".", ",");
        if($area->quarterly_growth != "")
            $pdf_data['AREA_SPECIFICATIONS']['Quarterly Growth'] = $area->quarterly_growth;
        if($area->month12_growth != "")
            $pdf_data['AREA_SPECIFICATIONS']['12 Month Growth'] = $area->month12_growth;
        if($area->year3_growth != "")
            $pdf_data['AREA_SPECIFICATIONS']['3 Year Growth'] = $area->year3_growth;
        if($area->year4_growth != "")
            $pdf_data['AREA_SPECIFICATIONS']['4 Year Growth'] = $area->year4_growth;
        if($area->median_growth_this_year != "")
            $pdf_data['AREA_SPECIFICATIONS']['Median Growth This Year'] = $area->median_growth_this_year;
        if($area->weekly_median_advertised_rent != "")
            $pdf_data['AREA_SPECIFICATIONS']['Weekly Median Advertised Rent'] = $area->weekly_median_advertised_rent;
        if($area->total_population != "")
            $pdf_data['AREA_SPECIFICATIONS']['Total Population'] = $area->total_population;
        if($area->median_age != "")
            $pdf_data['AREA_SPECIFICATIONS']['Median Age'] = $area->median_age;
        if($area->number_private_dwellings != "")
            $pdf_data['AREA_SPECIFICATIONS']['No. Private Dwellings'] = $area->number_private_dwellings;
        if($area->weekly_median_household_income != "")
            $pdf_data['AREA_SPECIFICATIONS']['Weekly Median Household Income'] = $area->weekly_median_household_income;
        if($area->closest_cbd != "")
            $pdf_data['AREA_SPECIFICATIONS']['Closest CBD'] = $area->closest_cbd;
        if($area->approx_time_cbd != "")
            $pdf_data['AREA_SPECIFICATIONS']['Approx time to CBD'] = $area->approx_time_cbd;
        if($area->approx_distance_cbd != "")
            $pdf_data['AREA_SPECIFICATIONS']['Approx Distance to CBD'] = $area->approx_distance_cbd; 
        
        //Get manual key facts
        $pdf_data['AREA_MANUAL_KEYFACTS'] = array();
        for($i = 1; $i <= 5; $i++)
        {
            $heading = "key_fact_heading" . $i;
            $text = "key_fact_text" . $i;
            if($area->$heading != "")
                $pdf_data['AREA_MANUAL_KEYFACTS'][$area->$heading] = $area->$text; 
        }
        
        // footer
        $pdf_data["AGENT_LOGO"] = "";
        if($agent->logo != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . $agent->logo;
		    if(file_exists($logo_path_abs)) {
                $pdf_data["AGENT_LOGO"] = $logo_path_abs;
		    }
        }
        
        $pdf_data['CONTACT_INFO'] = "For more information about this property, please contact" . "\n" . trim("$agent->first_name $agent->last_name") . ".";
        $pdf_data['CONTACT_PHONE'] = $agent->phone;
        $pdf_data['CONTACT_MOBILE'] = $agent->mobile;
        $pdf_data['CONTACT_EMAIL'] = $agent->email;
    }
    
    function area($area_id=0)
    {
		$pdf_data = array();
		
        // Make sure the brochure folder for this property exists (we will store the PDF there)
        $folder_path = ABSOLUTE_PATH . AREA_FILES_FOLDER . $area_id . "/brochure";
        
        if (!is_dir($folder_path)) {
            //create folder
            mkdir($folder_path);
            chmod($folder_path,0777);
        }
            
		// Paths
		$pdf_data["SAVE_PATH"] = $folder_path . "/brochure.pdf";
		$pdf_data["LINK_PATH"] = base_url() . AREA_FILES_FOLDER . $area_id . "/brochure/brochure.pdf";
		
		$this->_area_pdf_data($area_id, $pdf_data);
		
        //create the pdf
		$pdf_data["SAVE_PATH"] = "area-$area_id.pdf";
		$pdf_data["PDF_DEST"] = "I";
		
        $pdf = new MY_FPDF('P', 'mm');
		if (make_area_brochure($pdf_data, $pdf)) {
            echo '<a href="' . str_replace(ABSOLUTE_PATH, base_url(), $pdf_data["SAVE_PATH"]) . '?r=' . rand(9999, 9999999) . '" target="_blank">Click here</a>'; 
        }
    }
    
    function _project_pdf_data($project_id, &$pdf_data)
    {
        $this_user = $this->users_model->get_details($this->user_id);
        if(!$this_user)
            show_error("Invalid user");
        // If this user is an agent / advisor, set the agent object directly, otherwise find the agent for this user
        if($this->user_type_id == USER_TYPE_ADVISOR) {
            $agent = $this_user;    
        } else {
            $agent = $this->users_model->get_details($this_user->advisor_id);
            if(!$agent) {
                show_error("Couldn't load agent");
            }
        }
        
    	// Load the project object
        $project = $this->project_model->get_details($project_id);
		
		$project->project_id = $project_id;
		$project_min_price = $this->property_model->get_min_total_price($project_id);
		
		foreach($project_min_price->result() AS $project_min_price);
				
        if(!$project) {
            redirect("/projects");    
        }
        //By Ajay TasksEveryday
		$property_data = $this->property_model->get_property_min_max();
        //END
		// header 
        $pdf_data["HEADER_TEXT"] = "Project: " . $project->project_name;
        $pdf_data["SUB_HEADER_TEXT"] = "Area: " . $project->area_name;
        $pdf_data["PRICE_FROM"] = '$' . number_format($project_min_price->min_total_price, "0", ".", ",") . '+';
		
        // main
        $gallery = $this->document_model->get_list($doc_type = "project_gallery", $foreign_id = $project_id);
        if ($gallery) {
            $counter = 1;
            foreach ($gallery->result() as $index=>$doc)
            {
                if (!empty($doc->document_path)) {
                    if ($counter==1 AND file_exists(ABSOLUTE_PATH . $doc->document_path)) {
                		$pdf_data["PROJECT_PHOTO_1"] = ABSOLUTE_PATH . $doc->document_path;
                		$counter++;
                    } elseif ($counter==2 AND file_exists(ABSOLUTE_PATH . $doc->document_path)) {
                		$pdf_data["PROJECT_PHOTO_2"] = ABSOLUTE_PATH . $doc->document_path;
                		$counter++;
                    } elseif ($counter==3 AND file_exists(ABSOLUTE_PATH . $doc->document_path)) {
                		$pdf_data["PROJECT_PHOTO_3"] = ABSOLUTE_PATH . $doc->document_path;
                		$counter++;
                    }
                }
                if ($counter > 3) {
                	break;
                }
            }
        } else {
    		$pdf_data["HERO_IMAGE"]	= "";
        }
        $pdf_data["HERO_IMAGE"]	= ABSOLUTE_PATH . $project->logo;
        // Google maps image
        $pdf_data['PROJECT_MAP_IMAGE'] = PROJECT_FILES_FOLDER . "/$project_id/map.png";
        if( !file_exists($pdf_data["PROJECT_MAP_IMAGE"]) ) {
            $google_map_code = $project->google_map_code;
            if(!empty($google_map_code)) {
                $start_pos = strpos($google_map_code, 'll=');
                $end_pos = strpos($google_map_code, '&spn=');
                $ll = substr($google_map_code, $start_pos + 3, $end_pos - $start_pos - 3);
                $map_image = "http://maps.googleapis.com/maps/api/staticmap?center=".$ll."&zoom=4&size=600x400&sensor=false";
                file_put_contents(PROJECT_FILES_FOLDER . "/$project_id/map.png", file_get_contents($map_image));
            }
        }
        
		$pdf_data["PROJECT_SHORT_DESCRIPTION"] = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $project->page_body ), ENT_QUOTES)));
		// $pdf_data["QUICK_FACTS"] = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $project->quick_facts ), ENT_QUOTES)));
		$pdf_data["QUICK_FACTS"] = $project->quick_facts;
		
        $this->load->model('project_meta_model');
        $metadata = $this->project_meta_model->get_list(array("project_id" => $project_id));
		if($metadata) {
		    $pdf_data["PROJECT_MOREINFO"] = $metadata->result();
		} else {
		    $pdf_data["PROJECT_MOREINFO"] = array();
		}
		
        // Load project properties
        $filters = array();
        $filters["enabled"] = 1;
        $filters["archived"] = 0;
        $filters["project_id"] = $project_id;
        
        $user_logged = $this->users_model->get_details($this->user_id);
        if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
        {
        	$filters['permissions_user_id'] = $this->user_id;
        }
        
        $properties = $this->property_model->get_list($filters);
        $pdf_data['PROJECT_PROPERTIES'] = $properties ? $properties->result() : array();
		
        // footer
        $pdf_data["AGENT_LOGO"] = "";
        if($agent->logo != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . $agent->logo;
		    if(file_exists($logo_path_abs)) {
                $pdf_data["AGENT_LOGO"] = $logo_path_abs;
		    }
        }     
        
        $pdf_data['CONTACT_INFO'] = "For more information about this property, please contact" . "\n" . trim("$agent->first_name $agent->last_name") . ".";
        $pdf_data['CONTACT_PHONE'] = $agent->phone;
        $pdf_data['CONTACT_MOBILE'] = $agent->mobile;
        $pdf_data['CONTACT_EMAIL'] = $agent->email;

    }
    
    function project($project_id=0)
    {
		$pdf_data = array();
		
        // Make sure the brochure folder for this project exists (we will store the PDF there)
        $folder_path = ABSOLUTE_PATH . PROJECT_FILES_FOLDER . $project_id . "/brochure";
        
        if (!is_dir($folder_path)) {
            //create folder
            mkdir($folder_path);
            chmod($folder_path,0777);
        }
            
		// Paths
		$pdf_data["SAVE_PATH"] = $folder_path . "/brochure.pdf";
		$pdf_data["LINK_PATH"] = base_url() . PROJECT_FILES_FOLDER . $project_id . "/brochure/brochure.pdf";
		
		$this->_project_pdf_data($project_id, $pdf_data);
		
        //create the pdf
		$pdf_data["SAVE_PATH"] = "project-$project_id.pdf";
		$pdf_data["PDF_DEST"] = "I";
		
        $pdf = new MY_FPDF('P', 'mm');
		if (make_project_brochure($pdf_data, $pdf)) {
            echo '<a href="' . str_replace(ABSOLUTE_PATH, base_url(), $pdf_data["SAVE_PATH"]) . '?r=' . rand(9999, 9999999) . '" target="_blank">Click here</a>'; 
        }
    }
	
	function state($state_id=0)
    {
		$pdf_data = array();
		
        // Make sure the brochure folder for this property exists (we will store the PDF there)
        $folder_path = ABSOLUTE_PATH . STATE_FILES_FOLDER . $state_id . "/brochure";
        
        if (!is_dir($folder_path)) {
            //create folder
            mkdir($folder_path);
            chmod($folder_path,0777);
        }
            
		// Paths
		$pdf_data["SAVE_PATH"] = $folder_path . "/brochure.pdf";
		$pdf_data["LINK_PATH"] = base_url() . STATE_FILES_FOLDER . $state_id . "/brochure/brochure.pdf";
		
		$this->_state_pdf_data($state_id, $pdf_data);
		
        //create the pdf
		$pdf_data["SAVE_PATH"] = "state-$state_id.pdf";
		$pdf_data["PDF_DEST"] = "I";
		
        $pdf = new MY_FPDF('P', 'mm');
		if (make_state_brochure($pdf_data, $pdf)) {
            echo '<a href="' . str_replace(ABSOLUTE_PATH, base_url(), $pdf_data["SAVE_PATH"]) . '?r=' . rand(9999, 9999999) . '" target="_blank">Click here</a>'; 
        }
    }
	
	function _state_pdf_data($state_id, &$pdf_data)
    {   
        $this_user = $this->users_model->get_details($this->user_id);
        if(!$this_user)
            show_error("Invalid user");
        // If this user is an agent / advisor, set the agent object directly, otherwise find the agent for this user
        if($this->user_type_id == USER_TYPE_ADVISOR) {
            $agent = $this_user;    
        } else {
            $agent = $this->users_model->get_details($this_user->advisor_id);
            if(!$agent) {
                show_error("Couldn't load agent");
            }
        }
        $region_states = $this->state_model->get_details($state_id);
        if(!$region_states) redirect("/stocklist");
		// header 
        $pdf_data["HEADER_TEXT"] = strtoupper($region_states->state_name);
        if((is_numeric($region_states->median_house_price)) && ($region_states->median_house_price > 0))
            $pdf_data["MEDIAN_HOUSE_PRICE"] = '$' . number_format($region_states->median_house_price, 0, ".", ",");
            
        $hero_image = $region_states->state_hero_image;
    		
		$pdf_data["HERO_IMAGE"]	= ABSOLUTE_PATH . $hero_image;
  
        // Google maps image
        $map_image = "state_files/" . $state_id . "/map.png";
        $pdf_data['STATE_MAP_IMAGE'] = ABSOLUTE_PATH . $map_image;
        
		$pdf_data["STATE_OVERVIEW"] = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $region_states->overview ), ENT_QUOTES)));
		        
        // sidebar
		$pdf_data["STATE_SHORT_DESCRIPTION"] = preg_replace("/[\n\r]/","", html_entity_decode(strip_tags( $region_states->short_description ), ENT_QUOTES));
		$pdf_data["STATE_SPECIFICATIONS"] = array();
        
        
        if(is_numeric($region_states->median_house_price))
            $pdf_data['STATE_SPECIFICATIONS']['Median House Price'] = '$' . number_format($region_states->median_house_price, 0, ".", ",");         
		if((is_numeric($region_states->median_unit_price)) && ($region_states->median_unit_price > 0))
            $pdf_data['STATE_SPECIFICATIONS']['Median Unit Price'] = '$' . number_format($region_states->median_unit_price, 0, ".", ",");
        if($region_states->quarterly_growth != "")
            $pdf_data['STATE_SPECIFICATIONS']['Quarterly Growth'] = $region_states->quarterly_growth;
        if($region_states->month12_growth != "")
            $pdf_data['STATE_SPECIFICATIONS']['12 Month Growth'] = $region_states->month12_growth;
        if($region_states->year3_growth != "")
            $pdf_data['STATE_SPECIFICATIONS']['3 Year Growth'] = $region_states->year3_growth;
        if($region_states->year4_growth != "")
            $pdf_data['STATE_SPECIFICATIONS']['4 Year Growth'] = $region_states->year4_growth;
        if($region_states->median_growth_this_year != "")
            $pdf_data['STATE_SPECIFICATIONS']['Median Growth This Year'] = $region_states->median_growth_this_year;
        if($region_states->weekly_median_advertised_rent != "")
            $pdf_data['STATE_SPECIFICATIONS']['Weekly Median Advertised Rent'] = $region_states->weekly_median_advertised_rent;
        if($region_states->total_population != "")
            $pdf_data['STATE_SPECIFICATIONS']['Total Population'] = $region_states->total_population;
        if($region_states->median_age != "")
            $pdf_data['STATE_SPECIFICATIONS']['Median Age'] = $region_states->median_age;
        if($region_states->number_private_dwellings != "")
            $pdf_data['STATE_SPECIFICATIONS']['No. Private Dwellings'] = $region_states->number_private_dwellings;
        if($region_states->weekly_median_household_income != "")
            $pdf_data['STATE_SPECIFICATIONS']['Weekly Median Household Income'] = $region_states->weekly_median_household_income;
        if($region_states->closest_cbd != "")
            $pdf_data['STATE_SPECIFICATIONS']['Closest CBD'] = $region_states->closest_cbd;
        if($region_states->approx_time_cbd != "")
            $pdf_data['STATE_SPECIFICATIONS']['Approx time to CBD'] = $region_states->approx_time_cbd;
        if($region_states->approx_distance_cbd != "")
            $pdf_data['STATE_SPECIFICATIONS']['Approx Distance to CBD'] = $region_states->approx_distance_cbd; 
            
        // footer
        $pdf_data["AGENT_LOGO"] = "";
        if($agent->logo != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . $agent->logo;
		    if(file_exists($logo_path_abs)) {
                $pdf_data["AGENT_LOGO"] = $logo_path_abs;
		    }
        }
        
        $pdf_data['CONTACT_INFO'] = "For more information about this property, please contact" . "\n" . trim("$agent->first_name $agent->last_name") . ".";
        $pdf_data['CONTACT_PHONE'] = $agent->phone;
        $pdf_data['CONTACT_MOBILE'] = $agent->mobile;
        $pdf_data['CONTACT_EMAIL'] = $agent->email;
    }
	
	function region($region_id=0)
    {
		$pdf_data = array();
		
        // Make sure the brochure folder for this property exists (we will store the PDF there)
        $folder_path = ABSOLUTE_PATH . REGION_FILES_FOLDER . $region_id . "/brochure";
        
        if (!is_dir($folder_path)) {
            //create folder
            mkdir($folder_path);
            chmod($folder_path,0777);
        }
            
		// Paths
		$pdf_data["SAVE_PATH"] = $folder_path . "/brochure.pdf";
		$pdf_data["LINK_PATH"] = base_url() . REGION_FILES_FOLDER . $region_id . "/brochure/brochure.pdf";
		
		$this->_region_pdf_data($region_id, $pdf_data);
		
        //create the pdf
		$pdf_data["SAVE_PATH"] = "region-$region_id.pdf";
		$pdf_data["PDF_DEST"] = "I";
		
        $pdf = new MY_FPDF('P', 'mm');
		if (make_region_brochure($pdf_data, $pdf)) {
            echo '<a href="' . str_replace(ABSOLUTE_PATH, base_url(), $pdf_data["SAVE_PATH"]) . '?r=' . rand(9999, 9999999) . '" target="_blank">Click here</a>'; 
        }
    }
    
    function _property_region_pdf_data($region_id, &$pdf_data)
    {
        $this->load->model('region_meta_model');

        $region = $this->region_model->get_details($region_id);
        $region_metas = $this->region_meta_model->get_list(array('region_id'=>$region_id));
        
        $pdf_data['SUB_HEADER_TEXT'] = $region->region_name;
        $pdf_data['REGION'] = $region;
        if($region_metas)
            $pdf_data['REGION_METAS'] = $region_metas->result();
    }
	
	function _region_pdf_data($region_id, &$pdf_data)
    {
        $this_user = $this->users_model->get_details($this->user_id);
        if(!$this_user)
            show_error("Invalid user");
        // If this user is an agent / advisor, set the agent object directly, otherwise find the agent for this user
        if($this->user_type_id == USER_TYPE_ADVISOR) {
            $agent = $this_user;    
        } else {
            $agent = $this->users_model->get_details($this_user->advisor_id);
            if(!$agent) {
                show_error("Couldn't load agent");
            }
        }
        $regions = $this->region_model->get_details($region_id);
        if(!$regions) redirect("/stocklist");
		// header 
        $pdf_data["HEADER_TEXT"] = strtoupper($regions->region_name);
        if((is_numeric($regions->median_house_price)) && ($regions->median_house_price > 0))
            $pdf_data["MEDIAN_HOUSE_PRICE"] = '$' . number_format($regions->median_house_price, 0, ".", ",");
            
        
			
		$hero_image = $regions->region_hero_image;
    		
		$pdf_data["HERO_IMAGE"]	= ABSOLUTE_PATH . $hero_image;
       
        
        // Google maps image
        $map_image = "region_files/" . $region_id . "/map.png";
        $pdf_data['REGION_MAP_IMAGE'] = ABSOLUTE_PATH . $map_image;
        
		$pdf_data["REGION_OVERVIEW"] = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $regions->overview ), ENT_QUOTES)));
		        
        // sidebar
		$pdf_data["REGION_SHORT_DESCRIPTION"] = preg_replace("/[\n\r]/","", html_entity_decode(strip_tags( $regions->short_description ), ENT_QUOTES));
		$pdf_data["REGION_SPECIFICATIONS"] = array();
        
        
        if(is_numeric($regions->median_house_price))
            $pdf_data['REGION_SPECIFICATIONS']['Median House Price'] = '$' . number_format($regions->median_house_price, 0, ".", ",");         
		if((is_numeric($regions->median_unit_price)) && ($regions->median_unit_price > 0))
            $pdf_data['REGION_SPECIFICATIONS']['Median Unit Price'] = '$' . number_format($regions->median_unit_price, 0, ".", ",");
        if($regions->quarterly_growth != "")
            $pdf_data['REGION_SPECIFICATIONS']['Quarterly Growth'] = $regions->quarterly_growth;
        if($regions->month12_growth != "")
            $pdf_data['REGION_SPECIFICATIONS']['12 Month Growth'] = $regions->month12_growth;
        if($regions->year3_growth != "")
            $pdf_data['REGION_SPECIFICATIONS']['3 Year Growth'] = $regions->year3_growth;
        if($regions->year4_growth != "")
            $pdf_data['REGION_SPECIFICATIONS']['4 Year Growth'] = $regions->year4_growth;
        if($regions->median_growth_this_year != "")
            $pdf_data['REGION_SPECIFICATIONS']['Median Growth This Year'] = $regions->median_growth_this_year;
        if($regions->weekly_median_advertised_rent != "")
            $pdf_data['REGION_SPECIFICATIONS']['Weekly Median Advertised Rent'] = $regions->weekly_median_advertised_rent;
        if($regions->total_population != "")
            $pdf_data['REGION_SPECIFICATIONS']['Total Population'] = $regions->total_population;
        if($regions->median_age != "")
            $pdf_data['REGION_SPECIFICATIONS']['Median Age'] = $regions->median_age;
        if($regions->number_private_dwellings != "")
            $pdf_data['REGION_SPECIFICATIONS']['No. Private Dwellings'] = $regions->number_private_dwellings;
        if($regions->weekly_median_household_income != "")
            $pdf_data['REGION_SPECIFICATIONS']['Weekly Median Household Income'] = $regions->weekly_median_household_income;
        if($regions->closest_cbd != "")
            $pdf_data['REGION_SPECIFICATIONS']['Closest CBD'] = $regions->closest_cbd;
        if($regions->approx_time_cbd != "")
            $pdf_data['REGION_SPECIFICATIONS']['Approx time to CBD'] = $regions->approx_time_cbd;
        if($regions->approx_distance_cbd != "")
            $pdf_data['REGION_SPECIFICATIONS']['Approx Distance to CBD'] = $regions->approx_distance_cbd; 
            
        // footer
        $pdf_data["AGENT_LOGO"] = "";
        if($agent->logo != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . $agent->logo;
		    if(file_exists($logo_path_abs)) {
                $pdf_data["AGENT_LOGO"] = $logo_path_abs;
		    }
        }
        
        $pdf_data['CONTACT_INFO'] = "For more information about this region, please contact" . "\n" . trim("$agent->first_name $agent->last_name") . ".";
        $pdf_data['CONTACT_PHONE'] = $agent->phone;
        $pdf_data['CONTACT_MOBILE'] = $agent->mobile;
        $pdf_data['CONTACT_EMAIL'] = $agent->email;
    }
	
	function australia($australia_id=0)
    {
		$pdf_data = array();
		
        // Make sure the brochure folder for this australia exists (we will store the PDF there)
        $folder_path = ABSOLUTE_PATH . AUSTRALIA_FILES_FOLDER . $australia_id . "/brochure";
        
        if (!is_dir($folder_path)) {
            //create folder
            mkdir($folder_path);
            chmod($folder_path,0777);
        }
            
		// Paths
		$pdf_data["SAVE_PATH"] = $folder_path . "/brochure.pdf";
		$pdf_data["LINK_PATH"] = base_url() . AUSTRALIA_FILES_FOLDER . $australia_id . "/brochure/brochure.pdf";
		
		$this->_australia_pdf_data($australia_id, $pdf_data);
		
        //create the pdf
		$pdf_data["SAVE_PATH"] = "australia-$australia_id.pdf";
		$pdf_data["PDF_DEST"] = "I";
		
        $pdf = new MY_FPDF('P', 'mm');
		if (make_australia_brochure($pdf_data, $pdf)) {
            echo '<a href="' . str_replace(ABSOLUTE_PATH, base_url(), $pdf_data["SAVE_PATH"]) . '?r=' . rand(9999, 9999999) . '" target="_blank">Click here</a>'; 
        }
    }
	
	function _australia_pdf_data($australia_id, &$pdf_data)
    {
        $this_user = $this->users_model->get_details($this->user_id);
        if(!$this_user)
            show_error("Invalid user");
        // If this user is an agent / advisor, set the agent object directly, otherwise find the agent for this user
        if($this->user_type_id == USER_TYPE_ADVISOR) {
            $agent = $this_user;    
        } else {
            $agent = $this->users_model->get_details($this_user->advisor_id);
            if(!$agent) {
                show_error("Couldn't load agent");
            }
        }
        $australia = $this->australia_model->get_details();
        if(!$australia) redirect("/stocklist");
		// header 
        $pdf_data["HEADER_TEXT"] = strtoupper($australia->australia_name);
        	
		$hero_image = $australia->australia_hero_image;
    		
		$pdf_data["HERO_IMAGE"]	= ABSOLUTE_PATH . $hero_image;
       
        
        // Google maps image
        $map_image = "australia_files/" . $australia_id . "/map.png";
        $pdf_data['AUSTRALIA_MAP_IMAGE'] = ABSOLUTE_PATH . $map_image;
        
		$pdf_data["AUSTRALIA_OVERVIEW"] = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $australia->overview ), ENT_QUOTES)));
		        
        // sidebar
		$pdf_data["AUSTRALIA_SHORT_DESCRIPTION"] = preg_replace("/[\n\r]/","", html_entity_decode(strip_tags( $australia->short_description ), ENT_QUOTES));
		$pdf_data["AUSTRALIA_SPECIFICATIONS"] = array();
        
        // footer
        $pdf_data["AGENT_LOGO"] = "";
        if($agent->logo != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . $agent->logo;
		    if(file_exists($logo_path_abs)) {
                $pdf_data["AGENT_LOGO"] = $logo_path_abs;
		    }
        }
        
        $pdf_data['CONTACT_INFO'] = "For more information about this australia, please contact" . "\n" . trim("$agent->first_name $agent->last_name") . ".";
        $pdf_data['CONTACT_PHONE'] = $agent->phone;
        $pdf_data['CONTACT_MOBILE'] = $agent->mobile;
        $pdf_data['CONTACT_EMAIL'] = $agent->email;
    }
    
    function _title_property_pdf_data($agent, $property, &$pdf_data)
    {
        $pdf_data['CONTACT_INFO'] = "For more information about this property, please contact" . "\n" . trim("$agent->first_name $agent->last_name") . ".";
        $pdf_data['CONTACT_PHONE'] = $agent->phone;
        $pdf_data['CONTACT_MOBILE'] = $agent->mobile;
        $pdf_data['CONTACT_EMAIL'] = $agent->email;
        $pdf_data['CONTACT_NAME'] = $agent->first_name . " " . $agent->last_name;
        $pdf_data['CONTACT_COMPANY_NAME'] = $agent->company_name;
        $pdf_data['FULL_ADDRESS'] = "Lot ". $property->lot . ", " . $property->address . " " . $property->area_name . " " . $property->pstate . " " . $property->postcode;
    
        $pdf_data["AGENT_LOGO"] = "";
        if($agent->logo != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . $agent->logo;
		    if(file_exists($logo_path_abs)) {
                $pdf_data["AGENT_LOGO"] = $logo_path_abs;
		    }
        }
        
        
        $pdf_data["PROPERTY_LOGO"] = "";
        
        if($property->hero_image != "")
        {
		    $logo_path_abs = ABSOLUTE_PATH . PROPERTY_FILES_FOLDER . $property->property_id . '/images/' . $property->hero_image;
            // echo $logo_path_abs;die;
            if(file_exists($logo_path_abs)) {
                $pdf_data["PROPERTY_LOGO"] = $logo_path_abs;
		    }
        }
    }
    
    function _summary_pdf_data(&$pdf_data)
    {
        $pdf_data['ADD_SUMMARY'] = $this->input->post("add_summary");
        $summary_id = $this->input->post("summary");
        $this->load->model('Summaries_model');
        $pdf_data['SUMMARY'] = $this->Summaries_model->get_details($summary_id);
    }
	
	function _floorplan_pdf_data($property_id, &$pdf_data)
    {
        $images = $this->data["images"] = $this->document_model->get_list("property_gallery", $property_id); 
        if($images)
        {
            foreach($images->result() as $img)
            {
                if(strtolower($img->document_name) == 'floorplan.jpg' || strtolower($img->extra_data) == 'floorplan')
                {
                    $pdf_data['IMG_URL'] = $img->document_path;
                } 
            }
        }
    }
	
}