<?php

class Contact_model extends CI_Model 
{

    function Contact_model()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
	public function get_list($filters, $order_by = "name ASC",$limit = "")                        
    {
        if ( isset($filters['foreign_id']) && intval($filters['foreign_id'])) {
        	$this->db->where('foreign_id', intval($filters['foreign_id']));
        }
        
        if ( isset($filters['type']) && !empty($filters['type'])) {
        	$this->db->where('type', $filters['type']);
        }
        
        $this->db->order_by($order_by);

		$result = $this->db->get("nc_contacts");
	
		if($result->num_rows() > 0) {
			return $result;
		} else {
			return false;
		}
    }
    
    function save($contact_id,$data)
	{
		if (is_numeric($contact_id))
		{
			$this->db->where('contact_id',$contact_id);
			$this->db->update('nc_contacts',$data);
            return $contact_id;
		}
		else
		{
			$this->db->insert('nc_contacts',$data);
			return $this->db->insert_id();
		}
	}
	
	public function get_details($contact_id)
    {
        $query = $this->db->get_where('nc_contacts',array('contact_id' => $contact_id),1);
        // If there is a resulting row
        if ($query->num_rows() > 0) {
            return $query->row();
        } else {
            return false;
        }
    }
    
	public function delete($where_in)
	{
        $this->db->where_in('contact_id',$where_in)
            ->delete('contacts');
        return true;
	}
   
}