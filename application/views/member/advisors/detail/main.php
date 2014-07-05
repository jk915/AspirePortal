<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                    <ul class="breadcrumbs">
                        <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                        <li><a href="<?php echo base_url(); ?>advisors">My Advisors</a></li>
                        <li>Advisor Detail</li>
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
                        <h3>Actions</h3>                    
                        <ul>
                            <li style="display: none"><a href="#" data-reveal-id="deleteAdvisorConfirm">Delete Advisor</a></li>
                            <?php if(($user) && ($user->email != "")) : ?>
                            <li><a href="mailto:<?php echo $user->email; ?>">Email <?php echo $user->first_name; ?></a></li>      
                            <?php endif; ?>
                            
                            <?php $user_id = $this->session->userdata["user_id"]; ?>
                            <?php if ($user->created_by_user_id == $user_id) : ?>
                            <li><a href="javascript:;" data-reveal-id="loginAsThisUser">Log in as this user</a></li>
                            <?php endif; ?>
                            
                        </ul>
                        
                        <div id="loginAsThisUser" class="reveal-modal">
                        	<h2>Login as this User</h2>
                        	<p>Are you sure you want to login as this user?</p>
                        	<p>
                        		<div class="error login_as_this_user_error"><h4>Please complete the following fields before submitting:</h4></div>
	                        	<a class="btn inline btnlogin" uid="<?php echoifobj($user, "user_id"); ?>" action="<?php echo site_url('advisors/ajax')?>" action_name="login_as_this_user">Yes</a>&nbsp;
	                        	<a class="btn secondary inline close-reveal" href="#">No, cancel</a>
                        	</p>
                            <a class="close-reveal-modal">&#215;</a>
                        </div>
                        
                        <div id="deleteAdvisorConfirm" class="reveal-modal">
                             <h2>Confirmation Required</h2>
                             <p>Are you sure you want to delete this advisor? This action is not reversible.</p>
                             <p><a class="btn inline">Yes, delete this advisor</a>&nbsp;<a class="btn secondary inline" href="#">no, cancel</a></p>
                             <a class="close-reveal-modal">&#215;</a>
                        </div>
                        <?php else: ?>
                        <h3>Information</h3>
                        <p>To setup a new Advisor account, please enter their details and then hit the 'Save Changes' button under the form (bottom right).</p>
                        <p>We will send an email to the Advisor with login details so they can access this portal.</p>                      
                        <?php endif; ?>
                    <!-- end sidebar --></div>                
                    
                    <div class="mainCol"> 
                        <?php echo form_open('advisors/ajax', array("id" => "frmAdvisorDetail", "name" => "frmAdvisorDetail", "class" => "block")); ?>
                            <input type="hidden" id="user_id" name="user_id" value="<?php echoifobj($user, "user_id"); ?>" />
                            <input type="hidden" id="action" name="action" value="update_advisor" />
                            <h3>Advisor Detail</h3>  
                            
                            <div class="error"><h4>Please complete the following fields before submitting:</h4></div> 
                            <div class="success"><h4>Your advisor's information was updated successfully.</h4></div>
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
                            <p><a href="<?php echo base_url(); ?>advisors">&laquo; Back</a></p>
                            <input type="submit" value="save changes" />                           
                        </form>               
                    </div>                
                </div><!-- end main content -->