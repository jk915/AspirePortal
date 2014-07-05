<?php 
	if($articles)
	{
		$x = 0;
		$max_len = 220;
		
		foreach($articles->result() as $row)
		{
			$desc = strip_tags($row->short_description);
			$detail_url = base_url() . $row->article_code;
			?>
            <div class="article">
                <h1><?php echo $row->article_title; ?></h1>
                <h6><?php echo date("d M Y", strtotime($row->article_date)); ?></h6>
                <p>
                	<?php if($row->hero_image != "") : ?>
                	<a href="<?php echo $detail_url; ?>"><img src="<?php echo base_url() . $row->hero_image; ?>_thumb.jpg" border="0" width="145" alt="<?php echo $row->article_title; ?>" /></a>
                	<?php endif; ?>
                	<?php echo $desc; ?>
                </p>
                <p><a href="<?php echo $detail_url; ?>">Read More</a></p>                
            </div><!-- end article -->
			<?php
		}
		
		// Do we need to render pagination
		if($num_pages > 1)
		{
			echo '<ul id="pagination">';
			
			for($p = 1; $p <= $num_pages; $p++)
			{
				$class = ($p == $page_no) ? "active" : "na";
				echo '<li class="' . $class . '">';
				echo '<a href="' . base_url() . "latest-news/" . $p . '">' . $p . "</a>";
				echo '</li>';
			}
			
			echo '</ul>';
		}
	}
?>
