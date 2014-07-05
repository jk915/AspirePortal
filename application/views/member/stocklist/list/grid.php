<?php if($properties) : ?>
    <?php foreach($properties->result() as $property) : ?>
    <?php
        $detail_url = base_url() . "stocklist/detail/" . $property->property_id;
    ?>
    <li>
        <div class="property">
            <?php if($property->hero_image != ""  && file_exists("property/" . $property->property_id . "/images/" . $property->hero_image)) : ?>
            <?php
                $src = "property/" . $property->property_id . "/images/" . $property->hero_image;
                $resized = image_resize($src, 196, 130);
            ?>
            <img src="<?=$resized;?>" width="196" height="130" alt=" " />
            <?php else: ?>
            <img src="<?php echo base_url(); ?>images/member/home_default.jpg" width="196" height="130" alt=" " />
            <?php endif; ?>
            <div class="propertyDetails">
                <h2><?=$property->lot . ", " . $property->address;?></h2>
                <h3><?=$property->area_name . ", " . $property->state; ?></h3>
                <ul class="specs">
                    <li class="beds"><?=$property->bedrooms; ?></li>
                    <li class="baths"><?=$property->bathrooms; ?></li>
                    <li class="parking"><?=$property->garage; ?></li>
                </ul>
                <h4>$<?=number_format($property->total_price, 0, ".", ","); ?></h4> 
								
                <div class="additionalInfo">                                        
                    <p>
                    <em>NRAS:</em> <?=($property->nras) ? "Yes" : "No"; ?><br />
                    <em>SMSF:</em> <?=($property->smsf) ? "Yes" : "No"; ?><br />
					<?php
					if($property->titled == "1")
					{
					?>
                    <em>Titled:</em> <?=($property->titled) ? "Yes" : "No"; ?><br />
					<?php
					}
					else
					{
					?>
					<em>Titled:</em> <?=($property->titled) ? "Yes" : "No"; ?><br />
					<em>Estimated Date:</em><?php echo $property->estimated_date; ?><br/>
					<?php
					}
					?>
                    <em>House area:</em> <?=$property->house_area; ?>sqm<br/>
                    <em>Rent Yield:</em> <?=number_format($property->rent_yield, 2); ?>%
                    </p>
                <!--end additionalInfo--></div>  
            </div>
            <a class="overlay" href="<?=$detail_url; ?>"></a>
            <a class="viewMore" href="<?=$detail_url; ?>"></a>                            
        <!-- end property --></div>
    </li>        
    <?php endforeach; ?>
<?php endif; ?>