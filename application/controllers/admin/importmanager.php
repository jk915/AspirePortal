<?php
define("FIELDTYPE_STRING", 1);
define("FIELDTYPE_INTEGER", 2);
define("FIELDTYPE_FLOAT", 3);
define("FIELDTYPE_BOOLEAN", 4);

class Importmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $fields;
    private $message;
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array(); 
        $this->fields = array();
        $this->field_map = array();
        $this->message = "";           
        
        // Load models etc        
        $this->load->model("product_model");
        $this->load->library("utilities");    
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Import Manager";
        $this->data["page_heading"] = "Import Manager";
        
        if(!is_dir(ABSOLUTE_PATH."uploads/import"))
        {
            @mkdir(ABSOLUTE_PATH."uploads/import" ,0777); 
            
            if(!is_dir(ABSOLUTE_PATH."uploads/import"))
            {              
                show_error("Unable to create uploads/import directory - please create the uploads/import directory and make it web writable");
            }
        }

        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/importmanager/prebody', $this->data); 
        $this->load->view('admin/importmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    //handles all ajax requests within this page
    function ajaxwork($import_type = "", $filename = "")
    {
        // Load the qq uploader library
        $this->load->library("qqFileUploader");
                
        // Make sure an import type is specified
        if($import_type == "")
        {
            $import_type = $this->input->post("import_type"); 
            
            if($import_type == "")
            {
                die("{error: 'Invalid Import Type'}");    
            }               
        }
        
        // Handle the upload and move it to the uploads/import folder
        $path = ABSOLUTE_PATH . "uploads/import/";
        
        $result = $this->qqfileuploader->handleUpload($path, $filename, true);

        if($filename == "")
        {
            $filename = $this->qqfileuploader->file->getName();

            if($filename == "")
            {
                die ('{error: "Could not determine file name"}');
            }                 
        } 

        // Make sure the upload worked and get the file extension.
        $file_path = $path . $filename;
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));              
        $message = "";
        
        if(!file_exists($file_path))
        {
            die ('{error: "Upload failure"}');    
        }
        
        if($ext != "csv")
        {
            die ('{error: "Invalid upload file.  Must be a CSV file."}');        
        }
       
        switch($import_type)
        { 
            case "stock":
                $this->handle_stock_import($file_path);
                break;
                
            default:
                die ('{error: "Invalid import type."}');        
                break;
        }             
    }
    
    /***
    * Handles the stock import
    * 
    * @param string $file_path The path to the uploaded file.
    */
    private function handle_stock_import($file_path)
    {
        $this->load->model("property_model");
        $this->load->model("document_model");
        $this->load->model("property_stages_model");

        $this->add_message("The file was successfully uploaded.");
                    
        $fp = fopen($file_path, "r");
        if(!$fp)
        {
            die ('{error: "Could not open import file."}');     
        }
        
        $i = 0;
        $row = 0;
        $rowOk = 0;
        
        // Define the possible import fields.
        $this->fields["TITLE"] = new Field(true, FIELDTYPE_STRING, "title");
        $this->fields["LOT"] = new Field(true, FIELDTYPE_INTEGER, "lot");
        $this->fields["ADDRESS"] = new Field(true, FIELDTYPE_STRING, "address");
        $this->fields["SUBURB"] = new Field(true, FIELDTYPE_STRING, "suburb");
        $this->fields["PROJECT"] = new Field(true, FIELDTYPE_STRING, "project_id");
        $this->fields["STATE"] = new Field(false, FIELDTYPE_STRING, "state_id");
        $this->fields["POSTCODE"] = new Field(false, FIELDTYPE_INTEGER, "postcode");
        $this->fields["AREA"] = new Field(false, FIELDTYPE_STRING, "area_id");
        $this->fields["BEDROOMS"] = new Field(false, FIELDTYPE_INTEGER, "bedrooms");
        $this->fields["BATHROOMS"] = new Field(false, FIELDTYPE_INTEGER, "bathrooms");
        $this->fields["GARAGE"] = new Field(false, FIELDTYPE_INTEGER, "garage");
        $this->fields["HOUSE AREA"] = new Field(false, FIELDTYPE_FLOAT, "house_area");
        $this->fields["LAND AREA"] = new Field(false, FIELDTYPE_FLOAT, "land");
        $this->fields["HOUSE PRICE"] = new Field(false, FIELDTYPE_FLOAT, "house_price");
        $this->fields["LAND PRICE"] = new Field(false, FIELDTYPE_FLOAT, "land_price");
        $this->fields["TOTAL PRICE"] = new Field(false, FIELDTYPE_FLOAT, "total_price");
        $this->fields["ENABLED"] = new Field(false, FIELDTYPE_BOOLEAN, "enabled");
        $this->fields["ARCHIVED"] = new Field(false, FIELDTYPE_BOOLEAN, "archived");
        $this->fields["FEATURED"] = new Field(false, FIELDTYPE_BOOLEAN, "featured");
        $this->fields["STUDY"] = new Field(false, FIELDTYPE_BOOLEAN, "study");
        $this->fields["NRAS"] = new Field(false, FIELDTYPE_BOOLEAN, "nras");
        $this->fields["NRAS PROVIDER"] = new Field(false, FIELDTYPE_STRING, "nras_provider");
        $this->fields["NRAS RENT"] = new Field(false, FIELDTYPE_FLOAT, "nras_rent");
        $this->fields["NRAS FEE"] = new Field(false, FIELDTYPE_FLOAT, "nras_fee");
        $this->fields["SMSF"] = new Field(false, FIELDTYPE_BOOLEAN, "smsf");
        $this->fields["DESIGN"] = new Field(false, FIELDTYPE_STRING, "design");
        $this->fields["FACADE"] = new Field(false, FIELDTYPE_STRING, "facade");
        $this->fields["FRONTAGE"] = new Field(false, FIELDTYPE_STRING, "frontage");
        $this->fields["APPROX RENT"] = new Field(false, FIELDTYPE_FLOAT, "approx_rent");
        $this->fields["RENT YIELD"] = new Field(false, FIELDTYPE_STRING, "rent_yield");
        
        
        define("IMPORT_NUM_FIELDS", count($this->fields));
        $success = true;
        
        $this->db->trans_start();
        
        while(($line = fgetcsv($fp)) !== FALSE) 
        {
            $row++;
            
            $line_num_fields = count($line);
            
            // The first line must contain the field headers. 
            // Map the fields 
            if($row == 1)
            {
                define("IMPORT_LINE_NUM_FIELDS", $line_num_fields);
                
                try
                {
                    $this->map_fields($line);
                    
                    if(!$this->all_required_fields_present())
                    {
                        break;    
                    }
                }
                catch(Exception $e)
                {
                    // An error occured during mapping.
                    // Report the error and exit the import
                    $this->add_message("Fatal Error: " . $e->getMessage(), true);
                    $success = false;
                    break;
                }   
            }
            else
            {
                // Check to see if this line has an inconcistent number of fields
                if($line_num_fields != IMPORT_LINE_NUM_FIELDS)
                {
                    $this->add_message("Invalid field count at row $row", true);
                    continue;
                }  
                
                // Read the line data into the field objects
                if(!$this->read_line($line))
                {
                    continue;    
                }
                
                // Make sure all required fields are set
                if(!$this->all_required_fields_set())
                {
                    continue;    
                }
                
                // Get the field data
                $field_data = $this->get_field_data();
                
                // Perform transformations as needed
                $project_id = null;
                
                /********* PROJECT NAME --> PROJECT ID ******************/
                if(array_key_exists("project_id", $field_data))
                {
                    $project_name = $field_data["project_id"];
                    unset($field_data["project_id"]);

                    if($project_name != "")
                    {
                        $this->db->where("project_name", $project_name);
                        
                        $result = $this->db->get("projects");  
                        
                        if($result->num_rows() == 1)
                        {
                            $this_row = $result->row();
                            $project_id = $this_row->project_id;    
                        }
                    }    
                }
                
                if(is_null($project_id))
                {
                    $this->add_message("Invalid Project name on row $row", true);
                    continue;    
                }
                
                /********* STATE NAME --> STATE ID ******************/
                if(array_key_exists("state_id", $field_data))
                {
                    $state_name = $field_data["state_id"];
                    $field_data["state_id"] = null;
                    
                    if($state_name != "")
                    {
                        $this->db->where("((name LIKE '" . $state_name . "') || (preferredName LIKE '" . $state_name . "'))");
                        $result = $this->db->get("states");  
                        
                        if($result->num_rows() == 1)
                        {
                            $this_row = $result->row();
                            $field_data["state_id"] = $this_row->state_id;    
                        }
                    }    
                }
                
                /********* AREA NAME --> AREA ID ******************/
                if(array_key_exists("area_id", $field_data))
                {
                    $area_name = $field_data["area_id"];
                    $field_data["area_id"] = null;
                    
                    if($area_name != "")
                    {
                        $this->db->where("area_name", $area_name);
                        $result = $this->db->get("areas");  
                        
                        if($result->num_rows() == 1)
                        {
                            $this_row = $result->row();
                            $field_data["area_id"] = $this_row->area_id;    
                        }
                    }    
                }
                
                // Is there a matching property in the database already?  If so, update it, otherwise ignore.
                $this->db->where("lot", $field_data["lot"]);                 
                $this->db->where("address", $field_data["address"]);
                $this->db->where("suburb", $field_data["suburb"]);
                
                $result = $this->db->get("properties");  
                $property_id = "";
                $new_property = true;
                
                if($result->num_rows() >= 1)
                {
                    $new_property = false;
                    $this_row = $result->row();
                    $property_id = $this_row->property_id;
                    
                    // Remove any existing property_project records
                    $this->property_model->delete_projects($property_id);
                }
                
                $property_id = $this->property_model->save($property_id, $field_data);
                
                if($property_id)
                {
                    if(!$this->db->insert("property_project", array("project_id" => $project_id, "property_id" => $property_id)))
                    {
                        $this->add_message("Error setting property project", true);
                        $success = false;
                        break;
                    }
                    
                    if($new_property)
                    {
                        $this->document_model->add_default_documents("property_document", $property_id);
                        $this->property_stages_model->add_default_property_stages($property_id);                        
                    }
                    
                    $rowOk++;   
                }
                else
                {
                    $this->add_message("Error trying to save row $row into the database", true);        
                }
            }   
        } 
        
        fclose($fp); 
        
        if($success)
        {
            $this->db->trans_complete();
            
            $this->add_message($rowOk." row(s) imported successfully.");
            echo json_encode(array("success" => 1, "message" => '<ul>' . $this->message . '</ul>'));
            exit();
        }
        else
        {
            echo json_encode(array("success" => 0, "message" => '<ul>' . $this->message . '</ul>'));
            exit();            
        }
    }
    
    /***
    * Reads the values in the specified line array into the field objects.
    * Checks if a required value is missing OR if a value is of the incorrect type, in which case
    * false is returned.  
    * 
    * @param array $line The line data
    * @returns true if the line was read in without error.
    */
    private function read_line($line)
    {
        $position = 0;
        
        foreach($line as $field_value)
        {
            $field_name = $this->field_map[$position];
            $field = $this->fields[$field_name];
            
            try
            {
                $field->set_value($field_value);
            }
            catch(Exception $e)
            {
                $this->add_message("Error: " . $e->getMessage(), true);
                return false;    
            }
            
            $position++;
        }
        
        return true;    
    }
    
    /***
    * Checks to see if all required fields are present
    * @returns true if all required fields are present, false if not.
    */
    private function all_required_fields_present()
    {
        if(count($this->fields) == 0)
        {
            $this->add_message("No import fields found in the CSV", true);
            return false;
        }
        
        $missing_fields = 0;
        
        foreach($this->fields as $field_name => $field)
        {
            if(($field->is_required()) && (is_null($field->get_position())))
            {
                $this->add_message("The required field $field_name is missing", true);
                $missing_fields++;   
            }
        }
        
        return ($missing_fields == 0);
    }
    
    /***
    * @returns An associative array of data ready for inserting into the database
    */
    private function get_field_data()
    {
        $field_data = array();
        
        foreach($this->fields as $field_name => $field)
        {
            if(!is_null($field->get_value()))
            {
                $field_data[$field->get_db_field_name()] = $field->get_value();   
            }
        }
        
        return $field_data;
    }    
    
    /***
    * Checks to see if all required fields are set
    * @returns true if all required fields are set, false if not.
    */
    private function all_required_fields_set()
    {
        $missing_fields = 0;
        
        foreach($this->fields as $field_name => $field)
        {
            if(($field->is_required()) && (is_null($field->get_value())))
            {
                $this->add_message("The required field $field_name is missing", true);
                $missing_fields++;   
            }
        }
        
        return ($missing_fields == 0);
    }    

    /***
    * Adds a message to be sent back to the import manager at the end.
    * 
    * @param string $message The message to add
    * @param boolean $error Set to true if the message should be rendered as an error
    */
    private function add_message($message, $error = false)
    {
        $this->message .= '<li';
        
        if($error)
        {
            $this->message .= ' class="error"';
        }
        
        $this->message .= '>' . $message . '</li>';    
    }
    
    /***
    * Maps the fields defined in the CSV file to the possible import fields
    * 
    * @param array $line An array containing the CSV header line.
    */
    private function map_fields($line)
    {
        $position = 0;
        
        foreach($line as $this_field)
        {
            $this_field_upper = strtoupper($this_field);
            
            if(!array_key_exists($this_field_upper, $this->fields))
            {
                throw new Exception("The field $this_field is not a valid import field");        
            }
            
            $this->fields[$this_field_upper]->set_position($position);
            $this->field_map[$position] = $this_field_upper;
            
            $position++;
        }    
    }  
}

