<?php if($regions) : ?>
    <?php foreach($regions->result() as $region) : ?>
    <tr>
        <td><a href="<?php echo base_url() . "regions/detail/" . $region->region_id; ?>"><?=$region->region_name;?></a></td>                                
        
        <td><?=$region->region_name; ?></td>                                      
        <td>$ <?=$region->median_house_price; ?></td>                                                          
        
    </tr>    
    <?php endforeach; ?>
<?php endif; ?>