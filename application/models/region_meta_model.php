<?php
class Region_meta_model extends CI_Model 
{
    function Region_meta_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "id DESC",$limit = "")
    {
        $this->db->select("region_meta.*")
            ->from("region_meta");
		
        if ( isset($filters['region_id']) ) {
        	$this->db->where('region_id', intval($filters['region_id']));
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
    	$query = $this->db->get_where('region_meta', array('id' => $test_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($test_id, $data)
	{
		if(is_numeric($test_id)) {
			$this->db->where('id',$test_id);
			
			if($this->db->update('nc_region_meta',$data))
				return $test_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_region_meta',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($where_in)
	{
        $this->db->where_in('id',$where_in)
            ->delete('region_meta');
        return true;
	}
   
}