<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Stocklist extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Stocklist()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("property_model");
        $this->load->model("area_model");
        $this->load->model("project_model");
        $this->load->model("area_model");
        $this->load->model("resources_model");
        $this->load->model("document_model");
        $this->load->model("favourites_model");
        $this->load->model("builder_model");
		$this->load->model("Users_model");
		$this->load->model("Summaries_model");
	
        
        
        $this->load->helper("image");
        
        $this->patch_partner_ids();
        
        $this->data['user_type_id'] = $this->user_type_id;
    }
    
    function index($mode = "")
    {
        $this->data["meta_title"] = "Stocklist"; 
        
        $builder_id = "";
        
        // If the logged in user is a supplier, limit the projects and areas lists to contain only those containing properties assigned to the 
        // suppliers builder.
        if($this->user_type_id == USER_TYPE_SUPPLIER) {
             $this_user = $this->users_model->get_details($this->user_id);
             $builder_id = $this_user->builder_id;
             
             if(!is_numeric($builder_id)) show_error("Sorry, your profile is currently not assigned to a builder.  Please contact Aspire admin.");
        }
        
        $this->data["property_data"] = $this->property_model->get_property_min_max();
        $this->data["projects"] = $this->project_model->get_list(1, "", "", $count_all, "", "p.project_name ASC", array("has_available" => 1), $builder_id);
        $this->data["areas"] = $this->area_model->get_list(1, "", "", $count_all, "", "area_name ASC", $builder_id);
        $this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
        $this->data["property_types"] = $this->resources_model->get_list($resource_type = "property_type");
        $this->data["contract_types"] = $this->resources_model->get_list($resource_type = "contract_type");
        $this->data["status_options"] = $this->property_model->get_property_status(false);
        $this->data["mode"] = $mode;

        $this->load->view('member/header', $this->data);
        $this->load->view('member/stocklist/list/prebody.php', $this->data); 
        $this->load->view('member/stocklist/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function detail($property_id = "")
    {
        $this->load->model('property_permissions_model');
        
        if(!is_numeric($property_id))
        {
            redirect("/stocklist");    
        }
                
        // Load the property object
        $property = $this->property_model->get_details($property_id);
		
		// If the property could not be loaded or if it's currently in a pending status, redirect back to the stocklist.
        if((!$property) || ($property->status == "pending"))
        {
            redirect("/stocklist");
        }
        
        $user_logged = $this->users_model->get_details($this->user_id);
        if(!$user_logged) {
            redirect("/login/logout");    
        }
        
        if ($user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
        {
            $exists_property = $this->property_permissions_model->exists_property($property_id, $this->user_id);
            if (!$exists_property)
            {
                show_error("Sorry, you do not currrently have permission to view this property.  Please ask your advisor to add permission for you.");
                //redirect("/stocklist");
            }
        }
        
        // If this user is a supplier, ensure they are allowed to view this property
        if($this->user_type_id == USER_TYPE_SUPPLIER) {
            if($user_logged->builder_id != $property->builder_id) {
                redirect("/stocklist");
            } 
        }
        
        $this->data["property"] = $property;
        
        // Load area and project that are assigned to this property
        $area = false;
        
        if(is_numeric($property->area_id))
        {
            $area = $this->area_model->get_details($property->area_id);    
        }
        
        if(!$area)
        {
            redirect("/stocklist");    
        }
        
        $this->data["area"] = $area;
        
        $project = $this->project_model->get_property_project($property_id, $this->user_id); 

				
        if(!$project)
        {
            redirect("/stocklist");    
        }        
        
        $this->data["project"] = $project;
      
		$project_id = $project->project_id;
		
		$project = $this->project_model->get_details($project_id);
		
		$project->project_id = $project_id;
		$project_min_price = $this->property_model->get_min_total_price($project_id);
		
		foreach($project_min_price->result() AS $project_min_price);
		$this->data["project_min_price"] = $project_min_price->min_total_price;
		
        // Load the photo gallery for this property
        $this->data["gallery"] = $this->document_model->get_list($doc_type = "property_gallery", $foreign_id = $property_id);    
        
        // Load property documents
        $this->data["docs"] = $this->document_model->get_list($doc_type = "property_document", $foreign_id = $property_id);
        
        // Load favourite
        $this->data["favourite"] = $this->favourites_model->exists($foreign_type = "property", $foreign_id = $property_id, $user_id = $this->user_id);
        
        $this->data["user_type_id"] = $this->user_type_id;
        $this->data["user_id"] = $this->user_id;
        $this->data["meta_title"] = "Lot $property->lot , $property->address";
        $this->data["states"] = $this->tools_model->get_states(1);
        
      //  $this->load->model('Users_model');
        $this->data['user'] = $this->Users_model->get_details($this->user_id);   
        $this->data["is_sub_advisor"] = false;
        
        // If the logged in user is an advisor, are they are SUB advisor (i.e., created by another advisor)
        if(($this->user_type_id == USER_TYPE_ADVISOR) && (!empty($this->data['user']->created_by_user_id))) {
            $created_by = $this->Users_model->get_details($this->data['user']->created_by_user_id);
            
            if(($created_by) && ($created_by->user_type_id == USER_TYPE_ADVISOR)) {
                $this->data["is_sub_advisor"] = true;  
            }
        }     
        
        if (in_array($this->user_type_id, array(USER_TYPE_INVESTOR, USER_TYPE_LEAD, USER_TYPE_PARTNER)))
        {
            $this->data['advisor'] = $this->Users_model->get_details($this->data['user']->advisor_id);
            
            // If this user does not have the advisor id set, see if the logged in users grandfather is an advisor.
            // This should be the case.
            if(!$this->data['advisor'])
            {
                $creator = $this->Users_model->get_details($this->data['user']->created_by_user_id);
                if($creator)
                {
                    $grandfather = $this->Users_model->get_details($creator->created_by_user_id);
                    if(($grandfather) && ($grandfather->user_type_id == USER_TYPE_ADVISOR)) 
                    {
                        // Update the database with the correct advisor id
                        $update = array();
                        $update["advisor_id"] = $grandfather->user_id;    
                        
                        $this->Users_model->save($this->user_id, $update);
                        $this->data['advisor'] = $grandfather;
                    }
                }
            }
        }
        
        $this->data["relationship_types"] = array(
            "Single" => "Single",      
            "Married" => "Married",
            "Defacto" => "Defacto",
            "Divorce" => "Divorce"
            );
        
		$advisor_id = $this->user_id;
		$this->data['number_of_users'] = $this->users_model->number_of_users($advisor_id);
									
        $this->data["partners"] = $this->Users_model->get_partners_by_advisor_id($advisor_id);
        $this->data["investors"] = $this->Users_model->get_investor_by_advisor_id($advisor_id);
        $this->data["enquiries"] = $this->Users_model->get_enquiries_by_advisor_id($advisor_id);
        
        $filters = array();
        $filters["created_by"] = $advisor_id; 
        $this->data["summaries"] = $this->Summaries_model->get_list($filters, "s.created_date DESC", "", "", $count_all);
        
		$this->load->view('member/header', $this->data);
        $this->load->view('member/stocklist/detail/prebody.php', $this->data); 
        $this->load->view('member/stocklist/detail/main.php', $this->data);
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
            case 'submit_reserve_form':
                $this->handle_submit_reserve_form();
                break;
                
            case 'submit_reservation_request':
                $this->handle_submit_reservation_request();
                break;
                
            case 'get_investor_detail':
                $this->handle_get_investor_detail();
                break;
                
            case "load_stocklist":   // User is trying to update their own account
                $this->handle_load_stocklist();
                break;
                
            case "set_latlng":   // User is trying to update their own account
                $this->handle_set_latlng();
                break;                
                
            case "save_comments":   // Advisor save commission comments
                $this->handle_save_comments();
                break;
			
			case "assign_stock_permission":   // Advisor save commission comments
                $this->handle_assign_stock_permission();
                break;                
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    function generate()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        $this->form_validation->set_rules('list_type', 'List Type', 'required');
        $this->form_validation->set_rules('min_bedrooms', 'Min Bedrooms', 'required|number');
        $this->form_validation->set_rules('max_bedrooms', 'Max Bedrooms', 'required|number');
        $this->form_validation->set_rules('min_bathrooms', 'Min Bathrooms', 'required|number');
        $this->form_validation->set_rules('max_bathrooms', 'Max Bathrooms', 'required|number');  
        $this->form_validation->set_rules('min_garage', 'Min Garage', 'required|number');
        $this->form_validation->set_rules('max_garage', 'Max Garage', 'required|number');               
        $this->form_validation->set_rules('min_total_price', 'Min Total Price', 'required|number');
        $this->form_validation->set_rules('max_total_price', 'Max Total Price', 'required|number');         
        $this->form_validation->set_rules('min_land', 'Min Land', 'required|number');
        $this->form_validation->set_rules('max_land', 'Max Land', 'required|number'); 
        $this->form_validation->set_rules('min_yield', 'Min Yield', 'required|number');
        $this->form_validation->set_rules('max_yield', 'Max Yield', 'required|number');                              
        $this->form_validation->set_rules('nras', 'NRAS', 'number');
        $this->form_validation->set_rules('smsf', 'SMSF', 'number');
        $this->form_validation->set_rules('featured', 'Featured', 'number');
        $this->form_validation->set_rules('new', 'New', 'number');
        $this->form_validation->set_rules('project_id', 'Project ID', 'number');
        $this->form_validation->set_rules('area_id', 'Area ID', 'number');
        $this->form_validation->set_rules('state_id', 'State ID', 'number');
        $this->form_validation->set_rules('property_type_id', 'Property Type ID', 'number');
        $this->form_validation->set_rules('contract_type_id', 'Contract Type ID', 'number');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $status = $this->input->post("status");
        if(strtolower($status) == "pending")
        {
            $this->data["message"] = "You may not load properties in a pending status.";
            send($this->data);            
        }
        
        // Sort By Columns
        $valid_columns = array(
                                "p.address",
                                "p.total_price",
                                "r1.name",
                                "p.house_area",
                                "p.land",
                                "p.featured DESC, p.rent_yield",
                                "p.rent_yield",
                                "p.nras",
                                "p.smsf",
                                "area.area_name",
                                "st.name",
                                "proj.project_name"
                            );
                            
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }
        
        $search_fields = array("min_bedrooms", "max_bedrooms", "min_bathrooms", "max_bathrooms", "min_garage", 
            "max_garage", "min_total_price", "max_total_price", "min_land", "max_land", "min_house", "max_house", 
            "min_yield", "max_yield", "nras", "smsf", "project_id", "area_id", "state_id", "property_type_id", 
            "contract_type_id", "status", "featured", "new");
        
        $filters = array();
        //$filters["user_id"] = $this->user_id;
        $filters["enabled"] = 1;
        $filters["archived"] = 0;   
        $filters["keysearch"] = $this->input->post("keysearch");
        $filters["limit"] = 999999;
        $filters["offset"] = 0;
        
        // On the map view, load all properties.
        
        foreach($search_fields as $field)
        {
            $filters[$field] = $this->input->post($field);    
        }

        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        $user_logged = $this->users_model->get_details($this->user_id);
        if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id != USER_TYPE_ADVISOR))
        {
            $filters['permissions_user_id'] = $this->user_id;
        }
        
        $filters['user_type_id'] = $user_logged->user_type_id;
        $filters['user_builder_id'] = $user_logged->builder_id;

        $properties = $this->property_model->get_list($filters, $count_all, $order_by);

        if ($properties) {
            $columns = $this->input->post('columns');
            if (!is_array($columns) OR sizeof($columns)==0) {
                show_error('Please select at least one column for the generated document.');
            }
            // headings
            $aHeadings = array();
            $defaultHeadings = array(
                'lot' => 'Lot',
                'address' => 'Address',
                'area' => 'Area',
                'state' => 'State',
                'estate' => 'Estate',
                'price' => 'Price',
                'type' => 'Type',
                'size' => 'Size',
                'land' => 'Land',
                'yield' => 'Yield',
                'nras' => 'NRAS',
                'smsf' => 'SMSF',
            );
            foreach ($columns as $column)
            {
                $aHeadings[] = $defaultHeadings[$column];
            }
            
            // start generating
            if ($this->input->post('type')=='csv') {
                $delim = ",";
                $newline = "\n";
                $enclosure = '"';
                
                $out = '';
                
                foreach ($aHeadings as $heading)
                {
                    $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $heading).$enclosure.$delim;
                }
                $out = rtrim($out);
                $out .= $newline;
                
                foreach ($properties->result() as $record)
                {
                    foreach ($columns as $column)
                    {
                        switch ($column)
                        {
                            case 'area':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->area_name).$enclosure.$delim;
                                break;
                            case 'state':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->state_code).$enclosure.$delim;
                                break;
                            case 'estate':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->project_name).$enclosure.$delim;
                                break;
                            case 'price':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, '$'.number_format($record->total_price, 0, ".", ",")).$enclosure.$delim;
                                break;
                            case 'type':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->property_type).$enclosure.$delim;
                                break;
                            case 'size':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->house_area).$enclosure.$delim;
                                break;
                            case 'land':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, $record->land).$enclosure.$delim;
                                break;
                            case 'yield':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, number_format($record->rent_yield, 2)).$enclosure.$delim;
                                break;
                            case 'nras':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, ($record->nras) ? "Yes" : "No").$enclosure.$delim;
                                break;
                            case 'smsf':
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, ($record->smsf) ? "Yes" : "No").$enclosure.$delim;
                                break;
                            default:
                                $out .= $enclosure.str_replace($enclosure, $enclosure.$enclosure, isset($record->$column) ? $record->$column : '').$enclosure.$delim;
                                break;
                        }
                    }
                    $out = rtrim($out);
                    $out .= $newline;
                }
                
                header("Content-type: text/csv");
                header("Content-Disposition: inline; filename=stocklist-".date('YmdHis').".csv");
                header("Pragma: public");
                header("Expires: 0");
                ini_set('zlib.output_compression','0');
                echo $out;
            } else {
                $columnsWidth = array(
                    'lot' => 20,
                    'address' => 40,
                    'area' => 40,
                    'state' => 15,
                    'estate' => 25,
                    'price' => 20,
                    'type' => 25,
                    'size' => 25,
                    'land' => 15,
                    'yield' => 15,
                    'nras' => 15,
                    'smsf' => 15,
                );
                $totalWidthDefault = 0;
                foreach ($columnsWidth as $width) $totalWidthDefault += $width;
                
                // pdf
                $this->load->helper('pdf');
                $pdf=new FPDF_MultiCellTable('L');
                $pdf->AddFont('PTSans');
                $pdf->AddFont('PTSansBold');
                $pdf->AddPage();
                
                // set width
                $aWidth = array();
                $totalWidth = 0;
                foreach ($columns as $column)
                {
                    $totalWidth += $columnsWidth[$column];
                }
                foreach ($columns as $column)
                {
                    $aWidth[] = $columnsWidth[$column] * ($totalWidthDefault/$totalWidth);
                }
                $pdf->SetWidths($aWidth);
                
                $pdf->setHeading($aHeadings);
                $pdf->printHeading();
                
                $currentPage = 0;
                foreach ($properties->result() as $record)
                {
                    $aRowData = array();
                    foreach ($columns as $column)
                    {
                        switch ($column)
                        {
                            case 'area':
                                $aRowData[] = $record->area_name;
                                break;
                            case 'state':
                                $aRowData[] = $record->state_code;
                                break;
                            case 'estate':
                                $aRowData[] = $record->project_name;
                                break;
                            case 'price':
                                $aRowData[] = '$'.number_format($record->total_price, 0, ".", ",");
                                break;
                            case 'type':
                                $aRowData[] = $record->property_type;
                                break;
                            case 'size':
                                $aRowData[] = $record->house_area;
                                break;
                            case 'land':
                                $aRowData[] = $record->land;
                                break;
                            case 'yield':
                                $aRowData[] = number_format($record->rent_yield, 2);
                                break;
                            case 'nras':
                                $aRowData[] = ($record->nras) ? "Yes" : "No";
                                break;
                            case 'smsf':
                                $aRowData[] = ($record->smsf) ? "Yes" : "No";
                                break;
                            default:
                                $aRowData[] = isset($record->$column) ? $record->$column : '';
                                break;
                        }
                    }
                    $pdf->Row($aRowData);
                }
                $pdf->Output('stocklist-'.date('YmdHis').'.pdf', 'I');
            }
        } else {
            show_error('No properties found.');
        }
    }
    
    /***
    * Handles updating the lat and lng for a specific property
    */
    private function handle_set_latlng()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('property_id', 'Property ID', 'required|number'); 
        $this->form_validation->set_rules('lat', 'Lat', 'required|number');
        $this->form_validation->set_rules('lng', 'Lng', 'required|number');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        // Update the property with the correct lat/lng
        $save = array();
        $save["lat"] = $this->input->post("lat");
        $save["lng"] = $this->input->post("lng");
        
        if(!$this->property_model->save($this->input->post("property_id"), $save))
        {
            $this->data["status"] = "ERROR";
            $this->data["message"] = "An error occured whilst trying to set the lat and lng for this property";
            send($this->data);                 
        }
        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        send($this->data);  
    }  
    
    /***
    * Handles the load_stocklist action
    * Send a html listing of stock items back if successful
    */
    private function handle_load_stocklist()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        $this->form_validation->set_rules('list_type', 'List Type', 'required');
        $this->form_validation->set_rules('min_bedrooms', 'Min Bedrooms', 'required|number');
        $this->form_validation->set_rules('max_bedrooms', 'Max Bedrooms', 'required|number');
        $this->form_validation->set_rules('min_bathrooms', 'Min Bathrooms', 'required|number');
        $this->form_validation->set_rules('max_bathrooms', 'Max Bathrooms', 'required|number');  
        $this->form_validation->set_rules('min_garage', 'Min Garage', 'required|number');
        $this->form_validation->set_rules('max_garage', 'Max Garage', 'required|number');               
        $this->form_validation->set_rules('min_total_price', 'Min Total Price', 'required|number');
        $this->form_validation->set_rules('max_total_price', 'Max Total Price', 'required|number');         
        $this->form_validation->set_rules('min_land', 'Min Land', 'required|number');
        $this->form_validation->set_rules('max_land', 'Max Land', 'required|number'); 
        $this->form_validation->set_rules('min_yield', 'Min Yield', 'required|number');
        $this->form_validation->set_rules('max_yield', 'Max Yield', 'required|number');                              
        $this->form_validation->set_rules('nras', 'NRAS', 'number');
        $this->form_validation->set_rules('smsf', 'SMSF', 'number');
        $this->form_validation->set_rules('featured', 'Featured', 'number');
        $this->form_validation->set_rules('new', 'New', 'number');
        $this->form_validation->set_rules('titled', 'Titled', 'number');
        $this->form_validation->set_rules('project_id', 'Project ID', 'number');
        $this->form_validation->set_rules('area_id', 'Area ID', 'number');
        $this->form_validation->set_rules('state_id', 'State ID', 'number');
        $this->form_validation->set_rules('property_type_id', 'Property Type ID', 'number');
        $this->form_validation->set_rules('contract_type_id', 'Contract Type ID', 'number');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $status = $this->input->post("status");
        if(strtolower($status) == "pending")
        {
            $this->data["message"] = "You may not load properties in a pending status.";
            send($this->data);            
        }
        
        // Sort By Columns
        $valid_columns = array(
                                "p.address",
                                "p.total_price",
                                "r1.name",
                                "p.house_area",
                                "p.land",
                                "p.featured DESC, p.rent_yield",
                                "p.rent_yield",
                                "p.nras",
                                "p.smsf",
                                "area.area_name",
                                "st.name",
                                "proj.project_name"
                            );
                            
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }
        
        $current_page = $this->input->post("current_page");
        $list_type = $this->input->post("list_type");
        
        $_POST["status"] = "available";
        
        $search_fields = array("min_bedrooms", "max_bedrooms", "min_bathrooms", "max_bathrooms", "min_garage", 
            "max_garage", "min_total_price", "max_total_price", "min_land", "max_land", "min_house", "max_house", 
            "min_yield", "max_yield", "nras", "smsf", "titled", "project_id", "area_id", "state_id", "property_type_id", 
            "contract_type_id", "status", "featured", "new");
        
        $filters = array();
        //$filters["user_id"] = $this->user_id;
        $filters["enabled"] = 1;
        $filters["archived"] = 0;   
        $filters["keysearch"] = $this->input->post("keysearch");
        $filters["limit"] = STOCKLIST_PER_PAGE;
        $filters["offset"] = ($current_page - 1) * STOCKLIST_PER_PAGE;
        
        // On the map view, load all properties.
        if($list_type == "map")
        {
            $filters["limit"] = 9999;   
            $filters["offset"] = 0;
        }
        
        foreach($search_fields as $field)
        {
            $filters[$field] = $this->input->post($field);    
        }
		
        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        $user_logged = $this->users_model->get_details($this->user_id);
        $filters['user_type_id'] = $user_logged->user_type_id;
        
        // A supplier will only see the properties assigned to the the builder they are assigned to.
        if($this->user_type_id == USER_TYPE_SUPPLIER) {
            $filters['user_builder_id'] = $user_logged->builder_id; 
        } else {
            //if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id == USER_TYPE_INVESTOR OR $user_logged->user_type_id == USER_TYPE_PARTNER OR $user_logged->user_type_id == USER_TYPE_LEAD))
            if ($user_logged && $user_logged->view_all_property != 1 && ($user_logged->user_type_id != USER_TYPE_ADVISOR))
            {
                $filters['permissions_user_id'] = $this->user_id;
            }
        }
        
        $properties = $this->property_model->get_list($filters, $count_all, $order_by);
			
        $stockRequest = '<p id="stockRequest">Your search criteria returned no results. Please select different parameters or <a href="mailto:support@aspirenetwork.net.au?subject=Stock Request">CLICK HERE</a> to email a request to ASPIRE Property Acquisitions Team to investigate availability option for your request.</p>'; 
        
        switch($list_type)
        {
            case "list":
                $this->data["message"] = $this->load->view("member/stocklist/list/list", array("properties" => $properties), true);
                if($this->data["message"] == "") $this->data["message"] = $stockRequest; 
                break;
                
            case "grid":
                $this->data["message"] = $this->load->view("member/stocklist/list/grid", array("properties" => $properties), true);
                if($this->data["message"] == "") $this->data["message"] = $stockRequest;
                break;
                
            case "map":
                // For the map view, just send back the raw property data
                $property_array = array();

                if($properties)
                {
                    foreach($properties->result() as $property)
                    {
                        $item = array();
                        $item["property_id"] = $property->property_id;
                        $item["lot"] = $property->lot;
                        $item["address"] = $property->address;
                        $item["suburb"] = $property->suburb;
                        $item["area"] = $property->area_name;
                        $item["postcode"] = $property->postcode;
                        $item["state"] = $property->state_name;
                        $item["bedrooms"] = $property->bedrooms;
                        $item["bathrooms"] = $property->bathrooms;
                        $item["garage"] = $property->garage;
                        $item["nras"] = $property->nras;
                        $item["smsf"] = $property->smsf;
                        $item["total_price"] = $property->total_price;
                        $item["land_area"] = $property->land;
                        $item["house_area"] = $property->house_area;
                        $item["rent_yield"] = $property->rent_yield;
                        $item["rate"] = $property->rate;
                        $item["lat"] = $property->lat;
                        $item["lng"] = $property->lng;
                        $item["image"] = null;
                        
                        if($property->hero_image != "")
                        {
                            $src = "property/" . $property->property_id . "/images/" . $property->hero_image;
                            $item["image"] = image_resize($src, 196, 130);                
                        }          
                        
                        // Add the item to the property array
                        $property_array[] = $item;
                    }
                }        
            
                $this->data["message"] = $property_array;
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
    
    
    private function handle_get_investor_detail()
    {
        $id = (int) $this->input->post('id');
        $this->load->model('Users_model');
        $investor = $this->Users_model->get_details($id);
        if (!$investor) {
            send(array('status'=>'FAILED'));
        } else {
            send(array(
                'status' => 'SUCCESS',
                'first_name' => $investor->first_name,
                'middle_name' => $investor->middle_name,
                'last_name' => $investor->last_name,
                'email' => $investor->email,
                'secondary_email' => $investor->secondary_email,
                'company_name' => $investor->company_name,
                'phone' => $investor->phone,
                'home_phone' => $investor->home_phone,
                'mobile' => $investor->mobile,
                'fax' => $investor->fax,
                'billing_address1' => $investor->billing_address1,
                'billing_address2' => $investor->billing_address2,
                'billing_suburb' => $investor->billing_suburb,
                'billing_postcode' => $investor->billing_postcode,
                'billing_state_id' => $investor->billing_state_id,
                'billing_country_id' => $investor->billing_country_id,
                'delivery_address1' => $investor->delivery_address1,
                'delivery_suburb' => $investor->delivery_suburb,
                'delivery_postcode' => $investor->delivery_postcode,
                'delivery_state_id' => $investor->delivery_state_id,
                
                'additional_contact_first_name' => $investor->additional_contact_first_name,
                'additional_contact_middle_name' => $investor->additional_contact_middle_name,
                'additional_contact_last_name' => $investor->additional_contact_last_name,
                'additional_contact_relationships' => $investor->additional_contact_relationships,
                'additional_contact_mobile' => $investor->additional_contact_mobile,
                'additional_contact_phone' => $investor->additional_contact_phone,
                'additional_contact_email' => $investor->additional_contact_email,
                'additional_contact_comment' => $investor->additional_contact_comment,
                
                'legal_purchase_entity' => $investor->legal_purchase_entity,
                'purchase_comments' => $investor->purchase_comments,
                'acn' => $investor->acn,
                'smsf_purchase' => $investor->smsf_purchase
            ));
        }
    }
    
    private function handle_submit_reserve_form()
    {
        if ($this->user_type_id != USER_TYPE_ADVISOR) {
            send(array(
                'status'=>'FAILED',
                'message' => "Sorry, you don't have permission to do this."
            ));
        }
        $this->load->model('Users_model');
        $this->load->model('Property_model');
        $this->load->helper('email');
        $aFields = array(
            'billing_address1',
            'billing_address2',
            'billing_country_id',
            'billing_postcode',
            'billing_state_id',
            'billing_suburb',
            'comments',
            'company_name',
            'deposit_paid',
            'email',
            'secondary_email',
            'first_name',
            'middle_name',
            'last_name',
            'home_phone',
            'investor_id',
            'mobile',
            'phone',
            'property_id',
            'fax',
            'delivery_address1',
            'delivery_suburb',
            'delivery_postcode',
            'delivery_state_id',
            'additional_contact_first_name',
            'additional_contact_middle_name',
            'additional_contact_last_name',
            'additional_contact_relationships',
            'additional_contact_mobile',
            'additional_contact_phone',
            'additional_contact_email',
            'additional_contact_comment',
            'legal_purchase_entity',
            'purchase_comments',
            'acn',
            'smsf_purchase',            
        );
        foreach ($aFields as $field)
        {
            $$field = $this->input->post($field);
        }
        $advisorID = $this->user_id;
        $advisor = $this->Users_model->get_details($advisorID);
        $investor = $this->Users_model->get_details($investor_id);
        $property = $this->Property_model->get_details($property_id);
        
        $builder = "";
        if(is_numeric($property->builder_id)) {
            $objBuilder = $this->builder_model->get_details($property->builder_id);
            if($objBuilder) {
                $builder = $objBuilder->builder_name;
            }    
        }
        
        $errors = array();
        if (!$investor) $errors[] = "+ Client field is required.";
        if (!$property) $errors[] = "+ Unknown property.";
        if (empty($first_name)) $errors[] = "+ First name field is required.";
        if (empty($last_name)) $errors[] = "+ Last name field is required.";
        if (empty($email)) $errors[] = "+ Email field is required.";
        elseif (!valid_email($email)) $errors[] = "+ Email field is invalid.";
        if (empty($phone)) $errors[] = "+ Work phone field is required.";
        
        if (sizeof($errors)) {
            send(array(
                'status'=>'FAILED',
                'message' => "Please correct the error(s) below:" . "\n" . implode("\n", $errors)
            ));
        } else {
            $propertyData = array(
                'advisor_id' => $this->user_id,
                'investor_id' => $investor_id,
                'reserved_first_name' => $first_name,
                'reserved_last_name' => $last_name,
                'reserved_company_name' => $company_name,
                'reserved_email' => $email,
                'reserved_phone' => $phone,
                'reserved_mobile' => $mobile,
                'reserved_home_phone' => $home_phone,
                'reserved_address1' => $billing_address1,
                'reserved_address2' => $billing_address2,
                'reserved_suburb' => $billing_suburb,
                'reserved_country_id' => $billing_country_id,
                'reserved_state_id' => $billing_state_id,
                'reserved_postcode' => $billing_postcode,
                'reserved_comments' => $comments,
                'reserved_deposit_paid' => $deposit_paid ? 1 : 0,
                'reserved_date' => date('Y-m-d H:i:s'),
                'status' => 'reserved'
            );
            $this->Property_model->save($property_id, $propertyData);
            
            $userData = array(
                'billing_address1' => $billing_address1,
                'billing_address2' => $billing_address2,
                'billing_country_id' => $billing_country_id,
                'billing_postcode' => $billing_postcode,
                'billing_state_id' => $billing_state_id,
                'billing_suburb' => $billing_suburb,
                'company_name' => $company_name,
                'email' => $email,
                'secondary_email' => $secondary_email,
                'first_name' => $first_name,
                'middle_name' => $middle_name,
                'last_name' => $last_name,
                'home_phone' => $home_phone,
                'mobile' => $mobile,
                'phone' => $phone,
                'fax' => $fax,
                'delivery_address1' => $delivery_address1,
                'delivery_suburb' => $delivery_suburb,
                'delivery_postcode' => $delivery_postcode,
                'delivery_state_id' => $delivery_state_id,
                'additional_contact_first_name' => $additional_contact_first_name,
                'additional_contact_middle_name' => $additional_contact_middle_name,
                'additional_contact_last_name' => $additional_contact_last_name,
                'additional_contact_relationships' => $additional_contact_relationships,
                'additional_contact_mobile' => $additional_contact_mobile,
                'additional_contact_phone' => $additional_contact_phone,
                'additional_contact_email' => $additional_contact_email,
                'additional_contact_comment' => $additional_contact_comment,
                'legal_purchase_entity' => $legal_purchase_entity,
                'purchase_comments' => $purchase_comments,
                'acn' => $acn,
                'smsf_purchase' => $smsf_purchase
            );

            $this->Users_model->save($investor_id, $userData);

            // Set the appropriate partner
            $this->Property_model->set_reserved_property_partner($property_id);
            
            $state = $this->db->where('state_id', $billing_state_id)
                    ->get('states')->row();
            $billing_state_name = $state ? $state->name : '';
            $country = $this->db->where('country_id', $billing_country_id)
                    ->get('countries')->row();
            $billing_country_name = $country ? $country->name : '';
            
            $emailData = array(
                'advisor' => $advisor->first_name . " " . $advisor->last_name,
                'partner' => $investor->first_name . " " . $investor->last_name,
                'builder' => $builder,
                'property' => "Lot " . $property->lot . ', ' . $property->address . ' - ' . $property->title,
                'property_lot' => $property->lot,
                'property_address' => $property->address,
                'property_suburb' => $property->suburb,
                'property_postcode' => $property->postcode,
                'property_state' => $property->state,
                'first_name' => $first_name,
                'last_name' => $last_name,
                'email' => $email,
                'company_name' => $company_name,
                'phone' => $phone,
                'home_phone' => $home_phone,
                'mobile' => $mobile,
                'address1' => $billing_address1,
                'address2' => $billing_address2,
                'suburb' => $billing_suburb,
                'postcode' => $billing_postcode,
                'state' => $billing_state_name,
                'country' => $billing_country_name,
                'deposit_paid' => $deposit_paid ? 'Yes' : 'No',
                'comments' => nl2br($comments),
                
                'additional_contact_first_name' => $additional_contact_first_name,
                'additional_contact_middle_name' => $additional_contact_middle_name,
                'additional_contact_last_name' => $additional_contact_last_name,
                'additional_contact_relationships' => $additional_contact_relationships,
                'additional_contact_mobile' => $additional_contact_mobile,
                'additional_contact_phone' => $additional_contact_phone,
                'additional_contact_email' => $additional_contact_email,
                'additional_contact_comment' => nl2br($additional_contact_comment),
                
                'legal_purchase_entity' => nl2br($legal_purchase_entity),
                'purchase_comments' => nl2br($purchase_comments),
                'acn' => $acn,
                'smsf_purchase' => $smsf_purchase,                                
            );
            
            $this->load->model('settings_model');
            $this->load->model('email_model');
            
            $aBcc = array();
            $contactEmails = $this->settings_model->get_contacts("order_notification = 1"); 
            if ($contactEmails) {
                foreach ($contactEmails->result() as $index=>$row)
                {
                    if ($index==0) {
                        $toEmail = $row->email;
                    } else {
                        $aBcc[] = $row->email;
                    }
                }
            }
            
            if (!empty($toEmail)) {
                $this->email_model->send_email($toEmail, "reserve_email", $emailData, '', $aBcc);
            }
            send(array(
                'status'=>'SUCCESS',
            ));
        }
    }
    
    private function handle_submit_reservation_request()
    {
        // Only Lead and Investor can submit the reservation request
        if ($this->user_type_id != USER_TYPE_INVESTOR AND $this->user_type_id != USER_TYPE_LEAD AND $this->user_type_id != USER_TYPE_PARTNER) {
            send(array(
                'status'=>'FAILED',
                'message' => "Sorry, you don't have permission to do this."
            ));
        }
        
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model('Users_model');
        $this->load->model('Property_model');
        $this->load->helper('email');        
        
        // Validate the form submission
        $this->form_validation->set_rules('property_id', 'Property ID', 'required|number'); 
        $this->form_validation->set_rules('first_name', 'First Name', 'required');
        $this->form_validation->set_rules('last_name', 'Last Name', 'required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }        

        $investor = $this->Users_model->get_details($this->user_id);
        $advisor = $this->Users_model->get_details($investor->advisor_id);
        
        // See if we can locate a partner for the investor
        $partner_first_name = "";
        $partner_last_name = "";
        $partner_company_name = "";
        $partner_details = "";
                
        // Load the user that created this user
        $creator = $this->Users_model->get_details($investor->created_by_user_id);
        
        if(($creator) && ($creator->user_type_id == USER_TYPE_PARTNER))
        {
            $partner_first_name = $creator->first_name;
            $partner_last_name = $creator->last_name;
            $partner_company_name = $creator->company_name;
            
            $partner_details = "Partner: " . $partner_first_name;
            if($partner_last_name != "") $partner_details .= " " . $partner_last_name; 
            if($partner_company_name != "") $partner_details .= " (" . $partner_company_name . ")";
        }
        
        $property_id = $this->input->post('property_id');
        $property = $this->Property_model->get_details($property_id);
        if (!$property) {
            send(array(
                'status'=>'FAILED',
                'message' => "Unknown property."
            ));
        } else {
            $this->load->model('email_model');
            $emailData = array(
                'advisor_first_name' => $advisor->first_name,
                'advisor_last_name' => $advisor->last_name,
                'investor_first_name' => $this->input->post("first_name"),
                'investor_middle_name' => $this->input->post("middle_name"),
                'investor_last_name' => $this->input->post("last_name"),
                'investor_mobile' => $this->input->post("mobile"),
                'investor_phone' => $this->input->post("phone"),
                'investor_home_phone' => $this->input->post("home_phone"),
                'additional_contact_first_name' => $this->input->post("additional_contact_first_name"),
                'additional_contact_last_name' => $this->input->post("additional_contact_last_name"),
                'additional_contact_relationship' => $this->input->post("additional_contact_relationships"),
                'additional_contact_phone' => $this->input->post("additional_contact_phone"),
                'additional_contact_mobile' => $this->input->post("additional_contact_mobile"),
                'partner_details' => $partner_details,
                'property_lot' => $property->lot,
                'property_address' => $property->address,
                'property_suburb' => $property->suburb,
                'property_postcode' => $property->postcode,
                'property_state' => $property->state,
                'property_url' => site_url("stocklist/detail/$property_id"),
            );
            
            switch ($this->user_type_id) {
                case USER_TYPE_INVESTOR:
                    // Investor submit reservation request;
                    $emailData['investor_url'] = site_url("investors/detail/$this->user_id");
                break;
                
                case USER_TYPE_LEAD:
                    // Lead submit reservation request;
                    $emailData['investor_url'] = site_url("leads/detail/$this->user_id");
                break;
                
                case USER_TYPE_PARTNER:
                    // Partner submit reservation request;
                    $emailData['investor_url'] = site_url("partners/detail/$this->user_id");
                break;
            
                default:
                    // Investor submit reservation request;
                    $emailData['investor_url'] = site_url("investors/detail/$this->user_id");
                break;
            }
            
            //send email users checked as get "order notification" as BCC
            
            $this->load->model("settings_model");
            $aBcc = array();
            $order_contacts = $this->settings_model->get_contacts("order_notification = 1"); 
            
            if($order_contacts)
            {
                foreach($order_contacts->result() as $row)
                    $aBcc[] = $row->email;                    
            }
            
            $toEmail = $advisor->email;
            $this->email_model->send_email($toEmail, "reservation_request", $emailData, '', $aBcc);
            send(array(
                'status'=>'SUCCESS',
            ));
        }
    }
    
    private function handle_save_comments()
    {
        $property_id = (int) $this->input->post('property_id');
        $data = array(
            'advisor_comments_other' => $this->input->post('advisor_comments_other'),
            'commission_comments' => $this->input->post('commission_comments'),
            'commission_sharing_user_id' => $this->input->post('commission_sharing_user_id'),
        );
        $this->property_model->save($property_id, $data);
        echo 'OK';
        exit();
    }
	
	private function handle_assign_stock_permission()
	{
		$this->load->model('property_model');
		$this->load->model('project_model');
		$this->load->model('users_model');
        $this->load->model('Property_permissions_model','ppmd');
        
    	$user_id = $this->input->post("user_id");
    	$property_id = $this->input->post("property_id");
		
		$property = $this->property_model->get_details($property_id);
		$project_id = $property->project_id;
       
	   if ($property_id)
            {
				$exists_property = $this->ppmd->exists_property($property_id,$user_id);
				
				if (!$exists_property) {
					$data = array(
            					'permission_type' => 'Property',
            					'foreign_id' => $property_id,
            					'user_id' => $user_id,
            					'created_dtm' => date("Y-m-d %H:%i:%s")
            				);
    				$property_permissions_id = $this->ppmd->save('',$data);
					
				}
				
				$this->data["status"] = "OK";
            	
            }
		if ($project_id)
            {
				$exists_property = $this->ppmd->exists_property($project_id,$user_id);
				
				if (!$exists_property) {
					$data = array(
            					'permission_type' => 'Project',
            					'foreign_id' => $project_id,
            					'user_id' => $user_id,
            					'created_dtm' => date("Y-m-d %H:%i:%s")
            				);
    				$property_permissions_id = $this->ppmd->save('',$data);
					echo $this->db->last_query();
					exit();
				}
				
				$this->data["status"] = "OK";
            	send($this->data);
            }
		
	}
    
    private function patch_partner_ids()
    {
        $this->db->select("property_id");
        $this->db->from("properties");
        $this->db->where("status <> 'pending' and status <> 'available'");
        
        $rst = $this->db->get();
        
        foreach($rst->result() as $row)
        {
            $property_id = $row->property_id;
            $this->property_model->set_reserved_property_partner($property_id);  
        }    
    }
	
	function downloads($type,$foreign_id,$doc_type,$filename)
	{

		echo $path = $type.'/'.$foreign_id.'/'.$doc_type.'/'.$filename;
		redirect($path);
	}
}