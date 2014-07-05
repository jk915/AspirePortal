<body id="menu" >
	<div id="wrapper">
		<? $this->load->view("admin/navigation");?>

		<div id="content">
			<p><b>Welcome to your administration console <?php echo $name; ?>.</b></p>

			<div id="dynatip">&nbsp;</div>

			<ul class="menu_icon">
			<?php
	        	if($modules)
	        	{
					foreach($modules->result() as $module)
					{
						?>
				<li>
					<a class="<?php echo $module->css_icon_class; ?> toolTip" title="<?php echo $module->description; ?>" href="<?php print base_url();?>admin/<?php echo $module->controller; ?>"></a>
					<label><?php echo $module->module_name; ?></label>
				</li> 						
						<?php
					}
	        	}
			?>

				<li>
					<a class="logout toolTip" title="Logout" href="<?=base_url();?>logout"></a>
					<label>Log out</label>
				</li>
			</ul>                   

			<div class="clear"></div>