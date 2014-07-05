<?php if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
 * @desc	This class will help us to get, insert, update the broadcasts in database
 * @author 	Zoltan Jozsa
 *
 */
class Broadcast_model extends CI_Model {
	function Broadcast_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
	
	/**
	 * @method	get_details
	 * @access	public
	 * @desc	this method gets a details of a broadcast by broadcast id
	 * @author	Zoltan Jozsa
	 * @param 	int						$broadcast_id				- the id of the broadcast
	 * 
	 * @return  broadcast object or FALSE
	 */
	public function get_details( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return FALSE;
			
		$this->db->select( '*, nc_broadcasts.name as name' );
		$this->db->where( 'broadcasts.broadcast_id', $broadcast_id );
		$this->db->join( 'broadcast_status', 'broadcast_status.broadcast_status_id = broadcasts.broadcast_status_id' );
		$query = $this->db->get( 'broadcasts' );
		
		if( $query->num_rows() > 0 )
		{
			$broadcast 					= $query->row();
			$broadcast->nr_recipients 	= $this->get_recipient_number( $broadcast_id );
			$broadcast->nr_clicks		= $this->get_number_of_clicks( $broadcast_id );
			$broadcast->nr_unsubscribes	= $this->get_number_of_unsubscribes( $broadcast_id );
			return $broadcast;
		}
			
		return FALSE;
	}
	
	/**
	 * @access	public
	 * @desc	this method returns a user by filter param or all users
	 * @author	Zoltan Jozsa
	 * @param 	array					$params					- filter parameters
	 * @return  user object or array with user objects
	 */
	function get_many_by( $params = array(), $count = FALSE )
	{
		// setting filters
		$this->set_db_params( $params, $count );

		//$this->db->select( '*, (SELECT COUNT(broadcast_recipient_id) FROM nc_broadcast_recipients WHERE nc_broadcast_recipients.broadcast_id = nc_broadcasts.broadcast_id) as nr_recipients' );
		$this->db->join( 'broadcast_status', 'broadcast_status.broadcast_status_id = nc_broadcasts.broadcast_status_id' );
		$query = $this->db->get( 'broadcasts' );
		
		if( $count )
			return $query->num_rows();
		
		if( $query->num_rows() > 0 )
		{
			// if there is more users	
			foreach( $query->result() as $broadcast )
			{
				$broadcast->nr_recipients = $this->get_recipient_number( $broadcast->broadcast_id );
			}
			return $query;
		}
		
		return FALSE;
	}
	
	/**
	 * @method	save
	 * @access	public
	 * @desc	this method saves a new broadcast or updates an old one in database
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast for update
	 * @param	array					$data							- the data to udate or insert
	 * @return 	array with broadcast_status object or FALSE
	 */
	public function save( $broadcast_id = '', $data = array() )
	{
		if( !empty( $broadcast_id ) && is_numeric( $broadcast_id ) ) // update
		{
			$this->db->where( 'broadcast_id', $broadcast_id );
			if( $this->db->update( 'broadcasts', $data ) )
				return $broadcast_id;
		}
		else // save new
		{
			if( $this->db->insert( 'broadcasts', $data ) )
				return $this->db->insert_id();
		}
				
		return FALSE;
	}
	
	/**
	 * @method	delete
	 * @access	public
	 * @desc	this method delete broadcast(s) from database
	 * @author	Zoltan Jozsa
	 * @param	mixed					$ids							- the array with ids
	 * @return 	boolean
	 */
	public function delete( $ids = array() )
	{
		if( is_array( $ids ) )
			$this->db->where_in( 'broadcast_id', $ids );
		else
			$this->db->where( 'broadcast_id', $ids );
			
		return $this->db->delete( 'nc_broadcasts' );
	}
	
	/**
	 * @method	get_all_statuses
	 * @access	public
	 * @desc	this method gets all existing broadcast status from database
	 * @author	Zoltan Jozsa
	 * @return 	array with broadcast_status object or FALSE
	 */
	public function get_all_statuses()
	{
		$query = $this->db->get( 'broadcast_status' );
		
		if( $query->num_rows() > 0 )
			return $query;
			
		return FALSE;
	}
	
	/**
	 * @method	get_all_templates
	 * @access	public
	 * @desc	this method gets all existing broadcast templates from database
	 * @author	Zoltan Jozsa
	 * @return 	array with broadcast_template object or FALSE
	 */
	public function get_all_templates()
	{
		$query = $this->db->get( 'broadcast_templates' );
		
		if( $query->num_rows() > 0 )
			return $query;
			
		return FALSE;
	}
	
