<body id="contact">
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/builder/navigation"); ?>                        
            
            <form class="plain" id="frmBuilder" name="frmBuilder" action="<?php echo base_url()?>admin/buildermanager/builder/<?php echo $builder_id?>"  method="post">
                <input type="hidden" id="folder" value="<?php echo $builder_id;?>" />
                <input type="hidden" id="builder_id" value="<?php echo $builder_id;?>" />
     
<?php if(isset($builder)) : // We're editing and existing builder.  Show the tabs. ?>

                <br><br>
				<input id="submitbutton2" class="button right" type="button" value="<? echo ($builder_id == "") ? "Create New Builder": "Update Builder"; ?>" style="margin-top:-40px;" />

                <!-- tabs -->
                <ul class="css-tabs skin2">
                    <li><a href="#">Builder Details</a></li>
                    <li><a href="#">HTML Content</a></li>
                    <li><a href="#">Contacts</a></li>
                    <li><a href="#">Comments</a></li>
                    <li><a href="#" id="tabDocument">Documents</a></li>                    
                </ul>   
                
                <!-- panes -->
                <div class="css-panes skin2">
                    <div style="display:block">
<?php endif; ?>
						<div class="left" style="width:45%">
							<label for="builder_name">Builder Name:<span class="requiredindicator">*</span></label>
                    		<input id="builder_name" class="required" type="text" value="<? echo ($builder_id !="") ? $builder->builder_name : ""; ?>" name="builder_name"/> <br/> <br/>
							
							
							<input type="checkbox" name="display_on_front_end" value="1" class="left" <? echo ($builder_id !="") ? (($builder->display_on_front_end == 1) ? "checked" :"") : "checked" ?>  /><label for="display_on_front_end" class="left" style="padding-top:0px">&nbsp;Display on Front End</label>  <br/>
						
							
<?php if(isset($builder)) : ?>
                            <label for="acn">ACN:</label>
                    		<input id="acn" type="text" value="<? echo ($builder_id !="") ? $builder->acn : ""; ?>" name="acn"/>
                    		
                    		<label for="abn">ABN:</label>
                    		<input id="abn" type="text" value="<? echo ($builder_id !="") ? $builder->abn : ""; ?>" name="abn"/>
                    		
                    		<label for="year_established">Year established:</label>
                    		<input id="year_established" type="text" value="<? echo ($builder_id !="") ? $builder->year_established : ""; ?>" name="year_established"/>
                    		
                    		<label for="number_builds_per_year">Average number of builds per year:</label>
                    		<input id="number_builds_per_year" type="text" value="<? echo ($builder_id !="") ? $builder->number_builds_per_year : ""; ?>" name="number_builds_per_year"/>
<?php endif; ?>
						</div>
						
<?php if(isset($builder)) : ?>

                        <div class="left" style="width:50%">
                            <label for="summary">Summary</label>
                            <textarea name="summary" id="summary"><? echo ($builder_id !="") ? $builder->summary : ""; ?></textarea>
                            
                            <?php if($builder) : ?>
                            <p>Last Modified: <?=$this->utilities->isodatetime_to_ukdate($builder->last_modified); ?></p>
                            <?php endif; ?>
						</div>
						
						<div class="clear"></div>
                        
                        <label for="builder_logo">Builder Logo</label>
                        <div class="logo_img">
                        
                        <?php if (!empty($builder->builder_logo)) : ?>
                            <img id="builder_logo_img" src="<?php  echo base_url().$builder->builder_logo; ?>" width="250" class="<?php echo (empty($builder->builder_logo)) ?  "hidden" : ""; ?>" />
                      	<?php endif; ?>
                      	
                      	</div>
    					<input class="<?php echo ($builder->builder_logo == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Logo" id="delete_logo" />
    					
    					<div id="builder_logo_upload" class="showif <?php echo (!empty($builder->builder_logo)) ?  "hidden" : ""; ?>"></div>
                    
