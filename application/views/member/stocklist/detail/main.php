<?php
    $project_link = base_url() . "projects/detail/" . $project->project_id;
    $area_link = base_url() . "areas/detail/" . $area->area_id;
    $floorplan = "";
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
			            <h1>Lot <?php echo $property->lot . ', ' . $property->address;?> <span class="price">$<?php echo number_format($property->total_price, 0, ".", ","); ?></span></h1>
			            <ul class="secondary">
			                <li><a href="<?php echo $area_link; ?>"><?php echo $area->area_name; ?></a></li>
			                <li><a href="<?php echo $project_link; ?>"><?php echo $project->project_name; ?></a></li>
			            </ul>
			            <div id="hero">
                        <?php if($gallery) : ?>
                        <?php
                            $counter = 0;
                            $last_hero = "";
                            
                            foreach($gallery->result() as $doc)
                            {
                                if($doc->document_path == "") continue;

                                if((stristr($doc->document_name, "floorplan")) || (stristr($doc->extra_data, "floorplan"))) 
                                {
                                    $floorplan = $doc->document_path;
                                    continue;    
                                }
                                
                                $hero = image_resize($doc->document_path, 640, 452); 
                                
                                $last_hero = $hero;
                                $counter++;
                                ?>
                            <img data-thumb="<?php echo $doc->document_path . "_640x452.jpg"; ?>" src="<?php echo $hero; ?>" width="640" height="452" alt="<?php echo $property->lot . ', ' . $property->address;?> Image <?php echo $counter; ?>" />                                
                                <?php
                            }
                        ?>
                        <?php endif; ?>
			            </div>  
						<a id="nextThumb" href="#"></a><a id="prevThumb" href="#"></a>
                        <?php if($floorplan != "") : ?>
			            <!--<div id="floorplan"><img src="<?php //echo image_resize($floorplan, 640, 452);?>" width="640" height="452" alt="floorplan" /></div>-->
			            <a id="floorThumb" href="<?php echo base_url().$floorplan; ?>"><img src="<? echo base_url().'images/member/floorplan-icon.jpg' ;?>" width="117" height="80" alt="Click to view the floorplan" /></a>
                        <?php endif; ?>
			            <ul class="actions">
                            <?php if(($user_type_id == USER_TYPE_ADVISOR) && (!$is_sub_advisor)) : ?>
			                <li class="agents"><a href="javascript:;" class="propertyadvisorinfo"><img src="<?php echo base_url()?>images/member/icon-agent.png" width="17" height="17" alt="agent" />advisors area</a></li>
                            <?php endif; ?>
                            <?php if(($user_type_id == USER_TYPE_ADVISOR) && ($property->status == "available")) : ?>
			                <li class="reserve"><a href="#" id="btnReserve">reserve this property</a></li>
                            <?php elseif ((in_array($user_type_id, array(USER_TYPE_INVESTOR, USER_TYPE_LEAD, USER_TYPE_PARTNER)) AND isset($advisor) AND $advisor) && ($property->status == "available")) : ?>
			                <li class="reservation_request"><a href="#" id="btnReservationRequest">reservation request</a></li>
                            <?php endif; ?>
			                <li class="print"><a href="<?php echo site_url("brochure/property/$property->property_id")?>" target="_blank" id="print_brochure_property"><img src="<?php echo base_url()?>images/member/icon-print.png" width="17" height="14" alt="print"/>print report</a></li>
			            </ul>
			        <!-- end heroSlider --></div>
			        
			        <div class="subCol">
			            <h2>Project Summary <a href="<?php echo $project_link; ?>">view project detail</a><a href="<?php echo site_url("brochure/project/$property->project_id")?>" target="_blank"><img src="<?php echo base_url()?>images/member/icon-print.png" width="17" height="14" alt="print" /></a></h2>
                        <?php if($project->logo != "") : ?>
                        <img src="<?php echo image_resize($project->logo, 300, 0); ?>" width="300" alt="<?php echo $project->project_name; ?>" />
                        <?php endif; ?>
			            <p><em>Project Name:</em> <?php echo $project->project_name; ?></p>
			            <!-- By Ajay TasksEveryday -->
			            <p><em>Prices From:</em> $<?php echo number_format($project_min_price, 0, ".", ","); ?></p>
						<!-- END -->
			            <p><em>Number Available:</em> <?php echo $project->num_available_properties; ?></p>
			        </div>
			        
			        <div class="subCol alt">
			            <h2>Area Summary <a href="<?php echo $area_link; ?>">view area detail</a><a href="<?php echo site_url("brochure/area/$property->area_id")?>" target="_blank"><img src="<?php echo base_url()?>images/member/icon-print.png" width="17" height="14" alt="print" /></a></h2>
                        <?php if($area->area_hero_image != "") : ?>
                        <img src="<?php echo image_resize($area->area_hero_image, 300, 0); ?>" width="300" alt="<?php echo $area->area_name; ?>" />
                        <?php endif; ?>                        
			            <p><em>Area:</em>  <?php echo $area->area_name; ?></p>      
                        <?php if((is_numeric($area->total_population)) && ($area->total_population > 0)) : ?>
			            <p><em>Total Population:</em> <?php echo $area->total_population; ?></p>
                        <?php endif; ?>
                        <?php if((is_numeric($area->median_house_price)) && ($area->median_house_price > 0)) : ?>
                        <p><em>Median House Price:</em> $<?php echo number_format($area->median_house_price, 0, ".", ","); ?></p>
                        <?php endif; ?>  
                        <?php if($area->closest_cbd != "") : ?>                      
			            <p><em>Closest CBD:</em> <?php echo $area->closest_cbd; ?></p>
                        <?php endif; ?>
                        <?php if($area->approx_distance_cbd != "") : ?>                      
                        <p><em>Distance to CBD:</em> <?php echo $area->approx_distance_cbd; ?></p>
                        <?php endif; ?>                                  
			        </div>        
			        
                    
			    <!-- end mainCol --></div>     
			
			    <div class="sidebar">
                    <?php /* First paragraph needs a class of "imp" to make it bold */ ?>
                    <div class="justify">
                    	<?php echo $property->overview; ?>
                    </div>
					<?php if (!$favourite) : ?>                    
                    <a href="javascript:;" id="btnaddToFavourites" class="btn fleft">Add To Favourites</a>
                    <?php else: ?>
                    <a href="javascript:;" id="btnremoveFromFavourites" class="btn fleft">Remove From Favourites</a>
                    <?php endif; ?>
			
			        <table class="info" cellpadding="0" cellspacing="0">
			            <tr>
			                <td><img src="<?php echo base_url()?>images/member/icon-bedrooms-drk.png" width="24" height="14" alt="bedrooms" /><?php echo $property->bedrooms; ?></td>
			                <td><img src="<?php echo base_url()?>images/member/icon-bathrooms-drk.png" width="21" height="19" alt="bathrooms" /><?php echo $property->bathrooms; ?></td>
			                <td><img src="<?php echo base_url()?>images/member/icon-garage-drk.png" width="25" height="21" alt="garage" /><?php echo $property->garage; ?></td>
			            </tr>
			            <tr class="returns">
                            <?php $numcols = 1; ?>
                            <?php if($property->nras == 1) : ?>
			                <td><img src="<?php echo base_url()?>images/member/icon-tick.png" width="14" height="11" alt=" " />NRAS</td>
                            <?php $numcols++; ?>
                            <?php endif; ?>
                            <?php if($property->smsf == 1) : ?>
			                <td><img src="<?php echo base_url()?>images/member/icon-tick.png" width="14" height="11" alt=" " />SMSF</td>
                            <?php $numcols++; ?>
                            <?php endif; ?>
			                <td><img src="<?php echo base_url()?>images/member/icon-risk-<?php echo $project->rate; ?>.png" width="29" height="17" /></td>
                            <?php
                                $diff = 3 - $numcols;
                                while($diff > 0)
                                {
                                    echo '<td>&nbsp;</td>';
                                    $diff = $diff - 1;    
                                }
                            ?>
			            </tr>
						
			        </table>
			        
			        <ul class="tabNav">
			            <li><a href="javascript:;">Specifications</a></li>
			            <li><a href="javascript:;">Downloads</a></li>
			        </ul>
			        
			        <ul class="tabs">
			            <li>
			                <table cellpadding="0" cellspacing="0">
                                <?php if($property->property_type != "") : ?>
			                    <tr>
			                        <td>Property Type</td>
			                        <td><?php echo $property->property_type; ?></td>
			                    </tr>
                                <?php endif; ?>
                                
								
								<?php if($property->display_on_front_end != "0")
								{
								?>
			                    <tr>
			                        <td>Builder</td>
			                        <td><?php echo $property->builder_name; ?></td>
			                    </tr>
                                <?php } ?>
																
                                <tr>
			                        <td>Status</td>
			                        <td><?php echo ucfirst($property->status); ?></td>
			                    </tr>
                                
                                <?php if($property->contract_type != "") : ?>
                                <tr>
                                    <td>Contract Type</td>
                                    <td><?php echo $property->contract_type; ?></td>
                                </tr>
                                <?php endif; ?>

								
								<?php if($property->title_type != "") : ?>
                                <tr>
                                    <td>Title Type</td>
                                    <td><?php echo $property->title_type; ?></td>
                                </tr>
                                <?php endif; ?>
								
								
                                
			                    <tr>
			                        <td>Titled</td>
			                        <td><?php echo ($property->titled == 1) ? "Yes" : "No"; ?></td>
			                    </tr>
                                <?php if(($property->titled == 0) && ($property->estimated_date != "") && ($property->estimated_date != "0")) : ?>
                                <tr>
                                    <td>Estimated Date</td>
                                    <td><?php echo $property->estimated_date; ?></td>
                                </tr>
                                                               
                                <?php endif; ?>
                                <?php if((is_numeric($property->rent_yield)) && ($property->rent_yield > 0)) : ?>
			                    <tr> 
			                        <td>Rent Yield</td>
			                        <td><?php echo $property->rent_yield; ?>%</td>
			                    </tr>
                                <?php endif; ?>
                                <?php if((is_numeric($property->approx_rent)) && ($property->approx_rent > 0)) : ?>
                                <tr> 
                                    <td>Market Rent</td>
                                    <td>$<?php echo $property->approx_rent; ?></td>
                                </tr>
                                <?php endif; ?>   
                                <?php if((is_numeric($property->land)) && ($property->land > 0)) : ?>
                                <tr> 
                                    <td>Land Area</td>
                                    <td><?php echo $property->land; ?> sqm</td>
                                </tr>
                                <?php endif; ?>   
                                <?php if((is_numeric($property->house_area)) && ($property->house_area > 0)) : ?>
                                <tr> 
                                    <td>House Area</td>
                                    <td><?php echo $property->house_area; ?> sqm</td>
                                </tr>
                                <?php endif; ?> 
                                <?php if((is_numeric($property->land_price)) && ($property->land_price > 0)) : ?>
                                <tr> 
                                    <td>Land Price</td>
                                    <td>$<?php echo number_format($property->land_price, 0, ".", ","); ?></td>
                                </tr>
                                <?php endif; ?> 
                                <?php if((is_numeric($property->house_price)) && ($property->house_price > 0)) : ?>
                                <tr> 
                                    <td>House Price</td>
                                    <td>$<?php echo number_format($property->house_price, 0, ".", ","); ?></td>
                                </tr>
                                <?php endif; ?> 
                                <?php if($property->design != ""): ?>
                                <tr> 
                                    <td>Design</td>
                                    <td><?php echo $property->design; ?></td>
                                </tr>
                                <?php endif; ?>                                 
                                <?php if((is_numeric($property->frontage)) && ($property->frontage > 0)) : ?>
                                <tr> 
                                    <td>Frontage</td>
                                    <td><?php echo $property->frontage; ?> sqm</td>
                                </tr>
                                <?php endif; ?>  
                                <tr>
                                    <td>Study</td>
                                    <td><?php echo ($property->study == 1) ? "Yes" : "No"; ?></td>
                                </tr>
                                <?php if(($property->facade != "") && ($property->facade != "-1")) : ?>                                                                                                                                                                                                                        
	                            <tr>
			                        <td>Facade</td>
			                        <td><?php echo $property->facade; ?></td>
			                    </tr>  
                                <?php endif; ?>
                                <?php if((is_numeric($property->est_stampduty_on_purchase)) && ($property->est_stampduty_on_purchase > 0)) : ?>
			                    <tr>
			                        <td>Stamp duty Estimate</td>
			                        <td>$<?php echo number_format($property->est_stampduty_on_purchase, 0, ".", ","); ?></td>
			                    </tr> 
                                <?php endif; ?>
                                
                                <?php if((is_numeric($property->estimated_gov_transfer_fee)) && ($property->estimated_gov_transfer_fee > 0)) : ?>
			                    <tr>
			                        <td>Gov. Transfer Fee</td>
			                        <td>$<?php echo number_format($property->estimated_gov_transfer_fee, 0, ".", ","); ?></td>
			                    </tr> 
                                <?php endif; ?>
                                
                                <?php if((is_numeric($property->council_rates)) && ($property->council_rates > 0)) : ?>
                                <tr>
                                    <td>Council rates</td>
                                    <td>$<?php echo number_format($property->council_rates, 0, ".", ","); ?></td>
                                </tr> 
                                <?php endif; ?>     
                                <?php if((is_numeric($property->owner_corp)) && ($property->owner_corp > 0)) : ?>
                                <tr>
                                    <td>Owners Corp Fee</td>
                                    <td>$<?php echo number_format($property->owner_corp, 0, ".", ","); ?></td>
                                </tr> 
                                <?php endif; ?>  
                                <?php if(($property->other_fee_text != "") && (is_numeric($property->other_fee_amount)) && ($property->other_fee_amount > 0)) : ?>                                                         
			                    <tr>
			                        <td><?php echo $property->other_fee_text; ?></td>
			                        <td>$<?php echo $property->other_fee_amount; ?></td>
			                    </tr>
                                <?php endif; ?>
                                <?php if(($property->nras == 1) && ($property->nras_provider != "")) : ?>
			                    <tr>
			                        <td>NRAS Provider</td>
			                        <td><?php echo $property->nras_provider; ?></td>
			                    </tr>
                                <?php endif; ?>
                                <?php if(($property->nras == 1) && (is_numeric($property->nras_rent)) && ($property->nras_rent > 0)) : ?>                                
			                    <tr>
			                        <td>NRAS Discount</td>
			                        <td><?php echo $property->nras_rent; ?>%</td>
			                    </tr>
                                <?php endif; ?>
                                <?php if(($property->nras == 1) && ($property->nras_fee != "")) : ?>
			                    <tr>
			                        <td>NRAS Fee Summary</td>
			                        <td><?php echo $property->nras_fee; ?></td>
			                    </tr>
                                <?php endif; ?>
                                <?php if($property->special_features != "") : ?>        
                                <tr>
                                    <td>Special Features</td>
                                    <td>
                                    	<div class="justify">
                                    		<?php echo $property->special_features; ?>
                                    	</div>
                                	</td>
                                </tr> 
                                <?php endif; ?>                                
                                <?php if((in_array($user_type_id, array(USER_TYPE_ADVISOR))) && ($property->internal_comments != "")) : ?>
			                    <tr>
			                        <td>Internal Comments</td>
			                        <td>
			                        	<div class="justify">
			                        		<?php echo $property->internal_comments; ?>
			                        	</div>
		                        	</td>
			                    </tr>
                                <?php endif; ?> 
                                <?php if($property->misc_comments != "") : ?>        
                                <tr>
			                        <td>Misc Comments</td>
			                        <td>
			                        	<div class="justify">
			                        		<?php echo $property->misc_comments; ?>
			                        	</div>
		                        	</td>
			                    </tr> 
                                <?php endif; ?>
			                </table>
							<table>
						<tr>
							<td> Add Permission: </td>
							<td> <select id="user_id" name="user_id" style="width:187px;">
								<option> Select User: </option>
                                <?php if($number_of_users) : ?>
                                <?php 
									foreach($number_of_users->result() as $number_of_users)
									{
								?>		
								<option value="<?php echo $number_of_users->user_id; ?>"> <?php echo $number_of_users->first_name.' '.$number_of_users->last_name; ?> </option>
								<?php
									}
								?>
                                <?php endif; ?>	
							</select> 
							</td>
						</tr>
					</table>
			            </li>
			            
			            <li>
                            <?php if($docs) : ?>
			                <ul class="downloads">
                                <?php foreach($docs->result() as $doc) : ?>
                                    <?php if($doc->document_path != "") : ?>
                                    	<?php $utid = $this->session->userdata["user_type_id"]; ?>
                                    	<?php if ($utid == USER_TYPE_ADVISOR) : ?>
                                <li><a href="<?php echo base_url(); ?>stocklist/downloads/<?php echo $doc->document_path; ?>" target="_blank"><?php echo $doc->document_name; ?></a></li>
                                		<?php else : ?>
                                			<?php if ($doc->extra_data != 'advisors_only') : ?>
                        		<li><a href="<?php echo base_url() . $doc->document_path; ?>" target="_blank"><?php echo $doc->document_name; ?></a></li>
                        					<?php endif; ?>
                                		<?php endif; ?>
                                    <?php endif; ?>
                                <?php endforeach; ?>                                                                                                   
			                </ul>
							<!-- By Ajay TasksEveryday -->
						<a href="<?php echo base_url() . "projects/detail/" . $project->project_id ?>/1" id="project-dl">Click for PROJECT Downloads </a>
                           <!-- END -->
                            <?php endif; ?>
			            </li>
			        </ul>
			        
			        <!-- end sidebar --></div>                             
			<!-- end main content --></div>
            
            <?php if($user_type_id == USER_TYPE_ADVISOR) : ?>
            <div id="reserveModal" class="reveal-modal" style="width:620px;margin-left:-350px">
                 <h2>Reserve This Property</h2>
                 <p>To reserve this property, please fill in the form below..</p>
                 
                 <!-- Form to go here -->
                 <?php echo form_open('', 'class="block" id="reserveForm"', array('action'=>'submit_reserve_form','property_id'=>$property->property_id)) ;?>
                     <fieldset>
                        <label for="investor_id">Client:</label>
                        <?php echo form_dropdown_investors($user_id, 'investor_id', '', 'class="slb" id="investor_id"');?>
                        
                        <label for="first_name">First name <span class="required">*</span>:</label>
                        <input type="text" name="first_name" id="first_name" />
                        
                        <label for="middle_name">Middle name:</label>
                        <input type="text" name="middle_name" id="middle_name" />
                        
                        <label for="last_name">Last name <span class="required">*</span>:</label>
                        <input type="text" name="last_name" id="last_name" />
                        
                        <label for="company_name">Company name (Employer):</label>
                        <input type="text" name="company_name" id="company_name" />
                        
                        <label for="mobile">Mobile: <span class="required">*</span></label>
                        <input type="text" name="mobile" id="mobile" />
                        
                        <label for="phone">Work phone:</label>
                        <input type="text" name="phone" id="phone" />
                        
                        <label for="home_phone">Home phone:</label>
                        <input type="text" name="home_phone" id="home_phone" />
                        
                        <label for="fax">Fax:</label>
                        <input type="text" name="fax" id="fax" />
                        
                        <label for="email">Email (primary)<span class="required">*</span>:</label>
                        <input type="text" name="email" id="email" />
                        
                        <label for="secondary_email">Email (secondary):</label>
                        <input type="text" name="secondary_email" id="secondary_email" />
                        
                        <label><input type="checkbox" name="deposit_paid" id="deposit_paid" value="1" />  Deposit Paid</label>
                        
                     </fieldset>
                     
                     <fieldset style="margin-right:0;">
                        <label for="billing_address1">Address Line 1:</label>
                        <input type="text" id="billing_address1" name="billing_address1" />
                        
                        <label for="billing_address2">Address Line 2:</label>
                        <input type="text" id="billing_address2" name="billing_address2" />
                        
                        <label for="billing_suburb">Suburb:</label>
                        <input type="text" id="billing_suburb" name="billing_suburb" />
                        
                        <label for="billing_postcode">Postcode:</label>
                        <input type="text" id="billing_postcode" name="billing_postcode" />
                                
                        <label for="billing_state_id">State:</label>
                        <select class="required" id="billing_state_id" name="billing_state_id">
                            <option value="">Choose</option>
                            <?php echo $this->utilities->print_select_options($states, "state_id", "name", ''); ?>
                        </select>   
                                
                        <label for="billing_country_id">Country:</label>
                        <select name="billing_country_id" id="billing_country_id"> 
                            <option value="1">Australia</option>
                        </select>
                        
                        <label for="delivery_address1">Postal Address:</label>
                        <input type="text" name="delivery_address1" id="delivery_address1" />
                        
                        <label for="delivery_suburb">Delivery Suburb:</label>
                        <input type="text" name="delivery_suburb" id="delivery_suburb" />
                        
                        <label for="delivery_postcode">Delivery Postcode:</label>
                        <input type="text" name="delivery_postcode" id="delivery_postcode" />
                        
                        <label for="delivery_state_id">Delivery State:</label>
                        <select name="delivery_state_id" id="delivery_state_id">
                            <option value="">Choose</option>
                            <?php echo $this->utilities->print_select_options($states, "state_id", "name", ''); ?>
                        </select> 
                        
                        <label for="comments">Comments / Notes:</label>
                        
                        <textarea name="comments" id="comments" rows="4"></textarea>
                        
                     </fieldset>
                     
                     <div class="clear"></div>
                     <h3>Additional Contact</h3>
                        	<fieldset>                                         
                        		<label for="additional_contact_first_name">First Name</label>
                                <input type="text" name="additional_contact_first_name" value="" id="additional_contact_first_name" />
                                
                                <label for="additional_contact_middle_name">Middle Name</label>
                                <input type="text" name="additional_contact_middle_name" value="" id="additional_contact_middle_name" />
                                
                                <label for="additional_contact_last_name">Last Name</label>
                                <input type="text" name="additional_contact_last_name" value="" id="additional_contact_last_name" />                                
                                                                                    
                                <label for="additional_contact_relationships">Relationship</label>
                                <select name="additional_contact_relationships" id="additional_contact_relationships">
                                    <option value="">Choose</option>
                                    <?php echo $this->utilities->print_select_options_array($relationship_types, false, '');?>
                                </select>                                
                        	</fieldset>
                        	<fieldset>
                                <label for="additional_contact_mobile">Mobile</label>
                                <input type="text" name="additional_contact_mobile" value="" id="additional_contact_mobile" />
                                                            
                        		<label for="additional_contact_phone">Phone 2</label>
                                <input type="text" name="additional_contact_phone" value="" id="additional_contact_phone" />
                                
                                <label for="additional_contact_email">Email</label>
                                <input type="text" name="additional_contact_email" value="" id="additional_contact_email" />                                
                                
                                <label for="additional_contact_comment">Comment</label>
                                <input type="text" name="additional_contact_comment" value="" id="additional_contact_comment" />                                                                
                        	</fieldset>
                            <div class="clear"></div>
                            <h3>Legal Details</h3>
                            <label for="legal_purchase_entity">Full Legal Purchase Entity</label>
                            <textarea id="legal_purchase_entity" name="legal_purchase_entity" cols="30" rows="6" class="fullwidth"></textarea>                            
                            
                            <label for="purchase_comments" class="top-margin20">Purchase comments and ownership split</label>
                            <textarea id="purchase_comments" name="purchase_comments" cols="30" rows="6" class="fullwidth"></textarea>                                                        
                            
                            <div class="top-margin20"></div>
                            
                            <fieldset>
                                <label for="acn">ACN</label>
                                <input type="text" name="acn" value="" id="acn" />
                            </fieldset>                            
                            
                            <fieldset>
                                <label for="smsf_purchase">SMSF Purchase</label>
                                <input type="radio" id="smsf_purchase" class="yes_smsf" name="smsf_purchase" value="Yes"  /> Yes &nbsp; &nbsp;
                                <input type="radio" id="smsf_purchase" class="no_smsf" name="smsf_purchase" value="No"  /> No
                            </fieldset>                            

                 </form>
                 
                 <p><a class="btn inline" id="submitReserveForm" href="javascript:;">Yes, reserve this property</a>&nbsp;<a class="btn secondary inline" id="cancelReserveBtn" href="#">No, cancel</a></p>
                 <a class="close-reveal-modal">&#215;</a>
            </div>
            <?php endif; ?>
            
            <?php if (in_array($user_type_id, array(USER_TYPE_INVESTOR, USER_TYPE_LEAD, USER_TYPE_PARTNER)) AND isset($advisor) AND $advisor) : ?>
            <div id="reservationRequestModal" class="reveal-modal">
                <h2>Reservation Request</h2>
                <p><b>PLEASE ENSURE YOUR FULL LEGAL NAME INCLUDING MIDDLE NAME IS COMPLETED / UPDATED BELOW.</b></p>

                <?php echo form_open('', 'id="reservationRequestForm"', array('action'=>'submit_reservation_request','property_id'=>$property->property_id)) ;?>
                    <fieldset>
                        <legend>My Details</legend>
                        
                        <label for="rr_first_name">First Name <span class="required">*</span></label>
                        <input type="text" id="rr_first_name" name="first_name" value="<?=$user->first_name; ?>" class="required" />
                        
                        <label for="rr_middle_name">Middle Name</label>
                        <input type="text" id="middle_name" name="middle_name" value="" />                    

                        <label for="rr_last_name">Last Name <span class="required">*</span></label>
                        <input type="text" id="rr_last_name" name="last_name" value="<?=$user->last_name; ?>" class="required" />                    
                        
                        <label for="rr_mobile">Mobile</label>
                        <input type="text" id="rr_mobile" name="mobile" value="<?=$user->mobile; ?>" />
                        
                        <label for="rr_phone">Work Phone</label>
                        <input type="text" id="rr_phone" name="phone" value="<?=$user->phone; ?>" />                                        
                        
                        <label for="rr_home_phone">Home Phone</label>
                        <input type="text" id="rr_home_phone" name="home_phone" value="<?=$user->home_phone; ?>" />   
                    </fieldset>
                    
                    <fieldset>
                        <legend>Additional Contact</legend>
                        
                        <label for="rr_additional_contact_first_name">Additional Contact First Name</label>
                        <input type="text" id="rr_additional_contact_first_name" name="additional_contact_first_name" value="<?=$user->additional_contact_first_name; ?>" />                   
                        
                        <label for="rr_additional_contact_last_name">Additional Contact Last Name</label>
                        <input type="text" id="rr_additional_contact_last_name" name="additional_contact_last_name" value="<?=$user->additional_contact_last_name; ?>" />                                           

                        <label for="rr_additional_contact_relationships">Relationship to you <span class="required">*</span></label>
                        <input type="text" id="rr_additional_contact_relationships" name="additional_contact_relationships" value="<?=$user->additional_contact_relationships; ?>" />                    
                        
                        <label for="rr_additional_contact_phone">Additional Contact Phone</label>
                        <input type="text" id="rr_additional_contact_phone" name="additional_contact_phone" value="<?=$user->additional_contact_phone; ?>" />
                        
                        <label for="rr_additional_contact_mobile">Additional Contact Mobile</label>
                        <input type="text" id="rr_additional_contact_mobile" name="additional_contact_mobile" value="<?=$user->additional_contact_mobile; ?>" />                                           
                    </fieldset>                                                                             

                    <p>Send a reservation request to your advisor <em><?php echo trim("$advisor->first_name $advisor->last_name") ?></em> - are you sure?</p>

                    <p><a class="btn inline" id="submitReservationRequestForm" href="javascript:;">Yes, please</a>&nbsp;<a class="btn secondary inline" id="cancelReservationRequestBtn" href="#">No, cancel</a></p>
                    <a class="close-reveal-modal">&#215;</a>
                </form>
            </div>
            <?php endif; ?>
            
            <div id="addToFavouritesModal" class="reveal-modal">
                 <h2>Add To Favourites</h2>
                 <p>Are you sure you want to add to this property to favourites?</p>
                 <div class="error add_favourites_error"><h4>Please complete the following fields before submitting:</h4></div>
                 <?php echo form_open('', 'id="addToFavouritesForm"', array('action'=>'add_favourite','foreign_id'=>$property->property_id,'foreign_type'=>'property')) ;?>
                     <p><a class="btn inline" id="submitaddToFavourites" href="javascript:;">Yes, please</a>&nbsp;<a class="btn secondary inline close-reveal" id="canceladdToFavourites" href="#">No, cancel</a></p>
                     <a class="close-reveal-modal">&#215;</a>
                 </form>
            </div>
            
            <div id="propertyAdvisorModal" class="reveal-modal">
                <h2>Information for Advisors</h2>
                <h4>Total Commission: <?php echo !empty($property->total_commission) ? '$' . number_format($property->total_commission,2) : ''?></h4>
                <table cellspacing="1">
                    <tr>
                        <th style="width: 20%"></th>
                        <th style="width: 20%">Payment</th>
                        <th style="width: 20%">Percentage</th>
                        <th style="width: 40%">Payable</th>
                    </tr>
            <?php
                for ($i=1; $i<4; $i++) :
                    $paymentField = "stage{$i}_payment";
                    $percentageField = "stage{$i}_percentage";
                    $payableField = "stage{$i}_payable";
            ?>
                    <tr>
                        <th style="width: 20%">Stage <?php echo $i?></th>
                        <td style="width: 20%"><?php echo !empty($property->{$paymentField}) ? '$' . number_format($property->{$paymentField},2) : ''?></td>
                        <td style="width: 20%"><?php echo !empty($property->{$percentageField}) ? number_format($property->{$percentageField}, 2) . ' %' : ''?></td>
                        <td style="width: 40%"><?php echo !empty($property->{$payableField}) ? $property->{$payableField} : ''?></td>
                    </tr>
                <?php endfor; ?>
                </table>
                <?php if($property->advisor_comments != "") : ?>
                <p><?php echo $property->advisor_comments; ?></p>
                <?php endif; ?>
            <?php if ($property->status == 'reserved' AND $property->advisor_id == $user_id) : ?>
                <?php echo form_open('stocklist/ajax','id="commission_comments_form"',array('action'=>'save_comments','property_id'=>$property->property_id));?>
                    <div class="block">
                        <label>Commission sharing allocation comments:</label>
                        <textarea name="commission_comments" id="commission_comments" rows="3" style="width:100%"><?php echo $property->commission_comments?></textarea>
                        <label> Commission Sharing Partner:</label>
                        <?php echo form_dropdown_partners($user_id, 'commission_sharing_user_id', $property->commission_sharing_user_id, 'style="width:100%"' );?>
                        <label>Other Comments:</label>
                        <textarea name="advisor_comments_other" id="advisor_comments_other" rows="3" style="width:100%"><?php echo $property->advisor_comments_other?></textarea>
                        <input type="submit" value="Save Changes" class="button" style="float:none" />
                    </div>
                </form>
            <?php endif; ?>
                <a class="close-reveal-modal">&#215;</a>
            </div>
            
            <?php if($favourite) : ?>
            <div id="removeFromFavouritesModal" class="reveal-modal">
                 <h2>Remove From Favourites</h2>
                 <p>Are you sure you want to remove this property from your favourites?</p>
                 <div class="error remove_favourites_error"><h4>Please complete the following fields before submitting:</h4></div>
                 <?php echo form_open('', 'id="removeFromFavouritesForm"', array('action'=>'delete_favourite','favourite_id'=>$favourite->favourite_id,'foreign_type'=>'property')) ;?>
                     <p><a class="btn inline" id="submitRemoveFromFavourites" href="javascript:;">Yes, please</a>&nbsp;<a class="btn secondary inline close-reveal" id="canceladdToFavourites" href="#">No, cancel</a></p>
                     <a class="close-reveal-modal">&#215;</a>
                 </form>
            </div>
            <?php endif; ?>       


              <div id="preparedPrintModal" class="reveal-modal">
                 
                    <?php echo form_open("brochure/property/$property->property_id",'id="preparedPrintModalForm"',array('action'=>'print_brochure_property','property_id'=>$property->property_id));?>
                    <label for="prepared_for">Prepare for</label>
                    <select name="prepared_for" id="prepared_for">
                        <option value="">Choose</option>
                        <?php //echo $this->utilities->print_select_options($partners, "first_name,last_name", "first_name,last_name"); ?>
                        <?php echo $this->utilities->print_select_options($investors, "first_name,last_name", "first_name,last_name"); ?>
                        <?php echo $this->utilities->print_select_options($enquiries, "first_name,last_name", "first_name,last_name"); ?>
                    </select>
                    
                    <input type="text" id="prepared_for_manual" name="prepared_for_manual" style="display: none;"/>
                    <input type="checkbox" id="manual_type" name="manual_type"/> Other
                    
                    
                    <br />
                    <br />
                    <div class="clear"></div>
                    
                    <?php
                    if ($user_type_id != USER_TYPE_INVESTOR && $user_type_id != USER_TYPE_PARTNER) {
                    ?>
                    
                    <input type="checkbox" id="add_summary" name="add_summary"/> Add summary ?
                    
                    <label for="summary">Summary</label>
                    <select name="summary" id="summary" style="display: none;">
                        <option value="">Choose</option>
                        <?php echo $this->utilities->print_select_options($summaries, "summary_id", "title"); ?>
                        
                    </select>
                    
                    <?php
                    }
                    ?>
                    
                    <br />
                    <br />
                    <div class="clear"></div>
                    
                    <a href="javascript:;" class="btn arow green" id="print_brochure">Print Report</a>
                    
                    </form>
                 <a class="close-reveal-modal">&#215;</a>
            </div>            
