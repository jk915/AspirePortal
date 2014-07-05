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
                        <li><a href="<?php echo base_url(); ?>contacts">My Contacts</a></li>
                        <li>Contact Details</li>
                    </ul>
               
					<div class="mainCol" style="width:940px;">
						<ul class="tabNav" id="tabNav">
							<li id="li_tab1"  class="active"><a href="#" id="tabContact" class="active">Contacts Details</a></li>
							<?php
							if($builder)
							{
							?>
							<li id="li_tab2" ><a>Contacts</a></li>
							<li id="li_tab3" ><a>Notes</a></li>
							<li id="li_tab4" ><a href="#" id="tabDocument">Documents</a></li>
							<li id="li_tab5" ><a>Transactions</a></li>
				            <?php
							}
							?>
				        </ul>
                        
						<?php echo form_open('contacts/ajax', array("id" => "frmLeadDetail", "name" => "frmLeadDetail", "class" => "block")); ?>
                        <ul id="tabs">
							
                            <li>
                               <div id="tab1">
                       
                                <ul class="propertyListing" style="padding:15px 0 0 10px;">
								
									<input type="hidden" id="contacts_id" name="contacts_id" value="<?php echoifobj($builder, "contacts_id"); ?>" />
									<input type="hidden" id="action" name="action" value="update_contact" />
									<h3>Contact Details</h3>  
									
									<div class="error"><h4>Please complete the following fields before submitting:</h4></div> 
									<div class="success"><h4>Your Contact's information was updated successfully.</h4></div>

									<fieldset>
										<label for="first_name">First Name<span class="requiredindicator">*</span></label>
										<input type="text" class="required" name="first_name" value="<?php echoifobj($builder, "first_name"); ?>" id="first_name" /> 	
										
										<label for="last_name">Last Name</label>
										<input type="text" name="last_name" value="<?php echoifobj($builder, "last_name"); ?>" id="last_name" /> 
										
										<label for="company_name">Company Name </label>
										<input type="text" name="company_name" value="<?php echoifobj($builder, "company_name"); ?>" id="company_name" class="required" />
										
										<label for="mobile">Mobile</label>
										<input type="text" name="mobile" value="<?php echoifobj($builder, "mobile"); ?>" id="mobile" />                                
										
										<label for="billing_phone">Phone</label>
										<input type="text" name="billing_phone" value="<?php echoifobj($builder, "billing_phone"); ?>" id="billing_phone" class="secondary_email"  />

										<label for="billing_fax">Fax</label>
										<input type="text" name="billing_fax" value="<?php echoifobj($builder, "billing_fax"); ?>" id="billing_fax" />
										
										<label for="billing_address1">Address Line 1</label>
										<input type="text" name="billing_address1" value="<?php echoifobj($builder, "billing_address1"); ?>" id="billing_address1" />
										
										<label for="billing_address2">Address Line 2</label>
										<input type="text" name="billing_address2" value="<?php echoifobj($builder, "billing_address2"); ?>" id="billing_address2" />
										
										<label for="billing_suburb">City/Suburb</label>
										<input type="text" name="billing_suburb" value="<?php echoifobj($builder, "billing_suburb"); ?>" id="billing_suburb" />     
										
										<label for="billing_postcode">Postcode/Zip</label>
										<input type="text" name="billing_postcode" value="<?php echoifobj($builder, "billing_postcode"); ?>" id="billing_postcode" class="email"  />
										
										<label for="state">State</label>
										<select name="state" id="state">
											<option value="">Choose</option>
											<?php echo $this->utilities->print_select_options($states, "state_id", "name", ($builder) ? $builder->state_id : ""); ?>
										</select>
										
										<label for="country">Country</label>
										<select name="country"> 
											<option selected="selected" value="1">Australia</option>
										</select>
										
									</fieldset> 
									<fieldset>
										
										<label for="contact_type">Contact Type:</label> 
										<select id="contact_type" name="contact_type">
										<option value="">Choose</option>
										
										<?php echo $this->utilities->print_select_options($contact_types, "contact_type_name", "contact_type_name",$builder->contact_type); ?> 
										</select>
										
										<label for="count_number_transactions">Number of Transactions</label>
										<input type="text" name="count_number_transactions" value="<?php echoifobj($builder, "count_number_transactions"); ?>" id="count_number_transactions" />
										
										<label for="postal_address">Postal Address</label>
										<input type="text" name="postal_address" value="<?php echoifobj($builder, "postal_address"); ?>" id="postal_address" />
										
										<label for="postal_suburb">City/Suburb</label>
										<input type="text" name="postal_suburb" value="<?php echoifobj($builder, "postal_suburb"); ?>" id="postal_suburb" />
										
										<label for="postal_postcode">Postcode/Zip</label>
										<input type="text" name="postal_postcode" value="<?php echoifobj($builder, "postal_postcode"); ?>" id="postal_postcode" />
										
										<label for="postal_state_id">Postal State</label>
										<select name="postal_state_id" id="postal_state_id">
											<option value="">Choose</option>
											<?php echo $this->utilities->print_select_options($states, "state_id", "name", ($builder) ? $builder->postal_state_id : ""); ?>
										</select>
										
										<label for="email_1">Email 1</label>
										<input type="text" name="email_1" value="<?php echoifobj($builder, "email_1"); ?>" id="email_1" />    
										
										<label for="email_2">Email 2</label>
										<input type="text" name="email_2" value="<?php echoifobj($builder, "email_2"); ?>" id="email_2" />
										
										<label for="summary">Summary</label>
										<textarea id="summary" name="summary" cols="10" rows="6"><?php echoifobj($builder, "summary"); ?></textarea>
										
										
									</fieldset>
									<fieldset>
									<div class="sidebar">
										<div class="right" style="width:100%; float:left;">
										<label for="upload_hero_image">Hero Image:</label>
										<?php
										if(empty($builder->contacts_logo))
										{
										?>
										
										<div id="upload_hero_image"></div>
                    </div>
					<?php
					}
					else
					{
					?>
										<img id="" src="<?php  echo empty($builder->contacts_logo) ? '#' : base_url().$builder->contacts_logo . "_thumb.jpg"; ?>" width="250" class="<?php echo empty($builder->contacts_logo) ?  "hidden" : ""; ?>" />
              	</div>
              	<div class="clear"></div>
				<input class="<?php echo ($builder == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Logo" id="delete_logo" />
                <div id="logo_upload" class="showif <?php echo (!empty($builder->contacts_logo)) ?  "hidden" : ""; ?>"></div>
					
<?php
}
?>					
										
									
                        <?php if ($builder)
						{
						?>
                        <div class="top-margin20" style="background-color: #FFFFC0; padding: 5px; width:100%; float:left;">
                            <p>
                                
								 <b>Last Modified:</b> <?php if($builder): ?>
<?=$this->utilities->isodatetime_to_ukdate($builder->last_modified); ?>
<?php endif; ?><br/> 
                                

								<b>Date Created:</b> <?php echo ( empty($user->created_dtm)) ? "NA" : date("d/m/Y h:i:s A", strtotime($user->created_dtm)); ?><br/>
								<b>Created By:</b> <?php echo ( empty($username)) ? "NA" : $username; ?><br/>
                            </p>
						</div> <br/>
						<h3>Actions</h3>                    
                        <ul>
                            <?php if(in_array($utid, array(USER_TYPE_ADVISOR))) : ?>
                            <li><a href="javascript:;" data-reveal-id="deletePartnerConfirm">Delete Contact</a></li>
                            <?php endif; ?>
						</ul>
						<div id="deletePartnerConfirm" class="reveal-modal">
                             <h2>Confirmation Required</h2>
                             <p>Are you sure you want to delete this Contact? This action is not reversible.</p>
							 <p>
							 	<div class="error delete_error"><h4>Please complete the following fields before submitting:</h4></div>
                             	<a class="btn inline delete_contact" href="javascript:;" contacts_id="<?php echoifobj($builder, "contacts_id"); ?>" action="<?php echo site_url('contacts/ajax')?>" action_name="delete_contact">Yes, delete this Contact</a>
                             	&nbsp;<a class="btn secondary inline close-reveal" href="javascript:;">no, cancel</a>
							 </p>
                             <a class="close-reveal-modal">&#215;</a>
                        </div>
                        <br />
                        <?php
						} else {
						?>
						<div class="info" style="width:100%; float:left;">
                        <h3>Information</h3>
                        <p>To add a new Contact to your account, please enter their  details and then hit the 'Save Contact' button under the form (bottom right).</p></diV>
						<?php
						} 
						?>						
                        
						</div>
									</fieldset>
									
									<div class="clear"></div>                          
									
									<p><a href="<?php echo base_url(); ?>contacts">&laquo; Back</a></p>
									                           
								
								</ul>
                         
							</div>
                            </li>
                            <li>
                                <div id="tab2" style="min-height:450px;">
