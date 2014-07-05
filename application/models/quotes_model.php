<?php
class Quotes_model extends CI_Model 
{
    function Quotes_model()
    {

        // Call the Model constructor
        parent::__construct();
    }

    public function get_list($filters = array(), $order_by = "id DESC", $items_per_page = 0, $offset = 0)                        
    {
    	$this->db->select("quotes.*, a.first_name as agent_first_name, a.last_name as agent_last_name, l.first_name as lead_first_name, l.last_name as lead_last_name, l.company_name");
    	$this->db->join("users a", "quotes.agent_id = a.user_id");
    	$this->db->join("leads l", "quotes.lead_1 = l.id");
        $this->db->join("properties p", "quotes.property_id = p.property_id", "left");
    	$this->db->where("quotes.deleted", 0);

	    if (isset($filters["sort"]["quote_number"]) && isset($filters["sort"]["quote_number"]) != "") 
        {
	    	$order_by = "quote_number ".$filters["sort"]["quote_number"];
	    }

        if (isset($filters["sort"]["lead_id"]) && isset($filters["sort"]["lead_id"]) != "") {

	    	$order_by = "lead_id ".$filters["sort"]["lead_id"];

	    }

	    if (isset($filters["sort"]["quote_date"]) && isset($filters["sort"]["quote_date"]) != "") {

	    	$order_by = "quote_date ".$filters["sort"]["quote_date"];

	    }

	    if (isset($filters["sort"]["status"]) && isset($filters["sort"]["status"]) != "") {

	    	$order_by = "status ".$filters["sort"]["status"];

	    }

	    if (isset($filters["sort"]["client_name"]) && isset($filters["sort"]["client_name"]) != "") {

	    	$order_by = "a.first_name ".$filters["sort"]["client_name"];

	    }

	    if (isset($filters["sort"]["total_price"]) && isset($filters["sort"]["total_price"]) != "") {

	    	$order_by = "quotes.total_price ".$filters["sort"]["total_price"];

	    }

	    if (isset($filters["quote_not_used"]) && isset($filters["quote_not_used"])) {

	        $ids = $filters["quote_not_used"];

	    	$this->db->join("contract_requests cr", "quotes.id != cr.quote_id");

	    	$this->db->where_not_in('quotes.id', $ids);

	    }

	    

	    if (isset($filters["date_start"]) && isset($filters["date_end"])) {

	        $start = $filters["date_start"];

	        $end = $filters["date_end"];

	        $dateRange = "quotes.quote_date BETWEEN '$start%' AND '$end%'";

            $this->db->where($dateRange, NULL, FALSE);

	    }

    	

    	if (isset($filters["keysearch"]) && $filters["keysearch"] != "") {

    		$keysearch = $this->db->escape_str($filters['keysearch']);

        	$this->db->where("(nc_quotes.quote_number LIKE '%$keysearch%'

    	                       OR l.first_name LIKE '%$keysearch%'

    	                       OR l.last_name LIKE '%$keysearch%'

    	                       OR l.company_name LIKE '%$keysearch%'

    	                       )");

    	}

    	

    	if(isset($filters["agent_id"]))

    	{

			$this->db->where("quotes.agent_id", $filters["agent_id"]);

    	}

    	

    	if(isset($filters["status"]) && $filters["status"] != "")

    	{

			$this->db->where("quotes.status", $filters["status"]);

    	}

    	

    	if(isset($filters["lead_id"]) && $filters["lead_id"] != "")

    	{

			$this->db->where("quotes.lead_id", $filters["lead_id"]);

    	}



    	// Apply filters       

        $this->db->order_by($order_by);

        

        if($items_per_page > 0) {

            $this->db->limit($items_per_page);

            $this->db->offset($offset);

        }

        

        $query = $this->db->get("quotes");

        

        if ($query->num_rows() > 0)

        {

            return $query;

        }         

        else

            return false;

        

    }

  

    public function get_details($quote_id)

    {

        $this->db->select("quotes.*, 

        l1.first_name as lead_first_name, l1.last_name as lead_last_name, l1.company_name, l1.phone AS phone1, l1.id as lead_id,

        l1.suburb as suburb1, l1.postcode as postcode1, l1.address1 as lead1_address1, l1.address2 as lead1_address2, st1.name as state_name1,

        l2.first_name as lead_first_name2, l2.last_name as lead_last_name2, l2.company_name AS company_name2, l2.phone AS phone2, l2.id as lead_id2, 

        l2.suburb as suburb2, l2.postcode as postcode2, l2.address1 as lead2_address1, l2.address2 as lead2_address2, st2.name as state_name2,

        st.name as quote_state, pro.project_name, 

        c1.name AS country1, , c2.name AS country2");

        $this->db->join("projects pro", "quotes.project_id = pro.project_id",'left');

        $this->db->join("states st", "quotes.state_id = st.state_id");

        $this->db->join("leads l1", "quotes.lead_1 = l1.id");

        $this->db->join("countries c1", "l1.country = c1.country_id","left");

        $this->db->join("states st1", "l1.state = st1.state_id","left");

        $this->db->join("leads l2", "quotes.lead_2 = l2.id","left");

        $this->db->join("countries c2", "l2.country = c2.country_id","left");

        $this->db->join("states st2", "l2.state = st2.state_id","left");

        $this->db->where("quotes.id", $quote_id);

        $this->db->where("quotes.deleted", 0);

        $query = $this->db->get("quotes");

        if ($query->num_rows() > 0) {

           return $query->row();

        } else

            return false;

    }



	public function save($quote_id, $data)

	{

		if(is_numeric($quote_id)) {

			$this->db->where('id',$quote_id);

			$data["last_modification_dtm"] = date("Y-m-d"); 

			if($this->db->update('nc_quotes',$data))

				return $quote_id;	

			else

				return false;

		} else {

		    $data["created_dtm"] = date("Y-m-d"); 

			$this->db->insert('nc_quotes',$data);    

			return $this->db->insert_id(); 

		}

	}

    

	public function delete($where_in)

	{

		$this->db->where(" id in (".$where_in.")",null,false);

		$this->db->delete('nc_quotes');

	}

   

	public function get_quote_number($agent_id)

	{

	    $this->db->select("quotes.quote_number");

    	$this->db->where("quotes.agent_id", $agent_id);

	    $this->db->order_by("quote_number desc");

    	$query = $this->db->get("quotes");            

        if ($query->num_rows() > 0) {

           return $query->row();

        } else

            return false;

	}

	

	public function delete_extra_item($quote_id)

	{

		$this->db->where("quote_id = $quote_id",null,false);

		$this->db->delete('nc_extraitems');

	}

}

?>