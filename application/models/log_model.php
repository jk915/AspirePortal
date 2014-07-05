<?php
class Log_model extends CI_Model 
{
	function Log_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	function save($log_id,$data)
	{
		if (is_numeric($log_id))
		{
			$this->db->where('log_id',$log_id);
			$this->db->update('nc_user_log',$data);
            return $log_id;
		}
		else
		{
			$this->db->insert('nc_user_log',$data);
			return $this->db->insert_id();
		}
	}
	
	function get_details($log_id)
	{
		$this->db->select('*');
		$this->db->from('nc_user_log');
		$this->db->where('log_id',$log_id);
	}

}	