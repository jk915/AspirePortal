<?php
/**
* @desc common function used accross all models/views
* @created 17 November, 2009
* @last modified: 
*/
class Tools_model extends CI_Model
{
	function Tools_model()
	{	
		parent::__construct();
		
		$this->load->model("Error_model");		
	}
    
	/**
	* @desc The getValue function reads values passed by HTTP post or get (or cookies).
	* It allows a default value to be defined, a maximum length allowed, and the
	* ability to automatically add slashes to values for insertion into postgres.
	*/
	public function get_value($name, $default="", $type, $maxlen, $sanitise=false)
	{
		// Make comparison case insensitive
		$type = strtoupper($type);
		$result = "";
		
		if($type=="POST")
		{
			$result = $this->input->post($name);			
		}
		else if($type=="GET")
			{
				$result = $this->input->get($name);		 
			}
			else if($type=="COOKIE")
				{
					$result = $this->input->cookie($name);		 
				}
				else if($type=="SERVER")
					{
						$result = $this->input->server($name);		 
					}
					else
					{
						die("No type specified in safe_get_vars");
					}
		
		if($result == "")
		{
			$result = $default;
		}
		
		if($maxlen != 0 && strlen($result) > $maxlen)
		{
			$result = substr($result, 0, $maxlen);
		}
		
		if($sanitise)
		{
			// Protect from SQL injection
			$result = $this->security->xss_clean($result);
		}
		
		return $result;
	}	
	
	function printArray($data)
	{
		echo "<pre>";print_r($data);echo "</pre>";
	}
	
	function isPost()
	{
		return (strtolower($_SERVER['REQUEST_METHOD'])=='post');
	}
    
