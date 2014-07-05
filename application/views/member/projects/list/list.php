<?php if($projects) : ?>
    <?php foreach($projects->result() as $project) : ?>
    <tr>
        <td><a href="<?php echo base_url() . "projects/detail/" . $project->project_id; ?>"><?=$project->project_name;?></a></td>                                
        <td><?=$project->area_name; ?></td>
        <td><?=$project->state; ?></td>                                      
        <td>$<?=number_format($project->prices_from, 0, ".", ","); ?></td>                                                          
        <td><?=$project->rate; ?></td>
    </tr>    
    <?php endforeach; ?>
<?php endif; ?>