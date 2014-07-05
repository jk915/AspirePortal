<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
*/
class Project_model extends CI_Model 
{
    function Project_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($enabled = -1, $limit = "", $page_no = "", &$count_all, $search_term = "", $order_by = "p.project_name ASC", $filters = array(), $builder_id = "")
    {
        //count all result
        $this->db->select('p.*, a.area_name, s.name as state');
        $this->db->from('projects p');
        $this->db->join('areas a','a.area_id = p.area_id','left');
        $this->db->join('states s','s.state_id = a.state_id','left');

        $this->apply_filters($enabled, $search_term, $filters);

        if ($builder_id != "") {
            $this->db->join('properties prop', 'prop.area_id = a.area_id');
            $this->db->where('prop.builder_id', $builder_id);
            $this->db->group_by('p.project_id');
        }

        $count_all = $this->db->count_all_results();

        //with limit
        $this->db->select('p.*, a.area_name, s.name as state');
        $this->db->from('projects p');
        $this->db->join('areas a','a.area_id = p.area_id','left');
        $this->db->join('states s','s.state_id = a.state_id','left');

        $this->apply_filters($enabled, $search_term, $filters);
        
        if ($builder_id != "") {
            $this->db->join('properties prop', 'prop.area_id = a.area_id');
            $this->db->where('prop.builder_id', $builder_id);
            $this->db->group_by('p.project_id');
        }

        $this->db->order_by($order_by);     
                
        if ($limit != "" && $page_no != "" && $count_all > $limit)
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
    
    private function apply_filters($enabled, $search_term, $filters)
    {
        if($enabled > -1)
        {
            $this->db->where('p.enabled', $enabled);            
        }

        if($search_term != "") 
        {
            $this->db->like('p.project_name',$search_term);
            $this->db->or_like('p.project_code', $search_term);
        }
        
        if(isset($filters["permissions_user_id"]) && intval($filters["permissions_user_id"]))
        {
            $this->db->join('property_permissions per', 'p.project_id = per.foreign_id');
            $this->db->where('per.permission_type', 'Project');
            $this->db->where('per.user_id', $filters["permissions_user_id"]);
        }
        
        if(isset($filters["is_featured"]) && intval($filters["is_featured"]))
        {
            $this->db->where('p.is_featured', 1);
        }
        
        filter_where($filters, "area_id", "a.");
        filter_where($filters, "state_id", "a.");
        filter_where($filters, "archived", "p.");
        
        if(array_key_exists("has_available", $filters))
        {
            // We only want projects that actually have available projects.
            $where = "(SELECT COUNT(ip.property_id) " .
                "FROM nc_properties ip " .
                "INNER JOIN nc_property_project ipp ON ip.property_id = ipp.property_id " .
                "WHERE ip.enabled = 1 " .
                "AND ip.status = 'available' " .
                "AND ip.archived = 0 " .
                "AND ipp.project_id = p.project_id ) > 0";
                
            $this->db->where($where);
        }
        
        $numeric_filters = array();
        $numeric_filters["min_total_price"] = array("p.prices_from", ">=");
        $numeric_filters["max_total_price"] = array("p.prices_from", "<=");
        
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
    
    /**
     * @desc The get_details method loads all projects of a particular project as defined by project_id
     */
    public function get_details($project_id)
    {
        $this->db->select("p.*, a.*");
        $this->db->from('projects p');
        $this->db->join("areas a", "p.area_id = a.area_id", "left");
        $this->db->where('p.project_id',$project_id);
        
        $query = $this->db->get();        
        
        // check If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
    }
    
    function get_project_id($project_code)
    {
        $this->db->select('project_id');
        $this->db->where('project_code',$project_code);
        $this->db->where('enabled','1');        
        
        $query = $this->db->get('nc_projects',1);        
        
        // check If there is a resulting row
        if ($query->num_rows() > 0)
        {
            $project = $query->row();
            return $project->project_id;
        }         
        else
            return false;
    }
    
    function exists_project_code($project_code,$project_id)
    {
        $this->db->where('project_code',$project_code);
        $this->db->where('project_id !=',$project_id);
                
        $query = $this->db->get('nc_projects',1);       
        
        return ($query->num_rows() > 0);           
    }
    
    function get_projects($order_by = "", $project_id = "", $show_archived = TRUE, $show_project_type_name = FALSE, $project_type_id = "", $not_on_newsletter = -1, $current_projects=array())
    {
        $this->db->select('p.*, s.name as state');
        $this->db->from("nc_projects p");
        $this->db->join('nc_states s','s.state_id = p.state_id','left');
        if($show_project_type_name)
        {
            $this->db->select("p.*, pt.name as project_type_name");
            $this->db->join("nc_project_types pt", "p.project_type = pt.project_type_id");    
        }
        
        if($project_id != "")    
            $this->db->where("p.project_id", $project_id);
            
        if($not_on_newsletter > -1)   
        {
            $this->db->where("p.not_on_newsletter", $not_on_newsletter);            
		}
        
        if(!$show_archived)
            $this->db->where("p.archived", "0");
            
        if($project_type_id != "")    
            $this->db->where("p.project_type", $project_type_id);
        
        if (sizeof($current_projects)) {
        	$this->db->where_not_in('p.project_id', $current_projects);
        }
                
        $this->db->where('p.enabled','1');            
        
        if($order_by != "")
            $this->db->order_by($order_by);
        
        $query = $this->db->get();
        
        //die($this->db->last_query());
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    public function save($project_id,$data)
    {
        if (is_numeric($project_id))
        {
            $this->db->where('project_id',$project_id);
            $this->db->update('nc_projects',$data);
            return $project_id;
        }
        else
        {
            $this->db->insert('nc_projects',$data);
            $inserted_id = $this->db->insert_id();
            
            return $inserted_id;
        }
    }
    
    public function delete($where_in)
    {
        $this->db->where(" project_id in (".$where_in.")",null,false);
        $this->db->delete('nc_projects');
    }
    
	public function get_next_order()
	{
		// Get the next available article_order number for article categories within the same parent id
		$this->db->select_max('project_order');
		$query = $this->db->get('projects');	

		$row = $query->row();
		$next_order = $row->project_order;

		if(($next_order == null) || ($next_order == ""))
			return 1;
		else
			return ($next_order + 1);
	} 
    
    public function get_project_type($project_id)
    {
        $this->db->select('pt.project_type_id');
        $this->db->from('nc_projects p');
        $this->db->join('nc_project_types pt','pt.project_type_id = p.project_type');
        $this->db->where('p.project_id', $project_id);
        $this->db->limit(1);
        
        $query = $this->db->get();
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
    }

	public function get_avg( $property_id )
    {
    	$this->db->select('ROUND(AVG(bathrooms)) AS avg_bathrooms, ROUND(AVG(bedrooms)) AS avg_bedrooms, ROUND(AVG(garage)) AS avg_garage  
						  FROM nc_projects p INNER JOIN nc_property_project pp ON pp.project_id = p.project_id INNER JOIN nc_properties pr ON 
						  pp.property_id = pr.property_id where pp.project_id =  '.$property_id
    					);
    	//$this->db->where('pp.property_id =  '.$property_id, NULL, FALSE);
    	$query = $this->db->get();

    	if($query->num_rows() > 0)
    	{
    		return $query->row();
    	}
    	else
    	{
    		return false;
    	}
    }
	
    function get_project_rates()
    {
        $rates = array(
			'very_low' => 'Very Low',
            'low' =>'Low',
            'medium' => 'Medium',
            'high' =>'High',
            'very_high' =>'Very High'
        );    
        return $rates;
    }
    
    /***
    * Finds the project assigned to the specified property and returns it
    * @param int $property_id The id of the property to find the project for.
    * 
    * @returns A project record, false on failure.
    */
    function get_property_project($property_id, $user_id)
    {
        $this->db->select("p.*, get_available_property_count(p.project_id, $user_id) as num_available_properties", false);
        $this->db->from("projects p");    
        $this->db->join("property_project pp", "p.project_id = pp.project_id", "inner");
        $this->db->where("pp.property_id", $property_id);
        
        $result = $this->db->get();

        if($result->num_rows() != 1)
        {
            return false;    
        }
        
        return $result->row();
    }
    
    function get_project_min_max()
    {
        $select = "MIN(prices_from) as min_total_price, MAX(prices_from) as max_total_price ";
            
        $this->db->select($select, true);
        $this->db->from("projects");
        $this->db->where("enabled", 1);
        $this->db->where("archived", 0);
        $this->db->where("prices_from >", 0);

        $result = $this->db->get();
        
        if($result->num_rows <= 0)
        {
            return false;
        } 
        
        return $result->row();
    }
    
    /***
    * Deletes the google map image associated with the current project.
    * 
    * @param integer $project_id  The project to delete the map for.
    */
    function delete_map_image($project_id) 
    {
        if((!is_numeric($project_id)) || ($project_id <= 0)) {
            return;    
        }
        
        // Google maps image
        $map_image = "project_files/" . $project_id . "/map.png";
        $map_image_abs = ABSOLUTE_PATH . $map_image;
        
        if(file_exists($map_image_abs)) {
            unlink($map_image_abs);
        }        
    }
    
    /***
    * Create the google map image for the specified project, using the embed code stored against that project.
    * 
    * @param integer $project_id  The id of the project to regenerate the map image for.
    */
    function create_map_image($project_id)
    {
        if((!is_numeric($project_id)) || ($project_id <= 0)) {
            return;    
        }        
        
        // Load the project
        $project = $this->get_details($project_id);
        if(!$project) {
            return;
        }
        
        $map_code = $project->google_map_code;
        if($map_code == "") {
            return;    
        }

        // Ensure the project directory exists
        $map_dir = ABSOLUTE_PATH . "project_files/" . $project_id;
        if(!is_dir($map_dir)) {
            mkdir($map_dir);    
        }
        
        // Google maps image
        $map_image = "project_files/" . $project_id . "/map.png";
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