    function run_query($sql)
    {
        $query = $this->db->query($sql);
      
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    //@desc - uploads an image and creates all sizes, parameter hmtl input file name, returns an array on success
    function uploadImage($upload_path, $input_field_name="photofile")
    {
        $config['upload_path'] = $upload_path;
        $config['allowed_types'] = 'gif|jpg|png';
        $config['max_size']    = '100';
        $config['max_width']  = '2500';
        $config['max_height']  = '768';
        
        $this->load->library('upload',$config);
        $this->load->helper('date');
        
        if (!$this->upload->do_upload($input_field_name))
        {    
            $error = $this->upload->display_errors();
            echo $error;
            return "";
    
        }
        else
        {
            //if upload is ok
            $picturedata = $this->upload->data();
            //echo var_dump($picturedata);                    
            $datestring = "%Y%m%d%H%i%s";
            $time = time();

            $file_path = $picturedata['file_path'];
            $old_file = $picturedata['full_path'];
            
            //new file
            $newFileName=$picturedata['raw_name']."_".mdate($datestring, $time).$picturedata['file_ext'];
            //$thumbNewFileName = "thumb_".$newFileName;
            
            $newFile_path = $file_path.$newFileName;
            //$newFile_path_thumb = $file_path.$thumbNewFileName;
            
            rename($old_file,$newFile_path);
            
            //$this->tools_model->createThumb2($newFile_path,$newFile_path_thumb); 
            //$this->_createThumb($newFile_path);
            
            //create image types
            //$image_type_array = $this->tools_model->createAllImageTypes($newFile_path);

            $ret_array =  array(
                            'newfilename'=>$newFileName,
                            'newfile_path'=>$newFile_path                            
                            );
            
            return $ret_array;
        }
    }
    
    function get_states( $country_id = '')
    {
        if( $country_id != '' && is_numeric($country_id) )
            $this->db->where("country_id", $country_id);
            $this->db->order_by("name", "ASC");
         $query = $this->db->get('states');
         
         if($query->num_rows() > 0)
            return $query;
         else
            return false;          
    }
    
	function get_regions()
    {
        
         $this->db->order_by("region_name", "ASC");
         $query = $this->db->get('regions');
         
         if($query->num_rows() > 0)
            return $query;
         else
            return false;          
    }
	
    function get_countries()
    {
         $this->db->order_by("name");
         $query = $this->db->get('countries');
         
         if($query->num_rows() > 0)
            return $query;
         else
            return false;          
    }
    
    /**
     * @method	get_payment_gateways
     * @access	public
     * @desc	this method gets all payment gateways from database
     * @author	Zoltan Jozsa
     * @return 
     */
    public function get_payment_gateways()
    {
    	$this->db->order_by( 'name', 'asc' );
    	$query = $this->db->get( 'nc_payment_gateways' );
    	
    	if( $query->num_rows() > 0 )
    		return $query;
    		
    	return FALSE;
    }
    
    /**
     * @method	get_price_access_levels
     * @access	public
     * @desc	this method gets all price access levels from database
     * @author	Zoltan Jozsa
     * @return 
     */
    public function get_price_access_levels()
    {
    	$this->db->order_by( 'name', 'asc' );
    	$query = $this->db->get( 'nc_price_access_levels' );
    	
    	if( $query->num_rows() > 0 )
    		return $query;
    		
    	return FALSE;
    }
        
    function upload_company_logo($user_id, $FILES)
    {
       
        if($user_id != 0)
       {
            $this->load->library("Image");
            $this->load->library("Utilities");
             
            //folder name exits ?
            $folder_path = ABSOLUTE_PATH. "files/company_logos/".$user_id;
            //delete all files
            $this->utilities->delTree($folder_path."/");
            
            if (!is_dir($folder_path))
            {
                //create folder
                mkdir($folder_path);
                chmod($folder_path,0777);
            }
            
            $uploaded_file_name="";
            $file_selected = ($FILES['upload_file']['size'] != 0);
            
            
            if($file_selected)
            {
                $upload_path = "./files/company_logos/".$user_id;
                $res_arr = $this->tools_model->uploadImage($upload_path, "upload_file", 1240, 236, 100, "upload_company");
                
                if ($res_arr !="" && is_array($res_arr))
                {
                    $uploaded_path = $res_arr['newfile_path'];
                    $upload_file_name = $res_arr['newfilename'];
                    $error_message = "";

                    $info = pathinfo($upload_file_name);
                                                             
                    $input = $folder_path."/".$upload_file_name;
                    $output = $folder_path."/thumb_".$user_id.".".$info['extension'];
                    list($width, $height) = getimagesize($input);  
                    
                    if($height > 236) //it's bigger than 40mm
                        $this->image->create_thumbnail($input,$output,$error_message,236); 
                    else
                        rename($input, $output);
                            
                    
                    if($error_message != "")
                       $this->session->set_flashdata('error_message', "Error: ".$error_message);
                                                           
                }
                else
                {
                    if($res_arr != "")
                    {
                        $folder_path = ABSOLUTE_PATH. "files/company_logos/".$user_id;
                        //delete all files
                        $this->utilities->delTree($folder_path."/");
                        $this->session->set_flashdata('error_message', "Error: ".$res_arr);  
                    }
                }
            }
       }
    }
    
    function alphanumeric_link($string)
    {
        $string = preg_replace("/[^a-zA-Z0-9\s]/", "", $string);
        $string = strtolower(str_replace(" ","_",$string));
        
        return $string;
    }
    
    function get_tables($table)
    {
        if($table === 0 || $table === -1)
            return false;
        
        switch($table)
        {
            case "articles":
            	$query = "SELECT `nc_articles`.`article_id` as id, " .
            		"CONCAT_WS(' - ', `nc_article_categories`.`name`, `nc_articles`.`article_title`) as name " .
            		"FROM (`nc_articles`) " .
            		"JOIN `nc_article_categories` ON `nc_articles`.`category_id` = `nc_article_categories`.`category_id` " .
            		"ORDER BY `nc_articles`.`category_id`, `article_title`";
            		
            	$query = $this->db->query($query);
            break;   
             
            case "pages":    
                $this->db->select("page_id as id, page_title as name");
                $this->db->order_by("page_title");
                $this->db->from($table); 
                $query = $this->db->get();
            break;
            
            default:
            	return false;
            break;                
        }
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query;
        }         
        else
            return false;
    }
    
    public function get_serial_generators()
    {
    	$this->load->helper("file");
    	$dirname = FCPATH."serialgen";
    	
    	$arr = get_filenames( $dirname );
        sort($arr);
        
    	$array = array();
    	$array[0] = "Choose";
    	foreach ($arr as $item)
    	{
    		$file = str_replace(".inc.php","",$item);
    		$array[$file] = $file;
    	}
    	
    	return $array;
    	
    }
    
    function get_status($delivery_method = "")
    {
       $arr_data[""] = "Choose";
       $arr_data["failed"] = "Failed";
       $arr_data["pending"] = "Pending";
       $arr_data["completed"] = "Completed";         
         
       return $arr_data;  
    }
    
    function get_stage_status($status_name = 'status')
    {
        $arr_data = array();
        if ($status_name == 'status')
        {
            $arr_data[""] = "Choose";
            $arr_data["pending"] = "Pending";
            $arr_data["inprogress"] = "In Progress"; 
            $arr_data["completed"] = "Completed";    
        }
        else if ($status_name == 'public')
        {
            $arr_data[""] = "Choose";
            $arr_data["Yes"] = "Yes";
            $arr_data["No"] = "No"; 
        }
             
         
        return $arr_data;  
    }
}