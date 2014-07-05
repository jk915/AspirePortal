<?php
	/***
	* @helper db_helper 
	* @author Andrew Chapman
	*/
    
    function filter_where($filters, $field, $alias = "")
    {
        $ci = &get_instance();  
        
        if(array_key_exists($field, $filters))
        {
            if(!empty($filters[$field]))
            {
                $ci->db->where($alias . $field, $filters[$field]);
            }
        }
    }
