	        <h4>Browse our Products:</h4>
            
            <?php
            	if( $categories )
            	{
            		
            ?>
                <div class="accordionWrapper">
                <!--start accordion -->
                    
            <?php
            	$count = 0;
            	foreach ($categories->result() as $category) 
            	{
            ?>
					 <div class="accordion">
                        <h5><a href="<?php echo '#'.$category->category_code; ?>" ><?php echo $category->name; ?> <br />
                        <span class="description"><?php echo strip_tags($category->short_description);?></span></a></h5>
                          <div>
                             <a name="<?php echo $category->category_code; ?>"></a>
                             <?php
                             	$products = $this->product_model->get_list( 1, $category->product_category_id );
                             	if( $products )
                             	{
                             		foreach ( $products->result() as $product )
                             		{	 
                             ?>
		                                <a href="<?php echo base_url().'products/'.$product->model_number; ?>">
		                                	<?php
		                                		if( !empty($product->hero_image) )
		                                		{
		                                			$image = $this->document_model->get_details($product->hero_image);
		                                			if($image)
		                                			{
		                                	?>
		                                				<img class="left" src="<?php echo base_url().$image->document_path; ?>" border="0" width="30" height="30" alt="logo" />
		                                	<?php 			
		                                			}
		                                		}
		                                			 
		                                	?>
			                                
			                                <?php echo $product->product_name; ?>			                                
			                                <span class="description"><?php echo strip_tags($product->short_description);?></span>
		                                </a>
                            <?php
                             		}
                             	}
                             	$count++; 
                            ?>
                          </div>
                        </div> 
            <?php
            	} 
            ?>

                   <!--end accordion -->  
                <!-- end accordionWrapper --></div>
			<?php
            	} 
            /*    
            ?>
            <h4>Cart:</h4>
            <div id="cart">
            <?php $this->load->view( 'products/cart' ); ?>
            </div>
            */?>
