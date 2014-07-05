<?php
class Property_contact_model extends CI_Model 
{
	function Property_contact_model()
	{
		// Call the Model constructor
		parent::__construct();
	}

	public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $order_by = "l.contatcs_name ASC")
    {
        
        //count all result
        $this->db->select('*');
        $this->db->from('nc_panel_contacts as l');
         if($enabled > -1)
           $this->db->where('l.enabled', $enabled);            
         
        if($search_term !="") {
            $this->db->like('l.contacts_name',$search_term);
            $this->db->or_like('l.conatacts_content', $search_term);
        }
        
        $count_all = $this->db->count_all_results();
        
        //with limit
        $this->db->from('nc_panel_contacts as l');
        
        if($enabled > -1)
           $this->db->where('l.enabled', $enabled);            
         
        if($search_term !="") 
        {
            $this->db->like('l.contacts_name',$search_term);
            $this->db->or_like('l.contacts_content', $search_term);
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
    
	public function get_details($contacts_id, $by_name = false)
	{
		// Check to see if a record with this username exists.
		if(!$by_name)
			$query = $this->db->get_where('nc_panel_contacts', array('contacts_id' => $contacts_id));
		else
			$query = $this->db->get_where('nc_panel_contacts', array('contacts_name' => $contacts_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
        
	function save($property_id, $contacts_id)
	{
        $this->db->from('property_panel_contacts')->where('property_id',$property_id);
        
        if ($this->db->count_all_results() == 0) {
          // the check can be chained for less typing
          // A record does not exist, insert one.
          $this->db->insert('property_panel_contacts',array('property_id' => $property_id, 'contacts_id'=> $contacts_id));
          // echo $this->db->last_query();die;
        } else {
          // A record does exist, update it.
          $this->db->where("property_id", $property_id); 
          $this->db->update('property_panel_contacts', array('contacts_id'=>$contacts_id));
        }
	}
    
	public function delete($where_in)
	{
		$this->db->where(" contacts_id in (".$where_in.")",null,false);
		$this->db->delete('nc_panel_contacts');
	}
	
	public function get_property_panel_contatc($contacts_id)
	{		
        $this->db->select("l.*, p.*");
        $this->db->from("panel_contacts l");
        $this->db->join("property_panel_contacts pl", "pl.contacts_id = l.contacts_id", "inner");
        $this->db->join("properties p", "pl.property_id = p.property_id", "inner");
        //$this->db->join("users u", "p.user_id = u.user_id", "inner");
        $this->db->where("l.contacts_id", $contacts_id);

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
        
        $rst = $this->db->get_where("property_panel_contacts", array("property_id" => $property_id));
        if($rst->num_rows() == 0) {
            return false;    
        }
        
        $row = $rst->row();
        
        $rst = $this->db->get_where("panel_contacts", array("contacts_id" => $row->contacts_id));
        if($rst->num_rows() == 0) {
            return false;    
        }
        
        return $rst->row();        
    }
	
}