<?php
/**
* Email Manager
*/
class Emailmanager extends CI_Controller 
{
	
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
	function __construct()
	{
		parent::__construct();
        
		$this->load->model('email_template_model');
        $this->load->model( 'history_model' );
		$this->load->model( 'email_model' );
		$this->load->library('form_validation');
	}
	
	/**
	* @method index
	* @version 1.0
	*/   
	function index()
	{
		// Define page variables
		$this->data['meta_keywords'] 		= '';
		$this->data['meta_description'] 	= '';
		$this->data['meta_title'] 			= 'Email Manager';
		$this->data['page_heading'] 		= 'Email Manager';
		$this->data['name'] 				= $this->login_model->getSessionData("firstname");
		
		// get email templates		
		$this->data['email_templates'] 		= $this->email_template_model->get_list( $this->records_per_page, 1, $count_all );
		$this->data['pages_no'] 			= $count_all / $this->records_per_page;
		/*
		$arr 								= get_order_params( 'email_template' );
		$this->data["order_column"] 		= $arr['order_column'];
		$this->data["order_direction"] 		= $arr['order_direction'];*/
		
		// load admin page
		$this->load_page();
	}
	
	/**
	 * List the email template details
	 *
	 * @param int $email_template_id
	 */
	public function email_template( $email_template_id = '' )
	{
		$this->data['page_heading'] 	= 'Email Template Details';
		$this->data['message'] 			= '';
		
		// the template is edited
		if($email_template_id != '') 
		{
			$email_template		= $this->email_template_model->get_details($email_template_id);
			if( !$email_template )
			{
				show_error('Sorry, the email template could not be loaded.', "emailmanager/email_template - the email template with an id of '$email_template_id' could not be loaded");
				return;			
			}
			else
			{
				$this->data['email_template'] 	= $email_template;
			}
		}
		
		if( strtolower( $_SERVER['REQUEST_METHOD'] ) == 'post' )
			$this->_handle_post( $email_template_id );
		
		$this->data['meta_keywords'] 		= '';
		$this->data['meta_description'] 	= '';
		$this->data['meta_title'] 			= 'Email Template Menu';
		$this->data['email_template_id'] 	= $email_template_id;
		
		// Load views
		$this->load_email_template_page();
	}
	
	private function load_email_template_page()
	{
		// load header from global admin directory
		$this->load->view( 'admin/header', $this->data );
		
		//load module view
		$this->load->view( 'admin/email_template/prebody' );
		$this->load->view( 'admin/email_template/main' );
		
		// load footer from global admin directory
		$this->load->view( 'admin/pre_footer' );
		$this->load->view( 'admin/footer' );
	}
	
	public function send_test_mail()
	{
		$email_template_id 	= $this->input->post('current_template');
		$email_template 	= $this->input->post('email_template');
		$to_email_address	= $this->input->post('to_email_address');

		unset( $_POST['current_template'] );
		unset( $_POST['email_template'] );
		unset( $_POST['to_email_address'] );
		
		if( $email_template_id && $email_template && $to_email_address )
		{
			$email_data			= array();
			
			foreach( $_POST as $key => $val )
			{
				$email_data[$key] = $val;
			}
		
			$ret = $this->email_model->send_email($to_email_address, $email_template, $email_data);
			
			if ( $ret )
				$this->session->set_flashdata( 'success', 'Test email sent to '.$to_email_address);
			else
				$this->session->set_flashdata( 'error', 'An error occurred. Please try again.');		
		}
		else
		{
			$this->session->set_flashdata( 'error', 'An error occurred. Please try again.');
		}
	
		redirect( 'admin/emailmanager/email_template/'.$email_template_id );		
	}
	
