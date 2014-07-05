<?php if ($projects) :?>
<ul id="foo2">
    <?php foreach ($projects->result() AS $project) :?>
    <li class="left">
    	<a href="javascript:;" class="gl_thumb" title="<?php echo $project->project_name?>" pid="<?php echo $project->project_id?>">
    	   <img src="<?php echo site_url("project_files/".$project->project_id."/".$project->logo_print)?>" alt="<?php echo $project->project_name?>" width="170" height="115"/>
    	</a>
    	<p><?php echo $project->project_name?><br /><span class="txtadd"><?php echo $project->suburb." ".$project->state." ".$project->postcode?></span></p>
	</li>
	<?php endforeach;?>
</ul>
<div class="clearfix"></div>
<a class="prev" id="foo2_prev" href="#"><span>prev</span></a>
<a class="next" id="foo2_next" href="#"><span>next</span></a>
<?php endif;?>