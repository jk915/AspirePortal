<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
* @property Tools_model $tools_model
* @property property_model $property_model
*/

class Reservationmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;      
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("reservation_model");
        $this->load->model("property_model");
        $this->load->model("document_model");
        $this->load->library("utilities");    
                
        //if the $ci_session is passed in post, it means the swfupload has made the POST, don't check for login
        $ci_session = $this->tools_model->get_value("ci_session","","post",0,false);
      
        if ($ci_session == "")
        {
            // Check for a valid session
            if (!$this->login_model->getSessionData("logged_in"))            
                redirect("admin/login");       
        }               
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "Reservation Manager";
        $this->data["meta_description"] = "Reservation Manager";
        $this->data["meta_title"] = "Reservation Manager";
        $this->data["page_heading"] = "Reservation Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data["reservations"] = $this->reservation_model->get_list($this->records_per_page,1,$count_all);
        $this->data["pages_no"] = $count_all / $this->records_per_page;                                       
        
        $this->data["summary_table"] = $this->reservation_model->get_summary_table();        
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/reservationmanager/prebody', $this->data); 
        $this->load->view('admin/reservationmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    function history($sold = "")
    {
        // Define page variables
        $this->data["meta_keywords"] = "Reservation History";
        $this->data["meta_description"] = "Reservation History";
        $this->data["meta_title"] = "Reservation History";
        $this->data["page_heading"] = "Reservation History";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data["reservations"] = $this->reservation_model->get_list($this->records_per_page, 1, $count_all, $search_term = "", $search_period = "all", $from_date = "", $to_date = "", $sold = "");
        $this->data["pages_no"] = $count_all / $this->records_per_page;                                           
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/reservationmanager/prebody_history', $this->data); 
        $this->load->view('admin/reservationmanager/main_history', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 		
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        
        switch($type)
        {
            //delete reservations
            case 1:             
                
                //get reservation ids separated with ";"
                $reservation_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($reservation_ids!="")
                {
                    $arr_ids = explode(";",$reservation_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id))
                        {
                            if ($where_in != "") $where_in.=",";
                            
                            $where_in .= $id;
                        }
                    }
                    
                    if ($where_in!="")
                    {
                        $this->reservation_model->delete($where_in);
                    }
                }
                
                $this->refresh_reservation($current_page);
                
            break;
            
            //page number changed
            case 2:
            
                $this->refresh_reservation($current_page);
                
            break;
            
            //search for a project
            case 3:
               
                $current_page = 1;
                $this->refresh_reservation($current_page);
                                
            break; 
            
            //refresh summary table
            case 4:
                $return_data = array();
                
                $search_period = $this->tools_model->get_value("search_period","","post",0,false);
                $from_date = $this->tools_model->get_value("from_date","","post",0,false);
                $to_date = $this->tools_model->get_value("to_date","","post",0,false);
                
                $summary_table = $this->reservation_model->get_summary_table($search_period, $from_date, $to_date);        
                
                //load view
                $return_data["html"] = $this->load->view('admin/reservationmanager/summary_table', array('summary_table' => $summary_table), TRUE);                
                
                echo json_encode($return_data);     
            break;
            
            //Set property sold
            case 5:
            	// Get the id of the reservation that we want to flag as sold.
            	$reservation_id = $this->tools_model->get_value("reservation_id", "", "post", 0, false);
            	
            	if($reservation_id != "")
            	{
            		// Get the details of the reservation
            		$reservation = $this->reservation_model->get_details($reservation_id);
            		
            		if($reservation)
            		{
						// Set the sold flag, sold date, and sold by
						$admin_id = $this->login_model->getSessionData("id");

						$data = array();
						$data["sold_set_by_user_id "] = $admin_id;
						$data["sold"] = 1;
						$data["sold_date"] = date("y-m-d");
						
						$this->reservation_model->save($reservation_id, $data);
						
						// Also set the status of the related property to sold
				        $data = array(
				            "status"   => "sold"
				        );
				        
				        $this->property_model->save($reservation->property_id, $data);
            		}
				}
            	
				$this->refresh_reservation($current_page);   
            break;            
        }
    }
    
    function refresh_reservation($current_page)
    {
        $search_terms = $this->tools_model->get_value("user_search","","post",0,false);
        $search_period = $this->tools_model->get_value("search_period","","post",0,false);
        $from_date = $this->tools_model->get_value("from_date","","post",0,false);
        $to_date = $this->tools_model->get_value("to_date","","post",0,false);
                        
        //get list of reservations
        $reservations = $this->reservation_model->get_list($this->records_per_page, $current_page, $count_all, $search_terms, $search_period, $from_date, $to_date);                                      
        
        //load view 
        $this->load->view('admin/reservationmanager/reservation_listing', array('reservations'=>$reservations, 'pages_no' => $count_all / $this->records_per_page));                                
    }
}
?>