<?php if (!defined('BASEPATH')) exit('No direct script access allowed'); 

class Email_template_model extends CI_Model
{
	function Email_template_model()
	{
		// Call the Model constructor
        parent::__construct();
	}
	
	/**
	 * @method	get_details
	 * @access	public
	 * @desc	this method returns the email template details
	 * @author	
	 * @param 	mixed					$menu_id				- menu id or menu code
	 * 
	 * @version	1.0
	 * @return 	true/false or email template array
	 */
	public function get_details( $email_template_id = '', $is_id = true )
	{
		if( empty( $email_template_id ) || $email_template_id == '' )
			return FALSE;
		
		if( is_numeric( $email_template_id ) && $is_id )
            $this->db->where('id', $email_template_id);
		else
		    $this->db->where('email_template', $email_template_id);            	
        
        $this->db->limit(1);    
		
		$query = $this->db->get( 'email_template' );
        
        return ($query->num_rows() > 0) ? $query->row() : FALSE;        
	}
                   
    /**
    * @method get_list
    * @abstract This method gets a list of all blocks from the database.  
    * 
    * @param integer $limit - Limits the recordset to a specific number of records
    * @param integer $page_no - Starts the recordset at a specific page no.
    * @param integer $count_all - Counts all records.
    * 
    * @returns A list of email template
    */
    public function get_list($limit = "", $page_no = "", &$count_all, $where = "")
    {
        if($where != "") $this->db->where($where);            
        $count_all = $this->db->count_all_results('email_template');

        $this->db->select('*');
        $this->db->from('email_template');
        if($where != "") $this->db->where($where);
        
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
    
    /**
     * @method    save
     * @access    public
     * @desc    this method saves or updates an object
     * @author    
     * @param     mixed                $data                - the data to insert or to update
     * @param     int                    $foreign_id            - the id of the existing object
     * 
     * @version    1.0.1
     * @return     the article id or FALSE
     */
    public function save( $data = array(), $foreign_id = '' )
    {
        if( !empty( $foreign_id ) && is_numeric( $foreign_id ) )
        {
            $this->db->where( 'id', $foreign_id );
            
            if( $this->db->update( 'email_template', $data ) )
                return $foreign_id;
            else 
                return FALSE;
        }
        else
        {
            if( $this->db->insert( 'email_template', $data ) )
            {
                // get the insert id, because the log will have a new insert id
                $insert_id    = $this->db->insert_id();
                return $insert_id;
            }
            else
                return FALSE;
        }
    }
    
    /**
     * @method    delete
     * @access    public
     * @desc    this method delete objects by id or ids
     * @author    
     * @param     mixed                $ids                - the object id(s)
     * 
     * @version    1.0.1
     * @return     boolean
     */
    public function delete( $ids = array() )
    {
        if( !empty( $ids ) )
        {
            if( is_array( $ids ) )
                $this->db->where_in( 'id', $ids );                                
            else
                $this->db->where( 'id', $ids );                                
            
            return $this->db->delete( 'email_template' );
        }
        
        return FALSE;
    }	
}