<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* usermanager
* Handles listing, editing and adding system users.
*/
class Usermanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = 50;
    private $documents_records_per_page = 3;
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc    
        $this->load->model("users_model");
        $this->load->model("email_model");
        $this->load->model("document_model");            
        $this->load->model("log_model");            
    }
   
   /**
   * @method index
   * @author Andrew Chapman
   * @version 1.0
   */    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "User Manager";
        $this->data["page_heading"] = "User Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        //$extra_sql = ", (SELECT CONCAT(first_name, ' ', last_name) FROM nc_users creator WHERE creator.user_id = u.created_by_user_id) as created_by";
        //$extra_sql = ", (SELECT CONCAT(first_name, ' ', last_name) FROM nc_users owner WHERE owner.user_id = u.owner_id) as owner";
        $extra_sql = "";
        
        $filters = array();
        $filters["order_by"] = "u.first_name asc";
        
        $this->data["users"] = $this->users_model->get_list(true, $this->records_per_page, 1, $count_all, "", "", $filters, $extra_sql);
        
        //die($this->db->last_query() . "<br>" . $count_all)
        
        $this->data["pages_no"] = $count_all / $this->records_per_page;
        $this->data["user_types"] = $this->users_model->get_user_types();
		$this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
        $this->data["builders"] = $this->users_model->get_builders();

        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/usermanager/prebody', $this->data); 
        $this->load->view('admin/usermanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }    
   
	/**
	* @method: user
	* @author: Andrew Chapman
	* @version 1.0  
	* 
	* @desc: The user method shows a user with the specified user id.
	* If no id code is given, it means it a new user is going to be created.
	* 
	* @param integer $user_id - The user id of the user to load.
	*/
	function user($user_id = "")
	{
	    $this->load->helper('form');
	    
		$this->data["page_heading"] = "User Details";
		$this->data['message'] = "";
        $user = false;
        
        $this->data["relationship_types"] = array(
            "Single" => "Single",      
            "Married" => "Married",
            "Defacto" => "Defacto",
            "Divorce" => "Divorce"
            );
            
		$form_values = array();
		$form_values["username"] = "";
		$form_values["first_name"] = "";
		$form_values["last_name"] = "";
		$form_values["email"] = "";
		$form_values["user_type_id"] = -1;

		// Check for a user postback
		$postback = $this->tools_model->isPost();        

		// If there was a postback, handle it.    
		if ($postback)
			$this->_handlePost($user_id, $form_values);

		if($user_id != "") //edit
		{
		    if(!is_dir(FCPATH.USER_FILES_FOLDER)) //FCPATH
                @mkdir(FCPATH.USER_FILES_FOLDER ,DIR_WRITE_MODE);   
             
            if(!is_dir(FCPATH.USER_FILES_FOLDER.$user_id))
                @mkdir(FCPATH.USER_FILES_FOLDER.$user_id, DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH.USER_FILES_FOLDER.$user_id."/documents"))
                @mkdir(FCPATH.USER_FILES_FOLDER.$user_id."/documents",DIR_WRITE_MODE);       
            
			// Load user details
			$user = $this->users_model->get_details(intval($user_id));
            $this->data['documents'] = $this->document_model->get_list('user_document', intval($user_id));
            $this->data["documents_records_per_page"] = $this->documents_records_per_page;
            
			if(!$user)
			{
				// The page could not be loaded.  Report and log the error.
				$this->error_model->report_error("Sorry, the user could not be loaded.", "Usermanager/user - the user with an id of '$user_id' could not be loaded");
				return;            
			}

			// Set the form_values fields only if the user has NOT posted the form back
			if(!$postback)
			{
				$form_values["username"] = $user->username;
				$form_values["first_name"] = $user->first_name;
				$form_values["last_name"] = $user->last_name;
				$form_values["email"] = $user->email;
				$form_values["user_type_id"] = $user->user_type_id;
                                $form_values["builder_id"] = $user->builder_id;
			}
            
            $this->data["username"] = $this->users_model->get_user_name($user->created_by_user_id);            

		}
        
        // pass the user object to the view
        $this->data["user"] = $user; 
        
        // If this user was created by another user, also load that user
        $created_by = false;
        if(!empty($user->created_by_user_id)) {
            $created_by = $this->users_model->get_details($user->created_by_user_id);    
        }
        
        $this->data["created_by"] = $created_by;

		$this->data["user_types"] = $this->users_model->get_user_types();
		
        $this->data["builders"] = $this->users_model->get_builders();
		$this->data["form_values"] = $form_values;
		$this->data['message'] = ($user_id == "") ? "To create a new user, enter the users details below." : "You are editing the user &lsquo;<b>$user->username</b>&rsquo;";
		$this->data["states"] = $this->tools_model->get_states();
		$this->data["websites"] = $this->website_model->get_list(array());
		
		$user_log = $this->users_model->get_log_details($user_id);
		
		
		if($user_log)
		{
			$log_ids = array();
            
			foreach($user_log->result() as $user_log)
			{
				$log_id = $user_log->log_id;
				array_push($log_ids, $log_id);			
			}
		
		    $this->data["user_history"] = $this->users_model->get_user_log($log_ids, $this->records_per_page, 1, $count_all);
		    
		    $this->data["pages_no"] = $count_all / $this->records_per_page;
		}
		else
		{
			$this->data["user_history"] = null;
		}
		//Upload
		$this->data['images'] = $this->document_model->get_files( "user_file", $user_id, 'order', $count_all );
		$this->data['count_all'] = $count_all;
		// Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "User Manager - User Details";
		$this->data['user_id'] = $user_id;

		// Load views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/user/prebody.php', $this->data); 
		$this->load->view('admin/user/main.php', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data);      
	}
    
   /***
   * @method _handlePost
   * @author Andrew Chapman
   * @version 1.0
   * 
   * _handlePost is called after the user posts the user details form back to the server.
   * It will update an existing user and create a new user if required.
   * 
   * @param integer $user_id  The user id being updated (blank if new user)
   * @param mixed $form_values   The form values associative array - used to pass details back to the view.
   */
    function _handlePost($user_id, &$form_values)
    {
      // The user has submitted the form back.
      // Is the form valid?
      $this->load->library('form_validation');
      $this->load->model('email_model');
      
      $user_type_id = $this->input->post("user_type_id");
      if(!is_numeric($user_type_id)) show_error("Invalid User Type");
      
      $data = array(
            "phone" 			            => '',
            "mobile" 			            => '',
            "home_phone" 			        => '',
            "fax" 			                => '',
            "enabled" 						=> '0',
            "bypass_disclaimer" 			=> '0',
            "company_name" 			        => '',
            "billing_address1" 				=> '',
            "billing_address2" 				=> '',
            "billing_suburb" 				=> '',
            "billing_postcode" 				=> '',
            "billing_state_id" 				=> null,
        	"broadcast_access_level_id"		=> '1',
      		"file_permission_access_level"	=> '1',
      		"subscribed"					=> '0',
      		"is_text_only_newsletters"		=> '0',
      		'billing_country_id'			=> null,
            "advisor_id"                 => null,
            "owner_id"                 => null,
            
            "additional_contact_first_name" => '',
            "additional_contact_middle_name" => '',
            "additional_contact_last_name" => '',
            "additional_contact_relationships" => '',
            "additional_contact_mobile" => '',
            "additional_contact_phone" => '',
            "additional_contact_email" => '',
            "additional_contact_comment" => '',
            
            "legal_purchase_entity" => '',
            "purchase_comments" => '',
            "acn" => '',
            "smsf_purchase" => '',
            "keywords" => '',
			"email_notification" => '0',
			"login_expiry_date" => '',
			"new_listing_email" => '0',
			"weekly_sales_report" => '0'
      );
       
      $this->form_validation->set_rules('username', 'Username', 'required|min_length[4]');
      $this->form_validation->set_rules('user_type_id', 'User Type', 'required|integer');
      $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|xss_clean');
      $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|xss_clean');
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');
      
      $this->form_validation->set_rules('additional_contact_email', 'Contact Email', 'valid_email|trim');
      $this->form_validation->set_rules('additional_contact_first_name', 'Contact First Name', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_middle_name', 'Contact Middle Name', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_last_name', 'Contact Last Name', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_relationships', 'Relationship', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_mobile', 'Contact Mobile', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_phone', 'Phone 2', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_mobile', 'Contact Mobile', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_mobile', 'Contact Mobile', 'trim|xss_clean');
      $this->form_validation->set_rules('additional_contact_comment', 'Contact Comment', 'trim|xss_clean');
      
      $this->form_validation->set_rules('legal_purchase_entity', 'Full Legal Purchase Entity', 'trim|xss_clean');
      $this->form_validation->set_rules('purchase_comments', 'Purchase comments and ownership split', 'trim|xss_clean');
      $this->form_validation->set_rules('acn', 'ACN', 'trim|xss_clean');
      $this->form_validation->set_rules('smsf_purchase', 'SMSF Purchase', 'trim|xss_clean');
      
      if($user_type_id == USER_TYPE_ADVISOR) {
        $this->form_validation->set_rules('advisor_id', 'Advisor ID', 'integer');     
        $this->form_validation->set_rules('owner_id', 'Owner ID', 'integer');     
      } else if ($user_type_id == USER_TYPE_SUPPLIER) {
        $this->form_validation->set_rules('builder_id', 'Builder ID', 'integer');
      } else if($user_type_id > USER_TYPE_ADVISOR) {
        $this->form_validation->set_rules('advisor_id', 'Advisor ID', 'required|integer');
        $this->form_validation->set_rules('owner_id', 'Owner ID', 'required|integer');
      }
      
      // If this is a new user, we need to make sure a password is provided too.
      if(($user_id == "") || ($_POST["password"] != ""))
      {
         $this->form_validation->set_rules('password', 'Password', 'required|min_length[4]');    
         $this->form_validation->set_rules('password_repeat', 'Password Repeated', 'required|min_length[4]|matches[password]');
      }
      
      if ($this->form_validation->run() == FALSE)
      {
         // Form validation failed
         $this->data["warning"] = validation_errors();

         // Pass any values that the user typed in back to the view.
         $form_values["first_name"] 				= $_POST["first_name"];
         $form_values["last_name"] 					= $_POST["last_name"];
         $form_values["email"] 						= $_POST["email"];    
         $form_values["username"] 					= $_POST["username"];         
         $form_values["user_type_id"] 				= $_POST["user_type_id"];
      }
      else
      {
        // The form has validated OK.
        // Prepare the model data array to pass to the view.
        $model_data = array();
           
        $required_fields = array();
        $missing_fields = false;
            
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $model_data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
                
            // Ensure that all required fields are present    
            if(in_array($key,$required_fields) && $data[$key] == "")
            {
                $missing_fields = true;
                break;
            }
        }

        if ($missing_fields)
        {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "SettingsManager/HandlerPost update");
            return;
        }
        
        // new_listing_email
        if (isset($_POST['new_listing_email'])) {
            if ($_POST['user_type_id'] != USER_TYPE_ADVISOR && $_POST['user_type_id'] != USER_TYPE_ADMIN) {
                $model_data["new_listing_email"] 	=  0;
            }
        }
         
         $model_data["first_name"] 		=  $_POST["first_name"];
         $model_data["last_name"] 		=  $_POST["last_name"];
         $model_data["email"] 			=  $_POST["email"];
         $model_data["username"] 		=  $_POST["username"];         
         $model_data["user_type_id"] 	=  $_POST["user_type_id"];
         $model_data["builder_id"]              =  $_POST['builder_id'];
        
         if (isset($_POST['user_type_id']) && ($_POST['user_type_id'] = USER_TYPE_LEAD)) {
         	$model_data["status"] 	=  $this->input->post("lead_status");
         }
         
         if($_POST["password"] != "")
         {
             $salt = random_string("alnum", 15);
             $model_data["password"] = hash("SHA256", $this->input->post("password") . $salt);
             $model_data["salt"] = $salt;
         }
         
         // If we're updating an existing user account, and if the account is being enabled, and if the account was not previously enabled,
         // check to see if a password has been set for the user.  If not, set the password and send the welcome email to the user.
         if((is_numeric($user_id)) && ($this->input->post("enabled") == 1))
         {
            $user = $this->users_model->get_details($user_id);
            if(($user) && (!$user->enabled))
            {
                // Create a password for the user and send them the welcome email.
                $password = random_string("alnum", 8);
                $salt = random_string("alnum", 15);
                $model_data["password"] = hash("SHA256", $password . $salt);
                $model_data["salt"] = $salt;
                
                $email_data = array();
                $email_data["first_name"] = $this->input->post("first_name");
                $email_data["added_by"] = "we've";
                $email_data["email"] = $this->input->post("email"); 
                $email_data["password"] = $password;
                $email_data["login_link"] = base_url() . "login";
                
                $this->email_model->send_email($this->input->post("email"), "welcome_to_aspire", $email_data);
            }    
         }
         
         $ok = true;
         
         // Are we creating a new user
         if($user_id == "")
         {
            // Yes we are - check if a user with this username already exists
            if($this->users_model->exists($_POST["username"]))
            {
               // This username has been taken.
               $this->data["warning"] = "Sorry, a user with this username exists already.";  
               $ok = false;
               
               // Set the form values with the post data
               $form_values["username"] 					= $_POST["username"];
               $form_values["first_name"] 					= $_POST["first_name"];
               $form_values["last_name"] 					= $_POST["last_name"];
               $form_values["email"] 						= $_POST["email"];                   
               $form_values["user_type_id"] 				= $_POST["user_type_id"]; 
               $form_values["broadcast_access_level_id"] 	= $_POST["broadcast_access_level_id"];       
            }
         }
         
         // Everything OK to proceed with the update/insert?
         if($ok)
         {
            $user_id = $this->users_model->save($user_id, $model_data);

            if(!$user_id)
            {
               // Something went wrong whilst saving the user data.
               $this->error_model->report_error("Sorry, the user could not be saved/updated.", "Usermanager/user save - the user with an id of '$user_id' could not be saved");
               return;
            }

            //insert the user permissions

            //first delete the user permission
            $this->users_model->delete_user_permissions($user_id);
            
            // Determine if the user is assigned to a specific article category
	        if(isset($_POST["article_category_id"]))
				$article_category_id = $this->input->post("article_category_id");
	        else
            	$article_category_id = null;	            

            //insert the new permissions
            $data = array();
            foreach ($_POST as $key => $value)
            {
                if ( substr($key, 0, 6) == '' )
                {
                    $data['user_id'] = $user_id;
                    $data['type'] = 'controller';
                    $data['foreign_id'] = $key;
                    $data['is_enabled'] = 1;
                    
                    // If we're inserting the entry for the article manager,
                    // also write any value we found for article category assignment.
                    if($key == "articlemanager")
                    	$data['article_category_id'] = $article_category_id;
                    else
                    	$data['article_category_id'] = null;
                    	
                    $this->users_model->insert_user_permissions($data);
                }
            }
            
            if(isset($_POST["website_permissions"]))
            {            	
				foreach($_POST["website_permissions"] as $website_id)
				{
                    $data['user_id'] = $user_id;
                    $data['type'] = 'website';
                    $data['foreign_id'] = $website_id;
                    $data['is_enabled'] = 1;
                    $this->users_model->insert_user_permissions($data);
				}
            }
            
            // Check for a remove Login block request
            $remove_block = $this->input->post("remove_block");
            if($remove_block == "1") {
                // Remove the users block
                $data["block_until"] = null; 
                $user_id = $this->users_model->save($user_id, $data);   
            }
            
            
            // The user was saved OK.  Redirect back to the listing screen
            redirect("/admin/usermanager/user/".$user_id);
            exit();
         }
      }            
    }
    
	function download($user_type_id = "")
	{
		// Get a recordset of users
		$users = $this->users_model->get_list($enabled = -1, $limit = "", $page_no = "", $count_all, $search_term = "", $user_type = "4");
		if(!$users)
			show_error("Sorry, no users were found");
			
		// Define output columns
		$output_columns = array("created_dtm", "first_name", "last_name", "email", "postcode", "country", "dob", "receive_info_on", "fan_of");			

		// Open a file handle to memory
		$fiveMBs = 5 * 1024 * 1024;			
		$fp = fopen("php://temp/maxmemory:$fiveMBs", 'r+');

		// Output header line
		$line = array();
		foreach($output_columns as $column)
		{
			$line[] = ucwords(str_replace("_", " ", $column));
		}
		
		fputcsv($fp, $line, ",", "\"");
		
		// Loop through recordset and output
		foreach($users->result() as $user)
		{
			$line = array();
			
			foreach($output_columns as $column)
			{
				$line[] = $user->$column;
			}
			
			fputcsv($fp, $line, ",", "\""); 				
		}
		
		// Rewind the memory dataset and get the result as a string
		rewind($fp);

		// Output CSV header
		header("Content-type: application/octet-stream");
		header("Content-Disposition: attachment; filename=memberdata.csv");
		header("Pragma: no-cache");
		header("Expires: 0"); 		

		// Output data 
		fpassthru($fp);
		
		// Close file handle
		fclose($fp);
	}
           
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        
        switch($type)
        {
            // 1 - Delete users
            case 1:
            
                //get page ids separated with ";"
                $user_ids = $this->tools_model->get_value("todelete","","post",0,false);
                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                $user_type = $this->tools_model->get_value("user_type","","post",0,false);
                
                if ($user_ids!="")
                {
                    $arr_ids = explode(";",$user_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id))
                        {
                            if ($where_in != "") $where_in.=",";
                            
                            $where_in .= $id;
                        }
                    }
                    
                    //$where_in = "(".$where_in.")";
                    if ($where_in!="")
                    {
                        $this->users_model->delete($where_in);
                        
                        $this->firephp->log($this->db->last_query());
                    }
                }
                
                //get list of users
                $users = $this->users_model->get_list(-1,$this->records_per_page,$current_page,$count_all,$search_terms, $user_type);
                
                //load view 
                $this->load->view('admin/usermanager/user_listing',array('users'=>$users,'pages_no' => $count_all / $this->records_per_page));
                
            break;
            
            // 2 - Page number changed
            case 2:
                
                $search_terms = $this->tools_model->get_value("tosearch", "", "post", 0, false);
                $user_type = $this->tools_model->get_value("user_type", "", "post", 0, false);
                $advisor_id = $this->tools_model->get_value("advisor_id", "", "post", 0, false);
                
                $order_by = $this->input->post("order_by");
                $order_dir = $this->input->post("order_dir");
        
                $filters = array();
                if(is_numeric($advisor_id)) $filters["advisor_id"] = $advisor_id;
                $filters["search_term"] = $search_terms;
                $filters["order_by"] = "u.first_name asc";
                
                if(($order_by != "") && ($order_dir != "")) {
                    $filters["order_by"] = $order_by . " " . $order_dir;    
                }
                
        
                $users = $this->users_model->get_list(true, $this->records_per_page, $current_page, $count_all, "", $user_type, $filters, "");
                
                
                // Load view
                $this->load->view('admin/usermanager/user_listing',array('users'=>$users,'pages_no' => $count_all / $this->records_per_page));
                
            break;
            // 3 - Search for users
            case 3:
                
                 $state_id = $this->input->post('state_type');
				// $filters['state_id'] = $state_id;
				
				$search_terms = $this->tools_model->get_value("tosearch", "", "post", 0, false);
                $user_type = $this->tools_model->get_value("user_type", "", "post", 0, false);
                $advisor_id = $this->tools_model->get_value("advisor_id", "", "post", 0, false);
				//$state_id = $this->tools_model->get_value("state_id", "", "post", 0, false);

                $order_by = $this->input->post("order_by");
                $order_dir = $this->input->post("order_dir");
                   
                // Get list of users
                $filters = array();
                if(is_numeric($advisor_id)) $filters["advisor_id"] = $advisor_id;
                $filters["search_term"] = $search_terms;
				$filters["state_id"] = $state_id;
                $filters["order_by"] = "u.first_name asc";
                
                if(($order_by != "") && ($order_dir != "")) {
                    $filters["order_by"] = $order_by . " " . $order_dir;    
                }
                
                $users = $this->users_model->get_list(true, $this->records_per_page, $current_page, $count_all, "", $user_type, $filters, "");
                
                //$this->firephp->log($this->db->last_query());
				// print_r($this->db->last_query());
                
                // Load view
                $this->load->view('admin/usermanager/user_listing',array('users'=>$users,'pages_no' => $count_all / $this->records_per_page));
                
            break;
            
            case 4: //change password
            
                    $new_password = $this->tools_model->get_value("new_password","","post",150,false);
                    $email_new_password = intval($this->tools_model->get_value("email_new_password",0,"post",1,false));
                    $user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false));
                    
                    $message = "";
                    
                    
                    
                    if ($user_id !=0 && $new_password != "")
                    {
                         $user_details = $this->login_model->getUserDetails($user_id);
                         if ($user_details)
                         {
                             //reset password
                             $this->users_model->change_password($user_id,$new_password);

                             $message .= "Password reset successfully.<br/>";
                             
                             if (intval($email_new_password) == 1) //send email to user
                             {
                                 $email_data = array (  "first_name" => $user_details->first_name,
                                                        "user_name"  => $user_details->username,
                                                        "password"   => $new_password);
                                 
                                 $this->email_model->send_email($user_details->email, "admin_reset_email", $email_data);
                                 
                                 $message .= "Email sent successfully.<br/>";
                             }
                             
                         }
                    }
                    else
                    {
                        $message .= "Error check user_id and password.";
                    }
                    
                    
                    $data['message'] = $message;
                    
                    echo json_encode($data);
            
            break;
            
            case 5: //upload logo
                $user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false));
                
                $files = $this->utilities->get_files("uploads/logos");
                
                $tmp_name = $_FILES["Filedata"]["tmp_name"];
                $name = $_FILES["Filedata"]["name"];
                
                $ext = pathinfo($name,PATHINFO_EXTENSION);
                if($ext == "jpg")
                {
                    $file_path = ABSOLUTE_PATH."uploads/logos/";
                    
                    if (is_dir($file_path))
                        @chmod($file_path,DIR_WRITE_MODE);
                   
                    move_uploaded_file($tmp_name, $file_path.$user_id.".".$ext);
                    
                    echo "done";
                }
                else
                    echo "Error: Invalid extension. Please use .jpg ";
            break;
            
            case 6: //delete logo
                $user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false));
                
                $file_path = ABSOLUTE_PATH."uploads/logos/";                 
                $file_name = $user_id.".jpg";
                
                //delete logo
                if (is_file($file_path.$file_name))
                {
                    unlink($file_path.$file_name);                    
                }
                
                $html = $this->load->view("admin/user_logo",array("user_id"=> $user_id),true); 
                
                $return_data = array();
                $return_data["html"] = $html;
                
                echo json_encode($return_data);
            break;
            
            case 7: //refresh logo
                $user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false));
                
                $html = $this->load->view("admin/user_logo",array("user_id"=> $user_id),true); 
                
                $return_data = array();
                $return_data["html"] = $html;
                
                echo json_encode($return_data);
            break;
            case 8: 
      			// User File Upload
      			
      			// Read in the details of the file that has been uploaded
				$tmp_name = $_FILES["Filedata"]["tmp_name"];
				$name = $_FILES["Filedata"]["name"];
				// Read in the user
				$user_id = $this->tools_model->get_value("user_id","","post",0,false);
				
                // Load the user object
                $user = $this->users_model->get_details($user_id);
                if(!$user)
                	die();

                if(!is_dir(FCPATH.USER_FILES_FOLDER))
    			{
    				@mkdir(FCPATH.USER_FILES_FOLDER, DIR_WRITE_MODE);
    			}
    
    			if(!is_dir(FCPATH.USER_FILES_FOLDER.$user_id))
    			{
    				@mkdir(FCPATH.USER_FILES_FOLDER.$user_id, DIR_WRITE_MODE);
    			}                	
                
				// Determine file path and move the temporary file to the final destination.
				$file_path = FCPATH . USER_FILES_FOLDER . $user_id . "/" . $name;
				move_uploaded_file($tmp_name, $file_path);
				chmod($file_path, 0666);

				// Make sure the upload worked OK.
				if(!file_exists($file_path))
				{
					echo "error";
					exit();
				}

                // Save the gallery image into the documents table in the database.
				$file_data =  array(
					"document_type" => "user_file",
					"foreign_id" => $user_id,
					"document_name" => $name,
					"document_path" => USER_FILES_FOLDER . $user_id . "/" . $name
				);

				$this->document_model->save("", $file_data, $user_id, "user_file", $use_order = TRUE);

				echo "done";

				break;
            case 9:
            	// Delete user files
                $return_data = array();
                
                $user_id = intval($this->tools_model->get_value("user_id", 0, "post", 0, false));
                $file_names = $this->tools_model->get_value("todelete", "", "post", 0, false);

                if ($file_names!="")
                {
                    $arr_files = explode(";",$file_names);
                    
                    $this->document_model->delete_files($arr_files, $user_id, "user_file");
                    
                    $suffixes = array("_gallerythumb", "_gallerydetail", "_galleryzoom");
                    
                    // Loop through each file and delete the full image set associated with each one.
                    foreach($arr_files as $file_to_delete)
                    {
                    	if($file_to_delete != "")
                    	{
                    		$full_path = FCPATH . USER_FILES_FOLDER . $user_id . "/$file_to_delete";
                    		
                    		if(file_exists($full_path))
							{
								$this->image->remove_image_set($suffixes, $full_path); 	
							}		
						}
                    }
                }
                                
                //reresh category list
                $return_data["html"] = $this->_refresh_images( $user_id );
                
                echo json_encode($return_data);
                
                break;
            case 10:
            	// Update user files
                $return_data = array();
                $desc = $this->tools_model->get_value("desc", "", "post", 0, false);
                $document_name = $this->tools_model->get_value("document_name", "", "post", 0, false);
                $id = $this->tools_model->get_value("id", "", "post", 0, false);

                if (!empty($desc) && !empty($document_name) && !empty($id))
                {
                    $data["document_description"] = $desc;
                    $data["document_name"] = $document_name;
                    $file_id = $this->document_model->save($id, $data);    	
                }
                return;
                break;
            case 11: //up and down order
                $return_data = array();
                
                //get id 
                $user_id = $this->tools_model->get_value("user_id","","post",0,false);
                $this->_refresh_images( $user_id );
            break;

            case 12: //up and down order
                $fileid = $this->tools_model->get_value("fileid", "", "post", 0, false);
                $file = $this->document_model->get_details($fileid);
                $this->data['file'] = $file;
                $this->load->view('admin/user/editfile', $this->data);
                return;
            break;
            
            case 13: // Delete logo
                $user_id = $this->tools_model->get_value("user_id","","post",0,false);
                    
                //do we have a valid user_id ?
                if (is_numeric($user_id))
                {
                    
                    $user_folder = FCPATH;
                    
                    $user_details = $this->users_model->get_details($user_id);
                    
                    if ($user_details)
                    {
                            $logo_name = $user_details->logo;
                            
                            //delete files
                            if (file_exists($user_folder.$logo_name)) unlink($user_folder.$logo_name);
                            if (file_exists($user_folder.$logo_name . "_thumb.jpg")) unlink($user_folder . $logo_name . "_thumb.jpg");
                            $this->users_model->save($user_id,array( "logo"=> "" ));      
                            
                            die("done");
                    }
                    else
                        die("Error: User id not found");
                }
                else
                        die("Error: Not a valid user id");
            break;
            
            case 14: //download user document

                 $file = trim(urldecode($this->tools_model->get_value("file",0,"post",0,false)));
                 $user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false)); 
                 $document_type = $this->tools_model->get_value("document_type",0,"post",0,false); 
                 
                 $path = FCPATH. USER_FILES_FOLDER.$user_id."/$document_type/".$file;
                 $path = trim($path);
                 
                 $this->load->helper('file');
                 write_file('text.txt', "path:" . $path, 'a+');

                 if(file_exists($path)) {
                    $this->utilities->download_file($path);
                 }

            break;
            
            case 15:
                //get files names separated with ";"

                $files_id = $this->tools_model->get_value("todelete","","post",0,false);

                $user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false));

                $file_names = array();

                if ($files_id!="") {

                    $arr_id_files = explode(";",$files_id);

                    $removed_userfiles = $this->db->where_in('id',$arr_id_files)
                                            ->get('documents');

                    //delete from documents table

                    $this->document_model->delete($arr_id_files, '');

                    //delete images from folders           

                    if($removed_userfiles) {
                        foreach($removed_userfiles->result() as $row) {
                            $file_names[] = $row->document_name;
                        }
                        $this->utilities->remove_file(STAGE_FILES_FOLDER.$stage_id."/documents",$file_names,"");
                    }

                }
                echo 'OK';
            break;
            
            case 16:
            	// Login as this user
		        $this->load->model('login_model');
		        $this->load->model('Users_model');
		    	$user_id = $this->input->post("user_id");
				
		        if($user_id)
		        {
		            // We're updating an existing user.  Load their details.
		            $user = $this->Users_model->get_array_details($user_id);
		            
		            $array_items = array(
			            'user_id' => '',
			            'username' => '',
			            'first_name' => '',
			            'last_name' => '',
			            'company' => '',
			            'logged_in' => '',
			            'user_type_id' => '',
			            'logo' => '',
			            'advisor_first_name' => '',
			            'advisor_last_name' => '',
			            'advisor_email' => '',
			            'advisor_phone' => '',
			            'advisor_logo' => ''
			        );
			                        
			        $this->session->unset_userdata($array_items);
		            $this->login_model->setSessionData($user);
		        }
		        else
		        {
		        	$return_data = array();
		        	$return_data["status"] = "FAILED";
		        	$return_data['message'] = 'User id is required';
		        	echo json_encode($return_data);
		        }
		        $return_data = array();
		        $return_data["status"] = "OK";
		        $return_data["message"] = "";
		        echo json_encode($return_data);
		        
            break;
        }
    }
    
    function _refresh_images( $user_id )
    {
        //get files
        $files = $this->document_model->get_list("user_document", $user_id); 
        $count_all = count($files);

        //load view 
        $this->load->view('admin/user/document_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));
    }
    
    function upload_file($upload_type, $user_id, $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($user_id)))
		{
			die ('{error: "Invalid upload type $upload_type or $user_id"}');
		}
		
		// Handle a logo upload
		if(($upload_type == "hero_image") || ($upload_type == "documents"))
		{
            // Load the user in question
            $user = $this->users_model->get_details($user_id);
            if(!$user)
            {
				die ('{error: "Invalid user"}');	
            }
            
            
            $path = ABSOLUTE_PATH . USER_FILES_FOLDER . $user_id . "/";
            if ( !is_dir($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . USER_FILES_FOLDER . $user_id . "/documents/";
            	if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            }
            
            $result = $this->qqfileuploader->handleUpload($path, $filename, true);
            
         	if($filename == "")
         	{
				$filename = $this->qqfileuploader->file->getName();
				
         		if($filename == "")
         		{
         			die ('{error: "Could not determine file name"}');
				} 				
         	}
         	
         	$file_path =  $path . $filename;
         	if(!file_exists($file_path))
         	{
				die ('{error: "File did not upload correctly"}');	
         	}
         	
			// Move the temporary file to the final path.
  			chmod($file_path, 0666);
  			$return_path = '';
  			
  			if($upload_type == "documents") {
  			    
                // Save the document files into the documents table in the database.
				$doc_data =  array(
					"document_type" => "user_document",
					"foreign_id" => $user_id,
					"document_name" => $filename,
					"document_path" => USER_FILES_FOLDER . $user_id . "/documents/" . $filename
				);
                
				$return_path = USER_FILES_FOLDER . $user_id . "/documents/" . $filename;
				
				$this->document_model->save("", $doc_data, $user_id, "user_document", $use_order = TRUE);			
				
			} elseif ($upload_type == 'hero_image') {
			    
			    $logo_folder1 = FCPATH.USER_FILES_FOLDER.$user_id."/";
            	$thumb_path = $logo_folder1 . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($logo_folder1.$filename, $thumb_path, $error_message,THUMB_USER_LOGO_WIDTH,THUMB_USER_LOGO_HEIGHT);
			    
			    // Update the article with the logo.
	        	$update_data = array("logo" => USER_FILES_FOLDER . $user_id. "/" . $filename);
				$this->users_model->save($user_id, $update_data);
				
				$return_path = site_url(USER_FILES_FOLDER . $user_id. "/" . $filename);
				
			}

			$return = array();
			$return["status"] = "OK";
			$return["fileName"] = $return_path;
			$return["success"] = true;	
			
			echo json_encode($return);
            exit();	
		}
		else
		{
			die ('{error: "Invalid file type"}');
		}	
    }
}
?>