<?php
class Coupon_model extends CI_Model 
{
    function Coupon_model()
    {
        // Call the Model constructor
          parent::__construct();
    }      
   
    public function get_list($limit = "", $page_no = "", &$count_all, $coupon_code_required = '')
    {
        $this->_get_list($limit, $page_no, $count_all, $coupon_code_required,  true);
        $count_all = $this->db->count_all_results();
        
        $this->_get_list($limit, $page_no, $count_all, $coupon_code_required, false);
        $this->db->order_by( 'start_date', 'desc' );
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;                
    } 
    
    function _get_list($limit = "", $page_no = "", $count_all, $coupon_code_required = '',  $only_count = false)   
    {   
        $this->db->select("c.*,w.website_name");
    	$this->db->from('nc_coupons AS c');
        $this->db->join("nc_websites AS w","c.website_id = w.website_id","left",false);
       
        /*if(!$only_count)    
            $this->db->order_by("c.coupon_code", "ASC");     
		*/
        if( is_numeric($coupon_code_required) )
            $this->db->where('c.coupon_code_required', $coupon_code_required);
        
        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }
    }       
    public function get_details($coupon_id, $by_code = FALSE, $other_where = "")
    {
        // Check to see if a record with this username exists.
        
        if($coupon_id)
        {
            $this->db->select('*,DATE_FORMAT(start_date,"%d/%m/%Y") as dmy_start_date, DATE_FORMAT(finish_date,"%d/%m/%Y") as dmy_finish_date', false);
            
            /*if($by_code)
                $query = $this->db->get_where('nc_coupons', array('coupon_code' => $coupon_id));
            else    
                $query = $this->db->get_where('nc_coupons', array('coupon_id' => $coupon_id));
        */
                
                
            $this->db->from('nc_coupons');
            $this->db->where( ($by_code)  ? "coupon_code" : "coupon_id", $coupon_id );
            
            if ($other_where != "") $this->db->where( $other_where, NULL, false );
                
            $query = $this->db->get();    
            // If there is a resulting row, check that the password matches.
            if ($query->num_rows() > 0)
            {
                return $query->row();
            }         
            else
                return false;
        }
        else
            return false;
    }
    
    public function save($coupon_id,$data)
    {
        if (is_numeric($coupon_id))
        {
            $this->db->where('coupon_id',$coupon_id);
            $this->db->update('nc_coupons',$data);
            
            return $coupon_id;
        }
        else
        {
            $this->db->insert('nc_coupons',$data);
            return $this->db->insert_id();
        }
    }
    
    public function delete($where_in)
    {
        $this->db->where(" coupon_id in (".$where_in.")",null,false);
        $this->db->delete('nc_coupons');
    }
    
    /**
    * @method save_coupon_discount_products
    * @desc This method saves or updates the discount products
    * 
    * @param mixed $data
    * @param mixed $coupon_id
    * @param mixed $product_id
    */
    public function save_coupon_discount_products( $data, $coupon_id = '', $product_id = '' )
    {
        if (is_numeric($coupon_id) && is_numeric($product_id))
        {
            $this->db->where('coupon_id', $coupon_id);
            $this->db->where('product_id', $product_id);
            $this->db->update('nc_coupon_discount_products', $data);            
            //return $coupon_id;
        }
        else
        {
            $this->db->insert('nc_coupon_discount_products', $data);
            //return $this->db->insert_id();
        }
    }
    
    /**
    * @method delete
    * @desc this method deletes all the discount products from selected coupon id
    *     
    * @param mixed $coupon_id
    */
    
    public function delete_coupon_discount_products($coupon_id)
    {                    
        if( $coupon_id != "" && is_numeric($coupon_id) )
        {
            $this->db->where('coupon_id', $coupon_id);
            $this->db->delete('nc_coupon_discount_products');
        }
    }
    
    public function get_list_coupon_discount_products( $coupon_id, $type = 'buy', $custom_select = '' )
    {
        if( ! is_numeric($coupon_id))
            return FALSE;
            
        $this->db->select( ($custom_select != '') ? $custom_select : 'cdp.*, p.product_name, CONCAT(cdp.product_id, "_", cdp.qty) as discount_product_id, CONCAT(cdp.qty, "x ",p.product_name) as dicount_product_name');
        $this->db->from('nc_coupon_discount_products cdp');    
        $this->db->join('nc_products p', 'cdp.product_id = p.product_id');
        $this->db->where('cdp.coupon_id', $coupon_id);
        $this->db->where('cdp.type', $type);
            
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }         
        else
            return false;
    }
    
    /**
    * @method get_discount_prdoucts
    * @desc This method returns a row which products separated with comma
    * eg. 2x iPhone, 1x GuiDesigner 
    * 
    * @param mixed $co
    */
    function get_discount_prdoucts( $coupon_id, $type = 'buy' )
    {
        if( ! is_numeric($coupon_id))
            return FALSE;
            
        $this->db->select("GROUP_CONCAT( CONCAT(cdp.qty, 'x ', p.product_name) SEPARATOR ', ' ) as discount_products", false);     
        $this->db->from('nc_coupon_discount_products cdp');
        $this->db->join('nc_products p', 'p.product_id = cdp.product_id');
        $this->db->where('cdp.coupon_id', $coupon_id);
        $this->db->where('cdp.type', $type);
        $this->db->group_by('coupon_id');
        
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
    }
    
    /**
    * @method is_used_coupon
    * @desc This method returns TRUE if the coupon id was used in other orders else FALSE
    * 
    * @param int $coupon_id
    */
    
    function is_used_coupon($coupon_id)  
    {                
        $this->db->where('coupon_id', $coupon_id);
        $this->db->from('nc_order_header');
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        return ($query->num_rows() > 0);
        
    }   
}  
?>