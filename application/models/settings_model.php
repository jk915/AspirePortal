<?php
class Settings_model extends CI_Model 
{
    function Settings_model()
    {
        // Call the Model constructor
        parent::__construct();
    }
    
    function get_details($setting_type = "owner_details")
    {
        
        $query = $this->db->get_where('nc_global_settings', array('setting_type' => $setting_type));

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query;                   
        }         
        else
            return false;
    }
    
    function get_details_array( $setting_type = "owner_details" )
    {
        $result_array = array();
        $query = $this->get_details( $setting_type );
        
        if ( $query )
        {
            foreach($query->result_array() as $row)
            {
                $result_array[$row["setting_name"]] = $row["setting_value"];
            }
        }
        
        return $result_array;            
    }
    
    function save($setting_type, $data)
    {
        foreach($data as $key => $value)
        {
           $this->db->set("setting_value", $value); 
           $this->db->where("setting_type", "owner_details");
           $this->db->where("setting_name", $key);            
           $this->db->update("nc_global_settings");
        }
        
        return true;
    }
    
    /*
    add the missing fields
    */
    function add_fields($setting_type, $default_fields)
    {
       $result_array = $this->get_details_array();
       
       foreach($default_fields as $key => $value)
       {
           if(!isset($result_array[$key]))
           {
                $data = array(
                    "setting_type" => $setting_type,
                    "setting_name" => $key,
                    "setting_value" => ''
                );
                $this->db->insert("nc_global_settings", $data);                                       
           } 
       }        
    }
    /* CONTACTS*/
    function get_contacts( $where = "")
    {
        /*$this->db->select("cs.*, w.website_id, w.website_name");
        $this->db->from("nc_contact_settings cs");
        $this->db->join("nc_websites w", "cs.website_id = w.website_id",'left');
        */
    	if($where != "") $this->db->where($where);
        
        $query = $this->db->get( 'nc_contact_settings' );
        
         // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query;                   
        }         
        else
            return false;
    }
    
     function save_contact($contact_id, $data)
    {
        if (is_numeric($contact_id))
        {
            $this->db->where('id',$contact_id);
            return $this->db->update('nc_contact_settings',$data);
        }
        else
        {
            $this->db->insert('nc_contact_settings',$data);
            return $this->db->insert_id();
        }
    }
    
    public function delete_contacts($where_in)
    {
        $this->db->where(" id in (".$where_in.")",null,false);
        $this->db->delete('nc_contact_settings');
    }
    
    function update_notification($where_in, $update_field = 'contact_notification')
    {
        if($where_in != "")
        {
            $this->db->set($update_field, '1');
            $this->db->where(" id in (".$where_in.")", null, false);
            $this->db->update("nc_contact_settings");
            
            //$this->utilities->add_to_debug("Query: " . $this->db->last_query());
            
            //the others set as 0
            
            $this->db->set($update_field, '0');
            $this->db->where(" id not in (".$where_in.")", null, false);
            $this->db->update("nc_contact_settings");   
            //$this->utilities->add_to_debug("Query: " . $this->db->last_query());    
        }
        else
        {
            $this->db->set($update_field, '0');
            $this->db->update("nc_contact_settings");
        } 
        
    }
    
    /**
     * @method	delete_all_notification
     * @access	public
     * @desc	this method sets all notification to 0 before we updates from admin page
     * @author	Zoltan Jozsa
     * @return unknown_type
     */
    public function delete_all_notification()
    {
    	$this->db->set( 'order_notification', '0' );
    	$this->db->set( 'contact_notification', '0' );
    	
    	return $this->db->update( 'nc_contact_settings' );
    }
    
    /***
    * Gets a list of recipients for contact notifications and stores each recipient in an array
    * @returns array array of recipients
    */
    function get_contact_notification_recipients()
    {
        $recipients = array();
        
        $contacts = $this->get_contacts("contact_notification = 1");
        
        if($contacts)
        {
            foreach($contacts->result() as $contact)
            {
                if($contact->email != "")
                {
                    $recipients[] = $contact->email;
                }
            }    
        } 
        
        return $recipients;            
    }
}  