
<?php if($properties) : ?>
<tr>
    <th class="sortable" sort="p.address" style="text-align:center">Address</th>
	<th class="sortable" sort="p.Project" style="text-align:center">Project</th>
    <th class="sortable" sort="area.area_name" style="text-align:center">Area</th>
    <th class="sortable" sort="st.name" style="text-align:center">State</th>    
    <th class="sortable" sort="p.total_price" style="text-align:center">Price</th>
    <th class="sortable" sort="r1.name" style="text-align:center">Partner</th>
    <th class="sortable" sort="p.house_area" style="text-align:center">Purchaser</th>
    <th class="sortable" sort="p.land" style="text-align:center">Builder</th>
    <th class="sortable" sort="p.nras" style="text-align:center">Status</th>
    <th class="sortable" sort="p.nras" style="text-align:center">Tracker</th>
	
    
    <!--
    <th class="sortable" sort="p.status">Available</th>
    <th><img src="<?php echo base_url(); ?>images/member/icon-bedrooms-light.png" width="24" height="14" alt="bedrooms" /></th>
    <th><img src="<?php echo base_url(); ?>images/member/icon-bathrooms-light.png" width="21" height="19" alt="bathrooms" /></th>
    <th><img src="<?php echo base_url(); ?>images/member/icon-garage-light.png" width="25" height="21" alt="car parks" /></th>
    -->
</tr>
    
	<?php foreach($properties->result() as $property) : ?>
	
    <tr>
        <td><a href="<?php echo base_url() . "stocklist/detail/" . $property->property_id; ?>">Lot <?=$property->lot . ", " . $property->address;?></a></td>
		<td><a href="<?php echo base_url() . "projects/detail/" . $property->project_id; ?>"><?=$property->project_name; ?></a></td>                                
        <td><a href="<?php echo base_url() . "areas/detail/" . $property->area_id; ?>"><?=$property->area_name; ?></a></td>
        <td><?=$property->state_name; ?></td>        
                                     
        <td>$<?=number_format($property->total_price, 0, ".", ","); ?></td>                                                          
        <td><?php if($property->user_type_id == '5') { ?><a href="<?php echo base_url() . "staff/detail/" . $property->user_id; ?>"> <?php } ?> <?=$property->partner_full_name; ?></a></td>
        <td><a href="<?php echo base_url() . "leads/detail/" . $property->investor_id; ?>"><?=$property->purchaser_full_name; ?></a></td>
		<?php
					if($property->display_on_front_end != "0")
					{
					?>
                    <td><?=$property->builder_name; ?></td>
					<?php
					}
					else
					{
					?>
					<td>TBA</td>
					<?php
					}
					?>
		
        <td><?=$property->status; ?></td>
		<td> <a href="<?php echo base_url() . "construction/detail/" . $property->property_id; ?>"><img id="tracker" src="<?php echo base_url(); ?>images/member/tracker.png" alt="tracker" /></td>
        
    </tr>       
    <?php endforeach; ?>
<?php endif; ?>