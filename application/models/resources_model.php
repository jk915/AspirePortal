<?php
/***
* @abstract	The resources model is used to list, save, delete and get the details of items in the resources table.
* @author	Andrew Chapman
* @created	13th Feb, 2010
*/
class Resources_model extends CI_Model 
{
	function Resources_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	/***
	* @method get_list
	* @author Andrew Chapman
	* @abstract This method gets a list of all resources from the database for a particular resource type.  
	* 
	* @param string resource type - The resource type to get the resources for
	* 
	* @returns A list of resources
	*/
	public function get_list($resource_type = "", $order_by = "name ASC")
	{
		$count_all = $this->db->count_all_results('resources');

		$this->db->select('*');
		$this->db->from('resources');
		
		// If a resource type has been defined, only load resources with a matching resource type.
		if($resource_type != "")
			$this->db->where("resource_type", $resource_type);  
		
		// Order the resources by name
		$this->db->order_by($order_by);     

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
	* @method get_details
	* @author Andrew Chapman
   * @abstract	The get_details method loads all of the details for a particular resource record.
   *   
   * @param integer $resource_id	The id of the resource to load.
   */
	public function get_details($resource_id)
   {
		if(($resource_id == "") || (!is_numeric($resource_id)))
			show_error("Invalid resource ID");
		
		// Check to see if a record with this username exists.
		$query = $this->db->get_where('resources', array('id' => $resource_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
   /***
	* @method save
	* @author Andrew Chapman
   * @abstract	The save method either saves the data passed in the data array for an existing resource
   * (defined by resource_id), of if no resource_id is given, a new resource entry is created an it's
   * id is returned.
   *   
   * @param integer $resource_id	The id of the resource to save.  Leave blank if creating a new resource.
   * @param	data	An associative array of data to save or insert.
   * 
   * @returns	The id of the resource.
   */    
	function save($resource_id, $data)
	{
		if(($resource_id != "") && (is_numeric($resource_id)))
		{
			$this->db->where('id', $resource_id);
			if(!$this->db->update('resources',$data))
				show_error("Resources_model::save - Could not save resource");
				
			return $resource_id;
		}
		else
		{
			$this->db->insert('resources', $data);
			return $this->db->insert_id();
		}
	}
    
   /***
	* @method delete
	* @author Andrew Chapman
   * @abstract	The delete method deletes all resources with id's containued within a comma seperated string.
   *   
   * @param string $where_in	A comma separated string of resources to delete.
   */  
	public function delete($where_in)
	{
		$this->db->where(" id in (".$where_in.")", null, false);
		$this->db->delete('resources');
	}
}
?>