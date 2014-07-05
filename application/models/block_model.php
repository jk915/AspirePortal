<?php
class Block_model extends CI_Model 
{
	function Block_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	/***
	* @method get_list
	* @abstract This method gets a list of all blocks from the database.  
	* 
	* @param integer $limit - Limits the recordset to a specific number of records
	* @param integer $page_no - Starts the recordset at a specific page no.
	* @param integer $count_all - Counts all records.
	* 
	* @returns A list of blocks
	*/
	public function get_list($limit = "", $page_no = "", &$count_all)
	{
		$count_all = $this->db->count_all_results('nc_custom_blocks');

		$this->db->select('*');
		$this->db->from('nc_custom_blocks');

		$this->db->order_by("block_name", "ASC");     

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
    
	public function get_details($block_id, $by_name = false)
	{
		// Check to see if a record with this username exists.
		if(!$by_name)
			$query = $this->db->get_where('nc_custom_blocks', array('block_id' => $block_id));
		else
			$query = $this->db->get_where('nc_custom_blocks', array('block_name' => $block_id, 'enabled' => 1));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
    
	function save($block_id,$data)
	{
		if (is_numeric($block_id))
		{
			$this->db->where('block_id',$block_id);
			$this->db->update('nc_custom_blocks',$data);
            return $block_id;
		}
		else
		{
			$this->db->insert('nc_custom_blocks',$data);
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" block_id in (".$where_in.")",null,false);
		$this->db->delete('nc_custom_blocks');
	}
	
	/***
	* Prints the contents of the specified block, with the block contents parsed and any tags replaced.
	* 
	* @param string $blockName The name of the block to print
	*/
	public function printBlock($blockName)
	{
		$block = $this->get_details($blockName, true);
		if(!$block)
		{
			return;
		}
		
		$CI = &get_instance();
		                                            
		$html = $CI->utilities->replaceTags($CI, $block->block_content, $hint = "");
		
		echo $html;  
	}	
}