<?php
class Panel_contacts_model extends CI_Model 
{
	function Panel_contacts_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $order_by = "c.contacts_name ASC")
    {
     
        //count all result
        // $this->db->select('c.*');
        // $this->db->from('nc_panel_contacts as c');
            
        // if($enabled > -1) {
           // $this->db->where('c.enabled', $enabled);   
        // }
      
        // $this->db->where('c.is_panel', true);            
         
        // if($search_term !="") {
            // $this->db->like('c.contacts_name',$search_term);
            // $this->db->or_like('c.contacts_content', $search_term);
        // }
     
        // $count_all = $this->db->count_all_results();
          
        //with limit
       $this->db->select('c.*, (SELECT COUNT(*) FROM `nc_panel_contacts` WHERE contacts_id = c.contacts_id) as num_transactions');
        $this->db->from('nc_panel_contacts as c');
        
        if($enabled > -1) {
           $this->db->where('c.enabled', $enabled);            
        }
           
       // $this->db->where('c.is_panel', true);            
         
        if($search_term !="") 
        {
            $this->db->like('c.contacts_name',$search_term);
            $this->db->or_like('c.contacts_content', $search_term);
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
			return $query->result();
		}         
		else
			return false;
	}
    
	function save($contacts_id,$data)
	{
		if (is_numeric($contacts_id))
		{
			$this->db->where('contacts_id',$contacts_id);
			$this->db->update('nc_panel_contacts',$data);
            return $contacts_id;
		}
		else
		{
			$this->db->insert('nc_panel_contacts',$data);
            return $this->db->insert_id();
        }
		
	}
    
	public function delete($where_in)
	{
		$this->db->where(" contacts_id in (".$where_in.")",null,false);
		$this->db->delete('nc_panel_contacts');
	}
		//By Ajay TasksEveryday
	public function get_contacts($property_id)
	{
		$this->db->select('contacts_id');
        $this->db->from('nc_property_contacts');
        $this->db->where('property_id', $property_id);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
	
	public function update_contacts_transaction_count($contacts_id, $count)
	{
		// $this->db->where('lawyer_id', $builder_id);
		// $this->db->update('count_number_transactions',$count);
		
		$query = "UPDATE `nc_panel_contacts` SET `count_number_transactions`= $count WHERE `contacts_id` = " . $this->db->escape($contacts_id);
		
		$query = $this->db->query($query);
	}
	//END
}