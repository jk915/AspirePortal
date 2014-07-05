<?php
    // Use Google to create a static image of the map for the map area.
    $floorplan = $map;
?>
<!-- By Ajay TasksEveryday -->
<script>var uri='<?=$this->uri->segment(4);?>'</script>
<!-- END -->
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
                        <h1>Project: <?php echo $project->project_name;?> <span class="price">$<?php echo number_format($project_min_price, "0", ".", ","); ?>+</span></h1>
                        <ul class="secondary">
                            <li><a href="javascript:history.go(-1)">&lt;&lt; Back</a></li>
                        </ul>
                        <div id="hero">
                        <?php if($gallery) : ?>
                        <?php
                            $counter = 0;
                            $last_hero = "";
                            
                            foreach($gallery->result() as $doc)
                            {
                                if($doc->document_path == "") continue;
                                $hero = image_resize($doc->document_path, 640, 452); 
                                
                                $last_hero = $hero;
                                $counter++;
                                ?>
                            <img data-thumb="<?php echo $doc->document_path . "_640x452.jpg"; ?>" src="<?php echo $hero; ?>" width="640" height="452" alt=" " />                                
                                <?php
                            }
                        ?>
                        <?php endif; ?>
                        </div>  
                        <a id="nextThumb" href="#"></a><a id="prevThumb" href="#"></a>
                        <div id="floorplan"><img src="<?php echo image_resize($floorplan, 640, 452);?>" width="640" height="452" alt="floorplan" /></div>
                        <a id="floorThumb" href="#"><img src="<?php echo image_resize($floorplan, 117, 80);?>" width="117" height="80" alt="Click to view the location map" /></a>
                        <ul class="actions">
                            <!--<li class="agents"><a href="#"><img src="<?php echo base_url()?>images/member/icon-agent.png" width="17" height="17" alt="agent" />agents area</a></li>-->
                            <li class="print"><a href="<?php echo site_url("brochure/project/$project->project_id")?>" target="_blank"><img src="<?php echo base_url()?>images/member/icon-print.png" width="17" height="14" alt="print" />print report</a></li>
                        </ul>
                    <!-- end heroSlider --></div>
                    
                    <?php if($metadata) : ?>
                    <div class="moreinfo">
                        <h2>More Information</h2>
                        
                        <ul>
                            <?php foreach($metadata->result() as $item) : ?>
                            <li>
                                <h3><?=$item->name; ?></h3>
                                <div class="justify">
                                	<?=$item->value; ?>
                                </div>
                            </li>
                            <?php endforeach; ?>                    
                        </ul>
                    </div>
                    <?php endif; ?>  

                <!-- end mainCol --></div>     
            
                <div class="sidebar">
                	<div class="justify">
                    	<?php echo $project->page_body; ?>
					</div>
					
                    <ul class="tabNav">
                        <li><a href="javascript:;">Specifications</a></li>
                        <li><a href="javascript:;">Downloads</a></li>
                    </ul>
  
                    <ul class="tabs">
                        <li>
                            <table cellpadding="0" cellspacing="0">
                                <?php if($project->area_name != "") : ?>
                                <tr>
                                    <td>Area</td>
                                    <td><a href="<?php echo base_url() . "areas/detail/" . $project->area_id; ?>"><?=$project->area_name; ?></a></td>
                                </tr>
                                <?php endif; ?> 
                                <?php if(is_numeric($project->prices_from)) : ?>
                                <tr>
                                    <td>Prices From</td>
                                    <td>$<?=number_format($project_min_price, 0, ".", ","); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($project->website != "") : ?>
                                <tr>
                                    <td>Website</td>
                                    <td><a href="<?php echo $project->website; ?>" target="_blank"><?=$project->website; ?></a></td>
                                </tr>
                                <?php endif; ?>
								
								<?php if($project->eoi_deposit != " ") : ?>
                                <tr>
                                    <td>EOI Deposit</td>
                                    <td><?=$project->eoi_deposit; ?></td>
                                </tr>
                                <?php endif; ?>
								
								<?php if($project->credit_card != "") : ?>
                                <tr>
                                    <td>Credit Card</td>
                                    <td><?=$project->credit_card; ?></td>
                                </tr>
                                <?php endif; ?>
								
								<?php if($project->account_name != "") : ?>
                                <tr>
                                    <td>Account Name</td>
                                    <td><?=$project->account_name; ?></td>
                                </tr>
                                <?php endif; ?>
								
								<?php if($project->BSB != " ") : ?>
                                <tr>
                                    <td>BSB</td>
                                    <td><?=$project->BSB; ?></td>
                                </tr>
                                <?php endif; ?>
								
								<?php if($project->account_number != "") : ?>
                                <tr>
                                    <td>Account Number</td>
                                    <td><?=$project->account_number; ?></td>
                                </tr>
                                <?php endif; ?>
								
								<?php if($project->reference != "") : ?>
                                <tr>
                                    <td>Reference</td>
                                    <td><?=$project->reference; ?></td>
                                </tr>
                                <?php endif; ?>
								
								<?php if($project->payment_terms_conditions != "") : ?>
                                <tr>
                                    <td>Payment Terms Conditions</td>
                                    <td><?=$project->payment_terms_conditions; ?></td>
                                </tr>
                                <?php endif; ?>
								
                            </table>
                        </li>
                        
                        <li>
                            <?php if($docs) : ?>
			                <ul class="downloads">
                                <?php foreach($docs->result() as $doc) : ?>
                                    <?php if($doc->document_path != "") : ?>
                                    	<?php $utid = $this->session->userdata["user_type_id"]; ?>
                                    	<?php if ($utid == USER_TYPE_ADVISOR) : ?>
                                <li><a href="<?php echo base_url(); ?>projects/downloads/<?php echo $doc->document_path; ?>" target="_blank"><?php echo $doc->document_name; ?></a></li>
                                		<?php else : ?>
                                			<?php if ($doc->extra_data != 'advisors_only') : ?>
                        		<li><a href="<?php echo base_url() . $doc->document_path; ?>" target="_blank"><?php echo $doc->document_name; ?></a></li>
                        					<?php endif; ?>
                                		<?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>                                                                                                   
			                </ul>
                            <?php endif; ?>
                        </li>
                    </ul>
                    
                <!-- end sidebar --></div> 
                    
                    <?php if($properties) : ?>
                    <table class="zebra listing" cellpadding="0" cellspacing="0">
                        <thead>
                            <tr class="intro" >
                                <td colspan="9">Stock Available</td>
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
                            </tr>
                        </thead>
                        <tbody>
                        <?php
                            $this->load->view("member/stocklist/list/list", array("properties" => $properties));
                        ?>
                        </tbody>                                                                                                                                    
                    </table>                       
                    <?php endif; ?>                    
                                                
            <!-- end main content --></div>