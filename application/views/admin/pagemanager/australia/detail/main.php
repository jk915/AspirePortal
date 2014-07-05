<?php
    // Use Google to create a static image of the map for the map area.
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
			            <h1><?php echo $area->state_name;?> <span class="price">&nbsp;</span></h1>
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
                            <img data-thumb="<?php //echo $doc->document_path . "_640x452.jpg"; ?>" src="<?php //echo $hero; ?>" width="640" height="452" alt="<?php echo $area->state_name;?> Image <?php echo $counter; ?>" />                                
                                
                        <?php //endif; ?>
			            </div>  
			            <a id="nextThumb" href="#"></a><a id="prevThumb" href="#"></a>
			            <div id="floorplan"><img src="<?php echo image_resize($floorplan, 640, 452);?>" width="640" height="452" alt="floorplan" /></div>
			            <a id="floorThumb" href="#"><img src="<?php echo image_resize($floorplan, 117, 80);?>" width="117" height="80" alt="Click to view the location map" /></a>
			            <ul class="actions">
                            <?php if($user_type_id == USER_TYPE_ADVISOR) : ?>
			                <li class="agents"><a href="#" id="agentareabtn"><img src="<?php echo base_url()?>images/member/icon-agent.png" width="17" height="17" alt="agent" />agents area</a></li>
                            <?php endif; ?>
			                <li class="print"><a href="<?php echo site_url("brochure/states/$area->state_id")?>" target="_blank"><img src="<?php echo base_url()?>images/member/icon-print.png" width="17" height="14" alt="print" />print report</a></li>
			            </ul>
			        <!-- end heroSlider --></div>
			        
			        <div class="subCol">
                        <h2>Area Overview</h2>
                        <div class="justify">
                        	<?php echo $area->overview; ?>
                        </div>
			        </div>
			        
			        <div class="subCol alt">

			        </div>    
                    
                    <div class="clear"></div>
                    
                          
			        
                    
			    <!-- end mainCol --></div>     
			
			    <div class="sidebar">
			    	<div class="justify">
                    	<?php echo $area->short_description; ?>
					</div>
					
			        <ul class="tabNav">
			            <li><a href="javascript:;">Specifications</a></li>
			            <li><a href="javascript:;">Downloads</a></li>
			        </ul>
			        
			        <ul class="tabs">
			            <li>
                            <table cellpadding="0" cellspacing="0">
                                <?php if((is_numeric($area->median_house_price)) && ($area->median_house_price > 0)) : ?>
                                <tr>
                                    <td>Median House Price</td>
                                    <td>$<?php echo number_format($area->median_house_price, 0, ".", ","); ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if((is_numeric($area->median_unit_price)) && ($area->median_unit_price > 0)) : ?>
                                <tr>
                                    <td>Median Unit Price</td>
                                    <td>$<?php echo number_format($area->median_unit_price, 0, ".", ","); ?></td>
                                </tr>
                                <?php endif; ?>  
                                <?php if($area->quarterly_growth != "") : ?>
                                <tr>
                                    <td>Quarterly Growth</td>
                                    <td><?php echo $area->quarterly_growth; ?></td>
                                </tr>
                                <?php endif; ?> 
                                <?php if($area->month12_growth != "") : ?>
                                <tr>
                                    <td>12 Month Growth</td>
                                    <td><?php echo $area->month12_growth; ?></td>
                                </tr>
                                <?php endif; ?>   
                                <?php if($area->year3_growth != "") : ?>
                                <tr>
                                    <td>3 Year Growth</td>
                                    <td><?php echo $area->year3_growth; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($area->year4_growth != "") : ?>
                                <tr>
                                    <td>4 Year Growth</td>
                                    <td><?php echo $area->year4_growth; ?></td>
                                </tr>
                                <?php endif; ?>  
                                <?php if($area->median_growth_this_year != "") : ?>
                                <tr>
                                    <td>Median Growth This Year</td>
                                    <td><?php echo $area->median_growth_this_year; ?></td>
                                </tr>
                                <?php endif; ?>  
                                <?php if($area->weekly_median_advertised_rent != "") : ?>
                                <tr>
                                    <td>Weekly Median Advertised Rent</td>
                                    <td><?php echo $area->weekly_median_advertised_rent; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($area->total_population != "") : ?>
                                <tr>
                                    <td>Total Population</td>
                                    <td><?php echo $area->total_population; ?></td>
                                </tr>
                                <?php endif; ?>  
                                <?php if($area->median_age != "") : ?>
                                <tr>
                                    <td>Median Age</td>
                                    <td><?php echo $area->median_age; ?></td>
                                </tr>
                                <?php endif; ?>  
                                <?php if($area->number_private_dwellings != "") : ?>
                                <tr>
                                    <td>No. Private Dwellings</td>
                                    <td><?php echo $area->number_private_dwellings; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($area->weekly_median_household_income != "") : ?>
                                <tr>
                                    <td>Weekly Median Household Income</td>
                                    <td><?php echo $area->weekly_median_household_income; ?></td>
                                </tr>
                                <?php endif; ?> 
                                <?php if($area->closest_cbd != "") : ?>
                                <tr>
                                    <td>Closest CBD</td>
                                    <td><?php echo $area->closest_cbd; ?></td>
                                </tr>
                                <?php endif; ?> 
                                <?php if($area->approx_time_cbd != "") : ?>
                                <tr>
                                    <td>Approx time to CBD</td>
                                    <td><?php echo $area->approx_time_cbd; ?></td>
                                </tr>
                                <?php endif; ?>
                                <?php if($area->approx_distance_cbd != "") : ?>
                                <tr>
                                    <td>Approx Distance to CBD</td>
                                    <td><?php echo $area->approx_distance_cbd; ?></td>
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
                                <li><a href="<?php echo base_url() . $doc->document_path; ?>" target="_blank"><?php echo $doc->document_name; ?></a></li>
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
			<!-- end main content --></div>
			
            <div id="commentModal" class="reveal-modal">
                <h2>Comments</h2>
                <a class="close-reveal-modal">&#215;</a>
                <ul class="comments" id="commentsList">
            <?php if ($comments != false) : ?>
                <?php foreach ($comments->result() as $comment) : ?>
                    <li>
                        <div class="ct"><?php echo nl2br($comment->comment)?></div>
                        <div class="meta"><strong><?php echo trim("$comment->first_name $comment->last_name")?></strong> @ <em><?php echo date('d-m-Y h:ia', $comment->ts_added)?></em></div>
                    </li>
                <?php endforeach; ?>
            <?php endif; ?>
                </ul>
                <?php echo form_open('areas/ajax','id="commentForm"', array('action'=>'submit_comment','area_id'=>$area->area_id));?>
                    <label>Add your comment below:</label>
                    <textarea name="comment" rows="10"></textarea>
                    <input type="submit" value="Add Comment" />
                </div>
                <div class="clear"></div>
            </div>