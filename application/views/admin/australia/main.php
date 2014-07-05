<body id="contact">
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
		<?php
			$australia_id = $australia->australia_id;
		?>
        <div id="content">

            <?php $this->load->view("admin/australia/navigation"); ?>                        
            
            <form class="plain" id="frmAustralia" name="frmAustralia" action="<?php echo base_url()?>admin/australia/update_australia"  method="post">
                <input type="hidden" id="folder" value="<?php echo $australia_id;?>" />
                <input type="hidden" id="australia_id" value="<?php echo $australia_id;?>" />
     
<?php if(isset($australia)) : // We're editing and existing region.  Show the tabs. ?>
                
				<br><br>
			    <input id="submitbutton2" class="button right" type="submit" name="submit" value="<? echo ($australia_id == "") ? "Create New Australia": "Update Australia"; ?>" style="margin-top:-40px;" />

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
                        <div class="left" style="width:100%">
						<div class="left" style="width: 33%;">
							<label for="australia_name">Name:<span class="requiredindicator">*</span></label>
                    		<input id="australia_name" readonly class="required" type="text" value="<? echo ($australia_id !="") ? $australia->australia_name : ""; ?>" name="australia_name" />						
    						
    						<div class="clear"></div>
                             
							<br/><br/>
							<input type="checkbox" name="enabled" value="1" class="left" <? echo ($australia_id !="") ? (($australia->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Enabled</label> 
    					</div>		
                           
                            
<?php if(isset($australia)) : ?>
						<div class="left" style="width:33%">	
						<label for="australia_hero_image">Australia Hero Image</label>
                            <div class="hero_img">
                                <?php if (!empty($australia->australia_hero_image)) : ?>
                              	<img id="australia_hero_img" src="<?php  echo base_url().$australia->australia_hero_image; ?>" width="250" class="<?php echo ($australia->australia_hero_image == "") ?  "hidden" : ""; ?>" />
                              	<?php endif; ?>
                          	</div>
        					<input class="<?php echo ($australia->australia_hero_image == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Hero Image" id="delete_hero_image" />
        					<!--<input type="file" name="australia_hero_image" id="australia_hero_image" class="showif <?php echo (!empty($australia->australia_hero_image)) ?  "hidden" : ""; ?>" />  -->
        					<div id="hero_image_upload" class="showif <?php echo (!empty($australia->australia_hero_image)) ?  "hidden" : ""; ?>"></div>
                        </div>
<?php endif; ?>
                        <div style="width:33%; float:left;">
						
						<?php if(isset($australia)) : ?>
                            <p>Last Modified: <?=$this->utilities->isodatetime_to_ukdate($australia->last_modified); ?></p>
                            <?php endif; ?> 
						</div>
                        
					</div>	
<?php if(isset($australia)) : ?>

					</div><!-- END first tab -->
					
                    <div>
                    
                        <div class="clear"></div>
                        
                        <label for="short_description">Short Description / Intro</label><br />
                        <textarea id="wysiwyg" cols="20" rows="10" name="short_description" style="width:880px;height:300px" class="editor"><? echo ($australia_id !="") ? $australia->short_description : "" ?></textarea>
                        <input type="hidden" name="wysiwygWordCount" value="310" />
                        
                        <div class="clear"></div>
                        
                        <label for="overview">Brochure overview</label><br />
                        <textarea id="wysiwyg2" cols="20" rows="10" name="overview" style="width:880px;height:300px" class="editor"><? echo ($australia_id !="") ? $australia->overview : "" ?></textarea>
                        
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
            
                        <label>Australia Images</label>
            
                        <br/>
            
                        <div id="files_listing">
            
                            <div  id="page_listing">
                                <? $this->load->view('admin/australia/file_listing',array('files'=>$images,'pages_no' => count($images) / $images_records_per_page)); ?>
								<table cellspacing="0" class="cmstable" id="areadoclist" > 
        <tr>
            <th>Description</th>  
            <th>Download</th>
            <th style="width: 20px;">Delete</th> 
        </tr>
		</table>
								
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
                <input id="button" type="submit" name="submit" value="<? echo ($australia_id == "") ? "Create New Australia": "Update Australia"; ?>" /><br/>                    
            </form>
            
         <br/>
         <?php $this->load->view("admin/australia/navigation"); ?>    