<body id="contact">
    <div id="wrapper">

        <?php $this->load->view("admin/navigation");?>
        			
        <div id="content">

            <?php $this->load->view("admin/lawyer/navigation"); ?>                        
            
            <form class="plain" id="frmBuilder" name="frmBuilder" action="<?php echo base_url()?>admin/contactsmanager/contact/<?php echo $contacts_id?>"  method="post">
                <input type="hidden" id="folder" value="<?php echo $contacts_id;?>" />
                <input type="hidden" id="contacts_id" value="<?php echo $contacts_id;?>" />

<?php if(isset($builder)) : // We're editing and existing builder.  Show the tabs. ?>

                <br><br>
				<input id="submitbutton2" class="button right" type="button" value="<? echo ($contacts_id == "") ? "Create New Contact": "Update Contact"; ?>" style="margin-top:-40px;" />

                <!-- tabs -->
                <ul class="css-tabs skin2">
                    <li><a href="#">Contact Details</a></li>
                    <!--<li><a href="#">HTML Content</a></li>-->
                    <li><a href="#">Contacts</a></li>
                    <li><a href="#">Comments</a></li>
                    <li><a href="#" id="tabDocument">Documents</a></li>
					<li><a href="#">Transactions</a></li>	
                </ul>   
                
                <!-- panes -->
                <div class="css-panes skin2">
                    <div style="display:block">
<?php endif; ?>

						<div class="left" style="width:45%">
							<label for="first_name">First Name:<span class="requiredindicator">*</span></label>
                    		<input id="first_name" class="required" type="text" value="<? echo ($contacts_id !="") ? $builder->first_name : ""; ?>" name="first_name"/>

							<label for="last_name">Last Name:</label>
                    		<input id="last_name" type="text" value="<? echo ($contacts_id !="") ? $builder->last_name : ""; ?>" name="last_name"/>		
						
							<label for="company_name">Company Name:</label>
                    		<input id="company_name"  type="text" value="<? echo ($contacts_id !="") ? $builder->company_name : ""; ?>" name="company_name"/>
<?php if(isset($builder)) : ?>
                                               		
                    		<label for="count_number_transactions">Number of Transactions:</label>
                    		<input id="count_number_transactions" type="text" value="<? echo $builder->count_number_transactions; ?>" name="count_number_transactions" readonly />
							
							
							
							<label for="billing_address1">Address Line 1:</label> 
							<input type="text" name="billing_address1" id="address" value="<? echo ($contacts_id !="") ? $builder->billing_address1 : ""; ?>"/> 
							
							<label for="billing_address2">Address Line 2:</label> 
							<input type="text" name="billing_address2" id="billing_address2" value="<? echo ($contacts_id !="") ? $builder->billing_address2 : ""; ?>"/> 
											
							<label for="billing_suburb">City/Suburb:</label> 
							<input type="text" name="billing_suburb" id="billing_suburb" value="<? echo ($contacts_id !="") ? $builder->billing_suburb : ""; ?>"/> 
							
							<label for="billing_postcode">Postcode/Zip:</label> 
							<input type="text" name="billing_postcode" id="billing_postcode" value="<? echo ($contacts_id !="") ? $builder->billing_postcode : ""; ?>"/> 
							
							<label for="mobile">Mobile:</label>
                    		<input id="mobile"  type="text" value="<? echo ($contacts_id !="") ? $builder->mobile : ""; ?>" name="mobile"/>
							
							<label for="billing_phone">Phone:</label> 
							<input type="text" name="billing_phone" id="billing_phone" value="<? echo ($contacts_id !="") ? $builder->billing_phone : ""; ?>"/>
							
							<label for="billing_fax">Fax:</label> 
							<input type="text" name="billing_fax" id="billing_fax" value="<? echo ($contacts_id !="") ? $builder->billing_fax : ""; ?>"/>
							
							<label for="email_1">Email 1:</label>
                    		<input id="email_1"  type="text" value="<? echo ($contacts_id !="") ? $builder->email_1 : ""; ?>" name="email_1"/>
							
							<label for="email_2">Email 2:</label>
                    		<input id="email_2"  type="text" value="<? echo ($contacts_id !="") ? $builder->email_2 : ""; ?>" name="email_2"/>
							
							<label for="billing_state_id">State:</label> 
							<select id="billing_state_id" name="billing_state_id">
								<option value="">Choose</option>
								<?php echo $this->utilities->print_select_options($states, "state_id", "name",$builder->billing_state_id); ?> 
							</select>
						
							<label for="billing_country_id">Country:</label> 
							<select id="billing_country_id" name="billing_country_id">
								<option value="1">Australia</option>
							</select>
				
							
							
							
							<label for="postal_address">Postal Address:</label> 
							<input type="text" name="postal_address" id="address" value="<? echo ($contacts_id !="") ? $builder->postal_address : ""; ?>"/>
							
							<label for="postal_suburb">City/Suburb:</label> 
							<input type="text" name="postal_suburb" id="postal_suburb" value="<? echo ($contacts_id !="") ? $builder->postal_suburb : ""; ?>"/> 
							
							<label for="postal_postcode">Postcode/Zip:</label> 
							<input type="text" name="postal_postcode" id="postal_postcode" value="<? echo ($contacts_id !="") ? $builder->postal_postcode : ""; ?>"/> 
							
							<label for="postal_state_id">Postal State:</label> 
							<select id="postal_state_id" name="postal_state_id">
								<option value="">Choose</option>
								<?php echo $this->utilities->print_select_options($states, "state_id", "name",$builder->postal_state_id); ?> 
							</select>
							
							
							
