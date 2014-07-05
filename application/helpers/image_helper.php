<?php
    /***
    * Resizes a source image and returns the path to the resized image.
    * The resizing will only be done if the target doesn't already exist,
    * thus it can be used on the frontend to resize images as required.
    * 
    * @param string $source Path to the source image (relative to site root, not absolute)
    * @param int $width Desired width of the image
    * @param int $height Desired height of the image
    * @returns The path to the resized image.
    */
    function image_resize($source, $width, $height)
    {
        $ci = &get_instance();
        
        $extension = "_" . $width . "x" . $height . ".jpg";
        $source_abs = ABSOLUTE_PATH . $source;
        $target_abs = $source_abs . $extension; 
        $target_www = base_url() . $source . $extension;
        
        // If the target file already exists, there's nothing to do.
        if(file_exists($target_abs))
        {
            //unlink($target_abs);
            return $target_www;    
        }
        
        $config['image_library'] = 'ImageMagick';
        $config['library_path'] = MAGICKPATH;
        $config['source_image'] = $source_abs;
        $config['new_image'] = $target_abs;
        $config['create_thumb'] = FALSE;
        $config['maintain_ratio'] = TRUE;
        
        if($width > 0)
        {
            $config['width'] = $width;
        }
        
        if($height > 0)
        {
            $config['height'] = $height;  
        }
        
        $ci->load->library('image_lib');
        $ci->image_lib->initialize($config); 
        $result = $ci->image_lib->resize(); 
        $ci->image_lib->clear();   
        
        if(!$result) return false;
        
        return $target_www;
    }
