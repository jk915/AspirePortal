<?php

class Link_model extends CI_Model 

{

    function Link_model()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_list($link_type = "", $foreign_id = "", $params = array())                        
    {

        if($link_type != "")
            $this->db->where("link_type",$link_type);

        if($foreign_id != "")
            $this->db->where("foreign_id",intval($foreign_id));

        $query = $this->db->get("nc_links");

        if ($query->num_rows() > 0) {
            return $query;
        } else
            return false;
    }

    public function get_details($link_id)
    {
        $query = $this->db->get_where('nc_links',array('link_id' => $link_id),1);        

        // If there is a resulting row

        if ($query->num_rows() > 0) {
            return $query->row();
        }         
        else
            return false;
    }

    public function save($link_id,$data, $foreign_id = "")
    {           

        if (is_numeric($link_id)) {
            
            if($foreign_id != "")    
                $this->db->where("foreign_id",$foreign_id);

            $this->db->where('link_id',$link_id);
            $this->db->update('nc_links',$data);

            return $link_id;
            
        } else {                                              

            $this->db->insert('nc_links',$data);
            return $this->db->insert_id();                  
        }

    }  
    
    public function delete($ids, $link_type='' )
    {

    	if( $link_type == '' )
        	$this->db->where_in("link_id", $ids);

        else {
        	$this->db->where('foreign_id', $ids);
        	$this->db->where('link_type', $doc_type);
        }
        $this->db->delete("nc_links");         
    }
}