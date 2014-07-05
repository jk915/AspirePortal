<?php
class Favourites_model extends CI_Model 
{
    private $table_name = "";
    
    function Favourites_model()
    {
        // Call the Model constructor
        parent::__construct();
        
        $this->table_name = "favourites";
    }
    
    public function get_details($favourite_id)
    {
        $row = $this->db->select("*")
                ->from($this->table_name)
                ->where('favourite_id', $favourite_id)
                ->get()
                ->row();
                
        return $row;
    }
    
	public function get_list($filters, $order_by = "created_dtm DESC", $limit = "")                        
    {
        $this->db->select("*")
            ->from($this->table_name);
        
        filter_where($filters, "foreign_type");    
        filter_where($filters, "foreign_id");
        filter_where($filters, "user_id");
        
        $this->db->order_by($order_by);
	
		$result = $this->db->get();
		
		if($result->num_rows() <= 0)
        {
            return false;
        }
        
        return $result;
    }
    
	public function delete($where_in)
	{
        if(!$this->db->where_in('favourite_id', $where_in)->delete($this->table_name))
        {
            return false;    
        }
            
        return true;
	}
	
	public function save($favourite_id = "", $data)
    {           
        if (is_numeric($favourite_id))
        {
            $this->db->where('favourite_id', $favourite_id);
            if(!$this->db->update($this->table_name, $data))
            {
                return false;    
            }
            
            return $comment_id;
        }
        else
        {    
            if(!$this->db->insert($this->table_name,$data))
            {
                return false;    
            }
            
            return $this->db->insert_id();
        }
    }
    
    function exists($foreign_type='',$foreign_id=0,$user_id=0)
    {
        $this->db->where("foreign_type",$foreign_type);
        $this->db->where("user_id",$user_id);
        $this->db->where("foreign_id",$foreign_id);
        $query = $this->db->get('nc_favourites',1);

        if ($query->num_rows() > 0)
           return $query->row();
        else
           return false;
    }
}