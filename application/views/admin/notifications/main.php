<body id="contact">
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>

        <div id="content">

            <?php $this->load->view("admin/notifications/navigation"); ?>                        
            
				<!-- tabs -->
                <ul class="css-tabs skin2">
                    
                    <li><a href="#">New Listing Email</a></li>
                    <li><a href="#">Weekly Sales Report</a></li>
                    <li><a href="#">Sending</a></li>                    
                    <!-- <li><a href="#">Comments</a></li> -->                
                </ul>   
                
                <!-- panes -->
                <div class="css-panes skin2">
					
					
					<!-- New Listing Email -->
                    <div>
						
                        <table cellspacing="0" width="100%" class="left metalisting"> 
                            <tr>
                                <th width="10%">ID</th> 
                                <th align="left">User Name</th>
								<th align="left">User Type</th>	
                                <th width="10%">Notification</th>                            
                            </tr>
						<?php $i = 0;?>
                        
                        <?php if($new_listing_email) : ?>
						<?php foreach($new_listing_email->result() as $user) : ?>
							<?php
                                if($i++ % 2==1) $rowclass = "admintablerow";
			                    else  $rowclass = "admintablerowalt";
                            ?>
                            <tr class="<? print $rowclass;?>">
							
                                <td class="admintabletextcell" align="center"><a href="<?php echo base_url(); ?>admin/usermanager/user/<?php echo $user->user_id; ?>"><?php echo $user->user_id; ?></a></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><a href="<?php echo base_url(); ?>admin/usermanager/user/<?php echo $user->user_id; ?>" rel="" class="btnedit"> <?php echo $user->first_name .' '.$user->last_name; ?> </a></td>
								<td> <?php echo $user->user_type; ?> </td>
                                <td class="center"><a href="<?php echo site_url("admin/notifications/change_new_listing_email/$user->user_id")?>" class="fancybox fancybox.ajax"><?php echo ($user->new_listing_email == 1 ? 'Yes' : 'No'); ?></a></td>
								
                            </tr>
						<?php endforeach; ?>
                        <?php endif; ?>	
                        </table>
                        
                        <div class="clear"></div>
                        
                                                
                    </div><!-- END New Listing Email tab -->
                    
					<!-- Weekly Sales Report -->
                    <div>
						
                        <table cellspacing="0" width="100%" class="left metalisting"> 
                            <tr>
                                <th width="10%">ID</th> 
                                <th align="left">User Name</th>
								<th align="left">User Type</th>		
                                <th width="10%">Notification</th>                            
                            </tr>
						<?php $i = 0;?>

                        <?php if($weekly_sales_report) : ?>
						<?php foreach($weekly_sales_report->result() as $user) : ?>
							<?php
                                if($i++ % 2==1) $rowclass = "admintablerow";
			                    else  $rowclass = "admintablerowalt";
                            ?>
                            <tr class="<? print $rowclass;?>">
							
                                <td class="admintabletextcell" align="center"><a href="<?php echo base_url(); ?>admin/usermanager/user/<?php echo $user->user_id; ?>"><?php echo $user->user_id; ?></a></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><a href="<?php echo base_url(); ?>admin/usermanager/user/<?php echo $user->user_id; ?>" rel="" class="btnedit"> <?php echo $user->first_name .' '.$user->last_name; ?> </a></td>
								<td> <?php echo $user->user_type; ?> </td>
                                <td class="center"><a href="<?php echo site_url("admin/notifications/change_weekly_sales_report/$user->user_id")?>" class="fancybox fancybox.ajax"><?php echo ($user->weekly_sales_report == 1 ? 'Yes' : 'No'); ?></a></td>
								
                            </tr>
						<?php endforeach; ?>	
                        <?php endif; ?>
                        </table>
                        
                        <div class="clear"></div>
                        
                                                
                    </div><!-- END Weekly Sales Report tab -->
                    
					<!-- Sending Tab -->
					<div>
						
						<p>
							<label for="mail_type"> Select Type </label>
							<select id="mail_type" name="mail_type">
								<option value="" selected="selected">Select Type </option>
								<option value="1"> New Listing Email </option>
								<option value="2">Weekly Sales Report </option>
							</select>
						</p>
					
						<p>
								<label for="turn_on_off" class="labelStrong">Turn On/Off Notification</label>
								<select id="turn_on_off" name="turn_on_off">
									<option value="" selected="selected">Select Turn On/Off</option>
									<option value="1">Turn On</option>
									<option value="0">Turn Off</option>
								</select>
								
							</p>
							
							<p>
								<label for="duration" class="labelStrong">Mail Notification</label>
								<select id="duration" name="duration">
									<option value="" selected="selected">Select duration</option>
									<option value="weekly">Weekly</option>
									<option value="fortnightly">Fortnightly</option>
									
								</select>
														
							</p>

							<p id="due_date" style="display:none;">
								<label for="mail_date"> Due Date </label>
								<input type="text" name="mail_date" id="mail_date"  value="" />
							</p>
							
							<p>
								<div id="calender">
								</div>
							</p>
							<p>
								<input type="button" value="Send Now" id="submitorder" />
							</p>
							<p>
								<input id="update_notifications" name="update_notifications" class="button" type="button" value="Update Notifications"/>
							</p>
						
												
					</div>
					<!-- End Sending Tab -->
					
				</div>

                <div class="clear"></div>
    
        </div>    
         <br/>
            