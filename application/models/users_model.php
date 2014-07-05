<?php
class Users_model extends CI_Model 
{
    function Users_model()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    /***
    * @method get_list
    * @author Andrew Chapman
    * @abstract This method gets a list of all users from the database.  
    * 
    * @param integer $enabled - 1 returns a list of enabled users, 0 = not enabled, -1 = all users.
    * @param integer $limit - Limits the recordset to a specific number of records
   * @param integer $page_no - Starts the recordset at a specific page no.
   * @param integer $count_all - Counts all records.
    * 
    * @returns A list of pages
    */
    public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $user_type = "", $filters = array(), $select_sql = "")
    {
        $ci = &get_instance();
        
        $this->_get_list($enabled, $limit, $page_no, $count_all, $search_term, $user_type, true, $filters, $select_sql);                                 
        $count_all = $this->db->count_all_results();        
        
        $this->_get_list($enabled, $limit, $page_no, $count_all, $search_term, $user_type, false, $filters, $select_sql);
        $query = $this->db->get();  
		
        //If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
   
	}    
    
    function _get_list($enabled = -1, $limit = "", $page_no = "", $count_all, $search_term = "", $user_type = "", $only_count = false, $filters = array(), $select_sql = "")
    {
        $this->db->select('u.*, ut.type, get_user_note_last_created(u.user_id) as notes_last_created, CONCAT(adv.first_name, " ", adv.last_name) as advisor, CONCAT(own.first_name, " ", own.last_name) as owner, s.name as billing_state, Floor(TIMESTAMPDIFF(SECOND, u.last_logged_dtm, Now()) / 86400) as days_since_login' . $select_sql, false);
        $this->db->from('users u');
		
        /*
        $sub = $this->subquery->start_subquery('select');
        $sub->select('n.created_date')->from('nc_notes n')->where('n.`foreign_id`', 'u.`user_id`')->order_by('n.`created_date`','desc')->LIMIT(1);
        $this->subquery->end_subquery('notes_last_created'); 
        */
	    //$this->db->query('LEFT JOIN nc_notes n ON n.foreign_id=u.user_id LEFT JOIN   (SELECT foreign_id, MAX(created_date) maxDate     FROM payments  GROUP BY user_ID ) b ON n.foreign_id = b.user_ID AND n.created_date = b.maxDate');
		
        $this->db->join('user_types ut','u.user_type_id = ut.user_type_id','inner');
        $this->db->join('states s','u.billing_state_id = s.state_id','left');
        $this->db->join('users adv','u.advisor_id = adv.user_id','left outer');            
        $this->db->join('users own','u.owner_id = own.user_id','left outer');
          
        ///Join by Mayur
        //$this->db->join('nc_notes n','n.foreign_id=u.user_id','left');

        if($enabled > -1)
           $this->db->where('u.enabled', $enabled);            
         
        if($search_term !="")
           $this->db->like('u.username',$search_term);            
        
		if (isset($filters['status']) && !empty($filters['status']))
        	$this->db->where_in('u.status', $filters['status']);
			
		if (isset($filters['user_status']) && !empty($filters['user_status'])) {
			if($filters['user_status'] == 'active') {
				$this->db->where('u.enabled', '1');	
        	} else {
				$this->db->where_in('u.enabled', '0');
			}
        }
			
    	if (isset($filters['by_owner_id']) && intval($filters['by_owner_id']))
        {
        	$this->db->or_where('u.owner_id', $filters['by_owner_id']);  
        }
		
		
		if (isset($filters['state_id']) && intval($filters['state_id']))
        {
        	$this->db->where('u.billing_state_id', $filters['state_id']);  
        }
		
		
		
		
    	if(isset($filters['created_by_user_id']) && intval($filters['created_by_user_id']))
        {
        	$created_by_user_id = $filters['created_by_user_id'];
        	
            // Modification BY AC - Added support for the advisor_id column
        	if(isset($filters['owner_id']) && intval($filters['owner_id']) && (!isset($filters["created_by_only"])))
        	{
        		$owner_id = $filters['owner_id'];
                
                // also include the users of all the users I created, but don't include the users of advisors that I created.
        		$this->db->where("(u.created_by_user_id = '$created_by_user_id' OR u.created_by_user_id IN ( SELECT user_id FROM nc_users WHERE created_by_user_id='$created_by_user_id' AND user_type_id > " . USER_TYPE_ADVISOR . ") OR u.owner_id = '$owner_id' OR u.advisor_id = '$owner_id')");
        	}
        	else
        	{
                if(isset($filters["include_user_id"])) {
                    $this->db->where("(u.created_by_user_id = '$created_by_user_id' OR u.user_id = " . $this->db->escape($filters["include_user_id"]) . ")");    
                } else {
                    where($filters, "created_by_user_id", "u");    
                }
                
        	}
        }
        
        if (isset($filters['in_arr_ids']) && sizeof($filters['in_arr_ids']))
        {
        	$this->db->where_in('u.user_id',$filters['in_arr_ids']);
        	if (isset($filters['created_by']) && intval($filters['created_by']))
        	{
        		$this->db->or_where('u.created_by_user_id',$filters['created_by']);
        	}
        }
        
        if($user_type != "")
        {
        	$this->db->where_in('u.user_type_id',$user_type);
        }
        	  
        //where($filters, "created_by_user_id");
        where($filters, "deleted", "u");
        where($filters, "subscribed");
        where($filters, "advisor_id", "u");
		
        
        if(array_key_exists("search_term", $filters))
        {
            $term = $filters["search_term"];
            
            $sql = "((u.first_name LIKE '" . $term . "%') OR ";    
            $sql .= "(u.last_name LIKE '" . $term . "%') OR ";    
            $sql .= "(u.email LIKE '%" . $term . "%') OR ";
            $sql .= "(adv.first_name LIKE '" . $term . "%') OR ";
            $sql .= "(adv.last_name LIKE '" . $term . "%') OR ";
            $sql .= "(own.first_name LIKE '" . $term . "%') OR ";
            $sql .= "(own.last_name LIKE '" . $term . "%') OR ";            
            $sql .= "(u.company_name LIKE '" . $term . "%') OR ";    
            $sql .= "(u.keywords LIKE '" . $term . "%')) ";    
            
            $this->db->where($sql);
        }
        
        if(array_key_exists("order_by", $filters))
        {
            $this->db->order_by($filters["order_by"]);
        }
        else
        {
            if(!$only_count)
            {     
                $this->db->order_by("u.username", "ASC");   
            }
        }
                  
		if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }
        
    }
   
   /**
   * @desc The get_details method loads all properties of a particular page as defined by page_code
   */
    public function get_details($user_id, $by_username = FALSE)
    {
        $this->db->select('users.*, Floor(TIMESTAMPDIFF(SECOND, users.last_logged_dtm, Now()) / 86400) as days_since_login');
        $this->db->join('countries',"countries.country_id = users.billing_country_id",'left')
            ->select('countries.name AS billing_country_name')
            ->join('states',"states.state_id = users.billing_state_id",'left')
            ->select('states.name AS billing_state_name')
            ->join('users owner',"owner.user_id = users.created_by_user_id",'left')
            ->select('owner.created_by_user_id AS owner_created_by_user_id')
            ->join('user_types ut',"ut.user_type_id = users.user_type_id",'inner')
            ->select('ut.type')
            ->join('builders',"builders.builder_id = users.builder_id",'left')
            ->select('builders.builder_name');
            
        
        // Check to see if a record with this username exists.
        if($by_username)
            $query = $this->db->get_where('users', array('users.username' => $user_id));
        else
            $query = $this->db->get_where('users', array('users.user_id' => $user_id));

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    public function get_array_details($user_id, $by_username = FALSE)
    {
    	$this->db->select('users.*');
        $this->db->join('users owner',"owner.user_id = users.created_by_user_id",'left')
                 ->select('owner.created_by_user_id AS owner_created_by_user_id');
        // Check to see if a record with this username exists.
        if($by_username)
            $query = $this->db->get_where('users', array('users.username' => $user_id));
        else
            $query = $this->db->get_where('users', array('users.user_id' => $user_id));

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
    		return $query->row_array();
        }         
        else
            return false;
    }

    public function save($user_id, $data)
    {
        if(is_numeric($user_id))
        {
            $this->db->where('user_id',$user_id);
            
            if($this->db->update('nc_users',$data))
                return $user_id;    
            else
                return false;
        }
        else
        {
            $this->db->insert('nc_users',$data);    
            return $this->db->insert_id(); 
        }
    }
    
    public function delete($where_in)
    {
        $this->db->where(" user_id in (".$where_in.")",null,false);
        $this->db->delete('nc_users');
    }
   

    /***
    * @method exists
    * @author Andrew Chapman
    * @version 1.0
    * 
    * The exists method checks to see if a user with a given username exists.
    * Returns true if there is an existing user, false if not.
    *                   
    * @param string $username - The username to check.
    */
    public function exists($username)
    {
        // Check to see if a record with this username exists.
        $query = $this->db->get_where('nc_users', array('username' => $username));

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
            return $query;
        }        
        else
            return false;
    } 
     
   /*
   * @method exists
   * @version 1.0 
   * 
   * Returns all user types
   */
    public function get_user_types()
    {
        $query = $this->db->order_by("type");
        $query = $this->db->get("user_types");


        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }

    public function get_builders()
    {
        $query = $this->db->order_by("builder_name");
        $query = $this->db->get("builders");

        if ($query->num_rows() > 0)
        {
            return $query;
        }
        else
            return false;
    }
   
    public function change_password($user_id, $new_password)
    {
        $salt = random_string("alnum", 15);    

        $this->db->where('user_id',$user_id);
        $this->db->update('nc_users',array('password' => hash("SHA256", $new_password . $salt), 'salt' => $salt, 'enabled' => '1', 'logged_in' => ''));
    }
   
    public function get_user_with_hash($hash)
    {
        $query = $this->db->get_where('nc_users', array('hash' => $hash));

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }        
        else
            return false;
    }
    
    public function get_details_email($email)
    {
        // Check to see if a record with this username exists.
        $query = $this->db->get_where('nc_users', array('email' => $email));

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    public function update_password($url)
    {
        $query = $this->db->get_where('nc_users',array('reset_password_url'=>$url));
        if($query->num_rows()>0)
        {
            $query=$query->row();
            $this->save( $query->user_id, array('password'=>$query->new_password,'new_password'=>'','reset_password_url'=>''));
            return $query;
        }
        else
        {
            return false;
        }
    }
   
    /***
    * @method exists_email
    * @version 1.0
    * 
    * The exists_email method checks to see if a user with a given email address exists.
    * Returns the user's details if there is an existing user, false if not.
    *                   
    * @param string $email - The email address to check.
    */
    function exists_email($email)
    {
        // Check to see if a record with this email address exists.
        $query = $this->db->get_where('nc_users', array('email' => $email));

        if ($query->num_rows() > 0)
        {
            return $query->row();
        }        
        else
            return false;    
    }

    function insert_user_permissions($insert)
    {
        $this->db->insert('user_permissions',$insert);
    }

    function delete_user_permissions($user_id)
    {
        $this->db->where('user_id', $user_id );
        $this->db->delete('user_permissions');
    }

    function get_permissions($user_id, $type = "controller", $foreign_id = "")
    {
        $user = $this->get_details($user_id);
        
        if($user->user_type_id == ADMIN_USER_TYPE_ID) 
            return false;
        
        $permissions = array();
        $allowed_controllers = explode(",", EDITOR_CONTROLLERS);
        
        foreach($allowed_controllers as $controller)
        {
            $permissions[]["foreign_id"] = $controller;
        }
        
        return $permissions;
    }
   
    function check_user_controller_permission($user_id, $controller, $category_id = 0)
    {
        $user = $this->get_details($user_id);

        // If the user is an admin then they have access to everything.
        if($user->user_type_id == ADMIN_USER_TYPE_ID)
        {
            return true;
        }
        else if($controller == "admin_logout")
        {
            // Everyone has access to logout
            return true;
        }
        else if($controller == "admin_menu")
        {
            // Everyone has access to the main menu
            return true;
        }
        else if($user->user_type_id != USER_TYPE_EDITOR)
        {
            // Beyond this point, only editors have access to the CMS.
            return false;
        }

        $allowed_controllers = explode(",", EDITOR_CONTROLLERS);
        
        return (in_array($controller, $allowed_controllers)); 
    }
    
    /***
    * The check_user_menu_permission method checks to see if the specified
    * controller name appears in the permissions array.  If it does, true is returned,
    * otherwise false is returned.
    * 
    * @param array $permissions_array The permissions associative array.
    * @param string $foreign_id The controller name or website id to test against.
    */
    function check_user_menu_permission($permissions_array, $foreign_id)
    {
        // If there is no permissions array then the user
        // does have access to this module (probably an admin user)
        if(!$permissions_array)
            return true;
            
        // If the permissions array is empty then no need to check anything
        if(count($permissions_array) == 0)
            return false;
            
        foreach($permissions_array as $permission)
        {
            // If the controller matches the permisson foreign id, then the user
            // does have access.
            if($permission["foreign_id"] == $foreign_id)
                return true;
        }
        
        // Permission to this module denied.
        return false;
    }
    
    /***
    * The get_options_array method returns an associative array of available module options.
    */
    function get_options_array()
    {
        $options = array(
                "admin_articlemanager"         => "Article Manager",
                "admin_blockmanager"         => "Block Manager",
                "admin_pagemanager"         => "Page Manager",
                //"admin_websitemanager"         => "Website Manager",
                "admin_menumanager"         => "Menu Manager",
                "admin_usermanager"         => "User Manager",
                //"admin_storemanager"         => "Store Manager",
                "admin_settingsmanager"     => "Global Settings",
                "admin_filemanager"         => "Files &amp; Resources",
                "admin_broadcastmanager"    => "Broadcast Manager",  
                //"admin_usergroupmanager"    => "User Group Manager",
                "admin_couponmanager"        => "Coupon Manager",
                "admin_accesslevelmanager"    => "Access Level Manager",
                "admin_productmanager"        => "Product Manager",
                "admin_ordermanager"        => "Order Manager",
                "admin_emailmanager"        => "Email Manager"
            );
            
        return $options;        
    }
    
    function get_partners_by_advisor_id($advisor_id='')
    {
        $query = $this->db->get_where('nc_users', array('user_type_id' => USER_TYPE_PARTNER, 'advisor_id' => $advisor_id));
        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return false;
        }
    }
    
    function get_investor_by_advisor_id($advisor_id='')
    {
        $query = $this->db->get_where('nc_users', array('user_type_id' => USER_TYPE_INVESTOR, 'advisor_id' => $advisor_id));
        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return false;
        }
    }
    
    function get_enquiries_by_advisor_id($advisor_id='')
    {
        $query = $this->db->get_where('nc_users', array('user_type_id' => USER_TYPE_LEAD, 'advisor_id' => $advisor_id));
        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return false;
        }
    }
    
    /***
    * Gets reserved, signed, sold statistics for a partner
    * 
    * @param integer $partner_id The id of the partner
    */
    function get_partner_stats($partner_id)
    {
        $sql = "SELECT get_partner_status_count(%d, 'reserved') as num_reserved, " .
            "get_partner_status_count(%d, 'signed') as num_signed, " .
            "get_partner_status_count(%d, 'sold') as num_sold";
            
        $sql = sprintf($sql, $partner_id, $partner_id, $partner_id);
        
        $result = $this->db->query($sql);
        
        if($result->num_rows() <= 0)
        {
            return false;
        } 
        
        return $result->row();
    } 
    
    /***
    * Gets reserved, signed, sold statistics for an advisor
    * 
    * @param integer $advisor_id The id of the advisor
    */
    function get_advisor_stats($advisor_id)
    {
        $sql = "SELECT get_advisor_status_count(%d, 'reserved') as num_reserved, " .
            "get_advisor_status_count(%d, 'signed') as num_signed, " .
            "get_advisor_status_count(%d, 'sold') as num_sold";
            
        $sql = sprintf($sql, $advisor_id, $advisor_id, $advisor_id);

        $result = $this->db->query($sql);
        		
        if($result->num_rows() <= 0)
        {
            return false;
        } 
        
        return $result->row();
    }
    
    /***
    * Gets reserved, signed, sold statistics for an investor
    * 
    * @param integer $investor_id The id of the investor
    */
    function get_investor_stats($investor_id)
    {
        $sql = "SELECT get_investor_status_count(%d, 'reserved') as num_reserved, " .
            "get_investor_status_count(%d, 'signed') as num_signed, " .
            "get_investor_status_count(%d, 'sold') as num_sold";
            
        $sql = sprintf($sql, $investor_id, $investor_id, $investor_id);
        
        $result = $this->db->query($sql);
        
        if($result->num_rows() <= 0)
        {
            return false;
        } 
        
        return $result->row();
    }
    
    /***
    * Returns the users name in first_name + last_name + (company_name) format.
    * 
    * @param int $user_id The id of the user to get the name for
    * @returns the users name if successfull, blank/empty string if not.
    */
    function get_user_name($user_id)
    {
        if(!is_numeric($user_id)) return "";
        
        $user = $this->get_details($user_id);
        if(!$user) return "";
        
        $name = $user->first_name;
        if($user->last_name != "") $name .= " " . $user->last_name;
        
        if($user->company_name != "") $name .= " (" . $user->company_name . ")";
        
        return $name;
    }           
	
	
	/////////////////////////By Mayur- Taskseveryday[Active enquiry in Dashboard page]
	
	public function getactive_enquiry()
	{
		
		
$sql="SELECT u.*, ut.type, CONCAT(adv.first_name, ' ', adv.last_name) as advisor, CONCAT(own.first_name, ' ', own.last_name) as owner, s.name as billing_state, Floor(TIMESTAMPDIFF(SECOND, u.last_logged_dtm, Now()) / 86400) as days_since_login, get_last_note_date(u.user_id) as notes_last_created FROM (`nc_users` u) INNER JOIN `nc_user_types` ut ON `u`.`user_type_id` = `ut`.`user_type_id` LEFT JOIN `nc_states` s ON `u`.`billing_state_id` = `s`.`state_id` LEFT OUTER JOIN `nc_users` adv ON `u`.`advisor_id` = `adv`.`user_id` LEFT OUTER JOIN `nc_users` own ON `u`.`owner_id` = `own`.`user_id` WHERE `u`.`status` IN ('HOT') 
AND (u.created_by_user_id = '119' OR u.created_by_user_id IN ( SELECT user_id FROM nc_users WHERE created_by_user_id='119' AND user_type_id > 3) OR u.owner_id = '119' OR u.advisor_id = '119') AND `u`.`user_type_id` IN (7) ORDER BY `u`.`first_name` ASC";


		$this->db->query('SELECT u.*, ut.type, CONCAT(adv.first_name, " ", adv.last_name) as advisor, CONCAT(own.first_name, " ", own.last_name) as owner, s.name as billing_state, Floor(TIMESTAMPDIFF(SECOND, u.last_logged_dtm, Now()) / 86400) as days_since_login ,(select n.created_date from nc_notes n where n.foreign_id=a.user_id  ORDER BY  n.`created_date` DESC LIMIT 0 , 1) as notes_last_created FROM `nc_users` a ORDER BY notes_last_created DESC ');
		
		 $query = $this->db->get();  
		 print_r($query);
	}
	
	public function number_of_reports($advisor_id)
	{
		$sel = "SELECT count(*) as number_of_partners, (SELECT count(*) FROM `nc_properties` WHERE `status` = 'completed purchase' AND advisor_id = $advisor_id) as number_of_sales, (SELECT count(*) FROM `nc_users` WHERE `user_type_id` = 6 AND `advisor_id` = $advisor_id) as number_of_investors, (SELECT count(*) FROM `nc_users` WHERE `user_type_id` = 7 AND `advisor_id` = $advisor_id) as number_of_leads FROM `nc_users` a WHERE a.`user_type_id` = 5 AND a.`advisor_id` = $advisor_id";
		
		$query = $this->db->query($sel);
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query->row();
	}
	
	public function number_of_users($advisor_id)
	{
		
		$this->db->select('*');
		$this->db->where_in('user_type_id', array(5,6,7));
		$this->db->where('advisor_id', $advisor_id);
		$this->db->order_by("first_name","ASC");
		$this->db->from('nc_users');
		$query=$this->db->get();
				
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query;
		
	}
	
	public function get_user_advisor($user_id, $user_type_id)
	{
		// $this->db->select('*');
		// $this->db->where('user_id', $user_id);
		// $this->db->from('nc_users');
		// $this->db->join('users adv','user_id = adv.advisor_id','self');
		// $query=$this->db->get();
			
		if($user_type_id == 1 || $user_type_id == 2 || $user_type_id == 3)
		{
			$sql = "Select email AS adv_email,first_name,last_name from nc_users where user_id = $user_id";
		}
		else
		{
			$sql = "SELECT u2.email AS adv_email, u2.first_name AS first_name, u2.last_name AS last_name
			FROM nc_users u1
			INNER JOIN nc_users u2
			ON u1.advisor_id = u2.user_id
			WHERE u1.user_id = $user_id";
		}
		
		$query = $this->db->query($sql);
			
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query->result();
	
	}
	
	public function get_user_id($username)
	{
		$this->db->select('user_id, user_type_id, first_name');
		$this->db->where('email', $username);
		$this->db->from('nc_users');
		$query=$this->db->get();
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query->result();
		
	}
	
	public function get_email_notification_admins()
	{
		$this->db->select('email');
		$this->db->from('nc_users');
		$this->db->where('user_type_id', '1');
		$this->db->where('email_notification', '1');
		$query=$this->db->get();
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query->result();
		
	}
	
	public function expired_users()
	{
		$date = date('Y-m-d');
		$this->db->select('*');
		$this->db->from('nc_users');
		$this->db->where('enabled', '1');
		$this->db->where('login_expiry_date',$date);
		$query=$this->db->get();
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query->result();
	}
	public function update_user_expired($update_user_id)
	{
		
		$this->db->set('enabled', '0');
		$this->db->where_in('user_id',$update_user_id);
		$this->db->update('nc_users');
	}
	
	public function get_log_details($user_id)
	{
		$this->db->select('log_id');
		$this->db->from('nc_user_log');
		$this->db->where('foreign_id', $user_id);
		$query=$this->db->get();
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query;
	}
	
	public function get_user_log($log_ids, $limit, $page_no, &$count_all)
	{
		$this->db->select('*');
		$this->db->from('nc_usertracking');
		$this->db->where_in('user_identifier',$log_ids);
		$this->db->order_by('timestamp', 'desc');
		
		if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }
		$query=$this->db->get();
		$count_all = $this->db->count_all_results();
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query;
	}
	
	public function get_advisor()
	{
		$this->db->select('email');
		$this->db->from('nc_users');
		$this->db->where('enabled',1);
		$this->db->where('user_type_id',3);
		$this->db->where('new_listing_email',1);
		$this->db->or_where('weekly_sales_report',1);
		
		$query=$this->db->get();
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query;
		
	}
	
	public function get_new_listing_advisor()
	{
		$this->db->select('u.*,ut.type as user_type');
		$this->db->from('nc_users u');
		$this->db->where('u.enabled',1);
		$user_type_id = array(USER_TYPE_ADVISOR, USER_TYPE_ADMIN);
		// $user_type_id = array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER);
		$this->db->where_in('u.user_type_id',$user_type_id);
		$this->db->where('u.new_listing_email',1);
		$this->db->order_by('u.first_name','asc');
		$this->db->join('nc_user_types ut','ut.user_type_id = u.user_type_id');
		$query=$this->db->get();
		
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        return $query;
		
	}
	
	public function get_weekly_sales_advisor()
	{
	
		$this->db->select('u.*,ut.type as user_type');
		$this->db->from('nc_users u');
		$this->db->where('u.enabled',1);
		$user_type_id = array('3','5');
		$this->db->where_in('u.user_type_id',$user_type_id);
		$this->db->where('u.weekly_sales_report',1);
		$this->db->order_by('u.first_name','asc');
		$this->db->join('nc_user_types ut','ut.user_type_id = u.user_type_id');
		$query=$this->db->get();
	
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query;
		
	}
	
	public function get_user_weekly_sales_advisor($user_id)
	{
	
		$this->db->select('*');
		$this->db->from('nc_users');
		$this->db->where('user_id',$user_id);
	
		$query=$this->db->get();
	
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query->result();
	}

	public function get_user_new_listing_advisor($user_id)
	{
	
		$this->db->select('*');
		$this->db->from('nc_users');
		$this->db->where('user_id',$user_id);
	
		$query=$this->db->get();
	
		if($query->num_rows() <= 0)
        {
            return false;
        } 
        
        return $query->result();
	}
	
	///////////////////////////End
	
}