<?php

	function is_used_coupon_id( $coupon_id )
    {
    	$CI = &get_instance();
    	
    	$used_coupon_ids = $CI->session->userdata("used_coupon_ids");
        $ids = explode(";", $used_coupon_ids);
        return in_array( $coupon_id, $ids );
        	
    }
    
    function add_to_used_cupon_ids( $coupon_id )
    {
    	$CI = &get_instance();
    	
    	$used_coupon_ids = $CI->session->userdata("used_coupon_ids");
        $ids = explode(";", $used_coupon_ids);
        if (!in_array( $coupon_id, $ids ))
        {
        	array_push( $ids, $coupon_id);
        	$CI->session->set_userdata("used_coupon_ids", implode(";", $ids));
        }
    	
    }
    
    function remove_from_used_coupon_ids( $coupon_id )
    {
    	$CI = &get_instance();
    	
    	$used_coupon_ids = $CI->session->userdata("used_coupon_ids");
        
    	if (strpos($used_coupon_ids, ";".$coupon_id) !== false )
    	{   
    		$used_coupon_ids = str_replace(";".$coupon_id, "", $used_coupon_ids);
            $CI->session->set_userdata("used_coupon_ids", $used_coupon_ids);
        }
    }

    function coupon_id_applies( $coupon_id, $coupon_type )
    {
    		$CI = &get_instance();	
    		
    		switch( $coupon_type )
            {
                case 'percentage':
                    $coupon_ok = FALSE;        
                break;
                        
		        case 'value':
                    $coupon_ok = FALSE;        		        	
		        break;
		                        
		        case 'products':
		        	
		            //check if the coupon applies to the cart
		            $buy_products = $CI->coupon_model->get_list_coupon_discount_products($coupon_id, 'buy', "p.product_id, cdp.qty, p.product_name");
		                            
		             $coupon_ok = TRUE;
		             if ($buy_products)
		             {
		               //check if the product ids from the coupon products are also in cart
		               foreach( $buy_products as $product )
		               {	
		                    if ($coupon_ok == TRUE)
		                    {
		                       $coupon_ok = FALSE;
			                   foreach($CI->cart->contents() as $item)
			                   {
			                            			
			                       if ($item['id'] == $product->product_id && $item['qty'] >= $product->qty)
			                       {
			                           $coupon_ok = TRUE;
			                       }
		                       }
			                            		
		                     }	
			                            		   
		                 }
		               }
            		   else
               		   {
                 			$coupon_ok = FALSE;
               		   }
		        	
		        break;
                
                case 'amount':
                    $coupon_details = $CI->coupon_model->get_details($coupon_id);
                    $cart_info = $CI->product_model->get_cart_info();
                    $coupon_ok = FALSE; 
                    
                    if( $coupon_details )
                    {
                        if( $cart_info['total'] >= $coupon_details->discount)    
                            $coupon_ok = TRUE;    
                    }
                    
                break;
            }
    		
               

           return $coupon_ok;
    }
    
    function add_reward_products( $coupon_id )
    {
    		$CI = &get_instance();
    		             
    		$coupon = $CI->coupon_model->get_details($coupon_id);
            $coupon_name = ($coupon) ? $coupon->coupon_code : "";
    		$reward_products = $CI->coupon_model->get_list_coupon_discount_products($coupon_id, 'reward', "p.product_id, cdp.qty, p.product_name, p.rrp_price");
            $isOK = FALSE;
            
            foreach( $reward_products as $product )
            {
                   //add reward products to cart
                   $data = array(
                   			'id'      => $product->product_id * (-1),
		                   	'qty'     => $product->qty,
		                   	'price'   => "0.00",
		                   	'name'    => $product->product_name,// ." ". $coupon->coupon_code. " Coupon",
	                        'options' => array("coupon_id" => $coupon_id )
		                );
		                	
                    //print_r($data);        		
					$isOK = $CI->cart->insert($data);                    
             }
                          
             //add the coupon to the used coupon ids array in session
             if (!is_used_coupon_id($coupon_id) && $isOK)
                  add_to_used_cupon_ids($coupon_id); 
           	 
             
    }
    
    //this function check the used coupons the the cart content, if the cart content has changed and a used coupon
    //doesn't applies anyomore, the products associated to the coupon are removed 
    function check_used_coupons()
    {
    	$CI = &get_instance();
    	
    	$used_coupon_ids = $CI->session->userdata("used_coupon_ids");
        $ids = explode(";", $used_coupon_ids);
        $coupon_type = '';
        
        foreach ($ids as $coupon_id)
        {
        	if (is_numeric($coupon_id))
        	{
                $coupon_details = $CI->coupon_model->get_details($coupon_id);
                if($coupon_details)
                    $coupon_type = $coupon_details->discount_type;
                    
        		if (!coupon_id_applies($coupon_id, $coupon_type))
	        	{                       
	        		remove_coupon_products( $coupon_id );
	        	}
        	}
        }    	
    }
    
    function remove_coupon_products( $coupon_id )
    {
    	$CI = &get_instance();
    	
    	foreach($CI->cart->contents() as $item)
	    {
	        if (isset( $item['options'] ) && array_key_exists("coupon_id", $item["options"]) && $item["options"]["coupon_id"] == $coupon_id)
	        {
	        	$data = array(
                   'rowid' => $item['rowid'],
                   'qty'   => 0
                );
                
                $CI->cart->update($data);
                
                remove_from_used_coupon_ids( $coupon_id );
	        }
	    }                    			
  
            
    }
    
    /**
    * @method check_not_required_coupons
    * @desc this function checks the not required coupons and if the cart contents one of the not required 
    * coupons then we'll use it
    */
    
    function check_not_required_coupons()
    {
        $CI = &get_instance();
        
        $not_required_coupons = $CI->coupon_model->get_list("", "", $count_all, $coupon_code_required = 0);    
        
        if($not_required_coupons)
        {
            foreach( $not_required_coupons->result()  as $row)
            {
                $coupon_id = $row->coupon_id;
                $coupon_ok = coupon_id_applies($coupon_id, $row->discount_type);
                                  
                if ($coupon_ok)
                {
                    add_reward_products($coupon_id );
                }   
            }
        }
    }
    
    /**
    * @method get_number_of_coupons
    * @desc This function return the number of coupons
    * 
    */
    function get_number_of_coupons()
    {
        $CI = &get_instance();
        
        $used_coupon_ids = $CI->session->userdata("used_coupon_ids");
        $ids = array();
        
        if($used_coupon_ids != "")
            $ids = explode(";", $used_coupon_ids);
                
        return ($ids) ? count($ids) : 0;
    } 
    
    /**
    * @method is_used_coupon_id_in_orders
    * @desc This function returns TRUE if the coupon id it can be used multiple times else FALSE
    * 
    * @param mixed $coupon_id
    */
    function is_used_coupon_id_in_orders($coupon_id)
    {
        $CI = &get_instance();
        
        return $CI->coupon_model->is_used_coupon($coupon_id);            
    }

?>