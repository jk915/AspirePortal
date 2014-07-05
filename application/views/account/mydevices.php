    <h2>My Devices</h2>
    
    <?php if((isset($user_devices)) && ($user_devices)) : ?>
    
    <table id="tblMyDevices">
    	<tr>
    		<th>Product</th>
    		<th>No. Credits</th>
    		<th>Licenses</th>
    	</tr>
    	<?php foreach($user_devices->result() as $row) : ?>
    	<tr>
    		<td><?php echo $row->product_name; ?></td>
    		<td><?php echo $row->num_credits; ?> 
    			<?php if($row->num_credits > 0) : ?>
    			<a class="generate-licenses" href="<?php echo $row->product_id; ?>">Generate License<?php if($row->num_credits > 0) echo "s"; ?></a>
    			<?php endif; ?>
    		</td>
    		<td><?php echo $row->num_licenses; ?>
    			<?php if($row->num_licenses > 0) :?>
    			<a class="view-licenses" href="<?php echo $row->product_id; ?>">View License<?php if($row->num_licenses > 0) echo "s"; ?></a>
    			<?php endif; ?>
    		</td>
    	</tr>
    	<?php endforeach; ?>
    </table>
    
    <?php else: ?>
    <p>You have not yet purchased any products that require licenses.</p>
    <?php endif; ?>