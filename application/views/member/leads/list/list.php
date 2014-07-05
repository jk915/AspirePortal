<?php if($users) : ?>
	
	<?php foreach($users->result() as $user) : ?>
        <tr>
            <td><a href="<?php echo base_url() . "leads/detail/" . $user->user_id; ?>"><?php echo $user->first_name . " " . $user->last_name; ?></a></td>
            <!--<td><?php //echo $user->company_name; ?></td>-->
            <td><?php echo $user->mobile; ?></td>
			<td><?php echo get_days($user->created_dtm); ?></td>
			<td><?php echo ($user->notes_last_created != '')? date('d/m/Y',strtotime($user->notes_last_created)):""; ?></td>
			<td><?php echo $user->owner; ?></td>
            <td><?php echo $user->status; ?></td>
            <td><?php echo format_login_days($user->days_since_login); ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>