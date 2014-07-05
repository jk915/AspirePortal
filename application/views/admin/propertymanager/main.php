<body id="pagemanager" >   
   <div id="wrapper">
	 
		<? $this->load->view("admin/navigation");?>

        <div id="content">

        <? $this->load->view("admin/propertymanager/navigation",array("side"=>"top"));?>	
<div id="columns" class="reveal-modal">
    <form method="post" action="<?php echo site_url('admin/propertymanager/generate')?>" id="generateForm" style="background:none;">
        <input type="hidden" name="type" value="csv" id="gentype" />
    <?php
        $aColumns = array(
            'lot' => 'Lot',
            'address' => 'Address',
            'area' => 'Area',
            'state' => 'State',
            'estate' => 'Estate',
            'price' => 'Price',
            'type' => 'Type',
            'size' => 'Size',
            'land' => 'Land',
            'yield' => 'Yield',
            'nras' => 'NRAS',
            'smsf' => 'SMSF',
			'date_added' => 'Date Added',
			'status' => 'Status',
			'builder' => 'Builder',
			'advisor_full_name' => 'Advisor',
			'partner_full_name' => 'Partner',
			'purchaser_full_name' => 'Enquiry / Purchaser',
			'ts_reserved_date' => 'Date reserved'
        );
    ?>
        <p>Select the column(s) that you would like to appear in the generated document.</p>
        <ul class="columns">
        <?php foreach ($aColumns as $key=>$value) : ?>
            <li style="background:none;"><input type="checkbox" name="columns[]" checked="checked" value="<?php echo $key?>" /> <?php echo $value?></li>
        <?php endforeach; ?>
        </ul>
        <div style="clear:both;"></div>
        
        <input type="button" value="Generate CSV" class="btn" id="generate_csv"/>
    </form>
</div>
<form method="post" action="<?php echo site_url('admin/propertymanager/generate')?>" id="tempForm" style="display:none;"></form>

		<form class="plain">
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
                
                    <select id="status_search" name="status_search" style="width:100px;">
                        <option value="" selected="selected">- Status</option>
                        <option value="inprogress">In Progress</option>
                    <?php foreach ($status as $key) : ?>
                        <option value="<?php echo strtolower($key)?>" <?php echo ($profilters && $profilters['status'] == strtolower($key)) ? 'selected="selected"' : ''?>><?php echo $key?></option>
                    <?php endforeach; ?>
                    </select>
                
                    <select id="area_id_search" name="area_id_search" style="width:150px;">
                        <option value="" selected="selected">- Select Area</option>
                    <? if($areas):?>
                        <?=$this->utilities->print_select_options($areas,"area_id","area_name",$profilters ? $profilters['area_id'] : ''); ?>
                    <? endif; ?>
                    </select>
                    
                    <select id="builder_id_search" name="builder_id_search" style="width:150px;">
                        <option value="" selected="selected">- Select Builder</option>
                    <? if($builders):?>
                        <?=$this->utilities->print_select_options($builders,"builder_id","builder_name",$profilters ? $profilters['builder_id'] : ''); ?>
                    <? endif; ?>
                    </select>
                    
                    <label style="width:150px; float:right; padding-top:5px;">
                        <input type="checkbox" value="1" name="archived_search" id="archived_search" <?php echo ($profilters && $profilters['archived'] == 1) ? 'checked="checked"' : ''?>/> 
                        <span>Show Archived</span>
                    </label>
                    
                </div><br />
				<div id="page_listing">
					<? $this->load->view('admin/propertymanager/property_listing.php',array('properties'=>$properties)); ?>
				</div>
				
				<div id="controls">
				
					<div id="page_buttons" class="left" >
						<div id="pagination"></div>
					</div>
					
					<div class="right">	
                        
                        <select name="functions_list" id="functions_list">
                            <option value="">Select a function to apply</option>
                            <option value="archive">Set Archived</option>
                            <option value="unarchive">Set Not Archived</option>
                            <option value="delete">Delete</option>
                            <option value="feature">Set Featured</option>
                            <option value="unfeature">Set Not Featured</option>
                        </select>
                        <input class="button" type="button" value="Apply" id="apply" />
					</div>
					
				</div>	
		</form>

<? $this->load->view("admin/propertymanager/navigation",array("side"=>"bottom"));?>	
