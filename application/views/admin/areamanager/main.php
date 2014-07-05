<body id="pagemanager" >   
   <div id="wrapper">
     
      <? $this->load->view("admin/navigation");?>

      <div id="content">

      <? $this->load->view("admin/areamanager/navigation",array("side"=>"top"));?>    

<form class="plain">


		<!-- By Mayur - TasksEveryday -->
			
			<div class="boxsearch">
                    <?php
                        $profilters = $this->session->userdata("profilters");
                    ?>
                    <select id="state_id" name="state_id" style="width:150px;">
                        <option value="" selected="selected">View All States</option>
                    <? if($states):?>
                        <?=$this->utilities->print_select_options($states,"state_id","name",$profilters ? $profilters['state_id'] : ''); ?>
                    <? endif; ?>
                    </select>                  
                    
                    <input type="text" id="area_search" value="<?php echo $profilters ? $profilters['keysearch'] : '';?>" style="width:140px; padding:4px;" />
                
                    <select id="project_id" name="project_id" style="width:150px;">
                        <option value="" selected="selected">- Select Project</option>
                    <? if($projects):?>
                        <?=$this->utilities->print_select_options($projects,"project_id","project_name",$profilters ? $profilters['project_id'] : ''); ?>
                    <? endif; ?>
                    </select>
                    
            </div><br />

			<!-- By Mayur - TasksEveryday -->


		<div id="page_listing">
            <? $this->load->view('admin/areamanager/area_listing.php',array('areas'=>$areas)); ?>
        </div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Delete Areas" id="delete" />
            </div>
            
        </div>    
</form>

<? $this->load->view("admin/areamanager/navigation",array("side"=>"bottom"));?>    
