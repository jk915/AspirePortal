<?php if($media) : ?>
    <?php foreach($media->result() as $article) : ?>
    <?php
        $class = "download";
        $href = $article->article_id;
        $external = false;
    ?>
        <tr>
        	<td>
        		<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>
                    <?php echo $this->utilities->iso_to_ukdate($article->article_date); ?>
        		</a>
        	</td>
            <td style="text-align:center">
            	<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>
            		<?php echo $article->article_title; ?>
        		</a>
    		</td>
			<!-- By Ajay TasksEveryday -->
			<td style="text-align:center">
            	<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>
            		<?php echo $article->author; ?>
        		</a>
    		</td>
			<!-- END -->
        </tr>
    <?php endforeach; ?>
<?php endif; ?>