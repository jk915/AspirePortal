<?php if($areas) : ?>
    <?php foreach($areas->result() as $area) : ?>
    <tr>
        <td><a href="<?php echo base_url() . "area/detail/" . $area->area_id; ?>"><?=$area->area_name;?></a></td>                                
        
        <td><?=$area->state_name; ?></td>                                      
        <td>$ <?=$area->median_house_price; ?></td>                                                          
        
    </tr>    
    <?php endforeach; ?>
<?php endif; ?>