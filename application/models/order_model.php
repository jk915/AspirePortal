<?php
class Order_model extends CI_Model 
{    
	function Order_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    /***
    * @method get_list
    * @author Agnes Konya
    * @abstract This method gets a list of all orders from the database.  
    * 
    * @param integer $limit - Limits the recordset to a specific number of records
    * @param integer $page_no - Starts the recordset at a specific page no.
    * @param integer $count_all - Counts all records.
    * @param string $search_name - search by client business name
    * @param integer $search_doc_type_id - search by document type id 
    * @param string $search_period - search by period (eg. today, yesterday)
    * @param date $start_date - use for case "choose"
    * @param date $end_date - use for case "choose"
    * 
    * @returns A list of order headers
    */
    public function get_list($limit = "", $page_no = "", &$count_all, $search_name = "", $search_doc_type_id = -1, $search_period = "today", $start_date = "", $end_date = "", $search_status = "", $user_id = "")
    {
        $this->_get_list($limit,$page_no,$count_all,$search_name,$search_doc_type_id,$search_period,$start_date,$end_date,$search_status, $user_id, true);
        $count_all = $this->db->count_all_results();
        
        $this->_get_list($limit,$page_no,$count_all,$search_name,$search_doc_type_id,$search_period,$start_date,$end_date,$search_status, $user_id, false); 
        $query = $this->db->get();        
                
        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
        
    function _get_list($limit = "", $page_no = "", &$count_all, $search_name = "", $search_doc_type_id = -1, $search_period = "today", $start_date = "", $end_date = "", $search_status = "", $user_id = "", $only_count = false)
    {
        $sub_select = "
                        (    
                            SELECT  oi.product_id 
                            FROM nc_order_header nco 
                            INNER JOIN nc_order_items oi ON oi.order_id = nco.id      
                            WHERE oi.product_id = -1 AND oi.order_id = o.id 
                        ) AS abn_tfn";
        
        $this->db->select('o.*,date_format(o.created_date,"%d/%m/%Y") as order_date,'.$sub_select, false);
        $this->db->from('nc_order_header o');
        //$this->db->join("nc_order_items oi","oi.order_id = o.id");
        //$this->db->join("nc_products p","p.product_id = oi.product_id");        
        //$this->db->where("o.is_completed","1");   
        //$this->db->where('p.product_id >','0');             
        $this->db->order_by("o.created_date", "DESC");     
        
        if($search_name != "")
        {
        	$array_search_name = explode( ' ', $search_name );
        	$where = '(';
        	foreach( $array_search_name as $search )
        	{
        		if( strlen( $where ) > 6 )
        			$where .= "OR o.first_name LIKE '%$search%' OR o.last_name LIKE '%$search%' OR o.company LIKE '%$search%' ";
            	else $where .= "o.first_name LIKE '%$search%' OR o.last_name LIKE '%$search%' OR o.company LIKE '%$search%' ";
        	}
        	$where .= ')';
        	
        	$this->db->where( $where );
        }
        
        /*if($search_doc_type_id != -1)
            $this->db->where("p.product_category_id",$search_doc_type_id);    
        */
        //$this->db->where(" LOWER(o.order_status) != 'failed' ", null, false);
        //$this->db->where("p.product_category_id", COMPANY_DOCS_ID);
        
        if($search_period != "")
        {
            switch($search_period)
            {
                case "today":                    
                    $this->db->where('date_format(o.created_date,"%Y-%m-%d") = CURDATE()');
                break;
                
                case "yesterday":
                    $this->db->where('date_format(o.created_date,"%Y-%m-%d") = CURDATE() - INTERVAL 1 DAY ');
                break;
                
                case "week_to_date":
                    $this->db->where('date_format(o.created_date,"%Y-%m-%d") BETWEEN subdate(curdate(), INTERVAL weekday(curdate()) DAY) AND curdate()', null, false);
                break;
                
                case "last_week":
                    $this->db->where('date_format(o.created_date,"%Y-%m-%d") BETWEEN subdate(curdate(), INTERVAL weekday(curdate()) DAY)+ INTERVAL -7 DAY AND subdate(curdate(), INTERVAL weekday(curdate()) DAY)+ INTERVAL -1 DAY', null, false);                            
                break;
                
                case "month_to_date":
                    $this->db->where('date_format(o.created_date, "%Y-%m-%d") BETWEEN date_format(curdate(), "%Y-%m-01") AND curdate()', null, false);                                                  
                break;
                
                case "last_month":
                    $this->db->where('date_format(o.created_date,"%Y-%m") = date_format(curdate() - INTERVAL 1 MONTH, "%Y-%m")', null, false);
                break;
                
                case "last_quarter":
                    $this->db->where('date_format(o.created_date, "%Y-%m") BETWEEN date_format(curdate() - INTERVAL 3 MONTH, "%Y-%m") AND curdate()', null, false);                              
                break;
                
                case "this_quarter":
                    $current_month = date("n");
                    $months_remaining = $current_month % 3;
                    $where = 'date_format(' . $column . ',"%Y-%m-%d")  BETWEEN (date_format(curdate(),"%Y-%m-%d") - INTERVAL '. $months_remaining . ' MONTH) AND date_format(curdate(),"%Y-%m-%d")';
                break;
                
                case "choose":
                        $this->db->where('date_format(o.created_date,"%Y-%m-%d") BETWEEN "'. $this->utilities->uk_to_isodate($start_date) .'" AND "'. $this->utilities->uk_to_isodate($end_date).'"', null, false);
                break;
            }
        }
        
        if($search_status != "")
            $this->db->where("o.order_status",$search_status);        
        
        if($user_id != "")    
            $this->db->where("o.user_id", $user_id);
    
        if (!$only_count)
        {
            if ($limit != "" && $page_no!= "" && $count_all > $limit)
            {
                $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
            }          
        }
    }
    
    
    public function shipping_cost( $postcode, $shipping_type = 'normal' )
    {
    	/*if($postcode <= 2500)
    		return 80;
    	else if($postcode >2500 && $postcode <= 5000)
    		return 120;
    	else
    		return 180;*/
    	
    	/*$CI = &get_instance();
    	$CI->load_model('product_model');*/
    	
    	$cost = 0.00;
    	$cart_products = $this->product_model->get_cart_info();
    	$total_item = 0;
    	$total_weight = 0;
    	foreach($cart_products['cart_products'] as $item) 
        {
        	$total_item += $item['qty'];
        	$total_weight += $item['qty']*$this->product_model->get_weight( $item['id'] );
        }
        
        
        return $this->calculate_shipping($postcode, $shipping_type, $total_item, $total_weight);
    }
    
    public function calculate_shipping( $postcode, $shipping_type, $total_items, $total_weight)
    {
    	// if the total_weight is < 0.5
    	if($total_weight < 0.5)
    	{
    		$normal_price = 6.94;
    		$express_price = 7.72;
    		
    		// normal shipping method
    		if($shipping_type == 'normal')
    			return number_format($total_items*$normal_price,2,'.','');
    		
    		// express shipping method
    		else
    		{
    			//IF the order is for a customer in Melbourne
    			if($postcode == 1024)
    				return number_format($total_items*$normal_price,2,'.','');
    			else
    				return number_format($total_items*$express_price,2,'.','');
    		}
    	}
    	else
    	{
    		$freight_array = $this->get_freight_caharging( $postcode );
    		if( $freight_array )
    		{
    			$cost = $freight_array[$shipping_type."_first_item"] + ($total_items -1)*$freight_array[$shipping_type."_additional_item"] + $total_weight*$freight_array[$shipping_type."_weight_charge_kg"];
    			return number_format($cost,2,'.','');
    		}
    		else
    			return 0.00;
    	}
    }
    
    private function get_freight_caharging( $postcode )
    {
    	$this->db->select("*");
        $this->db->from("freight_charging AS fc");
        
        $this->db->where("fc.postcode_start <=",$postcode);
        $this->db->where("fc.postcode_finish >=",$postcode);
        
        $query = $this->db->get();
        //If there is a resulting row
        if ($query->num_rows() > 0)
            return $query->row_array();  
        else
        {
        	$state = $this->get_postocde($postcode);
        	if($state)
        	{
		       	$this->db->select("*");
        		$this->db->from("freight_charging AS fc");
		        
		        $this->db->where("fc.area",$state." OTHER");
		     	$query = $this->db->get();
		        //If there is a resulting row
		        if ($query->num_rows() > 0)
		        	return $query->row_array();        
		        else
		        	return false;
        	}
        	else
        		return false;
        }
    }
    
    public function get_postocde( $postcode )
    {
    	$this->db->select("p.state");
        $this->db->from("postcodes AS p");
        
        $this->db->where("p.postcode",$postcode);
     	$query = $this->db->get();
        //If there is a resulting row
        if ($query->num_rows() > 0)
        {
            $arr = $query->row_array();
            return $arr['state'];
        }         
        else
        {
        	return false;
        }
    }
    
    /*public function save_order( $data )
    {
    	$this->db->insert("nc_order_header",$data);
    	return $this->db->insert_id();
    }
    
    public function save_order_item( $data )
    {
    	$this->db->insert("nc_order_items",$data);
    }*/
    
    function save_order( $data, $order_id = '' )
    {
        if (is_numeric($order_id))
        {
            $this->db->where('id',$order_id);
            return $this->db->update('nc_order_header',$data);
        }
        else
        {
            $this->db->insert('nc_order_header',$data);
            return $this->db->insert_id();
        }
    }
    
    function save_order_item( $data, $order_item_id = '' )
    {
        if (is_numeric($order_item_id))
        {
            $this->db->where('id',$order_item_id);
            return $this->db->update('nc_order_items',$data);
        }
        else
        {
            $this->db->insert('nc_order_items',$data);
            return $this->db->insert_id();
        }
    }
    
    /**
    * @method delete_order_items
    * @abstract This function deletes all order items from a seletected order ID
    * 
    * @param mixed $where_in
    */
    function delete_order_items($order_id)
    {
        if( empty($order_id) || (!is_numeric($order_id)) )
            return;
            
        $this->db->trans_start();
            
        $this->db->where("order_id", $order_id);
        $this->db->delete('nc_order_items');                
        
        $this->db->trans_complete();
    }
    
    
    function save_message( $message, $order_id = '' )
    {
        if(is_numeric($order_id))
            $this->db->query("UPDATE nc_order_header SET payment_status = concat(payment_status, now(),' ','".$message."<br>') where id = ". $order_id);
            
    }
    
	function get_order_details($order_id = "", $session_id = "", $user_id = "", $is_completed = -1)
    {
        $this->db->select("*,date_format(created_date,\"%d/%m/%Y\") as order_date",false);
        
        if ($order_id != "") 
            $this->db->where("id",$order_id);
            
        if ($session_id != "") 
            $this->db->where("session_id",$session_id);
            
        if ($user_id != "") 
            $this->db->where("user_id",$user_id);
            
        if ($is_completed != -1) 
            $this->db->where("is_completed",$is_completed);
        
        $query = $this->db->get("nc_order_header");
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
    }
    
    function get_order_item_details($order_item_id = "", $order_id = "", $product_id = "")
    {
        $this->db->select("oi.*, p.product_id, p.product_name");
        $this->db->from("nc_order_items oi");
        $this->db->join("nc_products p","p.product_id = oi.product_id");
        if($order_item_id != "")
            $this->db->where("oi.id", $order_item_id);
        else
            $this->db->where("oi.order_id", $order_id);        
            
        if($product_id != "")
            $this->db->where("p.product_id",$product_id);
        else    
            $this->db->where("p.product_id >","0");
        $query = $this->db->get();
        
        //If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    function get_order_items($order_id)
    {
        $this->db->select("oi.*, p.product_id, p.product_name");
        $this->db->from("nc_order_items oi");
        $this->db->join("nc_products p","p.product_id = oi.product_id");
        
        $this->db->where("oi.order_id", $order_id);
        $this->db->order_by("p.product_id","desc");
        
        $query = $this->db->get();
        //If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    /***
    * The get_card_cards method loads all credit cards that are available
    */
    function get_card_cards()
    {
        $this->db->select('*');
        $this->db->from('credit_cards');
        $this->db->order_by("credit_card_name", "ASC");     
                                                                                     
        $query = $this->db->get();        

        // If there is a at least one resulting row, return the recordset, otherwise return false
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    /***
    * Method: get_card_details 
    * Desc: This method returns a credit_card record, as
    * specified by the credit_card_id
    * 
    * @param mixed $payment_id - The payment_id of the payment system to load.
    */
    function get_card_details($credit_card_id)
    {
        $query = $this->db->get_where('credit_cards', array('credit_card_id' => $credit_card_id));

        // If there is a resulting row, return it, otherwise return false
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;                
    } 
    
    public function delete($where_in)
    {
        $this->db->where(" order_id in (".$where_in.")",null,false);
        $this->db->delete('nc_order_items');
        
        $this->db->where(" id in (".$where_in.")",null,false);
        $this->db->delete('nc_order_header');
    }
    
    public function get_payment_systems()    
    {
        $query = $this->db->get_where('nc_payment_systems', array("enabled"=>"1"));
        
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    public function get_payment_details($payment_view)
    {
        $query = $this->db->get_where('nc_payment_systems', array("enabled"=>"1","payment_view"=>$payment_view));
        
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    public function order_history($user_id)
    {   
        $this->db->select("oi.order_id,date_format(o.created_date,'%d %b %Y') as order_date, o.invoice, p.product_name,",false);        
        $this->db->from("nc_order_header o");
        $this->db->join("nc_order_items oi","o.id = oi.order_id");
        $this->db->join("nc_products p","p.product_id = oi.product_id");
        $this->db->where("o.is_completed","1");
        $this->db->where("o.user_id",$user_id);                
        $this->db->where("p.product_id >",'0');
        $this->db->limit("10");
        $this->db->order_by("o.created_date","desc");
        
        
        $query = $this->db->get();        
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    public function check_order($order_id,$user_id)
    {
        $this->db->where("user_id",$user_id);                
        $this->db->where("id",$order_id);
        $query = $this->db->get("nc_order_header");
        
        return  ($query->num_rows() > 0);
        
    } 
    
    public function check_order_item($order_id, $product_id = "")    
    {
        $this->db->where("order_id", $order_id);
        
        if($product_id != "" )
            $this->db->where("product_id", $product_id);
        
        $query = $this->db->get("nc_order_items");
        
        return  ($query->num_rows() > 0);    
    }
    
	public function invoice_generator($order_id)
    {
        if( !is_numeric($order_id))
            return "";
            
        $order = $this->get_order_details($order_id);
        
        if( !$order)
            return ""; 
            
        $order_date = date("d M Y",strtotime($order->created_date));
        $order_items = $this->get_order_items($order_id);
        
        //client address
        $client_address = "";                                           
        //$client_address .= $order->first_name." ". $order->last_name."\n".$order->address."\n".$order->suburb.", ".$order->state." ".$order->postcode."\n";
        
        if($order->company != "")
        	$client_address .= $order->company."\n";
        else
        	$client_address .= $order->first_name." ". $order->last_name."\n";
        	
        $client_address .= $order->billing_address1."\n";
        
        if($order->billing_address2 != "")
            $client_address .= $order->billing_address2."\n";
            
        $client_address .= $order->billing_suburb." ".$order->billing_postcode."\n".$order->billing_state.", Australia\n";
        /*
        if($order->phone != "")
            $client_address .= "Phone: ". $order->phone."\n";
            
        if($order->fax != "")
            $client_address .= "Fax: ".$order->fax."\n";
        */
        if($order->email != "")    
            $client_address .= "Email: ".$order->email."\n";
         
        //delivery info
        $delivery_to = "";
        //$delivery_to .= $order->first_name." ". $order->last_name."\n".$order->address."\n".$order->suburb.", ".$order->state." ".$order->postcode."\n";
        
        if($order->company != "")
        	$delivery_to .= $order->company."\n";
        else
        	$delivery_to .= $order->first_name." ". $order->last_name."\n";
        	
        $delivery_to .= $order->delivery_address1."\n";
        
        if($order->delivery_address2 != "")
            $delivery_to .= $order->delivery_address2."\n";
            
        $delivery_to .= $order->delivery_suburb." ".$order->delivery_postcode."\n".$order->delivery_state.", Australia\n";
        
        if($order->email != "")    
            $delivery_to .= "Email: ".$order->email."\n";
       
        /*$owner_details = $this->settings_model->get_details_array("owner_details"); 
        $company_details = $owner_details['company_name']."\n".
        				   $owner_details['address1']."\n".$owner_details['address2']."".
        				   $owner_details['suburb'].", ".$owner_details['postcode']."\n".
        				   $owner_details['state'].", ".$owner_details['country']."\n".
        				   "Phone: ".$owner_details['phone']."\n"; */
        //$delivery_to .= $order->first_name." ". $order->last_name."\n".$order->address."\n".$order->suburb.", ".$order->state." ".$order->postcode."\n";
            
        $data = array(            
            "[INVOICE_NUMBER]"  => $order_id,
            "[INVOICE_DATE]"    => $order_date,
            "[CUSTOMER_ID]"     => $order->user_id,
            "[CLIENT_ADDRESS]"  => $client_address,
            "[DELIVERY_TO]"     => $delivery_to,
            "[SUBTOTAL]"        => "$". $order->order_subtotal,
            "[DISCOUNT]"        => "",
            "[TAX_TOTAL]"       => "$".$order->order_tax_amount,
            "[TOTAL]"           => "$".$order->order_total,   
            "[COMMENTS]"        => $order->comments         
        );
        
        $items = array();
        
        
        
        if($order_items)
        {
            //show we include GST?
            $include_gst = TRUE;
        
            $CI = &get_instance();
            $CI->load->model("login_model");
            
            // check the user country, if is Australia then we add GST also 
            $user_id = $this->login_model->getSessionData('user_id', 'user');
            if(is_numeric($user_id)) 
            {
                $user = $this->login_model->getUserDetails($user_id);
                if($user && $user->country != AU_CONTRY_ID)
                    $include_gst = FALSE;                                       
            }
            //end include GST
            
            $no_item = 1;
            $free_products = array();              
            foreach($order_items->result() as $row)
            {  
                if( !$row->is_free )
                {                  
                    $product_details = $this->product_model->get_details($row->product_id);
                    $items[] = array($no_item, $row->product_name, $row->quantity, "$".$row->item_subtotal, "$".(($include_gst) ? $row->item_total * GST : "0"), "$".$row->item_total);                                                
                    ++$no_item;
                }
                else
                    $free_products[] = $row;    
                    
            }
            
            //free products
            foreach($free_products as $row)
            {
                $product_details = $this->product_model->get_details($row->product_id);
                $items[] = array($no_item, $row->product_name, $row->quantity, "$".$row->item_subtotal, "$".(($include_gst) ? $row->item_total * GST : "0"), "$".$row->item_total);                                                
                ++$no_item;
            }
        }           
                        
        $pdfgenerator = new PDFGenerator();
        $invoice_name = "invoice_".$order_id."_".time().".pdf";
        
        $pdfgenerator->Generate($data,$items,$invoice_name);                
        
        return $invoice_name;
    }
    
    /**
     * @method    get_created_years
     * @access    public
     * @desc    this method returns the years when an orders were made
     * 
     * @version 1.0
     * @return     
     */
    public function get_created_years()
    {
        $this->db->select('YEAR(created_date) as year');
        $this->db->group_by('YEAR(created_date)');
        $this->db->order_by('YEAR(created_date) desc');
        
        $query = $this->db->get('nc_order_header');
        
        return ($query->num_rows() > 0) ? $query : false;
            
    }
    
    /**
     * @method    get_product_sales
     * @access    public
     * @desc    this method returns in a specific year and month the sales that were made in that period;
     *             if the month is -1 than we calculate all the sales in the given year
     * 
     * @param     int                $year                - the year when we want to know the sales
     * @param     int                $month                - the month when we want to know the sales
     *  
     * @version 1.0
     * @return     
     */
    public function get_product_sales( $year, $month )
    {
        if( $month == -1 )
        {
            $start_month = '01';
            $end_month     = '12';
        }
        else
        {
            $start_month = $month;
            $end_month     = $month;
        }
        
        $this->db->from('order_header as oh');
        $this->db->join('order_items as oi', 'oi.order_id = oh.id', 'inner');
        $this->db->join('products as p', 'p.product_id = oi.product_id', 'inner');
        $this->db->join('product_categories as pc', 'pc.product_category_id = p.product_category_id', 'inner');
        $this->db->where('oh.order_status', 'completed');
        $this->db->where("DATE(created_date) BETWEEN ( '$year-$start_month-01' ) AND  ( '$year-$end_month-31' )");
                             
        $query = $this->db->get();
       
        return ($query->num_rows() > 0) ? $query : false;
    }
    
    /**
     * @method    sales_by_month
     * @access    public
     * @desc    this method returns the number of sales in every month by a given year
     * 
     * @param     int                $year                - the year that we want to analyze
     * 
     * @version 1.0
     * @return     
     */
    public function sales_by_month( $year = '' )
   {
       
       if( $year == '' )
           $year         = date('Y'); 
       
       $sql=                  "SELECT   (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-01-01' ) AND  ( '$year-01-31' ) AND order_status = 'completed' 
                                        ) AS jan,
                                        
                                        (
                                        SELECT SUM(order_total)
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-02-01' ) AND  ( '$year-02-29' ) AND order_status = 'completed' 
                                        ) AS feb,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-03-01' ) AND  ( '$year-03-31' ) AND order_status = 'completed' 
                                        ) AS mar,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-04-01' ) AND  ( '$year-04-30' ) AND order_status = 'completed' 
                                        ) AS apr,
                                        
                                        (
                                        SELECT SUM(order_total)
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-05-01' ) AND  ( '$year-05-31' ) AND order_status = 'completed' 
                                        ) AS mai,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-06-01' ) AND  ( '$year-06-30' ) AND order_status = 'completed' 
                                        ) AS jun,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-07-01' ) AND  ( '$year-07-31' ) AND order_status = 'completed' 
                                        ) AS jul,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-08-01' ) AND  ( '$year-08-31' ) AND order_status = 'completed' 
                                        ) AS aug,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-09-01' ) AND  ( '$year-09-30' ) AND order_status = 'completed' 
                                        ) AS sep,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-10-01' ) AND  ( '$year-10-31' )  AND order_status = 'completed'
                                        ) AS oct,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-11-01' ) AND  ( '$year-11-31' ) AND order_status = 'completed' 
                                        ) AS nov,
                                        
                                        (
                                        SELECT SUM(order_total) 
                                        FROM nc_order_header
                                        WHERE DATE(created_date) BETWEEN  ( '$year-12-01' ) AND  ( '$year-12-31' ) AND order_status = 'completed' 
                                        ) AS dece ";
                                               
       $sql .= "LIMIT 1 ";  
       $query = $this->db->query($sql);
       //echo $this->db->last_query();    
       return ($query->num_rows() > 0) ? $query->row() : false;
   }          
}

include(APPPATH."controllers/PDFGenerator.php"); // include item class