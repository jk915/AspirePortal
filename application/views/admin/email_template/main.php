<body id="emailmanager" >
       
	<?php
	    if( $this->session->flashdata( 'return_data' ) )
	    	$email_template = (object)$this->session->flashdata( 'return_data' );
	?>
   
    <div id="wrapper">
                
		<?php $this->load->view("admin/navigation");?>
		
        <div id="content">

			<?php $this->load->view( 'admin/email_template/navigation' ); ?>			
			<?php $this->load->view( 'admin/notices' ); ?>    		
		<form class="plain" id="frmBlock" name="frmBlock" action="<?php echo base_url();?>admin/emailmanager/email_template<?php echo ifvalue( $email_template_id, $email_template_id, '', '/' ); ?>"  method="post">
			<div class="message">
            	<?php 
            	// write a message
               	if( !empty( $email_template_id ) )
               		echo 'You are editing the <b>'.$email_template->email_template.'</b> email template';
               	else
               		echo 'To add a new email template, enter the details below';
               	?>
            </div>
			<h2>Email Template Properties</h2>	 
			
			<input type="hidden" id="current_template" value="<?php echo ifvalue( $email_template_id, $email_template_id, '', '' ) ?>" ></input>
					
			<label for="email_template">Email Template Name:<span class="requiredindicator">*</span></label> 
   			<input type="text" name="email_template" id="email_template" class="required" value="<?php echo ifvalue( $email_template, 'email_template', ''); ?>"/>
   			<br/>
			
			<label for="email_subject">Email Subject:<span class="requiredindicator">*</span></label> 
   			<input type="text" name="email_subject" class="required" value="<?php echo ifvalue( $email_template, 'email_subject', ''); ?>" />
   			<br/>
   			
   			<label for="from_name">From Name:<span class="requiredindicator">*</span></label> 
   			<input type="text" name="from_name" class="required" value="<?php echo ifvalue( $email_template, 'from_name', ''); ?>" />
   			<br/>
   			
   			<label for="from_email">From Email:<span class="requiredindicator">*</span></label> 
   			<input type="text" name="from_email" class="required email" value="<?php echo ifvalue( $email_template, 'from_email', ''); ?>" />
   			<br/>			
			<?php /*
			<input type="checkbox" style="margin-top: 8px;margin-right: 7px;" name="is_html" id="is_html" class="left" <?php echo ifvalue( $email_template->is_html,'checked="checked"' , '' ); ?> value="1" />
   			<label for="is_html">Is Html</label> 
   			*/ ?>   			
   			<?php if( isset( $email_template->email_template_fields ) && $email_template_id != '' && $email_template->email_template_fields != '' ){ ?>
   			<div style="float: right;margin-right: 0px;" ><?php show_tooltip('You can use this fields in the editor. This fields will be replaced when the mail is sent.<br/><br/>'.str_replace(';', '<br/> ', $email_template->email_template_fields) ) ?> </div>
			<?php } ?>
			
			<div id="email_body_div" >
                <?php $this->load->view("admin/ckeditor/ckeditor_and_history", array( "id" => "email_body", "name" => "email_body", "table" => "email_template", "content" => (ifvalue( $email_template, 'email_body', '' )), "show_preview" => FALSE )); ?>				
			</div>
			
            <div class="clear"></div>    
				
		   	<label for="heading">&nbsp;</label> 
		   	<input id="button" type="submit" value="<?php echo ($email_template_id == "") ? "Create New Email Template": "Update Email Template"?>" />
		    		
			<?php if($email_template_id != "") { ?>
			<input id="test_template" type="button"  class="button" value="Test Email Template"/>
			<?php }?>
			<br/> 
		
			<input type="hidden" name="postback" value="1" />
			<input type="hidden" name="id" value="<?php echo $email_template_id ?>" />
		</form>


		<p></p>
		<?php $this->load->view( 'admin/email_template/navigation' ); ?>
