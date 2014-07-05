<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">
                
                	<?php 
                    	$user_id = $this->session->userdata["user_id"];
                    	$utid = $this->session->userdata["user_type_id"];
                	?>

                    <ul class="breadcrumbs">
                        <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                        <li><a href="<?php echo base_url(); ?>partners">My Partners</a></li>
                        <li>Partner Detail</li>
                    </ul>
                    
                    <div class="sidebar">
                        <?php if($user) : ?>
                        <h3>Stats</h3>
                        <table class="zebra" cellpadding="0" cellspacing="0">                                         
                            <tr>
                                <th>Statistic</th>
                                <th>Count</th>
                            </tr>
                            <tr>
                                <td>Reserved</td>
                                <td><?php echoifobj($stats, "num_reserved"); ?></td>
                            </tr>
                            <tr>
                                <td>Signed</td>
                                <td><?php echoifobj($stats, "num_signed"); ?></td>
                            </tr>
                            <tr>
                                <td>Sold</td>
                                <td><?php echoifobj($stats, "num_sold"); ?></td>
                            </tr>                                                        
                        </table>
                        <div class="top-margin20" style="background-color: #FFFFC0; padding: 5px;">
                            <p>
                                <b>Last Login:</b> <?php echo ($user->days_since_login > 9999 || empty($user->days_since_login)) ? "Never" : date("d/m/Y h:i:s A", strtotime($user->last_logged_dtm)); ?><br/>
                                <b>Days Since Login:</b> <?php echo ($user->days_since_login > 9999 || empty($user->days_since_login)) ? "NA" : $user->days_since_login; ?><br/>
                            </p>
                        </div>
                        <br />
                        <?php if(in_array($utid, array(USER_TYPE_ADVISOR))) : ?>
							<h3>Stock Permissions</h3>
							<p>
								<label><input type="checkbox" value="25" class="view_all_properties" <?php echo $user->view_all_property == 1 ? 'checked="checked"' : ''?>> Partner can view ALL properties.</label>
							</p>
							<div class="stock_permissions">
								<div class="stock_project_permissions"></div>
	                        	<div class="stock_property_permissions"></div>
                        	</div>
	                	<?php endif; ?>
                        
                        <h3>Actions</h3>                    
                        <ul>
                            <li style="display: none"><a href="#" data-reveal-id="deletePartnerConfirm">Delete Partner</a></li>
                            <?php if(($user) && ($user->email != "")) : ?>
                            <li><a href="mailto:<?php echo $user->email; ?>">Email Partner</a></li>      
                            <?php endif; ?>
                            
                            <?php $user_id = $this->session->userdata["user_id"]; ?>
                            <?php if ($user->created_by_user_id == $user_id && $user->enabled == 1) : ?>
                            <li class="login_as_this_user"><a href="javascript:;" data-reveal-id="loginAsThisUser">Log in as this user</a></li>
                            <?php endif; ?>
                        </ul>
                        
                        <div id="loginAsThisUser" class="reveal-modal">
                        	<h2>Login as this User</h2>
                        	<p>Are you sure you want to login as this user?</p>
                        	<p>
                        		<div class="error login_as_this_user_error"><h4>Please complete the following fields before submitting:</h4></div>
	                        	<a class="btn inline btnlogin" uid="<?php echoifobj($user, "user_id"); ?>" action="<?php echo site_url('partners/ajax')?>" action_name="login_as_this_user">Yes</a>&nbsp;
	                        	<a class="btn secondary inline close-reveal" href="#">No, cancel</a>
                        	</p>
                            <a class="close-reveal-modal">&#215;</a>
                        </div>
                        
                        <div id="deletePartnerConfirm" class="reveal-modal">
                             <h2>Confirmation Required</h2>
                             <p>Are you sure you want to delete this partner? This action is not reversible.</p>
                             <p><a class="btn inline">Yes, delete this partner</a>&nbsp;<a class="btn secondary inline" href="#">no, cancel</a></p>
                             <a class="close-reveal-modal">&#215;</a>
                        </div>
                        <?php else: ?>
                        <h3>Information</h3>
                        <p>To add a new partner, please enter their  details and then hit the 'Save Changes' button under the form (bottom right).</p>
                        <p>We will send an email to your partner with login details so they can access this portal.</p>
                        <p>You will have complete control over which estates and properties your partners will have access to.</p>
                        
                        <?php endif; ?>
                    <!-- end sidebar --></div>                
                    
                    <div class="mainCol"> 
                        <?php echo form_open('partners/ajax', array("id" => "frmPartnerDetail", "name" => "frmPartnerDetail", "class" => "block")); ?>
                            <input type="hidden" id="user_id" name="user_id" value="<?php echoifobj($user, "user_id"); ?>" />
                            <input type="hidden" id="action" name="action" value="update_partner" />
                            <h3>Partner Detail</h3>  
                            
                            <div class="error"><h4>Please complete the following fields before submitting:</h4></div> 
                            <div class="success"><h4>Your partner's information was updated successfully.</h4></div>
                            <fieldset>                            
                                <label for="first_name">First Name <span class="required">*</span></label>
                                <input type="text" name="first_name" value="<?php echoifobj($user, "first_name"); ?>" id="first_name" class="required" />
                                
                                <label for="last_name">Last Name <span class="required">*</span></label>
                                <input type="text" name="last_name" value="<?php echoifobj($user, "last_name"); ?>" id="last_name" class="required" />    
                                
                                <label for="company_name">Company/Business Name </label>
                                <input type="text" name="company_name" value="<?php echoifobj($user, "company_name"); ?>" id="company_name" />
                                
                                <label for="mobile">Mobile</label>
                                <input type="text" name="mobile" value="<?php echoifobj($user, "mobile"); ?>" id="mobile" />
                                
                                <label for="phone">Work Phone</label>
                                <input type="text" name="phone" value="<?php echoifobj($user, "phone"); ?>" id="phone" />
                                
                                <label for="home_phone">Home Phone</label>
                                <input type="text" name="home_phone" value="<?php echoifobj($user, "home_phone"); ?>" id="home_phone" />
                                
                                <label for="fax">Fax</label>
                                <input type="text" name="fax" value="<?php echoifobj($user, "fax"); ?>" id="fax" />     
                                
                                <label for="email">Email <span class="required">*</span></label>
                                <input type="text" name="email" value="<?php echoifobj($user, "email"); ?>" id="email" class="required email" <?php if($user) echo 'readonly="readonly"'; ?>  />                                                                     
                            </fieldset> 
                            <fieldset>
                                <label for="billing_address1">Address Line 1</label>
                                <input type="text" name="billing_address1" value="<?php echoifobj($user, "billing_address1"); ?>" id="billing_address1" />
                                
                                <label for="billing_address2">Address Line 2</label>
                                <input type="text" name="billing_address2" value="<?php echoifobj($user, "billing_address2"); ?>" id="billing_address2" />
                                
                                <label for="billing_suburb">Suburb</label>
                                <input type="text" name="billing_suburb" value="<?php echoifobj($user, "billing_suburb"); ?>" id="billing_suburb" />
                                
                                <label for="billing_postcode">Postcode</label>
                                <input type="text" name="billing_postcode" value="<?php echoifobj($user, "billing_postcode"); ?>" id="billing_postcode" />            
                                
                                <label for="billing_state_id">State <span class="required">*</span></label>
                                <select name="billing_state_id" id="billing_state_id" class="required">
                                    <option value="">Choose</option>
                                    <?php echo $this->utilities->print_select_options($states, "state_id", "name", ($user) ? $user->billing_state_id : ""); ?>
                                </select>   
                                
                                <label for="billing_country_id">Country</label>
                                <select name="billing_country_id"> 
                                    <option selected="selected" value="1">Australia</option>
                                </select>
                                
                                <label for="enabled">
                                	<input type="checkbox" name="enabled" id="enabled" value="1" <? echo (isset($user->enabled) &&  $user->enabled !="") ? (($user->enabled == 1) ? "checked" :"") : "checked" ?>  />&nbsp;Login Enabled
                            	</label>
                                <br/> <br/>
								<?php if($user && $user->user_type_id == 3): ?>
								<label for="acn">Login Expiry Date</label>
								<input type="text" name="login_expiry_date" value="<?php echoifobj($user, "login_expiry_date"); ?>" id="login_expiry_date" /> 
								<?php endif; ?>
                            </fieldset>                         
                            <p><a href="<?php echo base_url(); ?>partners">&laquo; Back</a></p>
                            <input type="submit" value="save changes" />                           
                        </form>  
                        
                        <?php if($user) : ?>
                        <!-- Note listing -->
                        <table cellpadding="0" cellspacing="0" class="note_listing listing">
                            <thead>
                                <tr class="intro">
                                    <td colspan="3">Notes</td>
                                </tr>
                                <tr>
                                    <th class="sortable">Date</th>
                                    <th class="sortable">Note</th>
                                    <th>Delete</th>
                                </tr>
                            </thead>
                            <tbody>
                            <!-- Listing will load here via AJAX -->
                            <?php if (isset($notes) && $notes) : ?>
                                <?php foreach ($notes->result() AS $note) : ?>
                                <tr>
                                    <td>
                                        <a class="shownote" href="<?php echo $note->note_id; ?>">
                                            <?php echo $this->utilities->iso_to_ukdate($note->note_date); ?>
                                        </a>
                                    </td>
                                    <td><?php echo $note->content; ?></td>
                                    <td><input class="delete_note" type="checkbox" value="<?php echo $note->note_id; ?>" /></td>
                                </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                            </tbody>
                        </table>
                        <p>
                            <a href="<?php echo base_url(); ?>notes/detail" class="btn arrow" id="btnAddNote" style="float:left;">add new note</a>
                            <a href="javascript:;" style="float:right;" id="load_5_note" cp="1">Load more</a>
                        </p>
                        <!-- End Note listing -->                        
                        <?php endif; ?>             
                    </div>                
                </div><!-- end main content -->
                
