	<div id="divCMSHeader">
		<a href="<?php echo base_url(); ?>admin/menu">
			<img class="left" id="client_logo" src="<?php echo base_url(); ?>images/admin/client_logo.png" border="0" height="80" alt="Client logo" />
		</a>
		<div class="right">
			<!--<img src="<?php echo base_url(); ?>images/admin/myndiecms.png" border="0" height="35" alt="AW Design" style="float: right;" />-->
			<div class="clear"></div>
			<div style="width: 215px; text-align: right; padding: 0px 0"><a href="<?=base_url();?>admin/logout">Log out</a></div>
		</div>
	</div>	
	<div id="nav">
		<div id="divNavHeading"> 
			<h1><?php if(isset($page_heading)) echo $page_heading; ?></h1>
		</div>
		<div id="cssmenu">
		<ul id="favourites">
	
		<?php foreach($modules->result() as $module) : ?>
		
			<?php if($module->favourite) : ?>

				<li class="has-sub"><a href="<?php echo base_url() . "admin/" . $module->controller; ?>"><?php echo $module->short_name; ?></a>
				<?php if($module->short_name == "Users") : ?> 
				
					<ul>
						<li><a href='<?php echo base_url(); ?>admin/contactsmanager'><span>Contacts</span></a></li>
					</ul>
				<?php endif; ?>
				<?php if($module->short_name == "Areas") : ?> 
					<ul>
						<li><a href='<?php echo base_url(); ?>admin/statemanager'><span>States</span></a></li>
						<li><a href='<?php echo base_url(); ?>admin/regionmanager'><span>Regions</span></a></li>
						<li><a href='<?php echo base_url(); ?>admin/australia'><span>Australia</span></a></li>
					</ul>
				<?php endif; ?>
				<?php if($module->short_name == "Stocklist") : ?> 
					<ul>
						<li><a href='<?php echo base_url(); ?>admin/buildermanager'><span>Builders</span></a></li>
					</ul>
				<?php endif; ?>
				</li>
			<?php endif; ?>
		<?php endforeach; ?>
			<li><a href="<?php echo base_url() . "admin/logout"; ?>">Logout</a></li>
		</ul>
		</div>
		<div class="clear"></div>
	<!-- end nav div--></div>
	<div class="clear top-margin"></div>  