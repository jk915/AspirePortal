<?php
class Notifications_model extends CI_Model 
{
    function Notifications_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
	
	public function save($notification_id,$data)
    {
        if (is_numeric($notification_id))
        {   
            $this->db->where('notification_id',$notification_id);
            $this->db->update('nc_mail_notifications',$data);
            return $notification_id;
        }
        
    }

	public function get_details($notification_id)
	{
		$this->db->select('*');
		$this->db->from('nc_mail_notifications');
		$this->db->where('notification_id', $notification_id);
		
		$query = $this->db->get(); 

		if ($query->num_rows() > 0)
		{
			return $query->result();
		}         
		else
			return false;
	}

	public function get_mail_details($notification_type)
	{
		$this->db->select('*');
		$this->db->from('nc_mail_notifications');
		$this->db->where('notification_type', $notification_type);
		
		$query = $this->db->get(); 

		if ($query->num_rows() > 0)
		{
			return $query->result();
		}         
		else
			return false;
	}

	
}	