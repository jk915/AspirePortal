<!-- Listing will load here via AJAX -->
<?php if ($tasks) : ?>
	<?php foreach ($tasks->result() AS $task) : ?>
	<tr>
		<td>
			<a class="showtask" href="<?php echo $task->task_id; ?>">
				<?php echo $this->utilities->iso_to_ukdate($task->due_date); ?>
			</a>
		</td>
        <td tid="<?php echo $task->task_id; ?>"><?php echo $task->title; ?></td>
        <td><?php echo ucfirst($task->priority); ?></td>
        <td><input class="delete_task" type="checkbox" value="<?php echo $task->task_id; ?>" /></td>
    </tr>
	<?php endforeach; ?>
<?php endif; ?>