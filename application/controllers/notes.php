<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER);

class Notes extends MY_Controller 
{
	public $data;        // Will be an array used to hold data to pass to the views.
    
    function Notes()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("Notes_model");
    }
    
    function index()
    {
    	
    }
    
    function detail()
    {
    	
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
            case "update_note":  
                $this->handle_update_note();
                break;
                
            case "load_notes":
                $this->handle_load_notes();
                break;
                
            case "load_note":
                $this->handle_load_note();
                break; 
                
            case "delete_note":
                $this->handle_delete_note();
                break;                                
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }
    }
    
    /***
    * Handles the update_note action
    * Send an OK status back if the note was updated/added successfully, error if not.
    */
    private function handle_update_note()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        // Validate the form submission
        $this->form_validation->set_rules('note_date', 'Note Date', 'required|callback_date_check');
        $this->form_validation->set_rules('content', 'Note Details', 'required');
        $this->form_validation->set_rules('note_id', 'Note ID', 'integer');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $note_id = $this->input->post("note_id");
        $add_mode = false;
        $note = false;
        
        if(is_numeric($note_id))
        {
            // We're updating an existing note.  Load the note details.
            $note = $this->Notes_model->get_details($note_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if((!$note) || ($note->created_by != $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }            
        }
        else
        {
            // We are inserting a new note.
            $add_mode = true;
        }
        
        
        $save = array();
        $save["last_modified_by"] = $this->user_id;
        $save["last_modified"] = date("Y-m-d H:i:s");
        $save["note_type"] = 'user';
        $save["content"] = $this->input->post("content");
        $save["foreign_id"] = $this->input->post("lead_id");
        $save["private"] = ($this->input->post("private") == 1) ? 1 : 0;
        
        if($this->input->post("note_date") != "")
        {
            $save["note_date"] = $this->utilities->uk_to_isodate($this->input->post("note_date"));        
        }
        
        //die("STATUS: " . $this->input->post("status"));
        
        if($add_mode)
        {
            // When adding a new record, set the username to the email
            $save["created_by"] = $this->user_id;
            $save["created_date"] = date("Y-m-d H:i:s");

        }
        
        $note_id = $this->Notes_model->save($note_id, $save);
        if(!$note_id)
        {
            $this->data["message"] = "Sorry, someone went wrong whilst trying to save this note.";
            send($this->data);             
        }
        
        $this->data["status"] = "OK";
        $this->data["message"] = "";
                
        send($this->data);
    }
    
    function handle_load_notes()
    {
    	
    }
    
    /***
    * Handles the load_note action
    * Loads a single note and sends the data back as json
    */
    private function handle_load_note()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('note_id', 'Note ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $note_id = $this->input->post("note_id");
        
        // Load the note details.
        $note = $this->Notes_model->get_details($note_id);
        
        // Sends the note attributes back to the client
        $note_data = get_object_vars($note);
        $note_data["note_date"] = $this->utilities->iso_to_ukdate($note_data["note_date"]);
        

        $this->data["status"] = "OK";
        $this->data["message"] = $note_data;
        
        send($this->data);
    }
    
    private function handle_delete_note()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('note_id', 'Note ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $note_id = $this->input->post("note_id");
        
        // Load the note details.
        $note = $this->Notes_model->get_details($note_id);
        
        // Make sure the user has permission to delete this note
        if($note->created_by != $this->user_id)
        {
            $this->data["message"] = "Sorry, you do not have permission to delete this note.";
            send($this->data);            
        }
        
        // Delete the note
        $this->Notes_model->delete($note_id, $this->user_id);

        // Delete worked correctly.   Send the OK back.
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        send($this->data);            
    }
    
    /*** Ensure a date is a valid UK date ******/
    public function date_check($str)
    {
        if($str == "") return true;
        
        //$result = strptime($str, '%d/%m/%Y');
        
        $result = date('d/m/Y');
        if(!$result)
        {
            return false;
        }    
        
        return true;
    }
}