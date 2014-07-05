<table cellspacing="0" class="cmstable" id="tableBlocks" > 
			<tr>
				<th>ID</th>
				<th>Block Name</th>						
				<th>Active</th>				
			</tr>
 		<? /* Setup alternating row colours, using the variable "rowclass" */ 
		$i = 0;
		if($blocks)
		{
			foreach($blocks->result() as $block)
			{
				if($i++ % 2==1) $rowclass = "admintablerow";
				else  $rowclass = "admintablerowalt";
			?> 
				<tr class="<?php echo $rowclass; ?>">
					<td class="admintabletextcell"><?php echo $block->block_id; ?></td>
					<td class="admintabletextcell"><a href="<?php echo $block->block_name; ?>" class="tooltip" title="<?php echo $block->block_description; ?>"><?php echo $block->block_name; ?></a></td>
					<td class="admintabletextcell"><?php echo ($block->enabled == '1') ? "Yes" : "No"; ?></td>					
				</tr>          
			<?
			}
		}
		?>
</table>