<table cellspacing="0" width="100%" class="left contact_listing" style="padding:0 0 0 10px;"> 
<thead>
    <tr>
        <th width="10%">ID</th>
        <th align="left">Contact Name</th>                            
        <th align="left">Position</th>                            
        <th align="left">Phone</th>
        <th width="10%">Delete</th>                            
    </tr>
</thead>
<tbody>

    <?php $i = 0;?>
<?php if ($contacts) : ?>
<?php foreach ($contacts->result() AS $contact) : ?>
    <?php
        if($i++ % 2==1) $rowclass = "admintablerow";
        else  $rowclass = "admintablerowalt";
    ?>
    <tr class="<? print $rowclass;?>">
        <td class="admintabletextcell" align="center"><?php echo $contact->contact_id;?></td>
        <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="<?php echo $contact->contact_id;?>" class="editcontact"><?php echo $contact->name;?></a></td>
        <td class="admintabletextcell" style="padding-left:12px;"><?php echo $contact->position;?></td>
        <td class="admintabletextcell" style="padding-left:12px;"><?php echo $contact->phone;?></td>
        <td class="center"><input type="checkbox" class="contacttodelete" value="<?php echo $contact->contact_id;?>" /></td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>



<div class="clear"></div>

<a href="javascript:;" class="button right center" id="deletecontact" style="margin-left:10px;">Delete</a>
<a href="javascript:;" class="button right center" id="addnewcontact">Add new</a>
<br />
<div class="clear"></div>

