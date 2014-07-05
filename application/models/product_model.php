<?php
  /**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
*/
class Product_model extends CI_Model 
{
    function Product_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($enabled = -1, $product_category_id = -1, $limit = "", $page_no = "", $product_id = "", $params = array() )
    {
        $select = "";
        $where = "";
        
        $arr_points = array(
                                'product_name'      => 20,
                                'tags'              => 10,
                                'description'       => 1, 
                                'd.document_name'   => 10
                           );
        
        if(isset($params["search_term"]) && $params["search_term"] != "")
        {    
            //explode the term
            $arr_terms = explode(" ", $params["search_term"]);
            
            if(count($arr_terms) > 0)
            {
                foreach($arr_terms as $row) 
                {
                    if($row != "")    
                    {
                        //create select
                        if($select != "")
                            $select .= "+ ";
                        
                        $i = 0;
                        foreach( $arr_points as $key => $value )
                        {
                            
                            if( $i != 0)
                                $select .= " +";
                            
                            if( $key == 'd.document_name' )
                                $select .= " IFNULL((LENGTH( $key ) - LENGTH(REPLACE( lower($key), '".strtolower($row)."', ''))) / LENGTH('".$row."') * ".$value. ",0)";                            
                            else                                            
                                $select .= " (IF( INSTR( lower($key), '".strtolower($row)."') > 0, $value, 0))";
                            
                            
                            $i++;
                        }
                        
                        //create where
                        if($where != "")
                            $where .= " OR ";
                        
                        $where .= "lower(product_name) LIKE '%".strtolower($row)."%' OR lower(tags) LIKE '%".strtolower($row)."%' OR lower(description) like '%".strtolower($row)."%' OR lower(d.document_name) like '%".strtolower($row)."%'";
                    }               
                }
                                
                if($select != "") $select .= " AS `count` ";           
            }
            
            if($select != "") $select = "," . $select;
        }
        
        
        $this->db->select("nc_products.* " . $select, false);        
        
        $this->db->from("nc_products");
        
        if(isset($params["search_term"]) && $params["search_term"] != "")
        {
            $this->db->join("nc_documents d", "d.foreign_id = product_id AND d.document_type = 'product_files'", 'left');
            $this->db->group_by("product_id");
        }    
        
        if($product_category_id != -1)
            $this->db->where("product_category_id", $product_category_id);
            
        if($enabled != -1)                
            $this->db->where("active", $enabled);
            
        if(($product_id != "") && (is_numeric($product_id)))
            $this->db->where("product_id", $product_id);              
        
        if(isset($params["show_on_downloads"]) && $params["show_on_downloads"] != "")    
            $this->db->where("show_on_downloads", $params["show_on_downloads"]);            
        
        if($where != "")    
            $this->db->where($where);
            
        if($select != "")
            $this->db->order_by("`count`","desc");  
                       
            
        $query = $this->db->get();
        
        return ($query->num_rows() > 0) ? $query : false;                    
    }
    
