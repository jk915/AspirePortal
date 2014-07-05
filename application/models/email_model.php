<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
* @property Tools_model $tools_model
*/

class Email_model extends CI_Model 
{
    function Email_model()
    {
        // Call the Model constructor
        parent::__construct();

    }

    function get_template($template_name)
    {
        $query = $this->db->get_where('email_template',array('email_template'=>$template_name),1);
        return $query->first_row();
    }
    
    function send_email($to_email, $template_name, $email_data,  $attach = "", $bcc = array())
    {
        $CI = &get_instance();
        $CI->load->library('email');
        $CI->load->library('parser');        
        
    	$config['mailtype']        = 'html';   
    	$CI->email->initialize($config);
        
        $email_settings = $this->get_template($template_name);

        if ($email_settings)
        {     
            $CI->email->clear(TRUE);

            $email_data['base_url'] = base_url();

            $email_body = $CI->parser->parse_string($email_settings->email_body, $email_data,true);
            $from_name = $email_settings->from_name;
            $from_email = $email_settings->from_email;
            $subject =  $CI->parser->parse_string($email_settings->email_subject, $email_data, true);
            
            $CI->email->from($from_email,$from_name);
            $CI->email->to($to_email);
            
            //set bcc
            if(!empty($bcc)) $CI->email->bcc($bcc);
                
            $CI->email->subject($subject);
            $CI->email->message($email_body);

            if($attach != "")
            	$CI->email->attach($attach);   
            
            return $CI->email->send();
        }
        else
        {
             $this->error_model->report_error("Sorry, email template $template_name could not be loaded.", "Email - send_email");
        }
    }
    
    /**
     * @method	send_broadcast
     * @access	public
     * @desc	this method send a broadcast email with specific parameters
     * @param 	int						$broadcast_id				- the id of the broadcast
     * @return 	boolean
     */
    public function send_broadcast( $broadcast_id = '', $email = '' )
    {
    	if( empty( $broadcast_id ) )
    		return FALSE;
    		
    	// load models, libraries
    	$this->load->model( 'broadcast_model' );
    	$this->load->library( 'email' );
    	$this->load->library( 'encrypt' );
    	
    	
    	$this->email->set_mailtype( 'html' );
            	
    	// load broadcast
    	$broadcast 			= $this->broadcast_model->get_details( $broadcast_id );
    	
    	// get the template
    	$template 			= $this->templates_model->get_details( $broadcast->broadcast_template_id );
    	
    	$body 				= ( !empty( $broadcast->html_content ) ? $broadcast->html_content : $broadcast->normal_content );
    	$content 			= '';
    	if( strpos( $template->content, '<html>' ) === FALSE )
    	{
    		// load template content from file
    		$content 		= $this->load->view( 'email/broadcast_content', NULL, TRUE );
    		$content 		= str_ireplace( '{{CONTENT}}', $template->content, $content );
    	}
    	else
    	{
    		$links = '<div style="clear: both;"></div>
    					<div style="clear: both;"></div>
						<div>							
							<p>To unsubscribe click this link: {{UNSUBSCRIBE_URL}}</p>
						</div>';
    		$content = $template->content;    		
    	}
    	
    	// insert body
    	$content 			= str_ireplace( '{{BODY}}', $body, $content );
    	                                                                                
    	// if $email is empty, we haven't set the email address which want to send this email 
    	if( empty( $email ) )
    	{
	    	// get all recipients
	    	$recipients = $this->broadcast_model->get_all_recipients( $broadcast_id );
	    	
	    	if( $recipients )
            {
                $original_content = $content;
	    		foreach( $recipients->result() as $recipient )
			    {
                    //replace anchors
                    $content = $this->_replace_anchors($original_content, $broadcast_id, $recipient->user_id);
                    
			    	// replace recipient data
			    	$content 	= str_ireplace( '{{FIRST_NAME}}', $recipient->first_name, $content );
			    	$content 	= str_ireplace( '{{LAST_NAME}}', $recipient->last_name, $content );
			    	$content 	= str_ireplace( '{{USER_NAME}}', $recipient->username, $content );
			    				    	
                    $link_code            = urlencode(base64_encode( $broadcast_id.';'.$recipient->user_id ));
			    	$unsubscribe_url 	= '<a href="'.base_url().'page/unsubscribe_broadcast/'.$link_code.'">Click here to Unsubscribe</a>';
			    				    	
			    	$content			= str_ireplace( '{{UNSUBSCRIBE_URL}}', $unsubscribe_url, $content );
			    	    
			    	 $this->email->from( $broadcast->from, $broadcast->from );
                     $this->email->to( $recipient->email );
			         $this->email->subject( $broadcast->subject );
			         $this->email->message( $content );
			
			         if ( strpos( base_url(), "localhost" ) === FALSE )
			            $this->email->send();
			    }                
            }
    	}
    	else
    	{
            $content = $this->_replace_anchors($content, $broadcast_id);
            
    		$link_code			= urlencode(base64_encode( $broadcast_id.';' ));
	    	
	    	$unsubscribe_url 	= '<a href="'.base_url().'page/unsubscribe_broadcast/'.$link_code.'">Click here to Unsubscribe</a>';
	    	$content			= str_ireplace( '{{UNSUBSCRIBE_URL}}', $unsubscribe_url, $content );
	             
	    	 $this->email->from( $broadcast->from, $broadcast->from );
	         $this->email->to( $email );             
	         $this->email->subject( $broadcast->subject );
	         $this->email->message( $content );
	
	         if ( strpos( base_url(), "localhost" ) === FALSE )
	            $this->email->send();
             
    	}
        
    	
    	return $content;
    }
    
    
    function _replace_anchors($content, $broadcast_id, $user_id = "")
    {
        //check for links and replace with ours as /postback/broadcast_click/[broadcast_id]/destination_url_base64encoded
        $regexp = "<a\s[^>]*href=(\"??)([^\" >]*?)\\1[^>]*>(.*)<\/a>";         
        $replace_with = base_url() . "postback/broadcast_click/". $broadcast_id ."/";
        
        if(preg_match_all("/$regexp/siU", $content, $matches, PREG_SET_ORDER)) 
        {
            foreach($matches as $match){
                
                $destination_link = $match[2];                
                $content = str_replace($destination_link, $replace_with . urlencode(base64_encode( $destination_link ."||". $user_id)) , $content);
         
            } 
        }
        return $content;
    }
}
?>
