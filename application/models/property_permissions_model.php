<?php
class Property_permissions_model extends CI_Model 
{
    function Property_permissions_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "created_dtm ASC", $items_per_page = 0, $offset = 0, $count_all=false)
    {
        $this->db->select("pp.*, p.project_id, p.project_name, pro.property_id, pro.lot, pro.address, st.name AS state_name")
            ->from("property_permissions AS pp");
		
        if ( isset($filters['property_permissions_id']) && intval($filters['property_permissions_id']))
        	$this->db->where('pp.id', $filters['property_permissions_id']);
        	
    	if ( isset($filters['user_id']) && intval($filters['user_id']))
        	$this->db->where('pp.user_id', $filters['user_id']);
        	
        if ( isset($filters['permission_type']) && !empty($filters['permission_type']))
        	$this->db->where('pp.permission_type', trim($filters['permission_type']));
        	
    	if ( isset($filters['foreign_id']) && intval($filters['foreign_id']) )
    		$this->db->where('pp.foreign_id', $filters['foreign_id']);
    		
		if ( isset($filters['project_id']) )
		{
			$this->db->where_in('property_project.project_id', $filters['project_id']);
		}
		
		if(isset($filters["not_in_property"]) && sizeof($filters["not_in_property"])) {
            $this->db->where_not_in('pp.foreign_id', $filters["not_in_property"]);
        }
    		
		$this->db->join('projects as p',"p.project_id = pp.foreign_id",'left');
		$this->db->join('properties as pro',"pro.property_id = pp.foreign_id",'left');
		$this->db->join('property_project', 'pro.property_id = property_project.property_id','left');
		$this->db->join('states st', 'st.state_id = pro.state_id','left');
    		
		if($items_per_page > 0)
		{
            $this->db->limit($items_per_page);
            $this->db->offset($offset);
        }
        
		$this->db->order_by($order_by);
		
		$result = $this->db->get();
		
		if($result->num_rows() > 0)
			if ($count_all)
				return $result->num_rows();
			else
				return $result;
		else
			return false;
    }
  
    public function get_details($property_permissions_id)
    {
    	$query = $this->db->get_where('property_permissions', array('id' => $property_permissions_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($property_permissions_id, $data)
	{
		if(is_numeric($property_permissions_id)) {
			$this->db->where('id',$property_permissions_id);
			
			if($this->db->update('nc_property_permissions',$data))
				return $property_permissions_id;	
			else
				return false;
		} else {
			$this->db->insert('nc_property_permissions',$data);    
			return $this->db->insert_id(); 
		}
	}
    
	public function delete($id)
	{
        $this->db->where('id',$id)
            ->delete('property_permissions');
        return true;
	}
	
	public function delete_project($id, $project_id, $user_id)
	{
		
        $this->db->where('id',$id)
            ->delete('property_permissions');
            
        $permission_type = 'Property';
  		$this->db->query("
						    DELETE per
						    FROM nc_property_permissions per 
						    JOIN nc_property_project pp ON per.foreign_id = pp.property_id
						    JOIN nc_projects proj ON pp.project_id = proj.project_id
						    WHERE (pp.project_id = $project_id) 
						    AND (per.permission_type = 'Property')
						    AND (per.user_id = $user_id)
					    ");
        return true;
	}
	
	function exists_project($project_id,$user_id)
    {
        $this->db->where("foreign_id",$project_id);
        $this->db->where("user_id",$user_id);
        $this->db->where("permission_type",'Project');
        $query = $this->db->get('nc_property_permissions',1);

        if ($query->num_rows() > 0)
           return $query->row();
        else
           return false;
    }
    
    function exists_property($property_id,$user_id)
    {
        $this->db->where("foreign_id",$property_id);
        $this->db->where("user_id",$user_id);
        $this->db->where("permission_type",'Property');
        $query = $this->db->get('nc_property_permissions',1);

        if ($query->num_rows() > 0)
           return $query->row();
        else
           return false;
    }
	
}