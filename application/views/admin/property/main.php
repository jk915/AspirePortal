<body id="contact" >   
    <div id="wrapper">
        <? $this->load->view("admin/navigation");?>
        <div id="content">
            <? $this->load->view("admin/property/navigation"); ?>                        

<form class="plain" id="frmProperty" name="frmProperty" action="<?=base_url()?>admin/propertymanager/property/<?=$property_id?>"  method="post">
    <input type="hidden" id="folder" value="<? echo $property_id;?>" />
    <input type="hidden" id="property_id" value="<?=$property_id;?>" />

    <h2>Property Details</h2>    

    <?php if(isset($property)) : ?>
	<input id="submitbutton2" class="button right" type="button" value="<? echo ($property_id == "") ? "Create New Property": "Update Property"; ?>" style="margin-top:-40px;" />
    <?php endif; ?>

<?php if(isset($property)) : // We're editing and existing property.  Show the tabs. ?>

    <!-- tabs -->
    <ul class="css-tabs skin2">
        <li><a href="#" class="tab">Address</a></li>
        <li><a href="#" class="tab">Description</a></li>
        <li><a href="#" class="tab">Specifications</a></li>
        <li id="tabStage" class="<?php echo (isset($property) && $property->status != 'available') ? '' : 'hidden';?> stage tab"><a href="#">Tracker</a></li>
        <li><a href="#" id="tabDocument" class="tab">Documents</a></li>
        <li><a href="#" id="tabGallery" class="tab">Gallery</a></li>
        <li><a href="#" id="tabNote" class="tab">Notes</a></li>                    
        <li><a href="#" id="tabAdvisor" class="tab">Advisor</a></li>
		<li><a href="#" id="tabHistory" class="tab">History</a></li>
    </ul>   

    <!-- panes -->
    <div class="css-panes skin2">
        <div style="display:block">

<?php endif; ?>    
            <div class="left" style="width: 45%;">   
            
                <label for="title">Title:<span class="requiredindicator">*</span></label>
                <input id="title" class="required" type="text" value="<? echo ($property_id !="") ? $property->title : "" ?>" name="title"/>   

                <label for="lot">Lot:<span class="requiredindicator">*</span></label>
                <input id="lot" class="required" type="text" value="<? echo ($property_id !="" && $property->lot != "-1") ? $property->lot : ""; ?>" name="lot"/>
                
                <label for="address">Address:<span class="requiredindicator">*</span></label>
                <input id="address" class="required" type="text" value="<? echo ($property_id !="") ? $property->address : "" ?>" name="address"/>   

                <label for="area_id">Area:<span class="requiredindicator">*</span></label>
                <select id="area_id" name="area_id" class="required">
                    <? if($areas):?>
                        <?=$this->utilities->print_select_options($areas,"area_id","area_name",($property != "") ? $property->area_id : ""); ?>
                    <? endif; ?>
                </select>
                
                <label for="postcode">Postcode:<span class="requiredindicator">*</span></label>
                <input id="postcode" class="required" type="text" value="<? echo ($property_id !="") ? $property->postcode : "" ?>" name="postcode"/>


                <label for="state_id">State:<span class="requiredindicator">*</span></label>
                <select id="state_id" name="state_id" >
                    <?php 
                    if($states)
                        echo $this->utilities->print_select_options($states,"state_id","preferredName",($property_id !="") ? $property->state_id : ""); 
                    ?>
                </select>
                
                				
				<label for="region_id">Regions:<span class="requiredindicator">*</span></label>
                <select id="region_id" name="region_id" class="required" >
                    <? if($regions):?>
                        <?=$this->utilities->print_select_options($regions,"region_id","region_name",($property != "") ? $property->region_id : ""); ?>
                    <? endif; ?>
                </select>

                <label for="design">House Design:</label>
                <input id="design" type="text" value="<? echo ($property_id !="") ? $property->design : "" ?>" name="design"/>   
            	
                <?php /*
                <label for="title_due_date">Title Due Date: (yyyymm)</label>
                <input id="title_due_date" class="digits" type="text" value="<? echo ($property_id !="") ? $property->title_due_date : "" ?>" name="title_due_date"/>
                */ ?>
                