    function get_details($product_id = "", $get_category_name = false)
    {
        if($get_category_name)
            $this->db->select("p.*,pc.product_category_id, pc.name as product_category_name");

        if ($product_id != "")
        {
	        if( is_numeric($product_id) )
	        	$this->db->where("product_id", $product_id);
	        else
	        	$this->db->where("model_number", $product_id);
        }
        	
        $this->db->from("nc_products p");    
        
        
        if($get_category_name)
            $this->db->join("nc_product_categories pc","p.product_category_id = pc.product_category_id");            
        
        $this->db->limit(1);
        
        $query = $this->db->get();  
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    public function save($product_id,$data)
    {
        if (is_numeric($product_id))
        {   
            $this->db->where('product_id',$product_id);
            $this->db->update('nc_products',$data);
            return $product_id;
        }
        else
        {   
            $this->db->insert('nc_products',$data); 
            return $this->db->insert_id();
        }
    }
    
    public function get_categories($parent_id = -1)
    {
        $this->db->where('parent_id',$parent_id);
        $this->db->order_by("seq_no", "ASC");
        $query = $this->db->get("nc_product_categories");
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }

    public function get_all_categories($category_id = '')
    {
        if($category_id != '' )
            $this->db->where_not_in('product_category_id', $category_id);

        $query = $this->db->get("nc_product_categories");

        if ($query->num_rows() > 0)
        {
            return $query;
        }
        else
            return false;
    }
    
    public function category_exists($category,$find_by_name = true)
    {
        $this->db->select('product_category_id');
        if($find_by_name)
            $this->db->where('name',$category);
        else //find by id
            $this->db->where('product_category_id',$category);
                
        $query = $this->db->get('nc_product_categories');
        
        return ($query->num_rows() > 0);
    }
    
    public function product_exists($product, $find_by_name = false)
    {
        $this->db->select('product_id');
        
        if($find_by_name)
            $this->db->where("product_name", $product);
        else    
            $this->db->where('product_id',$product);
        
        $query = $this->db->get('nc_products');
        
        return ($query->num_rows() > 0);
    }
    public function add_category($category_name,$parent_id)
    {
        $data = array(
            'name' => $category_name,
            'parent_id' => $parent_id
        );
        
        $this->db->insert("nc_product_categories",$data);        
    }
    public function delete_categories($arr_categories)
    {
        $this->db->where_in("product_category_id",$arr_categories);
        $this->db->delete("nc_product_categories");
    }
    
    public function get_products($category_id = -1, $limit = "", $page_no = "", &$count_all = 0)
    {
        $count_all = $this->count_products($category_id);

        $this->db->from("nc_products p");
        $this->db->from("nc_product_categories pc");

        $this->db->where("p.product_category_id", $category_id);

        $this->db->join("nc_product_categories","p.product_category_id = pc.product_category_id");
        $this->db->group_by("p.product_id");

        if ($limit != "" && $page_no!= "" && $count_all > $limit)
		{
			$this->db->limit(intval($limit), intval(($page_no-1) * $limit));
		}

        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false; 
    }

    function count_products($category_id)
    {

        $query = $this->db->get_where('nc_products',array('product_category_id'=>$category_id));
        if ($query->num_rows() == 0)
		{
			return '0';
		}
		
		return $query->num_rows();

    }
    
    public function delete_products($arr_products)
    {
        $this->db->where_in("product_id",$arr_products);
        $this->db->delete("nc_products"); 
    }
    
    public function get_category_details($category_id = "" , $category_name = "")
    {
        if($category_id != "")
            $query = $this->db->get_where("nc_product_categories",array("product_category_id"=>$category_id),1);
        else
            $query = $this->db->get_where("nc_product_categories",array("lower(name)"=>strtolower($category_name)),1, false);    
        
        if ($query->num_rows() > 0)
        {
            return $query->first_row();
        }         
        else
            return false;
        
    }
    
    function get_hero_image($product_id)
    {
        $this->db->select('hero_image');
        $query = $this->db->get_where('nc_products',array('product_id' => $product_id),1);
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
            return $result->hero_image;
        }         
        else
            return "";
    }
        
    
    //generate bradcrumbs for a given category_id
    public function generete_breadcrumbs($category_id, $level = 1)
    {
        $breadcrumb = "";
        
        if ($category_id == -1)
            return '<li><a href="'.base_url().'admin_productmanager">Home</a></li>';
        else
        {
           $category_detail =  $this->get_category_details($category_id);
           if ($category_detail)
           {
               
           
                   if($level == 1)
                      $breadcrumb = '<li>'.$category_detail->name.'</li>'.$breadcrumb;
                   else 
                      $breadcrumb = '<li><a href="'.base_url().'admin_productmanager/category/'.$category_detail->product_category_id.'">'.$category_detail->name.'</a></li>'.$breadcrumb;
               
           }
           else
                $this->error_model->report_error("Sorry.", "Error generating breadcrumbs (recursion)");
           
           $parent_name = $this->generete_breadcrumbs($category_detail->parent_id, ++$level);
           $breadcrumb =  $parent_name.$breadcrumb;
           
        }
        
        return $breadcrumb;
        
    }
    