class Field
{
    private $required; 
    private $field_type;
    private $db_field_name;
    private $value; 
    private $position;
    
    public function __construct($required, $field_type, $db_field_name)
    {
        $this->required = $required; 
        $this->field_type = $field_type;
        $this->db_field_name = $db_field_name;
        $this->position = null;
        
        $this->initialise();
    }
    
    public function initialise()
    {
        $this->value = null;        
    }
    
    public function is_required()
    {
        return $this->required;
    }  
    
    public function get_field_type()
    {
        return $this->field_type;
    }
    
    public function get_position()
    {
        return $this->position;
    }
    
    public function get_db_field_name()
    {
        return $this->db_field_name;
    }     
    
    public function get_value()
    {
        return $this->value;
    }                
    
    public function set_position($pos)
    {
        $this->position = $pos; 
    } 
    
    public function set_value($val)
    {
        if($this->required && (($val == "") || (is_null($val)) || empty($val)))
        {
            throw new Exception("Required field " . $this->db_field_name . " missing");    
        }
        
        if($val == "")
        {
            $this->value = null;    
            return true;
        }
        
        if($this->field_type == FIELDTYPE_INTEGER)
        {
            if(!is_numeric($val))
            {
                throw new Exception("Invalid integer value $val");
            } 
            
            $val = intval($val);   
        }
        else if($this->field_type == FIELDTYPE_FLOAT)
        {
            if(!is_numeric($val))
            {
                throw new Exception("Invalid integer value $val");
            }   
        } 
        else if($this->field_type == FIELDTYPE_BOOLEAN)
        {
            $match = strtoupper($val);
            if(($match == "YES") || ($match == "Y") || ($match == "T") || ($match == "TRUE") || ($match == "1"))
            {
                $val = 1;    
            }
            else
            {
                $val = 0;
            }  
        }               
        
        $this->value = $val; 
    }            
}