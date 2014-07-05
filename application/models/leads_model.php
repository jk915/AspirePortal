<?php
class Leads_model extends CI_Model 
{
    function Leads_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "id DESC", $items_per_page = 0, $offset = 0)                        
    {
        $select_reservation_no = "(SELECT COUNT(*) FROM nc_reservation r WHERE r.lead_id = nc_leads.id)  AS reservation_no";
        
    	$this->db->select("leads.*, a.first_name as agent_first_name, a.last_name as agent_last_name, " . $select_reservation_no . ", s.name as state_name");
    	$this->db->join("users a", "leads.agent_id = a.user_id");
    	$this->db->join("states s", "leads.state = s.state_id","left");
    	$this->db->where("deleted", 0);
    	
    	if(isset($filters["sort_default"])) {
            $order_by = "FIELD(status, Open, Converted, Lost), first_name ASC";
            $this->db->order_by($order_by);
            $this->db->ar_orderby = str_replace("`","'",$this->db->ar_orderby);
            $this->db->ar_orderby = str_replace(")'","')",$this->db->ar_orderby);
            $this->db->ar_orderby = str_replace("'first_name'","first_name",$this->db->ar_orderby);
    	} else {
    	    if(isset($filters["sort"])) {
    			foreach ($filters["sort"] AS $index=>$value) 
    			{
    			    $order_by = $index." ".$value;
    			}
        	}
        	$this->db->order_by($order_by);
    	}
    	if(isset($filters["agent_id"]))
    	{
			$this->db->where("agent_id", $filters["agent_id"]);
    	}
    	
    	if(isset($filters["status"]) && $filters["status"] != "")
    	{
			$this->db->where("status", $filters["status"]);
    	}
    	
    	if(isset($filters["not_lost"]) && $filters["not_lost"] != "")
    	{
			$this->db->where("status != ", "Lost");
    	}
    	
    	if (isset($filters["keysearch"]) && $filters["keysearch"] != "") {
    		$keysearch = $this->db->escape_str($filters['keysearch']);
        	$this->db->where("(nc_leads.first_name LIKE '%$keysearch%'
    	                       OR nc_leads.last_name LIKE '%$keysearch%'
    	                       OR nc_leads.company_name LIKE '%$keysearch%'
    	                       )");
    	}
    	
    	if (isset($filters["location"]) && $filters["location"] != "") {
    	    $location = $filters["location"];
    		$this->db->where("(nc_leads.address1 LIKE '%$location%'
    	                       OR nc_leads.address2 LIKE '%$location%'
    	                       OR nc_leads.suburb LIKE '%$location%'
    	                       OR nc_leads.postcode LIKE '%$location%'
    	                       OR nc_leads.state LIKE '%$location%'
    	                       )");
    	}
        
        if($items_per_page > 0) {
            $this->db->limit($items_per_page);
            $this->db->offset($offset);
        }
        
        $query = $this->db->get("leads");            
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
        
    }
    
    /***

    * @method get_all_leads

    * @abstract This method gets a list of all leads from the database.  

    * 

    * @param integer $limit - Limits the recordset to a specific number of records

   * @param integer $page_no - Starts the recordset at a specific page no.

   * @param integer $count_all - Counts all records.

    * 

    * @returns A list of pages

    */

    public function get_all_leads($limit = "", $page_no = "", &$count_all, $search_term = "", $name_type = "")
    {
        $this->_get_all_leads($limit, $page_no, $count_all, $search_term, $name_type, true);
        $count_all = $this->db->count_all_results();

        $this->_get_all_leads($limit, $page_no, $count_all, $search_term, $name_type, false);
        $query = $this->db->get();        

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }    

    

    function _get_all_leads($limit = "", $page_no = "", $count_all, $search_term = "", $name_type = "", $only_count = false)
    {
    	$this->db->select("leads.*, a.first_name as agent_first_name, a.last_name as agent_last_name, s.name as state_name");
    	$this->db->from("nc_leads");
        $this->db->join("users a", "leads.agent_id = a.user_id");
    	$this->db->join("states s", "leads.state = s.state_id","left");
    	$this->db->where("deleted", 0);

        if($search_term !=""){
             $keysearch = $this->db->escape_str($search_term);
             if($name_type != ""){
                if ($name_type == 'lead_name'){
                	$this->db->where("(nc_leads.first_name LIKE '%$keysearch%' 
            	                       OR nc_leads.last_name LIKE '%$keysearch%'
            	                       )");
                }else{
                    $this->db->where("a.first_name LIKE '%$keysearch%' 
                                       OR a.last_name LIKE '%$keysearch%' 
                                    ");
                }
            }else{
        	    $this->db->where("nc_leads.first_name LIKE '%$keysearch%' 
    	                       OR nc_leads.last_name LIKE '%$keysearch%'  
    	                       OR a.first_name LIKE '%$keysearch%' 
                               OR a.last_name LIKE '%$keysearch%' 
    	                       ");
            }
    	}
        
    	
        
        if(!$only_count)     
            $this->db->order_by("nc_leads.id", "ASC");    
        

        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }
        
    }
  
    public function get_details($lead_id)
    {
    	$query = $this->db->get_where('leads', array('id' => $lead_id,'deleted' => 0));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($lead_id, $data)
	{
		if(is_numeric($lead_id)) {
			$this->db->where('id',$lead_id);
			
			if($this->db->update('nc_leads',$data))
				return $lead_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_leads',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($where_in)
	{
		//$this->db->where(" id in (".$where_in.")",null,false);
		//$this->db->delete('nc_leads');
        $this->db->update('nc_leads', array('deleted'=>1), "`id` in (".$where_in.")");
	}
    
    public function get_states($country_id = '')
    {
        if( $country_id != '' && is_numeric($country_id) )
            $this->db->where("country_id", $country_id);
            
         $query = $this->db->get('states');
         
         if($query->num_rows() > 0)
            return $query;
         else
            return false;
    }
    
    public function get_countries()
    {
        $this->db->order_by("country");
         $query = $this->db->get('countries');
         
         if($query->num_rows() > 0)
            return $query;
         else
            return false;
    }                                    
   
}
?>