<!-- Note Detail Modal -->
                <div id="noteDetail" class="reveal-modal">
                     <h2>Note Details</h2>
                     <?php echo form_open('notes/ajax', array("id" => "frmNoteDetails", "name" => "frmNoteDetails")); ?>
                        <input type="hidden" name="action" value="update_note" />
                        <input type="hidden" id="note_id" name="note_id" value="" />
                        <input type="hidden" id="lead_id" name="lead_id" value="<?php echoifobj($user, "user_id"); ?>" />
                        <fieldset>
                            <label for="note_date">Note Date (dd/mm/yyyy) <span class="required">*</span></label>
                            <input type="text" id="note_date" name="note_date" value="<?php echo date('d/m/Y')?>" readonly="readonly"/>
                            
                            <label for="content">Note Details <span class="required">*</span></label>
                            <textarea id="content" name="content" cols="40" rows="10"></textarea>
                            
                            <label for="private"><input type="checkbox" id="private" name="private" value="1" /> Private</label>
                            
                        </fieldset>
                        
                        <p style="margin-top: 10px;">
                            <a class="btn inline" id="btnSaveNote">Save Note</a>&nbsp;
                            <!--<a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>-->
                        </p>
                     
                     </form>
                     <a class="close-reveal-modal">&#215;</a>
                </div>
                
                <!-- Note Delete Modal -->
                <div id="noteDelete" class="reveal-modal">
                     <h2>Delete Note</h2>
                     <?php echo form_open('notes/ajax', array("id" => "frmNoteDelete", "name" => "frmNoteDelete")); ?>
                        <input type="hidden" name="action" value="delete_note" />
                        <input type="hidden" id="note_id" name="note_id" value="" />
                        
                        <p class="confirmMessage">You are about to delete the note.  Are you sure you wish to continue?</p>
                        
                        <p style="margin-top: 10px;">
                            <a class="btn inline" id="btnDeleteNote">Yes, Confirm Delete</a>&nbsp;
                            <a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>
                        </p>
                     
                     </form>
                     <a class="close-reveal-modal">&#215;</a>
                </div>            
                
                <?php echo form_open('notes/ajax', array("id" => "frmLoadNote", "name" => "frmLoadNote")); ?>
                    <input type="hidden" name="action" value="load_note" />
                    <input type="hidden" id="note_id" name="note_id" value="" />                        
                </form>                