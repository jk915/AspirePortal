<?php
/**
* The Notifications controller shows the main menu to the user.
*/
class Notifications extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	private $user_type_id;	// The user type of the logged in user.
	
	/**
	* @method Notifications (Constructor)
	* @version 1.0
	*/
	function __construct()
	{
		parent::__construct();

		// Load models etc
		$this->load->model("users_model");         
		$this->load->model("login_model"); 
		$this->load->model("notifications_model"); 
		
		// Create the data array
		$this->data = array();				
	}
   
	/**
	* @method index
	* @version 1.0
	*/   
	function index()
	{
	
		$this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Notifications";
        $this->data["page_heading"] = "Notifications";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
	
		$new_listing_email = $this->users_model->get_new_listing_advisor();
		
		$weekly_sales_report = $this->users_model->get_weekly_sales_advisor();
		$this->data['new_listing_email'] = $new_listing_email;
		$this->data['weekly_sales_report'] = $weekly_sales_report;
		// Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/notifications/prebody.php', $this->data); 
        $this->load->view('admin/notifications/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
	}
	
	function change_weekly_sales_report($user_id='')
    {
        
        $weekly_sales_report = $this->users_model->get_user_weekly_sales_advisor($user_id);
		$this->data['weekly_sales_report'] = $weekly_sales_report;
		
        $this->load->view('admin/notifications/frm_weekly_sales_report.php', $this->data);
    }
	
	function change_new_listing_email($user_id='')
	{
		$new_listing_email = $this->users_model->get_user_new_listing_advisor($user_id);
		$this->data['new_listing_email'] = $new_listing_email;
		
		$this->load->view('admin/notifications/frm_new_listing_email.php', $this->data);
	}
	
	function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        
		switch($type)
        {
            //delete property
            case 3:
				// Change weekly_sales_report Notification
				$user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false));
                $weekly_sales_report = trim($this->tools_model->get_value("weekly_sales_report",0,"post",0,false));
				
				if($user_id != '')
                {
                    $this->users_model->save($user_id, array('weekly_sales_report' => $weekly_sales_report));
					
					$return_data = array(
                                            'success' => 1,
                                        );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                                            'success' => 0
                                        );
                    echo json_encode($return_data);		
                }
			
			case 4:
				// Change new_listing_email Notification
				$user_id = intval($this->tools_model->get_value("user_id",0,"post",0,false));
                $new_listing_email = trim($this->tools_model->get_value("new_listing_email",0,"post",0,false));
				
				if($user_id != '')
                {
                    $this->users_model->save($user_id, array('new_listing_email' => $new_listing_email));
					
					$return_data = array(
                                            'success' => 1,
                                        );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                                            'success' => 0
                                        );
                    echo json_encode($return_data);		
                }	
			
			break;

			case 5:
				// Update Notifications
				$notification_id = intval($this->tools_model->get_value("notification_id",0,"post",0,false));
                $turn_on_off = trim($this->tools_model->get_value("turn_on_off",0,"post",0,false));
                $duration = trim($this->tools_model->get_value("duration",0,"post",0,false));
                $due_date = trim($this->tools_model->get_value("due_date",0,"post",0,false));
				$due_date = date("Y-m-d", strtotime($due_date));
				//update project id

                $data = array(
                    "turn_on_off" => $turn_on_off,
					"duration" => $duration,
					"due_date" => $due_date
                );
				
                $update = $this->notifications_model->save($notification_id, $data); 
				
				if($update)
                {
                    $return_data = array(
                                            'success' => 1,
                                        );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                                            'success' => 0
                                        );
                    echo json_encode($return_data);		
                }
				
			break;

			case 6:
				// Update Notifications
				$notification_id = intval($this->tools_model->get_value("notification_id",0,"post",0,false));
				if($notification_id == '1')
				{
					$new_listing = $this->notifications_model->get_details($notification_id);
					$notification_id = '2';
					$weekly_sales = $this->notifications_model->get_details($notification_id);
					
					$new_list_turn_on_off = $new_listing['0']->turn_on_off;
					
					$new_listing_notification_type = $new_listing['0']->notification_type;
					$due_date = $weekly_sales['0']->due_date;
					$due_date = date("d-m-Y", strtotime($due_date));
					if(($new_list_turn_on_off == 1) && ($new_listing_notification_type == 'new_listing_email')) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'html' => 'Notifiation '.$new_listing_notification_type.'  is currently ON Weekly Sales is the next to send on '.$due_date,
                	                   );
                    echo json_encode($return_data);
					} 
					else {
						$return_data = array(
                	                       'status' => 'FAILED',
										   'html' => 'Notifiation '.$notification_type.' listing is currently OFF',
                	                   );
						echo json_encode($return_data);
					}
                }
				else if($notification_id == '2')
				{
					$weekly_sales = $this->notifications_model->get_details($notification_id);
				
					$notification_id = '1';
					$new_listing = $this->notifications_model->get_details($notification_id);	
					
					$weekly_sales_turn_on_off = $weekly_sales['0']->turn_on_off;
					
					$weekly_sales_notification_type = $weekly_sales['0']->notification_type;
					$due_date = $new_listing['0']->due_date;
					$due_date = date("d-m-Y", strtotime($due_date));
					if(($weekly_sales_turn_on_off == 1) && ($weekly_sales_notification_type == 'weekly_sales_notification')) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'html' => 'Notifiation '.$weekly_sales_notification_type.' listing is currently ON New Listing Email is the next to send on '.$due_date,
                	                   );
                    echo json_encode($return_data);
					} 
					else {
						$return_data = array(
                	                       'status' => 'FAILED',
										   'html' => 'This Notifiation listing is currently OFF',
                	                   );
						echo json_encode($return_data);
					}
					
				}
				
			break;

			
		}
	}	
}	