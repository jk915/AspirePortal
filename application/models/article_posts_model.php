<?php
class Article_posts_model extends CI_Model 
{
	function Article_posts_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	/***
	* @method get_list
	* @author Andrew Chapman
	* @abstract This method gets a list of all posts from the database. 
	* 
	* @param integer $article_id - Limits the blog posts to a particular article
	* @param integer $limit - Limits the recordset to a specific number of records
	* @param integer $page_no - Starts the recordset at a specific page no.
	* @param string $order_by - Determines how to order the posts
	* 
	* @returns A list of article posts
	*/
	public function get_list($article_id = "", $limit = "", $page_no = "", $notify_only = false, $order_by = "created_dtm ASC", &$count_all = 0)
	{
		if($article_id != "")
		{
			$this->db->where("article_id", $article_id);
			$count_all = $this->db->count_all_results('article_posts');
		}
		else
			$count_all = $this->db->count_all_results('article_posts');

		$this->db->select('*');
		$this->db->from('article_posts');
		
		// If a parent id has been defined, only load categories with a matching parent id.
		if($article_id != "")
			$this->db->where("article_id", $article_id);
			
		if($notify_only)
			$this->db->where("notify", 1);
			
		$this->db->where("deleted", 0);
			  
		
		$this->db->order_by($order_by);

		if ($limit != "" && $page_no!= "" && $count_all > $limit)
		{
			$this->db->limit(intval($limit), intval(($page_no-1) * $limit));
		}

		$query = $this->db->get();        

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query;
		}         
		else
			return false;
	}
    
	public function get_details($post_id, $by_hash = false)
	{
		// Check to see if a record with this post_id exists.
		if($by_hash)
			$query = $this->db->get_where('article_posts', array('hash' => $post_id));
		else
			$query = $this->db->get_where('article_posts', array('post_id' => $post_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
	
	public function already_exists($article_id, $name, $comment)
	{
		$hash = md5($article_id . $name . $comment);
		
		// Check to see if a record with this username exists.
		$query = $this->db->get_where('article_posts', array('hash' => $hash));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return true;
		}         
		else
			return false;
	}	
    
    
	public function save($post_id ,$data)
	{
		if (is_numeric($post_id))
		{
			$this->db->where('post_id', $post_id);
			$this->db->update('article_posts', $data);
			return $post_id;
		}
		else
		{
			$this->db->insert('article_posts', $data);
			$post_id = $this->db->insert_id();
			
			$this->update_post_stats($post_id);
			
			return $post_id;
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" block_id in (".$where_in.")",null,false);
		$this->db->delete('article_posts');
	}
	
	public function update_post_stats($post_id)
	{
		$post = $this->get_details($post_id);
		if(!$post)
			return false;

		// Count how many posts there have been for this article
		$this->db->from('article_posts');
		$this->db->where('article_id', $post->article_id);
		$this->db->where('deleted', 0);
		$num_posts = $this->db->count_all_results();
		
		// Update the category with the number of posts
		$data = array("num_comments" => $num_posts);		
		$this->db->where('article_id', $post->article_id);
		$this->db->update('articles', $data); 		
	} 
}
