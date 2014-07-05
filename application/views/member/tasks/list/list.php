<?php if($tasks) : ?>
    <?php foreach($tasks->result() as $task) : ?>
        <tr>
            <td><a class="showtask" href="<?php echo $task->task_id; ?>"><?php echo $task->title; ?></a></td>
            <td><?php echo $task->assign_client_name; ?></td>
            <td><?php echo $this->utilities->iso_to_ukdate($task->due_date); ?></td>
            <td><?php echo $task->priority; ?></td>
            <td><?php echo ($task->status == 0) ? "Active" : "Completed"; ?></td>
            <td><input class="delete" type="checkbox" value="<?php echo $task->task_id; ?>" /></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>