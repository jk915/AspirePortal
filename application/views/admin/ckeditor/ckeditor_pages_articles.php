    <?php
        $chars = array("/", "~", "|");
        $file_chars = array("~", "|");
        $this->utilities->get_file_tree($arr_all_files, "");
        $all_files = "";

        foreach($arr_all_files as $file)
        {
        	$path = str_replace(ABSOLUTE_PATH . "files/", "", $file);
        	$all_files .= str_replace($file_chars, "", $path) . "~" . str_replace($file_chars, "", $path) . "|";     
        }
        
        $sql = "SELECT a.article_id, c.name as category_name, a.article_title, a.article_code " .
        	"FROM nc_articles a " .
        	"INNER JOIN nc_article_categories c ON a.category_id = c.category_id AND c.enabled = 1 " .
        	"WHERE a.enabled = 1 " .
        	"ORDER BY c.seq_no, a.article_order";
        	
        $obj_all_articles = $this->db->query($sql);
        $all_articles = "";
        
        if($obj_all_articles)
        {
            foreach($obj_all_articles->result() as $row)
            {
                $article_title_link = $this->tools_model->alphanumeric_link($row->article_title);                                
                $all_articles .= str_replace($chars,"",$row->category_name . " - " . $row->article_title)."~". $row->article_code . "|";
            }
        }
        
        $flash_files = $this->utilities->get_files("files/flash");
        
         // include the PEAR package
        require APPPATH."/libraries/File_SWF.php";
        $all_flashes = array();
        
        if($flash_files)
        {
	        foreach($flash_files as $row)
	        {
	            $flash = new File_SWF( FCPATH."files/flash/".$row);
	            
	            if($flash->is_valid())
	            {
	                $stat = $flash->stat();
	                // this give all the info
	                // and also..
	                $fps = $flash->getFrameRate();
	                $size = $flash->getMovieSize();
	            
	                 $ass_arr_flash = array(
	                    "file" => $row,
	                    "width" => $size["width"],
	                    "height" => $size["height"]
	                 );
	                 $all_flashes[] = $ass_arr_flash;
	                //$all_flashes .= $row."/".$size["width"]."/".$size["height"]."~";                
	            } 
	        }
		}
    ?>
    
    <input type="hidden" id="arr_files" value="<?php echo (isset($all_files)) ? $all_files : "";?>" />
    <input type="hidden" id="arr_articles" value="<?php echo (isset($all_articles)) ? $all_articles : "";?>" />
    <input type="hidden" id="arr_flashes" value='<?php echo (isset($all_flashes)) ? serialize($all_flashes) : "";?>' />