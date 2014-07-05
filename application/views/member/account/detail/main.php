<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                    <ul class="breadcrumbs">
                        <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                        <li>My Account</li>
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
                        
                        <h3>Actions</h3>                    
                        <ul>
                            <li><a href="#" data-reveal-id="changePassword">Change My Password</a></li>               
                        </ul>
                        <div id="changePassword" class="reveal-modal">
                             <h2>Change My Password</h2>
                             <p>If you wish to change your login password, please enter your new password below and submit.</p>
                             <p>
                             	<div class="error change_password_error"><h4>Please complete the following fields before submitting:</h4></div>
                             	<div class="success change_password_success"><h4>Your password was updated successfully.</h4></div>
                             </p>
                             <?php echo form_open('account/ajax', array("id" => "frmChangePassword", "name" => "frmChangePassword")); ?>
								<p>
									<label for="new_password">New Password: <span class="required">*</span></label>
									<input type="password" value="" name="new_password" id="new_password" size="30" class="required strongpass" />
									
									<label for="re_new_password">New Password Repeat: <span class="required">*</span></label>
									<input type="password" value="" name="re_new_password" id="re_new_password" size="30" class="required" />
								</p>
							 </form>
							 <p>
							 <a class="btn inline change_password" action="change_password">Change Password</a>&nbsp;<a class="btn secondary inline close-reveal" href="javascript:;">No, cancel</a></p>
                             <a class="close-reveal-modal">&#215;</a>
                        </div>                   
                        <?php endif; ?>
                    <!-- end sidebar --></div>            
                    
                    <div class="mainCol"> 
                        <?php echo form_open('account/ajax', array("id" => "frmAccountDetail", "name" => "frmAccountDetail", "class" => "block")); ?>
                            <input type="hidden" id="action" name="action" value="update_account" />
                            <h3>My Account Details</h3>  
                            
                            <div class="error"><h4>Please complete the following fields before submitting:</h4></div> 
                            <div class="success"><h4>Your information was updated successfully.</h4></div>
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
                            </fieldset>                         
                            <input type="submit" value="save changes" />                           
                        </form>               
                    </div>                
                </div><!-- end main content -->