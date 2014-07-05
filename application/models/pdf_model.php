<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
*/
class Pdf_model extends CI_Model 
{
    function Pdf_model()
    {
        // Call the Model constructor
          parent::__construct();
    }
    
	function get_metadata($doc_id, $where = "")
	{
		
        if ($where != "")
            $this->db->where($where);
        
        $this->db->where("doc_id",$doc_id);    
		$query = $this->db->get_where('doc_metadata');
		
		// If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }         
        else
            return false;
	}
    
    
    function get_details($doc_id)
    {
        $query = $this->db->get_where("documents",array("doc_id" => $doc_id),1);
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
    }
    
    function get_doc_images($doc_id)
    {
        $query = $this->db->get_where("doc_images",array("doc_id" => $doc_id));
        
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }         
        else
            return false;
    }
}
?>