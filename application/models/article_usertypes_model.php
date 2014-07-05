<?php
class Article_usertypes_model extends CI_Model 
{
	function Article_usertypes_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($filters, $order_by = "id ASC",$limit = "")                        
    {
        $this->db->select("article_usertypes.*")
                ->from("article_usertypes");
                
        where($filters, "article_id");
        where($filters, "user_type_id");
            
        $this->db->order_by($order_by);
	
		$result = $this->db->get();
		
		if($result->num_rows() > 0) 
        {
			return $result;
		}

		return false;
    }
    
	public function get_details($id)
	{
		$query = $this->db->get_where('article_usertypes', array('id' => $id));

		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
	function save($id, $data)
	{
		if (is_numeric($id)) 
        {
			$this->db->where('id',$id);
            
			if(!$this->db->update('article_usertypes',$data))
            {
                return false;    
            }
            
            return $id;
		}
		else
		{
			if($this->db->insert('article_usertypes',$data))
            {
                return false;    
            }
            
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where("id in (".$where_in.")",null,false);
		$this->db->delete('article_usertypes');
	}
	
	public function delete_by_article($article_id)
	{
	    $this->db->where("article_id", $article_id);
		$this->db->delete('article_usertypes');
	}
}