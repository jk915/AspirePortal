<?php if($areas) : ?>
    <?php foreach($areas->result() as $area) : ?>
    <?php
        $detail_url = base_url() . "areas/detail/" . $area->area_id;
    ?>
    <li>
        <div class="property">
            <?php if($area->area_hero_image != ""  && file_exists(ABSOLUTE_PATH . $area->area_hero_image)) : ?>
            <?php
                $src = $area->area_hero_image;
                $resized = image_resize($src, 196, 130);
            ?>
            <img src="<?=$resized;?>" width="196" height="130" alt=" " />
            <?php else: ?>
            <img src="<?php echo base_url(); ?>images/member/home_default.jpg" width="196" height="130" alt=" " />
            <?php endif; ?>
            <div class="propertyDetails">
                
                <h3><?=$area->area_name; ?></h3>
                <h3><?=$area->state_name; ?> </h3>
                <h4>Median Price $ <?=$area->median_house_price; ?></h4>  
            </div>
            <a class="overlay" href="<?=$detail_url; ?>"></a>
            <a class="viewMore" href="<?=$detail_url; ?>"></a>                            
        <!-- end property --></div>
    </li>        
    <?php endforeach; ?>
<?php endif; ?>