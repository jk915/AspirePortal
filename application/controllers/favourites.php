<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Favourites extends MY_Controller 
{
	public $data;        // Will be an array used to hold data to pass to the views.
    
    function Favourites()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("Favourites_model");
    }
    
    function test($action = "add_favourite")
    {
        $_POST["action"] = $action;
        
        // Test adding
        if($action == "add_favourite")
        {
            $_POST["foreign_id"] = 143;    
            $_POST["foreign_type"] = "property";
            
            $this->ajax();
        }
        // Test deleting
        else if($action == "delete_favourite")
        {
            $_POST["favourite_id"] = 4;    
            $_POST["foreign_type"] = "property";
            
            $this->ajax();
        }        
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
            case "load_favourites":
                $this->handle_load_favourites();
                break;
                            
            case "add_favourite":  
                $this->handle_add_favourite();
                break;

            case "delete_favourite":
                $this->handle_delete_favourite();
                break;                                
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }
    }
    
    
    /***
    * @todo Need to code this method 
    */
    function handle_load_favourites()
    {
    }
    
    /***
    * Handles the event when the user wants to add a favourite.
    */
    private function handle_add_favourite()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('foreign_type', 'Favourite Type', 'required'); 
        $this->form_validation->set_rules('foreign_id', 'Favourite ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $favourite = array();
        $favourite["created_dtm"] = date("Y-m-d H:i:s");
        $favourite["foreign_type"] = $this->input->post("foreign_type");
        $favourite["foreign_id"] = $this->input->post("foreign_id");
        $favourite["user_id"] = $this->user_id;
        
        // See if this favourite already exists
        $result = $this->Favourites_model->get_list($favourite);
        if($result)
        {
            $this->data["message"] = "This item is already on your favourites list.";
            send($this->data);         
        }        
        
        
        // Save/add the favourite
        if(!$this->Favourites_model->save("", $favourite))
        {
            $this->data["message"] = "Sorry, something went wrong whilst trying to add this favourite.";
            send($this->data);             
        }

        // Delete worked correctly.   Send the OK back.
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        send($this->data);            
    }    
    
    /***
    * Handles the event when the user wants to delete a favourite.  We ensure that the user can only
    * delete a favourite that belongs to them.
    */
    private function handle_delete_favourite()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('favourite_id', 'Favourite ID', 'required|number'); 
        $this->form_validation->set_rules('foreign_type', 'Favourite Type', 'required');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $favourite_id = $this->input->post("favourite_id");
        $foreign_type = $this->input->post("foreign_type");
        
        // Load the note details.
        $favourite = $this->Favourites_model->get_details($favourite_id);
        
        // Make sure the user has permission to delete this favourite
        if(!$favourite)
        {
            $this->data["message"] = "Sorry, you do not have permission to delete this favourite.";
            send($this->data);            
        }        
        
        // Make sure the user has permission to delete this favourite
        if((!$favourite) || ($favourite->user_id != $this->user_id) || ($favourite->foreign_type != $foreign_type))
        {
            $this->data["message"] = "Sorry, you do not have permission to delete this favourite.";
            send($this->data);            
        }
        
        // Delete the favourite
        if(!$this->Favourites_model->delete($favourite_id))
        {
            $this->data["message"] = "Sorry, something went wrong whilst trying to delete this favourite.";
            send($this->data);             
        }

        // Delete worked correctly.   Send the OK back.
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        send($this->data);            
    }
}