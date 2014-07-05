<?php if($users) : ?>
    <?php foreach($users->result() as $user) : ?>
        <tr>
        	<td><a href="<?php echo base_url() . "partners/detail/" . $user->user_id; ?>"><?php echo $user->first_name . " " . $user->last_name; ?></a></td>
            <td><?php echo $user->company_name; ?></td>
            <td><?php echo $user->mobile; ?></td>
            <?php if(in_array($user_type_id, array(USER_TYPE_ADVISOR))): ?>
            <td><?php echo  ($user->notes_last_created != '')? date('m/d/Y',strtotime($user->notes_last_created)):"";  ?></td>
            <?php  else: ?>
            <td><?php echo ($user->num_sold > 0) ? $user->last_sold_date : "NA"; ?></td>
            <?php endif; ?>
            <td><?php echo $user->num_sold; ?></td>
            <td><?php echo format_login_days($user->days_since_login); ?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>