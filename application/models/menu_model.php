<?php
class Menu_model extends CI_Model 
{
	function Menu_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	public function get_menus($menu_id = "")
	{
        $this->db->select("m.*, CONCAT(m.name,' (',w.website_name, ')' ) as menu_website", false);
        $this->db->from('nc_menus m');
        $this->db->join('nc_websites w','m.website_id = w.website_id');
        
        if($menu_id != "")
            $this->db->where("menu_id", $menu_id);
            
		$query = $this->db->get();        

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query;
		}         
		else
			return false;
	}
    
	public function add_menu($name, $website_id)
	{
		$data = array(
            'name' => $name,
            'website_id' => $website_id
        );
        
		$this->db->insert("nc_menus",$data);
        return $this->db->insert_id();
	}
    
	public function remove_menu($menu_id)
	{
		//remove all menu items
		$this->db->delete("nc_menu_items",array("menu_id" => $menu_id));
		
		//remove menu
		$this->db->delete("nc_menus",array("menu_id" => $menu_id));
	}
    
	/*
	if the menu_name exists than return true
	else false
	*/
	public function menu_exists($menu, $byname, $website_id = "")
	{
		$where = ($byname) ? array("name" => $menu, "website_id" => $website_id) : array("menu_id" => $menu, "website_id" => $website_id);        
		$query = $this->db->get_where("nc_menus",$where,1);

		return ($query->num_rows() > 0);               
	}
    
	public function get_list($menu_id = "", $parent_id = "", $show_enabled_only = TRUE)
	{
		$sql = "SELECT m.*,(SELECT COUNT(menu_item_id) FROM nc_menu_items WHERE parent_id = m.menu_item_id) AS submenus,
			CASE WHEN parent_id = -1 THEN menu_item_id ELSE parent_id END AS `position`
			FROM nc_menu_items m
			WHERE ";

		$sql .= " enabled = ".($show_enabled_only) ? "1 " : "0 ";

		if($parent_id != "") $sql .=" AND parent_id = ".$parent_id;
		if($menu_id != "") $sql .=" AND menu_id = ".$menu_id;

		$sql .=" ORDER BY `menu_order`, parent_id";
		$query = $this->db->query($sql,array($menu_id));    

		return $query;
	}
    
    function clone_menu_items($clone_menu_id, $menu_id)
    {
        $sql = "INSERT INTO nc_menu_items(menu_item_name, menu_id, link, parent_id, class,enabled, menu_order, category_id)
                SELECT menu_item_name, $menu_id AS menu_id, link, parent_id, class,enabled,menu_order, category_id 
                FROM nc_menu_items
                WHERE menu_id = $clone_menu_id";
                
        $query = $this->db->query($sql);    

        return $query;        
    }
    
	/**
	* @desc The get_details method loads all properties of a particular menu item as defined by page_code
	*/
	public function get_details($menu_name = "", $menu_item_id = "")
	{
        if($menu_name == "")  //for admin page
        {
            $query = $this->db->get_where('nc_menu_items', array('menu_item_id' => $menu_item_id),1);

            // If there is a resulting row
            if ($query->num_rows() > 0)
            {
                return $query->row();
            }         
            else
                return false;
        }
        else
        {   //for front page
            $website_id = $this->utilities->get_session_website_id(TRUE);
                    
            $this->db->select('mi.*, mi.link as oldlink, ifnull(concat("page/", p.page_code), concat("article/", a.article_code)) as link', false);                        
            $this->db->from('nc_menu_items mi');
            $this->db->join("nc_menus m"," m.menu_id = mi.menu_id");        
            
            $this->db->where("m.name", $menu_name);
            $this->db->where("m.website_id", $website_id);                                   
                    
            $this->db->join("nc_pages p"," p.page_id = mi.link_to AND mi.link_type = 'pages' ", 'left');        
            $this->db->join("nc_articles a"," a.article_id = mi.link_to AND mi.link_type = 'article' ", 'left');        
            
		    $query = $this->db->get();

		    // If there is a resulting row
		    if ($query->num_rows() > 0)
		    {
			    return $query;
		    }         
		    else
			    return false;
        }
	}
    
	public function delete_menu_items($where_in)
	{
		$this->db->where(" menu_item_id in (".$where_in.")",null,false);
		$this->db->delete('nc_menu_items');
	}
    
	public function save($menu_item_id,$data)
	{
		if (is_numeric($menu_item_id) && $menu_item_id>0)
		{
			$this->db->where('menu_item_id',$menu_item_id);
			return $this->db->update('nc_menu_items',$data);
		}
		else
		{
			$this->db->insert('menu_items',$data);
			return $this->db->insert_id();
		}
	}
         
    
	/**
	* @method: get_flat_menu
	* @author: Andrew Chapman
	* @desc The get_flat_menu method method loads all menu items for a particular menu 
   * so long as they have the passed parent id (defaults to 0).
	* 
	* @param mixed $menu_id  - The id of the menu to load the items for.
	* @param mixed $parent_id - The parent_id of the menu
	*/
	public function get_flat_menu($menu_id, $parent_id = -1)
	{
		// Check to see if a record with this username exists.
        $this->db->order_by("menu_order", "ASC");
		$query = $this->db->get_where('menu_items', array('menu_id' => $menu_id, "parent_id" => $parent_id));
		
		//print $this->db->last_query() . "<br>";
		
		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
		   return $query;
		}    	 
		else
			return false;
	}
	
	/**
	* @method: get_menu_html
	* @author: Andrew Chapman
	* @created: 12th August, 2009
	* 
	* @desc: The get_menu_html method creates the necessary html
	* to render a two level drop down menu.  Note, the top level ul
	* tag is not included, so put that directly in your view before your call
	* this method.
	* 
	* @param mixed $menu_id - The id of the menu to load.
	*/
	public function get_menu_html($menu_id, $exclude_ids = false, $show_title = false, $add_title_tag = false, $parent_id = -1)
	{
		// Intiatialise vars
		$menuHTML = "";
		$menuItemsArray = array();
		$base_url = base_url();

		// Get the number of URL segments
		$num_segments = $this->uri->total_segments();

		// Get the value in the last segment
		$last_segment = $this->uri->segment($num_segments); 		

		// Load all top level menu items for the passed menu
		$items = $this->get_flat_menu($menu_id, $parent_id);        
		if(!$items)
			return false;
			
		// Loop through all menu items
		foreach($items->result() as $row)
		{
			// Load in menu item properties
			$menuItemID = $row->menu_item_id; 
			$menu_url = $row->link;
			$css_id = $row->class; 
            $category_id = $row->category_id;

			$title = $row->menu_item_name;
			$title = str_replace("&", "&amp;", $title); 	// If the user has entered any ambersands in the title, make them valid XHTML
            
         // Load any child pages
         $select_parent = false;
         $children = $this->get_flat_menu($menu_id, $menuItemID);
         
         if(($children) && ($children->num_rows() > 0))
         {
				foreach($children->result() as $row2)
				{
					if(stristr($row2->link, $last_segment))
						$select_parent = true;
				}                
         }
         
         //Load any child from articles if we have a category_id 
         $articles_children = $this->get_articles_menu($category_id);
         
         if($menu_url == "/")
            $menu_url = "";
			
			if(($menu_url != "#") && (!stristr($menu_url,$base_url)))
				$menu_url = $base_url . $menu_url;
			                                                        
			$selected_page = (stristr($row->link, $last_segment) && ($last_segment != "search"));
			
			$class = "";
			
			if(!$exclude_ids)
			{
				$class = "$css_id";			
			}			
            
			$class .= ($selected_page || ($num_segments == 0 && $row->link == "/") || ($select_parent) ) ? " active" : "";

			$menuHTML .= "\n\t\t\t<li><a ";
			
			if($class != "") $menuHTML .= "class=\"$class\" ";
            
            if($add_title_tag)    
                $menuHTML .= "title=\"$title\" ";

			if($show_title)
                $menuHTML .= "href=\"" . $menu_url . "\">" . $title . "</a>";			
            else    
			    $menuHTML .= "href=\"" . $menu_url . "\"></a>";						

		     
            if(($children) && ($children->num_rows() > 0) || ($articles_children && ($articles_children->num_rows() > 0)))
            {
                // This menu item has sub pages
                $menuHTML .= "\n\t\t\t\t<ul>";
                $menuHTML .= $this->_get_childrens_html($children);
                $menuHTML .= $this->_get_childrens_html($articles_children);
                $menuHTML .= "\n\t\t\t\t</ul>\n";
            }
            	
			
			$menuHTML .= "</li>";
		}        
        
		return $menuHTML;				
	}	  
    
    function _get_childrens_html($children)
    {
        $menuHTML = "";
        if($children)                 
        {
            foreach($children->result() as $row2)
            {
                //$arr_exp_link = explode("/",$row2->link);
                //$last_name = array_pop($arr_exp_link);
                
                //$url = implode("/",$arr_exp_link)."/".$this->tools_model->alphanumeric_link($last_name);
                $url = $row2->link;
                
                if(!stristr($url,base_url()))
                {
                    $url = base_url() . $url;                    
                }
                
                if ( !empty($row2->class) ) {
                	$class = 'class="'.$row2->class.'"';
                } else {
                    $class = '';
                }
                
                $menuHTML .= "\n\t\t\t\t\t<li><a href=\"" . $url . "\" $class>" . $row2->menu_item_name . "</a></li>";     
            }
        }
        return $menuHTML;
    }
    
    /**
    * @method: get_menu_item
    * @desc The get_menu_item method loads menu items if the condition is true
    * 
    * @param mixed $where  - The condition    
    */
    function get_menu_item($where)  
    {
        if($where != "")
            $this->db->where($where);    
        
        $query = $this->db->get("nc_menu_items");    
        
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    /**
    * @method: get_articles_menu
    * @desc The get_articles_menu method loads articles from a selected category id in a "menu" format
    * 
    * @param category_id - The article category id
    */
    function get_articles_menu($category_id = -1)
    {
        $this->db->select("article_code as link, article_title as menu_item_name",false);
        $this->db->where("category_id", $category_id);
        $this->db->order_by("article_order ASC");
        
        $query = $this->db->get("nc_articles");    
        
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    public function get_menu_html_extended($menu_id, $exclude_ids = false, $show_title = false, $add_title_tag = false, $parent_id = -1, $page_code = '')
	{
		// Intiatialise vars
		$menuHTML = "";
		$menuItemsArray = array();
		$base_url = base_url();

		// Get the number of URL segments
		$num_segments = $this->uri->total_segments();

		// Get the value in the last segment
		$last_segment = $this->uri->segment($num_segments); 		

		// Load all top level menu items for the passed menu
		$items = $this->get_flat_menu($menu_id, $parent_id);        
		if(!$items)
			return false;
			
			
		// Loop through all menu items
		foreach($items->result() as $row)
		{
			// Load in menu item properties
			$menuItemID = $row->menu_item_id; 
			$menu_url = $row->link;
			$css_id = 'nav_';

			$nr = strrpos( $menu_url, '/' ) + 1;
			$link_last_segment = substr( $menu_url, $nr );
            
			$title = $row->menu_item_name;
			$title = str_replace("&", "&amp;", $title); 	// If the user has entered any ambersands in the title, make them valid XHTML
            
	         // Load any child pages
	         $select_parent = false;
	         $children = $this->get_flat_menu($menu_id, $menuItemID);
	         
	         if(($children) && ($children->num_rows() > 0))
	         {
					foreach($children->result() as $row2)
					{
						if(stristr($row2->link, $last_segment))
							$select_parent = true;
					}                
	         }
         
	         if( $link_last_segment != "home" )
	         {
	         
			        if($menu_url == "/")
			            $menu_url = "";
					
					if(($menu_url != "#") && (!stristr($menu_url,$base_url)))
						$menu_url = $base_url . $menu_url;
					                                                        
					$selected_page = (stristr($row->link, $last_segment) && ($last_segment != "search"));
		            
					$class = ($selected_page || ($num_segments == 0 && $row->link == "/") || ($select_parent) ) ? "active" : "";
					
					if( $page_code != '' && $page_code == 'blog' && $page_code == $link_last_segment )
						$class = 'active';
					
					if($link_last_segment != "account")
						$menuHTML .= "\n\t\t\t<li><a class=\"$class\" ";
					else
						$menuHTML .= "\n\t\t\t<li id=\"navRight\"><a class=\"$class\" ";
					
					$menuHTML .= "id=\"".$css_id.$link_last_segment."\" ";
						
					//if(!$exclude_ids)
						
		            
		            if($add_title_tag)    
		                $menuHTML .= "title=\"$title\" ";
		
					if($show_title)
		                $menuHTML .= "href=\"" . $menu_url . "\">" . $title . "</a>";			
		            else    
					    $menuHTML .= "href=\"" . $menu_url . "\"></a>";						
	
			     
		            if(($children) && ($children->num_rows() > 0) )
		            {
		                // This menu item has sub pages
		                $menuHTML .= "\n\t\t\t\t<ul>";
		                $menuHTML .= $this->_get_childrens_html($children);
		                $menuHTML .= "\n\t\t\t\t</ul>\n";
		            }
		            	
					
					$menuHTML .= "\t\t\t</li>";
	        }
		}        
        
		return $menuHTML;				
	}
	
	public function buildMainMenuHTML($menu_id = 1)
	{
		$items = $this->get_flat_menu($menu_id);
		if(!$items)
		{
			return "";
		}
		
		$html = '<ul id="nav">';
		
		$bu = base_url();
		
		foreach($items->result() as $item)
		{
			if($item->link_type == "external")
			{
				$url = $item->link;	
				if(!stristr($url, "http"))
				{
					$url = "http://" . $url;
				}
			}
			else
			{
				$url = $bu . $item->link;
			}
			
			$html .= '<li><a id="' . $item->class . '" href="' . $url . '">' . $item->menu_item_name . '</a>';
			
 			$menu_id = $item->menu_id;
 			$parent_id = $item->menu_item_id;
 			// Load parent menu item
			$menu_parents = $this->get_list($menu_id,$parent_id);
			if($menu_parents->num_rows() > 0)
			{
				$html .= '<ul>';
				foreach($menu_parents->result() as $menu_parent)
				{
					$url = $bu . $menu_parent->link;
					
					$html .= '<li><a href="' . $url . '">' . $menu_parent->menu_item_name . '</a></li>';
				}
				$html .= '</ul>';
			}
			
			$html .= '</li>';
		}
		
		$html .= '</ul>';
		
		return $html;
	}
	
	public function buildFooterMenuHTML($menu_id = 1)
	{
		$items = $this->get_flat_menu($menu_id);
		
		if(!$items)
		{
			return "";
		}
		
		$html = '<ul class="nav left">';
		$bu = base_url();
		$x = 0;
		
		foreach($items->result() as $item)
		{
//			if($x > 0)
//			{
//				$html .= ' <span class="divider">|</span>';
//			}
//			

			if($item->link_type == "external")
			{
				$url = $item->link;	
				if(!stristr($url, "http"))
				{
					$url = "http://" . $url;
				}
			}
			else
			{
				$url = $bu . $item->link;
			}			
			
			$html .= '<li><a href="' . $url . '"';
			
			if($item->class != "")
			{
				$html .= ' class="' . $item->class . '" ';
			}
			
			$html .= '>' . htmlspecialchars($item->menu_item_name) . '</a></li>';
			$x++;
		}
		$html .= '</ul>';
		return $html;
	}	
    

    public function get_menu_list($menu_name)
    {
        $query = $this->db->get_where("nc_menus", array("name" => $menu_name), 1);
        if ($query->num_rows() > 0){
            $menu = $query->row(0);
            return $this->get_flat_menu($menu->menu_id);
        }
        return '';
    }    
    
}
