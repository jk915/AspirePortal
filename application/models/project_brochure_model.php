<?php
class Project_brochure_model extends CI_Model 
{
    function Project_brochure_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
    public function get_list($filters = array(), $order_by = "page ASC",$limit = "")
    {
        $this->db->select("b.*, ac.name as category_name, a.article_title, a.hero_image hero_image_link")
            ->from("brochures b");
            
        $this->db->join("project_brochure pb", "pb.brochure_id = b.brochure_id", "inner");
        $this->db->join("article_categories ac", "ac.category_id = b.asset_category", "left");
        $this->db->join("articles a", "a.article_id = b.asset", "left");
		
        if ( isset($filters['project_id']) ) {
        	$this->db->where('project_id', intval($filters['project_id']));
        }
		
		if ($limit) {
			$this->db->limit($limit);
		}
		
		$this->db->order_by($order_by);
		
		$result = $this->db->get();
		
        if($result->num_rows() > 0) {
			return $result;
		} else {
			return false;
		}			              
    }
    
    public function get_list_default($project_id = "", $create = false)
    {
        $this->db->select("*")
            ->from("project_brochure_default b");
		
		$this->db->order_by("page ASC");
		
		$result = $this->db->get();
        
        // Create the default brochure pages for the specified project
        if(($result) && (is_numeric($project_id)) && ($create)) {
            
            foreach($result->result() as $brochure)
            {
                $data = array(
                    'heading' => $brochure->heading,
                    'type'    => $brochure->type
                );
                
                $this->save($project_id, $data);
            }    
        }
            
        return $result;
    }
  
    public function get_details($brochure_id)
    {
    	$query = $this->db->get_where('brochures', array('brochure_id' => $brochure_id));

        if ($query->num_rows() > 0) {
           return $query->row();
        } else
            return false;
    }

	public function save($project_id = "", $data = array())
	{
        if(!isset($data['brochure_id']))
        {
            $this->db->from('brochures b');
            $this->db->where($data);
            $this->db->where('project_id', $project_id);
            $this->db->join("project_brochure pb", "pb.brochure_id = b.brochure_id", "inner");
            $query = $this->db->get();
            
            if ($query->num_rows() > 0) {
               return false;
            }
            
            //count num of page
            $query = $this->db->get_where('project_brochure', array('project_id' =>$project_id));
            $count = $query->num_rows();
            
            $data['page'] = $count + 1;
            $this->db->insert('brochures',$data);    
            $brochure_id = $this->db->insert_id(); 
            $this->db->insert('project_brochure', array('project_id' => $project_id, 'brochure_id' => $brochure_id));    
            return $brochure_id;
        }
        else
        {
            $this->db->where('brochure_id', $data['brochure_id']);
            $this->db->update('brochures', $data); 
            return $data['brochure_id'];
        }
    }
    
    
	public function delete($brochure_id, $project_id)
	{
        $delete_brochure = $this->get_details($brochure_id);
    
        $this->db->where('brochure_id',$brochure_id)
            ->delete('brochures');
        $this->db->where('brochure_id',$brochure_id)
            ->where('project_id',$project_id)
            ->delete('project_brochure');
            
        $where = 'brochure_id IN (
                SELECT pb1.brochure_id 
                FROM nc_project_brochure pb1 
                WHERE pb1.project_id='.$project_id.'
                ) AND page > '.$delete_brochure->page;
        $this->db->where($where);
        $this->db->set('page', 'page-1', FALSE);
        $this->db->update('brochures');
        
        //delete files
        if (file_exists($delete_brochure->image)) unlink($delete_brochure->image);
        if (file_exists($delete_brochure->image . "_thumb.jpg")) unlink($delete_brochure->image . "_thumb.jpg");               
            
        return true;
	}
    
    public function updatePage($project_id = 0, $brochure_id = 0, $page = 0, $change = 1)//1= up, -1 = down
    {
        if($project_id == 0 || $page == 0 || $brochure_id == 0)
            return false;
            
        $where = 'brochure_id IN (
                SELECT pb1.brochure_id 
                FROM nc_project_brochure pb1 
                WHERE pb1.project_id='.$project_id.'
                ) AND page = '.($page + $change);
        $this->db->update('brochures', array('page' => -1), $where);
        
        
        $this->db->update('brochures', array('page' => $page + $change), array('brochure_id' => $brochure_id));
        
        $this->db->update('brochures', array('page' => $page), array('page' => -1));
    
        return true;
    }
}