<?php if(isset($property)) : ?>
                
                <div class="clear"></div>
                
                <label for="titled" style="padding-bottom:10px;">Property Titled:</label>
                <input type="radio" name="titled" id="titled" value="1" <? echo ($property_id != '' && $property->titled == 1) ? 'checked="checked"' : '' ?>/> Yes
                <input type="radio" name="titled" id="titled" value="0" <? echo ($property_id != '' && $property->titled == 0) ? 'checked="checked"' : '' ?>/> No
                
                <span class="estimated_date hidden">
                    <label>Estimated Titled Date: (mm/yyyy)</label>
                    <input id="estimated_date" type="text" value="<? echo ($property_id !="") ? $property->estimated_date : "" ?>" name="estimated_date"/>
                </span>      
            
                <label for="status">Status: <span class="status_info"><? echo ($property_id !="") ? ucfirst($property->status) : "available" ?></span> <a href="#status_area" id="changestatus">Change Status</a></label> 
                
                <div class="top-margin20"></div>
                <h3>Actions</h3>
                <ul>
                    <li><a href="<?php echo base_url();?>admin/propertymanager/clone_property/<?php echo $property->property_id;?>" class="clone">Clone / Copy Property</a></li>
                    <li><a href="<?php echo base_url();?>admin/propertymanager/brochure/<?php echo $property->property_id;?>" class="print" target="_blank">Print Report</a></li>
                </ul>                    
                
                <div id="frm_change_status" style="display:none; height:280px;">
                    <div style="font-size:15px;font-weight:bold;color:#8B0304" class="property_title">
                        <?php echo trim("$property->lot $property->address $property->suburb")?>
                    </div>
                    
                    <label for="status">Change Status To:<span class="requiredindicator">*</span></label>           
                    
                    <select class="status" name="status">
                    <?php foreach ($status as $key => $value) : ?>
                        <option value="<?php echo $key?>"<?php echo ($key==$property->status) ? ' selected="selected"' : ''?>><?php echo $value?></option>
                    <?php endforeach; ?>
                    </select>
                    
                    <div class="status_user_area">
                    
                        <label for="advisor_id">Advisor: <span class="requiredindicator">*</span></label>
                        <select name="advisor_id" class="advisor_id">
                            <option value="-1">- Select Advisor</option>
                            <?php if ($advisors) : ?>
                                <?php foreach ($advisors->result() AS $advisor) : ?>
                                    <option value="<?php echo $advisor->user_id?>" <?php echo ($property->advisor_id == $advisor->user_id) ? 'selected="seleted"' : '' ?>>
                                        <?php echo trim($advisor->first_name.' '.$advisor->last_name)?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        
                        <label for="partner_id">Partner:</label>
                        <select name="partner_id" class="partner_id">
                            <option value="">None</option>
                            <?php if ($partners) : ?>
                                <?php foreach ($partners->result() AS $partner) : ?>
                                    <option value="<?php echo $partner->user_id?>" <?php echo ($property->partner_id == $partner->user_id) ? 'selected="seleted"' : '' ?>>
                                        <?php echo trim($partner->first_name.' '.$partner->last_name)?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        
                        <label for="investor_id">Investor:</label>
                        <select name="investor_id" class="investor_id">
                            <option value="">None</option>
                            <?php if ($investors) : ?>
                                <?php foreach ($investors->result() AS $investor) : ?>
                                    <option value="<?php echo $investor->user_id?>" <?php echo ($property->investor_id == $investor->user_id) ? 'selected="seleted"' : '' ?>>
                                        <?php echo trim($investor->first_name.' '.$investor->last_name)?>
                                    </option>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </select>
                        
                    </div>
                    <div class="clear"></div><br />
                    <input class="button updatestatus" type="button" value="Submit" pid='<?php echo $property->property_id;?>'/>
                </div>
