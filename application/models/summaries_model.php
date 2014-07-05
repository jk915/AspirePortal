<?php
class Summaries_model extends CI_Model 
{
    function Summaries_model()
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
    public function get_list($filters = array(), $order_by = "s.created_date DESC", $limit = "", $page_no = "", &$count_all, $select_sql="")
    {
        // Find out total record count
        $this->db->select("COUNT(s.summary_id) as num_items");
        $this->apply_filters($filters);
        $row = $this->db->get()->row();
        $count_all = $row->num_items;        

        // Now load the actual recordset
        $this->db->select("s.*, st.name state_name, a.area_name area_name, p.project_name project_name $select_sql", false);
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
        $this->db->from("summaries s");
        $this->db->join("states st", "st.state_id = s.state_id","left");
        $this->db->join("areas a", "a.area_id = s.area_id","left");
        $this->db->join("projects p", "p.project_id = s.project_id","left");
        
        // Search term
        if((array_key_exists("search_term", $filters)) && ($filters["search_term"] != ""))
        {
            $search_term = $filters["search_term"];
                            
            $this->db->where("((s.title LIKE '%" . $search_term . "%') OR " .
                "(st.name LIKE '%" . $search_term . "%') OR " .
                "(a.area_name LIKE '%" . $search_term . "%') OR " .
                "(p.project_name LIKE '%" . $search_term . "%'))");
        }
        
        if(isset($filters['created_by']) && intval($filters['created_by']))
        {
        	if(isset($filters['created_by']) && intval($filters['created_by']))
        	{
        		$this->db->where("created_by", $filters['created_by']);
        	}
        }
        
        // where($filters, "status", "t");
             
    }      

   
    /**
    * @desc The get_details method loads all properties of a particular task
    */
    public function get_details($summary_id)
    {
        $where = array();
        $where["summary_id"] = $summary_id;
        
        
        $query = $this->db->get_where('summaries', $where);

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

    public function save($summary_id, $data)
    {
        if(is_numeric($summary_id))
        {
            $this->db->where('summary_id', $summary_id);
            
            if(!$this->db->update('summaries', $data))
            {
                return false;    
            }
            
            return $summary_id;    
        }
        else
        {
            if(!$this->db->insert('summaries', $data))
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
        
        $this->db->where(" summary_id in (".$where_in.")",null,false);
        $this->db->delete('summaries');
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