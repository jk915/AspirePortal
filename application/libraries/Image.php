<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed');

class Image
{
	/**
	* @desc The get_block method returns a custom block with the passed id.
	* Note, block id constants are defined in application/config/constants.php
	*/
    
	public static function buildImageTag($image_path, $alt_text, $class="", $align="", $height=0, $width=0, $vspace=0, $hspace=0)
	{
		if($image_path == "")
			return "";
			
		$base_url = base_url();
			
		// If the image path contains the full website url, remove it.
		if(stristr($image_path, $base_url))
		{
			$image_path = str_replace($base_url, "", $image_path);
		}
		
		$image_path = ABSOLUTE_PATH . $image_path;
			
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
		
		if(stristr($image_path, ABSOLUTE_PATH))
		{
			$image_path = str_replace(ABSOLUTE_PATH, "", $image_path);
		}
		
		if(!stristr($image_path, base_url()))
			$image_path = base_url() . $image_path;
		
		$return = "<img alt=\"$alt_text\" title=\"$alt_text\" width=\"$width\" height=\"$height\" src=\"$image_path\"";
		
		if($align != "")
			$return .= " align=\"$align\"";
			
		if($class != "")
			$return .= " class=\"$class\"";	
			
		if($vspace > 0)
			$return .= " vspace=\"$vspace\"";
			
		if($hspace > 0)
			$return .= " hspace=\"$hspace\"";					
		
		$return .= " />";
		
		return $return;
	}
	
	public static function create_thumbnail($input, $output, &$error_message, $thumb_width = 100, $thumb_height="")
	{
		// If blank variables are passed, return false immediately.
		if(($input == "") || ($output == ""))
		{
			$error_message = "Either the input or output file is blank";
			return false;
		}
			
		// Ensure input file exists
		if(!file_exists($input))
		{
			$error_message = "Input file does not exist";
			return false;
		}  
		
		// Ensure input file type is valid, and if it is, create a GD image object
		if(stristr($input, ".jpg"))
		{
			// This is a JPEG image
			$src_img=imagecreatefromjpeg($input);
		}	
		else if(stristr($input, ".png"))
		{
			// This is a PNG file
			$src_img=imagecreatefrompng($input);
		} 
        else if(stristr($input, ".gif"))
        {
            // This is a GIF file
            $src_img=imagecreatefromgif($input);
        }
		else
		{               
			$error_message = "Invalid input file.  It must be either a JPEG or PNG ";
			return false;	
		}
		
		// Get x and y dimensions of the big image.
		$old_x = imageSX($src_img);
		$old_y = imageSY($src_img);
      
      	// Calculate what ratio the proposed thumbnail width is in proportion to the big image.
		$ratio = $thumb_width / $old_x;
		
		// Calculate appropriate thumbnail height
		if ($thumb_height == "") {
            $thumb_height = round($old_y * $ratio, 0);	
		}
		
		// Create a new image to hold the thumbnail
		$dst_img = ImageCreateTrueColor($thumb_width, $thumb_height);
		
		// Resample the original image into the thumbnail
		imagecopyresampled($dst_img, $src_img, 0, 0, 0, 0, $thumb_width, $thumb_height, $old_x, $old_y); 

		// Always save thumbnails as jpegs
		if(!imagejpeg($dst_img, $output))
		{
			$error_message = "Couldn't create ouput thumbnail file: $output";
			return false;	
		}
		
		// All done.
		return true;   
	}
		
	static public function create_square_thumbnail($source, $dest, $thumb_size=84, &$error_message = "") 
	{
		// If blank variables are passed, return false immediately.
		if(($source == "") || ($dest == ""))
		{
			$error_message = "Either the input or output file is blank";
			return false;
		}
			
		// Ensure input file exists
		if(!file_exists($source))
		{
			$error_message = "Input file does not exist";
			return false;
		}		
		
		$size = getimagesize($source);
		$width = $size[0];
		$height = $size[1];

		if($width > $height) 
		{
			$x = ceil(($width - $height) / 2 );
			$width = $height;
			$y = 0;
		}
		else if($height > $width) 
		{
			$y = ceil(($height - $width) / 2);
			$height = $width; 
			$x = 0;
		}

		$new_im = ImageCreatetruecolor($thumb_size,$thumb_size);
		$im = imagecreatefromjpeg($source);
		imagecopyresampled($new_im,$im,0,0,$x,$y,$thumb_size,$thumb_size,$width,$height);
		imagejpeg($new_im,$dest,100);

		return true;
	}
	