    public function get_price( $product_id)
    {
        $this->db->select('price');
        $query = $this->db->get_where('nc_products',array('product_id' => $product_id),1);
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            $result = $query->first_row();
            return $result->price;
        }         
        else
            return 0;
    }
    
	/***
	* category_exists_bycode checks whether an existing product category exists with the 
	* specified category code
	* 
	* @param mixed $category_code	The category_code to check the existance of.
	* @param mixed $parent_id
	*/
    public function category_exists_bycode($category_code, $parent_id = -1)
    {
        $where = array("category_code" => $category_code);
        
        // See if we want to restrict our duplication test to a particular parent id.
        if($parent_id > 0)
        	$where["parent_id"] = $parent_id;
        
        $query = $this->db->get_where("nc_product_categories", $where, 1);

        if($query->num_rows() > 0)               
        	return $query->row();
        else
        	return false;
    }    
    
    public function get_all_pricings($product_id, $access_level = "")
    {
    	$this->db->order_by( 'broadcast_access_level_id' );
    	$this->db->order_by( 'bracket_max' );
    	
    	$this->db->where( 'product_id', $product_id );
    	
    	if( $access_level != "" )
    		$this->db->where( 'broadcast_access_level_id', $access_level );
    	
    	$query = $this->db->get('nc_product_price');
        
    	if($query->num_rows()>0)
    	{
    		return $query;
    	}	
    	else
    	{
    		return false;
    	}
    }
    
    public function get_min_quantity($product_id, $access_level = "")
    {
        $this->db->select( 'min(bracket_max) as min_qty');        
        
        $this->db->where( 'product_id', $product_id );
        
        if( $access_level != "" )
            $this->db->where( 'broadcast_access_level_id', $access_level );
        
        $this->db->limit(1);
        $query = $this->db->get('nc_product_price');
        
        if($query->num_rows()>0)
        {
            $result = $query->row();
            return $result->min_qty;
        }    
        else
        {
            return false;
        }
    }
    
    /**
     * @method	delete_product_price
     * @access	public
     * @desc	this method deletes a product price
     * @author	Zoltan Jozsa
     * @param 	mixed					$product_price_id				- the id of the product price
     * @return 	boolean
     */
    public function delete_product_price( $product_price_id = '' )
    {
    	if( empty( $product_price_id ) )
    		return FALSE;
    	
    	if( is_array( $product_price_id ) )
    		$this->db->where_in( 'product_price_id', $product_price_id );
    	else
    		$this->db->where( 'product_price_id', $product_price_id );
    		
    	return $this->db->delete( 'product_price' );
    }
    
    /**
     * @method	save_product_price
     * @access	public
     * @desc	this method updates an exist or add a new product price
     * @author	Zoltan Jozsa
     * @param 	int					$product_price_id				- the id of the product price
     * @param	array				$data							- the data to insert or update
     * @return 	boolean
     */
    public function save_product_price( $product_price_id = '', $data = array() )
    {
    	if( !empty( $product_price_id ) ) // update
    	{
    		$this->db->where( 'product_price_id', $product_price_id );
    		$this->db->update( 'product_price', $data );
    	}
    	else // insert new
    	{
    		$this->db->insert( 'product_price', $data );
    	}
    }
    
    /**
     * @method    get_cart_info
     * @access    public
     * @desc      this method returns information about cart (cart products, subtotal, gst, total)
     * @author    Agnes Konya
     
     * @return    array
     */
    public function get_cart_info()
    {
        $CI = &get_instance();
        $CI->load->model("login_model");
        
        $cart_info['cart_products'] = $this->cart->contents();
        $cart_info['subtotal'] = $this->cart->total();
        $cart_info['gst'] = number_format($cart_info['subtotal']*GST,2,".","");
        $cart_info['include_gst'] = TRUE;
        
        /* check the user country, if is Australia then we add GST also */
        $user_id = $this->login_model->getSessionData('user_id', 'user');
        if(is_numeric($user_id)) 
        {
            $user = $this->login_model->getUserDetails($user_id);
            if($user && $user->country != AU_CONTRY_ID)
            {
                $cart_info['gst'] = 0;
                $cart_info['include_gst'] = FALSE;
            }   
        }
        
       	$cart_info['total'] = number_format($cart_info['subtotal']+$cart_info['gst'],2,".","");
        
       	return $cart_info;
    }
    
    /**
     * @method    get_total_number_of_items
     * @access    public
     * @desc      this method returns the total number of items in the cart
     * @author    Agnes Konya
     
     * @return    array
     */
    public function get_total_number_of_items()
    {
        $total_qty = 0;
                
        $cart_products = $this->cart->contents();
        
        if(isset($cart_products) && !empty($cart_products)){ 
        
            foreach($cart_products as $item) 
                $total_qty += $item['qty'];                    
        }
        //$this->cart->total_items() - Displays the total number of items in the cart.    
        
        return $total_qty; 
    }
    
    /**
    * @method    save_device
    * @access    public
    * @desc      this method saves or upates a device
    * @author    Agnes Konya

    * @return    array
    */
    public function save_device( $data, $device_id = '' )
    {
        if (is_numeric($device_id))
        {   
            $this->db->where('device_id', $device_id);
            $this->db->update('nc_devices', $data);
            return $product_id;
        }
        else
        {   
            $this->db->insert('nc_devices', $data); 
            return $this->db->insert_id();
        }       
    }
    
    /**
     * @method    get_category_list
     * @access    public
     * @desc    this method returns a list of product categories by filter params array or parent category id
     * 
     * @author    Agnes Konya
     * @param     mixed                $params                -  parent category id
     * 
     * @version 1.0
     * @return     array with objects of products
     */
    public function get_category_list( $category_id = "" )
    {
        if( $category_id != "" )
            $this->db->where('parent_id', $category_id);
            
        $query = $this->db->get('product_categories');
        return ($query->num_rows() > 0) ? $query : false;                    
    }
}
