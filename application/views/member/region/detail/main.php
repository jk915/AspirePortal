<?php
    // Use Google to create a static image of the map for the map region.
    $floorplan = $map;
?>

<body>
    <div id="wrapper">
    
        <?php $this->load->view("member/page_header"); ?>   
              
        <div id="main">
            <div id="curtain">
                <img src="<?php echo base_url(); ?>/images/member/bigloader.gif" width="64" height="64" alt="Loading" />
            </div>          
            <div class="content inner">           
			    <div class="mainCol">
			        <div class="heroSlider">
			            <h1><?php echo $region->region_name;?> <span class="price">&nbsp;</span></h1>
                        <ul class="secondary">
                            <li><a href="javascript:history.go(-1)">&lt;&lt; Back</a></li>
                        </ul>
			            <div id="hero">
                        <?php //if($gallery) : ?>
                        <?php
                            $counter = 0;
                            $last_hero = "";
                            
                            //foreach($gallery->result() as $doc)
                            // {
                                // if($doc->document_path == "") continue;
                                
                                // $hero = image_resize($doc->document_path, 640, 452); 
                                
                                // $last_hero = $hero;
                                // $counter++;
                                ?>
                            <img data-thumb="<?php //echo $doc->document_path . "_640x452.jpg"; ?>" src="<?php //echo $hero; ?>" width="640" height="452" alt="<?php echo $region->region_name;?> Image <?php echo $counter; ?>" />                                
                                
                        <?php //endif; ?>
			            </div>  
			            <a id="nextThumb" href="#"></a><a id="prevThumb" href="#"></a>
			            <div id="floorplan"><img src="<?php echo image_resize($floorplan, 640, 452);?>" width="640" height="452" alt="floorplan" /></div>
			            <a id="floorThumb" href="#"><img src="<?php echo image_resize($floorplan, 117, 80);?>" width="117" height="80" alt="Click to view the location map" /></a>
			            <ul class="actions">
                            <?php if($user_type_id == USER_TYPE_ADVISOR) : ?>
			                <li class="agents"><a href="#" id="agentareabtn"><img src="<?php echo base_url()?>images/member/icon-agent.png" width="17" height="17" alt="agent" />agents area</a></li>
                            <?php endif; ?>
			                <li class="print"><a href="<?php echo site_url("brochure/region/$region->region_id")?>" target="_blank"><img src="<?php echo base_url()?>images/member/icon-print.png" width="17" height="14" alt="print" />print report</a></li>
			            </ul>
			        <!-- end heroSlider --></div>
			        
			        <div class="subCol">
                        <h2>Region Overview</h2>
                        <div class="justify">
                        	<?php echo $region->overview; ?>
                        </div>
			        </div>
			        
			        <div class="subCol alt">

			        </div>    
                    
                    <div class="clear"></div>
                    
                          
			        
                    
			    <!-- end mainCol --></div>     
			
			    <div class="sidebar">
			    	<div class="justify">
                    	<?php echo $region->short_description; ?>
					</div>
					
			        <ul class="tabNav">
			            <li><a href="javascript:;">Specifications</a></li>
			            <li><a href="javascript:;">Downloads</a></li>
			        </ul>
			        
			        <ul class="tabs">
			            <li>
                            
			            </li>
			            
			            
			        </ul>
			        
			        <!-- end sidebar --></div>                             
			<!-- end main content --></div>
			
            
                <div class="clear"></div>
            </div>
		</div>	