<body id="contact" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/ckeditor/ckeditor_pages_articles"); ?>
        
		<?php $this->load->view("admin/navigation");?>
		
        <div id="content">

			<?php $this->load->view("admin/accesslevel/navigation"); ?>			
			<p><?php print $message?></p>	
		
<form class="plain" id="frmAccessLevel" name="frmAccessLevel" action="<?php echo base_url()?>accesslevelmanager/accesslevel/<?php echo $accesslevel_id?>"  method="post">
	<h2>Access Level Properties</h2>	
	
			<label for="level">Access Level Name:<span class="requiredindicator">*</span></label> 
   			<input type="text" name="level" id="level" class="required" value="<?php echo ($accesslevel_id !="") ? $accesslevel->level : "" ?>"/>
   			<br/>
			
			<label for="description">Access Level Description:</label> 
   			<input type="text" name="description" value="<?php echo ($accesslevel_id !="") ? $accesslevel->description : "" ?>" />
   			<br/>   	
			<br/>
			
            <div class="left">
                <input type="checkbox" name="is_enabled" value="1" class="left" <?php echo ($accesslevel_id !="") ? (($accesslevel->is_enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="is_enabled" class="left" style="padding-top:0px">&nbsp;Access Level is active</label> 
            </div>
            
            <div class="clear"></div>    
			
			<br/>
	
	   	   
   	<label for="heading">&nbsp;</label> 
    <input id="button" type="submit" value="<?php echo ($accesslevel_id == "") ? "Create New Access Level": "Update Access Level"?>" /><br/>    	    	

	<input type="hidden" name="postback" value="1" />
	<input type="hidden" name="id" value="<?php print $accesslevel_id?>" />
</form>


<p></p>
<?php $this->load->view("admin/accesslevel/navigation"); ?>