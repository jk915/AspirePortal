<?php
die("OFFLINE");
/***
* Resource_manager Controller
* @copyright: SIMB Pty Ltd, 2009
*/
class Resource_manager extends CI_Controller 
{
   // Define variables
   public $data;    // Array to hold data to pass to pages.  
   
   /***
   * @desc The Resource_manager constructor checks that the user has logged in
   * and loads any needed views, libraries, etc.
   */
   function Resource_manager()
   {
      parent::__construct();

      // Create the data array.
      $this->data = array();
      
      // Make sure the user is logged in.
      // All article pages should now be shown if the user is not logged in.
      if(!$this->utilities->is_logged_in($this->session))
      {
         redirect("/");
         exit();
      }
   } 
   
   function get_file($file_path_encoded)
   {
      $this->load->model("file_types_model");
      
      $file_path = base64_decode($file_path_encoded);
      $file_abs_path = ABSOLUTE_PATH . "files/" . $file_path;
      
      if(!file_exists($file_abs_path))
      {
         // The file could not be found.  Report and log the error.
         $this->tools_model->report_error("Sorry, the file could not be retrieved.", "Resource_manager/get_file - the file with a path of '$file_path' could not be loaded");
         return;                 
      }
      
      // Get the file name without the directory name in it.
      // e.g.  mypic.jpg instead of pictures/mypic.jpg
      $slash_pos = strrpos($file_path, "/");
      if($slash_pos <= 0)
         show_error("Bad file name");    
         
      $file_name = substr($file_path, $slash_pos + 1);

      // Get the file extension, e.g. "pdf"
      $dot_pos = strrpos($file_path, ".");
      if($dot_pos <= 0)
         show_error("Unhandled file type");
         
      $extension = strtolower(substr($file_path, $dot_pos + 1));
      
      // Load the mime type for this file type
      $file_type = $this->file_types_model->get_details("." . $extension);
      
      // If we don't know about this file type, show an error
      if(!$file_type)
         show_error("Unhandled file type: $extension");   
         
      // Output the mime type header
      header('Content-type: ' . $file_type->mime_type);

      // It will be called downloaded.pdf
      header('Content-Disposition: attachment; filename="' . $file_name . '"');

      // Stream the file down
      readfile($file_abs_path);         
   }
}

/* End of file resource_manager.php */
/* Location: ./system/application/controllers/resource_manager.php */
?>