	private function _handle_post( $email_template_id = '' )
	{
		if( !empty( $_POST ) )
		{
			/*if( $this->input->post('is_html') != '1' )
			{
				$_POST['email_body'] = strip_tags($_POST['email_body']);
			} */
			
			$this->session->set_flashdata( 'return_data', $_POST);
					
			$this->post_data 			= array(
											'email_template'        => '',
											'email_subject'			=> '',
											'from_name'				=> '',
				                            'from_email'   			=> '',
											'email_body'			=> '',
											//'is_html'               => '0'
										);
			
			$required_fields 		= array( 
											'email_template' 		=> '',
											'email_subject'			=> '',
											'from_name'				=> '',
				                            'from_email'   			=> ''
										);
			
            $missing_fields = false;
            
            //fill in data array from post values
            foreach($this->post_data as $key => $value)
            {
                $data[$key] = $this->tools_model->get_value($key, "", "post", 0, false);
                
                // Ensure that all required fields are present    
                if( in_array( $key, $required_fields ) && $data[$key] == "" )
                {
                    $missing_fields = true;
                    break;
                }
            }
            
            if ($missing_fields)
            {
                $this->session->set_flashdata( 'error', 'Sorry, please fill in all required fields to continue.' );
                redirect( ifvalue( $email_template_id, $email_template_id, '', 'emailmanager/email_template/' ) );
            }
            
			// check if the email template exists
			if( $this->email_template_model->get_list("", "", $count_all, $where="email_template = '". $this->input->post( 'email_template' ) ."' AND id !='".$email_template_id."'" ) )
			{
				$this->session->set_flashdata( 'error', 'This email template already exists' );
				redirect( ifvalue( $email_template_id, $email_template_id, '', 'emailmanager/email_template/' ) );
			}
			
			$email_template_id = $this->email_template_model->save( $data, $email_template_id );
            
			if( $email_template_id )
            {
            	
            	$history_data	= array(
									'foreign_id'		=> $email_template_id,
									'table'				=> 'email_template',
									'field'				=> 'email_body',
									'content'			=> $this->input->post( 'email_body' )
								);
				$this->history_model->save( $history_data );				
            }
			
			redirect( ifvalue( $email_template_id, $email_template_id, '', 'admin/emailmanager/email_template/' ) );
		}
	}
	
	public function ajaxwork()
	{
		$type 			= $this->input->post( 'type' );
		$current_page 	= $this->input->post( 'current_page' );
		
		switch($type)
		{
			#delete email templates
			case 1:
				$email_template_ids = $this->input->post( 'todelete' );
				
				if ($email_template_ids != '')
				{
					$arr_ids = explode( ";", $email_template_ids );
										
					if ( !empty($arr_ids) )
					{
						$this->email_template_model->delete( $arr_ids );
					}
				}        				                         
        
				$offset 							= ($current_page - 1) * $this->records_per_page;				
				$email_templates 					= $this->email_template_model->get_list( $this->records_per_page, $offset, $count_all );
				
				$return_data['email_templates'] 	= $email_templates;
				$return_data['pages_no'] 			= $count_all / $this->records_per_page;
				
				//load view 
				$this->load->view('admin/emailmanager/email_template_listing', $return_data);
				return;
			break;
			
			#page number changed
			case 2:
				//get list of pages
				$offset 						= ( $current_page - 1 ) * $this->records_per_page;				
				$email_templates 				= $this->email_template_model->get_list( $this->records_per_page, $offset, $count_all );
								
				$return_data['email_templates'] = $email_templates;
				$return_data['pages_no'] 		= $count_all / $this->records_per_page;
				
				//load view 
				$this->load->view( 'admin/emailmanager/email_template_listing', $return_data );
				return;
			break;
			   
			#get test mail content
			case 4;
				$email_template_id 		= $this->input->post('current_template');
				$email_settings 		= $this->email_template_model->get_details($email_template_id); 
               
				$data 					= array();
				$data['email_settings'] = $email_settings;
			              
                $return_data['html'] 	=  $this->load->view('admin/email_template/email_template_test', $data, TRUE);
			break;
			
			case 10:
				
				//get list of pages
				$current_page 					= 1;
				$offset 						= ($current_page - 1) * $this->records_per_page;
				$params 						= array( 'limit'=>$this->records_per_page, 'limit_from' => $offset );
				$email_templates 				= $this->email_template_model->get_list( $params );
				$count_all 						= $this->email_template_model->count_all( $params );
				
				$return_data['email_templates'] = $email_templates;
				$return_data['pages_no'] 		= $count_all / $this->records_per_page;
				
				//load view 
				$this->load->view('emailmanager/email_template_listing',$return_data);
				return;
				
			break;
		}
		
		// return respons
		echo json_encode($return_data);
        exit();
	}
    
    /**
     * @method    load_page
     * @access    public
     * @desc    this method loads the main page for some module
     * @author
     * 
     * @version    1.0
     * @return
     */
    function load_page()
    {           
        // Load views
        $this->load->view( 'admin/header', $this->data );
        
        //load module view
        $this->load->view( 'admin/emailmanager/prebody', $this->data ); 
        $this->load->view( 'admin/emailmanager/main', $this->data );
        
        $this->load->view( 'admin/pre_footer', $this->data ); 
        $this->load->view( 'admin/footer', $this->data ); 
    }
	
}
