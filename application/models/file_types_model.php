<?php
class File_types_model extends CI_Model 
{
   function File_types_model()
   {
     // Call the Model constructor
       parent::__construct();
   }
    

   /***
   * @method get_list
   * @author Andrew Chapman
   * @abstract This method gets a list of all file_types from the database.  
   *
   * @returns A list of file_types
   */
   public function get_list()
   {
      $this->db->select('*');
      $this->db->from('nc_file_types');
      $this->db->order_by("page_title", "ASC");     
                                                                          
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
   * @desc The get_details method loads all properties of a particular file_type as defined by extension
   */
   public function get_details($extension)
   {
      if($extension == "")
         return false;
         
      $query = $this->db->get_where('file_types', array('extension' => $extension));

      // If there is a resulting row, check that the password matches.
      if ($query->num_rows() > 0)
      {
         return $query->row();
      }         
      else
         return false;
    }
}