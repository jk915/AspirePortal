<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
			<tr>
				<th>ID</th>
				<th>Block Name</th>			
				<th>Active</th>
				<th style="width: 20px;">Delete</th> 
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
				<tr class="<?php print $rowclass;?>">
					<td class="admintabletextcell"><?php print $block->block_id;?></td>
					<td class="admintabletextcell"><a href="<?php print base_url();?>admin/blockmanager/block/<?php print $block->block_id;?>"><?php print $block->block_name;?></a></td>
					<td class="admintabletextcell"><?php print ($block->enabled == '1' ? "Yes" : "No" );?></td>
					<td class="center"><input type="checkbox" name="pagestodelete[]" value="<?php print $block->block_id;?>" /></td>
				</tr>          
			<?
			}
		}
		?>
</table>
