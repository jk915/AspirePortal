<h1><?php echo $article->article_title; ?></h1>
<dl>
    <?php if($article->article_date != "") : ?>
    <dt>Publication Date</dt>
    <dd><?php echo $this->utilities->iso_to_ukdate($article->article_date); ?></dd>
    <?php endif; ?>
    <?php if($article->last_modification_dtm != "") : ?>
    <dt>Last Modified</dt>
    <dd><?php echo date('d/m/Y', strtotime($article->last_modification_dtm)); ?></dd>
    <?php endif; ?>
    <?php if($article->tags != "") : ?>
    <dt>Tags</dt>
    <dd><?php echo substr($article->tags, 1, strlen($article->tags) - 2); ?></dd>
    <?php endif; ?>
    <?php if($article->www != "") : ?>
    <dt>Web Link</dt>
    <dd><a href="<?php echo addhttp($article->www); ?>" target="_blank">Click here to view</a></dd>
    <?php endif; ?>
    <?php if($article->author != "") : ?>
    <dt>Author</dt>
    <dd><?php echo $article->author; ?></dd>
    <?php endif; ?>
    <?php if($article->source != "") : ?>
    <dt>Source</dt>
    <dd><?php echo $article->source; ?></dd>
    <?php endif; ?>  
    <?php if($article->category_name != "") : ?>
    <dt>Document Type</dt>
    <dd><?php echo $article->category_name; ?></dd>
    <?php endif; ?>          
</dl>

<?php if($article->link_www != "") : ?>
<a class="btn" href="<?php echo $article->link_www; ?>" target="_blank">Download / View</a>
<?php endif; ?>

<?php if($article->video_code != "") : ?>    
<div id="modal_video">
<?php echo html_entity_decode($article->video_code); ?>
</div>
<?php else : ?>
	<?php if ($article->hero_image != "") : ?>
<div id="modal_video">
	<img src="<?php echo site_url($article->hero_image.'_detail.jpg'); ?>" width="400" height="300"/>
</div>	
	<?php endif; ?>
<?php endif; ?>

<?php if($article->comments != "") : ?>
<div class="comments"><p><?php echo nl2br($article->comments); ?></p></div>
<?php endif; ?>


