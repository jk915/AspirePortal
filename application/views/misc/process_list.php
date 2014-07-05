</p>
<ul class="processList">
<?php
	if($articles)
	{
		foreach($articles->result() as $process)
		{
			$url = base_url() . $process->article_code;
			
			// Load the icon image for this article
			$icon_url = "";
			
			$docs = $this->document_model->get_list("article_image", $process->article_id);
			if($docs)
			{
				$icon = $docs->row();
				$icon_url = base_url() . $icon->document_path;
			}
			?>
    <li>
        <img src="<?php echo $icon_url; ?>" width="106" height="74" alt="<?php echo $process->article_title; ?>" />
        <div class="description">
            <h2><?php echo $process->article_title; ?></h2>
            <?php echo $this->utilities->replaceTags($this, $process->short_description); ?>
            <p><a href="<?php echo $url; ?>">Read More&hellip;</a></p>
        </div>
    </li>			
			<?php
		}
	}
?>
</ul> 
<p>