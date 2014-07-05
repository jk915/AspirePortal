<body id="pagemanager" >   
   <div id="wrapper">
     
      <? $this->load->view("admin/navigation");?>

      <div id="content">

      <? $this->load->view("admin/regionmanager/navigation",array("side"=>"top"));?>    

<form class="plain">


		<!-- By Mayur - TasksEveryday -->
			
			<div class="boxsearch">
                    <?php
                        $profilters = $this->session->userdata("profilters");
                    ?>
                    <select id="region_id_search" name="region_id_search" style="width:150px;">
                        <option value="" selected="selected">View All States</option>
                    <? //if($states):?>
                        <?//=$this->utilities->print_select_options($states,"state_id","name",$profilters ? $profilters['state_id'] : ''); ?>
                    <? //endif; ?>
                    </select>                  
                    
                    <input type="text" id="property_search" value="<?php echo $profilters ? $profilters['keysearch'] : '';?>" style="width:140px; padding:4px;" />
                
                    <select id="state_id_search" name="state_id_search" style="width:150px;">
                        <option value="" selected="selected">- Select State</option>
                   <? if($states):?>
                        <?=$this->utilities->print_select_options($states,"state_id","name",$profilters ? $profilters['state_id'] : ''); ?>
                    <? endif; ?>
                    </select>
                    
            </div><br />

			<!-- By Mayur - TasksEveryday -->


		<div id="page_listing">
            <? $this->load->view('admin/regionmanager/region_listing.php',array('regions'=>$regions)); ?>
        </div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Delete Regions" id="delete" />
            </div>
            
        </div>    
</form>

<? $this->load->view("admin/regionmanager/navigation",array("side"=>"bottom"));?>    
