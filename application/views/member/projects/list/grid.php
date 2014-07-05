<?php if($projects) : ?>
    <?php foreach($projects->result() as $project) : ?>
    <?php
        $detail_url = base_url() . "projects/detail/" . $project->project_id;
    ?>
    <li>
        <div class="property">
            <?php if($project->logo != ""  && file_exists(ABSOLUTE_PATH . $project->logo)) : ?>
            <?php
                $src = $project->logo;
                $resized = image_resize($src, 196, 130);
            ?>
            <img src="<?=$resized;?>" width="196" height="130" alt=" " />
            <?php else: ?>
            <img src="<?php echo base_url(); ?>images/member/home_default.jpg" width="196" height="130" alt=" " />
            <?php endif; ?>
            <div class="propertyDetails">
                <h2><?=$project->project_name; ?></h2>
                <h3><?=$project->area_name; ?></h3>
                <h3><?=$project->state; ?></h3>
                <h4>From $<?=number_format($project->prices_from, 0, ".", ","); ?></h4>  
            </div>
            <a class="overlay" href="<?=$detail_url; ?>"></a>
            <a class="viewMore" href="<?=$detail_url; ?>"></a>                            
        <!-- end property --></div>
    </li>        
    <?php endforeach; ?>
<?php endif; ?>