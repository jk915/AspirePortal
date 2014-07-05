<input type="hidden" value="<?php print ceil($pages_no); ?>" id="pages_no" />
<?php 
	if( isset( $recipients ) && $recipients )
	{
?>
		<table>
		<thead>
			<tr>
				<th>Recipient</th>
				<th>User Group</th>
				<th style="width: 50px;">Selected</th>
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
							<td class="admintabletextcell"><?php print $recipient->access_level; ?></td>
							<td class="admintabletextcell">
								<?php 
									if( $broadcast->send_to == 'All' || ( $broadcast->send_to == 'Access Level' && $broadcast->send_to_access_level_id == $recipient->broadcast_access_level_id ) )
										print 'Yes';
									else if( $broadcast->send_to == 'Access Level' && $broadcast->send_to_access_level_id != $recipient->broadcast_access_level_id )
										print 'No';
									else
									{
								?>
										<input type="checkbox" class="chk_recipient" id="recipient<?php print $recipient->user_id; ?>" <?php print ( $this->utilities->in_multiarray( $recipient->user_id, $not_recipients ) ? '' : 'checked="checked"' ); ?> />
								<?php 		
									}
								?>
							</td>
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