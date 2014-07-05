<?php
class Product_category_model extends CI_Model
{
	function Product_category_model()
	{
		// Call the Model constructor
		parent::__construct();
	}

	public function get_list($parent_id = "", $limit = "", $page_no = "", $enabled_only = false)
	{
		$this->db->select('*');
		$this->db->from('nc_product_categories');

		// If a parent id has been defined, only load categories with a matching parent id.
		if($parent_id != "")
			$this->db->where("parent_id", $parent_id);
			
		if($enabled_only)
			$this->db->where("enabled", 1);

		$this->db->order_by("seq_no", "ASC");

		if ($limit != "" && $page_no!= "" )
		{
			$this->db->limit(intval($limit), intval(($page_no-1) * $limit));
		}
		
		$this->db->order_by("product_category_id", "ASC");

		$query = $this->db->get();

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query;
		}
		else
			return false;
	}

	public function get_details($category_id, $by_code = false)
	{
		// Check to see if a record with this category id/code exists.
		if($by_code)
			$query = $this->db->get_where('nc_product_categories', array('category_code' => $category_id, 'enabled' => 1));
		else
			$query = $this->db->get_where('nc_product_categories', array('product_category_id' => $category_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}
		else
			return false;
	}

	function save($category_id, $data)
	{
		if (is_numeric($category_id))
		{
			$this->db->where('product_category_id', $category_id);
			$this->db->update('nc_product_categories',$data);
			return $category_id;
		}
		else
		{
			$this->db->insert('nc_product_categories', $data);
			return $this->db->insert_id();
		}
	}

	public function delete($where_in)
	{
		$this->db->where(" product_category_id in (".$where_in.")",null,false);
		$this->db->delete('nc_product_categories');
	}
}
?>