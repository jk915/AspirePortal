<body id="contact" >   
   <div id="wrapper">
       		
      <?php $this->load->view("admin/navigation");?>
		
      <div id="content">
		   <?php $this->load->view("admin/user/navigation"); ?>
         
         <?php if((isset($warning)) && ($warning!="")) : ?>
            <div class="warning"><?=$warning?></div>
         <?php endif; ?>
         
		   <p><?php echo $message?></p>
         <form class="plain" id="frmUser" name="frmUser" action="<?php echo base_url()?>admin/usermanager/user/<?php echo $user_id?>"  method="post">
            <h2>User Properties</h2>
			
         <?php
            if($user)
            {
			
               // We're editing and existing user.  Show the tabs.
               ?>
               
            <!-- tabs -->
            <ul class="css-tabs skin2">
               <li><a href="#">User Details</a></li>
               <li><a href="#">Reset Password</a></li>
               <li><a href="#" id="tabDocument">Documents</a></li>
               <li><a href="#" id="tabHistory">History</a></li>
            </ul>

            <!-- panes -->
            <div class="css-panes skin2">
               <div style="display:block">
               <?php
            }
   
         ?>               
            <div class="left">
            
                <label for="username">Username:<span class="requiredindicator">*</span></label> 
                <input type="text" name="username" id="username" class="required" minlength="4" value="<?php print $form_values["username"]; ?>" autocomplete="false" />
                
                <label for="first_name">First Name:<span class="requiredindicator">*</span></label> 
                <input type="text" name="first_name" id="first_name" class="required" value="<?php print $form_values["first_name"]; ?>"/>
                
                <label for="first_name">Last Name:<span class="requiredindicator">*</span></label> 
                <input type="text" name="last_name" id="last_name" class="required" value="<?php print $form_values["last_name"]; ?>"/>
                
                <label for="email">Email address:<span class="requiredindicator">*</span></label> 
                <input type="text" name="email" id="email" class="required email" value="<?php print $form_values["email"]; ?>"/> 
                
                <label for="mobile">Mobile:</label> 
                <input type="text" name="mobile" id="mobile" class="" value="<?php if(isset($user->mobile)) echo $user->mobile; ?>"/>
                
                <label for="phone">Work Phone:</label> 
                <input type="text" name="phone" id="phone" class="" value="<?php if(isset($user->phone)) echo $user->phone; ?>"/>
                
                <label for="home_phone">Home Phone:</label> 
                <input type="text" name="home_phone" id="home_phone" class="" value="<?php if(isset($user->home_phone)) echo $user->home_phone; ?>"/>
                
                <label for="fax">Fax:</label> 
                <input type="text" name="fax" id="fax" class="" value="<?php if(isset($user->fax)) echo $user->fax; ?>"/>
                
                <?php if(($user) && ($user->user_type_id > 2)) : ?>
                <div class="clear"></div>
                
                <?php if($user): ?>
                <label for="created_by">Created By:</label>
                <p><?php echo ($created_by) ? '<a href="' . site_url("admin/usermanager/user/" . $created_by->user_id) . '">' . $created_by->first_name . " " . $created_by->last_name . '</a>' : "Admin"; ?></p>
                <?php endif; ?>
                
                <label for="advisor_id">Advisor:</label> 
                <?php echo form_dropdown_advisors($user->user_id, "advisor_id", $user->advisor_id); ?>
                
                <label for="owner_id">Linked Account:</label> 
                <?php echo form_dropdown_owner($user->created_by_user_id, "owner_id", $user->owner_id); ?>                
                
                
                    <label for="legal_entity_name">Company Name:</label> 
                    <input type="text" name="company_name" id="company_name" class="" value="<?php if(isset($user->company_name)) echo $user->company_name; ?>"/> 

                    <label for="billing_address1">Address Line 1:</label> 
                    <input type="text" name="billing_address1" id="address" value="<?php if(isset($user->billing_address1)) echo $user->billing_address1; ?>"/> 
                    
                    <label for="billing_address2">Address Line 2:</label> 
                    <input type="text" name="billing_address2" id="billing_address2" value="<?php if(isset($user->billing_address2)) echo $user->billing_address2; ?>"/> 
                    
               
                <?php endif; ?>               
                
                <div class="clear"></div><br/>
            </div>
            <div class="left" style="padding-left: 20px;">
    
                
               
                <?php
                if($user)
                {
                   // We're editing and existing user.  Show the tabs.
                   ?>
                    <div class="clear"></div>
                    <div class="left right-margin">
                        <label for="billing_suburb">City/Suburb:</label> 
                        <input type="text" name="billing_suburb" id="billing_suburb" value="<?php if(isset($user->billing_suburb)) echo $user->billing_suburb; ?>"/> 
                    </div>
                    
                    <div class="clear"></div>
                    <div class="left">
                        <label for="billing_postcode">Postcode/Zip:</label> 
                        <input type="text" name="billing_postcode" id="billing_postcode" value="<?php if(isset($user->billing_postcode)) echo $user->billing_postcode; ?>"/> 
                    </div>
                    <div class="clear"></div>
                    
                    <label for="billing_state_id">State:</label> 
                    <select id="billing_state_id" name="billing_state_id">
                        <option value="">Choose</option>
                        <?php
                            echo $this->utilities->print_select_options($this->tools_model->get_states(1), "state_id", "name", ($user) ? $user->billing_state_id : "");
                        ?>
                    </select>
                
                    <label for="billing_country_id">Country:</label> 
                    <select id="billing_country_id" name="billing_country_id">
                        <option value="1">Australia</option>
                    </select>
                                        
                    <?php if($user) : ?>
					
    
                    <div class="top-margin20" style="background-color: #FFFFC0; padding: 5px; width: 250px;">
                        <p>
                            <b>Last Login:</b> <?php echo ($user->days_since_login > 9999 || ($user->days_since_login == "")) ? "Never" : date("d/m/Y h:i:s A", strtotime($user->last_logged_dtm)); ?><br/>
                            <b>Days Since Login:</b> <?php echo ($user->days_since_login > 9999 || ($user->days_since_login == "")) ? "NA" : $user->days_since_login; ?><br/>
							<!-- By Mayur - TasksEveryday -->
							<b>Date Created:</b> <?php echo ( empty($user->created_dtm)) ? "NA" : date("d/m/Y h:i:s A", strtotime($user->created_dtm)); ?><br/>
							<b>Created By:</b> <?php echo ( empty($username)) ? "NA" : $username; ?><br/><br/><br/>
							<!-- By Mayur - TasksEveryday -->
                        </p>
                    </div>
                    
                    <?php if(!empty($user->block_until)) : ?>
                    <?php
                        $blocked_until_stamp = strtotime($user->block_until);
                        if($blocked_until_stamp > time()) :
                    ?>
                    <div class="top-margin20" style="background-color: #FF8080; padding: 5px; width: 250px;">
                        <p><b>This user's account is blocked until <?php echo date("d/m/Y h:i:s A", strtotime($user->block_until)); ?></b></p>
                        <p><a href="#" id="btnRemoveBlock">Remove Block</a></p>
                    </div>
                    <?php endif; ?>
                    <?php endif; ?>
					
						
                    <?php endif; ?>
                    
					<?php if($user->user_type_id == 1) : ?>
						<input type="checkbox" name="email_notification" value="1" class="left" <? echo (isset($user->email_notification) &&  $user->email_notification !="") ? (($user->email_notification == 1) ? "checked" :"") : "checked" ?>  /><label for="email_notification" class="left" style="padding-top:0px">&nbsp;Recieve IP Conflict Email </label><br />
						<input type="checkbox" name="new_listing_email" value="1" class="left" <? echo (isset($user->new_listing_email) &&  $user->new_listing_email !="") ? (($user->new_listing_email == 1) ? "checked" :"") : "checked" ?>  /><label for="new_listing_email" class="left" style="padding-top:0px">&nbsp;Receive properties' notifications </label>
					<?php endif; ?>
				
					<?php 
					if($user->user_type_id == 6 || $user->user_type_id == 7)
					{
					?>
                    <h3>Additional Contact</h3> 
                    
            		<label for="additional_contact_first_name">First Name</label>
                    <input type="text" name="additional_contact_first_name" value="<?php if(isset($user->additional_contact_first_name)) echo $user->additional_contact_first_name; ?>" id="additional_contact_first_name" />
                    
                    <label for="additional_contact_middle_name">Middle Name</label>
                    <input type="text" name="additional_contact_middle_name" value="<?php if(isset($user->additional_contact_middle_name)) echo $user->additional_contact_middle_name; ?>" id="additional_contact_middle_name" />
                    
                    <label for="additional_contact_last_name">Last Name</label>
                    <input type="text" name="additional_contact_last_name" value="<?php if(isset($user->additional_contact_last_name)) echo $user->additional_contact_last_name; ?>" id="additional_contact_last_name" />                                
                                                                        
                    <label for="additional_contact_relationships">Relationship</label>
                    <select name="additional_contact_relationships" id="additional_contact_relationships">
                        <option value="">Choose</option>
                        <?php echo $this->utilities->print_select_options_array($relationship_types, false, (isset($user->additional_contact_relationships)) ? $user->additional_contact_relationships : '');?>
                    </select>   
                    
                   <label for="additional_contact_mobile">Mobile</label>
                    <input type="text" name="additional_contact_mobile" value="<?php if(isset($user->additional_contact_mobile)) echo $user->additional_contact_mobile; ?>" id="additional_contact_mobile" />
                                                
            		<label for="additional_contact_phone">Phone 2</label>
                    <input type="text" name="additional_contact_phone" value="<?php if(isset($user->additional_contact_phone)) echo $user->additional_contact_phone; ?>" id="additional_contact_phone" />
                    
                    <label for="additional_contact_email">Email</label>
                    <input type="email" name="additional_contact_email" class="require:false, email" value="<?php if(isset($user->additional_contact_email)) echo $user->additional_contact_email; ?>" id="additional_contact_email" />                                
                    
                    <label for="additional_contact_comment">Comment</label>
                    <input type="text" name="additional_contact_comment" value="<?php if(isset($user->additional_contact_comment)) echo $user->additional_contact_comment; ?>" id="additional_contact_comment" />                                                                
                    <?php
					}
					?>
						
                    
                <?php
                }
                ?>
                <div class="clear"></div>          
            
            </div>
            <div class="left" style="padding-left: 20px;">
            	<label for="user_type_id">User Type:<span class="requiredindicator">*</span></label> 
                <select id="user_type_id" name="user_type_id">
                    <?php if($user_types):?>
                        <?php print $this->utilities->print_select_options($user_types,"user_type_id","type",$form_values["user_type_id"] ); ?>
                    <? endif; ?>                
                </select>
				
				<div  id="keywords" style="display: none;">

                <label for="lead_status">Keywords</label>
                <textarea rows="5" cols="6" name="keywords"> </textarea>
				</div>

						
                <span id="builder" style="display:none;">
                    <label for="builder_id">Builder:<span class="requiredindicator">*</span></label>
                    <select id="builder_id" name="builder_id">
                        <?php if($builders):?>
                            <?php print $this->utilities->print_select_options($builders,"builder_id","builder_name",$form_values['builder_id']); ?>
                        <? endif; ?>
                    </select>
                </span>
