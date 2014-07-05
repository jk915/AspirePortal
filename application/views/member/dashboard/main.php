<body class="dashboard">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">
                <h1>The Dashboard</h1>
				<div style="float:left;width:240px;">
                <div class="sidebar" id="slide">
                	<?php if ($featured_properties) : ?>
                    <div class="block" style="height:225px;">
                    	<ul class="slide_featured_property">
						<?php foreach ($featured_properties->result() AS $featured_property) : ?>
                    		<li>
		                        <h4><?php echo $featured_property->area_name . ", " . $featured_property->state_name;?></h4>
	                        <?php if (!empty($featured_property->hero_image) && file_exists("property/" . $featured_property->property_id . "/images/" . $featured_property->hero_image)) : ?>
		                        <?php
									$src = "property/" . $featured_property->property_id . "/images/" . $featured_property->hero_image;
		                			$resized = image_resize($src, 196, 130);
								?>
								<a href="<?php echo base_url() . "stocklist/detail/" . $featured_property->property_id; ?>" title="<?php echo $featured_property->area_name . ", " . $featured_property->state_name;?>">
									<img width="245" height="165" alt="<?php echo $featured_property->title;?>" src="<?php echo $resized;?>">	
								</a>
	                        <?php else : ?>
	                        	<a href="<?php echo base_url() . "stocklist/detail/" . $featured_property->property_id; ?>" title="<?php echo $featured_property->area_name . ", " . $featured_property->state_name;?>">
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

				
				<!-- By Mayur - TasksEveryday -->
				
					<div class="sidebar" id="slide1">
                	<?php if ($article_data) : ?>
												
					
                    <div class="block" style="height:225px;">
                    	<ul class="slide_featured_property">
						
						<?php foreach ($article_data->result() AS $article_property) : ?>

							<?php 
							
								$class = "download";
								$href = $article_property->article_id;
								$external = false;
							?>

							<li>
		                        <h4><?php echo substr($article_property->article_title,0,28); ?></h4>
	                        <?php if (!empty($article_property->hero_image) && file_exists( $article_property->hero_image)) : ?>
		                        <?php
									$src = $article_property->hero_image;
		                			$resized = image_resize($src, 196, 130);
								?>
								<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>
									<img width="245" height="165" alt="<?php echo substr($article_property->article_title,0,28);?>" src="<?php echo $resized;?>">	
								</a>
	                        <?php else : ?>
	                        	<a class="<?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>
		                        	<img width="245" height="165" alt="<?php echo substr($article_property->article_title,0,28);?>" src="<?php echo site_url('images/member/default_hero_image.jpg')?>">
		                        </a>
	                        <?php endif; ?>
		                        <p style="padding-top:10px;"><a class="btn arrow <?php echo $class; ?>" href="<?php echo $href; ?>" <?php if($external) echo 'target="_blank"'; ?>>view details</a></p>
                    		</li>
        				<?php endforeach; ?>
                    	</ul>
                    </div>
                    <?php endif; ?>
					
                    <?php if (in_array($user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))):?>
					<div class="user_report">

						Number of Enquiry : <?php echo $user_details->number_of_leads; ?> <br/>
						Number of Investors : <?php echo $user_details->number_of_investors; ?><br/>
						Number of Partners : <?php echo $user_details->number_of_partners; ?><br/>
						Number of Completed Purchases : <?php echo $user_details->number_of_sales; ?>

					</div>
					<?php endif; ?>
                    </div>  <!-- end sidebar -->
				</div>
				
				<!-- By Mayur - TasksEveryday -->
					
                
                <div class="mainCol">
                
				<!-- By Mayur - TasksEveryday -->
				
				<table cellpadding="0" cellspacing="0" class="listing articlelisting announcementlist">
                        <thead>
                            <tr class="intro">
                                <td colspan="5">Important Information</td>
                            </tr>
                            <tr>
                                <th class="sortable" sort="a.article_date">Date</th>
                                <th class="sortable" sort="a.article_title" style="text-align:center">Title</th>
								<th class="sortable" sort="a.article_title" style="text-align:center">Author</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table> 
				
					<?php echo form_open('announcements/ajax', array("id" => "frmAnnouncements", "name" => "frmAnnouncements")); ?>
                        <input type="hidden" id="announcements_sort_col" name="sort_col" value="a.article_date" />
                        <input type="hidden" id="announcements_sort_dir" name="sort_dir" value="DESC" />    
                        <input type="hidden" id="announcements_count_all" name="count_all" value="0" />  
                        <input type="hidden" id="announcements_items_per_page" name="items_per_page" value="<?php echo MEDIA_PER_PAGE; ?>" />
                        <input type="hidden" id="announcements_current_page" name="current_page" value="1" />
                        <input type="hidden" id="announcements_action" name="action" value="load_media" />                                        
                    </form>
				
				<?php if (in_array($user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))) : ?>
				<table cellpadding="0" cellspacing="0" class="leads_listing listing">
                        <tr class="intro">
                            <td colspan="3">Active Enquiry</td>
                        </tr>
                        <tr>
                            <th class="sortable" sort="u.first_name">Contact</th>
                            <th class="sortable" sort="u.mobile">Mobile</th>
                            <th class="sortable" sort="u.created_dtm">Days Old</th>
                            <th class="sortable" sort="notes_last_created">Date Last Note</th>
							<th class="sortable" sort="days_since_login">Last Login</th>
                        </tr>
                        
                        <?php if($leads) : ?>
						<?php foreach($leads->result() as $user) : ?>
							<tr>
								<td><a href="<?php echo base_url() . "leads/detail/" . $user->user_id; ?>"><?php echo $user->first_name . " " . $user->last_name; ?></a></td>
								<td><?php echo $user->mobile; ?></td>
								<td style="text-align:center;"><?php echo get_days($user->created_dtm); ?></td>
								<td><?php echo ($user->notes_last_created != '')? date('d/m/Y',strtotime($user->notes_last_created)):""; ?></td>
								<td><?php echo format_login_days($user->days_since_login); ?></td>
							</tr>
						<?php endforeach; ?>

                        
                        <?php endif; ?>                                                                       
                    </table>
                   
                    <div style="float: right;">
                    <div style="float:left;padding-right:5px">  
                   <a href="<?php echo base_url(); ?>leads/detail" class="btn arrow">add new enquiry</a></div>
                   
                    <div style="float:left;">
                    <a href="<?php echo base_url(); ?>leads" class="btn arrow">view more</a>   
                    </div>
                                   
                   <div style="clear:left;"></div>
