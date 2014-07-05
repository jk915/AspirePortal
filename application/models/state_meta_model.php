<?php
class State_meta_model extends CI_Model 
{
    function State_meta_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "id DESC",$limit = "")
    {
        $this->db->select("state_meta.*")
            ->from("state_meta");
		
        if ( isset($filters['state_id']) ) {
        	$this->db->where('state_id', intval($filters['state_id']));
        }
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		$this->db->order_by($order_by);
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0) {
			return $result;
		} else {
			return false;
		}			              
    }
  
    public function get_details($test_id)
    {
    	$query = $this->db->get_where('state_meta', array('id' => $test_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($test_id, $data)
	{
		if(is_numeric($test_id)) {
			$this->db->where('id',$test_id);
			
			if($this->db->update('nc_state_meta',$data))
				return $test_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_state_meta',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($where_in)
	{
        $this->db->where_in('id',$where_in)
            ->delete('state_meta');
        return true;
	}
   
}