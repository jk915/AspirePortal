<body id="contact" >   
	<div id="wrapper">     
   	<? $this->load->view("admin/navigation");?>
        
      <div id="content">
      	<? $this->load->view("admin/articlecategory/navigation"); ?>            
         
      	<p><?=$message?></p>    
        
		  	<form class="plain" id="frmCategory" name="frmCategory" action="<?=base_url()?>admin/contentmanager/category_edit/<?=$category_id?>"  method="post" onsubmit="setAssignedBlocks();">
    	  		<h2>Category Properties</h2>    

				<!-- tabs -->
				<ul class="css-tabs skin2">
					<li><a href="#">Category Details</a></li>
					<li><a href="#">Google/SEO</a></li>
					<li><a href="#">Content</a></li>
					<li><a href="#">Features</a></li>
					<?php /*<li><a href="#">Sidebar Blocks</a></li>*/?>
				</ul>   
    
				<!-- panes -->
				<div class="css-panes skin2">
					<div style="display:block">
						<label for="name">Category Name:<span class="requiredindicator">*</span></label> 
						<input type="text" name="name" id="name" class="required" value="<? echo ($category_id !="") ? $category->name : "" ?>"/>
						
						<label for="category_code">Category Code / URL ID:<span class="requiredindicator">*</span></label> 
						<input type="text" name="category_code" id="category_code" class="required" value="<? echo ($category_id !="") ? $category->category_code : "" ?>"/>
						
						<label for="parent_id">Parent Category:</label>
						<select id="parent_id" name="parent_id">
							<option value="-1">None</option>
							<?php echo $this->utilities->print_select_options($categories, "category_id", "name", ($category_id !="") ? $category->parent_id : "", "", "category_id", ($category_id !="") ? $category->parent_id : "-1"); ?>
						</select>
						
						<label for="seq_no">Category Seq. No: <span class="requiredindicator">*</span></label> 
						<input type="text" name="seq_no" id="seq_no" class="required number" value="<? echo ($category_id !="") ? $category->seq_no : "" ?>"/>						

						
						<label for="order_by">Order Articles By:</label>
						<select id="order_by" name="order_by">
							<?php echo $this->utilities->print_select_options($order_by_options, "name", "name", ($category_id !="") ? $category->order_by : ""); ?>
						</select> 
            		
            		<label for="order_dir">Order Direction:</label>
            		<select id="order_dir" name="order_dir">
            			<option value="DESC" <?php if($category) {if($category->order_dir == "DESC") print "selected=\"selected\""; } ?>>Descending</option>
            			<option value="ASC" <?php if($category) {if($category->order_dir == "ASC") print "selected=\"selected\""; } ?>>Ascending</option>
            		</select>
            		
                    <?php /*<label for="sidebar_position">Sidebar Blocks Position:</label>
            		<select id="sidebar_position" name="sidebar_position">
            			<option value="0" <?php if($category) {if($category->sidebar_position == "0") print "selected=\"selected\""; } ?>>Top</option>
            			<option value="1" <?php if($category) {if($category->sidebar_position == "1") print "selected=\"selected\""; } ?>>Bottom</option>
            		</select> 

					*/?>
                    <label for="hero_image">Hero Image</label>
                    <input type="text" id="hero_image" name="hero_image" class="left" value="<?php echo ($category_id !="") ? $category->hero_image : "" ?>" />
                    <input type="button" class="select-file left-margin" onclick="selectFile('hero_image');" value="Select" />

                    <label for="overlay_text">Hero Image Alt Text<span class="requiredindicator">*</span></label>
                    <input type="text" name="hero_image_alt" id="hero_image_alt" class="" value="<? echo ($category_id !="") ? $category->hero_image_alt : "" ?>"/>

                    <?php /*<label for="background_image">Background Image</label>
                    <input type="text" id="background_image" name="background_image" class="left" value="<?php echo ($category_id !="") ? $category->background_image : "" ?>" />
                    <input type="button" class="select-file left-margin" onclick="selectFile('background_image');" value="Select" />

                    <label for="text_color">Nav Text Color:</label>
            		<select id="text_color" name="text_color">
            			<option value="white" <?php if($category) {if($category->text_color == "white") print "selected=\"selected\""; } ?>>White</option>
            			<option value="black" <?php if($category) {if($category->text_color == "black") print "selected=\"selected\""; } ?>>Black</option>
            		</select>
            		
					<label for="facebook">Facebook URL:</label> 
					<input type="text" name="facebook" id="facebook" value="<? echo ($category_id !="") ? $category->facebook : "" ?>"/> 
					
					<label for="twitter">Twitter:</label> 
					<input type="text" name="twitter" id="twitter" value="<? echo ($category_id !="") ? $category->twitter : "" ?>"/>
					
					<label for="vimeo">Vimeo:</label> 
					<input type="text" name="vimeo" id="vimeo" value="<? echo ($category_id !="") ? $category->vimeo : "" ?>"/>
					
					<label for="youtube">Youtube:</label> 
					<input type="text" name="youtube" id="youtube" value="<? echo ($category_id !="") ? $category->youtube : "" ?>"/>
                    
                    <label for="youtube">WWW:</label> 
                    <input type="text" name="www" id="www" value="<? echo ($category_id !="") ? $category->www : "" ?>"/>                    
            		*/?>
            		<label for="view">MVC View (leave blank unless you're a  coder):</label>
	                <input type="text" id="view" name="view" value="<?php echo ($category_id !="") ? $category->view : "" ?>"/>

	               <div class="top-margin"></div>
	               <input type="checkbox" id="enabled" name="enabled" value="1" class="left" <?php echo ($category_id !="") ? (($category->enabled == 1) ? "checked=\"checked\"" :"") : "checked" ?>  />
	               <label for="enabled" class="left" style="padding-top:0px">&nbsp;Category is active</label> 
	               
	               <div class="left">&nbsp;</div>
	               <input type="checkbox" id="generate_rss_feed" name="generate_rss_feed" value="1" class="left" <?php echo ($category_id !="") ? (($category->generate_rss_feed == 1) ? "checked=\"checked\"" :"") : "checked" ?>  />
	               <label for="generate_rss_feed" class="left" style="padding-top:0px">&nbsp;Generate RSS Feed</label> 
	           		<div class="clear top-margin"></div>
       		   </div><!-- end first tab -->    
        
        		<!-- begin SEO tab -->
        		<div>
            		<label for="meta_title">Meta title:</label> 
	               <input type="text" id="meta_title" name="meta_title" value="<? echo ($category_id !="") ? $category->meta_title : "" ?>"/>
	            
            		<label for="meta_keywords">Keywords:</label> 
	               <input type="text" id="meta_keywords" name="meta_keywords" value="<? echo ($category_id !="") ? $category->meta_keywords : "" ?>"/>
	            
            		<label for="meta_description">Description / Call to action:</label> 
	               <input type="text" id="meta_description" name="meta_description" value="<? echo ($category_id !="") ? $category->meta_description : "" ?>"/>

            		<label for="meta_robots">Search Robots Permissions:</label>
	               <select id="meta_robots" name="meta_robots">
               		 <?=$this->utilities->print_select_options_array($robots, false, ($category_id !="") ? $category->meta_robots : ""); ?>                
            		</select>
	            
        			</div><!-- end SEO tab -->
        
        			<!-- begin content tab -->
        			<div>
        				<label for="short_description">Short Description:</label>
            		<div style="height:330px">
                		<textarea id="short_description" cols="20" rows="10" name="short_description" style="width:880px; height:300px;" class="editor"><? echo ($category_id !="") ? $category->short_description : "" ?></textarea>        
            		</div>
            		
            		<div class="clear top-margin"></div>
            		
            		<label for="long_description">Long Description:</label>
            		<div style="height:330px">
                		<textarea id="long_description" cols="20" rows="10" name="long_description" style="width:880px; height:300px;" class="editor"><? echo ($category_id !="") ? $category->long_description : "" ?></textarea>        
            		</div>            		

		        </div><!-- end content tab -->
		        
        		<!-- begin FEATURES tab -->
				<div>
					<?php
						$options = array(
							array("enable_tab_seo", "Google / SEO"),
							array("enable_tab_content", "HTML Content"),
							array("enable_tab_blocksleft", "Left Sidebar Blocks"),
							array("enable_tab_blocksright", "Right Sidebar Blocks"),
							array("enable_tab_gallery", "Gallery"),
							array("enable_tab_documents", "Documents"),
							array("enable_tab_area", "Areas"),
							array("enable_tab_relation", "Related Items"),
                            array("enable_tab_states", "States"),
                            array("enable_tab_usertypes", "User Types")
							);
					?>
					<p><b>Enabled Tabs</b></p>
					
					<?php
	                	foreach($options as $option)
	                	{
	                		$option_id = $option[0];
	                		$option_caption = $option[1];
							?>
					<div class="left" style="margin-left: 20px;">
						<input type="checkbox" id="<?php echo $option_id; ?>" name="<?php echo $option_id; ?>" value="1" class="left" <?php echo ($category_id !="") ? (($category->$option_id == 1) ? "checked=\"checked\"" :"") : "checked" ?>  />
						<label for="enabled" class="left" style="padding-top:0px">&nbsp;<?php echo $option_caption; ?></label> 						     			
					</div>							
							<?php
	                	}
					?>
					<div class="clear"></div>
					
					<?php
						$options = array(
							array("enable_field_publicationdate", "Date"),
							array("enable_field_author", "Author"),
							array("enable_field_tags", "Tags"),
							array("enable_field_www", "Web Link"),
							array("enable_field_heroimage", "Hero Image"),
							array("enable_field_heroimagealt", "Hero Image Alt"),
							array("enable_field_shortdescription", "Short Desc"),
							array("enable_field_content", "Content"),
							array("enable_field_view", "MVC View"),
							array("enable_field_featured", "Featured"),
							array("enable_field_prices_form", "Prices From"),
							array("enable_field_wholesale_price", "Wholesale Price"),
							array("enable_field_number_of_bedrooms", "Number Of Bedrooms"),
							array("enable_field_number_of_bathrooms", "Number Of Bathrooms"),
							array("enable_field_number_of_car", "Number Of Car Spaces"),
							array("enable_field_agent_login", "Agent Login"),
							array("enable_field_video_code", "Video Code"),
							array("enable_field_status", "Status"),
							array("enable_field_source", "Source"),
                            array("enable_field_document_attachment", "Document Attachment"),
                            array("enable_field_comments", "Comments"),
                            array("enable_field_article_icon", "Article Icon")
							);
					?>
					
					<p style="margin-top: 20px;"><b>Enabled Fields</b></p>
					
					<?php
	                	foreach($options as $option)
	                	{
	                		$option_id = $option[0];
	                		$option_caption = $option[1];
							?>
					<div class="left" style="margin-left: 20px;">
						<input type="checkbox" id="<?php echo $option_id; ?>" name="<?php echo $option_id; ?>" value="1" class="left" <?php echo ($category_id !="") ? (($category->$option_id == 1) ? "checked=\"checked\"" :"") : "checked" ?>  />
						<label for="enabled" class="left" style="padding-top:0px">&nbsp;<?php echo $option_caption; ?></label> 						     			
					</div>							
							<?php
	                	}
					?>
					<div class="clear"></div>
					
					<?php
						$options = array(
							array("image_hero_thumb", "Hero Thumbnail"),
							array("image_hero_detail", "Hero Detail"),
							array("image_hero_zoom", "Hero Zoom"),
							array("image_gallery_thumb", "Gallery Thumbnail"),
							array("image_gallery_detail", "Gallery Detail"),
							array("image_gallery_zoom", "Gallery Zoom")
							);
					?>
					
					<p style="margin-top: 20px;"><b>Image Sizes</b></p>
					<p>Note, if you set both the height and width then the image will be cropped so it is exactlty that size.  If you do not wish the image to be cropped,
					set either the height or width, and enter 0 for the value that you're not setting.
					</p>
					
					<?php
	                	foreach($options as $option)
	                	{
	                		$option_id_width = $option[0] . "_width";
	                		$option_id_height = $option[0] . "_height";
	                		
	                		$option_caption_width = $option[1] . " Width";
	                		$option_caption_height = $option[1] . " Height";
							?>
					<div class="clear" style="border-top: 1px dashed #CCCCCC; padding: 10px 0px; height: 25px;">
						<label for="enabled" class="left" style="padding-top:0px; width: 180px;">&nbsp;<?php echo $option_caption_width; ?></label> 						     			
						<input type="text" id="<?php echo $option_id_width; ?>" name="<?php echo $option_id_width; ?>" value="<?php if ($category_id !="") echo $category->$option_id_width; ?>" style="width: 50px;" class="left left-margin"  />

						<label for="enabled" class="left left-margin" style="padding-top:0px; width: 180px;">&nbsp;<?php echo $option_caption_height; ?></label> 						     			
						<input type="text" id="<?php echo $option_id_height; ?>" name="<?php echo $option_id_height; ?>" value="<?php if ($category_id !="") echo $category->$option_id_height; ?>" style="width: 50px;" class="left left-margin"  />						
					</div>						
							<?php
	                	}
					?>
					<div class="clear"></div>					
					
										
					
					
				</div><!-- end features tab -->		        
		        
				<?php /*<!-- sidebar blocks tab -->
				<div>
        			<div class="left" style="width: 300px;">
        				<label>Available Blocks</label>
        				<select id="blocks_available_right" name="blocks_available" size="10">
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
				</div><!-- end sidebar blocks tab -->
				*/?>		        
    
    			</div><!-- end panes -->
            
       		<label for="heading">&nbsp;</label> 
    			<input id="button" type="submit" value="<? echo ($category_id == "") ? "Create New Category": "Update Category"?>" />

               <input type="hidden" name="assigned_blocks_right" value="" /> 
               <input type="hidden" name="assigned_blocks_left" value="" />     			
    			<input type="hidden" name="postback" value="1" />
    			<input type="hidden" name="id" value="<?=$category_id?>" />
         </form>
         
         <div class="clear top-margin"></div>

	 		<? $this->load->view("admin/articlecategory/navigation"); ?>
