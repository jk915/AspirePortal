<?php
class Extraitems_model extends CI_Model 
{
    function Extraitems()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "id asc")                        
    {
    	$this->db->select("extraitems.*");
    	if (isset($filters["quote_id"])) {
    	   $this->db->where('quote_id',$filters["quote_id"]);	
    	}
    	// Apply filters       
        $this->db->order_by($order_by);
        
        $query = $this->db->get("extraitems");            
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
        
    }
  
    public function get_details($item_id)
    {
    	$query = $this->db->get_where('extraitems', array('id' => $item_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($item_id, $data)
	{
		if(is_numeric($item_id)) {
			$this->db->where('id',$item_id);
			
			if($this->db->update('nc_extraitems',$data))
				return $item_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_extraitems',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" id in (".$where_in.")",null,false);
		$this->db->delete('nc_extraitems');
	}
   
}
?>