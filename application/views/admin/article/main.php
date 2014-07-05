<body id="contact" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/ckeditor/ckeditor_pages_articles"); ?>
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">
            <?php $this->load->view("admin/article/navigation"); ?>
            <p><?php if(isset($message)) echo $message?></p>    
        
            <form class="plain" id="frmArticle" name="frmArticle" action="<?php echo base_url()?>admin/contentmanager/<?php echo ($article_id == "") ? "new_article": "article/".$article_id;?>/<? if(isset($category_id)) echo $category_id; ?>"  method="post" onsubmit="setAssignedBlocks();" enctype="multipart/form-data">
               <h2>Item Properties</h2>    
                
               <?php
                  if(isset($article))
                  {
                     // We're editing and existing article.  Show the tabs.
                     ?> 
                <!-- tabs -->
                <ul class="css-tabs skin2">
                    <li><a href="#">Main Details</a></li>
                    <?php if($category->enable_tab_seo) : ?>
                    <li><a href="#">Google/SEO</a></li>
                    <?php endif; ?>
                    <?php if($category->enable_tab_content) : ?>
                    <li><a href="#">HTML Content</a></li>
                    <?php endif; ?>
                    <?php if($category->enable_tab_gallery) : ?>
                    <li><a href="#" id="tabGallery">Gallery</a></li>
                    <?php endif; ?>
                    <?php if($category->enable_tab_blocksleft) : ?>
                    <li><a href="#">Left Sidebar</a></li>
                    <?php endif; ?>
                    <?php if($category->enable_tab_blocksright) : ?>
                    <li><a href="#">Right Sidebar</a></li>
                    <?php endif; ?>
                    <?php if($category->enable_tab_area) : ?>
                    <li><a href="#">Areas</a></li>
                    <?php endif; ?>
                    <?php if($category->enable_tab_states) : ?>
                    <li><a href="#">States</a></li>
                    <?php endif; ?>     
                    <?php if($category->enable_tab_usertypes) : ?>
                    <li><a href="#">User Types</a></li>
                    <?php endif; ?>                                   
                    <?php if($category->enable_tab_documents) : ?>
                    <li><a href="#">Documents</a></li>
                    <?php endif; ?>                    
                    <?php if($category->enable_tab_relation) : ?>
                    <li><a href="#">Related Items</a></li>
                    <?php endif; ?>
                </ul>   
                
                <!-- start tabs -->
               <div class="css-panes skin2">
                  <!-- being first tab article details -->
                  <div style="display:block">
                     <?php
                  }
               ?>
                   <div class="left" style="width: 50%">
                       
                         <label for="article_title">Item Title <span class="requiredindicator">*</span></label> 
                         <input type="text" name="article_title" id="article_title" class="required" value="<?php echo ($article_id !="") ? $article->article_title : "" ?>" maxlength="50"/>

                         <label for="article_code">Item Code / URL ID <span class="requiredindicator">*</span></label>
                         <input type="text" name="article_code" id="article_code" class="required" value="<?php  echo ($article_id !="") ? $article->article_code : "" ?>"/>

                         <div class="clear"></div>
                      
                         <?php 
                         if(isset($article)) 
                         { 
                         ?> 
                         
                         <?php /*<label for="article_heading"><?php echo ($category->name == "Podcasts") ? "Podcast Link" : "Article Heading"; ?></label> 
                         <input type="text" name="article_heading" id="article_heading" value="<?php echo ($article_id !="") ? $article->article_heading : "" ?>"/>                           
						*/?>
                         <div id="rows">
                         	<?php if($category->enable_field_publicationdate) : ?>
                         	<label for="article_date"><?php echo ((isset($category)) && (strtoupper($category->name) == "EVENTS")) ? "Event Date" : "Publication Date"; ?> <span class="requiredindicator">*</span></label>
                         	<div class="clear"></div>
                         	<input type="text" name="article_date" class="required thin date-pick" value="<?php echo ($article_id !="" && isset($article->article_date) && $article->article_date !="") ? $this->utilities->iso_to_ukdate($article->article_date) : "" ?>" />
                         	<?php endif; ?>
                         	
                         	<?php if($category->enable_field_author) : ?>
                         	<div class="clear"></div>
                            <label for="author">Author</label>
                            <div class="clear"></div>
                            <input type="text" style="margin-left:0px;" name="author" class="author" value="<?php echo ($article_id !="") ? $article->author : "" ?>"/>
                            <?php endif; ?>
                         </div>

                         <div class="clear"></div>

                         <label for="article_order">Item Order</label>
                         <input type="text" name="article_order" class="required number thin" value="<? echo ($article_id !="" && isset($article->article_order)) ? $article->article_order : "" ?>" />
                         
                         <div class="clear"></div> 
                         
                         <?php if($category->enable_field_source) : ?> 
                         <label for="source">Source</label>
                         <input type="text" name="source" value="<?php echo ($article_id !="") ? $article->source : "" ?>"/>
                         <div class="clear"></div> 
                         <?php endif; ?>
                         
                         <?php if($category->enable_field_tags) : ?> 
                         <label for="tags">Tags</label>
                         <input type="text" name="tags" value="<?php echo ($article_id !="") ? tags_remove_commas($article->tags) : "" ?>"/>
                         <div class="clear"></div>       
                         <?php endif; ?>
                         
                        <?php if($category->enable_field_www) : ?>
                        <label for="www">Web Link</label>
                        <input type="text" name="www" id="www" value="<?php echo ($article_id !="") ? $article->www : "" ?>"/>
                        <?php endif; ?>                         
                         
                         <?php if($category->enable_field_view) : ?>
                         <div class="clear"></div> 
                         <label for="tags">Template View</label>
                         <input type="text" name="view" value="<?php echo ($article_id !="") ? $article->view : "" ?>"/>
                         <div class="clear"></div> 
                         <?php endif; ?>                         

                         <?php if($category->enable_field_article_icon) : ?>
                         <div class="clear"></div> 
                         <label for="article_icon">Article Icon <span class="requiredindicator">*</span></label>
                         <select id="article_icon" name="article_icon" class="required">
                         	<option value="">Choose</option>
                         	<?=$this->utilities->print_select_options_array($article_icon,false,($article_id !="") ? $article->article_icon : ""); ?>
                         </select>
                         <div class="clear"></div> 
                         <?php endif; ?> 
                         
                         <?php
                         }
                         ?>
                         <label for="category_id">Item Category *</label>
                         <select id="category_id" name="category_id" class="required">
                     	    <option value="">Choose</option>
                     	    
                            <?php
                        	    if($article_id != "")
                        		    echo $this->utilities->print_select_category_tree($categories, $article->category_id);
                        	    else {
                        		    echo $this->utilities->print_select_category_tree($categories, $category_id);                          
                                }
                            ?>
                         </select>                     

                   </div>
                   
                    <?php  if(isset($article)) :?> 
                   <div class="left" style="width:50%">
                        <?php if($category->enable_field_document_attachment) : ?>
                        <label for="attachment">Document Attachment</label>
                        <?php 
    						$show_attachment = '';
    						$show_del_attachment_btn = '';
    						if( $article->attachment == ""){ 
    							$show_attachment = ' style="display:none;"';
    							$show_del_attachment_btn = ' style="display:none;"';
    						}
    						
    					?>
                        <div class="article_attachment" id="attachmentlink">
                        <?php if ($article_id AND !empty($article->attachment)) :?>
                            <a href="<?php echo base_url() . ARTICLE_FILES_FOLDER . $article_id . '/' . $article->attachment?>"><?php echo max_chars($article->attachment, 50);?></a>
                        <?php endif; ?>
                        </div>
       					<div style="padding-top:5px;">
       						<input <?php echo $show_del_attachment_btn; ?> type="button" id="delete_attachment" value="Delete attachment" class=" button">
                        </div>
                        
                        <div id="attachment_upload" class="showif <?php echo (!empty($article->attachment)) ?  "hidden" : ""; ?>"></div>
                        <?php endif; ?>
                        
                         <?php if($category->enable_field_featured) : ?> 
                         <label><input type="checkbox" name="is_featured" value="1" <?php echo ($article_id!="" AND $article->is_featured) ? 'checked="checked"' : ''?> /> Is featured?</label>
                         <div class="clear"></div> 
                         <?php endif; ?>
                         
                         <?php if($category->enable_field_video_code) : ?> 
                         <label for="category_id">Video Code</label>
                         <textarea name="video_code"><?php echo ($article_id !="") ? $article->video_code : "" ?></textarea>
                         <div class="clear"></div> 
                         <?php endif; ?>
                         
                         <?php if($category->enable_field_comments) : ?> 
                         <label for="comments">Comments</label>
                         <textarea name="comments"><?php echo ($article_id !="") ? $article->comments : "" ?></textarea>
                         <div class="clear"></div> 
                         <?php endif; ?>                         
                         
                                                  
                   </div>
                   <?php endif; ?>

                   
                   <div class="clear"></div>      
                     

                <?php if(isset($article)) : ?>

                    <?php if($category->enable_field_prices_form) : ?>
                    <label for="prices_form">Prices From</label>
                    <input type="text" name="prices_form" id="prices_form" value="<?php echo ($article_id !="") ? $article->prices_form : "" ?>"/>
                    <?php endif; ?>
                    
                    <?php if($category->enable_field_wholesale_price) : ?>
                    <label for="wholesale_price">Wholesale Price</label>
                    <input type="text" name="wholesale_price" id="wholesale_price" value="<?php echo ($article_id !="") ? $article->wholesale_price : "" ?>"/>
                    <?php endif; ?>
                    
                    <?php if($category->enable_field_number_of_bedrooms) : ?>
                    <label for="number_of_bedrooms">Number Of Bedrooms</label>
                    <input type="text" name="number_of_bedrooms" id="number_of_bedrooms" value="<?php echo ($article_id !="") ? $article->number_of_bedrooms : "" ?>"/>
                    <?php endif; ?>
                    
                    <?php if($category->enable_field_number_of_bathrooms) : ?>
                    <label for="number_of_bathrooms">Number Of Bathrooms</label>
                    <input type="text" name="number_of_bathrooms" id="number_of_bathrooms" value="<?php echo ($article_id !="") ? $article->number_of_bathrooms : "" ?>"/>
                    <?php endif; ?>
                    
                    <?php if($category->enable_field_number_of_car) : ?>
                    <label for="number_of_car">Number Of Car Spaces</label>
                    <input type="text" name="number_of_car" id="number_of_car" value="<?php echo ($article_id !="") ? $article->number_of_car : "" ?>"/>
                    <?php endif; ?>
                    
                    <?php if($category->enable_field_heroimage) : ?>
                    <label for="hero_image">Hero Image</label>
                    <?php
	                	/*if(($article_id != "") && ($article->hero_image != ""))
	                	{
							$thumbnail = base_url() . $article->hero_image;
							?>
							<div id="hero_image">
								<img class="left" src="<?php echo $thumbnail; ?>" height="100" width="100" />
								<img id="hero-loader" class="hidden left left-margin" src="<?php echo base_url(); ?>images/admin/ajax-loader-big.gif" border="0" width="31" height="31" alt="ajax-loader.gif (847 bytes)" />
								<div class="clear"></div>
								<div><a href="javascript:deleteHero();">Delete Image</a></div>
							</div>

							<?php
	                	}
					?>
                    <input type="file" name="hero_image" id="hero_image" class="hero_img" >
                    */?>
                    
                    <?php 
						$show_img = '';
						$show_del_btn = '';
						//$show_hid_chb = '';
						if( $article->hero_image == ""){ 
							$show_img = ' style="display:none;"';
							$show_del_btn = ' style="display:none;"';
							//$show_hid_chb = ' style="display:none;"';
						}
						
						$show_hid_chb = ' style="display:none;"';
					
					?>
                    <div class="article_hero_img">
                        <img <?php echo $show_img; ?> style="max-width:270px" src="<?php echo (($article_id !="") && ($article->hero_image != "")) ? base_url() . $article->hero_image . "_detail.jpg" : "" ?>" alt="logo_img" id="logo_img" style="display: inline;">                    
                    </div>
   					<div>
   						<input <?php echo $show_del_btn; ?> type="button" id="delete_logo" value="Delete hero image" class=" button">
	                    <span id="hide_btn"<?php echo $show_hid_chb; ?> >
	                    <input type="checkbox" value="1" class="showif " id="hide_logo" name="hide_logo">Hide hero image
	                    </span>
                    </div>
                    
                    <div id="hero_image_upload" class="showif <?php echo (!empty($article->hero_image)) ?  "hidden" : ""; ?>"></div>
                    <?php endif; ?>
                    
                    <?php if($category->enable_field_heroimagealt) : ?>
                    <label for="alt_hero_image">Hero Image Alt Text</label>
                    <input type="text" name="alt_hero_image" id="alt_hero_image" value="<?php echo ($article_id !="") ? $article->alt_hero_image : "" ?>"/>                 
                    <?php endif; ?>
                    
                    <?php if($category->enable_field_status) : ?>
                    <label for="status">Status</label>
                    <select name="status" class="left">
                        <option value="Current" <? echo ($article->status == "Current") ? "selected" :""?>>Current</option>
                        <option value="Completed" <? echo ($article->status == "Completed") ? "selected" :""?>>Completed</option>
                    </select><br /><br />
                    <?php endif;?>
                    
                    <div class="clear top-margin"></div>
                    
                    <input type="checkbox" name="enabled" value="1" class="left" <? echo ($article_id !="") ? (($article->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Published</label>
					
					<!-- By Mayur - TasksEveryday -->
					<div style="position:relative; left:13px;">
					<input type="checkbox" name="is_featured" value="1" class="left" <? echo ($article_id !="") ? (($article->is_featured == 1) ? "checked" :"") : "checked" ?>  /><label for="is_featured" class="left" style="padding-top:0px">&nbsp;Featured</label></div>
                    
                    <!-- By Mayur - TasksEveryday -->
					
					<?php if($category->enable_field_agent_login) : ?>
                    <div class="clear top-margin"></div>
                    <input type="checkbox" name="agent_login" value="1" class="left" <? echo ($article_id !="") ? (($article->agent_login == 1) ? "checked" :"") : "checked" ?>  /><label for="agent_login" class="left" style="padding-top:0px">&nbsp;Agent login</label>
                    <?php endif;?>
                    
                    <div class="clear top-margin"></div>

                  </div><!-- end first tab -->    
                 
                  <!-- being second tab google / seo -->
                  <?php if($category->enable_tab_seo) : ?>
                  <div>
                     <label for="meta_title">Browser title / Bookmark name</label> 
                     <input type="text" name="meta_title" value="<? echo ($article_id !="") ? $article->meta_title : "" ?>"/>
                     <br/>

                     <label for="meta_keywords">Keywords</label> 
                     <input type="text" name="meta_keywords" value="<? echo ($article_id !="") ? $article->meta_keywords : "" ?>"/>
                     <br/>

                     <label for="meta_description">Description / Call to action</label> 
                     <input type="text" name="meta_description" value="<? echo ($article_id !="") ? $article->meta_description : "" ?>"/>
                     <br/>

                     <label for="meta_robots">Search Robots Permissions:</label>
                     <select name="meta_robots">
                        <?=$this->utilities->print_select_options_array($robots,false,($article_id !="") ? $article->meta_robots : ""); ?>                
                     </select>
                        
                  </div><!-- end second tab -->
                  <?php endif; ?>
                  
                  <?php if($category->enable_tab_content) : ?>
                  <!-- begin third tab -->  
                  <div>

                  	<?php if($category->enable_field_shortdescription) : ?>
                     <div class="clear"></div>
                     <label for="short_description">Introduction</label>
                     <?php $this->load->view("admin/ckeditor/ckeditor_and_history", array( "id" => "wysiwyg_short_description", "name" => "short_description", "table" => "articles", "content" => ($article_id !="") ? $article->short_description : "", "foreign_id" => $article_id )); ?>
                     <?php endif; ?>

                     <?php if($category->enable_field_content) : ?>
                     <div class="clear"></div>
                     <label for="content">Article body</label>
                     <?php $this->load->view("admin/ckeditor/ckeditor_and_history", array( "id" => "wysiwyg_content", "name" => "content", "table" => "articles", "content" => ($article_id !="") ? $article->content : "", "foreign_id" => $article_id )); ?>
                     <?php endif; ?>
                  </div><!-- end third tab -->
                  <?php endif; ?>

                  
                  <?php if($category->enable_tab_gallery) : ?>
                  <!-- begin images tab -->
                  <div>

                      <div class="left">
                            <label>Images for Article </label><br/>
                            <div class="hint">Note, images should be less than 2MB in size, otherwise you may experience problems with uploading.</div>
                        </div>
                        <div class="right" style="padding-right:10px">
                             <div id="gallery_upload_file"></div>
                        </div>
                        <div class="clear"></div>

                        <br/>
                        
                        <div id="files_listing">
                            <div id="page_listing">
                                <?php $this->load->view('admin/article/file_listing'); ?>
                            </div>

                            <div class="clear"></div>
                            <div id="controls">
                                <div class="right">
                                    <input class="button" type="button" value="Delete Selected Files" id="delete_files" />
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                  </div>
                  <!-- ending images tab -->
                  <?php endif; ?>
                  
                  <?php if($category->enable_tab_blocksleft) : ?>
                  <!-- sidebar left blocks tab -->
                    <div>
                        <div class="left" style="width: 300px;">
                            <label>Available Blocks</label>
                            <select id="blocks_available_left" name="blocks_available_left" size="10">
                            <?php
                            if($blocks)
                            {
                                foreach($blocks->result() as $block)
                                {
                                    if(!$this->utilities->is_in_recordset($blocks_left, "block_id", $block->block_id))
                                        print '<option value="' . $block->block_id . '">' . $block->block_name . '</option>';
                                }
                            }
                            ?>
                            </select>
                            <a href="left" class="btnAssignBlock button top-margin-sm">Assign Block &gt;&gt;</a>
                        </div>
                        
                        <div class="left" style="width: 290px;">
                            <label>Assigned Blocks</label>
                            <select id="blocks_left" name="blocks_left" size="10">
                            <?php
                            if($blocks_left)
                            {
                                foreach($blocks_left->result() as $block)
                                {
                                    print '<option value="' . $block->block_id . '">' . $block->block_name . '</option>';
                                }
                            }
                            ?>
                            </select>
                            <a href="left" class="btnRemoveBlock button top-margin-sm">&lt;&lt; Remove Block</a>                
                        </div> 
                        
                        <div class="left" style="width: 155px; margin-top: 60px;">
                            <div id="updown_left" class="hidden">
                                <a href="left" class="btnMoveUp button top-margin-sm">Up</a>                
                                <a href="left" class="btnMoveDown button top-margin-sm">Down</a>                
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div><!-- end left sidebar blocks tab -->
                    <?php endif; ?>
                    
                    <?php if($category->enable_tab_blocksright) : ?>
                    <!-- sidebar right blocks tab -->
                    <div>
                        <div class="left" style="width: 300px;">
                            <label>Available Blocks</label>
                            <select id="blocks_available_right" name="blocks_available_right" size="10">
                            <?php
                            if($blocks)
                            {
                                foreach($blocks->result() as $block)
                                {
                                    if(!$this->utilities->is_in_recordset($blocks_right, "block_id", $block->block_id))
                                        print '<option value="' . $block->block_id . '">' . $block->block_name . '</option>';
                                }
                            }
                            ?>
                            </select>
                            <a href="right" class="btnAssignBlock button top-margin-sm">Assign Block &gt;&gt;</a>
                        </div>
                        
                        <div class="left" style="width: 290px;">
                            <label>Assigned Blocks</label>
                            <select id="blocks_right" name="blocks_right" size="10">
                            <?php
                            if($blocks_right)
                            {
                                foreach($blocks_right->result() as $block)
                                {
                                    print '<option value="' . $block->block_id . '">' . $block->block_name . '</option>';
                                }
                            }
                            ?>
                            </select>
                            <a href="right" class="btnRemoveBlock button top-margin-sm">&lt;&lt; Remove Block</a>                
                        </div> 
                        
                        <div class="left" style="width: 155px; margin-top: 60px;">
                            <div id="updown_right" class="hidden">
                                <a href="right" class="btnMoveUp button top-margin-sm">Up</a>                
                                <a href="right" class="btnMoveDown button top-margin-sm">Down</a>                
                            </div>
                        </div>
                        <div class="clear"></div>
                    </div><!-- end left sidebar blocks tab -->
                    <?php endif; ?>  
                    
                    <?php if($category->enable_tab_area) : ?>
                    <!-- areas tab -->
                    <div>
                        <label>Areas:</label><br />
                        <?php if ($areas) : ?>
                    <ul style="padding:0;">
                    <?php foreach ($areas->result() AS $area) : ?>
                        <li style="background:none;padding:0;"><label><input type="checkbox" value="<?php echo $area->area_id;?>" name="areas[]" <?php echo in_array($area->area_id,$article_areas) ? 'checked="checked"' : "";?>/> <?php echo $area->area_name;?></label></li>
                    <?php endforeach; ?>
                    </ul>
                        <?php endif; ?>
                    </div><!-- end areas tab -->
                    <?php endif; ?>
                    
                    <?php if($category->enable_tab_states) : ?>
                    <!-- states tab -->
                    <div>
                        <label>States:</label><br />
                        <?php if ($states) : ?>
                    <ul style="padding:0;">
                    <?php foreach ($states->result() AS $state) : ?>
                        <li style="background:none;padding:0;"><label><input type="checkbox" value="<?php echo $state->state_id;?>" name="states[]" class="article_state" <?php echo in_array($state->state_id,$article_states) ? 'checked="checked"' : "";?>/> <?php echo $state->name;?></label></li>
                    <?php endforeach; ?>
                    </ul>
                    
                    <p class="top-margin20"><a href="javascript:void(0);" onclick="$('.article_state').attr('checked', 'checked');">Select All</a></p>
                    
                        <?php endif; ?>
                    </div><!-- end states tab -->
                    <?php endif; ?>
                    
                    <?php if($category->enable_tab_usertypes) : ?>
                    <!-- usertypes tab -->
                    <div>
                        <label>User Type Permissions:</label><br />
                        <?php if ($usertypes) : ?>
                    <ul style="padding:0;">
                    <?php foreach ($usertypes->result() AS $type) : ?>
                        <li style="background:none;padding:0;"><label><input type="checkbox" value="<?php echo $type->user_type_id;?>" name="usertypes[]" class="user_type" <?php echo in_array($type->user_type_id,$article_usertypes) ? 'checked="checked"' : "";?>/> <?php echo $type->type;?></label></li>
                    <?php endforeach; ?>
                    </ul>
                    
                    <p class="top-margin20"><a href="javascript:void(0);" onclick="$('.user_type').attr('checked', 'checked');">Select All</a></p>
                    
                        <?php endif; ?>
                    </div><!-- end states tab -->
                    <?php endif; ?>                                        
                    
                    <?php if($category->enable_tab_documents) : ?>
                    <!-- documents tab -->
                    <div>                         
                    	<table width="100%" align="center" cellspacing="0">
                    		<tr>
                    			<th>Document Name</th>
                    			<th>Document File</th>
                    			<th>Save</th>
                    		</tr>
                    		<?php
	                            for($x = 1; $x <= 5; $x++)
	                            {
	                            	$params = array("order" => $x);
	                            	$doc = false;
	                            	$docs = $this->document_model->get_list($doc_type = "article_document", $article_id, $params);
	                            	
	                            	if($docs)
	                            	{
										$doc = $docs->row();
	                            	}
									?>
                    		<tr>
                    			<td>
                    				<input type="text" id="doc<?php echo $x; ?>_name" name="doc<?php echo $x; ?>_name" value="<?php if($doc) echo $doc->document_name; ?>" />
                    			</td>
                    			<td>
                    				<input type="text" id="doc<?php echo $x; ?>_file" name="doc<?php echo $x; ?>_file" value="<?php if($doc) echo $doc->document_path; ?>" class="shorttxt" />
                    				<input type="button" class="select-file left-margin" onclick="selectFile('doc<?php echo $x; ?>_file');" value="Select" />
                    			</td> 
                    			<td>
                    				<input type="button" class="button saveDoc" value="Save" id="saveDoc<?php echo $x; ?>" />
                    			</td>                    				                   				
                    		</tr>										
									<?php	
	                            }
							?>
                    	</table>

                    </div>      
                    <?php endif; ?>                       
                    
                    <?php if($category->enable_tab_relation) : ?>
                    <!-- related items tab -->
                    <div>
                        <p>Select the item(s) that related to this item, hold Ctrl and click for multiple selection.</p>
                    <?php if ($cat_items) : ?>
                        <select multiple="multiple" name="related[]">
                            <?php foreach ( $cat_items->result() as $catItem ) : ?>
                            <option value="<?php echo $catItem->article_id?>" <?php echo in_array($catItem->article_id, $related_items) ? 'selected="selected"' : ''?>><?php echo $catItem->article_title?></option>
                            <?php endforeach; ?>
                        </select>
                    <?php else : ?>
                        <p>This item is the only item in this category.</p>
                    <?php endif;?>
                    </div>      
                    <?php endif; ?>                       
                    
               </div><!-- end tabs -->  
               <?php endif; ?>

                          
               <label for="heading">&nbsp;</label> 
               <input id="button" type="submit" value="<? echo ($article_id == "") ? "Create New Article": "Update Article"?>" /><br/>                
               <input type="hidden" name="assigned_blocks_right" value="" /> 
               <input type="hidden" name="assigned_blocks_left" value="" /> 
               <input type="hidden" name="postback" value="1" />
               <input type="hidden" name="id" id="article_id" value="<?php echo $article_id;?>" />
               <input type="hidden" name="parent_category_code" id="parent_category_code" value="<?php echo isset($parent_category_code) ? $parent_category_code : "";?>" />
               
               <?php
               		if( isset($article) )
               		{
			   ?>
			   			<input type="hidden" name="id" id="article_code" value="<?php echo $article->article_code;?>" />
			   <?php                			
               		}
               ?>               
               
            </form>

            <div class="top-margin"></div>
            
            <? $this->load->view("admin/article/navigation"); ?> 
