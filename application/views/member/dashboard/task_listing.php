<tr class="intro">
    <td colspan="3">Tasks</td>
</tr>
<tr>
    <th class="sortable" sort="t.title">Task</th>
    <th class="sortable" sort="u2.first_name">Assigned To</th>
    <th class="sortable" sort="t.priority">Priority</th>
    <th class="sortable" sort="t.due_date">Due Date</th>
</tr>
<?php if($tasks): ?>
<?php 
	foreach($tasks->result() as $task) :
		switch ($task->user_type_id) {
			case 3:
				// Advisor
				$url_user_details = site_url('advisors/detail/'.$task->assign_to);
			break;
			
			case 5:
				// Partner
				$url_user_details = site_url('partners/detail/'.$task->assign_to);
			break;
			
			case 6:
				// Investor
				$url_user_details = site_url('investor/detail/'.$task->assign_to);
			break;
			
			case 7:
				// Lead
				$url_user_details = site_url('leads/detail/'.$task->assign_to);
			break;
			
			default:
				$url_user_details = site_url();
			break;
		}
?>
<tr>
    <td><a href="<?php echo base_url(); ?>tasks/index#<?php echo $task->task_id; ?>"><?php echo $task->title; ?></a></td>
    <!--<td><a href="<?php echo $url_user_details;?>"><?php echo $task->assign_client_name; ?></a></td>-->
    <td><?php echo $task->assign_client_name; ?></td>
    <td><?php echo ucfirst($task->priority); ?></td>
    <td><?php echo $this->utilities->iso_to_ukdate($task->due_date); ?></td>
</tr>                        
<?php endforeach; ?>
<?php endif; ?> 