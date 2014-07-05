        <div class="left">
            <h2 class="left" style="width:180px">Available Categories</h2>
                <a id="refresh_categories" href="javascript:void(0)" class="left">
                    <img  src="<?php echo base_url()?>images/admin/refresh.png" />
                </a>
                <br class="clear" />
                <div class="breadCrumbHolder module">
                    <div id="breadcrumbs" class="breadCrumb module">
                        <ul>
                            <?php echo $this->product_model->generete_breadcrumbs($category_id); ?>
                        </ul>
                    </div>
                </div>
                <br class="clear" />
                <div class="left" id="categories_div" style="width: 270px;"> 
                    <?php $this->load->view('admin/productmanager/category_listing.php'); ?>
                </div>
                
                <div class="left left-margin" id="products_div">
                    <div id="page_listing">
                        <?php $this->load->view('admin/productmanager/product_listing.php'); ?>
                    </div>
                </div>
                <br class="clear"/>

                <div id="controls">

                    <div id="page_buttons" class="left" style="margin-left: 299px;">
                        <div id="pagination"></div>
                    </div>

                </div>
                
                <div class="left">
                
                    <!--   add/delete category -->
                    <img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add Category." /> <a id="addcategory" href="javascript:void(0)">Add Category</a>
                    <span class="divider">|</span>  
                    <img src="<?=base_url();?>images/admin/i_trashcan.png" border="0" width="16" height="18" alt="Delete Category." /> <a id="deletecategory" href="javascript:void(0)">Delete Selected</a>
                    <br />
                    <br /> 
                    <div id="new_category_div" style="display:none">
                    	<label for="new_category">New Category Name:</label>
                        <input type="text" id="new_category" />
                        
						<label for="new_category_code">Category Code</label>  
						<input type="text" id="new_category_code" class="block" />                             
                                               
                    	<div class="top-margin-sm">
                        	<input type="button" id="savecategory" value="Add Category" class="smallbutton" />
                        	<input type="button" id="cancelcategory" value="Cancel" class="smallbutton" />
                        </div>
                    </div>
                    <!--  END add/delete category -->
                    
                </div>
                <div class="right">
                    <? if($category_id != -1) : ?>
                    <!--   add/delete products   -->
                    <img src="<?php echo base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add Product." /> <a href="<? echo base_url()."productmanager/add_product/".$category_id; ?>" >Add Product</a>
                    <span class="divider">|</span>  
                    <img src="<?php echo base_url();?>images/admin/i_trashcan.png" border="0" width="16" height="18" alt="Delete Product." /> <a id="deleteproduct" href="javascript:void(0)" href="<?php echo base_url();?>">Delete Product</a>
                    <br />
                    <br />                    
                    <!-- END add/delete products -->
                    <? endif; ?>
                    
                </div>
                <div class="clear"></div>
          </div>