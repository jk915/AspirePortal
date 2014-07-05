<?php
// Restrict access to this controller to specific user types
define("RESTRICT_ACCESS", USER_TYPE_ADVISOR . "," . USER_TYPE_SUPPLIER . "," . USER_TYPE_INVESTOR . "," . USER_TYPE_PARTNER . "," . USER_TYPE_LEAD);

class Tasks extends MY_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function Tasks()
    {
        $this->data = array();
        
        parent::__construct();
        
        $this->load->model("Tasks_model");
    }
   
    function index()
    {
        $this->data["meta_title"] = "My Tasks";
        
        // Load all the data that we need for the views
        $this->data["priorities"] = $this->Tasks_model->get_priorities();
        $this->data["statuses"] = $this->Tasks_model->get_statuses();
        
        // Load assign users
        $filters = array();
        
        $user_ids = array();
    	$user_logged = $this->users_model->get_details($this->user_id);
    	$user_ids[] = $user_logged->advisor_id;
    	$user_ids[] = $user_logged->created_by_user_id;
    	$user_ids[] = $this->user_id;
    	$filters['in_arr_ids'] = $user_ids;
        
        if (in_array($this->user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER)))
        {
        	$filters['created_by'] = $this->user_id;
        }
        
        $user_type = array(USER_TYPE_ADVISOR, USER_TYPE_INVESTOR, USER_TYPE_PARTNER, USER_TYPE_LEAD);
        	
        $filters["deleted"] = 0;
        $filters["order_by"] = "u.first_name ASC";
        
        $assign_client_select_sql = ", CASE " .
            "WHEN (length(u.company_name) > 0) THEN CONCAT(u.first_name, ' ', u.last_name, ' (', u.company_name, ')') " .
            "ELSE CONCAT(u.first_name, ' ', u.last_name) " .
            "END as assign_client_name";
        
        $assign_users = $this->users_model->get_list(1, '', '', $count_all, "", $user_type, $filters, $assign_client_select_sql);
        $this->data['assign_users'] = $assign_users;
        
        $this->load->view('member/header', $this->data);
        $this->load->view('member/tasks/list/prebody.php', $this->data); 
        $this->load->view('member/tasks/list/main.php', $this->data);
        $this->load->view('member/footer', $this->data); 
    }
    
    function detail($user_id = "")
    {
        $add_mode = !is_numeric($user_id);
        $this->data["user"] = false;
        
        if($add_mode)
        {
            $this->data["meta_title"] = "Add New Investor";    
        }
        else
        {
            // Load the user object
            $user = $this->Users_model->get_details($user_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if((!$user) || ($user->created_by_user_id != $this->user_id))
            {
                redirect("/investors");    
            }
            
            $this->data["user"] = $user; 
            
            // Load the user statistics
            // @todo Make Stats function for investors
            $this->data["stats"] = $this->Users_model->get_investor_stats($user_id);
            
            $this->data["meta_title"] = "Investor Detail";
        }
        
        $this->data["add_mode"] = $add_mode; 
        
        // Load the state options for Australia
        $this->data["states"] = $this->tools_model->get_states(1);
       
        $this->load->view('member/header', $this->data);
        $this->load->view('member/investors/detail/prebody.php', $this->data); 
        $this->load->view('member/investors/detail/main.php', $this->data);
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
            case "update_task":  
                $this->handle_update_task();
                break;
                
            case "load_tasks":
                $this->handle_load_tasks();
                break;
                
            case "load_task":
                $this->handle_load_task();
                break; 
                
            case "delete_task":
                $this->handle_delete_task();
                break;                                
                
            default:
                $this->data["message"] = "Unhandled method $action";
                send($this->data);
                break;    
        }  
    }
    
    private function handle_delete_task()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('task_id', 'Task ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $task_id = $this->input->post("task_id");
        
        // Load the task details.
        $task = $this->Tasks_model->get_details($task_id);
        
        // Make sure the user has permission to delete this task
        if($task->created_by != $this->user_id AND $task->assign_to != $this->user_id)
        {
            $this->data["message"] = "Sorry, you do not have permission to delete this task.";
            send($this->data);            
        }
        
        // Delete the task
        $this->Tasks_model->delete($task_id, $this->user_id);

        // Delete worked correctly.   Send the OK back.
        $this->data["status"] = "OK";
        $this->data["message"] = "";
        
        send($this->data);            
    }
    
    /***
    * Handles the load_task action
    * Loads a single task and sends the data back as json
    */
    private function handle_load_task()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        
        // Validate the form submission
        $this->form_validation->set_rules('task_id', 'Task ID', 'required|number'); 
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $task_id = $this->input->post("task_id");
        
        // Load the task details.
        $task = $this->Tasks_model->get_details($task_id);
        
        // Sends the task attributes back to the client
        $task_data = get_object_vars($task);
        $task_data["due_date"] = $this->utilities->iso_to_ukdate($task_data["due_date"]);
        $this->data["status"] = "OK";
        $this->data["message"] = $task_data;
        
        send($this->data);
    }
    
    /***
    * Handles the load_tasks action
    * Loads a list of tasks in accordance with search params
    */
    private function handle_load_tasks()
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
        						"t.title",
        						'u2.first_name',
        						't.due_date',
        						't.priority',
        						't.status'
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
        $filters["assign_to"] = $this->user_id;
        $filters["search_term"] = $this->input->post("search_term");
        
        if($this->input->post("status") != "")
        {
            $filters["status"] = $this->input->post("status");
        }
        
        $order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");
        
        $assign_client_select_sql = ", CASE " .
            "WHEN (length(u2.company_name) > 0) THEN CONCAT(u2.first_name, ' ', u2.last_name, ' (', u2.company_name, ')') " .
            "ELSE CONCAT(u2.first_name, ' ', u2.last_name) " .
            "END as assign_client_name";
        
        $tasks = $this->Tasks_model->get_list($filters, $order_by, TASKS_PER_PAGE, $current_page, $count_all, $assign_client_select_sql);        
        
        $this->data["status"] = "OK";
        $this->data["message"] = $this->load->view("member/tasks/list/list", array("tasks" => $tasks), true);
        $this->data["count_all"] = $count_all;
        
        send($this->data);
    }
                   
    
    /***
    * Handles the update_task action
    * Send an OK status back if the task was updated/added successfully, error if not.
    */
    private function handle_update_task()
    {
        // Load neccessary libs and models
        $this->load->library('form_validation');
        $this->load->model("email_model");
        
        // Validate the form submission
        $this->form_validation->set_rules('title', 'Task Title', 'required');
        $this->form_validation->set_rules('due_date', 'Due Date', 'callback_date_check');
        $this->form_validation->set_rules('assign_to', 'Email Address', 'integer');
        $this->form_validation->set_rules('priority', 'Priority', 'required');
        $this->form_validation->set_rules('completed', 'Task Completed', 'integer');
        $this->form_validation->set_rules('task_id', 'Task ID', 'integer');
        
        if ($this->form_validation->run() == FALSE)
        {
            $this->data["message"] = validation_errors('- ', '\n');
            send($this->data);
        }
        
        $task_id = $this->input->post("task_id");
		
        $add_mode = false;
        $task = false;
        
        if(is_numeric($task_id))
        {
            // We're updating an existing task.  Load the task details.
            $task = $this->Tasks_model->get_details($task_id);
            
            // If the user could not be loaded, OR if the user was created by someone else
            // Forbid this action.
            if((!$task) || ($task->created_by != $this->user_id AND $task->assign_to != $this->user_id))
            {
                $this->data["message"] = "Sorry, you do not have permission to perform this action.";
                send($this->data);     
            }            
        }
        else
        {
            // We are inserting a new task.
            $add_mode = true;
        }
        
        
        $save = array();
        $save["last_modified_by"] = $this->user_id;
        $save["last_modified"] = date("Y-m-d H:i:s");
        $save["title"] = $this->input->post("title");    
        $save["priority"] = $this->input->post("priority");
        $save["description"] = $this->input->post("description");
        
        if (in_array($this->user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER, USER_TYPE_INVESTOR, USER_TYPE_LEAD)))
        {
        	$save["assign_to"] = ifEmptyNull($this->input->post("assign_to"));
        }
        else 
        {
        	if ($add_mode)
        		$save["assign_to"] = $this->user_id;
        }
        
        $save["status"] = ($this->input->post("status") == 1) ? 1 : 0;
        
        //die("STATUS: " . $this->input->post("status"));
        
        if($this->input->post("due_date") != "")
        {
            $save["due_date"] = $this->utilities->uk_to_isodate($this->input->post("due_date"));        
        }
        
        if($task)
        {
            if(($task->status == 0) && ($this->input->post("status") == 1))
            {
                $save["completed_date"] = date("Y-m-d H:i:s");    
            }
        }
        
        if($add_mode)
        {
            // When adding a new record, set the username to the email
            $save["created_by"] = $this->user_id;
            $save["created_date"] = date("Y-m-d H:i:s");

        }
        
        $task_id = $this->Tasks_model->save($task_id, $save);
        if(!$task_id)
        {
            $this->data["message"] = "Sorry, someone went wrong whilst trying to save this task.";
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