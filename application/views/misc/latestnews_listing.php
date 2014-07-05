<?php if($latestnews) :?>
<ul class="newsblock">
    <?php foreach ($latestnews->result() AS $latestnew) :?>
	<li>
    	<div><a href="<?php echo site_url("news/".$latestnew->article_code)?>"><strong class="lato"><?php echo $latestnew->article_title." ".date("d M'y",strtotime($latestnew->article_date))?></strong></a></div>
        <p class="latolight"><?php echo shorten_text($latestnew->short_description, 14, '<a href="'.site_url("news/".$latestnew->article_code).'">Keep Reading &rsaquo;</a>');?></p>
    </li>
	<?php endforeach;?>
</ul>
<a href="<?php echo site_url("latest-news")?>"><img src="images/btn-readall.png" alt="Read All" /></a>
<?php endif;?>