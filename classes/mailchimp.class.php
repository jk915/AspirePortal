<?php
require_once("classes/MCAPI.class.php"); 

define("MCERROR_ALREADYSUBSCRIBED", 214);  

class MailChimp
{
	private $api_key;
	private $list_id;
	private $apiUrl;
	
	public function MailChimp($api_key, $list_id)
	{
		$this->apiUrl = 'http://api.mailchimp.com/1.3/';	
		$this->api_key = $api_key;
		$this->list_id = $list_id;
		
		if($this->api_key == "")
			die("MailChimp::Constructor - No API Key defined");
			
		if($this->list_id == "")
			die("MailChimp::Constructor - No list ID provided");			
	}
	
	public function sendToMailChimp($email, $data, &$error_code = "")
	{
		if($email == "")
			die("MailChimp::sendToMailChimp - No email address provided");
			
		if(!is_array($data))
			die("MailChimp::sendToMailChimp - Invalid data array");						
		
		$api = new MCAPI($this->api_key); 
		
		// Send the data to Mailchimp.
		$retval = $api->listSubscribe($this->list_id, $email, $data, "html", false, true);

		if ($api->errorCode)
		{
			$error_code = $api->errorCode;
			return false;
		} 
		else 
		{
   		return true;
		}		
	}
}