	/**
	 * @method	get_all_access_levels_to
	 * @access	public
	 * @desc	this method gets all existing broadcast send to access levels from database
	 * @author	Zoltan Jozsa
	 * @param	boolean					$enabled_only				- just enabled access levels or not
	 * @return 	array with broadcast_access_levels object or FALSE
	 */
	public function get_all_access_levels_to( $enabled_only = TRUE )
	{
		if( $enabled_only )
			$this->db->where( 'is_enabled', '1' );
			
		$query = $this->db->get( 'broadcast_access_levels' );
		
		if( $query->num_rows() > 0 )
			return $query;
			
		return FALSE;
	}
	
	/**
	 * @method	get_all_recipients
	 * @access	public
	 * @desc	this method gets all recipients for a broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with broadcast_access_levels object or FALSE
	 */
	public function get_all_recipients( $broadcast_id = '', $limit = '', $count = FALSE )
	{
		if( empty( $broadcast_id ) )
			return FALSE;
			
		$this->db->where( 'broadcast_id', $broadcast_id );
		$query = $this->db->get( 'nc_broadcasts' );
		if( $query->num_rows() > 0 )
		{
			// get all unsubscribed user
			$unsubscribed_user_ids = $this->get_unsubscribed_user_ids( $broadcast_id );
			if( sizeof( $unsubscribed_user_ids ) > 0 )
			{
				$where = '';
				foreach( $unsubscribed_user_ids as $user )
				{
					if( strlen( $where ) > 1 ) $where .= ', ';
					
					$where .= $user->user_id;
				}
			}
			
			$broadcast = $query->row();
			$this->db->select( '*, (SELECT level FROM nc_broadcast_access_levels WHERE broadcast_access_level_id = nc_users.broadcast_access_level_id) as access_level' );
			// get users by broadcast send to
			if( $broadcast->send_to == 'Access Level' )
			{
				$this->db->where( 'broadcast_access_level_id', $broadcast->send_to_access_level_id );
			}
			
			if( !empty( $where ) )
			{
				$this->db->where( "( subscribed = '1' OR user_id in ($where) )" );
				//$this->db->or_where_in( 'user_id', $where );
			}
			else
			{
				$this->db->where( 'subscribed', '1' );
			}
			$this->db->where( "user_id not in ( SELECT user_id FROM nc_broadcast_not_recipients WHERE broadcast_id = '$broadcast_id' )" );
			$this->db->where( 'enabled', '1' );
			
			// if count equal TRUE, then count the results
			if( $count )
			{
				return $this->db->count_all_results( 'users' );
			}
			
			// set the limit
			if( !empty( $limit ) )
				$this->db->limit( $limit );
			$recipients = $this->db->get( 'users' );
			
			//echo $this->db->last_query();die();
			
			if( $recipients->num_rows() > 0 )
				return $recipients;
		}
		
		return FALSE;
	}
	
	/**
	 * @method	get_recipients_many_by
	 * @access	public
	 * @desc	this method gets all recipients for a broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with broadcast_access_levels object or FALSE
	 */
	public function get_recipients_many_by( $params = array(), $count = FALSE )
	{
		if( empty( $params['broadcast_id'] ) )
			return FALSE;
			
		$this->db->where( 'broadcast_id', $params['broadcast_id'] );
		$query = $this->db->get( 'nc_broadcasts' );
		
		if( $query->num_rows() > 0 )
		{
			// get all unsubscribed user
			$unsubscribed_user_ids = $this->get_unsubscribed_user_ids( $params['broadcast_id'] );
			if( sizeof( $unsubscribed_user_ids ) > 0 )
			{
				$where = '';
				foreach( $unsubscribed_user_ids as $user )
				{
					if( strlen( $where ) > 1 ) $where .= ', ';
					
					$where .= $user->user_id;
				}
			}
			
			$broadcast = $query->row();
			$this->set_db_params( $params, $count );
			
			$this->db->select( '*, (SELECT level FROM nc_broadcast_access_levels WHERE broadcast_access_level_id = nc_users.broadcast_access_level_id) as access_level' );
			// get users by broadcast send to
			if( $broadcast->send_to == 'Access Level' )
			{
				$this->db->where( 'broadcast_access_level_id', $broadcast->send_to_access_level_id );
			}
			
			if( !empty( $where ) )
			{
				$this->db->where( "( subscribed = '1' OR user_id in ($where) )" );
				//$this->db->or_where_in( 'user_id', $where );
			}
			else
			{
				$this->db->where( 'subscribed', '1' );
			}
			$this->db->where( "user_id not in ( SELECT user_id FROM nc_broadcast_not_recipients WHERE broadcast_id = '$broadcast->broadcast_id' )" );
			$this->db->where( 'enabled', '1' );
			$recipients = $this->db->get( 'users' );
			
			if( $count )
				return $recipients->num_rows();
				
			if( $recipients->num_rows() > 0 )
				return $recipients;
		}
		
		return FALSE;
	}
	
