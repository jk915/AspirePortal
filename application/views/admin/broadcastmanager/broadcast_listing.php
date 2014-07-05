<input type="hidden" value="<?php print ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
			<tr>
				<th>Broadcast Name</th>			
				<th>Created Date</th>
				<th>Status</th>
				<th>No. Recipients</th>
				<th style="width: 20px;">Delete</th> 
			</tr>
 		<?php /* Setup alternating row colours, using the variable "rowclass" */ 
		$i = 0;
		if( $broadcasts )
		{
			foreach( $broadcasts->result() as $broadcast )
			{
				if($i++ % 2==1) $rowclass = "admintablerow";
				else  $rowclass = "admintablerowalt";
			?> 
				<tr class="<?php print $rowclass; ?>">
					<td class="admintabletextcell"><a href="<?php print base_url();?>broadcastmanager/broadcast/<?php print $broadcast->broadcast_id;?>"><?php print $broadcast->name; ?></a></td>
					<td class="admintabletextcell"><?php print date( 'd-m-Y', strtotime( $broadcast->insert_date ) );?></td>
					<td class="admintabletextcell"><?php print ( $broadcast->status );?></td>
					<td class="admintabletextcell"><?php print ( $broadcast->nr_recipients ? $broadcast->nr_recipients : '0' );?></td>
					<td class="center"><input type="checkbox" name="broadcaststodelete[]" value="<?php print $broadcast->broadcast_id;?>" /></td>
				</tr>          
			<?
			}
		}
		?>
</table>
