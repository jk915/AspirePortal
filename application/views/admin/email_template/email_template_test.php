<div class="w_caption" style="background-color: #CCC;height:23px">
	<a href="javascript:void(0);" class="w_close"><!-- --></a>
	<span class="w_captionText" id="_wicket_window_12">
		<b>Test Email Template</b>
	</span>
</div>

<form class="plain" id="frmSendTestMail" name="frmSendTestMail" action="<?php echo base_url().'admin/emailmanager/send_test_mail'  ?>" method="post">
	<div style="text-align: left;padding-left: 50px; padding-right: 50px;">
	    
	    <input type="hidden" name="email_template" value="<?php echo $email_settings->email_template ?>" />
	    <input type="hidden" name="current_template" value="<?php echo $email_settings->id ?>" />
	    
	    <?php 
	    $email_fields = array();
	    if( trim($email_settings->email_template_fields) != '' )
	    	$email_fields = explode(';', trim($email_settings->email_template_fields));
	    	
	    foreach( $email_fields as $field  )
	    {
	    	$cfield = str_replace('{', '', $field);	
	    	$cfield = str_replace('}', '', $cfield);	
	    ?>
	    	<label><?php echo $cfield; ?>:</label>
	    	<input type="text" name="<?php echo $cfield ?>" />
	    	<br/>
	    <?php
	    } 
	    ?>
	   
	   	<label>Send test email to:*</label>
	   	<input type="text" name="to_email_address" class="required email" value="" />
	   	<br/>
	   	<br/>
	    
	    <input id="send_test_mail" type="button" class="button" value="Send Test Mail" />
	    
	</div>
</form>

