<body id="contact" >
	<div id="wrapper">
   	<?php $this->load->view("admin/navigation");?>

      <div id="content">
      	<?php $this->load->view("admin/productcategory/navigation"); ?>

      	<p><?php echo $message?></p>

		  	<form class="plain" id="frmCategory" name="frmCategory" action="<?php echo base_url()?>productcategorymanager/edit/<?php echo $category_id?>"  method="post">
    	  		<h2>Category Properties</h2>

				<!-- tabs -->
				<ul class="css-tabs skin2">
					<li><a href="#">Category Details</a></li>
					<li><a href="#">Google/SEO</a></li>
					<li><a href="#">Content</a></li>
				</ul>

				<!-- panes -->
				<div class="css-panes skin2">
					<div style="display:block">
                        <div class="left">
                            
                            <label for="name">Category Name:<span class="requiredindicator">*</span></label>
                            <input type="text" name="name" id="name" class="required" value="<? echo ($category_id !="") ? $category->name : "" ?>"/>
                            
                            <label for="category_code">Category Code:<span class="requiredindicator">*</span></label>
                            <input type="text" name="category_code" id="category_code" class="required" value="<? echo ($category_id !="") ? $category->category_code : "" ?>"/>

                            <label for="parent_id">Parent Category:</label>
                            <select id="parent_id" name="parent_id">
                                <option value="-1">None</option>
                                <?php
                                      echo $this->utilities->print_select_product_tree($categories,$parent_id);
                                ?>
                            </select>
                            
							<!-- <label for="seq_no">Category Seq. No:<span class="requiredindicator">*</span></label>
                            <input type="text" name="seq_no" id="seq_no" class="required" value="<?php //echo ($category_id !="") ? $category->seq_no : "" ?>"/>                            
							 -->
							
                            <label for="order_by">Order Products By:</label>
                            <select id="order_by" name="order_by">
                                <?php echo $this->utilities->print_select_options($order_by_options, "name", "name", ($category_id !="") ? $category->order_by : ""); ?>
                            </select>

                            <label for="order_dir">Order Direction:</label>
                            <select id="order_dir" name="order_dir">
                                <option value="DESC" <?php if($category) {if($category->order_dir == "DESC") print "selected=\"selected\""; } ?>>Descending</option>
                                <option value="ASC" <?php if($category) {if($category->order_dir == "ASC") print "selected=\"selected\""; } ?>>Ascending</option>
                            </select>

                            <label for="hero_image">Hero Image</label>
                            <input type="text" id="hero_image" name="hero_image" class="left" value="<?php echo ($category_id !="") ? $category->hero_image : "" ?>" />
                            <input type="button" class="select-file left-margin" onclick="selectFile('hero_image');" value="Select" />

                            <label for="blog_text">Hero Image Alt Text</label>
                            <input type="text" name="hero_text" id="hero_text" class="" value="<? echo ($category_id !="") ? $category->hero_text : "" ?>"/>

                            <div class="top-margin"></div>
                            <input type="checkbox" id="enabled" name="enabled" value="1" class="left" <?php echo ($category_id !="") ? (($category->enabled == 1) ? "checked=\"checked\"" :"") : "checked" ?>  />
                            <label for="enabled" class="left" style="padding-top:0px">&nbsp;Category is active</label>
                            

                        </div>

                        <div class="left" style="padding-left: 50px;">

                            <label for="category_image">Category Selection Image</label>
                            <input type="text" id="category_image" name="category_image" class="left" value="<?php echo ($category_id !="") ? $category->category_image : "" ?>" />
                            <input type="button" class="select-file left-margin" onclick="selectFile('category_image');" value="Select" />

                            <label for="category_text">Category Selection Image Alt Text</label>
                            <input type="text" name="category_text" id="category_text" class="" value="<? echo ($category_id !="") ? $category->category_text : "" ?>"/>

                            <!-- <label for="background_image">Background Image</label>
                            <input type="text" id="background_image" name="background_image" class="left" value="<?php //echo ($category_id !="") ? $category->background_image : "" ?>" />
                            <input type="button" class="select-file left-margin" onclick="selectFile('background_image');" value="Select" />
                             -->

                            <!--<label for="view">MVC View (leave blank unless you're a  coder):</label>
                            <input type="text" id="view" name="view" value="<?php // echo ($category_id !="") ? $category->view : "" ?>"/>
                            -->

                        </div>

                        <div class="clear top-margin"></div>
                        
       		   </div><!-- end first tab -->

        			<!-- begin SEO tab -->
        			<div>
            		<label for="meta_title">Meta title:</label>
	               <input type="text" id="meta_title" name="meta_title" value="<?php  echo ($category_id !="") ? $category->meta_title : "" ?>"/>

            		<label for="meta_keywords">Keywords:</label>
	               <input type="text" id="meta_keywords" name="meta_keywords" value="<?php echo ($category_id !="") ? $category->meta_keywords : "" ?>"/>

            		<label for="meta_description">Description / Call to action:</label>
	               <input type="text" id="meta_description" name="meta_description" value="<?php echo ($category_id !="") ? $category->meta_description : "" ?>"/>

            		<label for="meta_robots">Search Robots Permissions:</label>
	               <select id="meta_robots" name="meta_robots">
               		 <?php echo $this->utilities->print_select_options_array($robots, false, ($category_id !="") ? $category->meta_robots : ""); ?>
            		</select>

        			</div><!-- end SEO tab -->

        			<!-- begin content tab -->
        			<div>
        				<label for="short_description">Short Description:</label>
            		<div style="height:330px">
                		<textarea id="short_description" cols="20" rows="10" name="short_description" style="width:880px; height:300px;" class="editor"><?php  echo ($category_id !="") ? $category->short_description : "" ?></textarea>
            		</div>

            		<div class="clear top-margin"></div>

            		<label for="long_description">Long Description:</label>
            		<div style="height:330px">
                		<textarea id="long_description" cols="20" rows="10" name="long_description" style="width:880px; height:300px;" class="editor"><?php  echo ($category_id !="") ? $category->long_description : "" ?></textarea>
            		</div>

		        </div><!-- end content tab -->

    			</div><!-- end panes -->

       		<label for="heading">&nbsp;</label>
    			<input id="button" type="submit" value="<? echo ($category_id == "") ? "Create New Category": "Update Category"?>" />

    			<input type="hidden" name="postback" value="1" />
    			<input type="hidden" name="id" value="<?php echo $category_id?>" />
         </form>

         <div class="clear top-margin"></div>

	 		<? $this->load->view("admin/productcategory/navigation"); ?>
