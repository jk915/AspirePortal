<?php
class Articles_related_model extends CI_Model 
{
	function Articles_related_model()
	{
		// Call the Model constructor
		parent::__construct();
	}
    
	/***
	* @method get_list
	* @author Andrew Chapman
	* @abstract This method gets a list of all articles that are related a specified article.  
	* 
	* @param integer $article_id - The article to find related articles for.
	* 
	* @returns A list of articles
	*/
	public function get_list($article_id)
	{
		$this->db->select('*');
		$this->db->from('articles_related');
		$this->db->join("articles a", "articles_related.related_article_id = a.article_id AND a.enabled = 1"); 
		$this->db->where("articles_related.article_id", $article_id);

		$query = $this->db->get();        

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query;
		}         
		else
			return false;
	}
    
	public function get_details($articles_related_id)
	{
		// Check to see if a record with this username exists.
		$query = $this->db->get_where('articles_related', array('articles_related_id' => $articles_related_id));

		// If there is a resulting row, check that the password matches.
		if ($query->num_rows() > 0)
		{
			return $query->row();
		}         
		else
			return false;
	}
    
    
	function save($articles_related_id, $data)
	{
		if (is_numeric($articles_related_id))
		{
			$this->db->where('articles_related_id', $articles_related_id);
			$this->db->update('articles_related',$data);
			return $articles_related_id;
		}
		else
		{
			$this->db->insert('articles_related', $data);
			return $this->db->insert_id();
		}
	}
    
	public function delete($where_in)
	{
		$this->db->where(" articles_related_id in (".$where_in.")",null,false);
		$this->db->delete('articles_related');
	}

    function exists($article_id, $related_article_id)
    {
        $this->db->where('article_id', $article_id);
        $this->db->where('related_article_id', $related_article_id);
                
        $query = $this->db->get('articles_related',1);       
        
        return ($query->num_rows() > 0);           
    }	
}
?>