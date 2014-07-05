<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR);

class Summaries extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Summaries()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("Summaries_model");
        $this->load->model("area_model");
        $this->load->model("project_model");
        $this->load->model("Users_model");
    }
   
    function index()
    {
        $this->data["meta_title"] = "My Summaries";
        
        // Load all the data that we need for the views
        $this->data["priorities"] = $this->Summaries_model->get_priorities();
        $this->data["statuses"] = $this->Summaries_model->get_statuses();
        
        // Load assign users
        $filters = array();
        
    	$user_logged = $this->users_model->get_details($this->user_id);
        
        if($user_logged->user_type_id != USER_TYPE_ADVISOR)
            redirect("dashboard");   
        
        $filters["deleted"] = 0;
        $filters["created_by"] = $user_logged->user_id;
        
        // Load the state options for Australia
        $this->data["states"] = $this->tools_model->get_states(1);
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/summaries/list/prebody.php', $this->data); 
        $this->load->view('member/summaries/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function detail($summary_id = "")
    {
        $add_mode = !is_numeric($summary_id);
        $this->data["user"] = false;
        
        $user_logged = $this->users_model->get_details($this->user_id);
        if($user_logged->user_type_id != USER_TYPE_ADVISOR)
            redirect("dashboard");   
        
        if($add_mode)
        {
            $this->data["meta_title"] = "Add New Summary";    
        }
        else
        {
            $this->data["meta_title"] = "Summary Detail";
        
            $this->data["summary"] = $this->Summaries_model->get_details($summary_id);
            
            if($this->data["summary"])
            {
                if($this->data["summary"]->state_id != '')
                    $this->data["areas"] = $this->area_model->get_list(1, "", "", $count_all, "", "area_name ASC", "", array('state_id' => $this->data["summary"]->state_id));
                if($this->data["summary"]->area_id != '')
                    $this->data["projects"] = $this->project_model->get_list(1,"", "", $count_all, "", "p.project_name ASC", array('area_id' => $this->data["summary"]->area_id));
            }
            
        }
        
        $this->data["add_mode"] = $add_mode; 
        
        $this->data["states"] = $this->tools_model->get_states(1);
        $this->data["partners"] = $this->Users_model->get_partners_by_advisor_id($user_logged->user_id);
        $this->data["investors"] = $this->Users_model->get_investor_by_advisor_id($user_logged->user_id);
        $this->data["enquiries"] = $this->Users_model->get_enquiries_by_advisor_id($user_logged->user_id);
        $this->load->view('member/header', $this->data);
        $this->load->view('member/summaries/detail/prebody.php', $this->data); 
        $this->load->view('member/summaries/detail/main.php', $this->data);
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
            case "update_summary":  
                $this->handle_update_summary();
                break;
                
            case "load_summaries":
                $this->handle_load_sumaries();
                break;
                
            case "load_summary":
                $this->handle_load_summary();
                break; 
                
            case "delete_summary":
                $this->handle_delete_summary();
                break;   

            case "load_areas":
                $this->handle_load_areas();
                break;
                
            case "load_projects":
                $this->handle_load_projects();
                break;

            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    private function handle_delete_summary()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('summary_id', 'Summary ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $summary_id = $this->input->post("summary_id");
        
        // Load the summary details.
        $summary = $this->Summaries_model->get_details($summary_id);
        
        // Make sure the user has permission to delete this summary
        if($summary->created_by != $this->user_id AND $summary->assign_to != $this->user_id)
        {
            $this->data["message"] = "Sorry, you do not have permission to delete this summary.";
            send($this->data);            
        }
        
        // Delete the summary
        $this->Summaries_model->delete($summary_id, $this->user_id);

        // Delete worked correctly.   Send the OK back.
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        send($this->data);            
    }
    
    /***
    * Handles the load_summary action
    * Loads a single summary and sends the data back as json
    */
    private function handle_load_summary()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('summary_id', 'Summary ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $summary_id = $this->input->post("summary_id");
        
        // Load the summary details.
        $summary = $this->Summaries_model->get_details($summary_id);
        
        // Sends the summary attributes back to the client
        $summary_data = get_object_vars($summary);
        $this->data["status"] = "OK";
        $this->data["message"] = $summary_data;
        
        send($this->data);
    }
    
    /***
    * Handles the load_summaries action
    * Loads a list of summaries in accordance with search params
    */
    private function handle_load_sumaries()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('current_page', 'Current Page', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        // Sort By Columns
        $valid_columns = array(
        						"s.title",
        						's.created_date',
        						's.description',
        						'a.area_name',
        						'p.project_name',
        						's.prepared_for',
        						'st.name'
    						);
    						
        $valid_dirs = array("ASC", "DESC");
        
        if((!in_array($this->input->post("sort_col"), $valid_columns))
            || (!in_array($this->input->post("sort_dir"), $valid_dirs)))
        {
            $this->data["message"] = "Invalid sort parameters";
            send($this->data);            
        }
        
        $current_page = $this->input->post("current_page");
        
        $filters = array();
        $filters["created_by"] = $this->user_id; 
        $filters["search_term"] = $this->input->post("search_term");
        
        
        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        
        $summaries = $this->Summaries_model->get_list($filters, $order_by, SUMMARIES_PER_PAGE, $current_page, $count_all);        
        
        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/summaries/list/list", array("summaries" => $summaries), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
    
    
    private function handle_load_areas()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('state_id', 'State ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $state_id = $this->input->post("state_id");
        
        $filters = array();
        $filters["state_id"] = $state_id;     
        
        $areas = $this->area_model->get_list(1, "", "", $count_all, "", "area_name ASC", "", $filters);
        if($areas)
        {
            $html = $this->utilities->print_select_options($areas,"area_id","area_name", "", "Choose");
        }
        else
            $html = "";
        $this->data["status"] = "OK";
        $this->data["html"] = $html;
        
        send($this->data);
    }
    
    
    private function handle_load_projects()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('area_id', 'Area ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $area_id = $this->input->post("area_id");
        
        $filters = array();
        $filters["area_id"] = $area_id;     
        
        $projects = $this->project_model->get_list(1,"", "", $count_all, "", "p.project_name ASC", $filters);
        if($projects)
        {
            $html = $this->utilities->print_select_options($projects,"project_id","project_name", "", "Choose");
        }
        else
            $html = "";
        $this->data["status"] = "OK";
        $this->data["html"] = $html;
        
        send($this->data);
    }
                   
    
    /***
    * Handles the update_summary action
    * Send an OK status back if the summary was updated/added successfully, error if not.
    */
    private function handle_update_summary()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        // Validate the form submission
        $this->form_validation->set_rules('title', 'Summary Title', 'required');
        $this->form_validation->set_rules('description', 'Summary Description', 'required');
        $this->form_validation->set_rules('summary_id', 'Summary ID', 'integer');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $summary_id = $this->input->post("summary_id");
		
        $add_mode = false;
        $summary = false;
        
        if(is_numeric($summary_id))
        {
            // We're updating an existing summary.  Load the summary details.
            $summary = $this->Summaries_model->get_details($summary_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if((!$summary) || ($summary->created_by != $this->user_id AND $summary->assign_to != $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }            
        }
        else
        {
            // We are inserting a new summary.
            $add_mode = true;
        }
        
        
        $save = array();
        $save["last_modified_by"] = $this->user_id;
        $save["last_modified"] = date("Y-m-d H:i:s");
        $save["title"] = $this->input->post("title");    
        $save["description"] = $this->input->post("description");
        $save["state_id"] = $this->input->post("state_id");
        $save["area_id"] = $this->input->post("area_id");
        $save["project_id"] = $this->input->post("project_id");
        
        $manual_type = $this->input->post("manual_type");
        if($manual_type == 'on')
            $save["prepared_for"] = $this->input->post("prepared_for_manual");
        else
            $save["prepared_for"] = $this->input->post("prepared_for");
        
        
        if($add_mode)
        {
            // When adding a new record, set the username to the email
            $save["created_by"] = $this->user_id;
            $save["created_date"] = date("Y-m-d H:i:s");

        }
        $summary_id = $this->Summaries_model->save($summary_id, $save);
        if(!$summary_id)
        {
            $this->data["message"] = "Sorry, someone went wrong whilst trying to save this summary.";
            send($this->data);             
        }
        
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