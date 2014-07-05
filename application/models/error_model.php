<?
/**
* @desc common function used accross all models/views
* @created 18 November, 2009
* @last modified: 
*/
class Error_model extends CI_Model
{
	function Error_model()
	{	
		parent::__construct();		
	}
	
	/***
	* The insert method inserts a new error log entry, writing the log message $log to the 
	* database.
	* 
	* @param mixed $log - The value to write to the database.
	*/
	public function insert($log)
	{
		// No cart item exists, so insert a new one
		$data = array("log" => $log);
		$this->db->insert('nc_error_log', $data); 
	}
	
	function report_error($display_message, $log_message)
	{
		// Log the error into the database.
		$this->insert($log_message);  
		
        show_error("A problem has occured: ".$display_message);
		/*$data = array();
		$data["message"] = $display_message;
		$data["meta_title"] = "A problem has occured";
		$data["meta_keywords"] = "";
		$data["meta_description"] = "";
		
		$this->load->view('header', $data);
		$this->load->view('error/prebody', $data); 
		$this->load->view('error/main', $data);*/
	}
}