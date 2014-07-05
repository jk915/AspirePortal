<?php 
	// Output xml header
	header ("content-type: text/xml"); 
	echo '<?xml version="1.0" encoding="utf-8" ?>'; 
?>
<data>
	<video path="video/video.flv" />
	<nav>
<?php
	foreach($regions->result() as $region)
	{
		if($region->region_icon != "")
		{
			if($region->external_url != "")
			{
				$url = $region->external_url;
				if(!strstr($url, "http"))
					$url = "http://" . $url;
			}
			else
			{
				$url_id = $region->url_id;
				if(strstr($url_id, "/"))
					$url_id = substr($url_id, 1);
			
				$url = base_url() . $url_id . "/page/home";
			}
			
			$icon_path = base_url() . $region->region_icon; 
		?>
		<item name="<?php echo $region->region_name; ?>" icon="<?php echo $icon_path;?>" link="<?php echo $url;?>" />
		<?php	
		}
	}
?>
	</nav>
</data>