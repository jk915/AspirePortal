<body id="product" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <? $this->load->view("admin/product/navigation"); ?>
            <p><?php echo $message?></p>    
              
                <form id="frmProduct" method="post" action="<?=base_url();?>productmanager/product/<? echo $product_id;?>">
                	<input type="hidden" name="product_id"  id="product_id" value="<? echo $product_id;?>" />
                    <input type="hidden" name="id" value="<?php echo $product_id;?>" />
                    <?php echo $product_category_id->build_item(); ?>
                    
                    <h2>Product Properties</h2> 
                    <?php if(isset($product_id) && $product_id!= ""){ ?>   
                    <ul class="css-tabs skin2">
                        <li><a href="#">General</a></li>
                        <li><a href="#">Description</a></li>
                    	<li><a href="#">Images</a></li>
                    	<li><a href="#">Pricing</a></li>
                    	<li><a href="#">Downloads</a></li>
                    </ul>   
                    
                    <div class="css-panes skin2">
                    
                        <div style="display:block">
                            <?php
                            }
                            ?>
                            <div class="left" style="width: 400px;">
	                            <div class="left">
                    		        <label for="product_name"><?php echo $product_name->get_caption(); ?></label>
                     		        <?php $product_name->build_item();?>
	                            </div>
	                            <div class="clear"></div>

	                            <label for="category_id">Product Category:</label>
	                            <select id="category_id" name="product_category_id" class="required ">
	                                <option value="">Choose</option>

	                                <?php
                                		if(isset($selected_category_id))
                                			echo $this->utilities->print_select_product_tree($categories, $selected_category_id);
                                		else
                                			echo $this->utilities->print_select_product_tree($categories, $category_id);
	                                ?>
	                             </select>
	                            <div class="clear"></div>
	                            
	                            <div class="left">
                    		        <label for="model_number"><?php echo $model_number->get_caption();?></label>
	                                <?php $model_number->build_item();?>
	                            </div>
	                            <div class="clear"></div>

	                            <div class="left">
                    		        <label for="rrp_price"><?php echo $rrp_price->get_caption();?></label>
	                                <?php $rrp_price->build_item();?>
	                            </div>
	                            <div class="clear"></div>
	                            
	                            <label for="require_shipping">
                    	    		<?php $require_shipping->build_item();?>
                            		<?php echo $require_shipping->get_caption();?>
	                            </label>
	                            <div class="clear"></div>
	                            
	                            <div id="product_sizes" <?php echo $display_sizes; ?>>
		                            <div class="left">
	                    		        <label for="weight"><?php echo $weight->get_caption();?></label>
		                                <?php $weight->build_item();?>
		                            </div>
		                            <div class="left left-margin20">
	                    		        <label for="height"><?php echo $height->get_caption();?></label>
		                                <?php $height->build_item();?>
		                            </div>
		                            <div class="clear"></div>
		                            
		                             <div class="left">
	                    		        <label for="width"><?php echo $width->get_caption();?></label>
		                                <?php $width->build_item();?>
		                            </div>
		                            <div class="left left-margin20 ">
	                    		        <label for="length"><?php echo $length->get_caption();?></label>
		                                <?php $length->build_item();?>
		                            </div>
		                            <div class="clear"></div>
	                            </div>
	                            
								<label for="article_category_id">Linked Article Category (for product overview)</label>
								<select id="article_category_id" name="article_category_id">
									<option value="">None</option>
									<?php
									if($product_id != "")
									{
										echo $this->utilities->print_select_category_tree($article_categories, $article_cat_id);
									}
									else
										echo $this->utilities->print_select_category_tree($article_categories,0);                          
									?>
								</select>
	                            <input type="button" id="view_category" value="View" class="button" />                                                         
	                            <div class="clear"></div>
	                            
	                            <div class="left">
                    		        <label for="tags"><?php echo $tags->get_caption(); ?></label>
                     		        <?php $tags->build_item();?>
	                            </div>
	                            <div class="clear"></div>
	                            
	                            
	                            <!-- 
	                            <div class="left">
                    		        <label for="price"><?php //echo $price->get_caption();?></label>
	                                <?php //$price->build_item();?>
	                            </div>
	                             
	                            
	                            <div class="clear"></div>

	                             
	                            <label for="image_only">
                    		    <?php //$image_only->build_item();?>
	                            <?php //echo $image_only->get_caption();?></label>                                                       
	                            
	                            <div class="clear"></div>
	                            -->                            
	                            <label for="serial_gen">Serial generator</label>
	                            <?php echo form_dropdown("serial_gen",$this->tools_model->get_serial_generators(), (isset($product_details['serial_gen'])) ? $product_details['serial_gen'] : "", "id='serial_gen' class='required'"); ?>
								<input type="button" id="test_serial_gen" value="TEST" class="button" />
	                            <div class="clear"></div>
	                            
	                            <div id="sg_test_form" style="display:none">
	                                <div class="left">
	                                    <label for="device_id"><?php echo $device_id->get_caption(); ?></label>
	                                    <?php $device_id->build_item();?>                                
		                                <input type="button" id="submit_test_serial_gen" value="SUBMIT" class="button" />
	                 			    </div>
	                 			    <div id="serial_number">
	                 			        <label>&nbsp;</label>	
	                 			    </div>
	                 			    
	                 			    
	                     		    <div class="clear"></div>
	                            </div>
	                            
	                            <div class="left">
	                            
	                                <label for="active">
                    		        <?php $active->build_item();?>
	                                <?php echo $active->get_caption();?></label>
	                            </div>
	                            <div class="left left-margin20">    
	                            
	                                <label for="show_on_downloads">
	                                <?php $show_on_downloads->build_item();?>
	                                <?php echo $show_on_downloads->get_caption();?></label>
	                            </div>
	                            <div class="clear"></div> 
	                        </div>  
	                        <div class="left" style="margin-left: 20px; width: 250px;">
								<h2>Hide Frontend Tabs</h2>
								
	                            <label for="hidetab_screenshots">
                    		    	<?php $hidetab_screenshots->build_item();?>
	                            	<?php echo $hidetab_screenshots->get_caption();?>
	                            </label>	
	                            
	                            <label for="hidetab_oem">
                    		    	<?php $hidetab_oem->build_item();?>
	                            	<?php echo $hidetab_oem->get_caption();?>
	                            </label>
	                            
	                            <label for="hidetab_downloads">
                    		    	<?php $hidetab_downloads->build_item();?>
	                            	<?php echo $hidetab_downloads->get_caption();?>
	                            </label>	                            	                            								                        	
	                        </div>
	                        <div class="clear"></div>  
                            <?php
                            if(isset($product_id) && $product_id!= "")
                            {
                            ?>          
                        </div><!-- END first tab -->    
                        
                        <div style="display:block">
                             
                            <label for="short_description"><?php echo $short_description->get_caption();?></label>
                            <?php echo $short_description->build_item();?>
                            
                            <label for="description" class="top-margin"><?php echo $description->get_caption();?></label>
                            <?php echo $description->build_item();?>
                            
                            <label for="oem_description" class="top-margin"><?php echo $oem_description->get_caption();?></label>
                            <?php echo $oem_description->build_item();?>                        	
                        	                          
                        
                        </div><!-- END second tab -->            
                      
                      
                        <div>
                            <div class="left">
                                <!-- <label>Images for <?php //echo ($product_id !="" ) ? $productName : "New Product"; ?></label><br/>  -->
                                <label>Hero Image</label>                
                            </div>
                            <div class="right" style="padding-right:10px">
                                 <input type="file" name="upload_file" id="product_upload_file" />    
                                 
                            </div>
                            <div class="clear"></div>
                            
                            <br/>
                            <?php $hero_image->build_item(); ?>
                            <div id="images_listing">
                                <div id="page_listing">
                                    <?php $this->load->view('admin/product/file_listing'); ?>
                                </div>
                                 
                                <div class="clear"></div>            
                                <div id="controls">
                                    <div class="right">
                                        <input class="button" type="button" value="Delete Selected Files" id="delete_product_files" />
                                    </div>                
                                </div>    
                                <div class="clear"></div>
                            </div>
                            
                            <br/>
                            <div class="left">
                                <label>Gallery</label><br/>                
                            </div>
                            <div class="right" style="padding-right:10px">
                                 <input type="file" name="upload_file" id="product_upload_gallery_file" />
                            </div>
                            <div class="clear"></div>
                            
                            <div id="gallery_listing">
                                <div id="photo_listing">
                                    <?php $this->load->view('admin/product/gallery_listing'); ?>
                                </div>
                                 
                                <div class="clear"></div>            
                                <div id="controls">
                                    <div class="right">
                                        <input class="button" type="button" value="Delete Selected Files" id="delete_gallery_files" />
                                    </div>                
                                </div>    
                                <div class="clear"></div>
                            </div>           
                             
                        </div><!-- END third tab -->
                        
                 </form>
                        
                        <!-- start Pricing tab -->
                        <div>
                        
                        	<label for="pricing_description"><?php echo $pricing_description->get_caption();?></label>
                            <?php echo $pricing_description->build_item();?>
                            <br/>
                        	<div id="bracket_listing" class="left">
                        		<?php $this->load->view('admin/product/price_listing'); ?>
                        	</div>
                        	
                        	<fieldset id="bracket_fieldset" class="left">
                        		<legend style="font-size: 12px;">Add a new bracket</legend>
                        		
                        		<form id="frm_Bracket" action="" method="post">
	                        		<label for="access_level_id">User Group:</label> 
								   	<select id="access_level_id" class="required">
										<?php print $this->utilities->print_select_options( $broadcast_access_levels_to, "broadcast_access_level_id", "level" ); ?>
									</select>
								   	<br />
								   	
								   	<label for="bracket_max">Bracket Minimum:</label>
								   	<input type="text" id="bracket_max" value="" class="required" />
								   	<br/>
								   	
								   	<label for="price" >Item Price:</label>
								   	<input type="text" id="price" value="" class="required" />
								   	<br/>
								   	
								   	<label></label>
								   	<input type="button" id="btn_add_bracket" value="Add Bracket"/>
							   	</form>
                        	</fieldset>
                        	<div class="clear"></div>
                        </div>
                        <!-- end Pricing tab -->
                        
                        <!-- Downloads tab -->
                        <div>
                        
                            <div class="left">
                    	        <label for="download_caption"><?php echo $download_caption->get_caption();?></label>
                                <?php $download_caption->build_item();?>
                            </div>                        
                            
                            <div class="left">
                    	        <label for="download_link"><?php echo $download_link->get_caption();?></label>
                                <?php $download_link->build_item();?>
                            </div>
                            <div class="clear"></div>
                            
                            <div class="left">
                    	        <label for="download_text"><?php echo $download_text->get_caption();?></label>
                                <?php $download_text->build_item();?>
                            </div>
                            <div class="clear"></div>
                                                    
                            <hr />
                        
                        	<div class="right" style="padding-right:10px">
                                 <input type="file" name="upload_file" id="product_upload_downloads" />    
                            </div>
                            <div class="clear"></div>
                            <br/>
                            
                            <div id="files_listing">
                            	<?php $this->load->view( 'admin/product/download_listing' ); ?>
                            </div>
                            
                            <div class="clear"></div>            
                            <div id="controls">
                                <div class="right">
                                    <input class="button" type="button" value="Delete Selected Files" id="delete_download_files" />
                                </div>                
                            </div>    
                            <div class="clear"></div>
                        </div><!-- End downloads tab -->
                                                    
                    </div>
                    <?php
                    }
                    ?>
    
                    <br/>
                    <input type="button" id="btnUpdateProduct" value="Save Product" class="button" />
                    <br/>
				 
            <?php $this->load->view("admin/product/navigation"); ?>           
        
