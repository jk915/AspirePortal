<div id="testimonial">
    <?php if($testimonials) :?>
    <div class="slides_container">
    <?php foreach($testimonials->result() AS $testimonial) :?>
        <div>
            <blockquote>"<?php echo shorten_text($testimonial->quote, 25);?>"</blockquote>
            <cite>- <?php echo $testimonial->author?>, <?php echo $testimonial->company?></cite>
        </div>
    <?php endforeach;?>
    </div>
    <?php endif;?>
    <div class="clear"></div>
</div>