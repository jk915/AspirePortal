<?php if($properties) : ?>
    <?php foreach($properties->result() as $property) : ?>
    <tr>
        <td><a href="<?php echo base_url() . "stocklist/detail/" . $property->property_id; ?>">Lot <?=$property->lot . ", " . $property->address;?></a></td>                                
        <td><?=$property->area_name; ?></td>
        <td><?=$property->state_name; ?></td>        
        <td><?=$property->project_name; ?></td>                                
        <td>$<?=number_format($property->total_price, 0, ".", ","); ?></td>                                                          
        <td><?=$property->property_type; ?></td>
        <td><?=$property->house_area; ?></td>
        <td><?=$property->land; ?></td>
        <td><?=number_format($property->rent_yield, 2); ?>%</td>
        <td><?=($property->nras) ? "Yes" : "No"; ?></td>
        <td><?=($property->smsf) ? "Yes" : "No"; ?></td>
    </tr>    
    <?php endforeach; ?>
<?php endif; ?>