<?php endif; ?>

            </div>

            <div class="left propety_details">
                
                <label for="project_id">Project:<span class="requiredindicator">*</span>
                </label>
                <select id="project_id" name="project_id" size="10" class="required">

                <?php 
                    if($projects) {
                        echo $this->utilities->print_select_options($projects, "project_id", "project_name", $selected_projects);
                    }
                ?>

                </select>
                
                <label for="builder_id">Builder:<span class="requiredindicator">*</span></label>
                <select id="builder_id" name="builder_id" class="required">
                    <? if($builder):?>
                        <?=$this->utilities->print_select_options($builder,"builder_id","builder_name",($property_id != "") ? $property->builder_id : ""); ?>
                    <? endif; ?>
                </select> 	
                
                <label for="property_type_id">Property Type:</label>           
                    
                <select class="property_type_id" name="property_type_id">
                    <?php 
                    if($property_types)
                        echo $this->utilities->print_select_options($property_types,"value","name",($property_id !="") ? $property->property_type_id : ""); 
                    ?>
                </select>
				
				
				<!-- By Mayur - TasksEveryday -->
				
				<label for="title_type_id">Title Type:</label>           
                    
                <select class="property_type_id" name="title_type_id">
                    <?php 
                    if($title_types)
                        echo $this->utilities->print_select_options($title_types,"value","name",($property_id !="") ? $property->title_type_id : ""); 
                    ?>
                </select>
				
				
				<!-- By Mayur - TasksEveryday -->
                
                <label for="contract_type_id">Contract Type:</label>           
                    
                <select class="contract_type_id" name="contract_type_id">
                    <?php 
                    if($contract_types)
                        echo $this->utilities->print_select_options($contract_types,"value","name",($property_id !="") ? $property->contract_type_id : ""); 
                    ?>
                </select>
                
                <label for="tracker_type">Tracker Type:</label>           
                    
                <select class="tracker_type" name="tracker_type">
                    <option value="Construction" <?php if(($property_id != "") && ($property->tracker_type == "Construction")) echo 'selected="selected"'; ?>>Construction</option>
                    <option value="Purchase" <?php if(($property_id != "") && ($property->tracker_type == "Purchase")) echo 'selected="selected"'; ?>>Purchase</option>
                </select>                
                
                <div class="clear top-margin"></div>
                <input type="checkbox" name="featured" id="featured" value="1"  <?php echo ($property_id !="" && $property->featured!="0") ? "checked" : "" ?> /> Is Featured?
                
                <div class="clear top-margin"></div>
                <input type="checkbox" name="archived" id="archived" value="1"  <?php echo ($property_id !="" && $property->archived!="0") ? "checked" : "" ?> /> Archived
                
                <?php if($property_id !="") : ?>
                <div class="clear top-margin"></div>
                <p>
                    <?php if($approved_by_user) : ?>
                    Approved By <a href="<?php echo base_url() . "admin/usermanager/user/" . $approved_by_user->user_id; ?>">
                    <?php echo $approved_by_user->first_name . " " . $approved_by_user->last_name; ?></a> @ <?=$this->utilities->isodatetime_to_ukdate($property->last_modification_dtm); ?><br/>
                    <?php endif; ?>
                
                    Last Modified: <?=$this->utilities->isodatetime_to_ukdate($property->last_modification_dtm); ?>
                </p>
                
                <?php if($property->status == "pending") : ?>
                <div class="warn">
                    <p>This property is currently in a pending status and is not showing in the portal.  You must first approve this property.</p>
                    <a id="btnApprove" href="#" class="button confirm">Approve</a>
                </div>
                <?php endif; ?>                 
                
                <?php endif; ?>                

            </div>

            <div class="clear"></div>

        </div><!-- END first tab -->
        
<? if(isset($property)) : ?>

        <div>

            <div style="height:700px">

            	<label for="overview">Website Short Intro:</label>
                <textarea cols="20" rows="5" name="overview" style="width:880px;height:100px" class="editor wysiwyg"><? echo ($property_id !="") ? $property->overview : "" ?></textarea>            

            	<label for="page_body">Brochure Overview: (<span id="remaining">700</span> characters remain)</label>
                <textarea cols="20" rows="10" name="page_body" style="width:880px;height:300px" class=""><? echo ($property_id !="") ? strip_tags($property->page_body) : "" ?></textarea>

            </div>

            <div class="clear">&nbsp;</div>

            <div class="top-margin">&nbsp;</div>

        </div><!-- END second tab -->

        <div class="specifications">

            <?php echo $this->load->view("admin/property/specifications");?>        

        </div><!-- END third tab -->
        
        <div class="<?php echo (isset($property) && $property->status != 'available') ? '' : 'hidden';?> construction_tracker">
            
            <?php echo $this->load->view("admin/property/construction_tracker");?>        
            <div class="clear"></div>
        </div>
        
<?php endif; ?>

