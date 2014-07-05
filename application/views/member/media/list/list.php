<?php if($media) : ?>
    <?php foreach($media->result() as $article) : ?>
    <?php
        $class = "download";
        $href = $article->article_id;
        $external = false;
        
        /*
        if(strlen($article->www) > 5)
        {
            $external = true;
            $class = "external";
            
            $href = $article->www;
            if(!stristr($href, "http"))
            {
                $href = "http://" . $href;
            }
        }
        else if($article->video_code != "")
        {
            $class = "video";        
        }
        */
    ?>
        <tr>
        	<td>
        		<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>
        		
        		<?php
        			$article_icon = 'aspire_logo.jpg';
        			switch ($article->article_icon) {
        				case 'pdf_logo':
        					$article_icon = 'pdf_logo.png';
    					break;
    					
    					case 'youtube_logo':
        					$article_icon = 'youtube_logo.png';
    					break;
    					
    					case 'www_logo':
        					$article_icon = 'www_logo.png';
    					break;
    					
    					case 'word_logo':
        					$article_icon = 'word_logo.png';
    					break;
    					
    					case 'excel_logo':
        					$article_icon = 'excel_logo.png';
    					break;
    					
    					case 'aspire_logo':
        					$article_icon = 'aspire_logo.jpg';
    					break;
    					
    					case 'powerpoint_logo':
        					$article_icon = 'powerpoint_logo.png';
    					break;
        			}
        		?>
        			<?php if (!empty($article->article_icon)) : ?>
        			<img src="images/member/<?php echo $article_icon;?>" style="width:32px;"/>
        			<?php endif; ?>
        		</a>
        	</td>
            <td>
            	<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>
            		<?php echo $article->article_title; ?>
        		</a>
    		</td>
            <td><?php echo $article->source; ?></td>            
            <td><?php echo $article->category_name; ?></td>
            <td><?php echo $this->utilities->iso_to_ukdate($article->article_date); ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>