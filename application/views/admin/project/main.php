<body id="contact" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/project/navigation"); ?>                        
            
            <form class="plain" id="frmProject" name="frmProject" action="<?php echo base_url()?>admin/projectmanager/project/<?php echo $project_id?>"  method="post">
            	
                <input type="hidden" id="folder" value="<?php echo $project_id;?>" />
                <input type="hidden" id="project_id" value="<?php echo $project_id;?>" />
     
<?php if(isset($project)) : // We're editing and existing project.  Show the tabs. ?>
                
				<?php if(isset($project)) : ?>
				<br><br>
			    <input id="submitbutton2" class="button right" type="button" value="<? echo ($project_id == "") ? "Create New Project": "Update Project"; ?>" style="margin-top:-40px;" />
			    <?php endif; ?>

                <!-- tabs -->
                <ul class="css-tabs skin2">
                    <li><a href="#">Project Details</a></li>
                    <!--<li><a href="#">Google/SEO</a></li>-->
                    <li><a href="#">HTML Content</a></li>
                    <li><a href="#">Sections</a></li>
                    <li><a href="#" id="tabGallery">Gallery</a></li>
                    <li><a href="#" id="tabDocument">Documents</a></li>
					 <li><a href="#" id="tabNote" class="tab">Notes</a></li>		
                    <li><a href="#" id="tabBrochure">Bro</a></li>
                </ul>   
                
                <!-- panes -->
                <div class="css-panes skin2">
                    <div style="display:block">
<?php endif; ?>     
						<div class="left" style="width:35%">
    						<div class="left">
    							<label for="project_name">Project Name:<span class="requiredindicator">*</span></label>
                        		<input id="project_name" class="required" type="text" value="<? echo ($project_id !="") ? $project->project_name : ""; ?>" name="project_name"/>						
    						</div>
    						
    						<div class="clear"></div>   						
    						
    						<div class="left"> 
    							<label for="project_code">Project URL:<span class="requiredindicator">*</span></label> 
    							<input type="text" name="project_code" id="project_code" class="required" value="<? echo ($project_id !="") ? $project->project_code : "" ?>" size="50" />      
    						</div>
    						
    						<div class="clear"></div>
    						
    						<div class="left">	
    							<label for="project_order">Project Order:</label>
    							<input id="project_order" type="text" value="<?php echo ($project_id !="") ? $project->project_order : $this->project_model->get_next_order(); ?>" name="project_order" /><br />
                            </div>
    						
    						<div class="clear"></div>
    
    						<div class="clear"></div>						

<?php if(isset($project)) : ?>
        				    <br />
        				    
    						<label for="logo">Project Logo for Web</label>
    						<div class="logo_img">
                                <img id="logo_img_upload" src="<?php echo empty($project->logo) ? '#' : base_url().$project->logo . "_thumb.jpg"; ?>" width="250" class="<?php echo empty($project->logo) ?  "hidden" : ""; ?>" />
                          	</div>
                          	<div class="clear"></div>
        					<input class="<?php echo ($project->logo == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Logo" id="delete_logo" />
                            <div id="logo_upload" class="showif <?php echo (!empty($project->logo)) ?  "hidden" : ""; ?>"></div>
                            
        					<br />
        					
                            <label for="logo2">Project Logo for Print:</label>
                            <div class="logo_img2">
        					   <img id="logo_img2_upload" src="<?php echo empty($project->logo_print) ? '#' : base_url().$project->logo_print . "_thumb.jpg"; ?>"  width="250" class="<?php echo empty($project->logo_print) ?  "hidden" : ""; ?>" />
        					</div>
                            
        					<div class="clear"></div>
        					<input class="<?php echo ($project->logo_print == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Logo" id="delete_logo2" />
        					<div id="logo_print_upload" class="showif <?php echo (!empty($project->logo_print)) ?  "hidden" : ""; ?>"></div>
                            <h3>Actions</h3>
                            <ul>
                                <li><a href="<?php echo base_url();?>admin/projectmanager/brochure/<?php echo $project_id;?>" class="print" target="_blank">Print project</a></li>
                            </ul>
