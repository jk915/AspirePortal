<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
*/
class Page_model extends CI_Model 
{
    function Page_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    

    /***
    * @method get_list
    * @author Andrew Chapman
    * @abstract This metod gets a list of all pages from the database.  By default the page
    * list will return only pages that are defined as "showing", but this can be bypassed with the
    * ignore_showing_flag parameter.  You can also restrict the list to pages that are showing 
    * on the sitemap.
    * 
    * @param mixed $ignore_showing_flag If set to true, the is_showing flag will be ignored and all pages returned.
    * @param mixed $sitemap_only If set to true, the page list will be restricted to those that are showing on the site map.
    * 
    * @returns A list of pages
    */
    public function get_list($ignore_showing_flag = false, $sitemap_only = false, $limit = "", $page_no = "", &$count_all, $search = "", $website_id = "")
    {
        //$this->_get_list($ignore_showing_flag, $sitemap_only, $limit, $page_no, $count_all, $search, $website_id, false);
        //$count_all = $this->db->count_all_results();
        
        $count_all = $this->count_pages();

       /* echo "cccc=".$count_all;
        echo $this->db->last_query()."<br/>";  */
        $this->_get_list($ignore_showing_flag, $sitemap_only, $limit, $page_no, $count_all, $search, $website_id, false);
        $query = $this->db->get();
       // echo $this->db->last_query()."<br/>";  die();
        
        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
       
    }

    function count_pages()
    {
        $query = $this->db->get('nc_pages');
        if($query->num_rows()>0)
            return $query->num_rows();
        else
            return 0;;
    }
    
    function _get_list($ignore_showing_flag = false, $sitemap_only = false, $limit = "", $page_no = "", $count_all, $search = "", $website_id = "", $only_count = false)
    {
      
        $website = 'SELECT GROUP_CONCAT(w1.website_name SEPARATOR " &amp; ") 
                    FROM nc_websites w1 
                    LEFT JOIN nc_website_assm wp1 ON w1.website_id = wp1.website_id 
                    WHERE wp1.foreign_id  = p.page_id
                    AND wp1.type = "page"';
                      
        $this->db->select('p.*, ('.$website.') as website_name');
        $this->db->from('nc_pages p');
        $this->db->join('nc_website_assm wp','wp.foreign_id = p.page_id AND wp.type = "page"','left');
        $this->db->join('nc_websites w','w.website_id = wp.website_id','left');
        
        if($sitemap_only)
            $this->db->where('p.enabled', 1);
        
        if(!$ignore_showing_flag)      
            $this->db->where('p.enabled', 1);
            
        if($search != "")    
            $this->db->where('(p.page_title like "%'.$search.'%" OR p.page_code like "%'.$search.'%") ');
        
        if($website_id != "")
            $this->db->where('w.website_id',$website_id);
       
        if(!$only_count)    
        {
            $this->db->group_by("p.page_id");
            //$this->db->order_by("p.page_title", "ASC");
        }
        //var_dump($count_all > $limit);
        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }
    }
   
   /**
   * @desc The get_details method loads all properties of a particular page as defined by page_code
   */
    public function get_details($page_identifier, $byid = FALSE)
    {
        // Check to see if a record with this username exists.
        if ($byid)
        {
        	// Load the page by id
            $query = $this->db->get_where('nc_pages', array('page_id' => $page_identifier));
        }
        else
        {	
   			// Load the page by page code
            $this->db->select("pages.*");
            $this->db->from("pages");
	        $this->db->where("pages.page_code", $page_identifier);
	        $this->db->where("pages.enabled", 1);
	        $query = $this->db->get();        	
        }

        // If there is a resulting row, check that the password matches.
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
    }
    
    /***
    * @method    load_column_html
    * @author    Andrew Chapman
    * @requires Block model to be loaded
    * 
    * @desc    This method loads the blocks that are assigned to the given page via the 
    * custom layout tool.  The result is a html string containing all the blocks, 
    * one after another.
    * 
    * @param mixed $page_id    The page to load the custom blocks for.
    * @param mixed $column    THe column to load the assigned blocks for (left, middle, right)
    * @return mixed    The html string containing the block html is returned.
    */
    public function load_column_html($page_id, $column = "right")
    {        
        $section_html = "";        
        
        // Build the custom page string based on the page id
        $custom_page_name = "custom_page_" . $page_id;
        
        // Load the assigned blocks
        $query = $this->db->get_where('page_settings', array('page_name' => $custom_page_name, 'setting_value' => $column));

        if ($query->num_rows() == 0)
            return "";

        // Loop through the assigned blocks and load them.
        foreach($query->result() as $row)
        {
            $block_name = $row->setting_name;
            
            // Get the block id from the block name. 
            $last_underscore_pos = strrpos($block_name, "_");
            if(!$last_underscore_pos)
                continue;
            
            $block_id = substr($block_name, $last_underscore_pos+1);
            $block = $this->Block_model->get_block($block_id);    
            
            // Add the block html to the result string
            $section_html .= $block->block_desc;
        }
        
        // Replace any SITEURL placeholders
        $section_html = str_replace("SITEURL/", base_url(), $section_html);        
        
        // Return the result string
        return $section_html;        
    }
    
    /**
    * @method check_user_permission
    * @author Andrew Chapman
    * @abstract This method checks to see if the logged in user has permission to access
    * the current page.
    * 
    * @param mixed $page_id - The id of the page the visitor is trying to view
    * @param mixed $user_type_id - The users user_type_id
    */
    public function check_user_permission($page_id, $user_type_id)
    {
        if(($page_id == "") || (!is_numeric($page_id)))
            return false;
            
        if(($user_type_id == "") || (!is_numeric($user_type_id)))
            return false;            
        
        $query = $this->db->get_where('nc_pages_user_types', array('page_id' => $page_id, 'user_type_id' => $user_type_id));

        return ($query->num_rows() > 0);    
    }
    
    public function save($page_id,$data)
    {
        

        if (is_numeric($page_id))
        {
            $this->db->where('page_id',$page_id);
            $this->db->update('nc_pages',$data);
            
            return $page_id;
        }
        else
        {
            $this->db->insert('nc_pages',$data);
            return $this->db->insert_id();
        }
    }
    
    public function delete($where_in)
    {
        $this->db->where(" page_id in (".$where_in.")",null,false);
        $this->db->delete('nc_pages');
    }
    
    function exists_page_code($page_code,$page_id)
    {
        $this->db->where('page_code',$page_code);
        $this->db->where('page_id !=',$page_id);
                
        $query = $this->db->get('nc_pages',1);       
        
        return ($query->num_rows() > 0);           
    }
}