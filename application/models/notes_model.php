<?php
class Notes_model extends CI_Model 
{
    function Notes_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "note_date DESC", $items_per_page = 0, $offset = 0, $count_all=false)
    {
        $this->db->select("notes.*")
            ->from("notes");
		
        if ( isset($filters['note_id']) && intval($filters['note_id']))
        	$this->db->where('note_id', $filters['note_id']);
        	
    	if ( isset($filters['created_by']) && intval($filters['created_by']))
        {
            $where = "(created_by = %d OR private = 0)"; 
            $where = sprintf($where, $filters['created_by'], $filters['created_by']); 
        	$this->db->where($where);
        }
        	
        if ( isset($filters['note_type']) && !empty($filters['note_type']))
        	$this->db->where('note_type', trim($filters['note_type']));
        	
    	if ( isset($filters['foreign_id']) && intval($filters['foreign_id']) )
    		$this->db->where('foreign_id', $filters['foreign_id']);
		
		if($items_per_page > 0)
		{
            $this->db->limit($items_per_page);
            $this->db->offset($offset);
        }
        
		$this->db->order_by($order_by);
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0)
			if ($count_all)
				return $result->num_rows();
			else
				return $result;
		else
			return false;
    }
  
    public function get_details($note_id)
    {
    	$query = $this->db->get_where('notes', array('note_id' => $note_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($note_id, $data)
	{
		if(is_numeric($note_id)) {
			$this->db->where('note_id',$note_id);
			
			if($this->db->update('nc_notes',$data))
				return $note_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_notes',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($where_in)
	{
        $this->db->where_in('note_id',$where_in)
            ->delete('notes');
        return true;
	}
   
}