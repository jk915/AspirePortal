<div id="newsScroller">
    <div class="customScrollBox">
        <div class="container">
            <div class="content">
			<?php 
				if($articles)
				{
					$x = 0;
					$max_len = 120;
					
					foreach($articles->result() as $row)
					{
						$date = date("d M y", strtotime($row->article_date));
						echo '<h3>' . $row->article_title . '</h3><h6>' . $date . '</h6>';
   						
   						$desc = strip_tags($row->short_description);
   						
   						if(strlen($desc) > $max_len)
   						{
							$desc = substr($desc, 0, $max_len) . "...";
   						}
   						
   						echo '<p>' . $desc . ' <a href="' . base_url() . $row->article_code . '">Read More &raquo;</a></p>';

   						$x++;
					}
				}
			?>            
            </div>
        </div>
        <div class="dragger_container">
            <div class="dragger"></div>
        </div>
    </div>
</div>                    
<a class="btn" href="<?php echo base_url(); ?>news" style="margin-top: 15px;">View All &raquo;</a>