<div id="formaddcontact" style="display:none;">
    <input type="hidden" name="contact_id" id="contact_id"/>
    
    <fieldset class="left" style="width: 300px;">
        <label for="contact_name">Contact Name:<span class="requiredindicator">*</span></label>
        <input type="text" id="contact_name" name="contact_name"/>
        
        <label for="contact_position">Position:</label>
        <input type="text" id="contact_position" name="contact_position"/>                            
        
        <label for="contact_phone">Phone:</label>
        <input type="text" id="contact_phone" name="contact_phone"/>
        
        <label for="contact_mobile">Mobile:</label>
        <input type="text" id="contact_mobile" name="contact_mobile"/>
        
        <label for="contact_fax">Fax:</label>
        <input type="text" id="contact_fax" name="contact_fax"/>
        
        <label for="contact_email">Email:</label>
        <input type="text" id="contact_email" name="contact_email"/>
    </fieldset>
    
    <fieldset class="left" style="width: 300px;"> 
        <label for="contact_address">Address:</label>
        <input type="text" id="contact_address" name="contact_address"/>
        
        <label for="contact_suburb">Suburb:</label>
        <input type="text" id="contact_suburb" name="contact_suburb"/>                       
        
        <label for="contact_postcode">Postcode:</label>
        <input type="text" id="contact_postcode" name="contact_postcode"/>
        
        <label for="contact_state_id">State:</label>
        <select id="contact_state_id" name="contact_state_id">
            <option value="">Choose</option>
            <?php echo $this->utilities->print_select_options($states, "state_id", "name"); ?> 
        </select>                             
        
        <label for="contact_comment">Comment:</label>
        <textarea id="contact_comment" name="contact_comment"></textarea>                                
    </fieldset>                            
    
    <div class="clear"></div><br />
    <a href="javascript:;" class="button left center savecontact">Save</a>
	
</div>

</div>
</li>

                            <li>
                                <div id="tab3" style="min-height:450px;">