	/**
	 * @method	get_all_not_recipients
	 * @access	public
	 * @desc	this method gets all user id who isn't a recipients for a broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with broadcast_not_recipients object or FALSE
	 */
	public function get_all_not_recipients( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return array();
			
		$this->db->select( 'user_id' );
		$this->db->where( 'broadcast_id', $broadcast_id );
		$query = $this->db->get( 'broadcast_not_recipients' );
		
		if( $query->num_rows() > 0 )
			return $query->result();
			
		return array();
	}
	
	/**
	 * @method	get_all_broadcast_user_clicks
	 * @access	public
	 * @desc	this method gets all user id who clicked in the broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with user_id s object or FALSE
	 */
	public function get_all_broadcast_user_clicks( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return array();
			
		$this->db->select( 'user_id' );
		$this->db->where( 'broadcast_id', $broadcast_id );
		$query = $this->db->get( 'broadcast_clicks' );
		
		if( $query->num_rows() > 0 )
			return $query->result();
			
		return array();
	}
	
	/**
	 * @method	get_all_broadcast_unsubscribed_users
	 * @access	public
	 * @desc	this method gets all user id who unsubscribed from broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with user_id s object or FALSE
	 */
	public function get_all_broadcast_unsubscribed_users( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return array();
			
		$this->db->select( 'user_id' );
		$this->db->where( 'broadcast_id', $broadcast_id );
		$query = $this->db->get( 'broadcast_unsubscribes' );
		
		if( $query->num_rows() > 0 )
			return $query->result();
			
		return array();
	}
	
	/**
	 * @method	get_recipient_number
	 * @access	public
	 * @desc	this method count all recipients for a broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with user_id s object or FALSE
	 */
	public function get_recipient_number( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return '0';
			
		$nr_recipients = 0;
			
		// get the broadcast
		$this->db->where( 'broadcast_id', $broadcast_id );
		$query = $this->db->get( 'broadcasts' );
		if( $query->num_rows() > 0 )
		{
			// get all unsubscribed user
			$unsubscribed_user_ids = $this->get_unsubscribed_user_ids( $broadcast_id );
			if( sizeof( $unsubscribed_user_ids ) > 0 )
			{
				$where = '';
				foreach( $unsubscribed_user_ids as $user )
				{
					if( strlen( $where ) > 1 ) $where .= ', ';
					
					$where .= $user->user_id;
				}
			}
			
			$broadcast = $query->row();
			if( $broadcast->send_to == 'Access Level' )
			{
				$this->db->where( 'broadcast_access_level_id', $broadcast->send_to_access_level_id );
			}
			
			if( !empty( $where ) )
			{
				$this->db->where( "( subscribed = '1' OR user_id in ($where) )" );
				//$this->db->or_where_in( 'user_id', $where );
			}
			else
			{
				$this->db->where( 'subscribed', '1' );
			}
			$this->db->where( 'enabled', '1' );
			$query = $this->db->get( 'users' );
			
			if( $query->num_rows() > 0 )
			{
				$nr_recipients = $query->num_rows();
				
				// if the broadcast send to == Pick, we have selected the recipients
				if( $broadcast->send_to == 'Pick' )
				{
					$this->db->where( 'broadcast_id', $broadcast_id );
					$not_recipients = $this->db->count_all_results( 'nc_broadcast_not_recipients' );
					
					$nr_recipients -= $not_recipients;
				}
			}
			
			return $nr_recipients;
		}
		
		return FALSE;
	}
	
	/**
	 * @method	get_number_of_clicks
	 * @access	public
	 * @desc	this method count all clicks for a broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with user_id s object or FALSE
	 */
	public function get_number_of_clicks( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return '0';
		
		$this->db->where( 'broadcast_id', $broadcast_id );
		
		return $this->db->count_all_results( 'broadcast_clicks' );
	}
	
	/**
	 * @method	get_number_of_unsubscribes
	 * @access	public
	 * @desc	this method count all unsubscribes for a broadcast
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with user_id s object or FALSE
	 */
	public function get_number_of_unsubscribes( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return '0';
		
		$this->db->where( 'broadcast_id', $broadcast_id );
		
		return $this->db->count_all_results( 'broadcast_unsubscribes' );
	}
	
