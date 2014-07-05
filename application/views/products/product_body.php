<?php
	$style = '';
	
	if( !empty($product->hero_image) )
	{
		$image = $this->document_model->get_details($product->hero_image);
		
		if($image && file_exists( FCPATH.$image->document_path ) )
		{
			$style = 'style ="background: url(\''.base_url().$image->document_path.'\') top left no-repeat;  min-height: 60px;"';			
		}
	}
	
	// If the products RRP price is 0 AND there are no prices found from the access levels, hide the prices tab.
	$showPricingTab = true;
	
	if(($product->rrp_price == 0) && (!$prices))
		$showPricingTab = false;          	 
?>   
        	<div class="productIcon" <?php echo $style; ?>>
              <h1><?php echo $product->product_name; ?></h1>
              <h2><?php echo str_replace("</p>","",str_replace("<p>", "", $product->description )); ?></h2>
            </div>  
        
            <div id="tabs">
               <ul>
                  <li><a href="#overview">overview</a></li>
                  <?php if(!$product->hidetab_screenshots) : ?>
                  <li><a href="#screenshots">screenshots</a></li>
                  <?php endif; ?>
                  <?php if($showPricingTab) : ?>
                  <li><a href="#pricing">pricing</a></li>   
                  <?php endif; ?>
                  <?php if(!$product->hidetab_oem) : ?>
                  <li><a href="#oem">OEM service</a></li>                
                  <?php endif; ?>
                  <?php if(!$product->hidetab_downloads) : ?>
                  <li><a href="#downloads">downloads</a></li>                 
                  <?php endif; ?>
                    
               </ul>
               
               <!-- overview -->               
               <div id="overview">
                  
                  <?php
                  	if($articles)
                  	{	
                  		foreach ( $articles->result() as $article )
                  		{
                  		    $style_div =( !empty($article->hero_image) && file_exists( FCPATH.$article->hero_image ) ) ?
                                          ' style=" background-image:url(\''.base_url().$article->hero_image.'\');"' :
                                          '';
                        ?>
                  			<div class="icon" <?php echo $style_div; ?>>
                  				<h3><?php echo $article->article_title; ?></h3>
                  				<?php echo $article->short_description; ?>
                  				<?php if(strlen($article->content) > 20) : ?>
                  				<p>
                  					<a class="arrow" href="<?php echo base_url()."articles/products/".$article->article_code; ?>">more info</a>
                  				</p>				
                  				<?php endif; ?>
                  			</div>
                  			
                        <?php
                  		} 
                  ?>
                  <?php 
                  	} 
                  	else 
                  ?>
                  
                  	<?php
                  		if( !empty( $product->download_link ) )
                  		{
                  	?>
                  			<a class="left btn downloadBtn" href="<?php echo $product->download_link; ?>" target="_blank">&nbsp;</a>
                  	<?php 
                  		}
                  	?>
                  	
                  	<?php
                  		if( !empty( $product->download_text ) )
                  		{
                  	?>
                  			<p class="subtle downloadtext"><?php echo $product->download_text; ?></p>
                  	<?php 
                  		}
                  	?> 
                    <div class="clear"></div>
                    <input type="button" class="button" value="Buy Now" id="buy_now" />          
                  
                  
                <!-- end tabs-1 --></div>
               
               <!-- gallery -->  
               <?php if(!$product->hidetab_screenshots) : ?>             
               <div id="screenshots">
				<?php
               		if( $gallery_files )
           			{
                  		?>
               <div id="loading"></div>
               		     
					<div id="thumbs">
                 		<a class="pageLink prev" href="#" title="Previous Page">&nbsp;</a>
                 		<ul class="thumbs"> 
                 		<?php
                 		foreach ( $gallery_files->result() as $file )
                       	{
                       		$large = base_url().$file->document_path;
                        			
                        	$position = strripos( $file->document_path, '/' );
                        	$file_name = substr( $file->document_path, ($position+1) );
                        	$file_path = PRODUCT_FILES_FOLDER.$product_id;
                        			 
                        	$small = base_url().$file_path.'/'.THUMB_SMALL_PREFIX.$file_name;
                        	$large = base_url().$file_path.'/'.THUMB_LARGE_PREFIX.$file_name;;
                        			
                        	if( file_exists( FCPATH.$file->document_path ) && file_exists( FCPATH.$file_path.'/'.THUMB_SMALL_PREFIX.$file_name ) )
                        	{
                        ?>
                        		<li>
	                				<a class="thumb" href="<?php echo $large;?>" title="<?php echo $file->document_name; ?>">
	                            		<img src="<?php echo $small; ?>" alt=" " />
	                      			</a>
	                       			<div class="caption"><h4><?php echo $file->document_name; ?></h4></div>
	                    		</li>
	                    <?php } 
                        	}?>
	             		</ul>
               			<a class="pageLink next" href="#" title="Next Page">&nbsp;</a>
           			</div>
       				<div id="slideshow"></div> 
  					<div id="caption"></div>                                                     
          			<div id="controls"></div>
     			<?php } ?>
               </div>
               <?php endif; ?>
               
               <!-- pricing -->
               <?php if($showPricingTab) : ?>
               <div id="pricing">
                    
                    <?php echo str_replace("</span>","",str_replace("<span>&nbsp;", "", $product->pricing_description )); ?>
                    
                     <?php
                    if ( $this->login_model->is_logged_in('user'))
					{ 
                    	if( $prices )
                    	{
                    		$count = count( $prices->result() );
                    		$price_result = $prices->result();
                    ?>
                    <table>
                        <tr>
                            <th>Number of Licenses</th>
                            <th>Price (AUD) per Device</th>
                        </tr>
                        
                        <?php
                            //echo "<pre>"; print_r($price_result); echo "</pre>";    
                            $user_currency = $this->login_model->getSessionData("currency", "user");
                            for ( $i=0; $i < $count; $i++ )
                        	{ 
                        ?>
                        		<tr>
                        			<td>
                                        <?php echo $price_result[$i]->bracket_max; ?> 
                                        <?php echo ( ($count - 1) == $i ) ? '+' : '&ndash; '. ($price_result[$i+1]->bracket_max - 1); ?>
                                    </td>
                        			<td>
                                        <?php echo "$".$price_result[$i]->price; ?>
                                        <?php echo ($user_currency != "" && $user_currency != "AUD") ? '(~'.$this->utilities->currency_converter($price_result[$i]->price, 'AUD', $user_currency). " ".$user_currency.")" : "AUD"; ?>
                                    </td>
                        		</tr>
                        <?php
                        	} 
                        ?>
                        <tr>
                            <td>Larger Orders</td>
                            <td>Please <a href="#">contact us</a>.</td>
                        </tr>                         
                    </table>
                    
                    <div class="add2cart">
                        <input type="hidden" name="product_id" id="product_id" value="<?php echo $product_id; ?>">
                        
                        <div class="left">
                            <label for="quantity" style="display:block;width:45px;padding-top:11px;">Quantity:</label>
                        </div>    
    					<div class="left" style="padding-top: 5px; padding-right: 0; padding-left: 20px">
                            <input type="text" id="quantity" name="quantity" value="1" class="numeric" />
                           
       					</div>
       					<div class="left" id="price">
       						<?php echo ( count($price_result) >0) ? "$".$price_result[0]->price : "" ?>
       					</div>
       					
       					<div class="left">       						
       						
    						<input type="button" name="add_to_cart" value="Add to cart" id="add_to_cart" >
    					    <div class="clear"></div>                                   
                            <a <?php if( $this->product_model->get_total_number_of_items() <= 0) echo 'style="display:none"'; ?> href="<?php echo base_url();?>order/order_details">Proceed to checkout</a>                        
                            	
    					</div>
           				<div class="clear"></div>  
                        
                        <p>&nbsp;</p><!-- here will appear the message -->
                                                
    				</div>
                    <?php
                    	}
					}
					else
					{ 
                    ?>
                    <p>RRP: <?php echo "$". $product->rrp_price;?></p>
                    <p>
                    	Please <a href="<?php echo base_url()."page/account"; ?>">login</a> to view prices
                    </p>
                    <?php } ?>
                </div><!-- end pricing tab -->
               <?php endif; ?>
               
               <?php if(!$product->hidetab_oem) : ?>
               <!-- OEM -->
               <div id="oem">
               <?php
               		if( !empty($product->oem_description) )
               		{
               ?>
	                    <div class="icon OEM">
	                        <h3>OEM Service.</h3>
	                        <?php echo $product->oem_description; ?>
	                    </div>
	            <?php
               		} 
	            ?>
                <!-- end tabs-4 --></div>  
                <?php endif; ?> 
               
               <?php if(!$product->hidetab_downloads) : ?>
               <!-- downloads -->
               <div id="downloads">
                    <h3><?php
						if($product->download_caption != "")
						{
							echo $product->download_caption;
						}
						else
						{
							?> for <?php 
							echo $product->product_name; 
						}
					?>
					</h3>
                    <?php
                  		if( !empty( $product->download_link ) )
                  		{
                  	?>
                  			<a class="left topSpace btn downloadBtn" href="<?php echo $product->download_link; ?>" target="_blank">&nbsp;</a>
                  	<?php 
                  		}
                  	?>
                  	
                  	<?php
                  		if( !empty( $product->download_text ) )
                  		{
                  	?>
                  			<p class="subtle downloadtext"><?php echo $product->download_text; ?></p>
                  	<?php 
                  		}
                  	?>
                    <div class="divider"></div>
                    
                    <?php echo $this->load->view("misc/list_download_files", array("product" => $product, "download_files" => $download_files, "show_title" => TRUE));?>
                <!-- end tabs-5 --></div>  
                <?php endif; ?>                       
            </div>
          
          
          	<?php
			
          	/* make the accordion expand to the right tab */
          	$category_id = $product->product_category_id;
          	$count = 0;
          	
          	if( isset($categories) && $categories )
          	{
          		foreach ( $categories->result() as $cat )
          		{
          			if( $cat->product_category_id == $category_id )
          				break;
          			$count++;
          		}
          	}
          	?>
          	
          	<input type="hidden" value="<?php echo $count; ?>" id="accordion_tab"></input>