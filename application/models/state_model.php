<?php
class State_model extends CI_Model 
{
	function State_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $order_by = "nc_region_states.state_name ASC", $builder_id = "", $filters = array())
    {
        
        //count all result
        $this->db->select('*');
        $this->db->from('nc_region_states');
         if($enabled > -1)
           $this->db->where('nc_region_states.enabled', $enabled);            
         
        if($search_term !="") {
            $this->db->like('nc_region_states.state_name',$search_term);
        }
		
		//$this->apply_filters($filters);

        if ($builder_id != "") {
            $this->db->join("nc_properties p", "p.state_id = nc_regions_states.state_id");
            $this->db->where("p.builder_id", $builder_id);
            $this->db->group_by("nc_regions_states.state_id");
        }
        
        $count_all = $this->db->count_all_results();
        
        //with limit
        $this->db->from('nc_region_states');
        
        if($enabled > -1)
           $this->db->where('nc_region_states.enabled', $enabled);            
         
        if($search_term !="") 
        {
            $this->db->like('nc_region_states.state_name',$search_term);
		}

        if ($builder_id != "") {
            $this->db->join("nc_properties p", "p.state_id = nc_regions_states.state_id");
            $this->db->where("p.builder_id", $builder_id);
            $this->db->group_by("nc_regions_states.state_id");
        }
           
        $this->db->order_by($order_by);     
                
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
	
	
	private function apply_filters($filters)
    {
             
        
        filter_where($filters, "state_id", "nc_regions_states.");
		
		//if ($filters['project_id'] != "") {
            $this->db->join("nc_projects ncp", "ncp.area_id = nc_areas.area_id");
            //$this->db->where("p.builder_id", $builder_id);
			filter_where($filters, "project_id", "");
            
        //}
        
    }
	
	
	public function get_details($state_id, $by_name = false)
	{
		// Check to see if a record with this username exists.
		if(!$by_name)
			$query = $this->db->get_where('nc_region_states', array('state_id' => $state_id));
		else
			$query = $this->db->get_where('nc_region_states', array('state_name' => $state_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
    //get detail by state_id in nc_states
    public function get_details2($state_id)
	{
		// Check to see if a record with this username exists.
		$query = $this->db->get_where('nc_region_states', array('region_id' => $state_id));
		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
	function save($builder_id,$data)
	{
		if (is_numeric($builder_id))
		{
			$this->db->where('state_id',$builder_id);
			$this->db->update('nc_region_states',$data);
            return $builder_id;
		}
		else
		{
			$this->db->insert('nc_region_states',$data);
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where("state_id in (".$where_in.")",null,false);
		$this->db->delete('nc_region_states');
	}
}