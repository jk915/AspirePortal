<?php
class Property_model extends CI_Model 
{
    function Property_model()
    {
        // Call the Model constructor
          parent::__construct();        
    }

    /***
    * @method get_list
    * @abstract This method gets a list of all properties from the database.  
    * 
    * @param integer $enabled - 1 returns a list of enabled properties, 0 = not enabled, -1 = all properties.
    * @param integer $limit - Limits the recordset to a specific number of records
    * @param integer $page_no - Starts the recordset at a specific page no.
    * @param integer $count_all - Counts all records.
    * 
    * @returns A list of properties
    */
    public function get_list( $data, &$count_all = 0, $order_by = "p.status, p.lot, proj.project_name ASC")
    {
        $this->_get_list($data, $count_all, TRUE, $order_by);         
        $count_all = $this->db->count_all_results();
        
        $this->_get_list($data, $count_all, FALSE, $order_by);            
        $query = $this->db->get(); 

        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
           return false;
   }

    function _get_list($data, $count_all, $count_results, $order_by)
    {
        if(!$count_results) 
        {
            $select_str = "p.*, s.name as state, p.suburb as suburb_name, proj.project_code, proj.project_name, " .
                "l.first_name AS lead_first_name, l.last_name AS lead_last_name, l.id AS lead_id, " .
                "proj.project_type, area.area_name, bd.builder_name, u.first_name AS advisor_first_name, " .
                "u.last_name AS advisor_last_name, r1.name as property_type, st.name as state_name, proj.rate";
                
            $this->db->select($select_str);
        }

        //count all result
        $this->db->from('nc_properties p');
        $this->db->join('nc_states s','s.state_id = p.state_id');        

        $this->db->join('property_project pp', 'p.property_id = pp.property_id');
        $this->db->join('projects proj', 'pp.project_id = proj.project_id');
        $this->db->join('reservation rs', 'p.property_id = rs.property_id','left');
        $this->db->join('leads l', 'rs.lead_id = l.id','left');
        $this->db->join('areas area', 'proj.area_id = area.area_id','left');
        $this->db->join('builders bd', 'p.builder_id = bd.builder_id','left');
        $this->db->join('users u', 'u.user_id = p.advisor_id','left');
        $this->db->join('resources r1', "p.property_type_id = r1.value AND r1.resource_type = 'property_type'",'left');
        $this->db->join('states st', 'st.state_id = p.state_id','left');

        if(isset($data["enabled"]) && $data["enabled"] > -1)
           $this->db->where('p.enabled', $data["enabled"]);  

        if(isset($data["search_terms"]) && $data["search_terms"] !="") {
            $this->db->like('p.address',$data["search_terms"]);
            $this->db->or_like('p.lot',$data["search_terms"]);
            $this->db->or_like('p.suburb',$data["search_terms"]);
            $this->db->or_like('p.postcode',$data["search_terms"]);
        }
        
        if(isset($data["archived"]) && $data["archived"] > -1) {
            $this->db->where('p.archived', intval($data["archived"]));
        }
        
        if(isset($data["not_in_property"]) && sizeof($data["not_in_property"])) {
            $this->db->where_not_in('p.property_id', $data["not_in_property"]);
        }
        
        if(isset($data["area_id"]) && $data["area_id"] != '') {
            $this->db->where('area.area_id', intval($data["area_id"]));
        }
        
        if(isset($data["builder_id"]) && $data["builder_id"] != '') {
            $this->db->where('bd.builder_id', intval($data["builder_id"]));
        }

        if(isset($data["keysearch"]) && $data["keysearch"] !="")  
        {
            $keysearch = $data["keysearch"];
            $this->db->where("(p.address LIKE '%$keysearch%'
    	                       OR p.lot LIKE '%$keysearch%'
    	                       OR p.suburb LIKE '%$keysearch%'
    	                       OR p.postcode LIKE '%$keysearch%'
    	                       )");

        }

        if(isset($data["project_id"]) && $data["project_id"] != -1 && is_numeric($data["project_id"]))    
        {
            $this->db->where("pp.project_id", $data["project_id"]);        
        }

        if(isset($data["state_id"]) && $data["state_id"] != -1 && is_numeric($data["state_id"]))    
            $this->db->where("s.state_id", $data["state_id"]); 


        if(isset($data["property_type_id"]) && $data["property_type_id"] != -1 && is_numeric($data["property_type_id"]))
        {
            $this->db->where("p.property_type_id", $data["property_type_id"]);            
        }
        
        if(isset($data["contract_type_id"]) && $data["contract_type_id"] != -1 && is_numeric($data["contract_type_id"]))
        {
            $this->db->where("p.contract_type_id", $data["contract_type_id"]);            
        }        


        if(isset($data["status"]) && $data["status"] != "") 
            (is_array($data["status"])) ? $this->db->where_in("p.status", $data["status"]) : $this->db->where("p.status", $data["status"]);    

        if(isset($data["suburb"]) && $data["suburb"] != "")
            $this->db->where("p.suburb", $data["suburb"]);

        if(isset($data["featured"]) && $data["featured"] > -1 )
            $this->db->where('p.featured', $data["featured"]);

        if(isset($data["min_price"]) && is_numeric($data["min_price"]))
            $this->db->where("p.total_price >=",$data["min_price"]);

        if(isset($data["max_price"]) && is_numeric($data["max_price"]))
            $this->db->where("p.total_price <=", $data["max_price"]);

        if(isset($data["num_bedrooms"]) && (is_numeric($data["num_bedrooms"])) && ($data["num_bedrooms"] > 0))
            $this->db->where("p.bedrooms >=", $data["num_bedrooms"]);

        if(isset($data["num_bathrooms"]) && (is_numeric($data["num_bathrooms"])) && ($data["num_bathrooms"] > 0))
            $this->db->where("p.bathrooms >=", $data["num_bathrooms"]);

        if(isset($data["num_cars"]) && (is_numeric($data["num_cars"])) && ($data["num_cars"] > 0))
            $this->db->where("p.garage >=", $data["num_cars"]);

        if(isset($data["is_recomended"]) && $data["is_recomended"] != -1)
            $this->db->where("p.recommended", $data["is_recomended"]);

        if(isset($data["hide_sold"]) && $data["hide_sold"] == 1)
            $this->db->where("p.status != ", "sold");

        if(isset($data["hide_reserved"]) && $data["hide_reserved"] == 1)
            $this->db->where("p.status != ", "reserved");

        if(isset($data["not_available"]) && $data["not_available"] == 1)
            $this->db->where("p.status != ", "available");                 

        if(isset($data["user_id"]) && $data["user_id"] != "-1")
        {
            $this->db->where("p.user_id", $data["user_id"]);
            //$this->db->where(" (p.status != 'sold' && p.status != 'reserved') ", null, false);           
            //$this->db->or_where(" (p.status = 'reserved' && p.user_id ='" . $data["user_id"] . "') ", null, false);           
        }
        
        if(isset($data["permissions_user_id"]) && intval($data["permissions_user_id"]))
        {
            $this->db->join('property_permissions per', 'p.property_id = per.foreign_id');
            $this->db->where('per.permission_type', 'Property');
            $this->db->where('per.user_id', $data["permissions_user_id"]);
        }
        
        if(isset($data["favourite_user_id"]) && intval($data["favourite_user_id"]))
        {
            $this->db->join('favourites f', 'p.property_id = f.foreign_id');
            $this->db->where('f.foreign_type', 'property');
            $this->db->where('f.user_id', $data["favourite_user_id"]);
        }
        
        if(isset($data["advisor_id"]) && $data["advisor_id"] != -1 && is_numeric($data["advisor_id"]))
        {
            $this->db->where("p.advisor_id", $data["advisor_id"]);            
        }
        
        if(isset($data["partner_id"]) && $data["partner_id"] != -1 && is_numeric($data["partner_id"]))
        {
            $this->db->where("p.partner_id", $data["partner_id"]);            
        }
        
        if(isset($data["investor_id"]) && $data["investor_id"] != -1 && is_numeric($data["investor_id"]))
        {
            $this->db->where("p.investor_id", $data["investor_id"]);            
        }
        
        $numeric_filters = array();
        $numeric_filters["min_total_price"] = array("p.total_price", ">=");
        $numeric_filters["max_total_price"] = array("p.total_price", "<=");
        $numeric_filters["min_bathrooms"] = array("p.bathrooms", ">=");
        $numeric_filters["max_bathrooms"] = array("p.bathrooms", "<=");    
        $numeric_filters["min_bedrooms"] = array("p.bedrooms", ">=");
        $numeric_filters["max_bedrooms"] = array("p.bedrooms", "<=");
        $numeric_filters["min_garage"] = array("p.garage", ">=");
        $numeric_filters["max_garage"] = array("p.garage", "<=");
        $numeric_filters["min_land"] = array("p.land", ">=");
        $numeric_filters["max_land"] = array("p.land", "<=");   
        $numeric_filters["min_yield"] = array("p.rent_yield", ">=");
        $numeric_filters["max_yield"] = array("p.rent_yield", "<=");                                 
        $numeric_filters["nras"] = array("p.nras", "=");
        $numeric_filters["smsf"] = array("p.smsf", "=");
        
        foreach($numeric_filters as $filter_key => $filter_values)
        {
            if((array_key_exists($filter_key, $data)) && (is_numeric($data[$filter_key])))
            {
                $filter_db_field = $filter_values[0];
                $filter_operator = $filter_values[1];
                $this->db->where($filter_db_field . " " . $filter_operator, $data[$filter_key]); 
            }              
        }       


        if($count_results == FALSE)
        {
            $this->db->order_by($order_by);             

            if (!isset($data["offset"])) 
            {
            	$data["offset"] = "";
            }

            if (isset($data["limit"]) && $data["limit"] != "") 
            {
            	$this->db->limit($data["limit"]);
	            $this->db->offset($data["offset"]);
            }
        }
    }


