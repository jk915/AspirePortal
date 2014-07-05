<body id="contact" >   
    <div id="wrapper">
        
        <?php //$this->load->view("admin/ckeditor_pages_articles"); ?>
        
		<?php $this->load->view("admin/navigation");?>
		
        <div id="content">

			<?php $this->load->view("admin/broadcast/navigation"); ?>			
			
			<?php if((isset($warning)) && ($warning!="")) : ?>
	            <div class="warning"><?php print $warning; ?></div>
	         <?php endif; ?>
	         
	        <?php if( $this->session->flashdata( 'warning' ) ) : ?>
	       		<div class="warning"><?php print $this->session->flashdata( 'warning' ); ?></div>
	        <?php endif; ?>
         
		   <p><?php echo $message?></p>
		
			<form class="plain" id="frmBroadcast" name="frmBroadcast" action="<?php echo base_url()?>broadcastmanager/broadcast/<?php print $broadcast_id; ?>" method="post">
				<h2>Broadcast Properties</h2>	
					<!-- tabs -->
			        <ul class="css-tabs skin2">
			        	<li><a href="#">Broadcast Details</a></li>
			            <?php 
						if( !empty( $broadcast ) && $broadcast )
						{
						?>
			               <li><a href="#">Html Version</a></li>
			               <li><a href="#">Text Version</a></li>
			               <li><a href="#">Recipients</a></li>
			            <?php 
						}
						?>		               
					</ul>
					
			            <!-- panes -->
			            <div class="css-panes skin2">
			               <div style="display:block">
								<div class="left">
									<label for="broadcast_name">Broadcast Name:<span class="requiredindicator">*</span></label> 
						   			<input type="text" name="name" id="broadcast_name" class="required" value="<?php echo ( !empty( $_POST['name'] ) ? $_POST['name'] : ( !empty( $broadcast_id ) ? $broadcast->name : "" ) ); ?>"/>
						   			<br/>
									
									<label for="broadcast_template_id">Template:<span class="requiredindicator">*</span></label>
									<select name="broadcast_template_id" id="broadcast_template_id" class="required">
										<option value="">Choose a Template</option>
										<?php print $this->utilities->print_select_options($broadcast_templates,"broadcast_template_id","template_name", ( !empty( $_POST['broadcast_template_id'] ) ? $_POST['broadcast_template_id'] : ( $broadcast_id ? $broadcast->broadcast_template_id : '' ) ) ); ?>
									</select>
									<br />
									
									<label for="subject">Email Subject:<span class="requiredindicator">*</span></label> 
						   			<input type="text" name="subject" id="subject" class="required" value="<?php echo ( !empty( $_POST['subject'] ) ? $_POST['subject'] : ( !empty( $broadcast_id ) !="" ? $broadcast->subject : "" ) ); ?>" />
						   			<br />
						   			
						   			<label for="from">Email From:<span class="requiredindicator">*</span></label> 
						   			<input type="text" name="from" id="from" class="required email" value="<?php echo ( !empty( $_POST['from'] ) ? $_POST['from'] : ( !empty( $broadcast_id ) !="" ? $broadcast->from : "" ) ); ?>" />
						   			<br />
						   			
						   			<label for="from">Send To:<span class="requiredindicator">*</span></label> 
						   			<select name="send_to" id="send_to">
										<?php print $this->utilities->print_select_options( $this->utilities->get_database_enums_array( 'nc_broadcasts', 'send_to' ), "send_to", "send_to", ( !empty( $_POST['send_to'] ) ? $_POST['send_to'] : ( $broadcast_id ? $broadcast->send_to : '' ) ) ); ?>
									</select>
						   			<br />
						   			
						   			<div id="select_access_level" style="display: none;">
							   			<label for="from" id="send_to_access_level_id_label">Access Level:<span class="requiredindicator">*</span></label> 
							   			<select name="send_to_access_level_id" id="send_to_access_level_id">
											<?php print $this->utilities->print_select_options( $broadcast_access_levels_to, "broadcast_access_level_id", "level", ( !empty( $_POST['send_to_access_level_id'] ) ? $_POST['send_to_access_level_id'] : ( $broadcast_id ? $broadcast->send_to_access_level_id : '' ) ) ); ?>
										</select>
							   			<br />
							   		</div>
						   			
								</div>
								<?php 
									if( $broadcast_id && $broadcast->broadcast_status_id == BROADCAST_STATUS_SENT_ID )
									{
								?>
								<div class="right">
									<fieldset>
										<legend>Stats</legend>
										<span>No. Recipients: <?php print( $broadcast_id ? $broadcast->nr_recipients : '0' ); ?></span>
										<span>No. Clicks: <?php print( $broadcast_id ? $broadcast->nr_clicks : '0' ); ?></span>
										<span>No. Unsubscribes: <?php print( $broadcast_id ? $broadcast->nr_unsubscribes : '0' ); ?></span>
									</fieldset>
								</div>
								<?php 
									}
								?>
								<div class="clear"></div>
							</div>
							
							<?php 
							if( !empty( $broadcast ) && $broadcast )
							{
							?>
							<div>
								<textarea class="wysiwyg editor" cols="20" rows="10" name="html_content" style="width:880px;height:300px"><? print ( !empty( $_POST['html_content'] ) ? $_POST['html_content'] : ( $broadcast_id ? $broadcast->html_content : '' ) ); ?></textarea>
							</div>
							
							<div>
								<textarea cols="20" rows="10" name="normal_content" style="width:880px;height:300px"><? print ( !empty( $_POST['normal_content'] ) ? $_POST['normal_content'] : ( $broadcast_id ? $broadcast->normal_content : '' ) ); ?></textarea>
							</div>
							
							<div>
								<div class="left">
									<select id="select_list" name="level_id">
										<option value="">All</option>
										<?php print $this->utilities->print_select_options( $broadcast_access_levels_to, "broadcast_access_level_id", "level", ( !empty( $_POST['send_to_access_level_id'] ) ? $_POST['send_to_access_level_id'] : ( $broadcast_id && $broadcast->send_to == 'Access Level' ? $broadcast->send_to_access_level_id : '' ) ) ); ?>
									</select>
								</div>
								<div class="right">
									<input type="text" id="search" class="box" />
								    <input type="button" value="Go" id="btn_search" class="button"/>
								</div>
								<div class="clear"></div>
								<div id="page_listing">
									<?php 
									if( $broadcast->broadcast_status_id == BROADCAST_STATUS_SENT_ID )
										$this->load->view( 'admin/broadcast/delivery_listing' );
									else
										$this->load->view( 'admin/broadcast/recipient_listing' );
									?>
								</div>
								<div id="controls">
									<div id="page_buttons" class="left" >
										<div id="pagination"></div>
									</div>
									<div class="clear"></div>
								</div>
							</div>
							<?php 
							}
							?>
						</div> <!-- End tabs -->
			   			<?php 
			   			/*
			   			<br/>   	
						<br/>			
						
			            <?php echo $this->load->view("admin/ckeditor/ckeditor_and_history", array( "id" => "wysiwyg", "name" => "html_content", "table" => "custom_blocks", "content" => ( !empty( $broadcast_id ) ? $broadcast->html_content : "" ), "foreign_id" => ( !empty( $broadcast_id ) ? $broadcast->broadcast_id : "" ) ) ); ?>
						
						<br class="clear"/>
						<br/>
						
			            <div class="left">
			                <input type="checkbox" name="enabled" value="1" class="left" <?php echo ($block_id !="") ? (($block->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Block is active</label> 
			            </div>
			            
			            <div class="left left-margin20">
			                <input type="checkbox" name="show_on_sidebar" value="1" class="left" <?php echo ($block_id !="") ? (($block->show_on_sidebar == 1) ? "checked" :"") : "checked" ?>  /><label for="show_on_sidebar" class="left" style="padding-top:0px">&nbsp;Block can be shown on sidebar</label> 
			            </div>
			            
			            <?php /*<div class="left left-margin20">
			                <input type="checkbox" name="hide_heading" value="1" class="left" <?php echo ($block_id !="") ? (($block->hide_heading == 1) ? "checked" :"") : "checked" ?>  /><label for="hide_heading" class="left" style="padding-top:0px">&nbsp;Hide heading</label> 
			            </div>            
			            */
			            /*
			            
			            <div class="clear"></div>    
						
						<br/>
			   			
					
				<br/>
				<br/>
				   	   
			   	<label for="heading">&nbsp;</label> 
			    <input id="button" type="submit" value="<?php echo ($block_id == "") ? "Create New Block": "Update Block"?>" /><br/>    	    	
				*/
			   			
			   	?>
			   	<label>&nbsp;</label> 
    			<input id="button" type="submit" value="<?php echo ($broadcast_id == "") ? "Create New Broadcast": "Update Broadcast"?>" />
    			<input type="button" id="btn_Send_Preview" class="button" value="Send Preview" />
			   	<?php if( !empty( $broadcast_id ) && $broadcast->broadcast_status_id != BROADCAST_STATUS_SENT_ID ) { ?>
    				<input id="btn_Send_Broadcast" class="button" type="button" value="Send Broadcast" />
    			<?php } ?>
    			<br/>
    			
				<input type="hidden" name="postback" value="1" />
				<input type="hidden" name="id" id="id" value="<?php print $broadcast_id; ?>" />
			</form>
			<div class="clear"></div>
			<div style="display: none;">
				<div id="fancy">
					<form id="frm_Preview">
						<label>Enter the email address:</label>
						<input type="text" id="preview_email" class="required email" value="<?php print CONTACT_EMAIL; ?>" />
						<input type="button" class="button" id="btn_send_preview_last" value="SEND PREVIEW" />
						<div class="clear"></div>
						<div id="fancy_message"></div>
					</form>
				</div>
			</div>
			<p>&nbsp;</p>
			<?php $this->load->view("admin/broadcast/navigation"); ?>
