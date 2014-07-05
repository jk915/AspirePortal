<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Utilities
{
    
    function Utilities()
    {
                    
    }
    /**
    * @desc The get_block method returns a custom block with the passed id.
    * Note, block id constants are defined in application/config/constants.php
    */
    public function get_block($db, $block_name, $only_enabled = TRUE, $level = 0)
    {
        // Get the row for the passed section.
           $query = $db->get_where('nc_custom_blocks', array('block_name' => $block_name,"enabled"=> ($only_enabled) ? 1 : 0),1);

           // If there is a resulting row, return the blurb, otherwise returned undefined.
           if ($query->num_rows() > 0)
           {
               $row = $query->row();
            $content = $row->block_content;
            
            $search_term = "/(\[\[GALLERY_\w+\]\])|(\[\[BLOCK_\w+\]\])/";        

            // Find any instances in the text that match the pattern [[BLOCK_]]  
            if($level < 10 && preg_match_all($search_term, $content, $matchsets, PREG_PATTERN_ORDER))
            {
                                                
                $matches = $matchsets[0];
                
                
                // Loop through all matches
                foreach($matches as $match)
                {
                    $isGallery = (strpos($match,"[[GALLERY") !== false);
                    $search_term_length = ($isGallery) ? 10 : 8;
                
                    
                    // Extrack out the block name (the value within the square brackets).
                    $pos = strpos($match, "]", 2);
                    
                    if($pos > 0)
                    {
                        // We've found a block or gallery, get it's name.
                        $blockName = substr($match, $search_term_length, $pos - $search_term_length); 
                        
                        //get content for the block
                        $html = $this->get_block($db,$blockName,$only_enabled,$level ++); 
                    
                        //echo $match."===".$blockName."===".htmlentities($html)."===".$search_term_length."===".$isGallery."<br/>";
                    
                        if ($html)
                        {    
                            // Substituion the block defintiion with the block content
                            $content = str_replace($match, $html, $content);        
                        }
                        else
                        {
                            //remove invalid wildchars
                            $content = str_replace($match,"",$content);
                        }
                            
                    }
                    
                    
                }
                
                return $this->replace_site_url($content);
                
            }
            else
                return $this->replace_site_url($content);
            
            
            
        }
        else
            return "";
    }
    
 
    
    
    public function replace_site_url($s)
    {
    	global $website;
    	
        $s = str_replace("SITEURL/", base_url(), $s);  
        $s = str_replace("WEBSITE/", $website . "/", $s);
        
        return $s;  
    }

	/**
	* @desc The getValue function reads values passed by HTTP post or get (or cookies).
	* It allows a default value to be defined, a maximum length allowed, and the
	* ability to automatically add slashes to values for insertion into postgres.
	*/
	public function get_value($name, $default="", $type, $maxlen, $sanitise=false)
	{
    	$CI =& get_instance();
    	
		// Make comparison case insensitive
		$type = strtoupper($type);
		$result = "";

		if($type=="POST")
		{
			$result = $CI->input->post($name);            
		}
		else if($type=="GET")
		{
			$result = $CI->input->get($name);         
		}
		else if($type=="COOKIE")
		{
			$result = $CI->input->cookie($name);         
		}
		else if($type=="SERVER")
		{
			$result = $CI->input->server($name);         
		}
		else
		{
			die("No type specified in safe_get_vars");
		}

		if($result == "")
		{
			$result = $default;
		}

		if(strlen($result) > $maxlen)
		{
			$result = substr($result, 0, $maxlen);
		}

		if($sanitise)
		{
			// Protect from SQL injection
			$result = $CI->input->xss_clean((stripSlashes($result)));
		}

		return $result;
	}
    
    static public function makeRandomStr($slen = 20)
    {
        // This function makes a random character string of $slen len
        // Valid safe characters between 49-57, 65-90 and 97-122

        //srand(time());
        $count = 0;
        $result = "";

        while($count < $slen)
        {
            $invalid = FALSE;
              $myindex = rand(49, 122);

            if((($myindex > 57) && ($myindex < 65)) || (($myindex > 90) && ($myindex < 97)))
            {
                 $invalid = TRUE;
            }
            else
            {
                 $result .= chr($myindex); 
                 $count++;
            }
        }
        
        return $result;
    }    
    
    public function is_logged_in($session)
    {
        $logged_in = true;
        
        if(!isset($session->userdata))
            $logged_in = false;
            
        if(($logged_in) && ($session->userdata("session_id") == ""))
            $logged_in = false;
            
        if(($logged_in) && ($session->userdata("user_id") == ""))
            $logged_in = false;
            
        return $logged_in;
    }
    
    /**
    * The checkRequiredPostFields method is used to check for BOTH the presence of, and not null values of POST form fields.
    * The field names to check should be passed in the 'fields' array.
    * 
    * @param mixed $fields - the array of POST field names to look for.
    */
    public static function checkRequiredPostFields($fields)
    {
        global $_POST;
        
        if(!is_array($fields))
            return false;
            
        foreach($fields as $field)
        {
            if(!isset($_POST[$field]))
            {
                return false;
            }
                
            if($_POST[$field] == "")
            {
                return false;
            }
        }
        
        return true;
    }    


    public function uk_to_isodate($uk_date)
    {
        if($uk_date == "")
            return null;

        $date_array = explode("/", $uk_date);

        if(count($date_array) != 3)
            return null;

        return $date_array[2] . "-" . $date_array[1] . "-" . $date_array[0];
    }

    public function iso_to_ukdate($iso_date)
    {
        if($iso_date == "")
            return null;

        $date_array = explode("-", $iso_date);

        if(count($date_array) != 3)
            return null;

        return $date_array[2] . "/" . $date_array[1] . "/" . $date_array[0];
    }

    public function isodatetime_to_ukdate($iso_date)
    {
        if($iso_date == "")
            return null;

        $tstamp = strtotime($iso_date);
        return date("d/m/y g:i a", $tstamp);
    }
    
    /***
    * isodatetime_to_userdate converts an ISO date into a date (and optionally date time) value
    * that's formatted accordingly to the users country.
    * 
    * @param string $iso_date  The date to format
    * @param bool $include_time Set to true to include the time
    * @return string The formatted date.
    */
    public function isodatetime_to_userdate($iso_date, $include_time = false)
    {
        if($iso_date == "")
            return null;
            
        $CI = &get_instance();
        
        $user = $CI->session->userdata('user');        
		$currency = $user['currency'];		

        $tstamp = strtotime($iso_date);
        
        if($currency == "USD")
			$format = "m/d/Y";
		else
			$format = "d/m/Y";
			
		if($include_time)
			$format .= " g:i a";
				

        return date($format, $tstamp);
    }    

	 /**
    * @desc The print_select_options_array method takes an array, a sort flag, and a selected value (optional),
    * and outputs HTML select option values for all values within the array.
    */
    public function print_select_options_array($options_array, $sort = true, $selected_val = "", $indent_char = "")
    {
        //print_r($options_array);
		$option_html = "";
        
        if(is_assoc($options_array))
        {
            // The array is key/value associative 
            foreach($options_array as $option_key => $option_value)
            {
                $option_html .= "<option value=\"" . $option_key . "\" ";
                $option_html .= ($option_key == $selected_val) ? "selected=\"selected\"" : "";
                $option_html .= ">";
                
                // Added support for indenting options in a list based on the 
                // presence of an indent delimited, e.g. '/'            
                if($indent_char != "")
                {
                    $elements = explode($indent_char, $option);
                    $num_elements = count($elements) - 1;
                    $indent = str_repeat("&ndash;&ndash;", $num_elements);
                    $option_html .= $indent;    
                }
                
                $option_html .= $option_value . "</option>";
            }               
        }
        else
        {
            // The array is a normal, non-associative array
            if($sort)
                sort($options_array); 

            foreach($options_array as $option)
            {
                $option_html .= "<option value=\"" . $option . "\" ";
                $option_html .= ($option == $selected_val) ? "selected=\"selected\"" : "";
                $option_html .= ">";
                
                // Added support for indenting options in a list based on the 
                // presence of an indent delimited, e.g. '/'            
        	    if($indent_char != "")
        	    {
				    $elements = explode($indent_char, $option);
				    $num_elements = count($elements) - 1;
				    $indent = str_repeat("&ndash;&ndash;", $num_elements);
				    $option_html .= $indent;	
        	    }
                
                $option_html .= $option . "</option>";
            }
        }
        
        return $option_html;
    }

	/**
	* @desc The print_select_options method takes an active record result object, a value column,
	* a text column, and a selected value (optional) and outputs HTML select option values for
	* all results within the result object.
	*/
	public function print_select_options($result_object, $val_col, $text_col, $selected_val = "", $default_text = "", $filter_key = "", $filter_match = "")
	{
		$option_html = "";

		if($default_text != "")
		{
			$option_html.= "<option value=\"-1\"";
			$option_html.= ($selected_val == "") ? "selected=\"selected\"" : "";
			$option_html.= " >".htmlspecialchars($default_text)."</option>\n";
		}
        
        $option_text_cols = explode(",", $text_col);
        $option_val_cols = explode(",", $val_col);

		if($result_object)   
		{
			if( is_object( $result_object ) )
			{
				//print_r($result_object);
				//echo 'object';
				foreach($result_object->result() as $row)
				{                             
					// Check for a filter.  If there is a filter, 
					// We need to see if this record matches the filter.  If it
					// doesn't we exclude the record
	         	    if($filter_key != "" && $filter_key!= -1)
	         	    {
						if($row->$filter_key != $filter_match)
							continue;	
	         	    }
                    
                    $option_html.= "<option value=\"";
                    if(count($option_val_cols) > 1) {     
                        foreach($option_val_cols as $col) {
                            $option_html .= htmlspecialchars($row->$col) . " "; 
                        }    
                    } else {
                         $option_html .= htmlspecialchars($row->$val_col);    
                    }
                    $option_html.= "\" ";
					// $option_html.= "<option value=\"" . $row->$val_col . "\" ";
                    
                    if(count($option_val_cols) == 1)
                    {
                        if(is_array($selected_val)) {
                            $option_html.= in_array($row->$val_col,$selected_val) ? "selected=\"selected\"" : "";
                        }
                        else {
                            $option_html.= ($row->$val_col == $selected_val) ? "selected=\"selected\"" : "";
                        }
                    }
					$option_html.= ">";
                    
                    
                    
                    if(count($option_text_cols) > 1) {     
                        foreach($option_text_cols as $col) {
                            $option_html .= htmlspecialchars($row->$col) . " "; 
                        }    
                    } else {
                        $option_html .= htmlspecialchars($row->$text_col);    
                    }
                    
                    $option_html .= "</option>\n";
				}
			}
			else if( is_array( $result_object ) )
			{
				$i = 0;

				//print_r($result_object);
				//echo 'array is..';
				foreach( $result_object as $row )
				{
					// Check for a filter.  If there is a filter, 
					// We need to see if this record matches the filter.  If it
					// doesn't we exclude the record
		         	if( $filter_key != "" && $filter_key!= -1 )
		         	{
							if( $row[$filter_key] != $filter_match )
								continue;	
		         	}
	         		
		         	// get value and text
		         	if( array_key_exists( $val_col, $result_object ) )
		         	{
		         		$row_value = $val_col;
		         		$row_text = $result_object[ $val_col ];
		         	}
		         	else if( is_array( $row ) && array_key_exists( $val_col, $row ) )
		         	{
		         		$row_value = $val_col;
		         		$row_text = $row[ $val_col ];
		         	}
		         	else
		         	{
		         		$row_value = $row;
		         		$row_text = $row;
		         	}
		         	
					$option_html.= '<option value="' . $row_value . '"';
	
					if( is_array( $selected_val ) )
						$option_html.= in_array( $row_value, $selected_val ? 'selected="selected"' : "" );
					else
						$option_html.= ( $row_value == $selected_val ? 'selected="selected"' : "" );
	
					$option_html.= ">" . htmlspecialchars( $row_text ) . "</option>\n";
				}
			}
		}
	
		return $option_html;
	}
    
	/**
	 * @method	get_database_enums_array
	 * @access	public
	 * @desc	this method gets a values of column of selected type like enum and creates an array
	 * @author	Zoltan Jozsa
	 * @param 	string					$table					- name of the table from
	 * @param 	string					$field					- name of the column
	 * @param 	string					$type					- type of the column ex: enum
	 * @return 	array
	 */
	function get_database_enums_array( $table = '', $field = '', $type = 'enum' )
    {
    	$CI = &get_instance();
    	
          $ret_array = array();
          $regex = "/'(.*?)'/";   
          
          $sql = " show columns FROM `$table`";// WHERE type like '%$type%'";
          
          //if ($field != "") $sql.=" and field='".$field."'";
          
          $query = $CI->db->query( $sql );
             
          foreach ($query->result() as $row)
          {
              if( ( !empty( $type ) && strpos( $row->Type, $type ) >= 0 ) && ( !empty( $field ) && $row->Field == $field ) )
              {
              		$columns_string = $row->Type;
              		$columns_string = str_ireplace( $type."(", '', $columns_string );
              		$columns_string = str_ireplace( "'", '', $columns_string );
              		$columns_string = str_ireplace( ")", '', $columns_string );
              		
              		if( strpos( $columns_string, ',' ) >= 0 )
              			$matches = explode( ',', $columns_string );
              		else
              			$matches = $columns_string;
              			
	              //preg_match_all( $regex, $row->Type, $matches );
	              
	              if ( is_array($matches) )
	              {
	                  //build array used for html selects
	                  $values = array();
	                  foreach ($matches as $match)
	                  {
	                      $values[$match] = $match;
	                  }
	                  
	                  $ret_array[$field] = $values;
	              }
	              else
	              {
	                  $ret_array[$field] = array();
	              }
              }
          }
          
          return $ret_array[ $field ];
    }
    
    /**
    * Method: xml_encode
    * Author: Andrew Chapman
    * Description: Replaces reserved XML characters with entities
    * 
    * @param mixed $x - The string to encode for use with xml
    * @return mixed - The resulting encoded string.
    */
    public function xml_encode($x)
    {
        $x = str_replace("<", "&lt;", $x);    
        $x = str_replace(">", "&gt;", $x);
        $x = str_replace("&", "&amp;", $x);
        $x = str_replace("%", "&#37;", $x);
        $x = str_replace("\"", "&#34;", $x);
        
        return $x;
    }
    
    public function extract_from_xml($xml, $tag_name)
    {
        $open = "<" . $tag_name;    
        $close = "</" . $tag_name;
        
        $open_pos = stripos($xml, $open);
        $close_pos = stripos($xml, $close);
        
        if(($open_pos === false) || ($close_pos === false))
            return false;
            
        // Get the position of the ">" after the open tag
        $temp_pos = strpos($xml, ">", $open_pos);
        if($temp_pos === false)
            return false;
            
        if($close_pos <= $temp_pos)
            return false;
            
        $result = substr($xml, $temp_pos + 1, $close_pos - ($temp_pos + 1));
        
        return $result;
    }
    
    public function build_image_tag($image_path, $alt_text, $align="", $height=0, $width=0)
    {
        if($image_path == "")
            return "";
            
        if(stristr($image_path, "http://"))
        {
            $img_pos = stripos($image_path, "/cms");
            if(!$img_pos)
                die("build_image_tag - No cms folder found in image path");
                
            $image_path = substr($image_path, $img_pos + 1);
        }
        
        die($image_path);
            
        if(!file_exists($image_path))
            return "";
        
        $image_data = getimagesize($image_path);
        $iwidth = $image_data[0];
        $iheight = $image_data[1];
        
        if(($height == 0) && ($width == 0))
        {
            // Neither height or width were provided, so use the values from the image itself.
            $height = $iheight;
            $width = $iwidth;    
        }
        else if(($height != 0) && ($width != 0))
        {
            // Manually provided height and width, do nothing    
        }
        else
        {
            if($height > 0)
            {
                $ratio = $height / $iheight;
                $width = floor($iwidth * $ratio);  
            }
            else
            {
                $ratio = $width / $iwidth;
                $height = floor($iheight * $ratio);             
            }    
        }
        
        $image_path = base_url() . "$image_path";
        
        $return = "<img alt=\"$alt_text\" width=\"$width\" height=\"$height\" src=\"$image_path\"";
        
        if($align != "")
            $return .= " align=\"$align\"";
        
        $return .= " />";
        
        return $return;
    }
    
    function resolve_ip_address($ip_address)
    {
        if($ip_address == "")
            return false;
            
        return array("VIC", "Melbourne");
            
        $url = "http://api.hostip.info/get_html.php?ip=" . $ip_address;
         
        $fp = fopen($url, "r");
        if(!$fp)
            return false;
            
        $data = fread($fp, 1024);
        fclose($fp);
        
        if($data == "")
            return false;

        $lines = explode("\n", $data);

        if(count($lines) != 2)
            return false;

        $country = trim(substr($lines[0], 9));
        $city = trim(substr($lines[1], 6));

        if(stristr($country, "Unknown"))
            $country = "Unknown";

        if(stristr($city, "Unknown"))
            $city = "Unknown";

        print "RESOLUTION: $country / $city";
    
        return true;
    }
    
    /**
    * @method get_url
    * @version 1.0
    * @abstract The get_url method reads the data provided at a particular url.
    * 
    * @param mixed $url    The url to read the result for
    * @returns    The html/xml data sent by the url.
    */
    public function get_url($url)
    {
                // Open URL and read the result into $data 
        $fp = @fopen($url, "r");
        if(!$fp)
            return false;
            
        $data = "";
        
        while(!feof($fp))
        {
            $data .= fread($fp, 2048);    
        }
        
        fclose($fp);
        
        // Return the result.
        return $data;
    }    
    
    /***
    * The get_files_with_type method loads either a list of folder names OR a list of files wtihin a folder.
    * If no folder name is given, a list of folder names is returned.
    * If a folder name is given, a list of files is returned
    * 
    * @param mixed $folder	If specified, the the name of folder to load the files from.
    * @param mixed $db	CI database object.
    * @return mixed	An array of files (or folder names)
    */
    function get_files_with_type($folder = "", $db = "") 
    {
        $result = array();
                
        if(!is_dir(ABSOLUTE_PATH. "files"))
            mkdir(ABSOLUTE_PATH. "files",0777);
            
        $dirPath = ABSOLUTE_PATH. "files".(($folder !="") ? "/".$folder : "");
        
        if (!is_dir($dirPath))
            return array();
        
        // Open the directory.
        $dh = opendir($dirPath);    
        
        if(!$dh) 
        {
            return false;    
        }
       
        if ($folder != "")
            $extensions = $this->get_extensions($db);
            
        $system_dirs = array("modal_types", "region", "website");
        
        while($f = readdir($dh))
        {
            if(($f != ".") && ($f != ".."))
            {
            	// If no folder name is given, we want to return an array of folder names
                if ($folder == "")
                {
                	// If this is a folder, add it to the result array.
                    if((is_dir("$dirPath/$f")) && (!in_array($f, $system_dirs))) 
                    {
                        array_push($result, "$f");
                    }
                }
                else
                {
                    if(!is_dir("$dirPath/$f")) {
                        $file = array();
                        
                        $file["name"] = $f;
                        
                        $ext = strtolower(pathinfo("$dirPath/$f", PATHINFO_EXTENSION));                        
                        
                        if(array_key_exists(".".$ext,$extensions))
                            $file["type"] = $extensions[".".$ext];
                        else
                            $file["type"] = "Unknown";  
                            
                        if($file["type"] == "Image")
                        {
							$info = getimagesize("$dirPath/$f", $imageinfo);
							$file["height"] = $info[1];
							$file["width"] = $info[0];
                        }  
                        
                        array_push($result, $file);
                    }    
                }
                
            }
        }
        
        // Add support for loading subfolders
        if ($folder == "")
        {
        	$this->load_sub_directories($result, $result);	
		}        
        
        usort($result, "case_insensitive_file_compare");   // Defined in strings helper.
        
        return $result;                
	}    
	
	function load_sub_directories(&$result, $parents)
	{
		$sub_directories = array();
		
		// Loop through each top level folder
		foreach($parents as $parent_folder)
		{
			$dir_path = ABSOLUTE_PATH. "files/$parent_folder";
			
			// Open this parent folder
			$dh = opendir($dir_path);
			if(!$dh)
				return false;
				
			while($item = readdir($dh))
			{
				$sub_dir_path = $dir_path . "/" . $item;
				
				// See if this item is a subdirectory.
				if(($item != ".") && ($item != "..") && (is_dir($sub_dir_path)))
				{
					// This is a subdirectory.  Add it to the result array
					array_push($result, $parent_folder . "/$item"); 
					array_push($sub_directories, $parent_folder . "/$item");
				}
			}
				
			closedir($dh);
		}		
		
 		if(count($sub_directories) > 0)
 			$this->load_sub_directories($result, $sub_directories);		
		
	}
    
    function get_files($folder = "",$is_sort = false, $return_thumbs = true)
    {
        $result = array();
        
        if($folder != "")
        {
            if(!is_dir(ABSOLUTE_PATH. $folder))
                @mkdir(ABSOLUTE_PATH.$folder ,0777);   
            
            
            $dirPath = ABSOLUTE_PATH.$folder;
            $dh = @opendir($dirPath);  
            
            if(!$dh) {
                return false;    
            }
            
            while($f = readdir($dh))
            {   
                if(($f != ".") && ($f != ".."))
                {   
                    
                    $file_ok = true;
                    
                    if ($return_thumbs == false)
                    {
                        if (strpos($f,THUMB_MEDIUM_PREFIX) !== false || strpos($f,THUMB_SMALL_PREFIX) !== false || strpos($f,THUMB_IMAGE_PREFIX) !== false)
                        {
                            $file_ok = false;
                        }
                    }
                    
                   
                   if ($file_ok)
                   {
                        if(!is_dir("$dirPath/$f")) 
                            array_push($result, "$f");
                   
                   }
                    
                   
                }
            }
            
             if($is_sort)
                        sort($result);    
        }
        return $result;
    }
    
    function delTree($dir, $exeption = "") {
        if (file_exists($dir))
        {
            $files = glob( $dir . '*', GLOB_MARK );
            foreach( $files as $file ){
                //print var_dump($files);
                if( substr( $file, -1 ) == '/' )
                    delTree( $file );
                else
                {
                    if(basename($file) != $exeption)
                    unlink( $file );
                }
            }
            @rmdir( $dir );
        }
        @rmdir($dir);
    }
    
    
    /**
     * This function removes files 
     *
     * @param mixed $folder This is the folder name
     * @param mixed $arr_files This is an array with  the given files     
     *
     */
    function remove_file($folder, $arr_files, $root_dir = "files")
    {
        $dirPath = ABSOLUTE_PATH.$root_dir."/";
        
        if(count($arr_files)>0)
        {
            foreach ( $arr_files as $file)
            {
                //$files_to_delete = array($dirPath.$folder."/".$file,$dirPath.$folder."/".THUMB_MEDIUM_PREFIX.$file,$dirPath.$folder."/".THUMB_SMALL_PREFIX.$file);
                $files_to_delete = array($dirPath.$folder."/".$file);
                
                foreach ($files_to_delete as $file_name)
                {
                    if (is_file($file_name))
                    {
                        unlink($file_name);                    
                    }
                }
            }
        }
    }
    
    function get_extensions($db)
    {
        // Get the extensions 
        $query = $db->get('nc_file_types');
        
        $result_array = $query->result_array();
        
        
        $ext_array = array();
        foreach($result_array as $extension)
        {
            $ext = $extension["extension"];
            $name = $extension["type"];
            
            $ext_array[$ext] = $name;
            
        }
        
        return $ext_array;
    }
    
    function get_robots()
    {
        return array("index,follow","noindex,follow","index,nofollow","noindex,nofollow");
    }
    
    function download_file($path)
    {
        $handle = fopen($path, "r");
        $file = basename($path);
        
        $CI = &get_instance();

        // Replace any spaces in the filename with underscores
        $file = str_replace(" ", "_", $file);
         
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename='.$file);
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Pragma: public');
        header('Content-Length: ' . filesize($path));
        readfile($path);
        fclose($handle);     
    }
    
    function updateHtaccess(&$framework)
    {       
            $path = ".htaccess";
            
            $start = "#custom redirects";
            $end = "#end custom redirects";
            
            
            
        
            if(!file_exists($path))
            {
                echo "Warning, no custom_routes file present. Please check configuration.";    
            }
            else
            {
                
                
                $framework->load->helper('file'); 
                
                $file_content = read_file($path);
                
                
                $start_poz = strpos($file_content, $start);
                $end_poz = strpos($file_content,$end);
                
                
                
                
                
                if ($start_poz === false || $end_poz === false)
                    return;
                    
                    
                //remove old redirects
                $old_redirects = substr($file_content,$start_poz, $end_poz - $start_poz + strlen($end));
                
                                                    
                // Get a list of pages and write the friendly url's for them.
                $framework->db->select('page_id,page_link,page_code');
                $query = $framework->db->get('nc_pages');
                            
                
                
                $data = "#custom redirects \n";
                
                //RewriteRule ^Cabaret_Latte act.php?id=1 [NC]
                foreach($query->result() as $row)
                {
                    if ($row->page_code == "property")
                    {
                        $data .= 'RewriteRule ^(.*)(?<!postback|admin_propertymanager/)'.$row->page_code."/(.*)$ page/show/".$row->page_id."/$2 [NC]\n";
                    }
                    else
                    {
                        $data .= 'RewriteRule ^(.*)(?<!postback/)'.$row->page_code."$ page/show/".$row->page_id." [NC]\n";
                    }
                }
                
                $data .= "#end custom redirects \n";
                
                
                $file_content = str_replace($old_redirects,$data,$file_content);
               
                
                //die($file_content);
                write_file($path, $file_content, 'w');
                
                
            }
            
    }
    
    
    function format_link($link, $default_uri)
    {
        if($link == "/") return "";
        
        //if the doesn't contain the '/' charachter we need concat the default_uri with the link
        if ((strpos($link,"/") === false))
            return $default_uri."/".$link;
        else
            return $link;
    }
    
   /***
   * @desc The generate_random_string method uses a-z and 0-9 characters to generate a random
   * string of specified length (default length is 6 characters).  This method is useful
   * for generating passwords.
   * 
   * @param mixed $password_length The password length to generate.
   * @returns a random password, e.g. "j7kl7v"
   */
    function generate_random_string($password_length = 6)
    {
       $valid_chars = array("a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", 
           "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", "0", "1", "2", "3", "4", "5", "6", "7", "8", "9");
           
       $min = 0;
       $max = 35;
       
       $x = 0;
       $password = "";
       
       while($x < $password_length)
       {
           $password .= $valid_chars[rand($min, $max)];
            $x++;
       }
       
       return $password;    
    }
    
	/**
	* @desc The invokeContentMethods method scans HTML code for PHP method markers and replaces the 
	* marker with the resulting HTML that the php method creates (if the php method exists).  
	* E.g. the marker [[PHP_myFunction]] would invoke the helper method myFunction and the marker 
	* would be replaced with the resulting html.
	*/
	function invokeContentMethods(&$framework, &$content)
	{ 
		// Find any instances in the text that match the pattern [[xx]]  (where x are digits)
		if(preg_match_all('/<p>[.\r\n\s\t]*\[\[PHP_[\w()]+]\]<\/p>/', $content, $matchsets, PREG_PATTERN_ORDER))
		{
			$matches = $matchsets[0];

			// Loop through all matches
			foreach($matches as $match)
			{   
				$search_term_length = 6;

				// Extrack out the block name (the value within the square brackets).
				$pos_start = strpos($match, "[[");
				$pos = strpos($match, "]", 2);         	 

				// Extract out the method name (the value within the square brackets).

				if(($pos_start >= 0) && ($pos > 0))
				{
					$search_term_length += $pos_start;
					$methodName = substr($match, $search_term_length, $pos - $search_term_length); 

					$params = ""; 

					$pos_bracket = strrpos($methodName, "(");
					if($pos_bracket > 0)
					{
						$params = substr($methodName, $pos_bracket + 1, strlen($methodName) - ($pos_bracket + 2)); 
						$methodName = substr($methodName, 0, $pos_bracket);
					}

					if(function_exists($methodName))
					{
						if($params != "")
						{
							$new_content = $methodName($framework, $params);   
						}
						else
						{
							$new_content = $methodName($framework);   
						}

						// Substitute the block marker with the actual block HTML
						$content = str_replace($match, $new_content, $content);  
					}
				}
			}
		}

		// Replace the content.
		return $content;   
	}
   
	/**
	* @desc The invokeContentMethods method scans HTML code for PHP method markers and replaces the 
	* marker with the resulting HTML that the php method creates (if the php method exists).  
	* E.g. the marker [[PHP_myFunction]] would invoke the helper method myFunction and the marker 
	* would be replaced with the resulting html.
	*/
	function replaceVideoTags(&$framework, &$content, $type = "")
	{
		// Find any instances in the text that match the pattern [[xx]]  (where x are digits)
		if(preg_match_all("/\[\[VIDEO-[\w()\-]+]]/x", $content, $matchsets, PREG_PATTERN_ORDER))
		{
			$matches = $matchsets[0];

			// Loop through all matches
			foreach($matches as $match)
			{   
				// Extrack out the method name (the value within the square brackets).
				$pos = strpos($match, "]", 2);

				if($pos > 0)
				{
					$videoID = substr($match, 8, $pos - 8);
					$new_content = "";

					if($type == "fullwidth")
                    {
						$size = 'width="700" height="415"';
                    }
					else
                    {
                        if($type == "front_page" || $type == "highlights_medium")
                        {
                            $size = 'width="320" height="220"';
                        }
                        else
                        {
                            if($type == "collective" || $type == "highlights" )
                                $size = 'width="240" height="160"'; //collective page
                            else if($type == "products")
                                $size = 'width="385" height="257"'; //collective page                                
					        else
                                $size = 'width="480" height="320"';
                        }
                    }

					//$size = ($type == "fullwidth") ? 'width="740" height="415"' : (($type == "front_page") ? 'width="320" height="220"' : 'width="240" height="160"');

					if(!is_numeric($videoID))
					{
						// YOUTUBE
						$new_content = '<object '.$size.'><param name="movie" value="http://www.youtube.com/v/' . $videoID . '&hl=en_GB&fs=1&"></param><param name="allowFullScreen" value="true"></param><param name="allowscriptaccess" value="always"></param><embed src="http://www.youtube.com/v/' . $videoID . '&hl=en_GB&fs=1&rel=0&" type="application/x-shockwave-flash" allowscriptaccess="always" allowfullscreen="true" '.$size.' wmode="transparent"></embed></object>';
					}
					else
					{
						// VIMEO
						$new_content = '<object '.$size.'><param name="allowfullscreen" value="true" /><param name="allowscriptaccess" value="always" /><param name="movie" value="http://vimeo.com/moogaloop.swf?clip_id=' . $videoID . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" /><embed src="http://vimeo.com/moogaloop.swf?clip_id=' . $videoID . '&amp;server=vimeo.com&amp;show_title=1&amp;show_byline=1&amp;show_portrait=0&amp;color=&amp;fullscreen=1" type="application/x-shockwave-flash" allowfullscreen="true" allowscriptaccess="always" '.$size.' wmode="transparent"></embed></object>';
					} 

					// Substitute the block marker with the actual block HTML
					$content = str_replace($match, $new_content, $content);                  
				}
			}
		}
        
		// Replace the content.
		return $content;   
	} 
   
   /**
   * @desc The replaceBlockTags method scans HTML code for BLOCK method markers and replaces the 
   * marker with the resulting HTML that the block method creates (if the block exists).  
   * E.g. the marker [[BLOCK_myFunction]] would invoke the helper method myFunction and the marker 
   * would be replaced with the resulting html.
   */
   function replaceBlockTags(&$framework, &$content)  
   {
        // Find any instances in the text that match the pattern [[BLOCK_w]]  
        if(preg_match_all('/<p>[.\r\n\s\t]*\[\[BLOCK_[a-zA-Z0-9 ]+\]\]<\/p>/', $content, $matchsets, PREG_PATTERN_ORDER))
        {
            $matches = $matchsets[0];
            
            // Loop through all matches
            foreach($matches as $match)
            {   
                $search_term_length = 8;
                
                // Extrack out the block name (the value within the square brackets).
                $pos_start = strpos($match, "[[");
                $pos = strpos($match, "]", 2);
                
                if(($pos_start >= 0) && ($pos > 0))
                {
                        // We've found a block or gallery, get it's name.
                        $search_term_length += $pos_start;
                        
                        $blockName = substr($match, $search_term_length, $pos - $search_term_length); 

                        //get content for the block
                        $html = $this->get_block($framework->db,$blockName,$only_enabled = true,$level = 0); 
                                                    
                        if ($html)
                        {    
                            // Substituion the block defintiion with the block content
                            $content = str_replace($match, $html, $content);        
                        }
                        else
                        {
                            //remove invalid wildchars
                            $content = str_replace($match,"",$content);
                        }  
                }
            }
        }
        // Replace the content.
       return $this->replace_site_url($content);                 
   }
   
   /**
   * @desc The replaceSettingTags method scans HTML code for SETTING_ markers and replaces the 
   * marker with the value of the corresponding setting if possible (it should exist as a constant, loaded in the "settings" hook).
   * E.g. the marker [[SETTING_email]] would replace the tag with the email address defined in the global settings.
   */
   function replaceSettingTags(&$framework, &$content)  
   {
        // Find any instances in the text that match the pattern [[SETTING_w]]  
        if(preg_match_all("/\[\[SETTING_[a-zA-Z0-9 ]+\]\]/", $content, $matchsets, PREG_PATTERN_ORDER))
        {
            $matches = $matchsets[0];
            
            // Loop through all matches
            foreach($matches as $match)
            {   
                $search_term_length = 10;
                
                // Extrack out the setting name (the value within the square brackets).
                $pos = strpos($match, "]", 2);

                if($pos > 0)
                {
                        // We've found a setting, get it's name.
                        $settingName = substr($match, $search_term_length, $pos - $search_term_length); 
                        $constantName = "OWNERDETAILS_" . $settingName;
                        
                        if(defined($constantName))
                        {
							$html = constant($constantName);
                        }
                        else
                        {
							$html = "";
                        }
   
   						// Substitute the setting tag with the constant value
                        $content = str_replace($match, $html, $content);        
                }
            }
        }
        // Replace the content.
       return $this->replace_site_url($content);                 
   }   
   
   function clean_html($html)
   {
        if($html == "")
            return $html;
            
        $html = str_replace(array("<BR>", "<br>"), "<br />", $html);
        $html = str_replace("<H1>", "<h1>", $html);
        $html = str_replace("<H2>", "<h2>", $html);
        $html = str_replace("<H3>", "<h3>", $html);
        $html = str_replace("<H4>", "<h4>", $html);
        $html = str_replace("<UL>", "<ul>", $html);
        $html = str_replace("</UL>", "</ul>", $html);
        $html = str_replace("<LI>", "<li>", $html);
        $html = str_replace("</LI>", "</li>", $html);
        
        return $html;
   }
   
   function print_select_category_tree($categories, $selected_category_id = "")
   {
   	    $array_cat = array();
        $html = "";
   	
        if($categories)
        {
		    foreach($categories->result() as $category)
		    {
			    $this_cat = array();
			    $this_cat["category_id"] = $category->category_id;
			    $this_cat["category_name"] = $category->name;
			    $this_cat["parent_id"] = $category->parent_id;
			    
			    array_push($array_cat, $this_cat);
		    }	
		     		
		
		    foreach($array_cat as $category)
		    {
			    // Only print the category if it's a top level category
			    if($category["parent_id"] == -1)
			    {
				    $html.= "<option value=\"" . $category["category_id"] . "\" ";
				    
				    if(($selected_category_id != "") && ($selected_category_id == $category["category_id"]))
					    $html.= "selected=\"selected\"";
				    
				    $html.= ">" . $category["category_name"] . "</option>";
				    
				    // Look for any children of this category
				    foreach($array_cat as $sub_category)
				    {
					    if($sub_category["parent_id"] == $category["category_id"])
					    {
						    $html.= "<option value=\"" . $sub_category["category_id"] . "\" ";
						    
						    if(($selected_category_id != "") && ($selected_category_id == $sub_category["category_id"]))
							    $html.= "selected=\"selected\"";						
						    
						    $html.= ">--- " . $sub_category["category_name"] . "</option>";
						    
						    
						    // Look for any children of this category
						    foreach($array_cat as $sub_category2)
						    {
							    if($sub_category2["parent_id"] == $sub_category["category_id"])
							    {
								    $html.= "<option value=\"" . $sub_category2["category_id"] . "\" ";
								    
								    if(($selected_category_id != "") && ($selected_category_id == $sub_category2["category_id"]))
									    $html.= "selected=\"selected\"";						
								    
								    $html.= ">------ " . $sub_category2["category_name"] . "</option>";
							    }					
						    }						    
						    
					    }					
				    }
			    }			
		    }
        }
		
		return $html;	
   }

   function print_select_product_tree($categories, $selected_category_id = "")
   {
   	    $array_cat = array();
        $html = "";

        if($categories)
        {
		    foreach($categories->result() as $category)
		    {
			    $this_cat = array();
			    $this_cat["category_id"] = $category->product_category_id;
			    $this_cat["category_name"] = $category->name;
			    $this_cat["parent_id"] = $category->parent_id;

			    array_push($array_cat, $this_cat);
		    }


		    foreach($array_cat as $category)
		    {
		    	if($category["parent_id"] == -1)  
		    	{
			        $html.= "<option value=\"" . $category["category_id"] . "\" ";

				    if(($selected_category_id != "") && ($selected_category_id == $category["category_id"]))
					    $html.= "selected=\"selected\"";

				    $html.= ">" . $category["category_name"] . "</option>";

				    // Look for any children of this category
				    foreach($array_cat as $sub_category)
				    {
					    if($sub_category["parent_id"] == $category["category_id"])
					    {
						    $html.= "<option value=\"" . $sub_category["category_id"] . "\" ";

						    if(($selected_category_id != "") && ($selected_category_id == $sub_category["category_id"]))
							    $html.= "selected=\"selected\"";

						    $html.= ">--- " . $sub_category["category_name"] . "</option>";
						    
							// Look for any children of this category
							foreach($array_cat as $sub_category2)
							{
								if($sub_category2["parent_id"] == $sub_category["category_id"])
								{
									$html.= "<option value=\"" . $sub_category2["category_id"] . "\" ";
									
									if(($selected_category_id != "") && ($selected_category_id == $sub_category2["category_id"]))
										$html.= "selected=\"selected\"";						
									
									$html.= ">------ " . $sub_category2["category_name"] . "</option>";
								}					
							}						    
						    
					    }   
				    }
				}
		    }
        }

		return $html;
   }
   
    
    function get_file_tree(&$tree = array(), $folder = "")
    {
    	// If no folder is defined, start at the root path
		if($folder == "")
		{
			$folder = ABSOLUTE_PATH . "files";
		}
		
		// Open a directory handle to the folder
		$dh = opendir($folder);
		if(!$dh)
		{
			die("Cannot open folder: $folder");
		}
		
		$new_folders = array();
		
		// Loop through all of the files and folders in this directory
		while($file = readdir($dh))
		{
			// Ignore unix . and .. files
			if(($file == ".") || ($file == ".."))
			{
				continue;
			}
			
			$path = $folder . "/" . $file;
			
			// If this is a directory, store it in a list of directories to explore later
			if(is_dir($path))
			{
				$new_folders[] = $path;
			}
			else
			{
				$tree[] = $path;	
			}
		}
		
		closedir($dh);
		
		// Loop through any sub folders found
		foreach($new_folders as $this_folder)
		{
			$this->get_file_tree($tree, $this_folder);	
		}
    }
   
	function create_thumb($thumb_image_settings, $folder, $file_name)
	{
		$CI = & get_instance();
		$CI->load->library('Image');  

		$error_message = "";
		
		foreach($thumb_image_settings as $setting)
		{
			$error_message = ""; 
			$thumb_path = $folder.$setting['prefix'].$file_name; 

			$CI->image->create_thumbnail($folder.$file_name,$thumb_path,$error_message,$setting['width']);

			if($error_message  == "")
				chmod($thumb_path,0777); 

		}

		return $error_message;
	}

	/***
	* Checks a CI resultset for a matching value in a specified column.
	* 
	* @param mixed $rst	The recordset to check
	* @param mixed $match_col	The column name to check
	* @param mixed $match_value	The value to look for
	*/
	function is_in_recordset($rst, $match_col, $match_value)
	{
		if(!$rst)
			return false;
			
		foreach($rst->result() as $item)                        
		{
            if($item->$match_col == $match_value)
        		return true;
		}
		
		return false;
	}
	
	function url_strip($s)
	{
		if($s == "")
			return $s;
			
		$s = str_replace(",", " ", $s);
		$s = str_replace("  ", " ", $s);
		
		return $s;
	}
	
	/***
	* set_admin_website_id
	* Checks for a selection of a website from the website selector
	* and if present, sets the selected website in the session.
	*/
	function set_admin_website_id()
	{
		$CI = & get_instance();
		
		$website_id = $CI->input->post("website_id");
		
		if(($website_id != "") && (is_numeric($website_id)))
		{
			// The user has selected a new website
			$CI->session->set_userdata("website_id", $website_id); 
		}
	}
    
    
    function get_website_id_by_code( $code )
    {
        $CI = & get_instance();
        $CI->load->model("website_model");
        
        //get id of the coresponding website code
        $website = $CI->website_model->get_details( "", "url_id = '" . $code . "'" );
                                                                                               
        if ($website)
        {
            return $website->website_id;
        }
        else
            return false;
    }
    
    function update_session_website_by_code( $website_code )
    {
           $new_website_id = $this->get_website_id_by_code( $website_code );       
           
           //save in session
           if ($new_website_id)
           {
                    $CI = & get_instance();
                    $CI->session->set_userdata("website_id", $new_website_id);
                    $CI->session->set_userdata("website_code", $website_code);
                    
                    return $new_website_id;
           }
           
           return FALSE;
    } 
    
    function get_session_website_id( $frontpage = FALSE)
    {
        if($frontpage)
        {
            global $website;
            
            //check website_id in session
            $CI = & get_instance();
            $sess_website_id = $CI->session->userdata("website_id");
            $sess_website_code = $CI->session->userdata("website_code");
            

            //no website id in session ?
            if((!$sess_website_id) || ($sess_website_id == "-1"))
            {        
                 return $this->update_session_website_by_code($website);
            }
            else
            {
                //check to see if the current website is different from the session website
                if ($sess_website_code != $website)
                {
                    return $this->update_session_website_by_code($website);
                }
                else
                    return $sess_website_id;
            } 
            
            return FALSE;            
        }
        else
        {
            $CI = & get_instance();
            $website_id = $CI->session->userdata("website_id");
            
            return (is_numeric($website_id) ? $website_id : "" );
        }
    }
    
   /**
   * @desc The replaceFlashTags method scans HTML code for flash markers and replaces the 
   * marker with the resulting HTML that the flash creates (if the flash exists).  
   * E.g. the marker [[FLASH_name;width;height]] would be replaced with a swfObject 
   */
   function replaceFlashTags(&$framework, &$content)
   {
        // Find any instances in the text that match the pattern [[FLASH_w]]  
        if(preg_match_all("/\[\[FLASH-\w+\.\w+;\d+\;\d+\]\]/", $content, $matchsets, PREG_PATTERN_ORDER))
        {
            $matches = $matchsets[0];
            
            // Loop through all matches
            foreach($matches as $match)
            {   
                $search_term_length = 8;
                
                // Extrack out the flash name (the value within the square brackets).
                $pos = strpos($match, "]", 2);
                
                if($pos > 0)
                {
                    $flash = substr($match, $search_term_length, $pos - $search_term_length);
                    $arr_flash = explode(";",$flash);
                    $new_content = "";
                    
                    if(count($arr_flash) == 3)
                    {
                        $flash_name = $arr_flash[0];
                        $width = $arr_flash[1];
                        $height = $arr_flash[2];
                        
                        //check if the flash exists
                        if(file_exists(FCPATH."files/flash/".$flash_name))
                        {  
                            $info = pathinfo($flash_name);
                            
                            $id ="flash_".basename($flash_name,'.'.$info['extension']);
                            $new_content = '<div id="flash_container_'.$id.'"></div>';
                            //$new_content .= '
                            //<script type="text/javascript">                        
                            //var flash_'.$id.' = new SWFObject( base_url+"files/flash/'.$flash_name.'", "'.basename($flash_name,'.'.$info['extension']).'", '.$width.', '.$height.', 7, "#FFFFFF");
                            //flash_'.$id.'.addParam("wmode","transparent");
                            //flash_'.$id.'.write("flash_container_'.$id.'");
                            //</script>';  
                            
                            $new_content .= '
                            <script type="text/javascript">  
                            swfobject.embedSWF(base_url + "files/flash/'.$flash_name.'", "flash_container_'.$id.'", "'.$width.'", "'.$height.'", "9.0.0");                      
                            </script>';                                                   
                        }
                    }
                    
                    // replace the FLASH with SWFObject
                    $content = str_replace($match, $new_content, $content);                  
                }
            }
        }
        
        return $content;
   }    
    
    public function replaceTags(&$framework, $content, $hint = "")
    {
        $content = $this->replaceBlockTags($framework, $content);
        $content = $this->replaceSettingTags($framework, $content);
        $content = $this->invokeContentMethods($framework, $content);
        
        if($hint == "")
        	$content = $this->replaceVideoTags($framework, $content);
        else
        	$content = $this->replaceVideoTags($framework, $content, $hint);
        	
        $content = $this->replaceFlashTags($framework, $content); 
        
        return $content;
    }  
    
    public function get_article_image($article_id, $category_id, $row_id, $type = "front_page")
    {
        $CI =& get_instance();
        
        $CI->load->model("modal_types_model");
        $CI->load->model("article_model");
        
        $website_id = $this->get_session_website_id(TRUE);
        $article_image = "";
                            
        if($article_id > 0){ //check if the image is override else return article hero_image
            $article = $CI->modal_types_model->get_details((-1)*$row_id, $modal_type = 1, $article_id, $type, $website_id);             
            $article_image = ($article) ? $article->parameter : "";
        }
        else
        {
            $article = $CI->article_model->get_articles($article_id, $category_id);                                
            $article_image = ($article) ? $article->hero_image : "";
        }
                                   
        if($article)
        {
                                    
            if(($article_image != "") && file_exists(FCPATH . $article_image))
                $article_image = $article_image;                    
        } 
        
        return $article_image;  
    }
    
	function add_to_debug($message)
	{
		
		
		$fp = fopen(ABSOLUTE_PATH . "files/debug.txt", "a")
			or die("Couldn't open debug file");
			
		fputs($fp, date("d/m/y h:i a") . " - $message\n");
		
		fclose($fp);
	}      
    
    /* @access public 
     * @param string string we are operating with 
     * @param integer character count to cut to 
     * @param string|NULL delimiter. Default: '...' 
     * @return string processed string 
     **/ 
    function neat_trim($str, $n, $delim='...') { 
       $len = strlen($str); 
       if ($len > $n) { 
           preg_match('/(.{' . $n . '}.*?)\b/', $str, $matches); 
           return rtrim($matches[1]) . $delim; 
       } 
       else { 
           return $str; 
       } 
    }
    
    /**
    * Limits the number of words in a string.
    * @param string $string                  
    * @param uint $word_limit   -   number of words to return   
    * @returns string           -   new string containing only words up to the word limit.
    */
    function string_limit_words($string, $word_limit) {
     $words = explode(' ', $string);
     return implode(' ', array_slice($words, 0, $word_limit));
    } 
    
    /**
     * @method	in_multiarray
     * @access	public
     * @desc	this method checks if an elem exists in a multiarray or not
     * @author	Zoltan Jozsa
     * @param 	mixed						$elem					- the elem to search
     * @param 	mixed						$array					- the array r object where we are checking
     * @return 	boolean
     */
	function in_multiarray($elem, $array)
    {
    	// if the $array is an array or is an object
     	if( is_array( $array ) || is_object( $array ) )
     	{
     		// if $elem is in $array object
     		if( is_object( $array ) )
     		{
     			$temp_array = get_object_vars( $array );
	     		if( in_array( $elem, $temp_array ) )
	     			return TRUE;
     		}
     		
     		// if $elem is in $array return true
     		if( is_array( $array ) && in_array( $elem, $array ) )
     			return TRUE;
     			
     		
     		// if $elem isn't in $array, then check foreach element
     		foreach( $array as $array_element )
     		{
     			// if $array_element is an array or is an object call the in_multiarray function to this element
     			// if in_multiarray returns TRUE, than return is in array, else check next element
     			if( ( is_array( $array_element ) || is_object( $array_element ) ) && $this->in_multiarray( $elem, $array_element ) )
     			{
     				return TRUE;
     				exit;
     			}
     		}
     	}
     	
     	// if isn't in array return FALSE
     	return FALSE;
    }
    
    /**
     * @method	valid_email
     * @access	public
     * @desc	this method checks an email is valid or not
     * @param 	string 					$email				- the email address to check
     * @return 	boolean
     */
    public function valid_email( $email = '' )
    {
        return ( is_string( $email ) && preg_match("/^([a-z0-9])(([-a-z0-9._])*([a-z0-9]))*\@([a-z0-9])*(\.([a-z0-9])([-a-z0-9_-])([a-z0-9])+)*$/i", $email ) );        
    	
    }
    
	function datefmt($date, $inFormat, $outFormat)
	{
	    /* A function to take a date in ($date) in specified inbound format (eg mm/dd/yy for 12/08/10) and
	     * return date in $outFormat (eg yyyymmdd for 20101208)
	     *    datefmt (
	     *                        string $date - String containing the literal date that will be modified
	     *                        string $inFormat - String containing the format $date is in (eg. mm-dd-yyyy)
	     *                        string $outFormat - String containing the desired date output, format the same as date()
	     *                    )
	     *
	     *
	     *    ToDo:
	     *        - Add some error checking and the sort?
	     */
	
	    $order = array('mon' => NULL, 'day' => NULL, 'year' => NULL);
	   
	    for ($i=0; $i<strlen($inFormat);$i++) 
	    {
	        switch ($inFormat[$i]) 
	        {
	            case "m":
	                $order['mon'] .= substr($date, $i, 1);
	                break;
	            case "d":
	                $order['day'] .= substr($date, $i, 1);
	                break;
	            case "y":
	                $order['year'] .= substr($date, $i, 1);
	                break;
	        }
	    }
	   
	    $unixtime = mktime(0, 0, 0, $order['mon'], $order['day'], $order['year']);
	    $outDate = date($outFormat, $unixtime);
	
	    if ($outDate == False) 
	    {
	        return False;
	    } 
	    else 
	    {
	        return $outDate;
	    }
	}
	
	function deleteAll($directory, $empty = false) 
	{
	    if(substr($directory,-1) == "/") 
	    {
	        $directory = substr($directory,0,-1);
	    }
	
	    if(!file_exists($directory) || !is_dir($directory)) 
	    {
	        return false;
	    } 
	    elseif(!is_readable($directory)) 
	    {
	        return false;
	    } 
	    else 
	    {
	        $directoryHandle = opendir($directory);
	       
	        while ($contents = readdir($directoryHandle)) 
	        {
	            if($contents != '.' && $contents != '..') 
	            {
	                $path = $directory . "/" . $contents;
	               
	                if(is_dir($path)) 
	                {
	                    deleteAll($path);
	                } 
	                else 
	                {
	                    unlink($path);
	                }
	            }
	        }
       
	        closedir($directoryHandle);
	
	        if($empty == false) 
	        {
	            if(!rmdir($directory)) 
	            {
	                return false;
	            }
	        }
	        return true;
    	}
	}
    
    
    function currency_converter($price, $from = 'AUD', $to = 'USD')
    {
        
        $GOOGLE_URL = "http://www.google.com/finance/converter?a=%d&from=%s&to=%s";
        
        //Fetch with CURL   
        $ch = curl_init ();
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_URL, sprintf($GOOGLE_URL,$price,$from,$to));
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, true);
        $response = curl_exec($ch);
        curl_close($ch);
        
        //Try to Convert
        
        $return_value = false;
        if($response) {
            if (preg_match("/<span class=\"?bld\"?>([\d.]*)/", $response, $matches)) {
            $return_value = $matches[1];
            }
        } 
        return number_format( $return_value, 0);
    }
    
    /***
    * Gets a list of all the directories within a specific directory
    * @returns An array of directory paths
    */ 
    function get_directories($path)
    {
        $dirs = array();
        
        $dh = opendir($path);
        if(!$dh) {
            return false;
        }  

        while($item = readdir($dh)) {
            if(($item == ".") || ($item == "..")) {
                continue;    
            }
            
            if(is_dir($path . "/" . $item)) {
                $dirs[] = $path . "/" . $item;    
            }
        }
        
        closedir($dh);

        return $dirs;    
    }     
	
}