    /***
    * Loads all the details of the specified property, as defined by property_id
    * @param integer $property_id
    */
    public function get_details($property_id)
    {
    	$this->db->select("p.*,s.*, s.name as state, proj.project_type, proj.project_name, proj.project_id, bd.builder_name, r1.name as property_type, r2.name as contract_type");
        $this->db->from('nc_properties p');
        $this->db->join('nc_states s', 's.state_id = p.state_id');        
        $this->db->join('nc_property_project pp', 'p.property_id = pp.property_id');
        $this->db->join('nc_projects proj', 'pp.project_id = proj.project_id');
        $this->db->join('builders bd', 'p.builder_id = bd.builder_id','left');
        $this->db->join('resources r1', "p.property_type_id = r1.value AND r1.resource_type = 'property_type'", 'left');
        $this->db->join('resources r2', "p.contract_type_id = r2.value AND r2.resource_type = 'contract_type'", 'left');
        $this->db->where('p.property_id', $property_id);

        $query = $this->db->get();        

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0) 
        {
            return $query->row();
        }
        else 
        {
            return false;
        }
    }

    

    public function save($property_id, $data)
    {
        if (is_numeric($property_id)) 
        {
            $this->db->set('last_modification_dtm','now()',false);
            $this->db->where('property_id',$property_id);

            if(!$this->db->update('nc_properties',$data))
            {
                return false;    
            }

            return $property_id;
        }
        else
        {
            if(!$this->db->insert('nc_properties',$data))
            {
                return false;    
            }
            
            $inserted_id = $this->db->insert_id();

            return $inserted_id;
        }
    }

    public function save_property_project($property_id, $data)
    {

        if (is_numeric($property_id)) {

            $this->db->where('property_id', $property_id);

            $this->db->update('nc_property_project', $data);

            return $property_id;
            
        } else {
            $this->db->insert('nc_property_project', $data);

            $inserted_id = $this->db->insert_id();

            return $inserted_id;
        }

    }

    

    public function delete($where_in)
    {

        $this->db->where(" property_id in (" . $where_in . ")", null, false);

        $this->db->delete('nc_properties');

    }    

   

    

    function exists_property($property_id)
    {
        $this->db->where("property_id",$property_id);
        $query = $this->db->get('nc_properties',1);

        return ($query->num_rows() > 0);           
    }

    

    /**

     * This is method get_resources

     *

     * @return mixed returns the a type from resources eg. locations

     *

     */

    function get_resources($type = "")
    {

        $this->db->order_by("name", "ASC");         

        if(is_array($type)) {
            $this->db->where_in("resource_type",$type);
            
        } else {
            if($type != "")
                $this->db->where("resource_type",$type);
        }

        $query = $this->db->get("nc_resources");

        // If there is a resulting row, check that the password matches.

        if ($query->num_rows() > 0) {
            return $query;
        } else {
            return false;
        }
    }

        

    function get_hero_image($property_id)
    {

        $this->db->select('hero_image');

        $query = $this->db->get_where('nc_properties',array('property_id' => $property_id),1);

        // If there is a resulting row

        if ($query->num_rows() > 0) {

            $result = $query->first_row();

            return $result->hero_image;

        } else {
            return "";
        }
    }

    

    function get_reservation_dates($property_id)
    {

        $this->db->select('date_format(reservation_date,"%d/%m/%Y") as reservation_date');

        $query = $this->db->get_where('nc_property_reservations',array('property_id' => $property_id));

        return $query->result();

    }

    

    function exists_reservation($property_id,$date)
    {

        $this->db->where("property_id",$property_id);

        $this->db->where('date_format(reservation_date,"%d/%m/%Y")',$date);            

        $query = $this->db->get('nc_property_reservations',1);

        return ($query->num_rows() > 0);

    }

    

    function add_property_reservation($data)
    {
        $this->db->insert("nc_property_reservations",$data);

        return $this->db->insert_id();        
    }

    

    function get_properties($category_id = "")
    {

        $this->db->select("property_id,property_name");

        $this->db->order_by("property_name", "ASC");         

        if($category_id !="")

            $this->db->where('(primary_cat_id like "%#'.$category_id.'#%" OR secondary_cat_id like "%#'.$category_id.'#%" )');

        $query = $this->db->get("nc_properties");

        // If there is a resulting row

        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;

    }

    function get_states($country=0)

    {

        $this->db->order_by("name","ASC");

        if ($country != 0) {

        	$this->db->where("country_id",$country);

        }

        $query = $this->db->get("nc_states");

        // If there is a resulting row

        if ($query->num_rows() > 0)

        {

            return $query;

        }         

        else

            return false;

    }

    function get_property_types()

    {

        $query = $this->db->get("nc_project_types");

        

        // If there is a resulting row

        if ($query->num_rows() > 0)

        {

            return $query;

        }         

        else

            return false;

    }

    function delete_projects($property_id)

    {

        $this->db->where("property_id", $property_id);

        $this->db->delete("nc_property_project");

    }

    function save_projects($property_id, $project_id)

    {

        $data  = array(

            "property_id" => $property_id,

            "project_id"  => $project_id

        );

        

        $this->db->insert("nc_property_project", $data);

    }

    function get_projects($property_id)

    {

        $query = $this->db->get_where("nc_property_project",array("property_id" => $property_id),1);

        

        // If there is a resulting row

        if ($query->num_rows() > 0)

        {

            $result_array = array();

            foreach( $query->result_array() as $proj)

                array_push($result_array, $proj["project_id"]);

                

            return $result_array;

        }         

        else

            return false;

    }

    /**

    * This is method allow_property_for_agent check if the property it is allowed for agent

    *

    * @return mixed returns true if the property is not allowed else retun false

    *

    */

    function allow_property_for_agent($property_id, $user_id)

    {

        $this->db->select("property_id");

        $this->db->where("property_id", $property_id);

        $this->db->where(" ((`status` != 'sold_archived' && `status` != 'reserved' ) OR (`status` = 'reserved' && user_id ='".$user_id."')) ",null,false);           

    

        $query = $this->db->get("nc_properties");

       

        // If there is a resulting row

        if ($query->num_rows() > 0)

        {

            return true;

        }         

        else

            return false;

    }

    function add_reservation_code($property_id, $reservation_code = "")

    {

        /*UPDATE `nc_properties` SET `reservation_code` = CONCAT(`reservation_code`,"",'a') WHERE `nc_properties`.`property_id` =1 LIMIT 1 ;*/



        $this->db->set('reservation_code','CONCAT(`reservation_code`,"'.$reservation_code.'",";")',false);

        $this->db->where('property_id',$property_id);        

        $this->db->update('nc_properties');        

    }

    function get_property_id($reservation_code)

    {

        $this->db->select("property_id");

        

        $this->db->like("reservation_code",$reservation_code.";");   

        

        $query = $this->db->get("nc_properties",1);

        

        // If there is a resulting row

        if ($query->num_rows() > 0)

        {

            return $query->first_row();

        }         

        else

            return false;

    }

    function get_project($property_id)

    {

        $this->db->select("proj.*");

        $this->db->from("nc_property_project pp");                

        $this->db->join("nc_projects proj","proj.project_id = pp.project_id");

        $this->db->where("pp.property_id", $property_id);        

        

        $query = $this->db->get();

        

        // If there is a resulting row

        if ($query->num_rows() > 0)

        {

            return $query->first_row();            

        }         

        else

            return false;

    }

    function build_address_string($property, $include_state = true)
    {
		$address = "";

		if(!empty($property->lot))
        {
			$address .= "Lot " . $property->lot . ", ";
        }

		if(!empty($property->address))
        {
			$address .= $property->address . ", ";
        }

		if(!empty($property->suburb))
        {
			$address .= $property->suburb . " ";
        }

        if(($include_state) && (!empty($property->state)))
        {
		    $address .= $property->state;   
        }

		return $address;		
    }

    
    function get_property_status()
    {
        $status = array(
            //"new" => "New",
            "available" => "Available",
            "reserved" => "Reserved",
            "signed" => "Signed",
            "sold" => "Sold"
        );    

        return $status;
    }

    function get_quote_project()
    {
        $this->db->select("proj.project_id,proj.project_name,proj.prices_from");
        $this->db->group_by("project_id"); 
        
        $this->db->from("nc_property_project pp");                
        $this->db->join("nc_projects proj","proj.project_id = pp.project_id");
        $this->db->join("nc_properties p","p.property_id = pp.property_id");
        $this->db->where("p.status", "available");        

        $query = $this->db->get();

        

        // If there is a resulting row

        if ($query->num_rows() > 0)

        {

            return $query->result();            

        }         

        else

            return false;

    }

    function get_quote_lot_number_in_project($project_id,$allstatus=false)
    {
        $this->db->select("p.lot,p.address,p.property_id,p.suburb,p.state_id,p.design_id,p.title,p.address,proj.project_type");
        //$this->db->group_by("project_id");
        $this->db->from("nc_properties p");                
        $this->db->join("nc_property_project pp","pp.property_id = p.property_id");
        $this->db->join('nc_projects proj', 'pp.project_id = proj.project_id');

        if (!$allstatus) {
            $this->db->where("p.status", "available");   
        }
        
        $this->db->where("pp.project_id", $project_id);   
        $this->db->order_by("property_id asc");
        $query = $this->db->get();

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {

            return $query->result();
            echo "<pre>";
            print_r($query->result());
            echo "</pre>";
        }         
        else
            return false;

    }
    
    function get_property_min_max()
    {
        $select = "MIN(total_price) as min_total_price, MAX(total_price) as max_total_price " .
            ", MIN(bedrooms) as min_bedrooms, MAX(bedrooms) as max_bedrooms" .
            ", MIN(bathrooms) as min_bathrooms, MAX(bathrooms) as max_bathrooms" .
            ", MIN(garage) as min_garage, MAX(garage) as max_garage" .
            ", MIN(house_area) as min_house, MAX(house_area) as max_house" .
            ", MIN(land) as min_land, MAX(land) as max_land" .
            ", MIN(rent_yield) as min_yield, MAX(rent_yield) as max_yield";
            
        $this->db->select($select, true);
        $this->db->from("properties");
        $this->db->where("enabled", 1);
        $this->db->where("archived", 0);
        $this->db->where("total_price >", 0);

        $result = $this->db->get();
        
        if($result->num_rows <= 0)
        {
            return false;
        } 
        
        return $result->row();
    }
}