<?php endif; ?>
                        <div class="clear"></div>
                        
                        <div class="left" style="padding-top: 20px;">
							<input type="checkbox" name="enabled" value="1" class="left" <? echo ($builder_id !="") ? (($builder->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Enabled</label> 
						</div>
						
						<div class="clear"></div>
						
<?php if(isset($builder)) : ?>

					</div><!-- END first tab -->
					
                    <div>
                        <label for="content">Summary</label><br />
                        <textarea id="wysiwyg" cols="20" rows="10" name="builder_content" style="width:880px;height:300px" class="editor"><? echo ($builder_id !="") ? $builder->builder_content : "" ?></textarea>
                        
                        <div class="clear"></div>
                        
                        <label for="history">History</label><br />
                        <textarea id="wysiwyg2" cols="20" rows="10" name="history" style="width:880px;height:300px" class="editor"><? echo ($builder_id !="") ? $builder->history : "" ?></textarea>
                        
                    </div><!-- END htmk content tab -->
                    
                    <div>
                        <table cellspacing="0" width="100%" class="left contact_listing"> 
                            <tr>
                                <th width="10%">ID</th>
                                <th align="left">Contact Name</th>                            
                                <th align="left">Position</th>                            
                                <th align="left">Phone</th>
                                <th width="10%">Delete</th>                            
                            </tr>
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
                        </table>
                        
                        <div class="clear"></div>
                        
                        <a href="javascript:;" class="button right center" id="deletecontact" style="margin-left:10px;">Delete</a>
                        <a href="javascript:;" class="button right center" id="addnewcontact">Add new</a>
                        
                        <div class="clear"></div>
                        
                        <div id="formaddcontact" style="display:none;">
                            <input type="hidden" name="contact_id" id="contact_id"/>
                            
                            <fieldset class="left" style="width: 290px;">
                                <label for="name">Contact Name:<span class="requiredindicator">*</span></label>
                                <input type="text" id="name" name="name"/>
                                
                                <label for="position">Position:</label>
                                <input type="text" id="position" name="position"/>                            
                                
                                <label for="phone">Phone:</label>
                                <input type="text" id="phone" name="phone"/>
                                
                                <label for="mobile">Mobile:</label>
                                <input type="text" id="mobile" name="mobile"/>
                                
                                <label for="fax">Fax:</label>
                                <input type="text" id="fax" name="fax"/>
                                
                                <label for="email">Email:</label>
                                <input type="text" id="email" name="email"/>
                            </fieldset>
                            
                            <fieldset class="left" style="width: 270px;"> 
                                <label for="address">Address:</label>
                                <input type="text" id="address" name="address"/>
                                
                                <label for="suburb">Suburb:</label>
                                <input type="text" id="suburb" name="suburb"/>                       
                                
                                <label for="postcode">Postcode:</label>
                                <input type="text" id="postcode" name="postcode"/>
                                
                                <label for="state_id">State:</label>
                                <select id="state_id" name="state_id">
                                    <option value="">Choose</option>
                                    <?php echo $this->utilities->print_select_options($states, "state_id", "name"); ?> 
                                </select>                             
                                
                                <label for="contact_comment">Comment:</label>
                                <textarea id="contact_comment" name="contact_comment"></textarea>                                
                            </fieldset>                            
                            
                            <div class="clear"></div><br />
                            <a href="javascript:;" class="button left center savecontact">Save</a>
                        </div>
                        
                        <div class="clear"></div>
                        
                    </div><!-- END contacts tab -->
                    
                    <div>
                        <table cellspacing="0" width="100%" class="left commentlisting"> 
                            <tr>
                                <th width="10%">ID</th>
                                <th align="left">Comment</th>
                                <th width="10%">Delete</th>
                            </tr>
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
                        
                        <div class="clear"></div>
                    </div><!-- END comments tab -->
                    
                    <div class="upload_documents">
                    <?php
            
                    if($documents) :
                        $i = 0;
                        foreach($documents->result() as $doc) :
                        
                            $filename = $doc->document_path;
                    
                            if(strlen($filename) > 40)
                                 $filename = substr($filename, 0, 40) . "...";
                                 
                            $line = "<div class='line'></div>";
                        ?>
                        <div class="document_<?php echo $doc->id;?>">  
                            <div>
                                <span class="doc_name">Allowed Access:</span>
                                <input type="radio" name="doc_<?php echo $doc->id;?>_extra_data" value="" <?php echo (empty($doc->extra_data)) ? 'checked="checked"' : ''?> /> All Users
                                <input type="radio" name="doc_<?php echo $doc->id;?>_extra_data" value="admin_only" <?php echo ($doc->extra_data=='admin_only') ? 'checked="checked"' : ''?> /> Admin Only
                            </div>
                            <div>
                                <span class="doc_name">Document Name:</span>
                                <input type="text" id="doc_<?php echo $doc->id;?>_name" name="doc_<?php echo $doc->id;?>_name" value="<?php echo $doc->document_name;?>" />
                            </div>
                            <div>    
                                <input type="hidden" value="<?php echo $doc->id;?>" id="doc_<?php echo $doc->id;?>_id" /> 
                        
                                <span class="doc_name left" style="padding:15px 39px 0 0;">Attachment:</span><span id="docpath_<?php echo $doc->id;?>" class="<?php echo ($filename == "")? "hidden": ""; ?>"><?php echo $doc->document_path; ?></span> 
                                <input type="button" name="delete_doc_<?php echo $doc->id;?>" id="delete_doc_<?php echo $doc->id;?>" value="Delete" style="width:70px;margin-top: 5px;" class="<?php echo ($doc->document_path == "")? "hidden del_path": "del_path"; ?> button" />    
                        
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
                    </div><!-- END documents tab -->            
                                       
                </div>
<?php endif; ?>
                <div class="clear"></div>
    
                <br/><br/>
                          
                <label for="heading">&nbsp;</label> 
                <input id="button" type="submit" value="<? echo ($builder_id == "") ? "Create New Builder": "Update Builder"; ?>" /><br/>                    
            </form>
            
         <br/>
         <?php $this->load->view("admin/builder/navigation"); ?>    