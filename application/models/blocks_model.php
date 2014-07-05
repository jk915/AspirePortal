<?php
class Blocks_model extends CI_Model 
{
	function Blocks_model()
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
	public function get_list($limit = "", $page_no = "", &$count_all = 0, $enabled = '', $sidebar = '')
	{
		$count_all = $this->db->count_all_results('custom_blocks');
		
		$this->db->select('custom_blocks.*');

        if($enabled != '')
            $this->db->where('enabled', $enabled);
        if($sidebar != '')
            $this->db->where('show_on_sidebar', $sidebar);
        
		$this->db->from('custom_blocks');
		
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
	
	/***
	* The get_assigned_blocks method gets a list of blocks that are assigned to a specific page
	* or article, and to a specific position (left or right sidebar).
	* 
	* @param int $foreign_id The id of the page or article that the blocks are bound to
	* @param string $assignment_type Should be either 'page' or 'article'
	* @param mixed $position Should be either 'left' or 'right'
	*/
	function get_assigned_blocks($foreign_id, $assignment_type = "page", $position = "right")
	{
		if(($foreign_id == "") || (!is_numeric($foreign_id)))
			return false;
			
		$this->db->select('custom_blocks.*');
		$this->db->from('blocks_assigned');
		$this->db->join('custom_blocks', 'custom_blocks.block_id = blocks_assigned.block_id');
		$this->db->where('assignment_type', $assignment_type);
		$this->db->where('foreign_id', $foreign_id);
		$this->db->where('position', $position);
      $this->db->where('enabled', '1');
      $this->db->where('show_on_sidebar', '1');
		$this->db->order_by("blocks_assigned.seq_no", "ASC");
		
		$query = $this->db->get();	

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query;
		}    	 
		else
			return false;		 
	}
	
	function update_assigned_blocks($foreign_id, $array_blocks, $assignment_type = "page", $position = "right")
	{
		if(($foreign_id == "") || (!is_array($array_blocks)))
		{
			return false;		
		}
		
		// Clear out all blocks that are currently assigned to this id, type and position
		$this->db->where('assignment_type', $assignment_type);
		$this->db->where('foreign_id', $foreign_id);
		$this->db->where('position', $position);
		$this->db->delete('blocks_assigned');
		
		// Now add the assigned blocks back in
		$seq_no = 1;
		
		foreach($array_blocks as $block_id)
		{
            if($block_id != "")
            {
			    $data = array();
			    $data["foreign_id"] = $foreign_id;
			    $data["assignment_type"] = $assignment_type;
			    $data["position"] = $position;
			    $data["block_id"] = $block_id;
			    $data["seq_no"] = $seq_no;
			    
			    $this->db->insert('blocks_assigned', $data);
			    
			    $seq_no++;
            }
		} 		
	}
}
