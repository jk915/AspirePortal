<?php
class Article_model extends CI_Model 
{
    private $CI;
    
	function Article_model()
	{
		// Call the Model constructor
		parent::__construct();      
        $this->CI = & get_instance();
        $this->CI->load->model("resources_model"); 
	}
                    
	public function get_list($category_id = "", $show_enabled_only = TRUE, $isRSS = false, $order_by = "article_order", $order_direction = "ASC", $items_per_page = 0, $offset = 0, $where = "", &$count_all = 0 )
	{
        $this->_get_list($category_id, $show_enabled_only, $isRSS, $order_by, $order_direction, $items_per_page, $offset, $where, $count_all, true );
        $count_all = $this->db->count_all_results();
        
        $this->_get_list($category_id, $show_enabled_only, $isRSS, $order_by, $order_direction, $items_per_page, $offset, $where, $count_all, false );
		$query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
        
	}
    
    function _get_list($category_id = "", $show_enabled_only = TRUE, $isRSS = false, $order_by = "article_order", $order_direction = "ASC", $items_per_page = 0, $offset = 0, $where = "", $count_all, $only_count = false )
    {
        if($only_count)
        {
        	$this->db->select("article_id");
	        $this->db->from('articles');  
	            
	        if($show_enabled_only)
	            $this->db->where('enabled','1');

	        if($category_id !="")    
	            $this->db->where('category_id',$category_id);    			
        }
        else
        {
	        if($isRSS == false)
	            $this->db->select('*, date_format(created_dtm,"%d/%m/%Y") as created, date_format(last_modification_dtm,"%d/%m/%Y") as last_modification, date_format(article_date,"%d/%m/%Y") as article_date_formatted',false); 
	        else
	            $this->db->select('article_id, article_code, article_title, date_format(created_dtm,"%d/%m/%Y - %H:%i") as created, short_description, created_dtm as pubDate, date_format(article_date,"%d/%m/%Y - %H:%i") as article_date, hero_image',false);      

	        $this->db->from('articles');  
	            
	        if($show_enabled_only)
	            $this->db->where('enabled','1');

	        if($category_id !="")    
	            $this->db->where('category_id',$category_id);    
	         
	        if($where != "")    
	            $this->db->where($where);

            if($order_by != "")
	            $this->db->order_by($order_by, $order_direction);  
            else
                $this->db->order_by("article_order ASC");

	        if($items_per_page > 0)
	        {
	            $this->db->limit($items_per_page);
	            $this->db->offset($offset);              
	        }			
        } 
    }
        
    
   /**
   * @desc The get_details method loads all properties of a particular article as defined by article_id
   */
    public function get_details($article_id, $by_name = FALSE, $join = false)
    {
    	if($by_name)
    	{
    		if( is_numeric( $article_id ) )
            {
                $this->db->select('art.*, acat.name as category_name, acat.category_code ');
                $this->db->from('articles as art');
                $this->db->join('article_categories as acat', 'art.category_id = acat.category_id');
                $this->db->where(array('art.article_id' => $article_id));
                $query = $this->db->get();                
            }
        	else
        	{
        		$this->db->select('art.*, acat.name as category_name, acat.category_code ');
        		$this->db->from('articles as art');
        	    if ( $join ) {
        	        $this->db->select("dir.article_code as director_code");
        	        $this->db->select("dir.article_title as director_name");
        	    	$this->db->join('articles as dir', "dir.article_id = art.director", 'left');
        	    	
        	        $this->db->select("epro.article_code as executive_producer_code");
        	        $this->db->select("epro.article_title as executive_producer_name");
        	    	$this->db->join('articles as epro', "epro.article_id = art.exe_producer", 'left');
        	    	
        	        $this->db->select("pro.article_code as producer_code");
        	        $this->db->select("pro.article_title as producer_name");
        	    	$this->db->join('articles as pro', "pro.article_id = art.producer", 'left');
        	    	
        	        $this->db->select("writer.article_code as writer_code");
        	        $this->db->select("writer.article_title as writer_name");
        	    	$this->db->join('articles as writer', "writer.article_id = art.writer", 'left');
        	    }
        		$this->db->join('article_categories as acat', 'art.category_id = acat.category_id');
        		$this->db->where(array('art.article_code' => $article_id));
        		$query = $this->db->get();
        	}
    	}
        else
        {
        	// We're getting the article by article_code
        	
        	// Get the current website id
	        //$this->utilities->get_session_website_id(TRUE);
	        $website_id = '1';//$this->session->userdata("website_id"); 
	        //$website_code = $this->session->userdata("website_code");
	        
	        // Find an article with this article_code that has permission to be viewed on this website.
            $this->db->select("articles.*");
            $this->db->from("articles");
            //$this->db->join("website_assm wa", "articles.article_id = wa.foreign_id AND wa.website_id = $website_id AND wa.type = 'article'");
            if( is_numeric( $article_id ) )
            	$this->db->where( 'articles.article_id', $article_id );
            else
	        	$this->db->where("articles.article_code", $article_id);
	        //$this->db->where("articles.enabled", 1);
	        $query = $this->db->get();
        }

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    function get_articles($selected_value, $category_id)
    {
        switch($selected_value)
        {
            case -2: //Most Recent Post
                $this->db->order_by("article_date", "desc");                
            break;
            
            case -1:  //random
                $this->db->order_by("article_id", "random");                
            break;            
        }
        
        $query = $this->db->get_where('articles', array('category_id' => $category_id,'hero_image !=' => ""), 1);
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    public function get_category_details($category)
    {
        $where = (is_numeric($category)) ? array("category_id" => $category) : array("name" => $category);
        $query = $this->db->get_where("nc_article_categories", $where ,1);
        
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    public function get_category_by_parent($category_parent)
    {
        if(is_numeric($category_parent))//id
        {
            $parent_id = $category_parent;
        }
        else//name
        {
            $parent_detail = $this->get_category_details($category_parent);
            if($parent_detail)
                $parent_id = $parent_detail->category_id;
            else
                return false;
        }
    
        $query = $this->db->get_where("nc_article_categories", array("parent_id" => $parent_id));
        
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    public function delete_articles($where_in)
    {
        $this->db->where(" article_id in (".$where_in.")",null,false);
        $this->db->delete('nc_articles');
    }
     
    public function save($article_id, $data)
    {
        if (is_numeric($article_id) && $article_id > 0)
        {
            $data["last_modification_dtm"] = date("Y-m-d %H:%i:%s");         
            $this->db->where('article_id',$article_id);
            $this->db->update('nc_articles',$data);
            return $article_id;
        }
        else
        {
            $data["created_dtm"] = date("Y-m-d %H:%i:%s"); 
            $this->db->insert('nc_articles',$data);
            return $this->db->insert_id();
        }
    }
    
	public function get_article_categories($enabled = -1, $website_id = "", $parent_id = "", $order_by = "name ASC")
	{
        $website_id = (is_numeric($website_id) ? $website_id : $this->utilities->get_session_website_id());
		
        if(is_numeric($website_id))
        {
		    $this->db->order_by($order_by);
		    
		    // We must always have a website id defined.
		    //$this->db->where("website_id", $website_id);
		    
            if($enabled != -1)
                $this->db->where("enabled", $enabled);
            
            if($parent_id != "")    
                $this->db->where("parent_id", $parent_id);
    
		    $query = $this->db->get('article_categories');        
            
		    // If there is a resulting row, check that the password matches.
		    if ($query->num_rows() > 0)
		    {
			    return $query;
		    }         
		    else
			    return false;
        }
        else
            return false;
	} 
    
    /***
    * Adds a new category to the database
    * 
    * @param string $name	The name of the category to add
    * @param integer $website_id The website the category is being added to
    * @param integer $parent_id	The parent category id (if applicable)
    */
	public function add_category($name, $category_code, $website_id, $parent_id = -1)
	{
		$this->db->insert("article_categories", array('name' => $name, 'category_code' => $category_code, 'website_id' => '1', 'parent_id' => $parent_id));
		return $this->db->insert_id();
	}
	
    /***
    * Adds a new category to the database and clone its parent data
    * 
    * @param string $name	The name of the category to add
    * @param integer $website_id The website the category is being added to
    * @param integer $parent_id	The parent category id (if applicable)
    */
	public function add_category_clone_parent_data($name, $category_code, $website_id, $parent_id = -1, $parent=false)
	{
	    $data = array(
	       'name' => $name, 
	       'category_code' => $category_code, 
	       'website_id' => '1', 
	       'parent_id' => $parent_id
        );
	    if ( $parent != false AND $parent_id != -1 ) {
	        $parentData = (array) $parent;
	        $aUnset = array(
	           'category_id',
	           'name',
	           'category_code',
	           'website_id',
	           'parent_id',
	           'enabled',
	           'meta_description',
	           'meta_keywords',
	           'meta_title',
	           'meta_robots',
	           'short_description',
	           'long_description',
	           'hero_image',
	           'hero_image_alt',
            );
            foreach ( $aUnset as $unset )
            {
    	        unset($parentData[$unset]);
            }
            $data = array_merge($data, $parentData);
	    }
		$this->db->insert("article_categories", $data);
		return $this->db->insert_id();
	}
	
	public function update_category( $product_id, $data )
	{
		$this->db->where('product_id', $product_id);
		$this->db->update('article_categories',$data);
	}
	
	public function delete_category_products($arr_products)
    {
        $this->db->where_in("product_id",$arr_products);
        $this->db->delete("article_categories");
    }
    
    public function remove_category($arr_categories)
    {                                                      
        //remove all articles from selected category
        $this->db->where_in("category_id",$arr_categories);
        $this->db->delete("nc_articles");
        
        //remove category
        $this->db->where_in("category_id",$arr_categories);
        $this->db->delete("nc_article_categories");
        
        //remove subcategories
    }
    
    /*
     if the category name exists than return true
     else false     
    */
    public function category_exists($category, $website_id, $byname, $return_row = false)
    {
        $where = ($byname) ? array("name" => $category, "website_id" => '1') : array("category_id" => $category, "website_id" => '1');
        $query = $this->db->get_where("nc_article_categories", $where, 1);
        
        $result = ($query->num_rows() > 0);               
        if(!$return_row)
        	return $result;
        else
        	return $query->row();
    }
    
    /*
     if the category name exists than return true
     else false     
    */
    public function category_exists_bycode($category_code, $website_id, $parent_id = -1)
    {
        $where = array("category_code" => $category_code, "website_id" => '1');
        
        // See if we want to restrict our duplication test to a particular parent id.
        if($parent_id > 0)
        	$where["parent_id"] = $parent_id;
        
        $query = $this->db->get_where("nc_article_categories", $where, 1);

        if($query->num_rows() > 0)               
        	return $query->row();
        else
        	return false;
    }        
    
   public function get_next_article_order($category_id)
   {
		// Get the next available article_order number for article categories within the same parent id
   		$this->db->select_max('article_order');
		$this->db->where("category_id", $category_id);
		$query = $this->db->get('articles');	

		$row = $query->row();
		$next_article_order = $row->article_order;

		if(($next_article_order == null)	|| ($next_article_order == ""))
			return 1;
		else
			return ($next_article_order + 1);
   }
   
   /**
   * @method get_article_category_count
   * @author Andrew Chapman
   * @desc Returns the number of articles that have been published in a particular category.
   */
	public function get_article_category_count($category_id)
	{
		// Get a list of articles assigned to this catgory
		$this->db->select('*');
		$this->db->from('articles');
		$this->db->where('articles.category_id', $category_id); 
		$this->db->where('articles.enabled', 1); 
                                                                                     
		return $this->db->count_all_results();
	}
    
    /**
    * @method exists_article_code
    * @desc Returns true if the article_code allready exists else false
    */
    function exists_article_code($article_code,$article_id)
    {
        $this->db->where('article_code',$article_code);
        $this->db->where('article_id !=',$article_id);
                
        $query = $this->db->get('nc_articles',1);       
        
        return ($query->num_rows() > 0);           
    }      
    
    /**
    * @method search_articles
    * @desc Returns articles which title or content contains the term
    */     
    function search_articles($term)
    {
        $select = "";
        $where = "";
        $points_title = 5;
        $points_content = 1;
        
        //explode the term
        $arr_terms = explode(" ",$term);
        
        if(count($arr_terms) > 0)
        {
            foreach($arr_terms as $row) 
            {
                if($row != "")    
                {
                    //create select
                    if($select != "")
                        $select .= "+ ";
                    
                    $select .= " (LENGTH( article_title) - LENGTH(REPLACE( article_title, '".$row."', ''))) / LENGTH('".$row."') * ".$points_title;
                    $select .= " + (LENGTH( content) - LENGTH(REPLACE( content, '".$row."', ''))) / LENGTH('".$row."') * ".$points_content;
                    
                    //create where
                    if($where != "")
                        $where .= " OR ";
                    
                    $where .= "article_title LIKE '%".$row."%' OR content LIKE '%".$row."%'";
                }               
            }
            if($select != "") $select .= " AS `count` ";           
        }
        
        if($select != "") $select = "," . $select;
        
        $this->db->select("article_id, article_title, short_description, content " . $select, false);
        $this->db->from("nc_articles");
        
        if($select != "")
            $this->db->order_by("`count`","desc");  
        if($where != "")    
            $this->db->where($where);
            
        $query = $this->db->get();    
        
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;      
    }
    
	//generate bradcrumbs for a given category_id
	public function generete_breadcrumbs($category_id, $level = 1)
	{
		$breadcrumb = "";

		if ($category_id == -1)
		{
			return '<li><a href="'.base_url().'admin/contentmanager">Home</a></li>';
		}
		else
		{
			$category_detail =  $this->get_category_details($category_id);

			if ($category_detail)
			{           
				if($level == 1)
				{
					$breadcrumb = '<li>'.$category_detail->name.'</li>'.$breadcrumb;
				}
				else 
				{
					$breadcrumb = '<li><a href="'.base_url().'admin/contentmanager/category/'.$category_detail->category_id.'">'.$category_detail->name.'</a></li>'.$breadcrumb;
				}
			}
			else
			{
				$this->error_model->report_error("Sorry.", "Error generating breadcrumbs (recursion)");
			}

			$parent_name = $this->generete_breadcrumbs($category_detail->parent_id, ++$level);
			$breadcrumb =  $parent_name.$breadcrumb;
		}        

		return $breadcrumb;
	}
    
    /***
    * The calc_post_stats method calculates total post statistics for each unique year/month
    * pair.  It can calculate all stats across all article categories, or stats for a specific
    * category_id if the category_id is passed.
    * 
    * @param integer $category_id Optional - A specific category ID to generate the stats for if necessary.
    */
    public function calc_post_stats($category_id = "")
    {
		// Empty out the stats table.
		if($category_id != "")
		{
			$this->db->where('category_id', $category_id);
			$this->db->delete('article_monthly_posts'); 			
		}
		else
			$this->db->empty_table('article_monthly_posts');     	
    	
		$query = "SELECT DATE_FORMAT(created_dtm, '%Y%m') as ym, category_id, COUNT(article_id) as num_articles " .
				"FROM nc_articles " .
				"WHERE enabled = 1 " .
				"AND created_dtm > '2010-01-01' ";
				
		if($category_id != "")
		{
			$query .= "AND category_id = $category_id ";			
		}
				
		$query .= "GROUP BY category_id, ym";
				
		$query = $this->db->query($query);
		if($query->num_rows() == 0)
			return;
			
		foreach($query->result() as $row)
		{
			$year_month = $row->ym;
			$category_id = $row->category_id;
			$num_articles = $row->num_articles;
			
			$year = substr($year_month, 0, 4);
			$month = substr($year_month, 4, 2);
			
			$data = array();
			$data["category_id"] = $category_id;
			$data["month"] = $month;
			$data["year"] = $year;
			$data["num_posts"] = $num_articles;
			
			$this->db->insert("article_monthly_posts", $data);
		}		
    }
    
    public function get_category_stats($category_id, &$sum_previous_year = 0)
    {
    	$current_year = date("Y");
    	$last_year = $current_year - 1;

    	// Figure out total posts for this category last year
		$this->db->select_sum('num_posts');
		$this->db->where_in("category_id", $category_id);
		$this->db->where("year", $last_year);
		$query = $this->db->get('nc_article_monthly_posts');  
		
		$row = $query->row();
		$sum_previous_year = $row->num_posts;  
		if($sum_previous_year == null)
			$sum_previous_year = 0;	
    	
        $this->db->select("*");
        $this->db->from("nc_article_monthly_posts as ap");
        $this->db->join('nc_article_categories as ac', 'ac.category_id = ap.category_id');
        $this->db->where_in("ap.category_id", $category_id); 
        $this->db->where("year", $current_year);  
        $this->db->order_by("month","desc");  
        
        $query = $this->db->get();    
        
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;    		
    }
    
    public function get_ambassadors($category_id, $limit = "", $order_by = "")
    {
		$query = "SELECT * " .
				"FROM nc_articles " .
				"WHERE enabled = 1 " .
				"AND article_code IN " .
					"(SELECT category_code " .
					"FROM nc_article_categories " .
					"WHERE parent_id IN ". 
						"(SELECT category_id " .
						"FROM nc_article_categories " .
						"WHERE parent_id = $category_id " .
						"AND name = 'ambassadors'))";
                        
        if($order_by != "")    
            $query .= " ORDER BY " . $order_by;
            
        if($limit != "")                
            $query .= " LIMIT ". $limit;
        				
		$query = $this->db->query($query);
		
		if($query->num_rows() > 0)
			return $query;
		else
			return false;
    } 
    
    public function get_dvds($category_id, $limit = "", $order_by = "")
    {
		$query = "SELECT * " .
				"FROM nc_articles " .
				"WHERE enabled = 1 " .
				"AND article_code IN " .
					"(SELECT category_code " .
					"FROM nc_article_categories " .
					"WHERE parent_id IN ". 
						"(SELECT category_id " .
						"FROM nc_article_categories " .
						"WHERE parent_id = $category_id " .
						"AND name = 'DVD'))";
                        
        if($order_by != "")    
            $query .= " ORDER BY " . $order_by;
            
        if($limit != "")                
            $query .= " LIMIT ". $limit;
        				
		$query = $this->db->query($query);
		
		if($query->num_rows() > 0)
			return $query;
		else
			return false;
    }

    public function get_articles_from_category( $category_id, $limit = '', $excludes = array(), $where='', $orderby='' )
    {
    	$this->db->select( 'art.*, acat.name as cat_name, acat.category_code as category_code' );
    	$this->db->from('articles as art');
    	$this->db->join('article_categories as acat', 'art.category_id = acat.category_id');
    	if ( !empty($where) ) {
    		$this->db->where($where);
    	}
	    $this->db->where('art.enabled','1');
        $this->db->where_in('art.category_id',$category_id);
        if ( !empty($orderby) ) {
    	    $this->db->order_by($orderby);
        } else {
    	    $this->db->order_by("art.article_date desc");
    	    $this->db->order_by("art.article_id desc");
        }
	    
	    if ( sizeof($excludes) ) {
	    	$this->db->where_not_in('art.article_id', $excludes);
	    }
	    
	    if( $limit != '' AND !empty($limit) )
	    	$this->db->limit($limit);
	    
	    $query = $this->db->get();
	    
	    if( $query->num_rows() > 0 )
	    {
	    	return $query;
	    }
	    else
	    {
	    	return false;
	    }
	    
    }
    
    public function get_articles_from_date( $category_id, $month, $year )
    {	
    	$this->db->select( 'art.*, acat.name as cat_name, acat.category_code as category_code' );
    	$this->db->from('articles as art');
    	$this->db->join('article_categories as acat', 'art.category_id = acat.category_id');
	    $this->db->where('art.enabled','1');
	    $this->db->where_in('art.category_id',$category_id);    
	    $this->db->orderby("art.article_date desc");
	    
	    $this->db->where( 'MONTH(art.article_date)', $month );
    	$this->db->where( 'YEAR(art.article_date)', $year );
    	
    	$query = $this->db->get();
    	
    	if($query->num_rows()>0)
    	{
    		return $query;
    	}
    	else
    	{
    		return false;
    	}
    }
    
    function get_subcategories( $category_id )
    {
    	$this->db->select('category_id');
    	$query  = $this->db->get_where('article_categories', array( 'parent_id' => $category_id ));
    	
    	if( $query->num_rows() > 0 )
    	{
    		return $query->result();
    	}
    	else
    	{
    		return false;
    	}
    }
  
    public function get_media($filters = array(), $order_by = "a.article_date DESC", $limit = "", $page_no = "", &$count_all, $select_sql = "")
    {
        // Find out total record count
        $this->db->select("COUNT(a.article_id) as num_items");
        $this->apply_media_filters($filters);
        $row = $this->db->get()->row();
        $count_all = $row->num_items;
        
        // Now load the actual recordset
        $this->db->select("a.*, ac.name as category_name");
        $this->apply_media_filters($filters);        
        $this->db->order_by($order_by);
        
        // Apply the limit if necessary
        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }        
        
        $query = $this->db->get(); 

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    private function apply_media_filters($filters)
    {
        $this->db->from("articles a");
        $this->db->join("article_categories ac", "a.category_id = ac.category_id");        
        $this->db->where("((a.category_id = " . CATEGORY_MEDIA . ") OR (ac.parent_id = " . CATEGORY_MEDIA . "))");
        
        // Search term
        if((array_key_exists("search_term", $filters)) && ($filters["search_term"] != ""))
        {
            $search_term = $filters["search_term"];
            
            $this->db->where("((a.article_title LIKE '%" . $search_term . "%') OR " .
                "(a.author LIKE '%" . $search_term . "%') OR " .
                "(a.tags LIKE '%" . $search_term . "%') OR " .
                "(a.comments LIKE '%" . $search_term . "%') OR " .
                "(a.source LIKE '%" . $search_term . "%'))");
        }
        
        // Apply permissions
        if((array_key_exists("user_type_id", $filters)) && (is_numeric($filters["user_type_id"])))
        {
            $user_type_id = $filters["user_type_id"]; 
            
            $where = "((get_article_totalusertype_count(a.article_id) = 0) OR (get_article_usertype_count(a.article_id, %d) > 0))";  
            $where = sprintf($where, $user_type_id);
            $this->db->where($where);
        }
        
        where($filters, "category_id", "a");
        
        // Applicable States
        if((array_key_exists("state_id", $filters)) && (is_numeric($filters["state_id"])))
        {
            $where = "a.article_id IN (SELECT article_id FROM " . $this->db->dbprefix . "article_states WHERE state_id = %d)";
            $where = sprintf($where, $filters["state_id"]);   
            $this->db->where($where);  
        }
        
        // Areas
        if((array_key_exists("areas", $filters)) && ($filters["areas"] != ""))
        {
            $areas = $filters["areas"];
            
            $where = "a.article_id IN (SELECT article_id FROM " . $this->db->dbprefix . "article_areas WHERE area_id IN ('%s'))";
            $where = sprintf($where, $areas);   
            $this->db->where($where);
        }        
    }
    
	public function get_announcements($filters = array(), $order_by = "a.article_date DESC", $limit = "", $page_no = "", &$count_all, $select_sql = "")
    {
        // Find out total record count
        $this->db->select("COUNT(a.article_id) as num_items");
        $this->apply_announcements_filters($filters);
        $row = $this->db->get()->row();
        $count_all = $row->num_items;
        
        // Now load the actual recordset
        $this->db->select("a.*, ac.name as category_name");
        $this->apply_announcements_filters($filters);        
        $this->db->order_by($order_by);
        
        // Apply the limit if necessary
        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }        
        
        $query = $this->db->get(); 

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    private function apply_announcements_filters($filters)
    {
        $this->db->from("articles a");
        $this->db->join("article_categories ac", "a.category_id = ac.category_id");       
        $this->db->where("a.enabled", 1); 
        $this->db->where("((a.category_id = " . CATEGORY_IMPORTANT_INFO . ") OR (ac.parent_id = " . CATEGORY_IMPORTANT_INFO . "))");
        
        // Search term
        if((array_key_exists("search_term", $filters)) && ($filters["search_term"] != ""))
        {
            $search_term = $filters["search_term"];
            
            $this->db->where("((a.article_title LIKE '%" . $search_term . "%') OR " .
                "(a.author LIKE '%" . $search_term . "%') OR " .
                "(a.tags LIKE '%" . $search_term . "%') OR " .
                "(a.comments LIKE '%" . $search_term . "%') OR " .
                "(a.source LIKE '%" . $search_term . "%'))");
        }
        
        // Apply permissions
        if((array_key_exists("user_type_id", $filters)) && (is_numeric($filters["user_type_id"])))
        {
            $user_type_id = $filters["user_type_id"]; 
            
            $where = "((get_article_totalusertype_count(a.article_id) = 0) OR (get_article_usertype_count(a.article_id, %d) > 0))";  
            $where = sprintf($where, $user_type_id);
            $this->db->where($where);
        }
        
        where($filters, "category_id", "a");
        
        // Applicable States
        if((array_key_exists("state_id", $filters)) && (is_numeric($filters["state_id"])))
        {
            $where = "a.article_id IN (SELECT article_id FROM " . $this->db->dbprefix . "article_states WHERE state_id = %d)";
            $where = sprintf($where, $filters["state_id"]);   
            $this->db->where($where);  
        }
        
        // Areas
        if((array_key_exists("areas", $filters)) && ($filters["areas"] != ""))
        {
            $areas = $filters["areas"];
            
            $where = "a.article_id IN (SELECT article_id FROM " . $this->db->dbprefix . "article_areas WHERE area_id IN ('%s'))";
            $where = sprintf($where, $areas);   
            $this->db->where($where);
        }        
    } 
	
    /***
    * Checks to see if a user of a particular user type can download a specific article
    * 
    * @param int $user_type_id
    * @param int $article_id
    * @returns true if the user may download the article.
    */
    public function user_type_has_article_permissions($user_type_id, $article_id)
    {
        $query = "SELECT get_article_totalusertype_count(%d) as total_count, get_article_usertype_count(%d, %d) as user_type_count";
        $query = sprintf($query, $article_id, $article_id, $user_type_id);
        
        $row = $this->db->query($query, true)->row();
        
        if($row->total_count == 0)
        {
            return true;    
        }
        else if($row->user_type_count > 0)
        {
            return true;    
        }   
        
        return false;     
    }  
    
    public function get_article_icon()
    {
        $status = array(
            "pdf_logo" => "PDF Logo",
            "youtube_logo" => "Youtube Logo",
            "www_logo" => "WWW Logo",
            "word_logo" => "Word",
            "powerpoint_logo" => "Powerpoint",
            "excel_logo" => "Excel",
            "aspire_logo" => "Aspire"
        );    

        return $status;
    }
}
