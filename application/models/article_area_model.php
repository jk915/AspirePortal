<?php
class Article_area_model extends CI_Model 
{
	function Article_area_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($filters, $order_by = "id DESC",$limit = "")                        
    {
        $this->db->select("article_areas.*")
                ->from("article_areas");
            
        if ( isset($filters['article_id']) && intval($filters['article_id'])) {
        	$this->db->where('article_id', intval($filters['article_id']));
        }
        
        $this->db->order_by($order_by);
	
		$result = $this->db->get();
		
		if($result->num_rows() > 0) {
			return $result;
		} else {
			return false;
		}
    }
    
	public function get_details($id, $by_name = false)
	{
		$query = $this->db->get_where('nc_article_areas', array('id' => $id));
		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
	function save($id,$data)
	{
		if (is_numeric($id)) {
			$this->db->where('id',$id);
			$this->db->update('nc_article_areas',$data);
            return $id;
		}
		else
		{
			$this->db->insert('nc_article_areas',$data);
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where("id in (".$where_in.")",null,false);
		$this->db->delete('nc_article_areas');
	}
	
	public function delete_by_article($article_id)
	{
	    $this->db->where("article_id = $article_id",null,false);
		$this->db->delete('nc_article_areas');
	}
}