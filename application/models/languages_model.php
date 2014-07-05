<?php
class Languages_model extends CI_Model 
{
	function Languages_model()
	{
		// Call the Model constructor
		parent::__construct(); 
	}
    
	/***
	* @method get_list
	* @author Andrew Chapman
	* @abstract This method gets a list of all languages in the database.
	* 
	* 
	* @returns A list of languages
	*/
	public function get_list()
	{
		$this->db->select('*');
		$this->db->from('languages');
		$this->db->order_by("language", "ASC");     

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
    * The get_details method returns the details of a specific language.
    * 
    * @param integer $lang_id The primary key of the language
    * @returns language row on success, false on failure.
    */
	public function get_details($lang_id)
	{
		// Check to see if a record with this lang_id exists.
		$query = $this->db->get_where('languages', array('lang_id' => $lang_id));

		// If there is a resulting row, return it, otherwise return false.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
    /***
    * The save method either creates a new language record (in the event that the 
    * passed lang_id is blank) or updates an existing record.
    * 
    * @param integer $lang_id The primary key of the language record to update.  Pass in a blank value to create a new record.
    * @param array $data The data to update.
    * @returns The lang_id of the new or existing record.
    */
	function save($lang_id, $data)
	{
		// Check to see if the lang_id is a valid numeric.  
		// If it is, update the existing record.  Otherwise create a new record.
		if (is_numeric($lang_id))
		{
			$this->db->where('lang_id', $lang_id);
			$this->db->update('languages', $data);
			return $lang_id;
		}
		else
		{
			$this->db->insert('languages', $data);
			return $this->db->insert_id();
		}
	}
    
    /***
    * The delete method deletes language records from the database.
    * 
    * @param string $where_in A comma separated list of language id records to delete.
    */
	public function delete($where_in)
	{
		$this->db->where(" lang_id in (".$where_in.")",null,false);
		$this->db->delete('languages');
	}
}
?>