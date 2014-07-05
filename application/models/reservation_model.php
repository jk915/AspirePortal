<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
*/
class Reservation_model extends CI_Model 
{
    function Reservation_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    /**
    * @method get_list
    * @author Agnes Konya
    * @abstract This method gets a list of all orders from the database.  
    * 
    * @param integer $limit - Limits the recordset to a specific number of records
    * @param integer $page_no - Starts the recordset at a specific page no.
    * @param integer $count_all - Counts all records.
    * @param string $search_term - search by client business name
    * @param string $search_period - search by period (eg. today, yesterday)
    * @param date $start_date - use for case "choose"
    * @param date $end_date - use for case "choose"
    * 
    * @returns A list of order headers
    */
    public function get_list($limit = "", $page_no = "", &$count_all, $search_term = "", $search_period = "all", $from_date = "", $to_date = "", $sold = 0)
    {   	
        //check from_date if is a valid date      
//        $arr_from_date = split("/",$from_date); 
        $arr_from_date = explode("/",$from_date); 
       
        if(count($arr_from_date) == 3)
        {
            if(!checkdate($arr_from_date[1],$arr_from_date[0],$arr_from_date[2])) //invalid date
              $from_date =  "";
        }
        else
            $from_date =  "";
        //check to_date if is a valid date      
//        $arr_to_date = split("/",$to_date); 
        $arr_to_date = explode("/",$to_date); 
       
        if(count($arr_to_date) == 3)
        {
            if(!checkdate($arr_to_date[1],$arr_to_date[0],$arr_to_date[2])) //invalid date
              $to_date =  "";
        }
        else
            $to_date = "";
        
        $this->_get_list($limit, $page_no, $count_all, $search_term, $search_period, $from_date, $to_date, TRUE, $sold);    
        $count_all = $this->db->count_all_results();
        
        $this->_get_list($limit, $page_no, $count_all, $search_term, $search_period, $from_date, $to_date, FALSE, $sold);                                                                                     
        $query = $this->db->get(); 
        
        //echo $this->db->last_query();
        //die("HERE");
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    function _get_list($limit = "", $page_no = "", &$count_all, $search_term = "", $search_period = "all", $from_date = "", $to_date = "", $count_results = TRUE, $sold)
    {	
        //count all result        
        $this->db->select('r.reservation_id, r.sold, p.property_id, concat(concat(p.title, " "), p.address) as property, u.legal_entity_name as company, 
        	concat(u.first_name," ",u.last_name) as contact_name, u.legal_entity_phone as phone, 
        	date_format(r.date_reserved,"%d/%m/%Y") as reservation_date, 
        	date_format(r.sold_date,"%d/%m/%Y") as sold_date, 
        	u.user_id, proj.project_name',false);
        $this->db->from('nc_reservation r');
        $this->db->join('nc_properties p', 'r.property_id = p.property_id');
        
        if($sold != "")
        {
        	$this->db->where('r.sold', $sold);
        	
        	if($sold == 0)
        		$this->db->where('p.status','reserved');
		}
        	
        $this->db->join('nc_users u','u.user_id = r.user_id');        
        $this->db->join('nc_states s','s.state_id = p.state_id');        
        $this->db->join('nc_property_project pp', 'p.property_id = pp.property_id');
        $this->db->join('nc_projects proj', 'pp.project_id = proj.project_id');
                
        if($search_term != "") 
        {
            $this->db->like('u.legal_entity_name', $search_term);            
            $this->db->or_like('concat(u.first_name," ",u.last_name)', $search_term);            
        }
        
        $this->_search_period($search_period, $from_date, $to_date);
        
        if(!$count_results)
        {
            $this->db->order_by("p.date_reserved", "DESC");     
                
            if ($limit != "" && $page_no != "" && $count_all > $limit)
            {
                $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
            }   
        }
    }
    
	function get_user_reservations($user_id)
	{
        $this->db->select('r.reservation_id, r.sold, p.property_id, concat(concat(p.title, " "), p.address) as property, 
        	date_format(r.date_reserved,"%d/%m/%Y") as reservation_date, 
        	date_format(r.sold_date,"%d/%m/%Y") as sold_date, proj.project_name',false);
        	
        $this->db->from('nc_reservation r');
        $this->db->join('nc_users u','u.user_id = r.user_id'); 
        $this->db->join('nc_properties p', 'r.property_id = p.property_id');
        $this->db->join('nc_property_project pp', 'p.property_id = pp.property_id');
        $this->db->join('nc_projects proj', 'pp.project_id = proj.project_id');
        $this->db->where("r.user_id", $user_id);	
        
        $query = $this->db->get(); 
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;        	
	}
    
   /**
   * @desc The get_details method loads all properties of a particular article as defined by article_id
   */
    public function get_details($reservation_id)
    {
        $query = $this->db->get_where('reservation', array('reservation_id' => $reservation_id), 1);

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    } 
    
    public function save($reservation_id, $data)
    {
        if (is_numeric($reservation_id) && $reservation_id > 0)
        {        
            $this->db->where('reservation_id', $reservation_id);
            $this->db->update('reservation',$data);
            
            return $reservation_id;
        }
        else
        {
            $data["date_reserved"] = date("Y-m-d %H:%i:%s"); 
            $this->db->insert('reservation', $data);
            return $this->db->insert_id();
        }
    }       
    
    
    /**
    * @method get_summary_table
    * @author Agnes Konya
    * @abstract This method lists all partners/agents, how many reservations each partner has made, and the last reservation date.  
    * 
    * @param string $search_period - search by period (eg. today, yesterday)
    * @param date $from_date - use for case "choose"
    * @param date $to_date - use for case "choose"
    * 
    * @returns A list of order headers
    */
    public function get_summary_table($search_period = "all", $from_date = "", $to_date = "")
    {
        $where = $this->_search_period($search_period, $from_date, $to_date, FALSE, "r.date_reserved");
        if($where != "") $where = "AND " . $where;
        
        $select_reservation_no = "(SELECT COUNT(*) FROM nc_reservation r WHERE r.user_id = u.user_id " . $where . ")  AS reservation_no";
        $select_sales_no = "(SELECT COUNT(*) FROM nc_reservation r WHERE r.user_id = u.user_id AND r.sold = 1 " . $where . ")  AS sales_no";
                
        $select_last_reservation_date = "(SELECT DATE_FORMAT(MAX(date_reserved), '%d/%m/%Y') FROM nc_reservation r2 WHERE r2.user_id = u.user_id) AS last_reservation_date";
        
        $this->db->select(" u.user_id, u.first_name, u.last_name, " . $select_reservation_no . ", " . $select_sales_no . ", " . $select_last_reservation_date, FALSE);
        $this->db->from("nc_users u");
        $this->db->where("u.user_type_id", AGENT_USER_TYPE_ID);
        $this->db->where('u.user_id !=','-1');
        
        $query = $this->db->get();        
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    /**
    * @method _search_period
    * @author Agnes Konya
    * @abstract This method it's part of sql, if the sql is implemented with Codeigniter then will be $this->db->where(condition) else returns the where condition
    * 
    * @param string $search_period - search by period (eg. today, yesterday)
    * @param date $from_date - use for case "choose"
    * @param date $to_date - use for case "choose"
    * @param is_CI_sql - is Codeigniter sql
    * 
    * @returns A list of order headers
    */
    public function _search_period($search_period, $from_date, $to_date, $is_CI_sql = TRUE, $column = "p.date_reserved")
    {
        $where = "";
        if($search_period != "")
        {
            switch($search_period)
            {
                case "today":
                    $where = 'date_format(' . $column . ',"%Y-%m-%d") = CURDATE()';                    
                break;
                
                case "yesterday":
                    $where = 'date_format(' . $column . ',"%Y-%m-%d") = CURDATE() - INTERVAL 1 DAY';                    
                break;
                
                case "week_to_date":
                    $where = 'date_format(' . $column . ',"%Y-%m-%d") BETWEEN subdate(curdate(), INTERVAL weekday(curdate()) DAY) AND curdate()';                    
                break;
                
                case "last_week":
                    $where = 'date_format(' . $column . ',"%Y-%m-%d") BETWEEN subdate(curdate(), INTERVAL weekday(curdate()) DAY)+ INTERVAL -7 DAY AND subdate(curdate(), INTERVAL weekday(curdate()) DAY)+ INTERVAL -1 DAY';
                break;
                
                case "month_to_date":
                    $where = 'date_format(' . $column . ',"%Y-%m-%d") BETWEEN date_format(curdate(), "%Y-%m-01") AND curdate()';
                break;
                
                case "last_month":
                    $where = 'date_format(' . $column . ',"%Y-%m-%d") = date_format(curdate(), "%Y-%m-%d") - INTERVAL 1 MONTH';
                break;
                
                case "last_quarter":
                    $where = 'date_format(' . $column . ',"%Y-%m-%d") = date_format(curdate(), "%Y-%m-%d") - INTERVAL 3 MONTH';
                break;
                
                case "this_quarter":
                    $current_month = date("n");
                    $months_remaining = $current_month % 3;
                    $where = 'date_format(' . $column . ',"%Y-%m-%d")  BETWEEN (date_format(curdate(),"%Y-%m-%d") - INTERVAL '. $months_remaining . ' MONTH) AND date_format(curdate(),"%Y-%m-%d")';
                break;
                
                case "choose":
                    $where = 'date_format(' . $column . ',"%d/%m/%Y") BETWEEN "'. $from_date .'" AND "'. $to_date.'"';
                break;
            }
            
            if($is_CI_sql)
            {
                if($where != "")
                    $this->db->where($where, null, false);
            }
            else
                return $where;    
        }
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
        $this->db->delete("nc_reservation");               
    }    
    
    public function add_reservation($property_id, $user_id, $lead_id)
    {
        $current_date = date("Y-m-d");
        
        $reservation_data = array(
            "user_id"       => $user_id, 
            "date_reserved" => $current_date,
            "status"        => "reserved"
       );
       
       $this->db->where("property_id", $property_id);
       $this->db->update("nc_properties", $reservation_data);              
       
       $arr = array(
            "property_id" => $property_id,
            "user_id" => $user_id,
            "lead_id" => $lead_id,
            "date_reserved" => $current_date
       );
       
       $this->db->insert("nc_reservation", $arr);
    }
    
    public function check_reservation($property_id, $user_id = -1, $reserved_date = "")
    {
        if($reserved_date == "")
            $reserved_date = date("Y-m-d");
            
        $this->db->where("property_id", $property_id);
        
        if($user_id != -1)
            $this->db->where("user_id", $user_id);   
        
        $this->db->where("status", "reserved");
        //$this->db->where("date_reserved",$reserved_date);   
                
        $query = $this->db->get('nc_properties',1);
        
        return ($query->num_rows() > 0);        
    }
    
    public function is_property_reserved($property_id)
    {
        $this->db->where("property_id", $property_id);
        
        $limit = 1;
        $query = $this->db->get('nc_reservation', $limit);
        
        return ($query->num_rows() > 0);        
    }     
    
    public function get_reservation_details($property_id)      
    {
        $this->db->select('u.first_name, u.last_name, u.address, u.suburb, u.postcode, u.state, u.legal_entity_name');
        $this->db->from('nc_properties p');
        $this->db->join('nc_users u','u.user_id = p.user_id');        
        $this->db->where("p.property_id",$property_id);
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
    }
}
?>
