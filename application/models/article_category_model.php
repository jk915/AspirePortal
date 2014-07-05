<?php
class Article_category_model extends CI_Model 
{
	function Article_category_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	/***
	* @method get_list
	* @author Andrew Chapman
	* @abstract This method gets a list of all blocks from the database.  
	* 
	* @param integer $limit - Limits the recordset to a specific number of records
	* @param integer $page_no - Starts the recordset at a specific page no.
	* @param integer $count_all - Counts all records.
	* 
	* @returns A list of blocks
	*/
	public function get_list($parent_id = "", $limit = "", $page_no = "", $enabled = -1, $order_by = "seq_no ASC")
	{
		$count_all = $this->db->count_all_results('nc_custom_blocks');

		$this->db->select('*');
		$this->db->from('article_categories');
		
		// If a parent id has been defined, only load categories with a matching parent id.
		if($parent_id != "")
			$this->db->where("parent_id", $parent_id);
            
        if($enabled != -1)
            $this->db->where("enabled", $enabled);    
		
		$this->db->order_by($order_by);

		if ($limit != "" && $page_no!= "" && $count_all > $limit)
		{
			$this->db->limit(intval($limit), intval(($page_no-1) * $limit));
		}

		$query = $this->db->get();        

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query;
		}         
		else
			return false;
	}
    
	public function get_details($category_id)
	{
		// Check to see if a record with this username exists.
		if( is_numeric($category_id) )
			$query = $this->db->get_where('article_categories', array('category_id' => $category_id));
		else
			$query = $this->db->get_where('article_categories', array('category_code' => $category_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
    
	function save($category_id,$data)
	{
		if (is_numeric($category_id))
		{
			$this->db->where('category_id', $category_id);
			$this->db->update('article_categories',$data);
			return $category_id;
		}
		else
		{
			$this->db->insert('article_categories', $data);
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" block_id in (".$where_in.")",null,false);
		$this->db->delete('nc_custom_blocks');
	}
	
	public function find_root_category($category_id)
	{
		$parent_id = 99;
		
        // Load the passed category
		$category = $this->get_details($category_id);
		if(!$category)
			return false;
			
		if($category->parent_id > 0)
			return $this->find_root_category($category->parent_id);
		else
			return $category;
	}
}
?>