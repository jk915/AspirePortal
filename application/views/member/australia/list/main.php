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
                        <li>States</li>
                    </ul>                
                                       
						<div class="sidebar">
                        <?php echo form_open('states/ajax', array("id" => "frmSearch", "name" => "frmSearch")); ?>
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo STOCKLIST_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_statelist" />
                            
                            <input type="hidden" id="list_type" name="list_type" value="grid" />
                            
                            <!-- Slider Fields -->
                            <input type="hidden" id="min_total_price" name="min_total_price" value="<?=$area_data->min_median_house_price;?>" />
                            <input type="hidden" id="max_total_price" name="max_total_price" value="<?=$area_data->max_median_house_price;?>" />                                                                 
                        </form>               
                    <!-- end sidebar --></div> 
						
                    <div class="mainCol mainColwidth">
                        <h1>States List</h1>
						<ul id="tabNav">
                            <li><a class="active" href="#" list_type="grid"><img src="<?php echo base_url(); ?>images/member/stocklist-view-grid.png" width="20" height="20" alt="View properties in a grid." /></a></li>
                            <li><a href="#" list_type="map"><img src="<?php echo base_url(); ?>images/member/stocklist-view-map.png" width="20" height="20" alt="View properties on a map." /></a></li>
                            <li><a href="#" list_type="list"><img src="<?php echo base_url(); ?>images/member/stocklist-view-table.png" width="20" height="20" alt="View properties as a list." /></a></li>
                        </ul>
						<ul id="tabs">
                            <li>
                                <ul class="propertyListing plwidth">                  
                                    <li>
                                        <div class="property">
                                            <img src="<?php echo base_url(); ?>images/member/temp-property1.jpg" width="196" height="130" alt=" " />
                                            <div class="propertyDetails">
                                                <h2>Property Name</h2>
                                                <h3>Property Location</h3>
                                                <ul class="specs">
                                                    <li class="beds">3</li>
                                                    <li class="baths">2</li>
                                                    <li class="parking">2</li>
                                                </ul>
                                                <h4>$459, 900</h4> 
                                                <div class="additionalInfo">                                        
                                                    <p><em>Width:</em> 12.5m wide<br />
                                                    <em>Land area:</em> 400sqm</p>
                                                    <p>Estimated completion date: 29/10/2012.</p>
                                                <!--end additionalInfo--></div>  
                                            </div>
                                            <a class="overlay" href="#"></a>
                                            <a class="viewMore" href="#"></a>                            
                                        <!-- end property --></div>
                                    </li>
                                    <li>
                                        <div class="property">
                                            <img src="<?php echo base_url(); ?>images/member/temp-property2.jpg" width="196" height="130" alt=" " />
                                            <div class="propertyDetails">
                                                <h2>Property Two Name</h2>
                                                <h3>Another Location</h3>
                                                <ul class="specs">
                                                    <li class="beds">3</li>
                                                    <li class="baths">2</li>
                                                    <li class="parking">2</li>
                                                </ul>
                                                <h4>$459, 900</h4> 
                                                <div class="additionalInfo">                                        
                                                    <p><em>Width:</em> 12.5m wide<br />
                                                    <em>Land area:</em> 400sqm</p>
                                                    <p>Estimated completion date: 29/10/2012.</p>
                                                <!--end additionalInfo--></div>  
                                            </div>
                                            <a class="overlay" href="#"></a>
                                            <a class="viewMore" href="#"></a>                            
                                        <!-- end property --></div>
                                    </li>
                                    <li>
                                        <div class="property">
                                            <img src="<?php echo base_url(); ?>images/member/temp-property3.jpg" width="196" height="130" alt=" " />
                                            <div class="propertyDetails">
                                                <h2>Property Name</h2>
                                                <h3>Property Location</h3>
                                                <ul class="specs">
                                                    <li class="beds">3</li>
                                                    <li class="baths">2</li>
                                                    <li class="parking">2</li>
                                                </ul>
                                                <h4>$459, 900</h4> 
                                                <div class="additionalInfo">                                        
                                                    <p><em>Width:</em> 12.5m wide<br />
                                                    <em>Land area:</em> 400sqm</p>
                                                    <p>Estimated completion date: 29/10/2012.</p>
                                                <!--end additionalInfo--></div>  
                                            </div>
                                            <a class="overlay" href="#"></a>
                                            <a class="viewMore" href="#"></a>                                 
                                        <!-- end property --></div>
                                    </li>
                                    <li>
                                        <div class="property">
                                            <img src="<?php echo base_url(); ?>images/member/temp-property4.jpg" width="196" height="130" alt=" " />
                                            <div class="propertyDetails">
                                                <h2>Property Name</h2>
                                                <h3>Property Location</h3>
                                                <ul class="specs">
                                                    <li class="beds">3</li>
                                                    <li class="baths">2</li>
                                                    <li class="parking">2</li>
                                                </ul>
                                                <h4>$459, 900</h4> 
                                                <div class="additionalInfo">                                        
                                                    <p><em>Width:</em> 12.5m wide<br />
                                                    <em>Land area:</em> 400sqm</p>
                                                    <p>Estimated completion date: 29/10/2012.</p>
                                                <!--end additionalInfo--></div>  
                                            </div>
                                            <a class="overlay" href="#"></a>
                                            <a class="viewMore" href="#"></a>                                
                                        <!-- end property --></div>
                                    </li>    
                                    <li>
                                        <div class="property">
                                            <img src="<?php echo base_url(); ?>images/member/temp-property5.jpg" width="196" height="130" alt=" " />
                                            <div class="propertyDetails">
                                                <h2>Property Name</h2>
                                                <h3>Property Location</h3>
                                                <ul class="specs">
                                                    <li class="beds">3</li>
                                                    <li class="baths">2</li>
                                                    <li class="parking">2</li>
                                                </ul>
                                                <h4>$459, 900</h4> 
                                                <div class="additionalInfo">                                        
                                                    <p><em>Width:</em> 12.5m wide<br />
                                                    <em>Land area:</em> 400sqm</p>
                                                    <p>Estimated completion date: 29/10/2012.</p>
                                                <!--end additionalInfo--></div>  
                                            </div>
                                            <a class="overlay" href="#"></a>
                                            <a class="viewMore" href="#"></a>                                
                                        <!-- end property --></div>
                                    </li>                                                                                             
                                </ul>
                            </li>
                            <li>
							<div class="maplist">
                            	<ul id="risk_comment">
									<li><img src="images/member/map_point_blue.png"/>Very Low</li>
                            		<li><img src="images/member/map_point_green.png"/> Low</li>
                            		<li><img src="images/member/map_point_yellow.png"/> Medium</li>
                            		<li><img src="images/member/map_point.png"/> High</li>
                            		<li><img src="images/member/map_point_black.png"/> Very High</li>
                            	</ul>
                            	
                            	<div style="clear:both;"></div>
                            	
                                <div id="map" class="block"></div>
								</div>
                            </li>
                            <li>
                                <table class="zebra listing" cellpadding="0" cellspacing="0">
                                    <thead>
                                        <tr class="intro" >
                                            <td colspan="9">Current Projects</td>
                                        </tr>
                                        <tr>
                                            
                                            <th class="sortable" sort="nc_areas.area_name">State</th>
                                            <th class="sortable" sort="s.name">State</th>                                            
                                            <th class="sortable" sort="p.prices_from">Median House Price</th>
                                            
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
                
                <?php echo form_open('states/ajax', array("id" => "frmSetLatLng", "name" => "frmSetLatLng")); ?>
                    <input type="hidden" name="action" value="set_latlng" />
                    <input type="hidden" id="lat" name="lat" value="" />
                    <input type="hidden" id="lng" name="lng" value="" />
                    <input type="hidden" id="project_id" name="project_id" value="" />
                </form>
