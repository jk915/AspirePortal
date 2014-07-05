<body class="dashboard">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">
                <h1>The Dashboard</h1>
                <div class="sidebar">
                	<?php if ($featured_properties) : ?>
                    <div class="block" style="height:225px;">
                    	<ul class="slide_featured_property">
            			<?php foreach ($featured_properties->result() AS $featured_property) : ?>
                    		<li>
		                        <h4><?php echo $featured_property->lot . ", " . $featured_property->address;?></h4>
	                        <?php if (!empty($featured_property->hero_image) && file_exists("property/" . $featured_property->property_id . "/images/" . $featured_property->hero_image)) : ?>
		                        <?php
									$src = "property/" . $featured_property->property_id . "/images/" . $featured_property->hero_image;
		                			$resized = image_resize($src, 196, 130);
								?>
								<a href="<?php echo base_url() . "stocklist/detail/" . $featured_property->property_id; ?>" title="<?php echo $featured_property->lot . ", " . $featured_property->address;?>">
									<img width="245" height="165" alt="<?php echo $featured_property->title;?>" src="<?php echo $resized;?>">	
								</a>
	                        <?php else : ?>
	                        	<a href="<?php echo base_url() . "stocklist/detail/" . $featured_property->property_id; ?>" title="<?php echo $featured_property->lot . ", " . $featured_property->address;?>">
		                        	<img width="245" height="165" alt="<?php echo $featured_property->title;?>" src="<?php echo site_url('images/member/default_hero_image.jpg')?>">
		                        </a>
	                        <?php endif; ?>
		                        <p style="padding-top:10px;"><a class="btn arrow" href="<?php echo base_url() . "stocklist/detail/" . $featured_property->property_id; ?>">view details</a></p>
                    		</li>
        				<?php endforeach; ?>
                    	</ul>
                    </div>
                    <?php endif; ?>
                    <!--
                    <div class="block">
                        <h3>Investor &amp; Partner Distribution</h3>
                        <img src="<?php echo base_url(); ?>images/member/temp-graph2.png" border="0" width="249" height="115" alt="temp-graph2.png (5,191 bytes)" />
                    </div>-->
                <!-- end sidebar --></div>                
                
                <div class="mainCol">
                <?php if (in_array($user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))) : ?>
                    <table cellpadding="0" cellspacing="0" class="listing">
                        <tr class="intro">
                            <td colspan="3">Recent Reservations</td>
                        </tr>
                        <tr>
                            <th class="sortable">Property</th>
                            <th class="sortable">Investor</th>
                            <th class="sortable">Date of Reservation</th>
                        </tr>
                <?php if ($recent_reservations) : ?>
                    <?php foreach ($recent_reservations->result() AS $reservedProperty) : ?>
                        <tr>
                            <td>
                                <a href="<?php echo site_url("stocklist/detail/$reservedProperty->property_id")?>">
                                    Lot <?php echo trim("$reservedProperty->lot, $reservedProperty->address, $reservedProperty->suburb")?>
                                </a>
                            </td>
                            <td>
                                <a href="<?php echo site_url("investors/detail/$reservedProperty->investor_id")?>">
                                    <?php echo trim("$reservedProperty->reserved_first_name $reservedProperty->reserved_last_name")?>
                                </a>
                            </td>
                            <td>
                                <?php echo date('d/m/Y', $reservedProperty->ts_reserved_date);?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
                    </table>
                <?php endif; ?>
                    
                    <table cellpadding="0" cellspacing="0" class="task_listing listing">
                        <tr class="intro">
                            <td colspan="3">Tasks</td>
                        </tr>
                        <tr>
                            <th class="sortable" sort="t.title">Task</th>
                            <th class="sortable" sort="u2.first_name">Assigned To</th>
                            <th class="sortable" sort="t.priority">Priority</th>
                            <th class="sortable" sort="t.due_date">Due Date</th>
                        </tr>
                        <?php if($tasks): ?>
                        <?php 
                        	foreach($tasks->result() as $task) :
                        		switch ($task->user_type_id) {
                        			case 3:
                        				// Advisor
                    					$url_user_details = site_url('advisors/detail/'.$task->assign_to);
                    				break;
                        			
                    				case 5:
                    					// Partner
                    					$url_user_details = site_url('partners/detail/'.$task->assign_to);
                    				break;
                    				
                    				case 6:
                    					// Investor
                    					$url_user_details = site_url('investor/detail/'.$task->assign_to);
                    				break;
                    				
                    				case 7:
                    					// Lead
                    					$url_user_details = site_url('leads/detail/'.$task->assign_to);
                    				break;
                    				
                        			default:
                    					$url_user_details = site_url();
                    				break;
                        		}
                    	?>
                        <tr>
                            <td><a href="<?php echo base_url(); ?>tasks/index#<?php echo $task->task_id; ?>"><?php echo $task->title; ?></a></td>
                            <!--<td><a href="<?php echo $url_user_details;?>"><?php echo $task->assign_client_name; ?></a></td>-->
                            <td><?php echo $task->assign_client_name; ?></td>
                            <td><?php echo ucfirst($task->priority); ?></td>
                            <td><?php echo $this->utilities->iso_to_ukdate($task->due_date); ?></td>
                        </tr>                        
                        <?php endforeach; ?>
                        <?php endif; ?>                                                                       
                    </table>
                    <?php if($tasks): ?>
                    <p><a href="<?php echo base_url(); ?>tasks" class="btn arrow">view more</a></p>                    
                    <?php else: ?>
                    <p>You currently don't have any tasks. Perhaps you'd like to add one?</p>
                    <p><a href="<?php echo base_url(); ?>tasks/index#add" class="btn arrow">add task</a></p>                    
                    <?php endif; ?>
                    <?php echo form_open('media/ajax', array("id" => "frmLoadTask", "name" => "frmLoadTask")); ?>
                    	<input type="hidden" id="sort_col" name="sort_col" value="" />
                        <input type="hidden" id="sort_dir" name="sort_dir" value="" />
                    </form>
                </div>                
                <!-- end main content --></div>