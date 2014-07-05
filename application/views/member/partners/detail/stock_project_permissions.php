	<p></p>
	
	<table class="zebra" cellpadding="0" cellspacing="0">
		<thead>
			<tr>
		        <th colspan="2">This partner can currently view the following projects</th>
		    </tr>
		</thead>
		<tbody>
	    <?php if ($assign_projects) : ?>
	    	<?php foreach ($assign_projects->result() AS $index=>$assign_project) : ?>
		    <tr <?php echo $index%2 ? 'class="alt"' : ''?>>
		        <td style="width:77%;">
		        	<a href="javascript:;" class="view_property_assign" pid="<?php echo $assign_project->foreign_id?>">
		        		<?php echo $assign_project->project_name;?>
		        	</a>
	        	</td>
		        <td>
		        	<a href="javascript:;" rel="<?php echo $assign_project->id;?>" pid="<?php echo $assign_project->foreign_id?>" class="remove_project">Remove</a>
	        	</td>
		    </tr>
		    <?php endforeach; ?>
	    <?php endif; ?>
    	</tbody>
    	<tfoot>
    		<tr>
    			<td colspan="2">
    				<label>Add another project:</label>
			        <select id="project_id" name="project_id" style="width:75%;">
			        	<option value="">Select Project</option>
			            <? if($projects):?>
			                <?=$this->utilities->print_select_options($projects,"project_id","project_name",$project_id); ?>
			            <? endif; ?>
			        </select>
    			</td>
    		</tr>
    	</tfoot>
	</table>