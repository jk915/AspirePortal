<?php
class Builder_model extends CI_Model 
{
	function Builder_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $order_by = "b.builder_name ASC")
    {
        
        //count all result
        $this->db->select('*');
        $this->db->from('nc_builders as b');
         if($enabled > -1)
           $this->db->where('b.enabled', $enabled);            
         
        if($search_term !="") {
            $this->db->like('b.builder_name',$search_term);
            $this->db->or_like('b.builder_content', $search_term);
        }
        
        $count_all = $this->db->count_all_results();
        
        //with limit
        $this->db->from('nc_builders as b');
        
        if($enabled > -1)
           $this->db->where('b.enabled', $enabled);            
         
        if($search_term !="") 
        {
            $this->db->like('b.builder_name',$search_term);
            $this->db->or_like('b.builder_content', $search_term);
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
    
	public function get_details($builder_id, $by_name = false)
	{
		// Check to see if a record with this username exists.
		if(!$by_name)
			$query = $this->db->get_where('nc_builders', array('builder_id' => $builder_id));
		else
			$query = $this->db->get_where('nc_builders', array('builder_name' => $builder_id));

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
			$this->db->where('builder_id',$builder_id);
			$this->db->update('nc_builders',$data);
            return $builder_id;
		}
		else
		{
			$this->db->insert('nc_builders',$data);
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" builder_id in (".$where_in.")",null,false);
		$this->db->delete('nc_builders');
	}
}