	/**
	 * @method	get_unsubscribed_user_ids
	 * @access	public
	 * @desc	this method get all unsubscribed user ids by broadcast id
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @return 	array with user_id s object or FALSE
	 */
	public function get_unsubscribed_user_ids( $broadcast_id = '' )
	{
		if( empty( $broadcast_id ) )
			return array();
			
		$this->db->select( 'user_id' );
		$this->db->where( 'broadcast_id', $broadcast_id );
		$query = $this->db->get( 'broadcast_unsubscribes' );
		
		if( $query->num_rows() > 0 )
			return $query->result();
			
		return array();
	}
	
	/**
	 * @method	delete_from_not_recipient
	 * @access	public
	 * @desc	this method delete row(s) from not recipients by broadcast id and user id
	 * @author	Zoltan Jozsa
	 * @param	mixed						$broadcast_id					- the id(s) of the broadcast(s)
	 * @param	mixed						$user_id						- the id(s) of the user(s)
	 * @return 	boolean
	 */
	public function delete_from_not_recipient( $broadcast_id = '', $user_id = array() )
	{
		if( empty( $broadcast_id ) )
			return FALSE;
			
		// if is array the broadcast id
		if( is_array( $broadcast_id ) )
			$this->db->where_in( 'broadcast_id', $broadcast_id );
		else // if not array
		{
			$this->db->where( 'broadcast_id', $broadcast_id );
			
			if( is_array( $user_id ) ) // if user id is array
				$this->db->where_in( 'user_id', $user_id );
			else
				$this->db->where( 'user_id', $user_id );
		}
		
		return $this->db->delete( 'broadcast_not_recipients' );
	}
	
	/**
	 * @method	add_to_not_recipient
	 * @access	public
	 * @desc	this method add a new row to not recipients
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @param	int						$user_id						- the id of the user
	 * @return 	boolean
	 */
	public function add_to_not_recipient( $broadcast_id = '', $user_id = '' )
	{
		if( empty( $broadcast_id ) || empty( $user_id ) )
			return FALSE;
			
		$this->db->where( 'broadcast_id', $broadcast_id );
		$this->db->where( 'user_id', $user_id );
		
		$query = $this->db->get( 'broadcast_not_recipients' );
		
		if( $query->num_rows() <= 0 )
		{
			$data = array();
			$data['broadcast_id']	= $broadcast_id;
			$data['user_id']		= $user_id;
			return $this->db->insert( 'broadcast_not_recipients', $data );
		}
		
		return TRUE;
	}
	
	/**
	 * @method	add_to_broadcast_clicks
	 * @access	public
	 * @desc	this method add a new row broadcast clicks
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @param	int						$user_id						- the id of the user
	 * @return 	boolean
	 */
	public function add_to_broadcast_clicks( $broadcast_id = '', $ip = '', $user_id = '' )
	{
		if( empty( $broadcast_id ) || empty( $ip ) )
			return FALSE;
			
		$this->db->where( 'broadcast_id', $broadcast_id );
		$this->db->where( 'ip', $ip );
        
        if($user_id != "")
            $this->db->where( 'user_id', $user_id);
		
		$query = $this->db->get( 'broadcast_clicks' );
		
		if( $query->num_rows() <= 0 )
		{
			$data = array();
			$data['broadcast_id']	= $broadcast_id;
            $data['ip']             = $ip;
			$data['user_id']		= $user_id;
			return $this->db->insert( 'broadcast_clicks', $data );
		}
		
		return TRUE;
	}
	
	/**
	 * @method	add_to_broadcast_unsubscribes
	 * @access	public
	 * @desc	this method add a new row broadcast clicks
	 * @author	Zoltan Jozsa
	 * @param	int						$broadcast_id					- the id of the broadcast
	 * @param	int						$user_id						- the id of the user
	 * @return 	boolean
	 */
	public function add_to_broadcast_unsubscribes( $broadcast_id = '', $user_id = '' )
	{
		if( empty( $broadcast_id ) || empty( $user_id ) )
			return FALSE;
			
		$this->db->where( 'broadcast_id', $broadcast_id );
		$this->db->where( 'user_id', $user_id );
		
		$query = $this->db->get( 'broadcast_unsubscribes' );
		
		if( $query->num_rows() <= 0 )
		{
			$data = array();
			$data['broadcast_id']	= $broadcast_id;
			$data['user_id']		= $user_id;
			return $this->db->insert( 'broadcast_unsubscribes', $data );
		}
		
		return TRUE;
	}
}
