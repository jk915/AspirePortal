            <?php
                $utid = $this->session->userdata["user_type_id"];
                $first_name = $this->session->userdata["first_name"];
                $last_name = $this->session->userdata["last_name"];
                $company = $this->session->userdata["company"];
                $logo = $this->session->userdata["logo"];
                $advisor_first_name = $this->session->userdata["advisor_first_name"];
                $advisor_last_name = $this->session->userdata["advisor_last_name"];
                $advisor_email = $this->session->userdata["advisor_email"];
                $advisor_phone = $this->session->userdata["advisor_phone"];
                $advisor_logo = $this->session->userdata["advisor_logo"];
                $pro_sub_menu = $this->session->userdata["sub_menu"];                 
				
                $logo_click_url = ($utid == USER_TYPE_SUPPLIER) ? "stocklist" : "dashboard";
				
                /*
				$dashboard_info = $this->article_category_model->get_list(CATEGORY_IMPORTANT_INFO, $limit = "", $page_no = "", $enabled = -1, $order_by = "category_id ASC");
			    */
            ?>
            <div id="header">
                <div class="content">
                    <a href="<?php echo base_url() . $logo_click_url; ?>">
                	<?php if(in_array($utid, array(USER_TYPE_ADVISOR))) : ?>
                		<?php if (!empty($logo)) : ?>
                    	<img id="logo" src="<?php echo site_url($logo)?>" border="0" height="115" alt="Back to the home page." />
                		<?php else : ?>
                		<img id="logo" src="<?php echo base_url(); ?>images/member/aspirenetwork_logo_white.jpg" border="0" width="473" height="115" alt="Back to the home page." />
                		<?php endif; ?>
                	<?php else : ?>
                		<?php if( !empty($advisor_logo)) : ?>
                    	<img id="logo" src="<?php echo site_url($advisor_logo)?>" border="0" height="115" alt="Back to the home page." />
                    	<?php else : ?>
                    	<img id="logo" src="<?php echo base_url(); ?>images/member/aspirenetwork_logo_white.jpg" border="0" width="473" height="115" alt="Back to the home page." />
                    	<?php endif; ?>
                	<?php endif; ?>
                	</a>
                    <?php
                        // Define navigation options and permissions.
                        // Format for each item is: Menu caption, controller, allowed user types array, developers only
                        $nav = array();
                        $nav[] = array("Dashboard", "dashboard", array(USER_TYPE_ADMIN, USER_TYPE_STAFF, USER_TYPE_ADVISOR, USER_TYPE_PARTNER, USER_TYPE_INVESTOR, USER_TYPE_LEAD), false); 
                        $nav[] = array("Enquiry", "leads", array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER), false);
                        
                        $nav[] = array("Partners", "partners", array(USER_TYPE_ADVISOR), false);
                        //$nav[] = array("Projects", "projects", false, false);
                         
                        // Suppliers see only the stocklist option - not the new/featured etc.
                        if($utid == USER_TYPE_SUPPLIER) {
                            $nav[] = array("Stocklist", "stocklist", false, false);                            
                        } else {
                            $nav[] = array("Properties", "stocklist/index/featured", false, false);
                        }
                        
                        $nav[] = array("My Properties", "myproperties", array(USER_TYPE_ADMIN, USER_TYPE_STAFF, USER_TYPE_ADVISOR, USER_TYPE_PARTNER, USER_TYPE_INVESTOR, USER_TYPE_LEAD), false);
                        $nav[] = array("Resource Centre", "media", array(USER_TYPE_ADMIN, USER_TYPE_STAFF, USER_TYPE_ADVISOR, USER_TYPE_PARTNER, USER_TYPE_INVESTOR, USER_TYPE_LEAD), false);
						
                        $nav[] = array("Advisors", "advisors", array(USER_TYPE_ADVISOR), false);
                    
                        if($utid == USER_TYPE_ADVISOR) {
                            $nav[] = array("Summaries", "summaries", false, false);                            
                        }
                    ?>

                    <div id="loggedIn" <?php echo (!in_array($utid, array(USER_TYPE_ADVISOR))) ? 'style="padding-top:41px;"' : ''?>>
                        <p><?=$first_name . " " . $last_name . (!empty($company) ? " of $company" : '') ; ?></p>
                        <ul>
                            <li><a class="link" href="<?php echo base_url(); ?>account/detail">Update Profile</a></li>
                            <li><a class="link" href="<?php echo base_url(); ?>login/logout">Logout</a></li>
                            <li><a class="link" href="#" id="btnSupport">Support</a></li>
                        </ul>
                    	<?php if(!in_array($utid, array(USER_TYPE_ADVISOR)) && !empty($advisor_email)) : ?>
                        <p class="linkmailto">Advisor: <a href="mailto:<?php echo $advisor_email;?>"><?php echo trim("$advisor_first_name $advisor_last_name"); ?></a>, <?php echo $advisor_phone;?></p>
                    	<?php endif; ?>
                    </div>  
                    
                    <?php echo form_open('postback/get_support_form', array("id" => "frmGetSupport")) ;?> 
                    </form>             
                <!-- end header content --></div>

                <div id="main_menu">
                    <ul id="nav">
                    <?php
                        foreach($nav as $item)
                        {
                            $caption = $item[0];
                            $controller = $item[1];
                            $permissions = $item[2];
                            $is_developer = $item[3];
                            
                            // Check permissions
                            if((is_array($permissions)) && (!in_array($utid, $permissions))) continue;
                            
                            // Check if this option should only be shown to a developer
                            if(($is_developer) && (!is_developer())) continue;   
                            
                            ?>
                                <?php if($caption == "Properties"):?>
                                    <li><a id="nav_<?=$controller;?>" href="<?php echo base_url(); ?><?=$controller;?>"><?=$caption;?></a>
                                        <ul>
                                            <?php foreach ($pro_sub_menu as $item):?>
                                            <?php if(($utid != USER_TYPE_SUPPLIER) || (($utid == USER_TYPE_SUPPLIER && trim($item['title']) != 'Print options' && trim($item['title']) != 'Projects'))) : ?>
                                            <li><a href="<?=$item['link']!=''? base_url() . $item['link']:'#' ?>" class="<?=$item['class']!=''?$item['class']:'' ?>"><?=$item['title']?></a></li>
                                            <?php endif; ?>
                                            <?php endforeach;?>
                                        </ul>                                    
                                    </li>
									<?php elseif($caption == "Dashboard"):?>
                                    <li><a id="nav_<?=$controller;?>" href="<?php echo base_url(); ?><?=$controller;?>"><?=$caption;?></a>
                                        <ul>
                                            <li><a href="<?php echo base_url("announcements"); ?>">Important Information</a></li>
                                            <li><a href="<?php echo base_url("tasks"); ?>">Tasks</a></li>
                                            <!--<li><a href="<?php echo base_url("contacts"); ?>">Contacts</a></li>-->
                                        </ul>                                    
                                    </li> 
									
									<?php elseif($caption == "Enquiry"):?>
                                    <li><a id="nav_<?=$controller;?>" href="<?php echo base_url(); ?><?=$controller;?>"><?=$caption;?></a>
                                        <ul>
                                            <li><a href="<?=site_url("/investors"); ?>">Investors</a></li>
										</ul>                                    
                                    </li>
									

                                <?php else:?>
                                    <li><a id="nav_<?=$controller;?>" href="<?php echo base_url(); ?><?=$controller;?>"><?=$caption;?></a></li>
                                <?php endif; ?>
                            <?php 
                        }
                    ?>                   
                    </ul>     
                </div>    
<!-- end header --></div>                                            