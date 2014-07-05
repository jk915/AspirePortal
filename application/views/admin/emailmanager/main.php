<body id="emailmanager" >   
   <div id="wrapper">
	 
      <?php $this->load->view("admin/navigation");?>

      <div id="content">

      <?php $this->load->view("admin/emailmanager/navigation");?>    
      
		<form class="plain" action="#">
			<p>Below is a list of the "email templates" that are available on your site.</p>	
			
				<div id="page_listing">
					<?php $this->load->view( 'admin/emailmanager/email_template_listing', array( 'email_templates' => $email_templates ) ); ?>
				</div>
				
				<div id="controls">
				
					<div id="page_buttons" class="left" >
						<div id="pagination"></div>
					</div>
					
					<div class="right">
						<input class="button" type="button" value="Delete Email Templates" id="delete" />						
					</div>
					
				</div>	
		</form>

		<?php $this->load->view("admin/emailmanager/navigation");?>