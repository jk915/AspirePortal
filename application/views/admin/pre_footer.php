		<!-- Include Page Footer --> 
	
		<div class="clear space"></div>                   
	<!-- end content div --></div>

	<?php if ($this->login_model->is_logged_in("cms_user")): ?>
	<div id="footer">
		<p id="viewMainSite">
			<a href="<?php echo base_url(); ?>admin/menu">Main Menu</a> |
			<a href="<?php echo site_url(); ?>">View My Website</a>
		</p>      		
		
		<form id="frmJump" name="frmJump" action="#" method="post">
			<select id="jump_to">
				<option value="">Jump To</option>
				<?php
                // Note, the modules resultset is loaded in by the check_user hook automatically
                // when each admin controller loads
				foreach($modules->result() as $module)
				{
					?>
				<option value="<?php echo $module->controller; ?>" <?php select_drop_down($module->controller);?>><?php echo $module->module_name; ?></option>									
					<?php	
				}
				?>                        
			</select>
		</form>
		<?php endif; ?>
		
		<div class="clear"></div>
	<!-- end footer div --></div>
 </div>                   
