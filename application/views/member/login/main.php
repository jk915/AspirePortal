<body class="home">    
    <div id="wrapper">                  
        <div id="main">  
            <div class="content">     
                <img id="logo" src="<?php echo base_url(); ?>images/member/logo.png" border="0" width="473" height="115" alt="Aspire Network Logo." />               
					
                <div class="mainCol">
                    <p class="welcome">
                        <?php if($this->session->flashdata("user_message") != "") : ?>
                        <?php echo $this->session->flashdata("user_message"); ?> 
                        <?php else: ?>
                        <em class="headerFont">Welcome to the ASPIRE Advisor Network</em> <!--specialists in property intelligence and investment. Please login below to gain access to the portal.-->
                        <?php endif; ?>
                    </p>
                    <?php echo form_open('#', array("id" => "frmLogin", "name" => "frmLogin")); ?>
                        <h3 class="headerFont"><strong>Portal Login</strong></h3>
                        <label for="username" class="headerFont" style="text-align:right">Email</label>
                        <input type="text" name="email" value="" id="email" class="required email" />
                        <label for="pword" class="headerFont" style="text-align:right">Password</label>
                        <input type="password" name="password" value="" id="password" class="required" />
                        <input type="submit" class="headerFont" value="login" />   
                    </form> 
                    <ul class="options">
                        <li><a href="#" id="btnReset">Forgotten your password?</a></li>
                        <li><a href="#" id="btnRegister">Register for access.</a></li>                            
                    </ul>               
                </div>
                                           
            <!-- end main content --></div>
            
            <!-- Registration Modal -->
            <div id="registerMember" class="reveal-modal">
                 <h2>Become an Aspire Member</h2>
                 <p>To join the Aspire Network portal and become a member, please enter your details below.</p>
                 <?php echo form_open('login/ajax', array("id" => "frmRegister", "name" => "frmRegister")); ?>
                    <input type="hidden" name="action" value="register" />
                    <label for="register_first_name">First Name <span class="required">*</span></label>
                    <input type="text" id="register_first_name" name="register_first_name" value="" class="required" />
                    
                    <label for="register_last_name">Last Name <span class="required">*</span></label>
                    <input type="text" id="register_last_name" name="register_last_name" value="" class="required" />
                    
                    <label for="register_company_name">Business Name <span class="required">*</span></label>
                    <input type="text" id="register_company_name" name="register_company_name" value="" class="required" /> 
                    
                    <label for="register_email">Email Address <span class="required">*</span></label>
                    <input type="text" id="register_email" name="register_email" value="" class="required email" />
                    
                    <label for="register_phone">Phone</label>
                    <input type="text" id="register_phone" name="register_phone" value="" class="" />
                    
                    <label for="register_user_type">Aspire Role <span class="required">*</span></label>
                    <select id="register_user_type" name="register_user_type" class="required">                                                                                                               
                        <option value="">Choose</option>
                        <option value="<?php echo USER_TYPE_ADVISOR; ?>">Advisor / Real Estate Agent</option>
                        <option value="<?php echo USER_TYPE_SUPPLIER; ?>">Supplier</option>
                    </select>
                    <p style="margin-top: 10px;">
                        <a class="btn inline" id="frmRegisterSubmit">Submit Registration</a>&nbsp;
                        <a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>
                    </p>
                 
                 </form>
                 <a class="close-reveal-modal">&#215;</a>
            </div>
            
            <!-- Password Reset Modal -->
            <div id="passwordReset" class="reveal-modal">
                 <h2>Reset your Aspire Network password</h2>
                 <p>To reset your Aspire Network password, please enter your email address and nominate a new password.  We'll then send you an email to activate your new password.</p>
                 <?php echo form_open('login/ajax', array("id" => "frmReset", "name" => "frmReset")); ?>
                    <input type="hidden" name="action" value="reset_password" />
                    
                    <label for="reset_email">Email Address <span class="required">*</span></label>
                    <input type="text" id="reset_email" name="reset_email" value="" class="required email" />
                    
                    <div class="hint">
                        <p>Please note that your password must:</p>
                        <ul>
                            <li>Be at least 6 characters long.</li>
                            <li>Contain at least 1 upper case letter.</li>
                            <li>Contain at least 1 number.</li>
                        </ul>
                    </div>
                    
                    <label for="reset_password">New Password <span class="required">*</span></label>
                    <input type="password" id="reset_password" name="reset_password" value="" class="required strongpass" />
                    
                    <label for="reset_password_confirm">Confirm Password <span class="required">*</span></label>
                    <input type="password" id="reset_password_confirm" name="reset_password_confirm" value="" class="required" equalto="#reset_password" />                    

                    <p style="margin-top: 10px;">
                        <a class="btn inline" id="frmResetSubmit">Submit</a>&nbsp;
                        <a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>
                    </p>
                 
                 </form>
                 <a class="close-reveal-modal">&#215;</a>
            </div>                        