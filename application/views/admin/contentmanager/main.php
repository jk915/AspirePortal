<body id="contact" >   
	<div id="wrapper">
		<?php $this->load->view("admin/navigation");?>

		<div id="content">
			<?php $this->load->view("admin/contentmanager/navigation", array("side" => "top")); ?>            
			<?php if($message!=""):?>
			<p><?php echo $message?></p>
			<? endif; ?>  

			<form class="plain" action="#">
				<div class="left">    
					<div class="breadCrumbHolder module">
						<div id="breadcrumbs" class="breadCrumb module">
							<ul>
								<?php echo $this->article_model->generete_breadcrumbs($category_id); ?>
							</ul>
						</div>
					</div><!-- end breadcrumb -->
					
					<br class="clear" />

					<?php 
                    // $default_website_id = $this->utilities->get_session_website_id();
                    // if($default_website_id > 0): 
					if($this->data["website_id"] > 0): ?>
                    <?php if(!isset($category_locked)) : ?>
					<div class="left" id="categories" style="width: 270px;"> 
						<?php $this->load->view('admin/contentmanager/category_listing.php'); ?>
					</div><!-- end categories -->
					<?php endif; ?>

					<div class="left" id="products_div" style="width:<?php echo (isset($category_locked)) ? '900' : '605'; ?>px">

						<div class="left left-margin" style="width:<?php echo (isset($category_locked)) ? '900' : '605'; ?>px">
							<h2 class="left" style="width:180px">Items in Category</h2>

							<a id="refresh_articles" href="javascript:void(0)" class="left">
								<img src="<?=base_url()?>images/admin/refresh.png" alt="Click to refresh" />
							</a>

							<div class="clear"></div>
							<div id="files_listing">
								<?php $this->load->view('admin/contentmanager/content_listing',array('articles' => $articles, 'category_id' => $category_id)); ?>
							</div>
						</div>

						<div class="clear"></div>

						<div id="controls">
							<div id="page_buttons" class="left" >
								<div id="pagination"></div>
							</div>

							<div class="right">
								<input class="button" type="button" value="Delete Selected Item" id="delete_files" />
							</div>
							<div class="clear"></div>
							<br/>            
						</div>    
					</div><!-- end products-div -->
					
					<br class="clear"/>   

					<?php if(!isset($category_locked)) : ?>
					<img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add Category." /> <a id="addcategory" href="javascript:void(0)">Add Category</a>
					<span class="divider">|</span>  
					<img src="<?=base_url();?>images/admin/i_trashcan.png" border="0" width="16" height="18" alt="Delete Category" /> <a id="deletecategory" href="javascript:void(0)">Delete Category</a>
					<br class="clear"/><br/>

					<div id="new_category_div" class="top-margin" style="display: none;">
						<label for="new_category">New Category Name</label>  
						<input type="text" id="new_category" class="block" />
						
						<label for="new_category_code">Category Code</label>  
						<input type="text" id="new_category_code" class="block" />						
                                       
						<div class="top-margin-sm">
							<input type="button" id="save" value="Add Category" class="smallbutton" />
							<input type="button" id="cancel" value="Cancel" class="smallbutton" />
						</div>
					</div><!-- end new_category_div -->
					<?php endif; ?>
					
					<?php else: ?>
					
					<div class="user_message">Please select a website from the website selector.</div>
					
					<?php endif; ?>
				</div><!-- end main float left -->
			</form>

			<?php
				$this->load->view("admin/contentmanager/navigation", array("side" => "bottom")); 
			?>