<?php
class Property_lawyer_model extends CI_Model 
{
	function Property_lawyer_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
    /*
	public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $order_by = "l.lawyer_name ASC")
    {
        
        //count all result
        $this->db->select('*');
        $this->db->from('nc_lawyers as l');
         if($enabled > -1)
           $this->db->where('l.enabled', $enabled);            
         
        if($search_term !="") {
            $this->db->like('l.lawyer_name',$search_term);
            $this->db->or_like('l.lawyer_content', $search_term);
        }
        
        $count_all = $this->db->count_all_results();
        
        //with limit
        $this->db->from('nc_lawyers as l');
        
        if($enabled > -1)
           $this->db->where('l.enabled', $enabled);            
         
        if($search_term !="") 
        {
            $this->db->like('l.lawyer_name',$search_term);
            $this->db->or_like('l.lawyer_content', $search_term);
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
			$query = $this->db->get_where('nc_lawyers', array('lawyer_id' => $builder_id));
		else
			$query = $this->db->get_where('nc_lawyers', array('lawyer_name' => $builder_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    */
    
	function save($property_id, $lawyer_id)
	{
        $this->db->from('property_lawyers')->where('property_id',$property_id);
        
        if ($this->db->count_all_results() == 0) {
          // the check can be chained for less typing
          // A record does not exist, insert one.
          $this->db->insert('property_lawyers',array('property_id' => $property_id, 'lawyer_id'=> $lawyer_id));
          // echo $this->db->last_query();die;
        } else {
          // A record does exist, update it.
          $this->db->where("property_id", $property_id); 
          $this->db->update('property_lawyers', array('lawyer_id'=>$lawyer_id));
        }
	}
    
	// public function delete($where_in)
	// {
		// $this->db->where(" lawyer_id in (".$where_in.")",null,false);
		// $this->db->delete('nc_lawyers');
	// }
	
	public function get_property_lawyer($lawyer_id)
	{		
        $this->db->select("l.*, p.*, u.first_name, u.last_name");
        $this->db->from("lawyers l");
        $this->db->join("property_lawyers pl", "pl.lawyer_id = l.lawyer_id", "inner");
        $this->db->join("properties p", "pl.property_id = p.property_id", "inner");
        $this->db->join("users u", "p.user_id = u.user_id", "inner");
        $this->db->where("l.lawyer_id", $lawyer_id);

		$query = $this->db->get();
        
		if ($query->num_rows() <= 0) {
			return false;
		}         	
            
        return $query;
	}
    
    /***
    * Gets a lawyer record from the database that is
    * associated with the specified property
    * 
    * @param int $property_id The id of the property
    */
    public function get_lawyer_for_property($property_id)
    {
        if((empty($property_id)) || (empty($property_id))) {
            return false;    
        }
        
        $rst = $this->db->get_where("property_lawyers", array("property_id" => $property_id));
        if($rst->num_rows() == 0) {
            return false;    
        }
        
        $row = $rst->row();
        
        $rst = $this->db->get_where("lawyers", array("lawyer_id" => $row->lawyer_id));
        if($rst->num_rows() == 0) {
            return false;    
        }
        
        return $rst->row();        
    }
	
}