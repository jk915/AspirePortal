<?php if($summaries) : ?>
    <?php foreach($summaries->result() as $summary) : ?>
        <tr>
            <td><?php echo $summary->created_date; ?></td>
            <td><a class="showtask" href="summaries/detail/<?php echo $summary->summary_id; ?>"><?php echo $summary->title; ?></a></td>
            <td><?php echo $summary->state_name; ?></td>
            <td><?php echo $summary->area_name; ?></td>
            <td><?php echo $summary->project_name; ?></td>
            <td><?php echo $summary->prepared_for; ?></td>
            <!--
            <td><input class="delete" type="checkbox" value="<?php echo $summary->summary_id; ?>" /></td>
            -->
        </tr>
    <?php endforeach; ?>
<?php endif; ?>