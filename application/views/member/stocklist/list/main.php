<?php
    $utid = $this->session->userdata["user_type_id"];
?>
<body class="stocklist">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
            
                <div id="curtain">
                    <img src="<?php echo base_url(); ?>/images/member/bigloader.gif" width="64" height="64" alt="Loading" />
                </div>            
            
                <div class="content inner">

                    <ul class="breadcrumbs"> 
                        <?php if($utid == USER_TYPE_SUPPLIER) : ?>
                        <li>Stocklist</li>
                        <?php else: ?>
                        <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                        <li>Properties</li>
                        <?php endif; ?>
                    </ul>                
                    
                    <div class="sidebar">
                        <?php echo form_open('stocklist/ajax', array("id" => "frmSearch", "name" => "frmSearch")); ?>
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo STOCKLIST_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_stocklist" />
                            <input type="hidden" id="sort_col" name="sort_col" value="" />
                            <input type="hidden" id="sort_dir" name="sort_dir" value="" />
                            <input type="hidden" id="list_type" name="list_type" value="grid" />
                            
                            <!-- Slider Fields -->
                            <input type="hidden" id="min_total_price" name="min_total_price" value="<?=$property_data->min_total_price;?>" />
                            <input type="hidden" id="max_total_price" name="max_total_price" value="<?=$property_data->max_total_price;?>" />
                            <input type="hidden" id="min_bathrooms" name="min_bathrooms" value="<?=$property_data->min_bathrooms;?>" />
                            <input type="hidden" id="max_bathrooms" name="max_bathrooms" value="<?=$property_data->max_bathrooms;?>" />                            
                            <input type="hidden" id="min_bedrooms" name="min_bedrooms" value="<?=$property_data->min_bedrooms;?>" />
                            <input type="hidden" id="max_bedrooms" name="max_bedrooms" value="<?=$property_data->max_bedrooms;?>" />                                                        
                            <input type="hidden" id="min_garage" name="min_garage" value="<?=$property_data->min_garage;?>" />
                            <input type="hidden" id="max_garage" name="max_garage" value="<?=$property_data->max_garage;?>" />                                                                                    
                            <input type="hidden" id="min_land" name="min_land" value="<?=floor($property_data->min_land);?>" />
                            <input type="hidden" id="max_land" name="max_land" value="<?=floor($property_data->max_land) + 1;?>" /> 
                            <input type="hidden" id="min_house" name="min_house" value="<?=floor($property_data->min_house);?>" />
                            <input type="hidden" id="max_house" name="max_house" value="<?=floor($property_data->max_house) + 1;?>" /> 
                            <input type="hidden" id="min_yield" name="min_yield" value="<?=number_format($property_data->min_yield, 1);?>" />
                            <input type="hidden" id="max_yield" name="max_yield" value="<?=number_format($property_data->max_yield, 1);?>" />        
                            <input type="hidden" id="status" name="status" value="available" />                                                                           
                                                    
                            <fieldset>
                                <ul class="options"> 
                                	<li>
                                        <h3>Price</h3>
                                        <p>From $<span class="lowerVal"><?=$property_data->min_total_price; ?></span> to $<span class="upperVal"><?=$property_data->max_total_price; ?></span></p>
                                        <div id="sliderPrice" class="noUiSlider"></div>
                                    </li>
                                    <li>
                                        <h3>Rent Yield</h3>
                                        <p>From <span class="lowerVal"><?=number_format($property_data->min_yield, 1); ?></span> to <span class="upperVal"><?=number_format($property_data->max_yield, 1); ?></span>%</p>
                                        <div id="sliderYield" class="noUiSlider"></div> 
                                    </li>
                                    <li>
                                        <h3>Bedrooms</h3>
                                        <p>From <span class="lowerVal"><?=$property_data->min_bedrooms; ?></span> to <span class="upperVal"><?=$property_data->max_bedrooms; ?></span></p>
                                        <div id="sliderBeds" class="noUiSlider"></div> 
                                    </li>   
                                    <li>
                                        <h3>Bathrooms</h3>
                                        <p>From <span class="lowerVal"><?=$property_data->min_bathrooms; ?></span> to <span class="upperVal"><?=$property_data->max_bathrooms; ?></span></p>
                                        <div id="sliderBaths" class="noUiSlider"></div> 
                                    </li>
                                    <li>
                                        <h3>Land Area</h3>
                                        <p>From <span class="lowerVal"><?=floor($property_data->min_land); ?></span> to <span class="upperVal"><?=floor($property_data->max_land); ?></span>sqm</p>                                    
                                        <div id="sliderLandArea" class="noUiSlider"></div>
                                    </li>
                                    <li>
                                        <h3>House Area</h3>
                                        <p>From <span class="lowerVal"><?=floor($property_data->min_house); ?></span> to <span class="upperVal"><?=floor($property_data->max_house) + 1; ?></span>sqm</p>                                    
                                        <div id="sliderHouseArea" class="noUiSlider"></div>
                                    </li> 
                                    <li>
                                        <h3>Featured</h3>
                                        <input <?php if($mode == "featured") : ?>checked="checked"<?php endif; ?> type="radio" name="featured" id="featured_yes" value="1" /> 
                                        <label class="cblabel" for="featured_yes">Yes</label>   
                                        <input type="radio" name="featured" id="featured_no" value="0" /> 
                                        <label class="cblabel" for="featured_no">No</label> 
                                        <input <?php if($mode != "featured") : ?>checked="checked"<?php endif; ?> type="radio" name="featured" id="featured_all" value="" /> 
                                        <label class="cblabel" for="featured_all">All</label> 
                                    </li> 
                                    <li>
                                        <h3>New</h3>
                                        <input <?php if($mode == "new") : ?>checked="checked"<?php endif; ?> type="radio" name="new" id="new_yes" value="1" /> 
                                        <label class="cblabel" for="new_yes">Yes</label>   
                                        <input type="radio" name="new" id="new_no" value="0" /> 
                                        <label class="cblabel" for="new_no">No</label> 
                                        <input <?php if($mode != "new") : ?>checked="checked"<?php endif; ?> type="radio" name="new" id="new_all" value="" /> 
                                        <label class="cblabel" for="new_all">All</label> 
                                    </li>                                                                           
                                    <li>
                                        <h3>NRAS</h3>
                                        <input type="radio" name="nras" id="nras_yes" value="1" /> 
                                        <label class="cblabel" for="nras_yes">Yes</label>   
                                        <input type="radio" name="nras" id="nras_no" value="0" /> 
                                        <label class="cblabel" for="nras_no">No</label> 
                                        <input checked="checked" type="radio" name="nras" id="nras_all" value="" /> 
                                        <label class="cblabel" for="both">All</label> 
                                    </li>
                                    <li>
                                        <h3>SMSF</h3>
                                        <input type="radio" name="smsf" id="smsf_yes" value="1" /> 
                                        <label class="cblabel" for="smsf_yes">Yes</label>   
                                        <input type="radio" name="smsf" id="smsf_no" value="0" /> 
                                        <label class="cblabel" for="smsf_no">No</label> 
                                        <input checked="checked" type="radio" name="smsf" id="smsf_all" value="" /> 
                                        <label class="cblabel" for="both">All</label> 
                                    </li>
									
									<li>
                                        <h3>TITLED</h3>
                                        <input type="radio" name="titled" id="titled_yes" value="1" /> 
                                        <label class="cblabel" for="titled_yes">Yes</label>   
                                        <input type="radio" name="titled" id="titled_no" value="0" /> 
                                        <label class="cblabel" for="titled_no">No</label> 
                                        <input checked="checked" type="radio" name="titled" id="titled_all" value="" /> 
                                        <label class="cblabel" for="both">All</label> 
                                    </li>
									
                                    <li>
                                        <h3>Lot / Address</h3>
                                        <input type="text" id="keysearch" name="keysearch" value="" />
                                    </li>
                                    <li>
                                        <h3>Project / Estate</h3>
                                        <select id="project_id" name="project_id">
                                            <option value="">View All</option>
                                            <?php echo $this->utilities->print_select_options($projects, "project_id", "project_name"); ?>
                                        </select>
                                    </li>
                                    <li>
                                        <h3>Area</h3>
                                        <select id="area_id" name="area_id">
                                            <option value="">View All</option>
                                            <?php echo $this->utilities->print_select_options($areas, "area_id", "area_name"); ?>
                                        </select>
                                    </li> 
                                    <li>
                                        <h3>State</h3>
                                        <select id="state_id" name="state_id">
                                            <option value="">View All</option>
                                            <?php echo $this->utilities->print_select_options($states, "state_id", "name"); ?>
                                        </select>
                                    </li> 
                                    <li>
                                        <h3>Property Type</h3>
                                        <select id="property_type_id" name="property_type_id">
                                            <option value="">View All</option>
                                            <?php echo $this->utilities->print_select_options($property_types, "value", "name"); ?>
                                        </select>
                                    </li>
                                    <li>
                                        <h3>Contract Type</h3>
                                        <select id="contract_type_id" name="contract_type_id">
                                            <option value="">View All</option>
                                            <?php echo $this->utilities->print_select_options($contract_types, "value", "name"); ?>
                                        </select>
                                    </li>  
                                    <?php /*
                                    <li>
                                        <h3>Status</h3>
                                        <select id="status" name="status">
                                            <?php echo $this->utilities->print_select_options_array($status_options, false); ?>
                                            <option value="">View All</option>
                                        </select>
                                    </li> */ ?>
                                </ul>
                                                      
                            </fieldset>
                        </form>               
                    <!-- end sidebar --></div>                
                    
                    <div class="mainCol">
                    <?php if ($user_type_id==USER_TYPE_ADVISOR) : ?>
                        <div class="utils">
                            <a href="javascript:;" rel="pdf" class="pdf revealcolumns"></a>
                            <a href="javascript:;" rel="csv" class="csv revealcolumns"></a>
                        </div>
                    <?php endif; ?>
                        <h1>Properties</h1>
                        <ul id="tabNav">
                            <li><a class="active" href="#" list_type="grid"><img src="<?php echo base_url(); ?>images/member/stocklist-view-grid.png" width="20" height="20" alt="View properties in a grid." /></a></li>
                            <li><a href="#" list_type="map"><img src="<?php echo base_url(); ?>images/member/stocklist-view-map.png" width="20" height="20" alt="View properties on a map." /></a></li>
                            <li><a href="#" list_type="list"><img src="<?php echo base_url(); ?>images/member/stocklist-view-table.png" width="20" height="20" alt="View properties as a list." /></a></li>
                        </ul>
                        <ul id="tabs">
                            <li>
                                <ul class="propertyListing">                  
                                </ul>
                            </li>
                            <li>
                            	<ul id="risk_comment">
                                    <!--
									
                            		<li><img src="images/member/map_point_green.png"/> Low</li>
                            		<li><img src="images/member/map_point_yellow.png"/> Medium</li>
                            		<li><img src="images/member/map_point.png"/> High</li>
                            		<li><img src="images/member/map_point_black.png"/> Very High</li>
                                    -->
                                    <li><img src="images/member/map_point_blue.png"/></li>
									<li><img src="images/member/map_point_green.png"/></li>
                                    <li><img src="images/member/map_point_yellow.png"/></li>
                                    <li><img src="images/member/map_point.png"/></li>
                                    <li><img src="images/member/map_point_black.png"/></li>                                    
                            	</ul>
                            	
                            	<div style="clear:both;"></div>
                            	
                                <div id="map" class="block"></div>
                            </li>
                            <li>
                                <table class="zebra listing" cellpadding="0" cellspacing="0">
                                    <thead>
                                        <tr class="intro" >
                                            <td colspan="9">Currently Available</td>
                                        </tr>
                                        <tr>
                                            <th class="sortable" sort="p.address">Address</th>
                                            <th class="sortable" sort="area.area_name">Area</th>
                                            <th class="sortable" sort="st.name">State</th>                                            
                                            <th class="sortable" sort="proj.project_name">Estate</th>
                                            <th class="sortable" sort="p.total_price">Price</th>
                                            <th class="sortable" sort="r1.name">Type</th>
                                            <th class="sortable" sort="p.house_area">Size (sqm)</th>
                                            <th class="sortable" sort="p.land">Land (sqm)</th>
                                            <th class="sortable" sort="p.rent_yield">Yield</th>
                                            <th class="sortable" sort="p.nras">NRAS</th>
                                            <th class="sortable" sort="p.smsf">SMSF</th>
                                            <!--
                                            <th><img src="<?php echo base_url(); ?>images/member/icon-bedrooms-light.png" width="24" height="14" alt="bedrooms" /></th>
                                            <th><img src="<?php echo base_url(); ?>images/member/icon-bathrooms-light.png" width="21" height="19" alt="bathrooms" /></th>
                                            <th><img src="<?php echo base_url(); ?>images/member/icon-garage-light.png" width="25" height="21" alt="car parks" /></th>
                                            -->
                                        </tr>
                                    </thead>
                                    <tbody>
                                    <!-- Listing will load here via AJAX -->
                                    </tbody>                                                                                                                                    
                                </table> 
                            </li>
                        </ul>
                    </div>
                </div><!-- end main content -->
                
                <?php echo form_open('stocklist/ajax', array("id" => "frmSetLatLng", "name" => "frmSetLatLng")); ?>
                    <input type="hidden" name="action" value="set_latlng" />
                    <input type="hidden" id="lat" name="lat" value="" />
                    <input type="hidden" id="lng" name="lng" value="" />
                    <input type="hidden" id="property_id" name="property_id" value="" />
                </form>

<?php if ($user_type_id==USER_TYPE_ADVISOR) : ?>
<div id="columns" class="reveal-modal">
    <form method="post" action="<?php echo site_url('stocklist/generate')?>" id="generateForm">
        <input type="hidden" name="type" value="pdf" id="gentype" />
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
			
        );
    ?>
        <h4>Select the column(s) that you would like to appear in the generated document.</h4>
        <ul class="columns">
        <?php foreach ($aColumns as $key=>$value) : ?>
            <li><input type="checkbox" name="columns[]" checked="checked" value="<?php echo $key?>" /> <?php echo $value?></li>
        <?php endforeach; ?>
        </ul>
        <div style="clear:both;"></div>
        <input type="submit" value="Generate PDF" class="btn" id="generate_pdf" />
        <input type="button" value="Generate CSV" class="btn" id="generate_csv" />
    </form>
</div>
<form method="post" action="<?php echo site_url('stocklist/generate')?>" id="tempForm" style="display:none;"></form>
<?php endif; ?>