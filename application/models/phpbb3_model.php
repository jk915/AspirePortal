<?php
class Phpbb3_model extends CI_Model 
{
    function Phpbb3_model()
    {
        // Call the Model constructor
          parent::__construct();                            
    }
    
    /***
    * Copies a Myndie/Ninja CMS user record to phpBB.  
    * You must do this before trying to login to phpBB with a user.
    * 
    * @param integer $nc_user_id - The Myndie/Ninja CMS user id of the user to copy to phpBB.
    */
    function copy_user($nc_user_id)
    {   
    	if(($nc_user_id == "") || (!is_numeric($nc_user_id)))
    		return false;
    	
    	// Setup phpBB constants
    	define("IN_PHPBB", true);	// Required to allow the functions include to work
    	define("CI_INTEGRATION", true);	// Injected within phpBB code when necessary to flag that CI integration is required.
		$phpbb_root_path = ABSOLUTE_PATH . "forum/";
		$phpEx = "php";   
		
		// Include all the phpBB includes and libs
		require_once($phpbb_root_path . 'common.' . $phpEx);   
    	
		// Load the user record
		$this->db->where("user_id", $nc_user_id);
		$this->db->from("users");
		$query = $this->db->get();
		
        // Make sure there is a resulting row
        if ($query->num_rows() != 1)
        	return false;
        	
        $user = $query->row();
        $username = $user->username;
        	
		// We need to do work on the phpbb tables.
		// Change the db prefix to phpbb
		$dbprefix_original = $this->db->dbprefix;
		$this->db->dbprefix = "phpbb_";
		
		// See if there is a matching record in phpBB3
		$this->db->where("username", $username);
		$this->db->from("users");
		$query = $this->db->get();
		
        // If there is a resulting row - nothing to do
        if ($query->num_rows() >= 1)
        {
            // Change the db prefix back
            $this->db->dbprefix = $dbprefix_original;
        	return true;		
        }
        
        // Create a new user record in phpBB
        $data = array();
        $data["username"] = $username;
        $data["username_clean"] = $username;
        $data["group_id"] = PHPBB_USERGROUP_GENERAL;
        $data["user_type"] = 0;
        $data["user_perm_from"] = 0;
        $data["user_lang"] = "en";
        $data["user_dateformat"] = "D M d, Y g:i a";
        $data["user_style"] = 1;
        $data["user_ip"] = $this->input->ip_address();
        $data["user_regdate"] = time();
        $data["user_passchg"] = time();
        $data["user_lastmark"] = time();
        $data["user_email"] = $user->email;
        $data["user_password"] = phpbb_hash("nodirectauth");
        $data["user_email_hash"] = phpbb_email_hash($user->email);
        
        // Insert the user record
        if(!$this->db->insert("users", $data))	
        	die("Failed to create new phpBB user record");
        
        // Get the id of the new user record	
        $phpbb_user_id = $this->db->insert_id();
        
        // Now insert the group records
        $phpbb_groups = array(PHPBB_USERGROUP_GENERAL, PHPBB_USERGROUP_NEWLYREGISTERED);
        
        // Loop through all defined groups
        foreach($phpbb_groups as $phpbb_group_id)
        {
        	// Create insert data array
			$data = array();
			$data["group_id"] = $phpbb_group_id;
			$data["user_id"] = $phpbb_user_id;
			$data["group_leader"] = 0;
			$data["user_pending"] = 0;
			
			// Insert the group
	        if(!$this->db->insert("user_group", $data))	
        		die("Failed to create new phpBB user group");			
        }
		
		// Change the db prefix back
		$this->db->dbprefix = $dbprefix_original;
		
		return true;
	}
	
	/***
	* Logs the user in to phpBB3.  Note, the user must already have a record
	* in the phpBB3 users table.  To create this, call the copy_user method (above) first.
	* 
	* @param integer $nc_user_id - The Myndie/Ninja CMS user id of the user to login.
	* @return bool True on success, false on failure.
	*/
	function log_user_in($nc_user_id)
	{
    	if(($nc_user_id == "") || (!is_numeric($nc_user_id)))
    		return false;
    		
    	global $phpbb_root_path, $phpEx, $auth; 
    		
    	// Setup phpBB constants
    	define("IN_PHPBB", true);	// Required to allow the functions include to work
    	define("CI_INTEGRATION", true);	// Injected within phpBB code when necessary to flag that CI integration is required.
		$phpbb_root_path = ABSOLUTE_PATH . "forum/";
		$phpEx = "php";
		
		// Include all the phpBB includes and libs
		require_once($phpbb_root_path . 'common.' . $phpEx);
    	
		// Load the user record
		$this->db->where("user_id", $nc_user_id);
		$this->db->from("users");
		$query = $this->db->get();
		
        // Make sure there is a resulting row
        if ($query->num_rows() != 1)
        	return false;
        	
        $user = $query->row(); 
        $username = $user->username;
        
		// We need to do work on the phpbb tables.
		// Change the db prefix to phpbb
		$dbprefix_original = $this->db->dbprefix;
		$this->db->dbprefix = "phpbb_";
		
		// See if there is a matching record in phpBB3
		$this->db->where("username", $username);
		$this->db->from("users");
		$query = $this->db->get();
		
        // If there is NOT a resulting row - the user doesn't exist in phpBB
        // Return false.
        if ($query->num_rows() != 1)
        {
            // Change the db prefix back
            $this->db->dbprefix = $dbprefix_original;
        
        	return false;        
        }
        	
        $phpbb_user = $query->row();
        $phpbb_user_id = $phpbb_user->user_id;
        
        $login = $auth->login($phpbb_user->username, "nodirectauth", true, true, false);
        
        $result = ((is_array($login)) && (isset($login["status"])) && ($login["status"] == LOGIN_SUCCESS));
        
		// Change the db prefix back
		$this->db->dbprefix = $dbprefix_original;
		
		return $result;        	
	}
}