<body id="pagemanager" >   
   <div id="wrapper">
     
      <? $this->load->view("admin/navigation");?>

      <div id="content">

      <? $this->load->view("admin/projectmanager/navigation",array("side"=>"top"));?>    

		<form class="plain"> 

			<!-- By Mayur - TasksEveryday -->
			
			<div class="boxsearch">
                    <?php
                        $profilters = $this->session->userdata("profilters");
                    ?>
                    <select id="state_id_search" name="state_id_search" style="width:150px;">
                        <option value="" selected="selected">View All States</option>
                    <? if($states):?>
                        <?=$this->utilities->print_select_options($states,"state_id","name",$profilters ? $profilters['state_id'] : ''); ?>
                    <? endif; ?>
                    </select>                  
                    
                    <input type="text" id="property_search" value="<?php echo $profilters ? $profilters['keysearch'] : '';?>" style="width:140px; padding:4px;" />
                
                    <select id="area_id_search" name="area_id_search" style="width:150px;">
                        <option value="" selected="selected">- Select Area</option>
                    <? if($areas):?>
                        <?=$this->utilities->print_select_options($areas,"area_id","area_name",$profilters ? $profilters['area_id'] : ''); ?>
                    <? endif; ?>
                    </select>
                    
            </div><br />

			<!-- By Mayur - TasksEveryday -->
		
			<div id="page_listing">
				<? $this->load->view('admin/projectmanager/project_listing.php',array('projects'=>$projects)); ?>
			</div>

			<div id="controls">

				<div id="page_buttons" class="left" >
					<div id="pagination"></div>
				</div>

				<div class="right">
					<select name="functions_list" id="functions_list">
						<option value="">Select a function to apply</option>
						<option value="delete">Delete</option>
						<option value="website">Set On Website</option>
						<option value="unwebsite">Set Not On Website</option>
						<option value="newsletter">Set On Newsletter</option>
						<option value="unnewsletter">Set Not On Newsletter</option>
						<option value="feature">Set Featured</option>
						<option value="unfeature">Set Not Featured</option>
					</select>
					<input class="button" type="button" value="Apply" id="apply" />
				</div>

			</div>
		</form>

<? $this->load->view("admin/projectmanager/navigation",array("side"=>"bottom"));?>    
