<?php
	function select_menu_item($search_for, $found_class)
	{
		if(is_array($search_for))
		{
			foreach($search_for as $s)
			{
				if(strstr($_SERVER["REQUEST_URI"], $s))
				{
					echo "class=\"$found_class\" ";				
					return;
				}
			}	
		}
		else
		{
			if(strstr($_SERVER["REQUEST_URI"], $search_for))
				echo "class=\"$found_class\" ";
		}
	}
   
	function select_drop_down($search_for)
	{
		if(strstr($_SERVER["REQUEST_URI"], $search_for))
			echo ' selected="selected" ';    
	}
   
	function inject_before($source, $inject_str, $match)
	{
		if(($source == "") || ($inject_str == "") || ($match == ""))
			return $source;

		$pos = stripos($source, $match, 0);

		if($pos === false)
			return $source;

		$start = substr($source, 0, $pos);
		$end = substr($source, $pos);
		return $start . $inject_str . $end;
	}
	
    /***
    * The tags_add_commas method adds commas to the start and end of a tags string.
    * e.g. tag1,tag2 is converted to ,tag1,tag2,
    * 
    * @param string $tags The tags string to convert
    * @return string The converted tag string.
    */
	function tags_add_commas($tags)
	{
   		if(substr($tags, 1, 1) != ",")
			$tags = ",$tags";
			
		if(substr($tags, strlen($tags) - 1, 1) != ",")
			$tags = $tags . ",";
			
		$tags = str_replace(" ", "", $tags);
		$tags = strtolower($tags);

		return $tags;
	}	
   
    /***
    * The tags_remove_commas method removes commas from the start and end of a tags string.
    * e.g. ,tag1,tag2, is converted to tag1,tag2
    * 
    * @param string $tags The tags string to convert
    * @return string The converted tag string.
    */
	function tags_remove_commas($tags)
	{
		if(substr($tags, 1, 1) == ",")
			$tags = substr($tags, 2);

		if(substr($tags, strlen($tags) - 1, 1) == ",")
			$tags = substr($tags, 1, strlen($tags) - 2);

		return $tags;
	}
	
	function max_words($str, $max_words)
	{
		if($str == "")
			return $str;
			
		$elements = explode(" ", $str);
		$num_elements = count($elements);
		
		if($num_elements <= $max_words)
			return $str;
			
		$result = "";
		$word_count = 0;
		foreach($elements as $element)
		{
			if($result != "") $result .= " ";
			$element = str_replace(",", "", $element);
			$result .= $element;
			$word_count++;
			
			if($word_count >= $max_words)
				break;
		}
		
		return $result;
	}
    
    /***
    * Makes sure that passed string is not longer than max_len chars.  If it is, it is truncated
    * 
    * @param string $s The string to truncate if necessary
    * @param integer $max_len The maximum length the string should be
    * @param string $append What to append to the end of the string if it it truncated
    * @return string
    */
    function max_chars($s, $max_len = 100, $append = "...")
    {
        if($s == "") return "";
        
        $return = $s;
        $slen = strlen($s);
        
        if($slen > $max_len)
        {
            $return = substr($s, 0, $max_len) . $append;    
        }
        
        return $return;
    }
	
    function case_insensitive_file_compare($a, $b)
    {
		if(is_array($a))
		{
        	$al = strtolower($a["name"]);
        	$bl = strtolower($b["name"]);
		}
		else
		{
        	$al = strtolower($a);
        	$bl = strtolower($b);			
		}
        
        if ($al == $bl) 
        {
            return 0;
        }
        
        return ($al > $bl) ? +1 : -1;
    }
    
    function prepare_for_metadesc($s)
    {
		if($s == "")
			return $s;
			
		$s = strip_tags($s);
		$s = str_replace("<br>", "", $s);
		$s = str_replace("<br/>", "", $s);
		$s = str_replace("\n", "", $s);
		
		return $s;
    }
    
    function ifvalue( &$array = NULL, $field = TRUE , $default_value = FALSE, $prefix = '', $sufix = '' )   
    {
        $return_value = '';
        // if there is no variable
        if( $array == NULL )
            $return_value = $default_value;
        // if is array
        else if( is_array( $array ) )
        {
            if( array_key_exists( $field, $array ) )
            {
                $return_value = $array[ $field ];
            }
            else
            {
                $return_value = $default_value;
            }
        }
           // if is object
        else if( is_object( $array ) )
        {
            if( property_exists( $array, $field ) )
            {
                $return_value = $array->$field;
            }
            else
            {
                $return_value = $default_value;
            }
        }
        // if is boolean
        else if( is_bool( $array ) )
        {
            // if is true return the selected value
            if( $array )
                $return_value = $field;
            // else return the default value
            else
                $return_value = $default_value;
        }
        // if it is just a variable, than check if == ''
        else if( !empty( $array ) )
            $return_value = $field;
        else
            $return_value = $default_value;

        // return the value
        return $prefix . $return_value . $sufix;
    }
    
    function show_tooltip( $tooltip_message = '', $return_data = false )
    {
        $CI                         = &get_instance();
        $data                         = array();
        $data['tooltip_message']     = $tooltip_message;
        
        if( $return_data )
        {
             return $CI->load->view( 'admin/show_tooltip', $data, true );
		}
        else        
        {
            $CI->load->view( 'admin/show_tooltip', $data );
		}
    }
    
    /** 
    * @method highlight words 
    * @param string $string 
    * @param array $words 
    * 
    * @return string 
    * 
    */
    function highlightWords($string, $words)
    {
        $text = $string;
        
        if(count($words) > 0)
        {
            /* loop of the array of words */
            foreach ($words as $word)
            {
                    /* quote the text for regex */
                    $word = preg_quote($word);
                    /*highlight the words */
                    $text = preg_replace("|($word)|Ui" , "<span class=\"highlight_word\">$1</span>" , $text );

            }
        }
        
        /* return the text */
        return $text;
        
    }
    
   
    /** 
    * @method highlight words2 
    * 
    * @param string $string    
    * @param array $words 
    * 
    * @return string 
    * 
    */
    function highlightWords2($string, $words)
    {
        foreach ( $words as $word )
        {
            $string = str_ireplace($word, '<span class="highlight_word">'.$word.'</span>', $string);
        }
        /*** return the highlighted string ***/
        return $string;
    }            
    
    function shorten_text($text='', $numWords=20, $moreLink='')
    {
        $text = strip_tags($text);
        $aWords = explode(' ', $text);
        if ( sizeof($aWords) <= $numWords ) {
            return $text . ' ' . $moreLink;
        } else {
            $aText = array();
            for ($i=0; $i<$numWords; $i++)
            {
                $aText[] = $aWords[$i];
            }
            return implode(' ', $aText) . ' ... ' . $moreLink;
        }
    }
    
    /***
    * Counts the number of documents there are in a document resultset that 
    * have an extension contained in the doctypes array.
    * 
    * @param dbresult $document_files The CI database resultset.
    * @param array $doctypes An array containing document types that we want to find, e.g. pdf, doc, jpg etc
    */
    function num_docs_of_type($document_files, $doctypes)
    {
        $count = 0; // Counts how many documents match the specified doctypes
        
        // Loop through all documents
        foreach ($document_files->result() AS $document_file)
        {
            // Extract the extension from the document path
            $path = $document_file->document_path;
            $dotpos = strrpos($path, ".");
            
            if($dotpos > 0)
            {
                $ext = strtolower(substr($path, $dotpos + 1));
                
                // Is the extension in the doctypes array?  If so, we have found a match.
                if(in_array($ext, $doctypes))
                {
                    $count++;   
                }
            }
        } 
        
        return $count;       
    }
    
    /***
    * Prepares and returns an array used for sending ajax messages.
    */
    function get_return_array()
    {
        return array("status" => "ERROR", "message" => "An unspecified error occured");        
    }
    
    /**
    * Echos the passed array JSON encoded and exists
    * @param array $array
    */
    function send($array)
    {
        echo json_encode($array);
        exit();
    }
    
    function ifEmptyNull($s)
    {
        if($s != "")
        {
            return $s;
        }
        else
        {
            return null;
        }    
    }
    
    /***
    * Applies a where filter to the database object
    * 
    * @param array $filter_array The filters array
    * @param string $filter_name The name of the filter to apply
    */
    function where($filter_array, $filter_name, $alias = "")
    {
        if(!array_key_exists($filter_name, $filter_array)) return;
        
        $ci = &get_instance();  
        $field_name = $filter_name;   
        
        // If there is an alias defined, apply it to the field name.
        if($alias != "")
        {
            $field_name = $alias . "." . $field_name;
        }
        
        $ci->db->where($field_name, $filter_array[$filter_name]); 
    }
    
    function echoifobj($object, $field)
    {
        if((is_object($object)) && (property_exists($object, $field)))
        {        
            echo $object->$field;    
        }
    }
    
    function checkedif($object, $field, $match)
    {
        if((is_object($object)) && (property_exists($object, $field)) && ($object->$field == $match))
        {        
            echo 'checked="checked"';   
        }
    }    
    
    function format_article_tags($tags)
    {
        if($tags == "") return "";
        
        $tags = explode(",", $tags);
        $result = "";
        
        foreach($tags as $tag)
        {
            $tag = trim($tag);
            
            if($tag != "")
            {
                if($result != "") $result .= ", ";
                $result .= $tag;
            }    
        }
        
        return $result;
    }
    
    /***
    * Tests if an array is associative or not
    * 
    * @param array $arr
    * Returns true if the array is associative, false if not.
    */
    function is_assoc($arr)
    {
        return array_keys($arr) !== range(0, count($arr) - 1);
    }
    
    /***
    * Returns true if the person viewing the website is a developer, false if not.
    */
    function is_developer()
    {
        // Developers - add your IP addresses into the array to include yourself
        $developer_ips = array("203.217.28.219","127.0.0.1");
        $ci = &get_instance();
        
        return (in_array($ci->input->ip_address(), $developer_ips));   
    }
    
    function addhttp($url) {
	    if (!preg_match("@^[hf]tt?ps?://@", $url)) {
	        $url = "http://" . $url;
	    }
	    return $url;
	}
        
    function has_permission($user, $logged_in_user_id)
    {
        if((!$user) || (($user->created_by_user_id != $logged_in_user_id) && ($user->owner_id != $logged_in_user_id) 
            && ($user->owner_created_by_user_id != $logged_in_user_id) && ($user->advisor_id != $logged_in_user_id)))
        {
            return false;   
        }        
        
        return true;
    }
    
    function format_login_days($days)
    {           
        if(($days == "") || ($days > 700)) return "NA";
        if($days == 1) return "1 day";
        return $days . " days";
    }
	
	  function get_days($date)
    {           
        $now = time(); // or your date as well
     $your_date = strtotime($date);
     $datediff = $now - $your_date;
     return floor($datediff/(60*60*24));
	 
    }