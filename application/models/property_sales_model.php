<?php
class Property_sales_model extends CI_Model 
{
    function Property_sales_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function delete($where_in)
    {
        $data = array(
            "status"   => "available",
            "user_id"  => "-1"
        );
        
        $this->db->where(" property_id  in (".$where_in.")",null,false);
        $this->db->update("nc_properties",$data); 
        
        $this->db->where(" property_id  in (".$where_in.")",null,false);
        $this->db->delete("nc_property_sales");               
    }    
    
    public function add_sale($property_id, $user_id, $admin_id)
    {
        $current_date = date("Y-m-d");
         
        $reservation_data = array(
            "user_id"       => $user_id, 
            "date_sold" => $current_date,
            "status"        => "reserved"
       );  
                   
       $this->db->where("property_id", $property_id);
       $this->db->update("nc_properties", $reservation_data);              
       
       $arr = array(
            "property_id" => $property_id,
            "user_id" => $user_id,
            "admin_id" => $admin_id,
            "date_sold" => $current_date
       );
       
       $this->db->insert("nc_property_sales", $arr);
       
    }
}
?>