<?php if(isset($property)) :?>                      

        <div class="upload_documents">

            <?php
            
                if($documents) :
                    $i = 0;
                    foreach($documents->result() as $doc) :
                    
                        $filename = $doc->document_path;
                
                        if(strlen($filename) > 40)
                             $filename = substr($filename, 0, 40) . "...";
                             
                        $line = "<br /><br /><div class='line'></div><br />";
                    ?>
                    <div class="document_<?php echo $doc->id;?>">  
                    	<div>
                            <span class="doc_name">Allowed Access:</span>
                            <input type="radio" name="doc_<?php echo $doc->id;?>_extra_data" value="" <?php echo (empty($doc->extra_data)) ? 'checked="checked"' : ''?> /> All Users
                            <input type="radio" name="doc_<?php echo $doc->id;?>_extra_data" value="advisors_only" <?php echo ($doc->extra_data=='advisors_only') ? 'checked="checked"' : ''?> /> Advisors Only
                        </div>
                        
                        <div>
                            <span class="doc_name">Document Name:</span>
                            <input type="text" id="doc_<?php echo $doc->id;?>_name" name="doc_<?php echo $doc->id;?>_name" value="<?php echo $doc->document_name;?>" />
                        </div>
                        
                        <div>    
                            <input type="hidden" value="<?php echo $doc->id;?>" id="doc_<?php echo $doc->id;?>_id" /> 
                    
                            <span class="doc_name left" style="padding:15px 39px 0 0;">Attachment:</span><span id="docpath_<?php echo $doc->id;?>" class="<?php echo ($filename == "")? "hidden": ""; ?>"><?php echo $doc->document_path; ?></span>
							
                            <input type="button" name="delete_doc_<?php echo $doc->id;?>" id="delete_doc_<?php echo $doc->id;?>" value="Delete" style="width:70px;margin-top: 5px;" class="<?php echo ($doc->document_path == "")? "hidden del_path": "del_path"; ?> button" />    
							<?php if(($doc->document_path != "") && file_exists($doc->document_path)): ?>
							<a href="<?php echo base_url($doc->document_path); ?>" target="_blank"> View Document </a>
							<?php endif; ?>	
                            <!--<input type="file" name="doc_<?php echo $doc->id;?>" id="doc_<?php echo $doc->id;?>" class="<?php echo ($filename != "") ? "hidden" : ""; ?>" />    -->
                            <div id="doc_upload_file_<?php echo $doc->id;?>" <?php echo ($filename != "") ? "hidden" : ""; ?> class="doc_upload_file" did="<?php echo $doc->id;?>" style="width:150px;float:left;padding-top:5px;"></div>
                  
                        </div>                                
                    </div>
                    <?php
                    
                    if($i != ($documents->num_rows() - 1)) echo $line;
                        
                    $i++;
                    endforeach;
                endif;
            ?>
			<br>
			<br>
            <div class="clear"></div>

        </div><!-- END fourth tab -->
        