<?php endif; ?>
                        </div>
                        
                        <div class="left" style="width:35%">
                            <div class="left">		
    			                <label for="area_id">Area:<span class="requiredindicator">*</span></label>
    			                <select id="area_id" name="area_id" class="required">
    			                    <? if($areas):?>
    			                        <?=$this->utilities->print_select_options($areas,"area_id","area_name",($project != "") ? $project->area_id : ""); ?>
    			                    <? endif; ?>
    			                </select> 	
    						</div>
							
							<div class="left">		
    			                <label for="state_id">State:<span class="requiredindicator">*</span></label>
    			                <select id="state_id" name="state_id" class="required" >
    			                    <? if($states):?>
    			                        <?=$this->utilities->print_select_options($states,"state_id","name",($project != "") ? $project->state_id : ""); ?>
    			                    <? endif; ?>
    			                </select> 	
    						</div>

                            <div class="left">		
    			                <label for="rate">Risk Comment:<span class="requiredindicator" >*</span></label>
    			                <select id="rate" name="rate" class="required" >
                                <option value="">Choose Option</option>
                                <?php if($rates) : ?>
                                <?=$this->utilities->print_select_options_array($rates, false, ($project != "") ? $project->rate : ""); ?>
                                <?php endif; ?>
    			                </select> 	
    						</div> 
    						
    						<div class="left">
    							<label for="website">Website:</label>
                        		<input id="website" type="text" value="<? echo ($project_id !="") ? $project->website : ""; ?>" name="website"/>
    						</div>
                            <?php if(isset($project)) : ?>    						
    						<div class="left">
    							<label for="google_map_code">Google Map Code:</label>
                        		<textarea id="google_map_code" name="google_map_code" style="height:130px;"><? echo ($project_id !="") ? $project->google_map_code : ""; ?></textarea>
                                <?php
                                    if(isset($project)) {
                                        $map_image = "project_files/" . $project_id . "/map.png";
                                        $map_image_abs = ABSOLUTE_PATH . "project_files/" . $project_id . "/map.png";
            
                                        if(file_exists($map_image_abs)) {
                                            ?>
                                 <img src="<?php echo base_url() . $map_image . "?r=" . rand(9999, 99999999); ?>" width="260" style="padding: 10px 0px;" />
                                 <input type="button" class="button" id="btnRegenerateMap" value="Regenerate Map" />
                                 <input type="hidden" id="deletemap" name="deletemap" value="0" />
                                            <?php
                                        }
                                    }                                    
                                ?>
    						</div>
                            
                            <div class="left">
                                <label for="disclaimer_id">Brochure Disclaimer:</label>
                                <select id="disclaimer_id" name="disclaimer_id">
                                    <option value="">Use Default Disclaimer</option>
                                    <?php echo $this->utilities->print_select_options($disclaimers, "article_id", "article_title", $project->disclaimer_id); ?>
                                </select>
                            </div>
                            
                            <?php endif; ?>
                        </div>
                        
                        <div class="left" style="width:20%">
    						<div class="left" style="padding-top: 30px; ">
    							<input type="checkbox" name="enabled" value="1" class="left" <? echo ($project_id !="") ? (($project->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Show on website</label> 
    						</div>
    						
    						<div class="clear"></div>
    						
    						<div class="left" style="padding-top: 15px; ">
    							<input type="checkbox" name="ck_newsletter" value="1" class="left" <? echo ($project_id !="") ? (($project->ck_newsletter == 1) ? "checked" :"") : "" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Show on newsletter</label> 
    						</div>
    						
    						<div class="clear"></div>
    						
    						<div class="left" style="padding-top: 15px; ">
    							<input type="checkbox" name="is_featured" value="1" class="left" <? echo ($project_id !="") ? (($project->is_featured == 1) ? "checked" :"") : "" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Is Featured?</label> 
    						</div>

							<div class="left">
    							<label for="eoi_deposit">EOI Deposit:</label>
                        		<input id="eoi_deposit" type="text" value="<? echo ($project_id !="") ? (($project->eoi_deposit != "") ? $project->eoi_deposit :"") : "1000" ?>" name="eoi_deposit"/>
    						</div>
							<div class="left"> &nbsp;<br/> </div>
							<div class="left">
    							<span><b>Credit Card Payment Available:</b></span>
								<input type="radio" name="credit_card" value="Yes" <? echo ($project_id !="") ? (($project->credit_card == "Yes") ? "checked" :"") : "" ?>>Yes
								<input type="radio" name="credit_card" value="No" <? echo ($project_id !="") ? (($project->credit_card == "No") ? "checked" :"") : "" ?>>No
    						</div>
							
							<div class="left">
								<label for="EFT_Deatils">EFT Deatils:</label>
    							<label for="account_name">Account Name:</label>
                        		<input id="account_name" type="text" value="<? echo ($project_id !="") ? $project->account_name : ""; ?>" name="account_name"/><br/>
    						</div>
							
							<div class="left">
								<label for="BSB">BSB:</label>
                        		<input id="BSB" type="text" value="<? echo ($project_id !="") ? $project->BSB : ""; ?>" name="BSB"/><br/>
    						</div>
							
							<div class="left">
								<label for="account_number">Account Number:</label>
                        		<input id="account_number" type="text" value="<? echo ($project_id !="") ? $project->account_number : ""; ?>" name="account_number"/><br/>
    						</div>
							
							<div class="left">
								<label for="reference">Reference:</label>
                        		<input id="reference" type="text" value="<? echo ($project_id !="") ? $project->reference : ""; ?>" name="reference"/><br/>
    						</div>
							
							<div class="left">
    							<label for="payment_terms_conditions"> Payment Terms & Conditions:</label>
                        		<textarea id="payment_terms_conditions" name="payment_terms_conditions" style="height:80px;"><? echo ($project_id !="") ? $project->payment_terms_conditions : ""; ?></textarea>
    						</div>
							
						</div>
						<div class="clear"></div>
<?php if(isset($project)) : ?>
                    </div><!-- END first tab -->
                    
                    <?php /*
                    <div>
                        <label for="meta_title">Meta title:</label> 
                        <input type="text" name="meta_title" value="<? echo ($project_id !="") ? $project->meta_title : "" ?>"/>
                        
                        <label for="meta_keywords">Keywords:</label> 
                        <input type="text" name="meta_keywords" value="<? echo ($project_id !="") ? $project->meta_keywords : "" ?>"/>
                        
                        <label for="meta_description">Description / Call to action:</label> 
                        <input type="text" name="meta_description" value="<? echo ($project_id !="") ? $project->meta_description : "" ?>"/>
                        
                        
                        <label for="meta_robots">Search Robots Permissions:</label>
                        <select name="meta_robots">
                                <?php echo $this->utilities->print_select_options_array($robots,false,($project_id !="") ? $project->meta_robots : ""); ?>                
                        </select>
                    </div><!-- END second tab --> 
                    <?php */ ?>
                    
                    <div>
                        <label>Brochure and website overview</label><br />
                        <div style="height:330px">
                            <textarea id="wysiwyg" cols="20" rows="10" name="page_body" style="width:880px;height:300px" class="editor"><? echo ($project_id !="") ? $project->page_body : "" ?></textarea>        
                            <input type="hidden" name="wysiwygWordCount" value="730" />
                        </div>
                        
                        <label>Brochure Quick Facts</label><br />
                        <div style="height:330px">
                            <textarea id="wysiwyg2" cols="20" rows="10" name="quick_facts" style="width:880px;height:300px" class="editor"><? echo ($project_id !="") ? $project->quick_facts : "" ?></textarea>        
                            <input type="hidden" name="wysiwyg2WordCount" value="730" />
                        </div>
                    </div><!-- END third tab -->
                    
                    <div>
                        <table cellspacing="0" width="100%" class="left metalisting"> 
                            <tr>
                                <th width="10%">ID</th> 
                                <th align="left">Section Name</th>                            
                                <th width="10%">Delete</th>                            
                            </tr>
                    <?php $i = 0;?>
                    <?php if ($metas) : ?>
                        <?php foreach ($metas->result() AS $meta) : ?>
                            <?php
                                if($i++ % 2==1) $rowclass = "admintablerow";
			                    else  $rowclass = "admintablerowalt";
                            ?>
                            <tr class="<? print $rowclass;?>">
                                <td class="admintabletextcell" align="center"><?php echo $meta->id;?></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="<?php echo $meta->id;?>" class="btnedit"><?php echo $meta->name;?></a></td>
                                <td class="center"><input type="checkbox" class="metatodelete" value="<?php echo $meta->id;?>" /></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                        </table>
                        
                        <div class="clear"></div>
                        
                        <a href="javascript:;" class="button right center" id="deletemeta" style="margin-left:10px;">Delete</a>
                        <a href="javascript:;" class="button right center" id="openformadd">Add new</a>
                        
                        <div class="clear"></div>
                        
                        <!-- FORM ADD META DATA -->
                        <div id="formaddwrap" style="display:none;">
                        
                            <label for="heading">Section Name:<span class="requiredindicator">*</span></label>
                            <input class="heading" type="text" value="" id="heading" style="width:750px"/>
                            <div class="clear"></div>
                            
                            <label for="icon_image">Icon Image</label>
                            
                            <div id="icon_upload_area">
                                <input type="text" readonly="readonly" id="icon_image" name="icon_image"  />
                                <input type="button" id="icon_upload" value="Upload a file" class=" button" onclick="selectFile('icon_image');"/>
                            </div>
                            
                            <label for="content">Section Content:<span class="requiredindicator">*</span></label>
                            <input type="hidden" id="meta_id" value=""/>
                            <textarea id="wysiwyg4" cols="20" rows="10" style="width:880px;height:300px;padding-top:5px;" class="editor"></textarea><br />
                            
                            <div class="clear"></div>
                            <a href="javascript:;" class="button left center savemeta">Save</a>
                            
                        </div>
                        <div class="clear"></div>
                    </div><!-- END section tab -->
                    
                    <div>
                        <div class="right" style="padding-right:10px">
                            <label for="upload_image">Upload a new image</label>
                            <div id="upload_file"></div>
                        </div>
                        <div class="clear"></div>
                        <br/>                
                        
                        <div id="files_listing">
                            <div id="page_listing">
                                <? $this->load->view('admin/project/file_listing',array('files'=>$images,'pages_no' => count($images) / $images_records_per_page)); ?>
                            </div>
                             
                            <div class="clear"></div>            
                            <div id="controls">
                                <div class="right">
                                	<input class="button" type="button" value="Save Changes" id="save_file_changes" />
                                    <input class="button" type="button" value="Delete Selected Files" id="delete_files" />
                                </div>                
                            </div>    
                            <div class="clear"></div>
                        </div>            
                    </div><!-- END fourth tab -->
                                        
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
                    </div><!-- END fifth tab -->
                    
					
					<!-- BEGIN Note tab -->
					<div id="addnote"  style="display:none;">
            <table cellspacing="0" width="100%" class="left commentlisting"> 
                <tr>
                    <th width="10%">ID</th>
                    <th align="left">Note</th>
                    
                    <th width="10%">Delete</th>
                </tr>
        <?php if ($comments) : ?>
            <?php foreach ($comments->result() AS $index=>$comment) : ?>
                <tr id="acomment_<?php echo $comment->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
                    <td class="admintabletextcell" align="center"><?php echo $comment->id;?></td>
                    <td class="admintabletextcell" style="padding-left:12px;">
                        <span style="font-weight:bold"><?php echo trim("$comment->first_name $comment->last_name")?></span>:<br />
                        "<?php echo nl2br($comment->comment)?>"
                    </td>
                    
                    <td class="center"><input type="checkbox" class="commenttodelete" value="<?php echo $comment->id;?>" /></td>
                </tr>
            <?php endforeach; ?>
        <?php endif; ?>
            </table>
            <a href="javascript:;" class="button right center" id="deletecomment">Delete</a>
            <a href="javascript:;" class="button right center" id="newcomment">New Note</a>
            
            <div class="clear"></div>
            
            <div id="formnewcomment" style="display:none;">
                        
                            <label for="comment">Comment:<span class="requiredindicator">*</span></label>
                            <textarea id="comment" style="width:400px;"></textarea>
                            <input type="hidden" id="comment_id"/>
                            
                            <div class="clear"></div><br />
                            <a href="javascript:;" class="button left center savecomment">Save</a>
                            
            </div>
            
            <div class="clear"></div>
        </div>	
		<!-- END Note tab -->	

        <div id="brochure_content"><!-- BEGIN tab Brochure -->
                    
            <div id="frmAddBrochure">
                
                <select id="page_type" name="page_type" class="required left">
                    <? if($brochures):?>
                    <?=$this->utilities->print_select_options($default_brochures,"type","type"); ?>
                    <? endif; ?>
                </select>
                <a href="javascript:;" class="button left center" id="addNewPage" style="margin-left:5px;margin-right:5px;">Add Page</a>
            
                <br />
                <br />
                <br />
                <div id="uploadNewImage" class="left" style="margin-left:5px;margin-right:5px;"></div>
            </div>
            
            <br />
            <br />
            
            <div id="page_list">
                <table cellspacing="0" width="100%" class="left page_list"> 
                    <thead>
                    <tr>
                        <th width="10%">Page</th> 
                        <th width="10%">Type</th>                            
                        <th width="10%">Heading</th>                            
                        <!--
                        <th width="10%">Asset Category</th>                            
                        <th width="10%">Asset</th>                            
                        -->
                        <th width="10%">Image</th>                            
                        <th width="10%">Delete</th>                            
                    </tr>
                    </thead>
                    <tbody>
                    <!-- will be load by ajax -->
                    </tbody>
                </table>
                
                <a href="javascript:;" class="button right center" id="deletebrochure">Delete</a>
            </div>
            
        </div><!-- END tab Brochure -->
        
        </div> <!-- end tabs -->
                
<?php endif; ?>             
                <div class="clear"></div>
    
                <label for="heading">&nbsp;</label> 
                <input id="button" type="submit" value="<? echo ($project_id == "") ? "Create New Project": "Update Project"; ?>" /><br/>                    
                <div class="clear"></div>
            </form>
         <?php $this->load->view("admin/project/navigation"); ?>