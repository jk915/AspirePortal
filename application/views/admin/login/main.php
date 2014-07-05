<body id="adminlogin">   
	<div id="wrapper">

		<div id="content">
			<h1><?php echo WEBSITE_NAME; ?> - Secure Login</h1>

			<? if($message!="") : ?> 
			<p class="intro" >
				<span class="warning"><?=$message?></span>
			</p>
			<? endif; ?>	


			<form id="login" name="login" method="post" class="left" action="<?php echo base_url(); ?>admin/login">				
				<fieldset>
					<legend>Please enter your login credentials</legend>

					<label for="username">Username: <span class="requiredindicator">*</span></label>
					<input type="text" tabindex="1" name="username" value="" class="required" size="150"/>

					<label for="password">Password:<span class="requiredindicator">*</span></label>
					<input type="password" tabindex="2" name="password" value="" class="required" size="150"/>

					<div class="space"></div>
					<input type="submit" value="Login &gt;&gt;" class="formsubmit" />
				</fieldset>
			</form>

			<div class="right" style="background-color: #FFFFFF; padding: 50px 10px 10px">
				<img src="<?php echo base_url(); ?>images/admin/client_logo.png" border="0" width="400" alt="Aspire Network Logo" />
			</div>

			<div class="clear"></div>

