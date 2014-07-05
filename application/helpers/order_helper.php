<?php

 	function get_status($isAdmin = true, $delivery_method = '')
    {
       $arr_data[''] = 'Choose';   
       $arr_data['pending'] = 'Pending';
       
       if($delivery_method == '')
       {
            $arr_data['printed'] = 'Printed';           
            $arr_data['emailed'] = 'Emailed';            
       }       
       else       
       {
           if(trim(strtolower($delivery_method)) == 'print')
              $arr_data['pending'] = 'Printed';
           else   
              $arr_data['emailed'] = 'Emailed';          
       }
       
       if($isAdmin) 
       {
         	$arr_data['failed'] 	= 'Failed';
          	$arr_data['completed'] 	= 'Completed';
       }          
         
       return $arr_data;  
    }
    
    function get_payment_method( $payment_id )
    {
    	$CI =& get_instance();
		
		//load document model
		$CI->load->model( 'payments/payment_model' );
		
		//get payment detail
		$payment_obj = $CI->payment_model->get_details( $payment_id );
		
		echo ($payment_obj) ? $payment_obj->name : '';
    }
    
    function payments_multiselect( $select_name, $size )
    {
    	$CI =& get_instance();
		
		//load document model
		$CI->load->model( 'payments/payment_model' );
		
		//get paymnet list
		$payments_obj = $CI->payment_model->get_list( array('1'=> '1') );
		$payment_data = $payments_obj->result();
		
		foreach( $payment_data as $item)
			$payments[$item->payment_system_id] = $item->name;
			
		//get accepted paymnet list
		$payments_obj = $CI->payment_model->get_list();
		$payments_accepted = array();
		if($payments_obj)
		{
			$payment_data = $payments_obj->result();
			
			foreach( $payment_data as $item)
				$payments_accepted[] = $item->payment_system_id;
		}
			
		$extra = '" size="'.$size.'" ';
		echo form_multiselect( $select_name, $payments, $payments_accepted, $extra);
    }
  	
    /**
	 * @method	sales_month_barchart
	 * @desc	this method gets an array with the montly sales and it processes so 
	 * 			that it can be used for the bar chart 
	 * @author	
	 * 
	 * @param 	array		$statistics			- an array with the montly sales
	 * 
	 * @version	1.0
	 * @return	array		-	a simple array containing the values which is required for the bar chart
	 */
	
	function sales_month_barchart( $statistics = array(), &$max )
	{
		$tmp_max 	= 0;
		$ret		= array();
		
		if( !empty( $statistics ) && $statistics )
		{
			foreach ( $statistics as $key => $val )
			{
				$ret[]	= floatval( $val );
                if( floatval($val) > $tmp_max )
					$tmp_max = floatval($val);
			}
			$max 	= $tmp_max + 1;
		}
		
		return $ret;
	}
	
	/**
	 * @method	sold_products_piechart
	 * @desc	this method gets an array with the sold products and it processes so 
	 * 			that it can be used as for the pie chart 
	 * @author	
	 * 
	 * @param 	array		$statistics			- an array with the sold products
	 * 
	 * @version	1.0
	 * @return	array		-	a simple array containing the values which is required for the pie chart
	 */
	
	function sold_products_piechart( $statistics = array() )
	{
		$CI =& get_instance();
		$CI->load->model('productmanager/product_model');
		
		$ret	= array();
		if( !empty( $statistics ) )
		{
			if( $statistics )
			{
				$categories	= $CI->product_model->get_category_list();
				if( $categories )
				{
					foreach ( $categories->result() as $category )
					{
						$nr = 0;
						foreach ( $statistics->result() as $stat )
						{
							if( $stat->product_category_id == $category->product_category_id )
								$nr += intval( $stat->quantity );
						}
						$ret[] = new pie_value( intval( $nr ), $category->name.': '.$nr );
					}
				}
			}
		}
		 
		return $ret;
	}
	
	/**
	 * @method	sold_products_piechart
	 * @desc	this method gets an array with the sold products and it processes so 
	 * 			that it can be used as for the pie chart 
	 * @author	
	 * 
	 * @param 	array		$statistics			- an array with the sold products
	 * 
	 * @version	1.0
	 * @return	array		-	a simple array containing the values which is required for the pie chart
	 */
	
	function sold_products_barchart( $statistics = array(), &$max, &$products_name )
	{
		$CI =& get_instance();
		$CI->load->model('productmanager/product_model');
		
		$tmp_name = array();
		$tmp_max = 0;
		$ret	= array();
		
		if( !empty( $statistics ) )
		{
			if( $statistics )
			{
				$products	= $CI->product_model->get_list();
				if( $products )
				{
					foreach ( $products->result() as $product )
					{
						$nr = 0;
						foreach ( $statistics->result() as $stat )
						{
							if( $stat->product_id == $product->product_id )
								$nr += intval( $stat->quantity );
						}
						
						$ret[$product->product_name] = intval( $nr );
						
						if( intval( $nr ) > $tmp_max )
							$tmp_max = intval( $nr );
					}
				}
			}
		}

		$max = $tmp_max+1;
		$products_name 	= array();
		$ret_array		= array();
		
		array_multisort( $ret, SORT_DESC );
		$ret = array_slice( $ret, 0, 10 );
		
		foreach ( $ret as $key => $value )
		{
			$products_name[] = $key;
			$ret_array[]	 = $value;	
		}
		
		return $ret_array;
	}
