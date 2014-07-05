<body id="contact" >   
    <div id="wrapper">
    
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/settingsmanager/navigation"); ?>            
            <?php 
                if(isset($message) && $message != "")
                {
                    ?>
                    <p><?php echo $message ?></p>    
                    <?php
                }
            ?> 
            <h2>Global Settings</h2>    
            
            <!-- tabs -->
            <ul class="css-tabs skin2">
                <li><a href="#">Owner Details</a></li>      
                <li><a href="#">Commerce Settings</a></li>
                <li><a href="#">Contacts &amp; Notifications</a></li>          
            </ul>   
            
            <!-- panes -->
            <div class="css-panes skin2">
                                
                <div style="display:block">
                	   
                    <form class="plain" id="frmSettings" name="frmSettings" action="<?php echo base_url()?>admin/settingsmanager"  method="post">
                                           
                        <label for="company_name">Company Name:</label> 
                        <input type="text" name="company_name" id="company_name" value="<?php if(isset($owner_details["company_name"])) echo $owner_details["company_name"]; ?>" /><div class="clear"></div>

                        <label for="address1">Address Line 1:</label> 
                        <input type="text" name="address1" id="address1" value="<?php if(isset($owner_details["address1"])) echo $owner_details["address1"]; ?>"/>

                        <label for="address2">Address Line 2:</label> 
                        <input type="text" name="address2" id="address2" value="<?php if(isset($owner_details["address2"])) echo $owner_details["address2"]; ?>"/>
                        <div class="clear"></div>

                        <div class="left">
                            <label for="suburb">Suburb:</label> 
                            <input type="text" name="suburb" id="suburb" value="<?php if(isset($owner_details["suburb"])) echo $owner_details["suburb"]; ?>" class="small"/>
                        </div>
                        
                        <div class="left">    
                            <label for="postcode">Postcode:</label> 
                            <input type="text" name="postcode" id="postcode" value="<?php if(isset($owner_details["postcode"])) echo $owner_details["postcode"]; ?>" class="small"/>
                        </div>    
                        <div class="clear"></div>

                        <div class="left">
                            <label for="state">State:</label> 
                            <select name="state" id="state" class="small_select">
                                <?php echo $this->utilities->print_select_options($states,"name", "name", (isset($owner_details["state"])) ? $owner_details["state"] : ""); ?>
                            </select><div class="clear"></div> 
                        </div>
                        <div class="left">
                            <label for="country">Country</label>
                            <select name="country" id="country" class="small_select">
                                <?php echo $this->utilities->print_select_options($countries,"name", "name", (isset($owner_details["country"])) ? $owner_details["country"] : ""); ?>                            
                            </select>
                        </div>
                        
                        <div class="clear"></div>
                        
                        <div class="left">
                            <label for="email">Public Email:</label> 
                            <input type="text" name="email" id="email" value="<?php if(isset($owner_details["email"])) echo $owner_details["email"]; ?>" />
                        </div>
                        
                        <div class="clear"></div>  
                        
                        <div class="left">
                            <label for="phone">Phone:</label> 
                            <input type="text" name="phone" id="phone" value="<?php if(isset($owner_details["phone"])) echo $owner_details["phone"]; ?>" />
                        </div>
                        
                        <div class="clear"></div>  
                        
                        <div class="left">
                            <label for="fax">Fax:</label> 
                            <input type="text" name="fax" id="fax" value="<?php if(isset($owner_details["fax"])) echo $owner_details["fax"]; ?>" />
                        </div>                        
                        
                        <div class="clear"></div>                                                
                        
                        <div class="left">
                            <label for="skype">Skype ID:</label> 
                            <input type="text" name="skype" id="skype" value="<?php if(isset($owner_details["skype"])) echo $owner_details["skype"]; ?>" />
                        </div>
                        
                        <div class="clear"></div> 
                        
                        <div class="left">
                            <label for="facebook">Facebook URL:</label> 
                            <input type="text" name="facebook" id="facebook" value="<?php if(isset($owner_details["facebook"])) echo $owner_details["facebook"]; ?>" />
                        </div>
                        
                        <div class="clear"></div> 
                        
                        <div class="left">
                            <label for="twitter">Twitter URL:</label> 
                            <input type="text" name="twitter" id="twitter" value="<?php if(isset($owner_details["twitter"])) echo $owner_details["twitter"]; ?>" />
                        </div>
                        
                        <div class="clear"></div> 
                        
                        <div class="left">
                            <label for="vimeo">Vimeo URL:</label> 
                            <input type="text" name="vimeo" id="vimeo" value="<?php if(isset($owner_details["vimeo"])) echo $owner_details["vimeo"]; ?>" />
                        </div>
                        
                        <div class="clear"></div> 
                        
                        <div class="left">
                            <label for="youtube">Youtube URL:</label> 
                            <input type="text" name="youtube" id="youtube" value="<?php if(isset($owner_details["youtube"])) echo $owner_details["youtube"]; ?>" />
                        </div>
                        
                        <div class="clear"></div> 
                        
                        <div class="left">
                            <label for="linkedin">LinkedIn URL:</label> 
                            <input type="text" name="linkedin" id="linkedin" value="<?php if(isset($owner_details["linkedin"])) echo $owner_details["linkedin"]; ?>" />
                        </div>
                        
                    	<div class="clear"></div> 
                        
                        <div class="left">
                            <label for="analytics_id">Google Analytics ID:</label> 
                            <input type="text" name="analytics_id" id="analytics_id" value="<?php if(isset($owner_details["analytics_id"])) echo $owner_details["analytics_id"]; ?>" />
                        </div>                                                                                              
                        
                        <div class="clear"></div>
                        
                        <div class="left">
                            <label for="mailchimp_api_key">Mailchimp Api Key:</label> 
                            <input type="text" name="mailchimp_api_key" id="mailchimp_api_key" value="<?php if(isset($owner_details["mailchimp_api_key"])) echo $owner_details["mailchimp_api_key"]; ?>" />
                        </div>
                        
                    	<div class="clear"></div>
                    	
                    	<div class="left">
                            <label for="mailchimp_list_id">Mailchimp List Id:</label> 
                            <input type="text" name="mailchimp_list_id" id="mailchimp_list_id" value="<?php if(isset($owner_details["mailchimp_list_id"])) echo $owner_details["mailchimp_list_id"]; ?>" />
                        </div>
                        
                    	<div class="clear"></div>
                    	
                        <br/>                            
                        <input type="checkbox" name="enabled" value="1" class="left" <?php echo (isset($owner_details["enabled"])) ? (($owner_details["enabled"] == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Website(s) are enabled</label> 
                        <br/>
                        
                        <input type="hidden" name="tax_id" id="hidden_tax_id" />
                        <input type="hidden" name="tax_gst" id="hidden_tax_gst" />
                        <input type="hidden" name="payment_gateway_id" id="hidden_payment_gateway" />
                        <input type="hidden" name="gateway_id" id="hidden_gateway_id" />
                                                
                    </form>
                      
                </div><!-- END first tab -->    
                
                <div><!-- Start second tab -->
                    
                    <form class="plain" id="frmSettingsTax" name="frmSettingsTax" action="<?php echo base_url()?>settingsmanager"  method="post">
                    
	                	<label for="tax_id">TAX ID / ABN:</label> 
	                    <input type="text" id="tax_id" name="tax_id" value="<?php if(isset($owner_details["tax_id"])) echo $owner_details["tax_id"]; ?>" class="required" />
	                    
	                    <label for="tax_gst">TAX / GST %:</label> 
	                    <input type="text" id="tax_gst" name="tax_gst" value="<?php if(isset($owner_details["tax_gst"])) echo $owner_details["tax_gst"]; ?>" class="required" />
	                    
	                    <label for="payment_gateway">Payment Gateway:</label> 
	                    <select id="payment_gateway" name="payment_gateway">
	                        <?php echo $this->utilities->print_select_options($payment_gateways, "payment_gateway_id", "name", ( isset( $owner_details["payment_gateway_id"] ) ? $owner_details["payment_gateway_id"] : '' ) ); ?> 
	                    </select>
	                    
	                    <label for="gateway_id">GATEWAY ID</label> 
	                    <input type="text" id="gateway_id" name="gateway_id" value="<?php if(isset($owner_details["gateway_id"])) echo $owner_details["gateway_id"]; ?>" class="required" />	                
                   </form>
                        
	            </div><!-- End second tab -->
                                
	            <div><!-- Start third -->
	                    <form id="frmAddContacts" name="frmAddContacts" action="#" method="post">
	                    
	                        <label for="first_name">First Name:</label> 
	                        <input type="text" id="first_name" name="first_name" value="" class="required" />
	                 
	                        
	                        <label for="last_name">Last Name:</label> 
	                        <input type="text" id="last_name" name="last_name" value="" class="required" />
	                        
	                        <label for="email">Email Address:</label> 
	                        <input type="text" id="contact_email" name="contact_email" value="" class="required email" />
	                        
	                        <?php /*<label for="website_id">Website:</label>
	                        <select name="website_id" id="website_id">
	                            <?php echo $this->utilities->print_select_options($websites, "website_id", "website_name"); ?> 
	                        </select>
	                                */
	                        ?>            
	                        <br/>
	                        <label>&nbsp;</label>
	                        <input class="button" id="add_contact" type="button" value="Add Contacts" /><div class="clear"></div>
	                        <br/>
	                     
	                    </form>   
	                    
	                    <div id="contact_listing">
	                        <?php $this->load->view('admin/settingsmanager/contact_listing.php',array('contacts'=>$contacts)); ?>
	                    </div>
	                    
	                    <?php if($contacts && $contacts->num_rows() > 0)
	                    {
	                    ?>
	                    <div id="controls">
	                        <div class="right">
	                            <input class="button" type="button" value="Update Contacts" id="update_contacts" />&nbsp;
	                            <input class="button" type="button" value="Delete Contacts" id="delete" />
	                        </div>
	                        <div class="clear"></div>
	                    </div>    
	                    <?php
	                    }
	                    ?>
	                    
	                </div><!-- END third tab -->
            </div>    
            <br/>
            <br/>
                      
            <label for="heading">&nbsp;</label> 
            <input id="button" type="button" value="Save Settings" /><br/>                
           
<p>&nbsp;</p>
<?php $this->load->view("admin/settingsmanager/navigation"); ?>
