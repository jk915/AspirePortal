<?php
class Filemanager extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	private $records_per_page = ITEMS_PER_PAGE;
	
	function __construct()
	{
		parent::__construct();
		
		
		// Create the data array.
		$this->data = array();			
		
		// Load models etc		
		$this->load->library("utilities");		
	}
	
	function index()
	{
		// Define page variables
		$this->data["meta_keywords"] = "";
		$this->data["meta_description"] = "";
		$this->data["meta_title"] = "File Manager";
		$this->data["page_heading"] = "File Manager";
		$this->data["name"] = $this->login_model->getSessionData("firstname");
		$this->data["page_heading"] = "Files & Resources";
		$this->data['message'] = "";
		
		//get folders
		$this->data["folders"] = $this->utilities->get_files_with_type();
		$this->data["selected_folder"] = isset($this->data["folders"][0]) ? $this->data["folders"][0] : "";
        $this->data["files"] = $this->utilities->get_files_with_type($this->data["selected_folder"],$this->db);
		$this->data["count_all"] = count($this->data["files"]);
		$this->data["records_per_page"] = $this->records_per_page;
		
		// Load Views
		$this->load->view('admin/header', $this->data);
		$this->load->view('admin/filemanager/prebody', $this->data); 
		$this->load->view('admin/filemanager/main', $this->data);
		$this->load->view('admin/pre_footer', $this->data); 
		$this->load->view('admin/footer', $this->data); 
	}
	
	//handles all ajax requests within this page
	function ajaxwork()
	{
		
		$type = intval($this->tools_model->get_value("type",0,"post",0,false));
		$folder = $this->tools_model->get_value("folder",0,"post",0,false);
		
		
		switch($type)
		{
			case 1: //delete selected files
				//get files names separated with ";"
				$file_names = $this->tools_model->get_value("todelete","","post",0,false);
				
				if ($file_names!="")
				{
					$arr_files = explode(";",$file_names);
					
					$this->utilities->remove_file($folder,$arr_files);
					
					echo "ok";
				}
								
				break;
			//get list of files from selected folder, listing refresh
			case 2:
                $window = $this->tools_model->get_value("window","","post",0,false);
				//get files
				$files = $this->utilities->get_files_with_type($folder,$this->db);
				$count_all = count($files);
				
				//load view 
				$this->load->view('admin/filemanager/file_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page,'window' => $window, 'selected_folder' => $folder));
				break;
			
			case 3: //add new folder
			
				$return_data = array();
						
				$folder_name = $this->tools_model->get_value("folder_name", 0, "post", 0, false);
				$parent_folder = $this->tools_model->get_value("parent_folder", 0, "post", 0, false);
				
				//folder name exits ?
				$folder_path = ABSOLUTE_PATH. "files/";
				
				if($parent_folder != "")
					$folder_path .= $parent_folder . "/";
					
				$folder_path .= $folder_name;
				
				if (!is_dir($folder_path))
				{
					//create folder
                    mkdir($folder_path);
					chmod($folder_path,DIR_WRITE_MODE);
					
					$return_data["message"] = "Folder created";
				}
				else
				{
					$return_data["message"] = "Folder already exists";
				}	


				//rereshfolderlist
				$folders = $this->utilities->get_files_with_type();
				$options_html = $this->utilities->print_select_options_array($folders, true, isset($folders[0]) ? $folders[0] : "", "/");
									
				$return_data["html"] = $options_html;
				echo json_encode($return_data);
			
			break;
			
			case 4: //delete folder
				
				$return_data = array();
						
				$folder_name = $this->tools_model->get_value("folder_name",0,"post",0,false);
				
				//fodler name exits ?
				$folder_path = ABSOLUTE_PATH. "files/".$folder_name;
				
				if (!is_dir($folder_path))
				{
					
					$return_data["message"] = "Folder not found";
				}
				else
				{
					$this->utilities->delTree($folder_path."/");
					$return_data["message"] = "Folder deleted";
				}	


				//rereshfolderlist
				$folders = $this->utilities->get_files_with_type();
				$options_html = $this->utilities->print_select_options_array($folders, true, isset($folders[0]) ? $folders[0] : "", "/");
														
				
				
				$return_data["html"] = $options_html;
				
				echo json_encode($return_data);
			
			break;
			
			case 5: //upload file
			
				
				$tmp_name = $_FILES["Filedata"]["tmp_name"];
				$name = $_FILES["Filedata"]["name"];

				$file_path = ABSOLUTE_PATH. "files/".$folder."/".$name;				
				move_uploaded_file($tmp_name, $file_path);
				
  			    chmod($file_path,DIR_WRITE_MODE);

  			    
				if( $folder == 'blog')
				{
                $this->load->library('Image');
                $blog_hero_width = 300;
                
                $this->image->resize_magick($file_path, $file_path, $blog_hero_width, $height=0, $crop = true, $error_message = "");              
				}
			
				echo "done";
            
            break;
            
            case 6:    //refresh folder list
                
                $folders = $this->utilities->get_files_with_type();
                $options_html = $this->utilities->print_select_options_array($folders,true,isset($folders[0]) ? $folders[0] : "");
                
                $return_data["html"] = $options_html;
                
                echo json_encode($return_data);
				
			break;
            
            case 7: //get main content of the file manager page, used for selecting images from the jHtmlArea
            
                $this->data["folders"] = $this->utilities->get_files_with_type();
                $this->data["selected_folder"] = isset($this->data["folders"][0]) ? $this->data["folders"][0] : "";
                $this->data["files"] = $this->utilities->get_files_with_type($this->data["selected_folder"],$this->db);
                $this->data["count_all"] = count($this->data["files"]);
                $this->data["records_per_page"] = $this->records_per_page;
                $this->data['window'] = "popup";
                
                $html = $this->load->view("admin/filemanager/main_inner",$this->data,true);
                $html.= $this->load->view("admin/filemanager/image_properties",$this->data,true);
                
                $return_data = array();
                $return_data["html"] = $html;
                
                echo json_encode($return_data);
            break;
            case 8:  //download image
                
                $file = urldecode($this->tools_model->get_value("file",0,"post",0,false));
                 
                $path = ABSOLUTE_PATH. "files/".$file;
                 
                $this->utilities->download_file($path);
            break;
            case 9: // get new file selector dialog
                $this->data["folders"] = $this->utilities->get_files_with_type();                
                $this->data["selected_folder"] = isset($this->data["folders"][0]) ? $this->data["folders"][0] : "";
                $this->data["files"] = $this->utilities->get_files_with_type($this->data["selected_folder"], $this->db);
                $this->data["count_all"] = count($this->data["files"]);

                $html = $this->load->view("admin/fileselector/main", $this->data, true);

                $return_data = array();
                $return_data["html"] = $html;

                echo json_encode($return_data);                
            break;  
            
         case 10: // get file listing for the selected folder in the selected folder
                $folder_name = $this->tools_model->get_value("folder_name", 0, "post", 0, false);

                $this->data["folders"] = $this->utilities->get_files_with_type();
                $this->data["selected_folder"] = $folder_name;
                $this->data["files"] = $this->utilities->get_files_with_type($this->data["selected_folder"], $this->db);

                $html = $this->load->view("admin/fileselector/file_listing", $this->data, true);

                $return_data = array();
                $return_data["html"] = $html;

                echo json_encode($return_data);                
            break;                      
		}		
	}	
}
?>