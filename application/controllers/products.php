<?php
die("OFFLINE");
class Products extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    public $website_code;
   
    
    function Products()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();
        
        global $website;	// Read the website code from the globals  
        $this->website_code = $website; 
        
        // Load models etc
        $this->load->model( "product_category_model" );
        $this->load->model( "document_model" );
        $this->load->model( "product_model" );  
		$this->load->model( "menu_model" );
		$this->load->model( "blocks_model" );
		$this->load->library( "Image" );
		$this->load->model( "region_model" );
		$this->load->model( "article_category_model" );  
		$this->load->model( "article_model" );
		$this->load->model( "users_model" ); 
        $this->load->model( "coupon_model" );  
		$this->load->model( "login_model" );  

		$this->load->helper('coupon_helper');

		$this->data = $this->product_model->get_cart_info();
    }
    
    /***
    * The category function displays an individual product
    * 
    * @param string $product_code  The product code of the product to show.
    */
    function category( $product_code )
    {	
    	// Ensure a valid product code was passed
		if($product_code == "")
			$this->tools_model->report_error("Sorry, the url seems to be invalid.", "Products/category - the Product catgory code was missing");
		
		$product_query = $this->product_model->get_details($product_code);
		if(!$product_query)
		{
			// The page could not be loaded. Redirect the user to the page-not-found page.			
			redirect("/page/page-not-found");
			exit();
						
			//show_error("Sorry, we could not find the specified product category.  Please press back and try again.");
		}
			
		$this->load->model("accesslevel_model");
			
		$product = $product_query->row();
		
		// Make sure the product is active.
		if(!$product->active)
		{
			// The page could not be loaded. Redirect the user to the page-not-found page.			
			redirect("/page/page-not-found");
			exit();			
			//show_error("Sorry, this product is not currently available");
		}
		
		// Load linked articles
		$articles = false; 
		
		if( (!empty($product->article_category_id)) && ($product->article_category_id != "") && (is_numeric($product->article_category_id)))
			$articles = $this->article_model->get_articles_from_category( $product->article_category_id );
		
		// Load the product category.
		if($product)
		{
			$category = $this->product_category_model->get_details( $product->product_category_id );	
			$this->__prepareProduct($product);
		}
			
		if(!$category)
		{
			$this->tools_model->report_error("Sorry, the category could not be loaded.", "Products/category - the Product catgory with code $product_code could not be loaded");
		}
		
        $this->data["meta_keywords"] = "Command Fusion, " . $product->product_name; 
        $this->data["meta_description"] = "Command Fusion - " . $product->product_name . ". " . prepare_for_metadesc($product->short_description);
        $this->data["meta_title"] = "Command Fusion - " . $product->product_name;

        $this->data["gallery_files"] = $this->document_model->get_list("gallery_image", $product->product_id);
        
        //check user file permission acces level
        $user_log = $this->session->userdata("user");
        $params = array();
        if( $user_log )
        {
            $user = $this->users_model->get_details($user_log['user_id']);
            $file_permission_level = $user->file_permission_access_level;            
        }                         
        
        //$params["broadcast_access_level_id"] =  isset($file_permission_level) ? $file_permission_level : -1;
        
        $this->data["download_files"] = $this->document_model->get_list("product_files", $product->product_id, $params);
		
        $this->data["articles"] = $articles;
		$this->data["category"] = $category;
		$this->data["product"] = $product;
		$this->data["product_id"] = $product->product_id;
		$this->data["nav_main"] = $this->menu_model->get_menu_html_extended( HEADER_MENU, HOME_MENU_ITEM);
		$this->data['categories'] = $this->product_category_model->get_list("", "", "", true);
		
		/* get the pricing table */
		//get the user id
		//$user_id = DEFAULT_USER_ID;
		//get user details
		//$user = $this->users_model->get_details($user_id);
		
		$this->data['prices'] = false;
		
		$user_log = $this->session->userdata("user");
		
		
		if( $user_log )
		{
			$user = $this->users_model->get_details($user_log['user_id']);
			$access_level = $user->broadcast_access_level_id;
			
			//get all the price for the product
			$prices = $this->product_model->get_all_pricings( $product->product_id, $access_level );
			
			if((!$prices) && ($access_level > 1))
			{
				// There's no pricing for this users access level.
				// Load all access levels that have a LOWER order and recurse until we find an access level
				$current_level = $access_level - 1;
				$found = false;
				
				$levels = $this->accesslevel_model->get_list($limit = "", $page_no = "", $count_all, $order_by = "seqno DESC", $max_seq = $current_level);
				
				// Loop through the access levels until we find one that has pricing.  
				// Note, we're looping in reverse order due to the seqno desc query order,
				// so we will find the highest access level with prices.
				if($levels)
				{
					foreach($levels->result() as $level)
					{
						$this_level_id = $level->broadcast_access_level_id;
						$prices = $this->product_model->get_all_pricings( $product->product_id, $this_level_id);
						
						// If we have prices, end the loop.
						if($prices)
							break;	
					}
				}
			}
			
			$this->data['prices'] = $prices;
		}
		
        $this->load->view('header', $this->data);
        $this->load->view('products/prebody', $this->data); 
        $this->load->view('products/main', $this->data);
        $this->load->view('pre_footer', $this->data); 
        $this->load->view('footer', $this->data); 		
    }
    
    private function __prepareProduct(&$product)
    {
		$product->oem_description = $this->utilities->replaceTags($this, $product->oem_description);
    }
    
	function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        
        switch($type)
        {
        	
            //Add to cart 
            case 3:
                $quantity = $this->tools_model->get_value("quantity","","post",0,false);
                $product_id = $this->tools_model->get_value("product_id",-1,"post",0,false);
                $error_message = "";
                
                //check if the quntety is OK
                $quantity_msg = $this->check_quantity($product_id, $quantity);
                
                if ($quantity_msg != "")
                    $error_message = $quantity_msg;
                else
                {    
                    
                    $ret_row = $this->get_rowid($product_id);
                    //print_r($ret_row);
                    
                    if($ret_row['rowid'] != "0")
                    {
                	    $data = array(
	               		    'rowid' => $ret_row['rowid'],
	               		    'qty'   => 0
	            	    );

					    $this->cart->update($data); 
					    $quantity = $ret_row['qty'] + $quantity;
                    }
                        
                    $price = $this->get_price($product_id, $quantity);
                    $product_obj = $this->product_model->get_details($product_id);
                    $product_detail = $product_obj->row_array();
	     		    $data = array(
	                       'id'      => $product_detail['product_id'],
	                       'qty'     => $quantity,
	                       'price'   => $price,
	                       'name'    => $product_detail['product_name']
	                    );
	                    
				    $result = $this->cart->insert($data); 
                    check_not_required_coupons();
                }
				
                $this->update_cart($error_message);
            break;
            
            #empty the cart
       		case 4:
				$this->cart->destroy();
				$this->session->unset_userdata("used_coupon_ids");
              
				$this->update_order_page();         
            break;
            
            #change cart quantity
            case 5:
                $rowid = $this->tools_model->get_value("rowid","","post",0,false);
                $quantity = $this->tools_model->get_value("quantity", 0, "post", 0, false);
                 
                $row = $this->cart->get_row($rowid);
                
                $product_id = $row['id'];
                
        		$user_log = $this->session->userdata("user");
				$user = $this->users_model->get_details($user_log['user_id']);
				$access_level = $user->broadcast_access_level_id;
					
				$price = $this->get_price( $product_id, $quantity, $access_level );

				
                
                 $data = array(
                   'rowid' => $rowid,
                   'qty'   => $quantity
                
                );
                
                //$this->cart->chage_row_price($rowid, ($quantity > 0) ? $price : 0 );
                
                //check if the products is not discount product
                $options = $this->cart->product_options($rowid);
                
                //if the products is discount product, don't change the qty
                if (! (is_array($options) && array_key_exists("coupon_id", $options)))
                {
                	
                 	$this->cart->update($data); 
                 	check_used_coupons();
                    check_not_required_coupons();
                	$this->update_cart();
                } 
                            
            break;
            
            #remove one item on the cart
            case 6:
                $rowid = $this->tools_model->get_value("rowid","","post",0,false);
                $quantity = $this->tools_model->get_value("quantity","","post",0,false);
                $price = $this->get_price($product_id, $quantity);
                
		     	$data = array(
	               'rowid' => $rowid,
	               'qty'   => $quantity,
		     	   'price' => $this->get_price($product_id, $quantity)
	            );

	            //check if the products is not discount product
                $options = $this->cart->product_options($rowid);
                
                //if the products is discount product, don't remove it from the cart 
                if (! (is_array($options) && array_key_exists("coupon_id", $options)))
                {
	            	$this->cart->update($data);
                    check_not_required_coupons(); 
	              	$this->update_cart();
                }             
            break;
            
            #refresh numbers of items on navigation 
            case 7:
                $return_data         = array();
                $return_data['html'] = "";
                
                $total_qty = $this->product_model->get_total_number_of_items();
                $return_data['html'] = $total_qty ." items";
                $return_data['total_qty'] = $total_qty;
                
                echo json_encode($return_data);                
            break;
            
            //check if the user is logged in
            case 8:
                $return_data          = array();
                $return_data['error'] = "";
                
                if( !$this->login_model->is_logged_in('user') )
                    $return_data['error'] = 'You must login first.';
                
                echo json_encode($return_data);                    
            break;
            
            //get price for quantity
            case 9:
            	$qty = $this->tools_model->get_value("qty","","post",0,false);
            	$product_id = $this->tools_model->get_value("product_id","","post",0,false);
            	$user_currency = $this->login_model->getSessionData("currency", "user");
            	
            	
        		$user_log = $this->session->userdata("user");
				
        		$price = 0;
		
				if( $user_log )
				{
					$user = $this->users_model->get_details($user_log['user_id']);
					$access_level = $user->broadcast_access_level_id;
				
					
					$price = $this->get_price( $product_id, $qty, $access_level );
					
					
				}
				
				$return_data = array();
				$return_data['price'] = "";
				
				if ($price != 0 && $price != "0.00" )
				{
					$return_data['price'] = "$". ($qty * $price);
				}
				
				echo json_encode($return_data); 
            	
            	
            break;
            
            //download documents
            case 10:
                $doc_id = intval($this->tools_model->get_value("doc_id", 0, "post", 0, false)); 
                $do = $this->tools_model->get_value("do", "", "post", 0, false);
                
                $document = $this->document_model->get_details($doc_id);
                
                $do = ($do == "download")  ? "download" : "check";
                
                $return_data = array();
                $return_data["error"] = "";
                
                //check user file permission acces level
                $user_log = $this->session->userdata("user");
                
                if( $user_log )
                {
                    $user = $this->users_model->get_details($user_log['user_id']);
                    $file_permission_level = $user->file_permission_access_level;            
                }
                else
                    $return_data["error"] = "You must create and account and login before accessing downloads.";
                
                if($document &&  $return_data["error"] == "")     
                {                                          
                    if($document->document_type != "product_files")                
                        $return_data["error"] = "Invalid document";
                    else
                    {
                        
                        if( ($document->is_exact == 1 && $document->broadcast_access_level_id == $file_permission_level)  ||
                            ($document->is_exact == 0 && $document->broadcast_access_level_id <= $file_permission_level) )     
                            { 
                                if($do == "download")                                                                                                            
                                {
                                    $path = FCPATH.$document->document_path;
                                     
                                    $this->utilities->download_file($path);
                                }
                            }
                            else
                                $return_data["error"] = "You don't have permission for this document.";                        
                    }
                }
                
                if($do == "check")                
                    echo json_encode($return_data); 
            break;
            
            //search for products
            case 11:
                $search_term = $this->tools_model->get_value("search_term", "", "post", 0, false);
                $return_data = array();
                $return_data["html"] = "";       
            
                $params = array();
                $params["search_term"] = $search_term;
                
                $this->data["products"] = $this->product_model->get_list(1, -1, "", "", "", $params);
                $this->data["arr_terms"] = explode(" ", $search_term);
                $this->data["searh_page"] = TRUE;
                $return_data["html"] = $this->load->view("misc/search_page", $this->data, TRUE);
                
                echo json_encode($return_data); 
            break;
        }
    }
    
    
	
    private function update_cart($error_message = "")
    {
    	$return_data = array();
    	$cart_info = $this->product_model->get_cart_info();
		
        $return_data["html"] = $this->load->view('products/cart',$cart_info,true);
        $return_data["error_message"] = $error_message;
                    
  		echo json_encode($return_data);
    }
    
 	/*private function update_order_page()
    {
    	$return_data = array();
    	$cart_info = $this->product_model->get_cart_info();
    	
    	$order_cost = $this->session->userdata("orders_costs");
		if(isset($order_cost) && !empty($order_cost))
		{
    		$postcode = $order_cost['postcode'];
    		$shipping_method = $order_cost['shipping_method'];
    		$shipping_cost = $this->order_model->shipping_cost($postcode, $shipping_method);
			$order_total = $cart_info['total'] + $shipping_cost;
		    	
			$this->session->set_userdata("orders_costs",array("goods_total"=>$cart_info['total'],"shipping_cost"=> $shipping_cost,"postcode"=>$postcode,"total"=>$order_total, "shipping_method"=>$shipping_method));
		}
        $return_data["html"] = $this->load->view('order/order_details',$cart_info,true);
                    
  		echo json_encode($return_data);
    }*/
    
    private function get_rowid($product_id)
    {
    	$cart_products = $this->cart->contents();
    	
    	$ret['rowid'] = 0;
    	$ret['qty'] = 0;
    	foreach ($cart_products as $item)
    	{
    		if($item['id'] == $product_id)
    		{
    			$ret['rowid'] = $item['rowid'];
    			$ret['qty'] = $item['qty'];
    			return $ret;
    		}
    	}
    	return $ret;
    }
    
    private function get_price( $product_id, $quantity, $access_level = 1 )
    {
    	$prices = $this->product_model->get_all_pricings( $product_id, $access_level );
        if($prices)
        {
        	$prev_price = 0.00;
       		foreach( $prices->result() as $item )
       		{
       			if($quantity < $item->bracket_max )
       			{
       				//return $item->price;
       				return $prev_price;
       			}
       				
       			$prev_price = $item->price;
       		}
       		return $item->price;
        }
        else
        	return 0.00;
        
    }
    
    private function check_quantity( $product_id, $quantity, $access_level = 1)
    {
        $min_qty = $this->product_model->get_min_quantity( $product_id, $access_level );
        if( $min_qty )
        {            
           if( $quantity < $min_qty )
           {
                return "Please order min " . $min_qty . " from this product.";         
           }
           else
               return "";           
        }
        else
            return "No prices added to this product.";
        
            
    }
}
