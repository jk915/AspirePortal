<script language="Javascript" type="text/javascript">

var states = {};
<?php foreach($states->result() as $s):?>
states['<?php echo $s->state_id;?>'] = <?php echo '"' . $s->name . '"';?>;
<?php endforeach;?>


</script>

<body id="contact">
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/state/navigation"); ?>                        
            
            <form class="plain" id="frmState" name="frmState" action="<?php echo base_url()?>admin/statemanager/state/<?php echo $state_id?>"  method="post">
                <input type="hidden" id="folder" value="<?php echo $state_id;?>" />
                <input type="hidden" id="state_id" value="<?php echo $state_id;?>" />
     
<?php if(isset($state)) : // We're editing and existing state.  Show the tabs. ?>
                
				<br><br>
			    <input id="submitbutton2" class="button right" type="button" value="<? echo ($state_id == "") ? "Create New State": "Update State"; ?>" style="margin-top:-40px;" />

                <!-- tabs -->
                <ul class="css-tabs skin2">
                    <li><a href="#" id="tabDetail">Details</a></li>
                    <li><a href="#">HTML Content</a></li>
                    <li><a href="#">Sections</a></li>
                    <li><a href="#">Links</a></li>
                    <li><a href="#" id="tabDocument">Documents</a></li>                    
                    <li><a href="#" id="tabGallery">Gallery</a></li>                    
                    <li><a href="#">Comments</a></li>                    
                </ul>   
                
                <!-- panes -->
                <div class="css-panes skin2">
                    <div style="display:block">
