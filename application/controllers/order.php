<?php
die("OFFLINE");
class Order extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    
    function Order()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();    
        $this->data['success'] = 0;            
        
        // Load models etc
        $this->load->model("login_model");
        $this->load->model("menu_model");
        $this->load->model("product_model");        
        $this->load->model("document_model");        
        $this->load->model("order_model");    
        $this->load->model("eway_model");
        $this->load->model("email_model");   
        $this->load->model("coupon_model");
        $this->load->model("settings_model");

        $this->load->helper("coupon_helper");
        
        if ( !$this->login_model->is_logged_in('user') )
            redirect( base_url() . "page/account" );              
    }
    
    public function index()
    {
        $this->order_details();
    }
    
    public function order_details()
    {   
        $order_step = $this->session->userdata("order_step"); 
        $cart = $this->product_model->get_cart_info();            
        
        if( $order_step < 1 && count($cart['cart_products']) < 1)
            redirect(base_url(1));
            
        $order_step = $this->session->userdata("order_step");
        if(!isset($order_step) || empty($order_step))
            $this->session->set_userdata("order_step", 1);             
            
        $this->data['cart_products'] = $cart;
        
        $this->data['step'] = 1;
        $this->data['order_step_name'] = "order_details";        
        $this->data['nav_main'] = $this->menu_model->get_menu_html_extended(1, 11);
        $this->data['hide_coupon_code'] = $this->_hide_coupon_code();
                
        //load meta tags
        $this->data["meta_keywords"] = "Commandfusion - Order detail";
        $this->data["meta_description"] = "Commandfusion - Order detail"; 
        $this->data["meta_title"] = "Commandfusion - Order detail";
        
        $this->session->set_userdata("order_step",2);
        
        $this->view();        
        
    }
    
    public function your_details()
    {
        /*$order_step = $this->session->userdata("order_step");
        if( !$order_step )
            redirect(base_url(1));
            
        if( $order_step < 2)
            redirect(base_url(1)."order/order_details");*/
                 
        $order_cost = $this->session->userdata("orders_costs");    
        $data = array(  
                        "first_name"              => '',
                        "last_name"               => '',
                        "email"                   => '',
                        "country"                 => '',
                        "currency"                => '',
                        "billing_address1"        => '',
                        "billing_address2"        => '',
                        "billing_suburb"          => '',
                        "billing_postcode"        => '',
                        "billing_state"           => '',
                        "delivery_address1"       => '',
                        "delivery_address2"       => '',
                        "delivery_suburb"         => '',
                        "delivery_postcode"       => '',
                        "delivery_state"          => '',
                        "comments"                => '',
                        "same_as_billing"         => '0'
                        
                    );
                        /*
                        "company"           => '',
                        "phone"             => '',
                        "fax"               => '',
                        "address"           => '',
                        "address2"          => '',
                        "suburb"            => '',
                        "state"             => '',
                        "postcode"          => $order_cost['postcode']  
                    );*/

            //get user details (my account)
           	$user = $this->session->userdata("user");
			$user_data = $this->users_model->get_details( $user['user_id'] );
			
                    
           	$order_details = $this->session->userdata("orders_your_details");
           	if(isset($order_details) && !empty($order_details))
           	{
               $this->data['order_details'] = $order_details;
           	}
           	else
           	{
               $this->data['order_details'] = $user_data;
           	}
           	
           	$this->data['post'] = $this->data['order_details'];
        
        
        $this->data['step'] = 2;
        $this->data['order_step_name'] = "your_details";
        //add classes to commerce details
        $this->data['class_billing_address1']  = 'required';
        $this->data['class_billing_suburb']    = 'required';
        $this->data['class_billing_postcode']  = 'required';
        $this->data['class_billing_state']     = 'required';
        $this->data['class_delivery_address1'] = 'required';
        $this->data['class_delivery_suburb']   = 'required';
        $this->data['class_delivery_postcode'] = 'required';
        $this->data['class_delivery_state']    = 'required';
        
        $this->data['cart_products'] = $this->product_model->get_cart_info();
        if(empty($this->data['cart_products']['cart_products']))
        {
            show_404();
            return;
        }
        
        $this->data['states'] = $this->tools_model->get_states();
        $this->data['countries']  = $this->tools_model->get_countries();
   		$this->data['currencies'] = $this->resources_model->get_list('currency');
        
        
        $this->data["meta_keywords"] = "orders";
        $this->data["meta_description"] = "order";
        $this->data["meta_title"] = "Commandfusion - Your detail"; 
        
        $this->session->set_userdata("order_step",3);
        
        $this->view();
    }
    
    public function save_your_details()
    {
         $data = array(  
                        "first_name"              => '',
                        "last_name"               => '',
                        "email"                   => '',
                        "country"                 => '',
                        "currency"                => '',
                        "billing_address1"        => '',
                        "billing_address2"        => '',
                        "billing_suburb"          => '',
                        "billing_postcode"        => '',
                        "billing_state"           => '',
                        "delivery_address1"       => '',
                        "delivery_address2"       => '',
                        "delivery_suburb"         => '',
                        "delivery_postcode"       => '',
                        "delivery_state"          => '',
                        "comments"                => '',
                        "same_as_billing"         => '0'  
                    );
           
         $your_details = array();
         $same_as_billing = $this->input->post("same_as_billing");
         
         foreach($data as $key => $value)
         {
             $data[$key] = $this->tools_model->get_value($key, $data[$key], "post", "", true);
             
             if($same_as_billing == 1) //override delivery info with billing information
             {
                 $pos = strpos($key, "delivery_");
                 if( $pos !== false )
                 {
                     $data[$key] = $this->tools_model->get_value( str_replace("delivery_", "billing_", $key), $data[$key], "post", "", true); 
                 }
             }
             
         }
         
         $this->session->set_userdata("orders_your_details", $data);
          
          redirect(base_url(1)."order/order_confirmation");
    }
    
    public function submit_order()
    {
        $order_step = $this->session->userdata("order_step");

        if( !$order_step )
        	redirect(base_url(1));

        if( $order_step < 4)
        	redirect(base_url(1)."order/order_confirmation");

        $data = array(  
                    "card_holder"       => '',
                    "card_number"       => '',
                    "card_type"         => '',
                    /*"exp_date"          => '', */
                    "exp_month"         => '',
                    "exp_year"          => '',
                    "card_cvv"          => ''
                );

        foreach($data as $key => $value)
        {
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post","",true);
        }

        #insert order details
        $order_details = $this->session->userdata("orders_your_details");
        $cart_products = $this->product_model->get_cart_info();
        
        unset($order_details['same_as_billing']); 
        $order_details['order_subtotal'] = $cart_products['subtotal'] ;
        $order_details['order_total'] = $cart_products['total'];
        $order_details['order_tax_amount'] = $cart_products['gst'];
        $order_details['order_status'] = 'pending';  
        $order_details['user_id'] = $this->login_model->getSessionData( 'user_id', 'user');
        $order_details['ip_address'] = $_SERVER['REMOTE_ADDR'];
        
        //save the coupon code
        $used_coupon_ids = $this->session->userdata("used_coupon_ids"); 
        
        if($used_coupon_ids != "")
        {
            $ids = explode(";", $used_coupon_ids);
            $order_details['coupon_id'] = ($ids) ? $ids[1] : "";
        }
        
        $order_id = $this->session->userdata('order_id');
        
        if(empty($order_id))
        {
            //save order header
            $order_id = $this->order_model->save_order($order_details);                          
            #insert order 
            //$this->session->set_userdata('order_id'); //added order id to session Agi            
        }
        else
        {
            //delete order items to add new one
            $this->order_model->delete_order_items($order_id);
            
            //update order header
            $this->order_model->save_order($order_details, $order_id);
        }
        
        if(!empty($order_id))        
        {
            //add order items (products)
            foreach($cart_products['cart_products'] as $item) 
            {
                $temp['product_id'] = abs($item['id']);
                $temp['order_id'] = $order_id;
                $temp['quantity'] = $item['qty'];
                $temp['item_subtotal'] = $item['price'];
                $temp['item_tax_amount'] = $item['subtotal'] * GST;
                $temp['item_total'] = $item['subtotal'];
                $temp['is_free'] = ($item['id'] < 0) ? 1 :0;

                
                $this->order_model->save_order_item($temp);
                
            }
        }
        
        //list($exp_month,$exp_year) = explode("-",$data['exp_date']);
         
        //$invoice_name = $this->order_model->invoice_generator($order_id);
        //$data2['invoice'] = $invoice_name;
        
        $owner_details = $this->settings_model->get_details_array("owner_details"); 
        $eway_default_customer_id = (isset($owner_details["gateway_id"])) ? $owner_details["gateway_id"] : "";
        
        /*load eway payment*/                
        
        $this->eway_model->init($eway_default_customer_id, EWAY_DEFAULT_PAYMENT_METHOD, EWAY_DEFAULT_LIVE_GATEWAY);

        $this->eway_model->setTransactionData("TotalAmount",$order_details['order_total']*100 ); //mandatory field
        $this->eway_model->setTransactionData("CustomerFirstName", ifvalue($order_details['first_name'],""));
        $this->eway_model->setTransactionData("CustomerLastName", ifvalue($order_details['last_name'], ""));
        $this->eway_model->setTransactionData("CustomerEmail", ifvalue($order_details['email'], ""));
        $this->eway_model->setTransactionData("CustomerAddress", ifvalue($order_details['billing_address1'],""));
        $this->eway_model->setTransactionData("CustomerPostcode", ifvalue($order_details['billing_postcode'], ""));
        $this->eway_model->setTransactionData("CustomerInvoiceDescription", "Testing");
        $this->eway_model->setTransactionData("CustomerInvoiceRef", "INV{$order_id}");
        $this->eway_model->setTransactionData("CardHoldersName", $data['card_holder']); //mandatory field
        $this->eway_model->setTransactionData("CardNumber", $data['card_number']); //mandatory field
        $this->eway_model->setTransactionData("CardExpiryMonth", $data['exp_month']); //mandatory field
        $this->eway_model->setTransactionData("CardExpiryYear", $data['exp_year']); //mandatory field
        $this->eway_model->setTransactionData("TrxnNumber", $order_id);
        $this->eway_model->setTransactionData("Option1", "1");
        $this->eway_model->setTransactionData("Option2", "2");
        $this->eway_model->setTransactionData("Option3", "3");
        
        //for REAL_TIME_CVN
        //$this->eway->setTransactionData("CVN", "123");
    
        //for GEO_IP_ANTI_FRAUD
        ///$this->eway->setTransactionData("CustomerIPAddress", $this->eway->getVisitorIP()); //mandatory field when using Geo-IP Anti-Fraud
        //$this->eway->setTransactionData("CustomerBillingCountry", "AU"); //mandatory field when using Geo-IP Anti-Fraud
        
        
        //special preferences for php Curl
        $this->eway_model->setCurlPreferences(CURLOPT_SSL_VERIFYPEER, 0);  //pass a long that is set to a zero value to stop curl from verifying the peer's certificate 
        //$this->eway_model->setCurlPreferences(CURLOPT_CAINFO, "/usr/share/ssl/certs/my.cert.crt"); //Pass a filename of a file holding one or more certificates to verify the peer with. This only makes sense when used in combination with the CURLOPT_SSL_VERIFYPEER option. 
        //$this->eway_model->setCurlPreferences(CURLOPT_CAPATH, "/usr/share/ssl/certs/my.cert.path");
        //$this->eway_model->setCurlPreferences(CURLOPT_PROXYTYPE, CURLPROXY_HTTP); //use CURL proxy, for example godaddy.com hosting requires it
        //$this->eway_model->setCurlPreferences(CURLOPT_PROXY, "http://proxy.shr.secureserver.net:3128"); //use CURL proxy, for example godaddy.com hosting requires it
       //4444333322221111 
        $ewayResponseFields = $this->eway_model->doPayment();
        /*print "<pre>";
        print_r($ewayResponseFields);
        print "</pre>";
    exit();*/
        $this->data['transaction_message']="";
        if($ewayResponseFields["EWAYTRXNSTATUS"]=="False")
        {
            $this->data['transaction_message']="(".$ewayResponseFields["EWAYTRXNERROR"].")";    
            $data2['order_status'] = 'failed';
             $this->order_model->save_order($data2,$order_id);
             $this->session->set_userdata('order_id',$order_id);
                    
        }
        else 
        
        	if($ewayResponseFields["EWAYTRXNSTATUS"]=="True")
        	{
	            $this->data['transaction_message']="Order success<br>";
	            /*$owner_details = $this->settings_model->get_details_array("owner_details");*/ 
	            $this->data['phone'] = "+61 422 639 728";/*$owner_details['phone'];*/
	            
	            $email_data = array(    
	                                "order_id" => $order_id, 
	                                "first_name"=> $order_details['first_name'],
	                                "phone" => $this->data['phone']
	                               );
	            
	            $invoice_name = $this->order_model->invoice_generator($order_id); 
	            $data2['invoice'] = $invoice_name;
	            $data2['order_status'] = 'completed';
	            $data2['is_completed'] = 1;
	            $this->order_model->save_order($data2,$order_id); 
	
	            //prepare the email
	            $attach = FCPATH ."/invoices/".$invoice_name;
	            if( !file_exists($attach)) $attach = "";
	            
                //send email users checked as get "order notification" as BCC
                $arr_bcc = array();
                $order_contacts = $this->settings_model->get_contacts("order_notification = 1"); 
                
                if($order_contacts)
                {
                    foreach($order_contacts->result() as $row)
                        $arr_bcc[] = $row->email;                    
                }
                
                //send email to user
                $this->email_model->send_email($order_details['email'], "invoice_email", $email_data, $attach, $arr_bcc);
	                            
	            //check if there are products in the cart that have serial number generation assigned
	            $user = $this->session->userdata("user");
	            
	            foreach($cart_products['cart_products'] as $item) 
	            {
	                $item_id = abs($item['id']);
	                $product_obj = $this->product_model->get_details($item_id);
	                $product = ($product_obj) ? $product_obj->row() : FALSE;
	                if( $product)// && !empty($product->serial_gen) )
	                { 
	                
	                    for($i = 1; $i <= $item['qty']; $i++)
	                    {
	                        //include (FCPATH."serialgen/" . $product->serial_gen . ".inc.php");                    
	                        //$serial_num = generateSerial($product->product_id);
	                        
	                        $device_data = array(
	                                            'user_id'     => $user['user_id'],
	                                            'product_id'  => $product->product_id,
	                                            'date_added'  => date("Y-m-d H:i:s"),  
	                                            'last_mod'    => date("Y-m-d H:i:s"),                                        
	                                       );
	                        $this->db->trans_start();               
	                        $this->product_model->save_device($device_data);
	                        $this->db->trans_complete();               
	                    }
	                }
	            }
	            
	            $this->cart->destroy();
	            $this->session->unset_userdata("orders_your_details");
	            $this->session->unset_userdata("orders_costs");
	            $this->session->unset_userdata("order_id");
	            $this->session->unset_userdata("order_step");
	            $this->data['success'] = 1;
	            $this->data['order_id'] = $order_id;
	            $this->data['invoice_name'] = $invoice_name;
        }
         
         
        $this->data['cart_products'] = $this->product_model->get_cart_info();

        $this->data['step'] = 4;
        $this->data['order_step_name'] = "status_transaction";

        $this->data["meta_keywords"] = "orders";
        $this->data["meta_description"] = "order";
        $this->data["meta_title"] = "CommandFusion- Status transaction"; 

        $this->session->set_userdata("order_step",5);

        $this->view();
     
    }
    
    public function order_confirmation()
    {
        $order_step = $this->session->userdata("order_step");
        
        if( !$order_step )
            redirect(base_url(1));
        
        if( $order_step < 3 )
            redirect(base_url(1)."order/your_details");
        
        
        $this->data['step'] = 3;
        $this->data['order_step_name'] = "payment_details";
        $this->data['cart_products'] = $this->product_model->get_cart_info();
        if(empty($this->data['cart_products']['cart_products']))
        {
            show_404();
            return;
        }
        
        $this->data['states'] = $this->tools_model->get_states();
        
        $this->data["meta_keywords"] = "orders";
        $this->data["meta_description"] = "order";
        $this->data["meta_title"] = "CommandFusion- Payment detail"; 
        
        $this->session->set_userdata("order_step",4);
        
        $this->view();
    }
    
    
    private function view()
    {
		$this->data["nav_main"] = $this->menu_model->get_menu_html_extended(1, 11);    	
    	
        $this->load->view('header', $this->data);
        $this->load->view('order/prebody', $this->data);
        $this->load->view('order/main', $this->data);
        $this->load->view('pre_footer', $this->data); 
        $this->load->view('footer', $this->data);
    }
    
    
    
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
       
        switch($type)
        {
            #refresh shopping cart HTML
            case 1:
                $return_data         = array();
                $return_data['html'] = "";
                
                $this->data['cart_products'] = $this->product_model->get_cart_info();        
                
                $return_data['hide_coupon_code'] = $this->_hide_coupon_code();                
                $return_data['html'] = $this->load->view("order/list_cart", $this->data, TRUE);
                
                echo json_encode($return_data);
            break;
            
            case 2:
                $return_data = array();
                $cart_info = $this->product_model->get_cart_info();
                $postcode = $this->tools_model->get_value("postcode",-1,"post",0,false);
                $shipping_method = $this->tools_model->get_value("shipping_method",'normal',"post",0,false);
                
                $shipping_cost = $this->order_model->shipping_cost($postcode, $shipping_method);
                $order_total = $cart_info['total'] + $shipping_cost;
                $gst = $cart_info['gst'];
                
                $this->session->set_userdata("orders_costs",array("goods_total"=>$cart_info['total'],"shipping_cost"=> $shipping_cost,"postcode"=>$postcode,"total"=>$order_total, "shipping_method"=>$shipping_method, 'gst'=>$gst));
                
                $return_data["html"] = $this->load->view('order/order_costs',$cart_info,true);
                            
                  echo json_encode($return_data);
                
            break;
            
            case 3:
                $return_data = array();
                $cart_info = $this->product_model->get_cart_info();
                $postcode = $this->tools_model->get_value("postcode",-1,"post",0,false);
                $shipping_method = $this->tools_model->get_value("shipping_method",'normal',"post",0,false);
                
                $shipping_cost = $this->order_model->shipping_cost($postcode);
                $order_total = $cart_info['total'] + $shipping_cost;
                
                $gst = $cart_info['gst'];
                
                $this->session->set_userdata("orders_costs",array("goods_total"=>$cart_info['total'],"shipping_cost"=> $shipping_cost,"postcode"=>$postcode,"total"=>$order_total, "shipping_method"=>$shipping_method, 'gst'=>$gst));
                            
                $return_data["html"] = $this->load->view('order/costs',$cart_info,true);
                            
                echo json_encode($return_data);
                
            break;
            //check the coupon code
            case 4:
                $coupon_code = $this->tools_model->get_value("coupon_code", "", "post", 0, false);    
                
                $return_data = array();
                $return_data["error"] = "";
                
                //Allow only one coupon to apply.
                if( get_number_of_coupons() > 0 )
                    $return_data["error"] = "Error: Only one coupon is allowed.";
                else
                {   
                    //check if the coupon code exists
                    $coupon = $this->coupon_model->get_details($coupon_code, $by_code = TRUE, " start_date <= now() AND finish_date >= now() AND coupon_code_required = 1");
                        
                    if(! $coupon)
                        $return_data["error"] = "Error: Invalid coupon code.";
                    else
                    {
                        $coupon_id = $coupon->coupon_id;
                        //$cart_info = $this->product_model->get_cart_info();
                        
                        if (is_used_coupon_id($coupon_id))
                        {
                    	    $return_data["error"] = "Error: Coupon code already used.";
                    	    
                        }
                        elseif( !$coupon->use_multiple_times && is_used_coupon_id_in_orders($coupon_id) )
                        {
                            $return_data["error"] = "Error: Coupon code already used.";   
                        }
                        else
                        {                       	                                
                    	    $coupon_ok = coupon_id_applies($coupon_id, $coupon->discount_type);
		                              
		               	    if ($coupon_ok)
		                    {
		                      	add_reward_products($coupon_id );
		                    }
		                    else
		                    {
		                   	    $return_data["error"] = "Error: Invalid coupon code.";	         	
		                    } 		                
		                        
		                }
                    } 
                }
		                
                echo json_encode($return_data);   
            break;
            
            case 5:/* get states*/
                $return_data = array();
                $country_id = $this->tools_model->get_value("country_id", 0, "post", 0, false);
                
                $states = $this->tools_model->get_states($country_id);
                
                $return_data["html"] = $this->utilities->print_select_options( $states, "state_id", "name");
                
                echo json_encode($return_data);       
            break;
        }
    }
                
    function _hide_coupon_code()
    {
        $hide_coupon_code = FALSE;
        
        //check if the client allready used a coupon code
        $used_coupon_ids = $this->session->userdata("used_coupon_ids");
        if($used_coupon_ids != "")
            $hide_coupon_code =  TRUE;
            
        return $hide_coupon_code;    
    }            
    
    function test()
    {
        /*$invoice_name = $this->order_model->invoice_generator(68); 
        echo base_url()."invoices/".$invoice_name;    */                
    }
}

?>