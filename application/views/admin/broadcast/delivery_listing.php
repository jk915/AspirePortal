<input type="hidden" value="<?php print ceil($pages_no); ?>" id="pages_no" />
<?php 
	if( isset( $recipients ) && $recipients )
	{
?>
		<table>
		<thead>
			<tr>
				<th>Recipient</th>
				<th>Status</th>
				<th style="width: 50px;">Clicked</th>
				<th style="width: 70px;">Unsubscribed</th>
				<th>Html Sent</th>
			</tr>
		</thead>
		
		<tbody>
			<?php
					$i=1; 
					foreach( $recipients->result() as $recipient )
					{
				?>
						<tr class="admintablerow<?php print ( $i%2 == 0 ? 'alt' : '' ); ?>">
							<td class="admintabletextcell"><?php print $recipient->first_name .' '. $recipient->last_name; ?></td>
							<td class="admintabletextcell"><?php print 'Delivered'; ?></td>
							<td class="admintabletextcell">
								<?php print ( $this->utilities->in_multiarray( $recipient->user_id, $broadcast_clicks ) ? 'Yes' : 'No' ); ?>
							</td>
							<td class="admintabletextcell">
								<?php print ( $this->utilities->in_multiarray( $recipient->user_id, $broadcast_unsubscribes ) ? 'Yes' : 'No' ); ?>
							</td>
							<td><a href="<?php print base_url(); ?>broadcastmanager/view/<?php print $broadcast_id.'/'.$recipient->user_id; ?>" target="_new">Click to view</a></td>
						</tr>
				<?php 
					}
			?>
		</tbody>
	</table>
<?php 
	}
	else
	{
?>
		No recipients
<?php 
	}
?>