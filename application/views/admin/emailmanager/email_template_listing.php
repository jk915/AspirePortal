<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<?php /*<input type="hidden" value="<?php echo $order_column; ?>" id="order_column"/>
<input type="hidden" value="<?php echo $order_direction; ?>" id="order_direction"/>*/ ?>
<input type="hidden" value="email_template" id="order_table"/>
<table cellspacing="0" class="cmstable" > 
	<tr>
		<th>ID</th>
		<th>Email Template</th>		
		<th>Email Subject</th>
		<th style="width: 20px;">Delete</th>		
	</tr>
<?php 
	$i = 0;
	if($email_templates)
	{
		foreach($email_templates->result() as $email_template)
		{
			if( $i++ % 2==1 ) 
				$rowclass = 'admintablerow';
			else  
				$rowclass = 'admintablerowalt';
				
		?> 
		
			<tr class="<?php echo $rowclass; ?>">
			
				<td class="admintabletextcell">
					<?php echo $email_template->id; ?>
				</td>
				<td class="admintabletextcell">
					<a href="<?php echo base_url();?>admin/emailmanager/email_template/<?php echo $email_template->id; ?>"><?php echo $email_template->email_template; ?></a>
				</td>
				<td class="admintabletextcell">
					<?php echo $email_template->email_subject; ?>
				</td>
				
				<td class="center">
					<input type="checkbox" name="templatestodelete[]" value="<?php echo $email_template->id; ?>" />
				</td>				
			</tr>
			          
		<?php
		}
	}
	else
	{
?>
		<tr><td colspan="10">There are no email templates</td></tr>
<?php 
	}
?>
</table>
