<table cellspacing="0" class="cmstable">         
<tr>
	<th>Title</th>            
	<th>Last Modified</th>
	<th>Article Order</th>
	<th style="width: 20px;">FEATURED</th>    
	<th>Delete</th>                
</tr>
<? 
$i = 0;

if($articles)
{
	$num_recs = $articles->num_rows();
	
	foreach($articles->result() as $item)
	{
		if($i++ % 2==1) 
			$rowclass = "admintablerow";
		else  
			$rowclass = "admintablerowalt";
		?> 
		<tr class="<?=$rowclass;?>">
			<td class="admintabletextcell">
				<a href="<?php echo base_url(); ?>admin/contentmanager/article/<?=$item->article_id;?>/<?php echo $category_id; ?>">
					<?=$item->article_title;?>
				</a>
			</td>
			<td><?=$item->last_modification;?></td>
			<td>
			<?php
				echo '<div class="left">' . $item->article_order . '</div>';

				if($i > 1)
				{
					print '<a onclick="change_order(' . $item->article_id . ', \'up\');" href="javascript:void(0);" class="arrow-up left left-margin sprite top-margin-sm2"></a>';
				}
				
				if($i < $num_recs)
				{
					print '<a onclick="change_order(' . $item->article_id . ', \'down\');" href="javascript:void(0);" class="arrow-down left left-margin sprite top-margin-sm2"></a>';
				}
			?>
			</td> 
			<td class="center"><?=($item->enabled == '1') ? "Yes" : "No";?></td>                
			<td class="center"><input type="checkbox" name="itemstodelete[]" value="<?=$item->article_id;?>" /></td>                
		</tr>                  
		<?
	}
}
?>
</table>