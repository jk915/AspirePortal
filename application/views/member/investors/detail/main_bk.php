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
                        <li><a href="<?php echo base_url(); ?>investors">My Investors</a></li>
                        <li>Investor Detail</li>
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
								<label><input type="checkbox" value="25" class="view_all_properties" <?php echo $user->view_all_property == 1 ? 'checked="checked"' : ''?>> Investor can view ALL properties.</label>
							</p>
							<div class="stock_permissions">
								<div class="stock_project_permissions"></div>
	                        	<div class="stock_property_permissions"></div>
                        	</div>
	                	<?php endif; ?>
                        
                        <h3>Actions</h3>                    
                        <ul>
                            <li style="display: none"><a href="#" data-reveal-id="deletePartnerConfirm">Delete Investor</a></li>
                            <?php if(($user) && ($user->email != "")) : ?>
                            <li><a href="mailto:<?php echo $user->email; ?>">Email <?php echo $user->first_name; ?></a></li>      
                            <?php endif; ?>
                            
                            <?php if ( (($user->created_by_user_id == $user_id) || ($user->owner_id == $user_id) || ($user->owner_created_by_user_id == $user_id)) && ($user->enabled == 1) ) : ?>
                            <li class="login_as_this_user"><a href="javascript:;" data-reveal-id="loginAsThisUser">Log in as this user</a></li>
                            <?php endif; ?>
                        </ul>
                        
                        <div id="loginAsThisUser" class="reveal-modal">
                        	<h2>Login as this User</h2>
                        	<p>Are you sure you want to login as this user?</p>
                        	<p>
                        		<div class="error login_as_this_user_error"><h4>Please complete the following fields before submitting:</h4></div>
	                        	<a class="btn inline btnlogin" uid="<?php echoifobj($user, "user_id"); ?>" action="<?php echo site_url('investors/ajax')?>" action_name="login_as_this_user">Yes</a>&nbsp;
	                        	<a class="btn secondary inline close-reveal" href="#">No, cancel</a>
                        	</p>
                            <a class="close-reveal-modal">&#215;</a>
                        </div>
                        
                        <div id="deletePartnerConfirm" class="reveal-modal">
                             <h2>Confirmation Required</h2>
                             <p>Are you sure you want to delete this investor? This action is not reversible.</p>
                             <p><a class="btn inline">Yes, delete this investor</a>&nbsp;<a class="btn secondary inline" href="#">No, cancel</a></p>
                             <a class="close-reveal-modal">&#215;</a>
                        </div>
                        <?php else: ?>
                        <h3>Information</h3>
                        <p>To add a new Investor to your account, please enter their  details and then hit the 'Save Changes' button under the form (bottom right).</p>
                        <p>We will send an email to the Investor with login details so they can access this portal.</p>
                        <p>You will have complete control over which estates and properties your investors will have access to.</p>                        
                        <?php endif; ?>
                    <!-- end sidebar --></div>                
                    
                    <div class="mainCol"> 
                        <?php echo form_open('investors/ajax', array("id" => "frmInvestorDetail", "name" => "frmInvestorDetail", "class" => "block")); ?>
                            <input type="hidden" id="user_id" name="user_id" value="<?php echoifobj($user, "user_id"); ?>" />
                            <input type="hidden" id="action" name="action" value="update_investor" />
                            <h3>Investor Detail</h3>  
                            
                            <div class="error"><h4>Please complete the following fields before submitting:</h4></div> 
                            <div class="success"><h4>Your investor's information was updated successfully.</h4></div>
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
                            	
                            	<label for="advisor_id">Advisor / Owner</label>
                                <?php if((!$user) || ($user->created_by_user_id == $user_id)) : ?>
                                <select name="advisor_id">
                                    <option value="">Choose</option>
                                    <?php echo $this->utilities->print_select_options($advisors, "user_id", "owner_name", ($user) ? $user->advisor_id : '');?>
                                </select> 
                                <?php elseif($user): ?>
                                <p><?=$this->Users_model->get_user_name($user->advisor_id); ?></p>
                                <?php endif; ?>                               
                                

                                <label for="owner_id">Lead Source Partner or Advisor</label>
                                <?php if((!$user) || ($user->created_by_user_id == $user_id)) : ?>
                                <select name="owner_id">
                                	<option value="">Choose</option>
                                	<?php echo $this->utilities->print_select_options($owners, "user_id", "owner_name", ($user) ? $user->owner_id : '');?>
                                </select>
                                <?php elseif($user): ?>
                                <p><?=$this->Users_model->get_user_name($user->owner_id); ?></p>                                
                                <?php endif; ?>
                                
                                <label for="other_lead_source">Other Lead Source</label>
                                <?php if((!$user) || ($user->created_by_user_id == $user_id)) : ?>
                                <input type="text" name="other_lead_source" value="<?php echoifobj($user, "other_lead_source"); ?>" id="other_lead_source" />
                                <?php else: ?>
                                <p><?=$user->other_lead_source; ?></p>
                                <?php endif; ?>

                                <?php if($user) : ?>
                                    <?php if(in_array($utid, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))) : ?>
                                    <label for="enabled">
                                	    <input type="checkbox" name="enabled" id="enabled" value="1" <? echo (isset($user->enabled) &&  $user->enabled !="") ? (($user->enabled == 1) ? "checked" :"") : "checked" ?>  />&nbsp;Login Enabled
                            	    </label>
                                    <?php else: ?>
                                    <input type="hidden" name="enabled" value="<?php echo (isset($user->enabled) &&  $user->enabled !="") ? (($user->enabled == 1) ? "1" :"0") : "1" ?>" />
                                    <?php endif; ?>
                                <?php endif; ?>
                                
                            </fieldset>
                            
                            <div class="clear"></div>
                            <h3>Additional Contact</h3>
                            
                        	<fieldset>
                        		<label for="additional_contact_name">Contact Name</label>
                                <input type="text" name="additional_contact_name" value="<?php echoifobj($user, "additional_contact_name"); ?>" id="additional_contact_name" />
                                
                                <label for="additional_contact_relationships">Relationship</label>
                                <input type="text" name="additional_contact_relationships" value="<?php echoifobj($user, "additional_contact_relationships"); ?>" id="additional_contact_relationships" />
                        	</fieldset>
                        	<fieldset>
                        		<label for="additional_contact_phone">Phone</label>
                                <input type="text" name="additional_contact_phone" value="<?php echoifobj($user, "additional_contact_phone"); ?>" id="additional_contact_phone" />
                                
                                <label for="additional_contact_mobile">Mobile</label>
                                <input type="text" name="additional_contact_mobile" value="<?php echoifobj($user, "additional_contact_mobile"); ?>" id="additional_contact_mobile" />
                        	</fieldset>
                            
                            <p><a href="<?php echo base_url(); ?>investors">&laquo; Back</a></p>
                            <input type="submit" value="save changes" />                           
                        </form>
                        
                        <!-- Task listing -->
                        <table cellpadding="0" cellspacing="0" class="task_listing listing">
	                        <thead>
	                            <tr class="intro">
	                                <td colspan="4" class="sorttask" sort_col="t.due_date" sort_dir="ASC">Tasks</td>
	                            </tr>
	                            <tr>
	                                <th class="sortable" sort="t.due_date">Date</th>
	                                <th class="sortable" sort="t.title">Task</th>
	                                <th class="sortable" sort="t.priority">Priority</th>
	                                <th>Delete</th>
	                            </tr>
	                        </thead>
	                        <tbody>
	                        <!-- Listing will load here via AJAX -->
	                        <?php if (isset($tasks) && $tasks) : ?>
                            	<?php foreach ($tasks->result() AS $task) : ?>
                            	<tr>
                            		<td>
                            			<a class="showtask" href="<?php echo $task->task_id; ?>">
                            				<?php echo $this->utilities->iso_to_ukdate($task->due_date); ?>
                        				</a>
                    				</td>
						            <td><?php echo $task->title; ?></td>
						            <td><?php echo ucfirst($task->priority); ?></td>
						            <td><input class="delete_task" type="checkbox" value="<?php echo $task->task_id; ?>" /></td>
						        </tr>
                        		<?php endforeach; ?>
                            <?php endif; ?>
	                        </tbody>
	                    </table>
	                    <p>
	                    	<a href="<?php echo base_url(); ?>tasks/detail" class="btn arrow" id="btnAddTask" style="float:left;">add new task</a>
	                    	<a href="javascript:;" style="float:right;" id="load_5_task" cp="1">Load more</a>
	                    </p>
	                    <!-- End Task listing -->
                        
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
                        
                    </div>                
                </div><!-- end main content -->
                
                <!-- Task Detail Modal -->
	            <div id="taskDetail" class="reveal-modal">
	                 <h2>Task Details</h2>
	                 <?php echo form_open('tasks/ajax', array("id" => "frmDetails", "name" => "frmDetails")); ?>
	                    <input type="hidden" name="action" value="update_task" />
	                    <input type="hidden" id="task_id" name="task_id" value="" />
	                    <?php
	                    	$utid = $this->session->userdata["user_type_id"];
	                    	$logged_user_id = $this->session->userdata["user_id"];
	                	?>
	                	<input type="hidden" id="logged_user_id" value="<?php echo $logged_user_id;?>" />
	                    
	                    <fieldset>
	                        <label for="title">Task Title <span class="required">*</span></label>
	                        <input type="text" id="title" name="title" value="" class="required" />
	                    
	                        <label for="due_date">Due Date (dd/mm/yyyy)</label>
	                        <input type="text" id="due_date" name="due_date" value="<?php echo date('d/m/Y')?>" readonly="readonly"/>
	                        <input type="hidden" id="current_date" value="<?php echo date('d/m/Y')?>"/>
	                        
	                        <?php if(in_array($utid, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))) : ?>
                        
	                        <label for="assign_to">Assigned To</label>
	                        <select name="assign_to" id="assign_to">
	                        	<option value="">Choose</option>
	                        	<?php echo $this->utilities->print_select_options($assign_users, "user_id", "assign_client_name", $logged_user_id);?>
	                        </select>
	                        
	                        <?php endif; ?>
	                        
	                        <label for="priority">Priority <span class="required">*</span></label>
	                        <?php foreach($priorities as $priority_val => $priority_name) : ?>
	                        <input type="radio" name="priority" value="<?php echo $priority_val; ?>" /> <?php echo $priority_name; ?><br>
	                        <?php endforeach; ?>
	                        
	                        <div id="taskCompletedWrapper">
	                            <label for="completed" >Task Completed</label>
	                            <input type="checkbox" id="status" name="status" value="1" />
	                        </div>
	                    </fieldset>
	                    
	                    <fieldset>
	                        <label for="description">Task Description</label>
	                        <textarea id="description" name="description"></textarea>                        
	                    </fieldset>
	                    
	                    <p style="margin-top: 10px;">
	                        <a class="btn inline" id="btnSaveTask">Save Task</a>&nbsp;
	                        <!--<a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>-->
	                    </p>
	                 
	                 </form>
	                 <a class="close-reveal-modal">&#215;</a>
	            </div>
	            
	            <!-- Task Delete Modal -->
	            <div id="taskDelete" class="reveal-modal">
	                 <h2>Delete Task</h2>
	                 <?php echo form_open('tasks/ajax', array("id" => "frmDelete", "name" => "frmDelete")); ?>
	                    <input type="hidden" name="action" value="delete_task" />
	                    <input type="hidden" id="task_id" name="task_id" value="" />
	                    
	                    <p class="confirmMessage">You are about to delete the task "[TASKNAME]".  Are you sure you wish to continue?</p>
	                    
	                    <p style="margin-top: 10px;">
	                        <a class="btn inline" id="btnDeleteTask">Yes, Confirm Delete</a>&nbsp;
	                        <a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>
	                    </p>
	                 
	                 </form>
	                 <a class="close-reveal-modal">&#215;</a>
	            </div>            
	            
	            <?php echo form_open('tasks/ajax', array("id" => "frmLoad", "name" => "frmLoad")); ?>
	                <input type="hidden" name="action" value="load_task" />
	                <input type="hidden" id="task_id" name="task_id" value="" />                        
	            </form>
	            
	            
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