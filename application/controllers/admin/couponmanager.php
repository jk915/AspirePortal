<?php
class Couponmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
    function __construct()
    {
        parent::__construct();
      
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        //$this->load->model("website_model");            
        $this->load->model("coupon_model");
        $this->load->model( 'product_category_model' );
        $this->load->model( 'product_model' );           
      
        $this->load->library("utilities");    
     
        // Check for a valid session
        if (!$this->login_model->getSessionData("logged_in"))
           redirect("login");       
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Coupon Manager";
        $this->data["page_heading"] = "Coupon Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        $this->data["coupons"] = $this->coupon_model->get_list( $this->records_per_page, 1, $count_all );
       
        $this->data["pages_no"] = $count_all / $this->records_per_page;
      
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/couponmanager/prebody', $this->data); 
        $this->load->view('admin/couponmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
  
    /**
    * @method: coupon
    * @desc: The website method shows a coupon with the specified coupon id.
    * If no id code is given, it means it is a new website
    * 
    * @param mixed $coupon_id - The coupon id of the page to load.
    */
    function coupon($coupon_id = "")
    {
        $this->data["page_heading"] = "Coupon Details";
        $this->data['message'] = "";
      
        $postback = $this->tools_model->isPost();
            
        if ($postback)
        {
            $this->_handlePost($coupon_id,$missing_fields);
        }
        
        if($coupon_id != "") //edit
        {      
            // Load coupon details
            $coupon = $this->coupon_model->get_details($coupon_id);
            if(!$coupon)
            {
                // The coupon could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the coupon could not be loaded.", "Coupon/show - the coupon with a code of '$coupon_id' could not be loaded");
                return;            
            }
            else
            {
                //pass page details
                $this->data["coupon"] = $coupon;
                
                //get buy products
                $this->data["buy_products"] = $this->coupon_model->get_list_coupon_discount_products($coupon_id, 'buy');
                $this->data["reward_products"] = $this->coupon_model->get_list_coupon_discount_products($coupon_id, 'reward');
            } 
        }
       
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data['coupon_id'] = $coupon_id;
        $this->data["robots"] = $this->utilities->get_robots();
        $count_all = 0;
        //$this->data['websites'] = $this->website_model->get_list(true,"","",$count_all);
        $this->data['categories']	= $this->product_category_model->get_list( '', '', '', TRUE );
        $this->data['products']		= $this->product_model->get_list( TRUE, ( !empty( $coupon ) ? $coupon->product_category_id : '-1' ) );
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/coupon/prebody.php', $this->data); 
        $this->load->view('admin/coupon/main.php', $this->data);        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);              
    }
    
    function _handlePost($coupon_id,&$form_values)
    {
		//$this->tools_model->printArray($_POST);  die();
    	
        $data = array(  
                        "coupon_code"        => '',
                        "start_date"         => '',
                        "finish_date"      	 => '',
                        "website_id"         => '1',
                        "discount"           => '',
        				'discount_type'		 => 'percentage',
                        'coupon_code_required' => '0',
                        'use_multiple_times'   => '0'
        				/*'username'			 => '',
        				'product_category_id'=> '-1',
        				'product_id'		 => '-1',
        				'use_max'			 => '',
        				'minimum_order'		 => '',
        				'message'			 => ''*/
                    );
            $required_fields = array("coupon_code","start_date","finish_date","discount_type");
            $type = $this->input->post("discount_type");
            
            if( $type != "products")
                array_push($required_fields, "discount");
            
            $missing_fields = false;
            
            //fill in data array from post values
            foreach($data as $key=>$value)
            {
                $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
               
                if($key == "start_date" || $key == "finish_date")
                {
                    if($data[$key] != "")                           
                        $data[$key] = $this->utilities->uk_to_isodate($data[$key]);                    
                }       
                // Ensure that all required fields are present    
                if(in_array($key,$required_fields) && $data[$key] == "")
                {
                    $missing_fields = true;
                    break;
                }
            }
            
            if ($missing_fields)
            {
                $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "WebsiiteManager/HandlerPost update - the coupon with a code of '$coupon_id' could not be saved");
                return;
            }
            /*
            $data['use_max'] 				= intval( $data['use_max'] );
            $data['product_category_id'] 	= intval( $data['product_category_id'] );
            $data['product_id'] 			= intval( $data['product_id'] );
            $data['minimum_order'] 			= intval( $data['minimum_order'] );*/
            
            //depeding on the $coupon_id do the update or insert
            
            $coupon_id = $this->coupon_model->save($coupon_id,$data);
            
            if(!$coupon_id)
            {
               // Something went wrong whilst saving the user data.
               $this->error_model->report_error("Sorry, the coupon could not be saved/updated.", "couponManager/coupon save");
               return;
            }
            
            //delete coupons discount products
            $this->coupon_model->delete_coupon_discount_products($coupon_id);
                
            //save coupons discount products
            if( $data['discount_type'] == 'products' )
            {
                $buy_ids = $this->input->post('buy_ids');
                $this->_save_products($buy_ids, 'buy', $coupon_id);
                
            }
            
            if( $data['discount_type'] == 'products' || $data['discount_type'] == 'amount')
            {
                $reward_ids = $this->input->post('reward_ids');                   
                $this->_save_products($reward_ids, 'reward', $coupon_id);
            } 
                            
            redirect("/couponmanager/coupon/$coupon_id");
            
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        
        switch($type)
        {
            //delete coupons
            case 1:
                             
                //get coupon ids separated with ";"
                $coupon_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($coupon_ids!="")
                {
                    $arr_ids = explode(";",$coupon_ids);
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
                        $this->coupon_model->delete($where_in);
                    }
                }
                                
                //get list of coupons
                $coupons = $this->coupon_model->get_list($this->records_per_page, $current_page, $count_all);                            
                
                //load view 
                $this->load->view('admin/couponmanager/coupon_listing',array('coupons'=>$coupons,'pages_no' => $count_all / $this->records_per_page));                
                
            break;
            
            //page number changed
            case 2:
                
                //get list of coupons
                $coupons = $this->coupon_model->get_list($this->records_per_page, $current_page, $count_all);                            
                
                //load view 
                $this->load->view('admin/couponmanager/coupon_listing',array('coupons'=>$coupons,'pages_no' => $count_all / $this->records_per_page));                
                
            break;
            
            // categories changed for a coupon
            case 3:
            	$product_category_id 	= $this->tools_model->get_value("product_category_id","","post",0,false);
            	
            	$products 				= $this->product_model->get_list( TRUE, $product_category_id );
            	$html 					= $this->utilities->print_select_options( $products, 'product_id', 'product_name', '-1', 'All products' );
            	
            	echo $html;
            break;
        }
    }    
    
    /**
    * @method save_products
    * @desc This methos saves the discount products
    * 
    * @param mixed $products_ids
    */
  
    function _save_products( $products_ids, $discount_type, $coupon_id )
    {
        if( $products_ids != "" )
        {
            $arr_coupons_discount =  explode(";", $products_ids);
            
            foreach($arr_coupons_discount as $row)
            {
                if($row != "")
                {
                    list($product_id, $qty) = explode("_", $row);
                    $product_data =  array(
                        'coupon_id'  => $coupon_id,
                        'product_id' => $product_id,
                        'qty'        => $qty,
                        'type'       => $discount_type
                    );                    
                    $this->coupon_model->save_coupon_discount_products($product_data);    
                }
            }
            
        }
    }
}  
?>