<body class="stocklist">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
            
                <div id="curtain">
                    <img src="<?php echo base_url(); ?>/images/member/bigloader.gif" width="64" height="64" alt="Loading" />
                </div>            
            
                <div class="content inner">

                    <ul class="breadcrumbs">
                        <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                        <li>My Properties</li>
                    </ul>

                    <div class="sidebar" style="display: none">
                        <?php echo form_open('myproperties/ajax', array("id" => "frmSearch", "name" => "frmSearch")); ?>
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo STOCKLIST_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_my_properties" />
                            <input type="hidden" id="sort_col" name="sort_col" value="" />
                            <input type="hidden" id="sort_dir" name="sort_dir" value="" />
                            <input type="hidden" id="list_type" name="list_type" value="favourites" />
                            
                            <!-- Slider Fields -->
                            <input type="hidden" id="min_total_price" name="min_total_price" value="<?=$property_data->min_total_price;?>" />
                            <input type="hidden" id="max_total_price" name="max_total_price" value="<?=$property_data->max_total_price;?>" />
                            <input type="hidden" id="min_bathrooms" name="min_bathrooms" value="<?=$property_data->min_bathrooms;?>" />
                            <input type="hidden" id="max_bathrooms" name="max_bathrooms" value="<?=$property_data->max_bathrooms;?>" />                            
                            <input type="hidden" id="min_bedrooms" name="min_bedrooms" value="<?=$property_data->min_bedrooms;?>" />
                            <input type="hidden" id="max_bedrooms" name="max_bedrooms" value="<?=$property_data->max_bedrooms;?>" />                                                        
                            <input type="hidden" id="min_garage" name="min_garage" value="<?=$property_data->min_garage;?>" />
                            <input type="hidden" id="max_garage" name="max_garage" value="<?=$property_data->max_garage;?>" />                                                                                    
                            <input type="hidden" id="min_land" name="min_land" value="<?=$property_data->min_land;?>" />
                            <input type="hidden" id="max_land" name="max_land" value="<?=$property_data->max_land;?>" /> 
                            <input type="hidden" id="min_house" name="min_house" value="<?=$property_data->min_house;?>" />
                            <input type="hidden" id="max_house" name="max_house" value="<?=$property_data->max_house;?>" /> 
                            <input type="hidden" id="min_yield" name="min_yield" value="<?=number_format($property_data->min_yield, 1);?>" />
                            <input type="hidden" id="max_yield" name="max_yield" value="<?=number_format($property_data->max_yield, 1);?>" />                                                                                   
                                                    
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
                                        <p>From <span class="lowerVal"><?=$property_data->min_land; ?></span> to <span class="upperVal"><?=$property_data->max_land; ?></span>sqm</p>                                    
                                        <div id="sliderLandArea" class="noUiSlider"></div>
                                    </li>
                                    <li>
                                        <h3>House Area</h3>
                                        <p>From <span class="lowerVal"><?=$property_data->min_house; ?></span> to <span class="upperVal"><?=$property_data->max_house; ?></span>sqm</p>                                    
                                        <div id="sliderHouseArea" class="noUiSlider"></div>
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
                                    <li>
                                        <h3>Status</h3>
                                        <select id="status" name="status">
                                            <option value="">View All</option>
                                            <?php echo $this->utilities->print_select_options_array($status_options, false); ?>
                                        </select>
                                    </li>
                                </ul>
                                                      
                            </fieldset>
                        </form>               
                    <!-- end sidebar --></div>                
                    
                    <div class="mainCol">
                        <h1>My Properties</h1>
                        <ul class="tabNav" id="tabNav">
				            <li><a class="active" href="javascript:;" list_type="favourites">My Favourites</a></li>
                            <li><a href="javascript:;" list_type="reserved" name="reserved">Reserved</a></li>
                            <li><a href="javascript:;" list_type="current_purchases">Current Purchases</a></li>
                            <li><a href="javascript:;" list_type="completed_purchases">Completed Purchases</a></li>
				        </ul>
                        <!--<ul id="tabNav">
                            <li><a class="active" href="javascript:;" list_type="favourites">My Favourites</a></li>
                            <li><a href="javascript:;" list_type="reserved">Reserved</a></li>
                            <li><a href="javascript:;" list_type="sold">Sold</a></li>
                        </ul>-->
                        <ul id="tabs">
                            <li>
                                <ul class="propertyListing"></ul>
                            </li>
                            <li>
                                <ul class="propertyListing"></ul>
                            </li>
                            <li>
                                <ul class="propertyListing"></ul>
                            </li>
                            <li>
                                <ul class="propertyListing"></ul>
                            </li>
                        </ul>
                    </div>                
                </div><!-- end main content -->