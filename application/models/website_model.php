<?php
class Website_model extends CI_Model 
{
    function Website_model()
    {
        // Call the Model constructor
          parent::__construct();
    }      

    /***
    * @method get_list
    * @abstract This metod gets a list of all websites from the database.  By default the website
    * list will return only websites that are defined as "showing", but this can be bypassed with the
    * ignore_showing_flag parameter.  
    * 
    * @param mixed $ignore_showing_flag If set to true, the is_showing flag will be ignored and all websites returned.
    * 
    * @returns A list of websites
    */
    public function get_list($filters = array(), $order_by = "website_id ASC")
    {
		$this->db->select("*");
		$this->db->from("websites");
		
		if(isset($filters["enabled"]))
		{
			$this->db->where("enabled", $filters["enabled"]);
		}
		
		if(isset($filters["deleted"]))
		{
			$this->db->where("deleted", $filters["deleted"]);
		}
		else
		{
			// If the deleted flag was not passed - do not show deleted websites.
			$this->db->where("deleted", 0);
		}		
		
		$this->db->order_by($order_by);
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0)
		{
			return $result;
		}
		else
		{
			return false;
		}			              
    } 
    
   /**
   * @desc The get_details method loads all properties of a particular website as defined by website_id
   */
    public function get_details($website_id = "", $where = "" )
    {
        // Check to see if a record with this username exists.
        
        $this->db->select('*,date_format(start_date,"%d/%m/%Y") as dmy_start_date, date_format(expiry_date,"%d/%m/%Y") as dmy_expiry_date', false);
        
        if($where != "")
            $this->db->where($where);
            
        if(is_numeric($website_id))
            $this->db->where('website_id', $website_id);
            
        $query = $this->db->get('nc_websites');
    
        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;        
    }
    
    public function save($website_id,$data)
    {
        if (is_numeric($website_id))
        {
            $this->db->where('website_id',$website_id);
            $this->db->update('nc_websites',$data);
            
            return $website_id;
        }
        else
        {
            $this->db->insert('nc_websites',$data);
            return $this->db->insert_id();
        }
    }
    
    public function delete($where_in)
    {
        $this->db->set("deleted", 1);
        $this->db->where_in("website_id", $where_in);
        $this->db->update("websites");
    }
    
    public function add_website_assm($foreign_id, $arr_website, $type = "page")
    {
        $this->db->where("foreign_id", $foreign_id);
        $this->db->where("type",$type);
        $this->db->delete("nc_website_assm");
        
        foreach($arr_website as $row)
        {
            $data = array(
                "website_id" => $row,
                "foreign_id" => $foreign_id,
                "type" => $type
            );
            
            $this->db->insert("nc_website_assm", $data);
        }
    }
    
    public function get_website_assm($foreign_id, $type = "page")
    {
        $this->db->where("foreign_id", $foreign_id);
        $this->db->where("type", $type);
        $query = $this->db->get('nc_website_assm');
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
}  
