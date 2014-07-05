<?php
class Lawyer_model extends CI_Model 
{
	function Lawyer_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $filters = array(), $order_by = "l.company_name ASC")
    {
        
        //count all result
        // $this->db->select('l.*');
        // $this->db->from('nc_panel_contacts as l');
            
        // if($enabled > -1) {
           // $this->db->where('l.enabled', $enabled);   
        // }
           
        // $this->db->where('l.is_panel', true);            
         
        // if($search_term !="") {
            // $this->db->like('l.lawyer_name',$search_term);
            // $this->db->or_like('l.lawyer_content', $search_term);
        // }
        
        // $count_all = $this->db->count_all_results();
        
        //with limit
        $this->db->select('l.*, (SELECT COUNT(*) FROM `nc_panel_contacts` WHERE contacts_id = l.contacts_id) as num_transactions, st.name as state_name');
        $this->db->from('nc_panel_contacts as l');
		$this->db->join('states st', 'st.state_id = l.state_id','left');
        
        if($enabled > -1) {
           $this->db->where('l.enabled', $enabled);            
        }
           
        //$this->db->where('l.is_panel', true);            
         
        if(!empty($filters['search_term'] ) ) 
        {
            $this->db->like('l.company_name',$filters['search_term']);
         
		}
		
		if(!empty($filters['contact_type'] ))
		{
		   $this->db->like('l.contact_type', $filters['contact_type']);
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
	public function get_lawyer($property_id)
	{
		$this->db->select('lawyer_id');
        $this->db->from('nc_property_lawyers');
        $this->db->where('property_id', $property_id);
		
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
	
	public function update_lawyer_transaction_count($lawyer_id, $count)
	{
		// $this->db->where('lawyer_id', $builder_id);
		// $this->db->update('count_number_transactions',$count);
		
		$query = "UPDATE `nc_lawyers` SET `count_number_transactions`= $count WHERE `lawyer_id` = " . $this->db->escape($lawyer_id);
		
		$query = $this->db->query($query);
	}
	
	public function get_contact_types()
	{
		$this->db->select('*');
        $this->db->from('nc_contact_type');
		$this->db->order_by('contact_type_name', 'ASC');
        	
		$query = $this->db->get();
		
		if ($query->num_rows() > 0)
		{
			return $query;
		}         
		else
			return false;
	
	}
	//END
}