</div>                                
                   
                    <?php echo form_open('media/ajax', array("id" => "frmLoadTask", "name" => "frmLoadTask")); ?>
                    	<input type="hidden" id="sort_col" name="sort_col" value="" />
                        <input type="hidden" id="sort_dir" name="sort_dir" value="" />
                    </form>
				<?php endif; ?>	
				
				<!-- By Mayur - TasksEveryday  -->
				
				
				
				<?php if (in_array($user_type_id, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER))) : ?>
                    <table cellpadding="0" cellspacing="0" class="reservation_listing listing">
                        <tr class="intro">
                            <td colspan="3">Recent Reservations</td>
                        </tr>
                        <tr>
                            <th class="sortable" sort="p.address">Property</th>
                            <th class="sortable" sort="p.reserved_first_name">Investor</th>
                            <th class="sortable" sort="ts_reserved_date">Date of Reservation</th>
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
					
					<div style="float:right;">
                    <a href="<?php echo base_url(); ?>myproperties" class="btn arrow">view more</a>   
                    </div>
					
					
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
                    
                    <?php echo form_open('media/ajax', array("id" => "frmDownload", "name" => "frmDownload")); ?>
                        <input type="hidden" id="download_action" name="action" value="download_media" />
                        <input type="hidden" id="download_article_id" name="article_id" value="" />
                    </form>
                </div>                
                <!-- end main content -->
                </div>
                
                <div id="article_modal" class="reveal-modal">
                <a class="close-reveal-modal">&#215;</a>
                <div></div>
            </div>