<div class="sitemapCategory">
	<?php /* <h3><?php echo $category->name; ?></h3> */ ?>
	
	<?php if($articles) : ?>
	<ul>
	<?php foreach($articles->result() as $a) : ?>
		<?php if((!$a->hide_from_sitemap) && (!$a->agent_login)) : ?>
		<li><a href="<?php echo base_url() . $a->article_code; ?>"><?php echo $a->article_title; ?></a></li>
		<?php endif; ?>
	<?php endforeach; ?>
	</ul>
	<?php endif; ?>
</div>                    