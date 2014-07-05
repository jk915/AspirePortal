<?php
class Lead_comments_model extends CI_Model 
{
    function Lead_comments_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "id DESC", $items_per_page = 0, $offset = 0)                        
    {
    	$this->db->select("lead_comments.*");
    	
    	if(isset($filters["lead_id"]) && intval($filters["lead_id"]))
    	{
			$this->db->where("lead_id", $filters["lead_id"]);
    	}
    	
        if($items_per_page > 0) {
            $this->db->limit($items_per_page);
            $this->db->offset($offset);
        }
        
        $this->db->order_by($order_by);
        
        $query = $this->db->get("lead_comments");            
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
        
    }
    
    public function get_details($lead_id)
    {
    	$query = $this->db->get_where('lead_comments', array('id' => $lead_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($lead_id, $data)
	{
		if(is_numeric($lead_id)) {
			$this->db->where('id',$lead_id);
			
			if($this->db->update('lead_comments',$data))
				return $lead_id;	
			else
				return false;
		} else {
			$this->db->insert('lead_comments',$data);    
			return $this->db->insert_id(); 
		}
	}
   
}
?>