	static public function resize_magick($source, $dest, $width=0, $height=0, $crop = false, &$error_message = "") 
	{
		// If blank variables are passed, return false immediately.
		if(($source == "") || ($dest == ""))
		{
			$error_message = "Either the input or output file is blank";
			return false;
		}
			
		// Ensure input file exists
		if(!file_exists($source))
		{
			$error_message = "Input file does not exist";
			return false;
		}
		
		$array_image = getimagesize($source);
		$img_width = $array_image[0];
		$img_height = $array_image[1];
		
		$dimensions = "";
		if($width > 0)
			$dimensions = $width;
			
		if(($height > 0) && (!$crop))
		{
			if($dimensions == "")
				$dimensions = "x" . $height;
			else
				$dimensions .= "x" . $height . "!";
		}
		else if($width > $img_width)
			$dimensions .= "!";
		
		if($width > $img_width)
			$dimensions = $img_width;

		
		// Formulate the imagemagic command
		$command = MAGICKPATH . "convert \"$source\" -resize " . $dimensions . " ";
		if($width < 300)
			$command .= "-sharpen 1.2 ";
			
		$command .= "\"$dest\"";
		
		// Execute
		shell_exec($command);
		
		// Check if we need to crop the image also.
		if(($height > 0) && ($crop))
		{
			// Get the image size of the destination image that's been created so far.
			$array_image = getimagesize($dest, $imageinfo);
			$img_width = $array_image[0];
			$img_height = $array_image[1];			
			
			$offset = floor(($img_height / 2) - ($height / 2));
			$dimensions = $width . "!x" . $height . "!+0+$offset";	   
			$command = MAGICKPATH . "convert \"$dest\" -crop " . $dimensions . " \"$dest\"";	

			shell_exec($command);		
		}
		
		return true;
	}
	
	function create_image_set($category, $images, $suffixes, $file_path)
	{         
		$CI = & get_instance();                                    
		
		if((!$category) || (!is_array($images)) || (!is_array($suffixes)) || ($file_path == ""))
		{
			$CI->add_to_debug("Utilities::create_image_set - Invalid category, images array or suffixes array");
			return false;
		}
		
		if(!file_exists($file_path))
		{
			$CI->add_to_debug("Utilities::create_image_set - Source file does not exist.");
			return false;			
		}
		
		// Figure out how many images we need to create.
  		$num_images = count($images);
   		
   		for($i = 0; $i < $num_images; $i++)
   		{
   			// Get the current iamge and suffix.
   			$image = $images[$i];
   			$suffix = $suffixes[$i];
   			
   			// Determine the image height and width.
			$width_field = $image . "_width";		
			$height_field = $image . "_height";
			
			$width = $category->$width_field;
			$height = $category->$height_field;
			
			// Make sure at least one of the height or width are greater than 0.
			if(($width == 0) && ($height == 0))
			{
				$CI->utilities->add_to_debug("Contentmanager - create hero image set error - $width_field AND $height_field are both 0");
				return false;
			}
			
			// We need to crop the image if both the height and width are set.
			$crop = (($width != 0) && ($height != 0));
			
			$resized_path = $file_path . $suffix . ".jpg";
			
			$this->resize_magick($file_path, $resized_path, $width, $height, $crop, $error_message);  
			chmod($resized_path, 0666);
			            
			if($error_message != "")
			{
				$CI->utilities->add_to_debug("Contentmanager - create hero image set error $error_message");
				return false;
			}
			
			$CI->utilities->add_to_debug("Contentmanager - created $resized_path");
			
   		}		
		
		return true;			
	}    
	
	// Deletes all of the images for an article, given the original image path.
	function remove_image_set($suffixes, $file_path)
	{
		$CI = & get_instance();
		
		if((!is_array($suffixes)) || ($file_path == ""))
		{
			$CI->utilities->add_to_debug("Utilities::create_image_set - suffixes array or file path");
			return false;
		}	
		
		// Delete all the suffix images
		foreach($suffixes as $suffix)
		{
			//$CI->utilities->add_to_debug("REMOVING: " . $file_path . $suffix . ".jpg");
			@unlink($file_path . $suffix . ".jpg");
		}	
		
		// Delete the source file too.
		//$CI->utilities->add_to_debug("REMOVING: " . $file_path);
		@unlink($file_path);

		return true;
	}
    
    function set_image_type( $type, &$image_details, $image_type = "")			
    {
        
         if($type == "front_page")
         {
            $image_details["image_type"] = "medium_frontpage";                        
         }
         else
         {
            if($type == "collective")    
                $image_details["image_type"] = "medium_collective";                        
            else //highlights
                $image_details["image_type"] = ($image_type != "") ? $image_type : "small_collective";                        
         }

    }
}
