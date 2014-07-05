<body id="contact" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/ckeditor/ckeditor_pages_articles"); ?>
        
		<?php $this->load->view("admin/navigation");?>
		
        <div id="content">

			<?php $this->load->view("admin/block/navigation"); ?>			
			<p><?php print $message?></p>	
		
<form class="plain" id="frmBlock" name="frmBlock" action="<?php echo base_url()?>admin/blockmanager/block/<?=$block_id?>"  method="post">
	<h2>Block Properties</h2>	
	
			<label for="block_name">Block Name:<span class="requiredindicator">*</span></label> 
   			<input type="text" name="block_name" id="block_name" class="required" value="<?php echo ($block_id !="") ? $block->block_name : "" ?>"/>
   			<br/>
			
			<label for="block_description">Block Description:<span class="requiredindicator">*</span></label> 
   			<input type="text" name="block_description" class="required" value="<?php echo ($block_id !="") ? $block->block_description : "" ?>" />
   			<br/>   	
			<br/>			
			
            <?php echo $this->load->view("admin/ckeditor/ckeditor_and_history", array( "id" => "wysiwyg", "name" => "block_content", "table" => "custom_blocks", "content" => ($block_id !="") ? $block->block_content : "", "foreign_id" => isset($block) ? $block->block_id : "" )); ?>
			
			<br class="clear"/>
			<br/>
			
            <div class="left">
                <input type="checkbox" name="enabled" value="1" class="left" <?php echo ($block_id !="") ? (($block->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Block is active</label> 
            </div>
            
            <div class="left left-margin20">
                <input type="checkbox" name="show_on_sidebar" value="1" class="left" <?php echo ($block_id !="") ? (($block->show_on_sidebar == 1) ? "checked" :"") : "checked" ?>  /><label for="show_on_sidebar" class="left" style="padding-top:0px">&nbsp;Block can be shown on sidebar</label> 
            </div>
            
            <?php /*<div class="left left-margin20">
                <input type="checkbox" name="hide_heading" value="1" class="left" <?php echo ($block_id !="") ? (($block->hide_heading == 1) ? "checked" :"") : "checked" ?>  /><label for="hide_heading" class="left" style="padding-top:0px">&nbsp;Hide heading</label> 
            </div>            
            */?>
            <div class="clear"></div>    
			
			<br/>
   			
		
	<br/>
	<br/>
	   	   
   	<label for="heading">&nbsp;</label> 
    <input id="button" type="submit" value="<?php echo ($block_id == "") ? "Create New Block": "Update Block"?>" /><br/>    	    	

	<input type="hidden" name="postback" value="1" />
	<input type="hidden" name="id" value="<?php print $block_id?>" />
</form>


<p></p>
<?php $this->load->view("admin/block/navigation"); ?>
