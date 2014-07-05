<?php
class Property_stages_model extends CI_Model 
{
	function Property_stages_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($completed = -1, $property_id = -1, $limit = "", $page_no = "", &$count_all, $public = -1)
    {
        //count all result
        $this->db->select('s.*, r.stage_name, UNIX_TIMESTAMP(s.datetime_completed) AS ts_date');
        $this->db->from('property_stages as s');
        $this->db->join('stages as r', 'r.stage_no = s.stage_no AND s.tracker_type = r.tracker_type', 'inner');
        
        $this->apply_filters($completed, $property_id, $public);
         
        $count_all = $this->db->count_all_results();
        
        //with limit
        $this->db->select('s.*, r.stage_name, UNIX_TIMESTAMP(s.datetime_completed) AS ts_date');
        $this->db->from('property_stages as s');
        $this->db->join('stages as r', 'r.stage_no = s.stage_no AND s.tracker_type = r.tracker_type', 'inner');
        
        $this->apply_filters($completed, $property_id, $public);
         
        $this->db->order_by("s.stage_no", "ASC");     
                
        if ($limit != "" && $page_no != "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }
                                                                                     
        $query = $this->db->get();        

        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    private function apply_filters($completed = -1, $property_id = -1, $public = -1)
    {
        if($completed != -1)
        {
            $this->db->where('s.status', $completed);            
        }
        
        if ($property_id > -1) 
        {
            $this->db->where('s.property_id', $property_id);
        }     
        if ($public != -1) 
        {
            $this->db->where('s.public', $public);
        }                     
    }
    
	public function get_details($id)
	{
	    $query = $this->db->select('s.*, r.stage_name, UNIX_TIMESTAMP(s.datetime_completed) AS ts_date')
                        ->from('property_stages s')
                        ->join('stages as r', 'r.stage_no = s.stage_no', 'inner')
                        ->where('s.id', $id);
                        
        $query = $this->db->get();
        
		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
	function save($id,$data)
	{
		if (is_numeric($id))
		{
			$this->db->where('id',$id);
            
            if($this->db->update('property_stages',$data))
            {
                return false;    
            }
            
            return $id;
		}
		else
		{
			if(!$this->db->insert('property_stages',$data))
            {
                return false;    
            }
            
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where("id in (".$where_in.")",null,false);
		$this->db->delete('property_stages');
	}
	
	public function add_default_property_stages($property_id)
    {           
        // Load the property in question
        $property = $this->db->get_where("properties", array("property_id" => $property_id))->row();

        if(!$property)
        {
            show_error("Property_stages_model::add_default_property_stages - Invalid property");
        } 
        
        $tracker_type = $property->tracker_type;
        
        $sql= "INSERT INTO `nc_property_stages` (`stage_no`,`tracker_type`, `property_id`, `public`) " .
            "SELECT `stage_no`, '" . $tracker_type . "', %d ,`public` FROM `nc_stages` " .
            "WHERE tracker_type = '" . $tracker_type . "' " .
            "AND stage_no NOT IN (SELECT stage_no FROM nc_property_stages WHERE property_id = %d AND tracker_type = '" . $tracker_type . "');";
            
        $sql = sprintf($sql, $property_id, $property_id); 

        $this->db->query($sql);                     
    }
    
    public function delete_by_property($property_id)
	{
	    $this->db->where("property_id", $property_id);
		$this->db->delete('property_stages');
	}
}