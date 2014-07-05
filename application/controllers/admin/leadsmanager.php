<?php  if (!defined('BASEPATH')) exit('No direct script access allowed');

/**

* leadsmanager

* Handles listing, editing and adding system users.

*/

class Leadsmanager extends CI_Controller 

{

    public $data;        // Will be an array used to hold data to pass to the views.

    private $records_per_page = ITEMS_PER_PAGE;


    function __construct()
    {
        parent::__construct();
        // Create the data array.
        $this->data = array();            

        // Load models etc    
        $this->load->model("leads_model");
        $this->load->model("users_model");
        $this->load->helper("form");
    }

   

    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Leads Manager";
        $this->data["page_heading"] = "Leads Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        $this->data["leads"] = $this->leads_model->get_all_leads($this->records_per_page,1,$count_all);
        $this->data["pages_no"] = $count_all / $this->records_per_page;

        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/leadsmanager/prebody', $this->data); 
        $this->load->view('admin/leadsmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }    

   

	/**
	* @desc: The lead method shows a lead with the specified lead id.
	* @param integer $lead_id - The lead id of the lead to load.
	*/

	function lead($lead_id = "")
	{
		$this->data["page_heading"] = "Lead Details";
		$this->data['message'] = "";

		$form_values = array();
		$form_values["first_name"] = "";
		$form_values["last_name"] = "";
		$form_values["email"] = "";

		// Check for a user postback
		$postback = $this->tools_model->isPost();        

		// If there was a postback, handle it.    
		if ($postback)
			$this->_handlePost($lead_id, $form_values);

		if($lead_id != "") //edit
		{
			// Load lead details
			$lead = $this->leads_model->get_details($lead_id);
			if(!$lead)
			{
				// The page could not be loaded.  Report and log the error.
				$this->error_model->report_error("Sorry, the lead could not be loaded.", "Usermanager/user - the user with an id of '$lead_id' could not be loaded");
				return;            
			}

			// Set the form_values fields only if the user has NOT posted the form back
			if(!$postback)
			{
				$form_values["first_name"] = $lead->first_name;
				$form_values["last_name"] = $lead->last_name;
				$form_values["email"] = $lead->email;
			}

			// pass the user object to the view
			$this->data["lead"] = $lead;
            $this->data["agent"] = $this->users_model->get_details($lead->agent_id);
		}
        else
        {
            redirect(site_url('admin/leadsmanager'));
            exit();
        }
            
        
		$this->data["form_values"] = $form_values;
		$this->data['message'] = ($lead_id == "") ? "To create a new lead, enter the lead details below." : "You are editing the lead &lsquo;<b>$lead->first_name $lead->last_name</b>&rsquo;";
		$this->data["states"] = $this->tools_model->get_states();
		$this->data["websites"] = $this->website_model->get_list(array());

		// Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "Lead Manager - Lead Details";
		$this->data['lead_id'] = $lead_id;


		// Load views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/lead/prebody.php', $this->data); 
		$this->load->view('admin/lead/main.php', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data);      
	}

    

   /***

   * @method _handlePost

   * @author Andrew Chapman

   * @version 1.0

   * 

   * _handlePost is called after the lead posts the lead details form back to the server.

   * It will update an existing lead

   * 

   * @param integer $lead_id  The lead id being updated

   * @param mixed $form_values   The form values associative array - used to pass details back to the view.

   */

    function _handlePost($lead_id, &$form_values)
    {
      // The user has submitted the form back.
      // Is the form valid?
      $this->load->library('form_validation');
      $data = array(
            "first_name" 			        => '',
            "last_name" 					=> '',
            "company_name" 			        => '',
            "acn" 						    => '0',
            "phone" 						=> '',
            "mobile" 						=> '',
            "fax" 						    => '',
        	"email"		                    => '',
      		"address1"	                    => '',
      		"address2"					    => '',
      		"suburb"		                => '',
      		'postcode'						=> '',
            'state'						    => '1',
            'country'						=> '1',
            'status'						=> 'Open'
      );

       
      $this->form_validation->set_rules('status', 'Status', 'required');
      $this->form_validation->set_rules('country', 'Country', 'required|integer');
      $this->form_validation->set_rules('state', 'State', 'required|integer');
      $this->form_validation->set_rules('first_name', 'First Name', 'required|trim|xss_clean');
      $this->form_validation->set_rules('last_name', 'Last Name', 'required|trim|xss_clean');
      $this->form_validation->set_rules('email', 'Email', 'required|valid_email|trim');      

      if ($this->form_validation->run() == FALSE)
      {
         // Form validation failed
         $this->data["warning"] = validation_errors();

         // Pass any values that the user typed in back to the view.
         $form_values["first_name"] 				= $_POST["first_name"];
         $form_values["last_name"] 					= $_POST["last_name"];
         $form_values["email"] 						= $_POST["email"];              
      }
      else
      {
        // The form has validated OK.
        // Prepare the model data array to pass to the view.
        $model_data = array();
        $required_fields = array();
        $missing_fields = false;
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $model_data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
            // Ensure that all required fields are present    
            if(in_array($key,$required_fields) && $data[$key] == "")
            {
                $missing_fields = true;
                break;
            }
        }

        if ($missing_fields)
        {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "SettingsManager/HandlerPost update");
            return;
        }

         $model_data["first_name"] 		=  $_POST["first_name"];
         $model_data["last_name"] 		=  $_POST["last_name"];
         $model_data["email"] 			=  $_POST["email"];
         
         $ok = true;
         // Everything OK to proceed with the update/insert?

         if($ok)
         {
            $lead_id = $this->leads_model->save($lead_id, $model_data);
            if(!$lead_id)
            {
               // Something went wrong whilst saving the user data.
               $this->error_model->report_error("Sorry, the lead could not be saved/updated.", "Leadsmanager/Lead save - the lead with an id of '$lead_id' could not be saved");
               return;
            }

            // The lead was saved OK.  Redirect back to the listing screen
            redirect("/admin/leadsmanager/lead/".$lead_id);
            exit();
         }
      }            
    }
    

    //handles all ajax requests within this page

    function ajaxwork()

    {

        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));

        
        switch($type)
        {
            // 1 - Delete leads
            case 1:

                //get page ids separated with ";"

                $lead_ids = $this->tools_model->get_value("todelete","","post",0,false);

                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);

                $name_type = $this->tools_model->get_value("name_type","","post",0,false);

                
                if ($lead_ids!="")
                {

                    $arr_ids = explode(";",$lead_ids);

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
                        $this->leads_model->delete($where_in);
                    }
                }

                //get list of leads
                $leads = $this->leads_model->get_all_leads($this->records_per_page,$current_page,$count_all,$search_terms, $name_type);

                //load view 
                $this->load->view('admin/leadsmanager/lead_listing',array('leads'=>$leads,'pages_no' => $count_all / $this->records_per_page));

            break;

            // 2 - Page number changed
            case 2:

                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);

                $name_type = $this->tools_model->get_value("name_type","","post",0,false);

                // Get list of leads

                $leads = $this->leads_model->get_all_leads($this->records_per_page,$current_page,$count_all,$search_terms, $name_type);

                // Load view

                $this->load->view('admin/leadsmanager/lead_listing',array('leads'=>$leads,'pages_no' => $count_all / $this->records_per_page));

            break;

            // 3 - Search for leads
            case 3:

                $search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                
                $name_type = $this->tools_model->get_value("name_type","","post",0,false);

                // Get list of leads

                $leads = $this->leads_model->get_all_leads($this->records_per_page,$current_page,$count_all,$search_terms, $name_type);


                // Load view

                $this->load->view('admin/leadsmanager/lead_listing',array('leads'=>$leads,'pages_no' => $count_all / $this->records_per_page));

            break;
            
             // 4 - Load states
            case 4:
                $country_id = $this->input->post( 'country_id' );
                $states = $this->leads_model->get_states($country_id);
                $return_data['html'] = $this->utilities->print_select_options($states, 'state_id', 'name');
                echo json_encode( $return_data );
            break;

        }
    }

}
