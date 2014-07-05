<body id="contact" >   
    <div id="wrapper">
    
        <?php $this->load->view("admin/ckeditor/ckeditor_pages_articles"); ?>
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/page/navigation"); ?>            
            <p><?php echo $message?></p>
            
    <form class="plain" id="frmPage" name="frmPage" action="<?php echo base_url()?>pagemanager/page/<?php echo $page_id?>"  method="post" onsubmit="setAssignedBlocks();" enctype="multipart/form-data">



    <h2>Page Properties</h2>    
    
    <?php
        if(isset($page))
        {
               // We're editing and existing page.  Show the tabs.
    ?>
            
    <!-- tabs -->
    <ul class="css-tabs skin2">
        <li><a href="#">Page Details</a></li>
        <li><a href="#">Google/SEO</a></li>
        <li><a href="#">HTML Content</a></li>
        <li><a href="#">Sidebar Blocks</a></li>
        <?php //<li><a href="#">Right Sidebar Blocks</a></li> ?>
    </ul>   
    
    <!-- panes -->
    <div class="css-panes skin2">
        <div style="display:block">
                        
    <?php
        }
    ?>    
            <div class="left" style="width: 50%"><!-- First tab -->
            
                <label for="page_title">Page Name:<span class="requiredindicator">*</span></label> 
                <input type="text" id="page_title" name="page_title" class="required" value="<?php echo ($page_id !="") ? $page->page_title : "" ?>" /><div class="clear"></div>
                
                <label for="page_code">Page Code:<span class="requiredindicator">*</span></label> 
                <input type="text" name="page_code" id="page_code" class="required" value="<?php echo ($page_id !="") ? $page->page_code : "" ?>"/>
                                                
				<label for="view">MVC View (leave blank unless you're a coder):</label>
                <input type="text" name="view" value="<?php  echo ($page_id !="") ? $page->view : "" ?>"/>


                <?php /*if((isset($page)) && (($page->page_code == 'home') || ($page->page_code == 'sign-up') || ($page->page_code == 'splash'))) : ?>
                        <label for="background_img">Background</label>
                        <input type="text" id="background" name="background" class="left" value="<? echo ($page_id !="") ? $page->background : "" ?>" />
                        <input type="button" class="select-file left-margin" onclick="selectFile('background');" value="Select" />
                        <div class="clear"></div>


                        <label for="text_color">Nav Text Color</label>

                        <?php /*$colors = get_colors();
                        <select id="text_color" name="text_color">
                            <?php foreach ($colors as $key=>$value)
                                  {
                            ?>
                                    <option value="<?php echo $key; ?>" <?php if($page_id !="") {  if($page->text_color == $key)  {echo 'selected="selected"';} else echo ""; } else echo ""; ?> ><?php echo $value; ?></option>

                            <?php } ?>
                        </select>
                        
                        <?php //echo form_dropdown('text_color', array(1=>'White',2=>'Black',3=>'Red',4=>'Green'), ($page_id !="") ? $page->text_color : "") ?>
                <?php endif ?>
                
                <?php if($page_id !='' &&  $page->page_code == 'splash' ) : ?>
                        <label for="flash_movie">Flash Movie</label>
                        <input type="text" id="flash_movie" name="flash_movie" class="left" value="<? echo ($page_id !="") ? $page->flash_movie : "" ?>" />
                        <input type="button" class="select-file left-margin" onclick="selectFile('flash_movie');" value="Select" />
                        <div class="clear"></div>                
                <?php endif ?>
                
                <label for="image1">Hero Image</label>
                <input type="text" id="image1" name="image1" class="left" value="<? echo ($page_id !="") ? $page->image1 : "" ?>" />
                <input type="button" class="select-file left-margin" onclick="selectFile('image1');" value="Select" />
                <div class="clear"></div>
                
                <label for="image1_caption">Hero Image Alt Text</label>
                <input type="text" name="image1_caption" value="<?php  echo ($page_id !="") ? $page->image1_caption : "" ?>"/>                                 
               
                <br/>
                <br/>
                  
                <input type="checkbox" name="edit_page" value="1" class="left" <? echo ($page_id !="") ? (($page->edit_page == 1) ? "checked" :"") : "checked" ?>  /><label for="edit_page" class="left" style="padding-top:0px">&nbsp;Local contributors can edit this page</label>
                <br/>*/
                ?>
                <div class="clear"></div>
                <br />
                
                <input type="checkbox" name="enabled" value="1" class="left" <? echo ($page_id !="") ? (($page->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Page is active</label>
                <div class="clear"></div>
                <br/>
            </div>
            <?php /*<div class="left">
                <label for="website_pages">This page will be shown for:</label>
                <?php 
                if(isset($websites) && $websites)
                {
                    ?>                  
                    <select multiple="multiple" id="website_pages" name="website_pages[]">
                        <?php echo $this->utilities->print_select_options($websites, "website_id", "website_name", ($page_id != "") ?  $website_pages : ""); ?>
                    </select>
                    <?php
                }
                ?>
            </div>
            */?>
            <div class="clear"></div>
            
        <?php if(isset($page)) : ?>   
        </div><!-- END first tab -->    
        
        <div>
            
            <label for="meta_title">Browser Title / Bookmark Name:</label> 
               <input type="text" name="meta_title" value="<?php echo ($page_id !="") ? $page->meta_title : "" ?>"/>
               <br/>
            
            <label for="meta_keywords">Keywords:</label> 
               <input type="text" name="meta_keywords" value="<?php echo ($page_id !="") ? $page->meta_keywords : "" ?>"/>
               <br/>
            
            <label for="meta_description">Description / Call to action:</label> 
               <input type="text" name="meta_description" value="<?php echo ($page_id !="") ? $page->meta_description : "" ?>"/>
               <br/>
            
                  <label for="meta_robots">Search Robots Permissions:</label>
               <select name="meta_robots">
                <?php print $this->utilities->print_select_options_array($robots,false,($page_id !="") ? $page->meta_robots : ""); ?>                
            </select>
            
        </div><!-- END second tab -->
        
        <div>
        
        <?php 
            $this->load->view("admin/ckeditor/ckeditor_and_history", array( "id" => "wysiwyg", "name" => "page_body", "table" => "pages", "content" => ($page_id !="") ? $page->page_body : "", "foreign_id" => $page_id));
        ?>
        </div><!-- END third tab -->
        
		<!-- sidebar blocks tab -->
		<div>
        	<div class="left" style="width: 300px;">
        		<label>Available Blocks</label>
        		<select id="blocks_available_left" name="blocks_available" size="10">
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
		
        <?php /*<!-- right sidebar blocks tab -->
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
		</div><!-- end right sidebar blocks tab -->
    	*/?>
        
    </div>    
    <?php endif; ?>
    <br/>
    <br/>
              
    <label for="heading">&nbsp;</label> 
    <input id="button" type="submit" value="<? echo ($page_id == "") ? "Create New Page": "Update Page"?>" /><br/>                

    <input type="hidden" name="assigned_blocks_right" value="" /> 
    <input type="hidden" name="assigned_blocks_left" value="" /> 
    <input type="hidden" name="postback" value="1" />
    <input type="hidden" name="id" value="<?=$page_id?>" />
</form>


<p></p>
<? $this->load->view("admin/page/navigation"); ?>
