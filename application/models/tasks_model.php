<?php
class Tasks_model extends CI_Model 
{
    function Tasks_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    /***
    * @method get_list
    * @author Andrew Chapman
    * @abstract This method gets a list of all tasks from the database.  
    * 
    * @returns A list of tasks
    */
    public function get_list($filters = array(), $order_by = "t.due_date ASC", $limit = "", $page_no = "", &$count_all, $select_sql="")
    {
        // Find out total record count
        $this->db->select("COUNT(t.task_id) as num_items");
        $this->apply_filters($filters);
        $row = $this->db->get()->row();
        $count_all = $row->num_items;        

        // Now load the actual recordset
        $this->db->select("t.*, u2.first_name, u2.last_name, u2.user_type_id $select_sql", false);
        $this->apply_filters($filters);        
        $this->db->order_by($order_by);
        
        // Apply the limit if necessary
        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }        
        
        $query = $this->db->get();       

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }  
    
    private function apply_filters($filters)
    {
        $this->db->from("tasks t");
        $this->db->join("users u2", "t.assign_to = u2.user_id", "left");        
        
        // Search term
        if((array_key_exists("search_term", $filters)) && ($filters["search_term"] != ""))
        {
            $search_term = $filters["search_term"];
            
            $this->db->where("((t.title LIKE '%" . $search_term . "%') OR " .
                "(u2.first_name LIKE '%" . $search_term . "%') OR " .
                "(u2.last_name LIKE '%" . $search_term . "%'))");
        }
        
        if(isset($filters['created_by']) && intval($filters['created_by']))
        {
        	$created_by = $filters['created_by'];
        	if(isset($filters['assign_to']) && intval($filters['assign_to']))
        	{
        		$assign_to = $filters['assign_to'];
        		$this->db->where("(assign_to = '$assign_to' OR created_by = '$created_by')");
        	}
        	else
        	{
        		where($filters, "created_by", "t");
        	}
        }
        
        //where($filters, "created_by", "t");
        where($filters, "status", "t");
             
    }      

   
    /**
    * @desc The get_details method loads all properties of a particular task
    */
    public function get_details($task_id, $created_by = "")
    {
        $where = array();
        $where["task_id"] = $task_id;
        
        if(is_numeric($created_by))
        {
            $where["created_by"] = $created_by;        
        }
        
        $query = $this->db->get_where('tasks', $where);

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
        {
            return false;
        }
    }

    public function save($task_id, $data)
    {
        if(is_numeric($task_id))
        {
            $this->db->where('task_id', $task_id);
            
            if(!$this->db->update('tasks', $data))
            {
                return false;    
            }
            
            return $task_id;    
        }
        else
        {
            if(!$this->db->insert('tasks', $data))
            {
                return false;    
            }
            
            return $this->db->insert_id(); 
        }
    }
    
    /***
    * Deletes the specified tasks
    * 
    * @param mixed $where_in
    */
    public function delete($where_in, $created_by = "")
    {
        if(is_numeric($created_by))
        {
            $this->db->where("created_by", $created_by);    
        }
        
        $this->db->where(" task_id in (".$where_in.")",null,false);
        $this->db->delete('tasks');
    }

    /***
    * The get_priorities method returns an associative array of the priority options
    */
    function get_priorities()
    {
        $priorities = array(
            "high"         => "High",
            "medium"         => "Medium",
            "low"         => "Low"
        );
            
        return $priorities;        
    } 
    
    /***
    * The get_statuses method returns an associative array of the status options
    */
    function get_statuses()
    {
        $statuses = array(
            "0"         => "Active",
            "1"         => "Completed"
        );
            
        return $statuses;        
    }             
}