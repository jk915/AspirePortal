<?php
class Australia_model extends CI_Model 
{
	function Australia_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_details()
	{
		
			$query = $this->db->get_where('nc_australia');

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
	
	function save($australia_id,$data)
	{
		if (is_numeric($australia_id))
		{
			$this->db->where('australia_id',$australia_id);
			$this->db->update('nc_australia',$data);
            return $australia_id;
		}
	}	
}
?>	