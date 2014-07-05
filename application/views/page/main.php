<body class="<?php echo !empty($article->article_code) ? $article->article_code : ""?>">
    <?php $this->load->view("page_header"); ?>
    
    <div id="header">
    	<div class="wrap">
        	<a href="<?php echo base_url()?>" class="logo left">Logo</a>
        </div>
    </div><!-- #header -->
    
    <div id="main">
    	<div class="wrap">
        	<div id="content" class="left">
            	<div class="format">
                	<h1><?php echo strtoupper($article->article_title)?></h1>
                    <?php echo $article->content;?>
                </div>
            </div><!-- #content -->
            
            <?php $this->load->view("sidebar_right"); ?>
            
            <div class="clear"></div>
        </div>
    </div><!-- #main -->