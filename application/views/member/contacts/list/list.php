
<?php 

if($builders) : ?>
	
	<?php foreach($builders->result() as $builder) : ?>
        <tr>
            <td><a href="<?php echo base_url() . "contacts/detail/" . $builder->contacts_id; ?>"><?php echo $builder->first_name.' '.$builder->last_name; ?></a></td>
            <td><?php echo $builder->company_name; ?></td>
			<td><?php echo $builder->state_name; ?></td>
            <td><?php echo $builder->contact_type; ?></td>
            <td><?php echo $builder->mobile; ?></td>
			<!--<td><?php //echo ($user->notes_last_created != '')? date('d/m/Y',strtotime($user->notes_last_created)):""; ?></td> -->
			
           
        </tr>
    <?php endforeach; ?>
<?php endif; ?>