<table cellspacing="0" width="100%" class="left commentlisting" style="padding:0 0 0 10px;"> 
<thead>
    <tr>
        <th width="10%">ID</th>
        <th align="left">Comment</th>
        <th width="10%">Delete</th>
    </tr>
</thead>
<tbody>

    <?php //$this->load->view("admin/comments/list", array("comments" => $comments)); ?>
	<?php if ($comments) : ?>
<?php foreach ($comments->result() AS $index=>$comment) : ?>
    <tr id="comment_<?php echo $comment->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
        <td class="admintabletextcell" align="center"><?php echo $comment->id;?></td>
        <td class="admintabletextcell" style="padding-left:12px;">
            <span style="font-weight:bold"><?php echo trim("$comment->first_name $comment->last_name")?></span>
            @ <em style="font-style:italic;"><?php echo date('d/m/Y h:i A', $comment->ts_added)?></em>:<br />
            "<?php echo nl2br($comment->comment)?>"
        </td>
        <td class="center"><input type="checkbox" class="commenttodelete" value="<?php echo $comment->id;?>" /></td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>
</tbody>
</table>

<a href="javascript:;" class="button right center" id="deletecomment">Delete</a>
<a href="javascript:;" class="button right center" id="newcomment">New Comment</a>
<div class="clear"></div>

<div id="formnewcomment" style="display:none;">
    <label for="comment">Comment:<span class="requiredindicator">*</span></label>
    <textarea id="comment" style="width:400px;"></textarea>
    <input type="hidden" id="comment_id"/>
    
    <div class="clear"></div><br />
    <a href="javascript:;" class="button left center savecomment">Save</a>
</div>
</div>
                            </li>
							<li><div id="tab4" style="min-height:450px;" >
					<div class="right" style="padding-right:10px">
                        <label for="upload_document">Upload a new document</label>
                       <!--<input type="file" name="upload_file" id="upload_file" />    -->
						<div id="upload_document"></div>
                    </div>
                    <div class="clear"></div>
                    <br/>
					
					<table cellspacing="0" class="cmstable" style="padding:0 0 0 10px;"> 
            <tr>
                <th>File Name</th>  
                <th>Description</th>
                <th>Download</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
<?php			
$i = 0;
if($documents)
{
    foreach($documents->result() as $file)
    {                                                                                    
        $filename = $file->document_name;
        $file_path = $file->document_path;
        
        if (file_exists($file_path))
        {
            $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";        
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $filename;?></td>
                    <td class="admintabletextcell"><span class="document_description" id="<?php echo $file->id; ?>">
					<?php 
					if($file->document_description)
					{					
						echo $file->document_description;
					}
					else
					{
						echo '<a href="javascript:;" id="description"  class="'.$file->id.'">click here to add description text </a>';
					}					?></span></td>
                    <td class="admintabletextcell"><a class="download_userfile" href="<?php echo base_url($file_path);?>" type="documents">Click to download</a></td>
                    <td class="center"><input type="checkbox" class="user_docstodelete" value="<?php echo $file->id;?>" /></td>
                </tr>                   
            <?php
        }
    }
}
        ?>
</table>
	<div id="controls">
                            <div class="right">
                                <input class="button" type="button" value="Delete Selected Files" id="delete_builder_files" />
                            </div>
                        </div>	
						
<div id="formdescription" style="display:none;">
    <label for="description">Description:<span class="requiredindicator">*</span></label>
    <textarea id="newdescription" style="width:400px;"></textarea>
    <input type="hidden" id="description_id"/>
    
    <div class="clear"></div><br />
    <a href="javascript:;" class="button left center savedescription">Save</a>
</div>
</div></li>
<li><div id="tab5" style="min-height:450px;">
<table cellspacing="0" class="cmstable" style="padding:15px 0 0 10px;"> 
            <tr>
                <th style="text-align:center">Property Address</th>  
                <th style="text-align:center">Seller</th>
                <th style="text-align:center">Purchaser</th>
                <th style="text-align:center">Date Reserved</th>
				<th style="text-align:center">Status</th>		
            </tr>
</table>
</div></li>
                        </ul>
						<div class="clear"></div>
						<br/>
						<br/>
						<label for="heading">&nbsp;</label>
<input type="submit" value="Save Contact" />
</form>

                    </div> 
