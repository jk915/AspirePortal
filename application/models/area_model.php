<?php
class Area_model extends CI_Model 
{
	function Area_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $order_by = "nc_areas.area_name ASC", $builder_id = "", $filters = array())
    {
        //count all result
        $this->db->select('*, nc_states.name as state_name');
        $this->db->from('nc_areas');
		$this->db->join('nc_states','nc_states.state_id = nc_areas.state_id','left');
        
        if($enabled > -1) { 
            $this->db->where('nc_areas.enabled', $enabled);            
        }
         
        if($search_term != "") {
            $this->db->like('nc_areas.area_name', $search_term);
        }
		
		$this->apply_filters($filters);

        if ($builder_id != "") {
            $this->db->join("nc_properties p", "p.area_id = nc_areas.area_id");
            $this->db->where("p.builder_id", $builder_id);
            $this->db->group_by("nc_areas.area_id");
        }
        
        $count_all = $this->db->count_all_results();
        
        //with limit
		$this->db->select('*, nc_states.name as state_name');
        $this->db->from('nc_areas');
		$this->db->join('nc_states','nc_states.state_id = nc_areas.state_id','left');
        
        if($enabled > -1) {
           $this->db->where('nc_areas.enabled', $enabled);            
        }
         
        if($search_term !="")  {
            $this->db->like('nc_areas.area_name',$search_term);
		}
        
        $this->apply_filters($filters);

        if ($builder_id != "") {
            $this->db->join("nc_properties p", "p.area_id = nc_areas.area_id");
            $this->db->where("p.builder_id", $builder_id);
            $this->db->group_by("nc_areas.area_id");
        }
            
        $this->db->order_by($order_by);     
                
        if ($limit != "" && $page_no != "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }
                                                                                     
        $query = $this->db->get();
		// echo $this->db->last_query();die;
		// echo $query->num_rows();die;
		if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
	
	
	private function apply_filters($filters)
    {
        filter_where($filters, "state_id", "nc_areas.");
		
		if ((isset($filters['project_id'])) && ($filters['project_id'] != ""))  {
            $this->db->join("nc_projects ncp", "ncp.area_id = nc_areas.area_id");
			filter_where($filters, "project_id", "ncp.");
		}

		$numeric_filters = array();
        $numeric_filters["min_total_price"] = array("nc_areas.median_house_price", ">=");
        $numeric_filters["max_total_price"] = array("nc_areas.median_house_price", "<=");

        foreach($numeric_filters as $filter_key => $filter_values)
        {
            if((array_key_exists($filter_key, $filters)) && (is_numeric($filters[$filter_key])))
            {
                $filter_db_field = $filter_values[0];
                $filter_operator = $filter_values[1];
                $this->db->where($filter_db_field . " " . $filter_operator, $filters[$filter_key]); 
            }              
        }
        
    }
	
	
	public function get_details($area_id, $by_name = false)
	{
		// Check to see if a record with this username exists.
		if(!$by_name)
			$query = $this->db->get_where('nc_areas', array('area_id' => $area_id));
		else
			$query = $this->db->get_where('nc_areas', array('area_name' => $area_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
	function save($builder_id,$data)
	{
		if (is_numeric($builder_id))
		{
			$this->db->where('area_id',$builder_id);
			$this->db->update('nc_areas',$data);
            return $builder_id;
		}
		else
		{
			$this->db->insert('nc_areas',$data);
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where("area_id in (".$where_in.")",null,false);
		$this->db->delete('nc_areas');
	}
	
	function get_area_min_max()
    {
        $select = "MIN(median_house_price) as min_median_house_price, MAX(median_house_price) as max_median_house_price ";
            
        $this->db->select($select, true);
        $this->db->from("areas");
        $this->db->where("enabled", 1);
        $this->db->where("median_house_price >", 0);

        $result = $this->db->get();
        
        if($result->num_rows <= 0)
        {
            return false;
        } 
        
        return $result->row();
    }
    
    /***
    * Deletes the google map image associated with the current area.
    * 
    * @param integer $area_id  The area to delete the map for.
    */
    function delete_map_image($area_id) 
    {
        if((!is_numeric($area_id)) || ($area_id <= 0)) {
            return;    
        }
        
        // Google maps image
        $map_image = "area_files/" . $area_id . "/map.png";
        $map_image_abs = ABSOLUTE_PATH . $map_image;
        
        if(file_exists($map_image_abs)) {
            unlink($map_image_abs);
        }        
    }
    
    /***
    * Create the google map image for the specified area, using the embed code stored against that area.
    * 
    * @param integer $area_id  The id of the area to regenerate the map image for.
    */
    function create_map_image($area_id)
    {
        if((!is_numeric($area_id)) || ($area_id <= 0)) {
            return;    
        }        
        
        // Load the project
        $area = $this->get_details($area_id);
        if(!$area) {
            return;
        }
        
        $map_code = $area->googlemap;
        if($map_code == "") {
            return;    
        }

        // Ensure the project directory exists
        $map_dir = ABSOLUTE_PATH . "area_files/" . $area_id;
        if(!is_dir($map_dir)) {
            mkdir($map_dir);    
        }
        
        // Google maps image
        $map_image = "area_files/" . $area_id . "/map.png";
        $map_image_abs = ABSOLUTE_PATH . $map_image;
        
        if((!file_exists($map_image_abs)) || (filesize($map_image_abs) <= 0)) {
            // Get the lat/lng pairs out of the embed url
            $found = preg_match_all("/ll=[-\d\.]*,[-\d\.]*/", $map_code, $matches);
            
            if($found > 0) {
                $matched = $matches[0];
                $num_matches = count($matched);
                $latlng = str_replace("ll=", "", $matched[$num_matches - 1]);    
                $url = "http://maps.googleapis.com/maps/api/staticmap?center=" . $latlng . "&zoom=12&size=640x452&sensor=true&key=" . GOOGLE_APIKEY;             
            }
            
            $map = file_get_contents($url);
            if(strlen($map) > 100) {
                file_put_contents($map_image_abs, $map);  
            }
        }               
    }    	
}