<?php if (isset($property)) : ?>
        <div id="uploadTabContent" style="display:none;">
            
            <label>Property Images for Print:</label>
            <select id="image_print1" name="image_print1">
                <? if($images):?>
                    <?=$this->utilities->print_select_options($images,"document_path","document_name",($property != "") ? $property->image_print1 : "", "Choose"); ?>
                <? endif; ?>
            </select>
            
            <select id="image_print2" name="image_print2">
                <? if($images):?>
                    <?=$this->utilities->print_select_options($images,"document_path","document_name",($property != "") ? $property->image_print2 : "", "Choose"); ?>
                <? endif; ?>
            </select>
        

            <div class="right" style="padding-right:10px">

                <label for="upload_file">Upload a new image</label>
                <!--<input type="file" name="upload_file" id="upload_file" />    -->
                <div id="upload_file"></div>

            </div>

            <div class="clear"></div>

            <label>Property Images</label>

            <br/>

            <div id="files_listing">

                <div  id="page_listing">
                    <? $this->load->view('admin/property/file_listing',array('files'=>$images,'pages_no' => count($images) / $images_records_per_page,'hero_image'=>$property->hero_image)); ?>
                </div>

                <div class="clear"></div>            

                <div id="controls">
                    <div class="right">
                        <input class="button" type="button" value="Delete Selected Files" id="delete_files" />
                    </div>                
                </div>    

                <div class="clear"></div>

            </div>

        </div><!-- END fifth tab -->
        
        <div id="addnote"  style="display:none;"><!-- BEGIN Note tab -->
            <table cellspacing="0" width="100%" class="left commentlisting"> 
                <tr>
                    <th width="10%">ID</th>
                    <th align="left">Note</th>
                    <th width="20%">Date</th>
                    <th width="10%">Delete</th>
                </tr>
        <?php if ($comments) : ?>
            <?php foreach ($comments->result() AS $index=>$comment) : ?>
                <tr id="acomment_<?php echo $comment->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
                    <td class="admintabletextcell" align="center"><a href="javascript:;" rel="<?php echo $comment->id; ?>" class="editComment"><?php echo $comment->id;?></a></td>
                    <td class="admintabletextcell" style="padding-left:12px;">
                        <span style="font-weight:bold"><?php echo trim("$comment->first_name $comment->last_name")?></span>:<br />
                        "<?php echo nl2br($comment->comment)?>"
                    </td>
                    <td class="admintabletextcell" align="center"><?php echo date('d/m/Y', $comment->ts_added);?></td>
                    <td class="center"><input type="checkbox" class="commenttodelete" value="<?php echo $comment->id;?>" /></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
            </table>
            <a href="javascript:;" class="button right center" id="deletecomment">Delete</a>
            <a href="javascript:;" class="button right center" id="newcomment">New Note</a>
            
            <div class="clear"></div>
            
            <div id="formnewcomment" style="display:none;">
            
                <label for="comment">Note:<span class="requiredindicator">*</span></label>
                <textarea id="comment" style="width:400px;"></textarea>
                 <label for="note_date">Date:<span class="requiredindicator">*</span></label>
                <input type="text" readonly="readonly" class="date-choose" value="" id="note_date" name="note_date" />                
                <input type="hidden" id="comment_id"/>
                <br /><br />
                <label>View permission:</label>
                <label for="advisor">Advisor
                <input type="checkbox" value="<?=USER_TYPE_ADVISOR?>" name="view[]" id="advisor" /></label>
                <label for="partner">Partner
                <input type="checkbox" value="<?=USER_TYPE_PARTNER?>" name="view[]" id="partner" /></label>
                <label for="investor">Investor
                <input type="checkbox" value="<?=USER_TYPE_INVESTOR?>" name="view[]" id="investor" /></label> 
                <label for="private_note">Private Note
                <input type="checkbox" value="1" name="private_note" id="private_note"  /></label>
				
                <div class="clear"></div><br />
                <a href="javascript:;" class="button left center savecomment">Save</a>
                
            </div>
            
            <div class="clear"></div>
        </div><!-- END Note tab -->
        
        <!-- Advisor Tab -->
        <div>
            <label for="total_commission">Total Commission $</label>
            <input type="text" id="total_commission" name="total_commission" value="<?php echoifobj($property, "total_commission"); ?>" class="number amount" />

            <div class="clear"></div>
            
            <fieldset class="top-margin20 left" style="width: 280px;">
                <legend>Stage 1</legend>
                
                <label for="stage1_payment">Stage 1 Payment</label>
                <input type="text" id="stage1_payment" name="stage1_payment" value="<?php echoifobj($property, "stage1_payment"); ?>" itemno="1" class="number amount stage_payment" />
                
                <label for="stage1_percentage">Stage 1 Percentage</label>
                <input type="text" id="stage1_percentage" name="stage1_percentage" value="<?php echoifobj($property, "stage1_percentage"); ?>" class="number" />
                
                <label for="stage1_payable">Stage 1 Payable</label>
				<select id="stage1_payable" name="stage1_payable">
				<option> Select </option>
				<?php foreach ($pro_stages as $key => $value) : ?>
                        <option value="<?php echo $key?>"<?php echo ($key==$property->stage1_payable) ? ' selected="selected"' : ''?>><?php echo $value?></option>
                    <?php endforeach; ?>
				</select>
            </fieldset>
            
            <fieldset class="top-margin20 left left-margin" style="width: 280px;">
                <legend>Stage 2</legend>
                
                <label for="stage2_payment">Stage 2 Payment</label>
                <input type="text" id="stage2_payment" name="stage2_payment" value="<?php echoifobj($property, "stage2_payment"); ?>" itemno="2" class="number amount stage_payment" />
                
                <label for="stage1_percentage">Stage 2 Percentage</label>
                <input type="text" id="stage2_percentage" name="stage2_percentage" value="<?php echoifobj($property, "stage2_percentage"); ?>" class="number" />
                
                <label for="stage2_payable">Stage 2 Payable</label>
                <select id="stage2_payable" name="stage2_payable">
				<option> Select </option>
				<?php foreach ($pro_stages as $key => $value) : ?>
                        <option value="<?php echo $key?>"<?php echo ($key==$property->stage2_payable) ? ' selected="selected"' : ''?>><?php echo $value?></option>
                    <?php endforeach; ?>
				</select>                
            </fieldset> 
            
            <fieldset class="top-margin20 left left-margin" style="width: 280px;">
                <legend>Stage 3</legend>
                
                <label for="stage3_payment">Stage 3 Payment</label>
                <input type="text" id="stage3_payment" name="stage3_payment" value="<?php echoifobj($property, "stage3_payment"); ?>" itemno="3" class="number amount stage_payment" />
                
                <label for="stage3_percentage">Stage 3 Percentage</label>
                <input type="text" id="stage3_percentage" name="stage3_percentage" value="<?php echoifobj($property, "stage3_percentage"); ?>" class="number" />
                
                <label for="stage3_payable">Stage 3 Payable</label>
                <select id="stage3_payable" name="stage3_payable">
				<option> Select </option>
				<?php foreach ($pro_stages as $key => $value) : ?>
                        <option value="<?php echo $key?>"<?php echo ($key==$property->stage3_payable) ? ' selected="selected"' : ''?>><?php echo $value?></option>
                    <?php endforeach; ?>
				</select>                
            </fieldset>   
            
            <div class="clear"></div>                    

            <label for="advisor_comments">Comments</label>
            <textarea id="advisor_comments" name="advisor_comments" cols="50" rows="6" style="width:500px;"><?php echoifobj($property, "advisor_comments"); ?></textarea>
            
            <label for="commission_comments">Commission Comments</label>
            <textarea id="commission_comments" name="commission_comments" cols="50" rows="6" style="width:500px;" readonly="readonly"><?php echoifobj($property, "commission_comments"); ?></textarea>

            <label for="commission_sharing_user_id">Commission Sharing Partner</label>
            <?php echo form_dropdown_partners($property->advisor_id, 'commission_sharing_user_id', $property->commission_sharing_user_id, 'style="width:500px" disabled="disabled"' );?>

            <label for="advisor_comments_other">Other Comments</label>
            <textarea id="advisor_comments_other" name="advisor_comments_other" cols="50" rows="6" style="width:500px;" readonly="readonly"><?php echoifobj($property, "advisor_comments_other"); ?></textarea>

        </div>        
        
		<div id="addhistory"  style="display:none;"><!-- BEGIN History tab -->

            <table cellspacing="0" width="100%" class="left historylisting"> 
                <tr>
                    <th width="10%" sort="created_dtm">Date</th>
                    <th width="10%" sort="change_type">Change Type</th>
                    <th width="10%" sort="old_value">Change From</th>
                    <th width="10%" sort="new_value">Change To</th>
                    <th width="10%" sort="user_id">User</th>
                </tr>
        <?php if ($histories) : ?>
            <?php foreach ($histories->result() AS $index=>$history) : ?>
                <tr id="ahistory_<?php echo $history->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
                   <td class="center"><?php echo $history->created_dtm;?></td>
                   <td class="center"><?php echo $history->change_type;?></td>
                   <td class="center"><?php echo $history->old_value;?></td>
                   <td class="center"><?php echo $history->new_value;?></td>
                   <td class="center"><a href="<?php echo base_url(); ?>admin/usermanager/user/<?php echo $history->user_id; ?>"><?php echo $history->user_name;?></a></td>
				   
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
            </table>
            
            <div class="clear"></div>
        </div><!-- END History tab -->
		
    </div><!-- end tabs -->

<? else : ?>

<?php endif; ?>

    <div class="clear"></div>
    <!--<input id="button" type="submit" value="<? echo ($property_id == "") ? "Create New Property": "Update Property"; ?>" style="visibility:hidden;height:0px;margin:0;padding:0;line-height:0px;font-size:0px;" />-->
    <input type="hidden" name="postback" value="1" />
<?php if (isset($property)) : ?>
    <input type="hidden" name="hero_image" value="<?php echo $property->hero_image?>" />
<?php endif; ?>
    <input type="hidden" name="id" value="<?=$property_id?>" />
</form>

        
        
<?php endif; ?>
    <input id="submitbutton" class="button" type="button" value="<? echo ($property_id == "") ? "Create New Property": "Update Property"; ?>" style="margin-top:25px;" />
<br/>

<? $this->load->view("admin/property/navigation"); ?>