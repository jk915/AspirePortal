<?php

class Keydate_model extends CI_Model 
{

    function Comment_model()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    public function get_details($keydate_id=0)
    {
        $row = $this->db->select("keydates.*")
                ->select('UNIX_TIMESTAMP(datetime_added) AS ts_added')
                ->select('users.first_name')
                ->select('users.last_name')
                ->from("keydates")
                ->join('users', 'users.user_id = keydates.user_id', 'inner')
                ->where('id', $keydate_id)
                ->get()
                ->row();
        return $row;
    }
    
	public function get_list($filters, $order_by = "datetime_added",$limit = "")                        
    {
        $this->db->select("keydates.*")
            ->select('UNIX_TIMESTAMP(datetime_added) AS ts_added')
            ->select('users.first_name')
            ->select('users.last_name')
            ->from("keydates")
            ->join('users', 'users.user_id = keydates.user_id', 'inner');
        
        if ( isset($filters['foreign_id']) && intval($filters['foreign_id'])) {
			
			$this->db->where('foreign_id', intval($filters['foreign_id']));
        }
        
        if ( isset($filters['type']) && !empty($filters['type'])) {
        	$this->db->where('type', $filters['type']);
        }
        
        $this->db->order_by($order_by);
	
		$result = $this->db->get();
		
		if($result->num_rows() > 0) {
			return $result;
		} else {
			return false;
		}
    }
    
	public function delete($where_in)
	{
        $this->db->where_in('id',$where_in)
            ->delete('keydates');
        return true;
	}
	
	public function save($keydate_id,$data)
    {           
        if (is_numeric($keydate_id)) {
            
            $this->db->where('id',$keydate_id);
            $this->db->update('nc_keydates',$data);
            return $keydate_id;
            
        } else {
            
            $this->db->insert('nc_keydates',$data);
            return $this->db->insert_id();
            
        }

    }
	
	public function get_all_keydates()
	{
		$this->db->select('*');
		$this->db->from('nc_keydates');
		$this->db->order_by('followup_date','desc');
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0) 
		{
			return $result;
		} 
		else 
		{
			return false;
		}
		
	}
	
	public function get_keydate($keydate_id)
	{
		$this->db->select('*');
		$this->db->from('nc_keydates');
		$this->db->where('id',$keydate_id);
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0) 
		{
			return $result;
		} 
		else 
		{
			return false;
		}
	}
	
   
}