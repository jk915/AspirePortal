<?php
/**
* The Menu controller shows the main menu to the user.
*/
class Menu extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	private $user_type_id;	// The user type of the logged in user.
	
	/**
	* @method Menu (Constructor)
	* @version 1.0
	*/
	function __construct()
	{
		parent::__construct();

		// Load models etc
		$this->load->model("login_model");         
		$this->load->model("modules_model");  

		// Create the data array
		$this->data = array();				
	}
   
	/**
	* @method index
	* @version 1.0
	*/   
	function index()
	{			
		// Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "Website Administration Menu";
		$this->data["page_heading"] = "Main Menu";
		$this->data["name"] = $this->login_model->getSessionData("firstname");
		$this->data["permissions"] = false;

		// Load views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/menu/prebody.php', $this->data); 
		$this->load->view('admin/menu/main.php', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data); 
	}
}
