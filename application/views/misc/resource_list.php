<ul class="itemListing">
<?php
	if($articles)
	{
		foreach($articles->result() as $resource)
		{
			$url = base_url() . $resource->article_code;
			
			// Load the icon image for this article
			$docs = $this->document_model->get_list("article_document", $resource->article_id);
			$links = "";
			
			if($docs)
			{
				foreach($docs->result() as $doc)
				{
					$download_text = "Download PDF";					
					$doc = $docs->row();
					$download_url = base_url() . "files/" . $doc->document_path;
					
					if($doc->document_name != "")
					{
						$download_text = $doc->document_name;	
					}
					
					if($links != "")
					{
						$links .= " <span class=\"linkdivider\">|</span> ";
					}
					
					$links .= '<a href="' . $download_url . '">Download ' . $download_text . ' &raquo;</a>';
				}
			}
			
			$desc = strip_tags($this->utilities->replaceTags($this, $resource->content));
			?>
    <li>
        <h3><?php echo $resource->article_title; ?></h3>
        <p><?php 
        	echo $desc; 
        	
        	if($links != "")
        	{
				echo ' ' . $links;
        	}
        ?>
    </li>			
			<?php
		}
	}
?>
</ul>

<?php if($total_pages > 1) : ?>
<div class="pagination">
    <?php if($current_page > 1) : ?>
    <a class="btn" href="<?php echo base_url() . 'resources/' . ($current_page - 1); ?>">&laquo; Prev</a>
    <?php endif; ?>
    <ul>
    <?php
    for($page = 1; $page <= $total_pages; $page++)
    {
        $url = base_url() . "resources/$page";
		?>
		<li><a <?php if($page == $current_page) echo 'class="active"'; ?> href="<?php echo $url; ?>"><?php echo $page; ?></a></li>
		<?php
    }
	?>                                                                                             
    </ul>
    <?php if($current_page < $total_pages) : ?>
    <a class="btn" href="<?php echo base_url() . 'resources/' . ($current_page + 1); ?>">Next &raquo;</a>    
    <?php endif; ?>                
</div><!-- end pagination -->
<?php endif; ?>