<?php if ($user) : ?>
    <?php if ($user->user_type_id == USER_TYPE_LEAD) : ?>
                <label for="lead_status">Status:</label>
                <?php echo form_dropdown_investor_status('lead_status',$user->status,''); ?>
    <?php endif; ?>
                <label for="logo">User Logo:</label>
				<div class="logo_img">
                    <img id="logo_img_upload" src="<?php  echo empty($user->logo) ? '#' : base_url().$user->logo . "_thumb.jpg"; ?>" width="250" class="<?php echo empty($user->logo) ?  "hidden" : ""; ?>" />
              	</div>
              	<div class="clear"></div>
				<input class="<?php echo ($user->logo == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Logo" id="delete_logo" />
                <div id="logo_upload" class="showif <?php echo (!empty($user->logo)) ?  "hidden" : ""; ?>"></div>
                
				<?php 
					if($user->user_type_id == 6 || $user->user_type_id == 7)	
					{
				?>
				
                <h3>Legal Details</h3>
               <label for="legal_purchase_entity">Full Legal Purchase Entity</label>
                <textarea id="legal_purchase_entity" name="legal_purchase_entity" cols="30" rows="6" class="fullwidth"><?php if(isset($user->legal_purchase_entity)) echo $user->legal_purchase_entity; ?></textarea>                            
                
                <label for="purchase_comments" class="top-margin20">Purchase comments and ownership split</label>
                <textarea id="purchase_comments" name="purchase_comments" cols="30" rows="6" class="fullwidth"><?php if(isset($user->purchase_comments)) echo $user->purchase_comments; ?></textarea>                                                        
                <label for="acn">ACN</label>
                <input type="text" name="acn" value="<?php if(isset($user->acn)) echo $user->acn; ?>" id="acn" />  
               <label for="smsf_purchase">SMSF Purchase</label>
                <input type="radio" id="smsf_purchase" class="yes_smsf" name="smsf_purchase" value="Yes" <?php checkedif($user, "smsf_purchase", "Yes"); ?>  /> Yes &nbsp; &nbsp;
                <input type="radio" id="smsf_purchase" class="no_smsf" name="smsf_purchase" value="No" <?php checkedif($user, "smsf_purchase", "No"); ?> /> No
				<?php
				}
				?>
                  
                
				<?php endif; ?>
				<div class="clear"></div>
            	<br>
				
				<?php if(($user) && (in_array($user->user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER, USER_TYPE_LEAD, USER_TYPE_INVESTOR, USER_TYPE_SUPPLIER)) && substr($user->username, 0, 5) != "LEAD_")) : ?>
            	<a href="javascript:;" class="login_as_this_user button center">Login as this user</a>
				<?php endif; ?>
            
            
				<label for="acn">Login Expiry Date</label>
                <input type="text" name="login_expiry_date" value="<?php if(isset($user->login_expiry_date)) echo $user->login_expiry_date; ?>" id="login_expiry_date" />  
				<br /><br />
				<div>
                    <?php if($user):?>
					<?php if(($user->user_type_id == 3) || ($user->user_type_id == 5)):?>
						<input type="checkbox" name="new_listing_email" value="1" class="left" <? echo (isset($user->new_listing_email) &&  $user->new_listing_email !="") ? (($user->new_listing_email == 1) ? "checked" :"") : "checked" ?>  /><label for="new_listing_email" class="left" style="padding-top:0px">&nbsp;New Listings Notification </label><br /><br />
					<?php endif; ?>
						
					<?php if(($user->user_type_id == 3) || ($user->user_type_id == 5)): ?>
						<input type="checkbox" name="weekly_sales_report" value="1" class="left" <? echo (isset($user->weekly_sales_report) &&  $user->weekly_sales_report !="") ? (($user->weekly_sales_report == 1) ? "checked" :"") : "checked" ?>  /><label for="weekly_sales_report" class="left" style="padding-top:0px">&nbsp;Weekly Sales Report </label>
					<?php endif; ?> <br />
                    <?php endif;?>
				</div>
				
			</div>
				
			<div class="clear"></div>
            <div class="left">
            
            	<input type="checkbox" name="enabled" value="1" class="left" <? echo (isset($user->enabled) &&  $user->enabled !="") ? (($user->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;User is enabled</label><div class="left">&emsp;</div>
            
            	<input type="checkbox" name="bypass_disclaimer" value="1" class="left" <? echo (isset($user->bypass_disclaimer) &&  $user->bypass_disclaimer !="") ? (($user->bypass_disclaimer == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Bypass disclaimer</label><div class="left">&emsp;</div> 
            	
                <!--
                <input type="checkbox" name="subscribed" id="subscribed" value="1" class="left" <? echo (isset($user->subscribed) &&  $user->subscribed !="") ? (($user->subscribed == 1) ? "checked" :"") : "checked" ?>  /><label for="subscribed" class="left" style="padding-top:0px">&nbsp;User is subscribed to newsletter</label><div class="left">&emsp;</div>
                <input type="checkbox" name="is_text_only_newsletters" id="is_text_only_newsletters" value="1" class="left" <? echo (isset($user->is_text_only_newsletters) &&  $user->is_text_only_newsletters !="") ? (($user->is_text_only_newsletters == 1) ? "checked" :"") : "checked" ?>  /><label for="is_text_only_newsletters" class="left" style="padding-top:0px">&nbsp;Text Only Newsletters</label>
                -->
            </div>
            <div class="clear"></div>          
            <?php
               if(!$user)
               {
                  ?>
			            <h2>Set User Password</h2>
			            
			            <label for="password">Password:<span class="requiredindicator">*</span> <a class="autogenerate" href="#">Auto Generate</a></label> 
			            <input type="password" name="password" id="password" class="required strongpass" minlength="6" />
			            
			            <label for="password_repeat">Password Repeat:<span class="requiredindicator">*</span></label> 
			            <input type="password" name="password_repeat" id="password_repeat" class="required" minlength="6" equalto="#password" />                         
                        <p id="new_pass" class="clear hidden">The generated password was: <span></span></p>
                  <?php
               }
               else           
               {
                  // We're editing an existing user
                  ?>
               </div><!-- end user details tab -->
               
               <!-- start password reset tab -->
               <div>
                
                  <label for="password">New Password:<span class="requiredindicator">*</span></label> 
                  <input type="password" name="password" id="password" minlength="6" value="" autocomplete="off" />
                  
                  <label for="password_repeat">New Password Repeat:<span class="requiredindicator">*</span></label> 
                  <input type="password" name="password_repeat" id="password_repeat" minlength="6" equalto="#password" value="" />
                  
                  <br/>
                  <br/>
                  <input type="checkbox" id="email_password" name="email_password" value="1" /> Email new password to user ?
                  <br/>
                  <br/>
                  <input type="button" value="Change Password" id="change_email" /> 
                  <br/>
                  <br/>
                  
                  <p id="message" style="display:none"></p>                 
               </div><!-- end password reset tab -->
               <!-- start Documents tab -->
               <div>
                    <div class="right" style="padding-right:10px">
                        <label for="upload_document">Upload a new document</label>
                        <!--<input type="file" name="upload_file" id="upload_file" />    -->
                        <div id="upload_document"></div>
                    </div>
                    <div class="clear"></div>
                    <br/>
                    
                    <div id="files_listing">
                        <div id="page_listing">
                            <? $this->load->view('admin/user/document_listing',array('files'=>$documents,'pages_no' => count($documents) / $documents_records_per_page)); ?>
                        </div>
                        
                        <div class="clear"></div>
                        <div id="controls">
                            <div class="right">
                                <input class="button" type="button" value="Delete Selected Files" id="delete_user_files" />
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
               </div><!-- end Documents tab -->
				
				<!-- start History tab -->
               <div style ="overflow:hidden";>
                <table cellspacing="0" width="100%" class="left historylisting"> 
                <tr>
                    <th width="10%" sort="created_dtm">Date</th>
                    <th width="10%" sort="change_type">IP Address</th>
                    <th width="10%" sort="old_value">Visited Page</th>
                    <th width="10%" sort="user_id">User</th>
                </tr>
				<?php if ($user_history) : ?>
					<?php foreach ($user_history->result() AS $index=>$history) : ?>
						<tr id="ahistory_<?php echo $history->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
						   <td class="center"><?php echo $history->timestamp;?></td>
						   <td class="center"><?php echo $history->client_ip;?></td>
						   <td class="center"><?php echo $history->request_uri;?></td>
						   <td class="center"><?php echo $user->first_name.' '.$user->last_name; ?></td>
						   
						</tr>
					<?php endforeach; ?>
				<?php endif; ?>
				</table>
				<div id="controls">
                      <div id="page_buttons" class="left" >
                              <div id="pagination"></div>
                      </div>
				</div>
               </div><!-- end History tab -->
				
				
            </div><!-- end tabs -->
                  <?php
               }
               
            ?>           
            
            <label>&nbsp;</label> 
            <input id="button" type="submit" value="<? echo ($user_id == "") ? "Create New User": "Update User"?>" /><br/>    	    	

            <input type="hidden" id="remove_block" name="remove_block" value="0" />
            <input type="hidden" name="postback" value="1" />
            <input type="hidden" name="id" id="id" value="<?=$user_id?>" />
         </form>

         <br/>
         
         <? $this->load->view("admin/user/navigation"); ?>
         <? $this->load->view("admin/user/prefooter"); ?>
