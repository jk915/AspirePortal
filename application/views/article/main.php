<body>
	<div id="wrapper">
        <div id="header">
            <?php $this->load->view('nav', $this->data); ?>   
        <!-- end header div --></div>
        
        <div id="sidebar">
        	<?php $this->load->view("blocks", array( "foreign_id" => 7, "assignment_type" => "page", "position" => "left")); ?>
        	<?php $this->load->view("blocks", array( "foreign_id" => $article->article_id, "assignment_type" => "article", "position" => "left")); ?>
        </div>
        
        <div id="main">
            <div class="blogPost">
           		<h1><?php echo $article->article_title; ?></h1>
                <?php
                	if(($article->hero_image) && (file_exists( FCPATH.$article->hero_image )) && ($parent_category_code != "products"))
                    { 
                ?>	
                		<img alt="<?php echo $article->alt_hero_image; ?>" src="<?php echo base_url().$article->hero_image; ?>" />
                <?php
                    } 
                 ?>
                <?php echo $article->content; ?>
            </div> 
            <div class="clear"></div>        
            
            <?php
            if( !isset($parent_category_code) ||  (isset($parent_category_code) && $parent_category_code != "products"))
            {
            ?>        
                    
                <ul class="blogData full">
	                <li class="postDate"><?php echo $this->utilities->datefmt( $article->article_date, 'yyyy-mm-dd', 'l F jS, Y'); ?></li>
	                <li class="category">Filed under: <a href="<?php echo base_url().'category/'.$article->category_code; ?>"><?php echo ( $article->category_code == BLOG_ARTICLE_CATEGORY_CODE ) ? 'none' : $article->category_name; ?></a></li>
	                <li class="author">Post written by: <a href="<?php echo base_url().'user/'.$article->author; ?>"><?php echo $article->author; ?></a></li>                                               
                </ul>
                
                <div class="blogComments">
            	    <h2 class="commentsHeading"><?php echo $article->num_comments;?> comments</h2>
            	    <a class="addAComment" href="#addComment">Add A Comment</a>
            	    
            	    <?php 
					    if(isset($posts) && $posts)
					    {
		 				    $this->load->view("misc/posts");
					    }
					    else
					    {
						    echo '<p>There are no comments for this post yet.  Perhaps you should be the first?</p>';
					    }
				    ?>
				    
				    <form id="commentForm" name="commentForm" action="<?php echo base_url();?>/postback/articlepost" method="post" onsubmit="return $('#commentForm').validate().form();">
					    <a name="addComment"></a>
					    <h3>Add A Comment</h3>
					    
					    <label for="name">Name (required)</label>
					    <input id="name" class="required" type="text" name="name" value="<?php echo $this->login_model->getSessionData("first_name", 'user'); ?>" />
					    
					    <label for="email">Email address (required) (will not be published)</label>
					    <input id="email" class="required email"  type="text" name="email" value="<?php echo $this->login_model->getSessionData("email", 'user'); ?>" />
					                                
					    <label for="website">Website</label>
					    <input id="website" type="text" name="website"/>    
					                                
					    <label for="comment">Comment</label>
					    <textarea id="comment" class="required" cols="35" rows="6" name="comments"></textarea>  
					                                
					    <label style="width: 300px; margin-bottom: 10px;">Enter the following 4 letter code</label>
					    <img src="<?php echo base_url(); ?>securimage/securimage_show.php" alt="secureimage"/>
					    <label for="security_code">Code (required)</label>
					    <input type="text" class="text-field required" id="security_code" name="security_code" maxlength="4" minlength="4" />
					     
					    <input type="hidden" name="category_id" value="<?php echo $category->category_id; ?>" />
					    <input type="hidden" name="article_id" value="<?php echo $article->article_id; ?>" />
					    <input type="hidden" name="return_url" value="<?php echo base_url(); ?>blog/<?php echo $article->article_code; ?>" />
					    
					    <?php
						    if(!isset($error_message))
		                        $error_message = $this->session->flashdata("error_message");
		                        
		                    if($error_message != "")
		                        print '<p class="error_message">Notice: ' . $error_message .  '</p>';
					    ?>
					                                                     
					    <input type="submit" class="submit" name="submit" value="Submit Comment" />
				    </form>
            	    
                </div>
            <?php
            }
            ?>   
				
		</div>
        	
      </div>
