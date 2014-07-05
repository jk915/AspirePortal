<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');
/**
* Broadcast manager
* Handles listing, editing and adding broadcasts.
*/
class Broadcastmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc    
        $this->load->model( 'broadcast_model' );
        $this->load->model("users_model");    
        $this->load->model("email_model");
        $this->load->model("login_model");
        $this->load->model("website_model");
        $this->load->library("utilities");                
      
      //if the $ci_session is passed in post, it means the swfupload has made the POST, don't check for login
        $ci_session = $this->tools_model->get_value("ci_session","","post",0,false);
      
        if ($ci_session == "")
        {
            // Check for a valid session
            if (!$this->login_model->getSessionData("logged_in"))            
                redirect("login");       
        }
    }
   
   /**
	* @method index
	* @author Zoltan Jozsa
	* @version 1.0
	*/    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"]		= "";
        $this->data["meta_description"] 	= "";
        $this->data["meta_title"] 			= "Broadcast Manager";
        $this->data["page_heading"] 		= "Broadcast Manager";
        $this->data["name"] 				= $this->login_model->getSessionData("firstname");
        $this->data['broadcast_statuses']	= $this->broadcast_model->get_all_statuses();
        $params								= array();
        $params['order_by']					= 'broadcasts.insert_date';
        $params['order_direction']			= 'desc';
        $params['limit']					= $this->records_per_page;
        $this->data['broadcasts']			= $this->broadcast_model->get_many_by( $params );
        $count_all 							= $this->broadcast_model->get_many_by( array(), TRUE );
        $this->data["pages_no"] 			= $count_all / $this->records_per_page;
        $this->data["user_types"] 			= $this->users_model->get_user_types();
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/broadcastmanager/prebody', $this->data); 
        $this->load->view('admin/broadcastmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }    
    
    /**
     * @method	broadcast
     * @access	public
     * @desc	this method is called when user wants to add or edit a broadcast
     * @author	Zoltan Jozsa
     * @param 	int						$broadcast_id					- the broadcast id to edit
     * @return 
     */
    function broadcast( $broadcast_id = '' )
    {
    	$this->data["page_heading"] = "Broadcast Details";
		$this->data['message'] = "";
			
		if( !empty( $broadcast_id ) && is_numeric( $broadcast_id ) ) // edit
		{
			$broadcast = $this->broadcast_model->get_details( $broadcast_id );
			
			if( $broadcast )
			{
				
			}
			else 
			{
				// The page could not be loaded.  Report and log the error.
				$this->error_model->report_error("Sorry, the broadcast could not be loaded.", "Broadcast/broadcast - the broadcast with an id of '$broadcast_id' could not be loaded");
				return;		
			}
		}
		
		if( !empty( $_POST ) )
			$this->_handlePost( $broadcast_id );
		
		if ( empty( $_POST ) )	
			$this->data['message'] = ($broadcast_id == "") ? "To create a new broadcast, enter the broadcast details below." : "You are editing the broadcast &lsquo;<b>$broadcast->name</b>&rsquo;";
			
		// Define page variables
		$this->data["meta_keywords"] 				= "";
		$this->data["meta_description"] 			= "";
		$this->data["meta_title"] 					= "Broadcast Details";
		$this->data['broadcast_id'] 				= $broadcast_id;
		$this->data['broadcast']					= $this->broadcast_model->get_details( $broadcast_id );
		$this->data['broadcast_templates']			= $this->broadcast_model->get_all_templates();
		$this->data['broadcast_access_levels_to']	= $this->broadcast_model->get_all_access_levels_to();
		//if( $this->data['broadcast']->broadcast_status_id != BROADCAST_STATUS_SENT_ID )
		$this->data['recipients']					= $this->broadcast_model->get_all_recipients( $broadcast_id, $this->records_per_page );
		
		$this->data['not_recipients']				= $this->broadcast_model->get_all_not_recipients( $broadcast_id );
		
		if( !empty( $broadcast_id ) && $this->data['broadcast']->broadcast_status_id == BROADCAST_STATUS_SENT_ID )
		{
			$this->data['broadcast_clicks']				= $this->broadcast_model->get_all_broadcast_user_clicks( $broadcast_id );
			$this->data['broadcast_unsubscribes']		= $this->broadcast_model->get_all_broadcast_unsubscribed_users( $broadcast_id );
		}
		$count_all									= $this->broadcast_model->get_all_recipients( $broadcast_id, $this->records_per_page, TRUE );
		$this->data['pages_no']						= $count_all / $this->records_per_page;
		
		// Load views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/broadcast/prebody.php', $this->data); 
		$this->load->view('admin/broadcast/main.php', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data);  		
			
    }
    
    /**
     * @method	_handlePost
     * @access	private
     * @desc	this method saves data for a new broadcast or for an existing
     * @author	Zoltan Jozsa
     * @param	int						$broadcast_id			- the id of the broadcast
     * @return 
     */
    function _handlePost( $broadcast_id = '' )
    {
    	$data = array(
    			'name'						=> '',
    			'broadcast_template_id' 	=> '',
    			'subject'					=> '',
    			'from'						=> '',
    			'send_to'					=> '',
    			'send_to_access_level_id'	=> '',
    			'html_content'				=> '',
    			'normal_content'			=> ''
    	);
    	
    	$required_fields = array( 'name', 'subject', 'from', 'send_to' );
    	$missing_fields = false;
			
		//fill in data array from post values
		foreach($data as $key => $value)
		{
			$data[$key] = $this->tools_model->get_value($key, "", "post", 0, false);
			
			// Ensure that all required fields are present	
			if( in_array( $key, $required_fields ) && $data[$key] == "" )
			{
				$missing_fields = true;
				break;
			}
		}
		
		if( $data['send_to'] == 'Access Level' && empty( $data['send_to_access_level_id'] ) )
			$missing_fields = true;
		
		if ($missing_fields)
		{
			$this->data['warning'] = 'Sorry, please fill in all required fields to continue.';
			//$this->error_model->report_error("Sorry, please fill in all required fields to continue.", "BroadcastManager/HandlerPost update - the broadcast with a code of '$broadcast_id' could not be saved");
			return;
		}
		
		//depeding on the $page_id do the update or insert
		$broadcast_id = $this->broadcast_model->save( $broadcast_id, $data );
		if( !$broadcast_id )
        {
        	// Something went wrong whilst saving the user data.
        	$this->data['warning'] = 'Sorry, the broadcast could not be saved/updated.';
            //$this->error_model->report_error("Sorry, the broadcast could not be saved/updated.", "BroadcastManage/Broadcast save");
            return;
        }
	            
        //save the page content in history 
        //$this->history_model->save_history($table = "custom_blocks", $broadcast_id);
			
		redirect("/broadcastmanager/broadcast/$broadcast_id");
		exit;
    }
    
    /**
     * @method	view
     * @access	public
     * @desc	this method shows the sent broadcast by broadcast and user id
     * @author	Zoltan Jozsa
     * @param	int						$broadcast_id			- the id of the broadcast
     * @param	int						$user_id				- the id of the user
     * @return 
     */
    function view( $broadcast_id = '', $user_id = '' )
    {
    	// load models
    	$this->load->model( 'templates_model' );
    	$this->load->model( 'users_model' );
    	
    	$this->load->library( 'encrypt' );
    	
    	// load broadcast
    	$broadcast = $this->broadcast_model->get_details( $broadcast_id );
    	if( !$broadcast )
    	{
    		// The page could not be loaded.  Report and log the error.
			$this->error_model->report_error("Sorry, the broadcast could not be loaded.", "Broadcast/view - the broadcast with an id of '$broadcast_id' could not be loaded");
			return;	
    	}
    	// get the template
    	$template = $this->templates_model->get_details( $broadcast->broadcast_template_id );
    	if( !$template )
    	{
    		// The page could not be loaded.  Report and log the error.
			$this->error_model->report_error("Sorry, the broadcast template could not be loaded.", "Broadcast/view - the broadcast template with an id of '$broadcast->broadcast_template_id' could not be loaded");
			return;	
    	}
    	// get user
    	$user = $this->users_model->get_details( $user_id );
    	if( !$user )
    	{
    		// The page could not be loaded.  Report and log the error.
			$this->error_model->report_error("Sorry, the user could not be loaded.", "Broadcast/view - the user with an id of '$user_id' could not be loaded");
			return;	
    	}
    	
    	$body = ( !empty( $broadcast->html_content ) ? $broadcast->html_content : $broadcast->normal_content );
    	$content = '';
    	if( strpos( $template->content, '<html>' ) === FALSE )
    	{
    		// load template content from file
    		$content = $this->load->view( 'email/broadcast_content_no_links', NULL, TRUE );
    		$content = str_ireplace( '{{CONTENT}}', $template->content, $content );
    	}
    	else
    	{
    		$content = $template->content;
    		if( stripos( $content, '{{VIEW_URL}}' ) === FALSE )
    			$content = $template->content;
    	}
    	
    	// insert body
    	$content 			= str_ireplace( '{{BODY}}', $body, $content );
    	
	    // replace recipient data
	    $content 			= str_ireplace( '{{FIRST_NAME}}', $user->first_name, $content );
	    $content 			= str_ireplace( '{{LAST_NAME}}', $user->last_name, $content );
	    $content 			= str_ireplace( '{{USER_NAME}}', $user->username, $content );
    	
    	print $content;
    	
    	exit;
    }
    
    /**
     * @method	send
     * @access	public
     * @desc	this method sends a broadcast
     * @author	Zoltan Jozsa
     * @param	int						$broadcast_id			- the id of the broadcast
     * @return 
     */
    function send( $broadcast_id = '' )
    {
    	// load model
    	$this->load->model( 'templates_model' );
    	$this->load->model( 'email_model' );
    	
    	$broadcast = $this->broadcast_model->get_details( $broadcast_id );
    	
    	if( empty( $broadcast_id ) || !$broadcast )
    	{
    		$this->session->set_flashdata( 'warning', "Sorry, this broadcast doesn't exists" );
    		redirect( 'broadcastmanager' );
    		exit;
    	}
    	
    	// get the template
    	$template = $this->templates_model->get_details( $broadcast->broadcast_template_id );
    	if( !$template )
    	{
    		$this->session->set_flashdata('warning', "Sorry, this template doesn't exists" );
    		redirect( 'broadcastmanager/broadcast/'.$broadcast_id );
    		exit;
    	}
    	
    	if( $broadcast->broadcast_status_id == BROADCAST_STATUS_SENT_ID )
    	{
    		$this->session->set_flashdata('warning', "Sorry, broadcast already sent" );
    		redirect( 'broadcastmanager/broadcast/'.$broadcast_id );
    		exit;
    	}
    	
    	// send broadcast
    	$this->email_model->send_broadcast( $broadcast_id );
    	
    	// update broadcast to sent
    	$data = array( 'broadcast_status_id' => BROADCAST_STATUS_SENT_ID );
    	if( $this->broadcast_model->save( $broadcast_id, $data ) )
    	{
    		$this->session->set_flashdata( 'warning', 'The broadcast has been sent.' );
    	}
    	else
    	{
    		$this->session->set_flashdata( 'warning', 'Error while sending broadcast, please try again.' );
    	}
    	
    	redirect( 'broadcastmanager/broadcast/'.$broadcast_id );
    }
    
    /**
     * @method	ajaxwork
     * @access	public
     * @desc	this method help us to refresh a broadcast list or others
     * @author	Zoltan Jozsa
     * @return 
     */
    function ajaxwork()
    {
    	$type = intval($this->tools_model->get_value("type",0,"post",0,false));
		$current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
		
		switch($type)
		{
			case 1: // delete broadcast
				//get broadcast ids separated with ";"
				$broadcast_ids = $this->tools_model->get_value("todelete","","post",0,false);
				
				if ( $broadcast_ids != "" )
				{
					$arr_ids = explode( ";",$broadcast_ids );
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
						$this->broadcast_model->delete($where_in);
					}
				}
				
				//get list of blocks
				$params 				= array();
				$params['limit']		= $this->records_per_page;
				$params['limit_from']	= ( $current_page - 1 ) * $this->records_per_page;
				$data['broadcasts'] 	= $this->broadcast_model->get_many_by( $params );
				$data['pages_no'] 		= $this->broadcast_model->get_many_by( array(), TRUE ) / $this->records_per_page;
				//load view 
				$this->load->view('admin/broadcastmanager/broadcast_listing', $data );
			break;
			
			case 2: // refresh list when repicient category select changed
				$level_id 									= $this->tools_model->get_value( 'level_id', '', 'post', 0, false );
				$broadcast_id 								= $this->tools_model->get_value( 'broadcast_id', '', 'post', 0, false );
				$text										= $this->tools_model->get_value( 'search', '', 'post', 0, false );
				
				if( !is_numeric( $broadcast_id ) )
				{
					print 'Some error occured, please try again';
					exit;
				}
				
				if( !is_numeric( $current_page ) || $current_page <= 0 )
					$current_page = 1;

				if( $text )
					$text = explode( ' ', $text );
					
				$this->data['broadcast_id'] 				= $broadcast_id;
				$this->data['broadcast']					= $this->broadcast_model->get_details( $broadcast_id );
				$this->data['broadcast_templates']			= $this->broadcast_model->get_all_templates();
				$this->data['broadcast_access_levels_to']	= $this->broadcast_model->get_all_access_levels_to();
				if( $this->data['broadcast']->broadcast_status_id != BROADCAST_STATUS_SENT_ID )
				{
					$view = 'recipient_listing';
				}
				else
				{
					$view = 'delivery_listing';
					$this->data['broadcast_clicks']			= $this->broadcast_model->get_all_broadcast_user_clicks( $broadcast_id );
					$this->data['broadcast_unsubscribes']	= $this->broadcast_model->get_all_broadcast_unsubscribed_users( $broadcast_id );
				}
				$parameters								= array();
				$parameters['broadcast_id']				= $broadcast_id;
				$parameters['where']					= array(
															'subscribed'	=> '1'
														);
				if( $level_id )
					$parameters['where']['broadcast_access_level_id'] = $level_id;
				
				if( $text )
				{
					$parameters['or_like']				= array();
					foreach ( $text as $search )
					{
						$parameters['or_like']['first_name'] 	= $search;
						$parameters['or_like']['last_name'] 	= $search;
						$parameters['or_like']['username'] 		= $search;
						$parameters['or_like']['email'] 		= $search;
					}
				}	
				$parameters['limit']					= $this->records_per_page;
				$parameters['limit_from']				= ( $current_page - 1 ) * $this->records_per_page;
				// get recipients
				$this->data['recipients']				= $this->broadcast_model->get_recipients_many_by( $parameters );
				
				$count_all								= $this->broadcast_model->get_recipients_many_by( $parameters, TRUE );
				
				$this->data['pages_no']					= $count_all / $this->records_per_page;
				$this->data['not_recipients']				= $this->broadcast_model->get_all_not_recipients( $broadcast_id );
		
				print $this->load->view( 'admin/broadcast/'.$view, $this->data, TRUE );
			break;
			
			case 3: // filter for status
				//get status to view
				$status_id = $this->tools_model->get_value("status_id",'',"post",0,false);
				
				$params 					= array();
				if( !empty( $status_id ) && $status_id != 0 )
					$params[ 'where' ]		= array( 'nc_broadcasts.broadcast_status_id' => $status_id );
					
				$params['limit']			= $this->records_per_page;
				$params['order_by']			= 'broadcasts.insert_date';
        		$params['order_direction']	= 'desc';
				$params['limit_from']		= ( $current_page - 1 ) * $this->records_per_page;
				$data['broadcasts'] 		= $this->broadcast_model->get_many_by( $params );
				$data['pages_no'] 			= $this->broadcast_model->get_many_by( $params, TRUE ) / $this->records_per_page;
				//load view 
				$this->load->view('admin/broadcastmanager/broadcast_listing', $data );
			break;
			
			case 4: // Add new recipient, delete from not recipient table
				$user_id 		= $this->tools_model->get_value("user_id",'',"post",0,false);
				$broadcast_id 	= $this->tools_model->get_value("broadcast_id",'',"post",0,false);
				
				if( !empty( $user_id ) && !empty( $broadcast_id ) && is_logged_in() )
				{
					$this->broadcast_model->delete_from_not_recipient( $broadcast_id, $user_id );
				}
			break;
			
			case 5: // Delete a recipient, add to not recipient table
				$user_id 		= $this->tools_model->get_value("user_id",'',"post",0,false);
				$broadcast_id 	= $this->tools_model->get_value("broadcast_id",'',"post",0,false);
				
				if( !empty( $user_id ) && !empty( $broadcast_id ) && is_logged_in() )
				{
					$this->broadcast_model->add_to_not_recipient( $broadcast_id, $user_id );
				}
			break;
			
			case 6: // send preview
				// load model
		    	$this->load->model( 'templates_model' );
		    	$this->load->model( 'email_model' );
                $this->load->library('form_validation');
				
                $this->form_validation->set_rules('email', 'email', 'required|email|xss_clean|trim');
                
                if ($this->form_validation->run() == FALSE)
                {
                    print 'Sorry this email address is not valid.';
                    exit;
                }
                
                $broadcast_id     = $this->tools_model->get_value("broadcast_id", '', "post", 0, false);
                $email            = $this->tools_model->get_value("email", '', "post", 0, false);
				
				$broadcast = $this->broadcast_model->get_details( $broadcast_id );
		    	if( empty( $broadcast_id ) || !$broadcast )
		    	{
		    		print "Sorry, this broadcast doesn't exists";
		    		exit;
		    	}
		    	
		    	// get the template
		    	$template = $this->templates_model->get_details( $broadcast->broadcast_template_id );
		    	if( !$template )
		    	{
		    		print "Sorry, this template doesn't exists";
		    		exit;
		    	}
		    	
		    	/*if( $broadcast->broadcast_status_id == BROADCAST_STATUS_SENT_ID )
		    	{
		    		$this->session->set_flashdata('warning', "Sorry, broadcast already sent" );
		    		redirect( 'broadcastmanager/broadcast/'.$broadcast_id );
		    		exit;
		    	}*/
		    	
		    	if( empty( $email ) ) $email = CONTACT_EMAIL;		    	
		    	
		    	// send broadcast
		    	if( $this->email_model->send_broadcast( $broadcast_id, $email ) )
		    	{
		    		print 'Email has been sent.';
		    		exit;
		    	}
		    	else
		    	{
		    		print 'Error while sending broadcast, please try again';
		    		exit;
		    	}
			break;
		}
    }        
}