<?php endif; ?>

						</div>
						
<?php if(isset($builder)) : ?>

                        <div class="left" style="width:50%">
                            <label for="summary">Summary</label>
                            <textarea name="summary" id="summary"><? echo ($contacts_id !="") ? $builder->summary : ""; ?></textarea>
                            
                            <?php if($builder) : ?>
                            <p>Last Modified: <?=$this->utilities->isodatetime_to_ukdate($builder->last_modified); ?></p>
                            <?php endif; ?>
						</div>

						<label for="contact_type">Contact Type:</label> 
							<select id="contact_type" name="contact_type">
								<option value="">Choose</option>
								
								<?php echo $this->utilities->print_select_options($contact_types, "contact_type_name", "contact_type_name",$builder->contact_type); ?> 
							</select>
					
						<div class="clear"></div>
						
						
                        
                        <!--<label for="builder_logo">Panel Logo</label>
                        <div class="logo_img">
                        
                        <?php //if (!empty($builder->lawyer_logo)) : ?>
                            <img id="builder_logo_img" src="<?php  //echo base_url().$builder->lawyer_logo; ?>" width="250" class="<?php //echo (empty($builder->lawyer_logo)) ?  "hidden" : ""; ?>" />
                      	<?php //endif; ?>
                      	
                      	</div>
    					<input class="<?php //echo ($builder->lawyer_logo == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Logo" id="delete_logo" />
    					
    					<div id="builder_logo_upload" class="showif <?php //echo (!empty($builder->lawyer_logo)) ?  "hidden" : ""; ?>"></div>-->
                    
<?php endif; ?>

                        <div class="clear"></div>
                        
                        <div class="left" style="padding-top: 20px;">
							<input type="checkbox" name="enabled" value="1" class="left" <? echo ($contacts_id !="") ? (($builder->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Enabled</label> 
						</div>
						
						<div class="clear"></div>
						
<?php if(isset($builder)) : ?>

					</div><!-- END first tab -->
					
                 <!--   <div>
                        <label for="content">Summary</label><br />
                        <textarea id="wysiwyg" cols="20" rows="10" name="builder_content" style="width:880px;height:300px" class="editor"><? //echo ($builder_id !="") ? $builder->lawyer_content : "" ?></textarea>
                        
                        <div class="clear"></div>
                        
                        <label for="history">History</label><br />
                        <textarea id="wysiwyg2" cols="20" rows="10" name="history" style="width:880px;height:300px" class="editor"><? //echo ($builder_id !="") ? $builder->history : "" ?></textarea>
                        
                    </div>--><!-- END htmk content tab -->
                    
                    <div>
                    <?php $this->load->view("admin/contacts/main", array("foreign_id" => $contacts_id, "type" => "builder_contact", "contacts" => $contacts, "states" => $states)); ?>
                </div> <!-- END contacts tab -->
                    
                    <div>
                        <?php $this->load->view("admin/comments/main", array("foreign_id" => $contacts_id, "type" => "builder_comment", "comments" => $comments)); ?>
                        
                        <div class="clear"></div>
                    </div><!-- END comments tab -->
                    
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
                            <? $this->load->view('admin/lawyer/document_listing',array('files'=>$documents,'pages_no' => count($documents) / $documents_records_per_page)); ?>
                        </div>
                        
                        <div class="clear"></div>
                        <div id="controls">
                            <div class="right">
                                <input class="button" type="button" value="Delete Selected Files" id="delete_builder_files" />
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div>
               </div><!-- end Documents reset tab -->            
                
				<!-- start transaction tab -->
               <div>
                    
                    <div class="clear"></div>
                    <br/>
                    
                    <div id="files_listing">
                        <div id="page_listing">
                            <? $this->load->view('admin/lawyer/transaction_listing',array('files'=>$documents,'pages_no' => count($documents) / $documents_records_per_page)); ?>
                        </div>
                        
                        <div class="clear"></div>
                        
                    </div>
							
               </div><!-- end transaction reset tab -->	

				
                </div>
<?php endif; ?>

                <div class="clear"></div>
    
                <br/><br/>
                          
                <label for="heading">&nbsp;</label> 
                <input id="button" type="submit" value="<? echo ($contacts_id == "") ? "Create New Contact": "Update Contact"; ?>" /><br/>                    
            </form>
            
         <br/>
         <?php $this->load->view("admin/lawyer/navigation"); ?>  
		 