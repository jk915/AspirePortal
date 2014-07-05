<?php
class Modules_model extends CI_Model 
{
    private $CI;
    
	function __construct() 
	{
		// Call the Model constructor
		parent::__construct();      
        $this->CI = & get_instance();
	}
	
	/***
	* get_list
	* Gets a list of modules from the modules table, taking into account any filters.
	* 
	* @param array $filters An associative array of filters options.  Options are: user_type_id, enabled
	* @param string $order_by  Determines how to order the recordset
	* @returns A resultset of module records if successful, false on failure.
	*/
	public function get_list($filters = array(), $order_by = "seqno ASC")
	{
		$this->db->select("*");
		$this->db->from("modules");
		
		if(isset($filters["user_type_id"]))
		{
			$this->db->join("module_permissions", "modules.module_id = module_permissions.module_id", "inner");
			$this->db->where("module_permissions.user_type_id", $filters["user_type_id"]);
		}
		
		if(isset($filters["enabled"]))
		{
			$this->db->where("enabled", $filters["enabled"]);
		}
		
		if(isset($filters["favourite"]))
		{
			$this->db->where("favourite", $filters["favourite"]);
		}		
		
		$this->db->order_by($order_by);
		
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
	
	/***
	* Gets a list of modules that the user of the specified user type has access to.
	* 
	* @param integer $user_type_id	The user type to grab the modules for
	* @param integer $favourites	Set to 1 if you only want to get "favourite" menu items.
	*/
	public function get_user_modules($user_type_id, $favourites = 0)
	{
		$filters = array();
		$filters["enabled"] = 1;
		
		// Do we only want to get *favourite* menu items?
		if($favourites)
		{
			$filters["favourite"] = 1;
		}
		
		// If the user is not an administrator, load only the modules they have permission to view.
		if($user_type_id != USER_TYPE_ADMIN)
		{
			$filters["user_type_id"] = $this->user_type_id;
		}
			
        return $this->get_list($filters);
	}
}