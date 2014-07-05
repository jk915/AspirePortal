<div class="intro">
	<div class="left">
		<img src="<?php print base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?php print base_url();?>menu">Back to Menu</a>  
	<span class="divider">|</span>  
	<img src="<?php print base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add new broadcast." /> <a href="<?php print base_url();?>broadcastmanager/broadcast">Add New Broadcast</a>
	</div>
	<div class="right">
		<select class="select_status">
			<option value="">View all</option>
			<?php print $this->utilities->print_select_options($broadcast_statuses,"broadcast_status_id","status" ); ?>
		</select>
	</div>
	<div class="clear"></div>
</div>
