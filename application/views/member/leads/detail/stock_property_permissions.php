	<p></p>
<?php if($logged_user_type == USER_TYPE_PARTNER): ?>
    <?php if($assign_properties): ?> 
	<table class="zebra" cellpadding="0" cellspacing="0">                                         
		<thead>
			<tr>
		        <th>This lead can currently access the following properties</th>
		    </tr>
		</thead>
	    <tbody>
            <?php foreach ($assign_properties->result() AS $index=>$assignPro) : ?>
	    	<tr <?php echo $index%2 ? 'class="alt"' : ''?>>
		        <td style="width:77%;"><a href="<?php echo site_url("stocklist/detail/" . $assignPro->property_id); ?>">Lot <?php echo $assignPro->lot . ', ' . $assignPro->address.' '.$assignPro->state_name;?></a></td>
		    </tr>
		    <?php endforeach; ?>
        </tbody>
     </table>
        
     <?php endif; ?>
<?php else: ?>
	
	<table class="zebra" cellpadding="0" cellspacing="0">                                         
		<thead>
			<tr>
		        <th colspan="2">This lead can currently view the following properties</th>
		    </tr>
		</thead>
	    <tbody>
    	<?php if ($assign_properties) : ?>
    		<?php foreach ($assign_properties->result() AS $index=>$assignPro) : ?>
	    	<tr <?php echo $index%2 ? 'class="alt"' : ''?>>
		        <td style="width:77%;">Lot <?php echo $assignPro->lot . ', ' . $assignPro->address.' '.$assignPro->state_name;?></td>
		        <td>
		        	<a href="javascript:;" rel="<?php echo $assignPro->id;?>" pid="<?php echo $assignPro->foreign_id?>" class="remove_property">Remove</a>
		        </td>
		    </tr>
		    <?php endforeach; ?>
	    <?php endif; ?>
	    </tbody>
	    
	    <tfoot>
	    	<tr>
	    		<td colspan="2">
	    			<label>Add another property:</label>
					<select id="property_id" name="property_id" style="width:75%;">
						<option value="" <?php echo $property_id == '' ? 'selected="selected"' : ''?>>Select Property</option>>
		            <? if($properties):?>
		            	<?php foreach ($properties->result() AS $property) : ?>
		            	<option value="<?php echo $property->property_id?>" <?php echo $property->property_id == $property_id ? 'selected="selected"' : ''?>>
		            		Lot <?php echo $property->lot . ', ' . $property->address.' '.$property->state_name;?>
	            		</option>
		                <?php endforeach; ?>
		            <? endif; ?>
					</select>
	    		</td>
	    	</tr>
	    </tfoot>
	</table>
    
<?php endif; ?>    