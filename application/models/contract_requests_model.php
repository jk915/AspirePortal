<?php
class Contract_requests_model extends CI_Model 
{
    function Contract_requests_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "id DESC", $items_per_page = 0, $offset = 0)                        
    {
    	$this->db->select("contract_requests.*, a.first_name as agent_first_name, q.base_price, a.last_name as agent_last_name, a.user_id as agent_id, q.quote_number, q.total_price, q.id as quote_id, q.quote_date, l.first_name as lead_first_name, l.last_name as lead_last_name, l.id as lead_id, d.id as contract_document_id ");
    	$this->db->join("quotes q", "contract_requests.quote_id = q.id");
    	$this->db->join("leads l", "q.lead_1 = l.id","left");
    	$this->db->join("users a", "q.agent_id = a.user_id");
    	$this->db->join("documents d", "contract_requests.id = d.foreign_id AND document_type = 'contract_document'","left");
        
        // By default we only want to show not-deleted requests.
        // The admin console however should see them all, so check for the deleted = * filter.
        if((array_key_exists("deleted", $filters)) && ($filters["deleted"] == "*"))
        {
    	    // Do nothing
        }
        else
        {
            $this->db->where("contract_requests.deleted", 0);
        }
    	
    	if(isset($filters["sort_default"])) {
            $order_by = "FIELD(nc_contract_requests.status, Pending, Approved, Rejected, OldVersion) ASC";
            $this->db->order_by($order_by);
            $this->db->ar_orderby = str_replace("`","'",$this->db->ar_orderby);
            $this->db->ar_orderby = str_replace(")'","')",$this->db->ar_orderby);
    	} else {
    	    if(isset($filters["sort"])) {
    			foreach ($filters["sort"] AS $index=>$value) 
    			{
    			    $order_by = $index." ".$value;
    			}
        	}
        	$this->db->order_by($order_by);
    	}
    	
    	// Apply filters
    	
    	if(isset($filters["agent_id"]))
    	{
			$this->db->where("contract_requests.agent_id", $filters["agent_id"]);
    	}
    	
    	if(isset($filters["status"]) && $filters["status"] != "")
    	{
			$this->db->where("contract_requests.status", $filters["status"]);
    	}
    	
    	if (isset($filters["agent_name"]) && $filters["agent_name"] != "") {
    		$agent_name = $this->db->escape_str($filters['agent_name']);
        	$this->db->where("(a.first_name LIKE '%$agent_name%'
    	                       OR a.last_name LIKE '%$agent_name%'
    	                       )");
    	}
    	
    	if (isset($filters["date_start"]) && isset($filters["date_end"])) {
	        $start = $filters["date_start"];
	        $end = $filters["date_end"];
	        $dateRange = "q.quote_date BETWEEN '$start%' AND '$end%'";
            $this->db->where($dateRange, NULL, FALSE);  
	    }
    	
        if($items_per_page > 0) {
            $this->db->limit($items_per_page);
            $this->db->offset($offset);
        }
        
        $query = $this->db->get("contract_requests");            
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
        
    }
  
    public function get_details($request_id)
    {
        $this->db->select("contract_requests.*, q.base_price, a.first_name as agent_first_name, a.last_name as agent_last_name, a.user_id as agent_id, a.email as agent_email, q.quote_number, q.status as quote_status, q.address, q.total_price, q.id as quote_id, q.commission, q.quote_date,q.property_id, l.first_name as lead_first_name, l.last_name as lead_last_name, l.id as lead_id ");
    	$this->db->join("quotes q", "contract_requests.quote_id = q.id");
    	$this->db->join("leads l", "q.lead_1 = l.id","left");
    	$this->db->join("users a", "q.agent_id = a.user_id");
        $this->db->where('contract_requests.id',$request_id);
        $query = $this->db->get("contract_requests");
        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($request_id, $data)
	{
		if(is_numeric($request_id)) {
			$this->db->where('id',$request_id);
			
			if($this->db->update('nc_contract_requests',$data))
				return $request_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_contract_requests',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" id in (".$where_in.")",null,false);
		$this->db->delete('nc_contract_requests');
	}
	
	public function get_by_quote($quote_id)
	{
		$this->db->select("contract_requests.*");
        $this->db->where('contract_requests.quote_id',$quote_id);
        $this->db->where('contract_requests.status != ',"OldVersion");
        $this->db->where('contract_requests.deleted',0);
        $query = $this->db->get("contract_requests");
        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
	}
   
}
?>