<?
class Login_model extends CI_Model 
{
    function Login_model()
    {    
    	parent::__construct();  
    }
    
    /**
     * This is method getSessionData
     *
     * @param mixed $name the name of the variable in session
     * @return mixed Retuns the session data for the given variable if exists else empty string
     *
     */
   function getSessionData($name, $session_array = "cms_user")
   {
      $userdata = $this->session->userdata($session_array);
      
      if(isset($userdata[$name]) && $userdata[$name])
      {
         return $userdata[$name];
      }
      else
      {
         return "";
      }
   }
    
    public function is_logged_in($session_array = "cms_user")
    {
        $logged_in = true;
        
        if($session_array == "user")
        {
            if(!array_key_exists("logged_in", $this->session->userdata))
            {
                return false;    
            }
            
            return true;
        }
        
        if ($this->getSessionData("logged_in", $session_array) != 1)
            $logged_in = false;
            
        return $logged_in;
    }
    
    //types: 1 - administrator, 2 = member
    function check_username_password($username, $password, $user_type_id = -1)
    {
        $this->db->select('user_id, username, password, salt, first_name, last_name, email, user_type_id, company_name, created_by_user_id, r.value as currency, logo, phone, advisor_id, enabled');
        $this->db->from('nc_users');
        $this->db->join('nc_resources r',' currency = r.id and r.resource_type="currency" ', 'left');
        $this->db->where('username', $username);
        $this->db->where('enabled',1);
        
        if($user_type_id != -1)
        {
            $this->db->where('user_type_id', $user_type_id);
        }
        
        $query = $this->db->get();
        
        // If there is a at least one resulting row, return the recordset, otherwise return false
        if ($query->num_rows() <= 0)
        {
            return false;
        }         
        
        $user_array = $query->row_array();
        
        if($user_array["salt"] != "")
        {
            // There is a salt value defined.
            if($user_array["password"] == hash("SHA256", $password . $user_array["salt"]))
            {
                return $user_array;    
            }            
        }
        else
        {
            // No salt defined.  Do a straight md5 hash of the password for comparison 
            if($user_array["password"] == md5($password))
            {
                return $user_array;    
            }    
        }
        
        return false;
    }

    
    function logLogin($user_id, $ip)
    {
        
        $this->db->set('last_logged_ip',$ip);
        $this->db->set('last_logged_dtm','now()',false);
        $this->db->where('user_id',$user_id);
        
        $this->db->update('nc_users');
    }
    
    function getUserDetails($user_id)        
    {
       $query = $this->db->get_where('nc_users',array("user_id"=>$user_id),1);
       return $query->first_row();
    }
    
    function setSessionData($user=array())
    {
    	if( !in_array($user["user_type_id"], array(USER_TYPE_ADVISOR)) )
        {
        	$advisor = $this->Users_model->get_details($user['advisor_id']);
        	if ($advisor)
        	{
        		$advisor_first_name = $advisor->first_name;
        		$advisor_last_name = $advisor->last_name;
        		$advisor_email = $advisor->email;
        		$advisor_phone = $advisor->phone;
        		$advisor_logo = $advisor->logo;
        	}
        }

        $user_data = array(
            'user_id' => $user["user_id"],
            'username' => $user["username"],
            'first_name' => $user["first_name"],
            'last_name' => $user["last_name"],
            'company' => $user["company_name"],
            'logged_in' => true,
            'user_type_id' => $user["user_type_id"],
            'logo' => $user["logo"],
			'phone' => $user["phone"],
            'advisor_first_name' => isset($advisor_first_name) ? $advisor_first_name : '',
            'advisor_last_name' => isset($advisor_last_name) ? $advisor_last_name : '',
            'advisor_email' => isset($advisor_email) ? $advisor_email : '',
            'advisor_phone' => isset($advisor_phone) ? $advisor_phone : '',
            'advisor_logo' => isset($advisor_logo) ? $advisor_logo : ''
        );
                        
        $this->session->set_userdata($user_data);

        //update last login date and ip
        $this->logLogin($user["user_id"], $this->input->ip_address());
    }
	
	function lockUser($username)
    {
        $date = date('Y-m-d H:i:s', time()+10800);
		
		$this->db->where('email',$username);
		$this->db->or_where('username',$username);
		$this->db->set('block_until',$date);
        $this->db->update('nc_users');
		
    }
	
	function check_blocked_username($username)
    {
        
        $query = $this->db->select('block_until')
					  ->from('nc_users')
					  ->where('username', $username)
					  ->get();
                      
        if ($query->num_rows() <= 0)
        {
            return false;
        } 

		return $query->row()->block_until;			
    }
	
	function update_user($logged_in,$user_id)
	{
		$this->db->set('logged_in',$logged_in);
		$this->db->where('user_id',$user_id);
		$this->db->update('nc_users');
	}
	
	function disable_user($username)
	{
		$this->db->set('enabled', '0');
		$this->db->where('email',$username);
		$this->db->update('nc_users');
	}
	
	function user_logged($username)
	{
		$query = $this->db->select('logged_in')
					  ->from('nc_users')
					  ->where('username', $username)
					  ->get();
                      
        if ($query->num_rows() <= 0)
        {
            return false;
        } 

		return $query->row()->logged_in;
	
	}
	
	function is_user_enabled($user_id)
	{
		$query = $this->db->select('enabled')
					  ->from('nc_users')
					  ->where('user_id', $user_id)
					  ->get();
                      
        if ($query->num_rows() <= 0)
        {
            return false;
        } 

		return $query->row()->enabled;
	
	}
}