<?php endif; ?>
                        <div class="left" style="width:33%">
							<label for="state_name">State Name:<span class="requiredindicator">*</span></label>
                    		<input id="state_name" class="required" type="text" value="<? echo ($state_id !="") ? $state->state_name : ""; ?>" name="state_name"/>						
    						<div class="clear"></div>
                            <label for="region_id">State:<span class="requiredindicator">*</span></label>
                            <select id="region_id" name="region_id" class="required">
                                <option value="">Choose</option>
                                <?php 
                                if($states)
								{
                                    foreach($states->result() as $s)
									{
                                ?>
								<option value="<?php echo $s->state_id; ?>" <?php echo ((isset($state)) && ($state->region_id == $s->state_id)) ? "selected" : ""; ?> ><?php echo $s->preferredName; ?></option>
								<?php
								
									}
								}
								?>
                            </select>                              
                            <!--echo $this->utilities->print_select_options($states,"state_id","preferredName",($state_id !="") ? $state->region_id : "");--> 
							<?php //var_dump($state);die;?>
                            <div class="left" style="padding-top: 20px;">
    							<input type="checkbox" name="enabled" value="1" class="left" <? echo ($state_id !="") ? (($state->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Enabled</label> 
    						</div>
						
						</div>
<?php if(isset($state)) : ?>
						<div class="left" style="width:33%">
                            
						<label for="state_hero_image">state Hero Image</label>
                            <div class="hero_img">
                                <?php if (!empty($state->state_hero_image)) : ?>
                              	<img id="area_hero_img" src="<?php  echo base_url().$state->state_hero_image; ?>" width="250" class="<?php echo ($state->state_hero_image == "") ?  "hidden" : ""; ?>" />
                              	<?php endif; ?>
                          	</div>
        					<input class="<?php echo ($state->state_hero_image == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Hero Image" id="delete_hero_image" />
        					<!--<input type="file" name="area_hero_image" id="area_hero_image" class="showif <?php echo (!empty($state->state_hero_image)) ?  "hidden" : ""; ?>" />  -->
        					<div id="hero_image_upload" class="showif <?php echo (!empty($state->state_hero_image)) ?  "hidden" : ""; ?>"></div>
                        </div>
<?php endif; ?>
							
        				<div style="width:33%; float:left;">
						
						<?php if(isset($state)) : ?>
                            <p>Last Modified: <?=$this->utilities->isodatetime_to_ukdate($state->last_modified); ?></p>
                            <?php endif; ?> 
						</div>
						
<?php if(isset($state)) : ?>

					</div><!-- END first tab -->
					
                    <div>
                    
                        <div class="clear"></div>
                        
                        <label for="short_description">Short Description / Intro</label><br />
                        <textarea id="wysiwyg" cols="20" rows="10" name="short_description" style="width:880px;height:300px" class="editor"><? echo ($state_id !="") ? $state->short_description : "" ?></textarea>
                        <input type="hidden" name="wysiwygWordCount" value="310" />
                        
                        <div class="clear"></div>
                        
                        <label for="overview">Brochure overview</label><br />
                        <textarea id="wysiwyg2" cols="20" rows="10" name="overview" style="width:880px;height:300px" class="editor"><? echo ($state_id !="") ? $state->overview : "" ?></textarea>
                        
                    </div><!-- END html content tab -->
                    
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
                            
                            <label for="content">Section Content:<span class="requiredindicator">*</span></label>
                            <input type="hidden" id="meta_id" value=""/>
                            <textarea id="wysiwyg4" cols="20" rows="10" style="width:880px;height:300px;padding-top:5px;" class="editor"></textarea><br />
                            
                            <div class="clear"></div>
                            <a href="javascript:;" class="button left center savemeta">Save</a>
                            
                        </div>
                        <div class="clear"></div>
                    </div><!-- END section tab -->
                    
                    <div>
                    
                        <table cellspacing="0" width="100%" class="left link_listing"> 
                            <tr>
                                <th width="10%">ID</th> 
                                <th align="left">Title</th>                            
                                <th width="50%" align="left">Url</th>                            
                                <th width="10%">Delete</th>                            
                            </tr>
                    <?php $i = 0;?>
                    <?php if ($links) : ?>
                        <?php foreach ($links->result() AS $link) : ?>
                            <?php
                                if($i++ % 2==1) $rowclass = "admintablerow";
			                    else  $rowclass = "admintablerowalt";
                            ?>
                            <tr class="<? print $rowclass;?>">
                                <td class="admintabletextcell" align="center"><?php echo $link->link_id;?></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="<?php echo $link->link_id;?>" class="editlink"><?php echo $link->title;?></a></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><?php echo $link->url;?></td>
                                <td class="center"><input type="checkbox" class="linktodelete" value="<?php echo $link->link_id;?>" /></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                        </table>
                        
                        <div class="clear"></div>
                        
                        <a href="javascript:;" class="button right center" id="deletelink" style="margin-left:10px;">Delete</a>
                        <a href="javascript:;" class="button right center" id="addnewlink">Add new</a>
                        
                        <div class="clear"></div>
                        
                        <div id="formaddlink" style="display:none;">
                        
                            <label for="link_title">Link Title:<span class="requiredindicator">*</span></label>
                            <input type="text" id="link_title"/>
                            <div class="clear"></div>
                            
                            <label for="url">Url:<span class="requiredindicator">*</span></label>
                            <input type="text" id="url"/>
                            <input type="hidden" id="link_id"/>
                            
                            <div class="clear"></div><br />
                            <a href="javascript:;" class="button left center savelink">Save</a>
                            
                        </div>
                        
                        <div class="clear"></div>
                        
                    </div><!-- END tab web link -->
                    
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
                    </div><!-- END document tab -->
                    
                    <div>
                        <div class="right" style="padding-right:10px">

                            <label for="upload_file">Upload a new image</label>
                            <!--<input type="file" name="upload_file" id="upload_file" />    -->
                            <div id="upload_file"></div>
            
                        </div>
            
                        <div class="clear"></div>
            
                        <label>state Images</label>
            
                        <br/>
            
                        <div id="files_listing">
            
                            <div  id="page_listing">
                                <? $this->load->view('admin/state/file_listing',array('files'=>$images,'pages_no' => count($images) / $images_records_per_page)); ?>
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
                    </div><!-- END gallery tab -->
                    
                    <div><!-- BEGIN comments tab -->
                        <table cellspacing="0" width="100%" class="left commentlisting"> 
                            <tr>
                                <th width="10%">ID</th>
                                <th align="left">Comment</th>
                                <th width="10%">Delete</th>
                            </tr>
                    <?php if ($comments) : ?>
                        <?php foreach ($comments->result() AS $index=>$comment) : ?>
                            <tr id="acomment_<?php echo $comment->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
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
                                       
                </div>
<?php endif; ?>
                <div class="clear"></div>
    
                <label for="heading">&nbsp;</label> 
                <input id="button" type="submit" value="<? echo ($state_id == "") ? "Create New state": "Update state"; ?>" /><br/>                    
            </form>
            
         <br/>
         <?php $this->load->view("admin/state/navigation"); ?>    