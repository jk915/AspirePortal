<?php
class Testimonials_model extends CI_Model 
{
    function Testimonials_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "order ASC",$limit = "")
    {
		$this->db->select("*");
		$this->db->from("testimonials");
		
		if(isset($filters["enabled"])) {
			$this->db->where("enabled", $filters["enabled"]);
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
    	$query = $this->db->get_where('testimonials', array('id' => $test_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($test_id, $data)
	{
		if(is_numeric($test_id)) {
			$this->db->where('id',$test_id);
			
			if($this->db->update('nc_testimonials',$data))
				return $test_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_testimonials',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" id in (".$where_in.")",null,false);
		$this->db->delete('nc_testimonials');
	}
   
}
?>