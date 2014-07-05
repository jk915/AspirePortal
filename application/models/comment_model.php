<?php

class Comment_model extends CI_Model 
{

    function Comment_model()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    public function get_details($comment_id=0)
    {
        $row = $this->db->select("comments.*")
                ->select('UNIX_TIMESTAMP(datetime_added) AS ts_added')
                ->select('users.first_name')
                ->select('users.last_name')
                ->from("comments")
                ->join('users', 'users.user_id = comments.user_id', 'inner')
                ->where('id', $comment_id)
                ->get()
                ->row();
        return $row;
    }
    
	public function get_list($filters, $order_by = "datetime_added DESC",$limit = "")                        
    {
        $this->db->select("comments.*")
            ->select('UNIX_TIMESTAMP(datetime_added) AS ts_added')
            ->select('users.first_name')
            ->select('users.last_name')
            ->from("comments")
            ->join('users', 'users.user_id = comments.user_id', 'inner');
        
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
            ->delete('comments');
        return true;
	}
	
	public function save($comment_id,$data)
    {           
        if (is_numeric($comment_id)) {
            
            $this->db->where('id',$comment_id);
            $this->db->update('nc_comments',$data);
            return $comment_id;
            
        } else {
            
            $this->db->insert('nc_comments',$data);
            return $this->db->insert_id();
            
        }

    }
   
}