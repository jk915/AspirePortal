<?php if($region_states) : ?>
    <?php foreach($region_states->result() as $region_state) : ?>
    <tr>
        <td><a href="<?php echo base_url() . "states/detail/" . $region_state->state_id; ?>"><?=$region_state->state_name;?></a></td>                                
        
        <td><?=$region_state->state_name; ?></td>                                      
        <td>$ <?=$region_state->median_house_price; ?></td>                                                          
        
    </tr>    
    <?php endforeach; ?>
<?php endif; ?>