
<body id="<?php echo $article->article_code; ?>">

    <?php $this->load->view("sidebar", array("use_cache" => 0)); ?>

    <div id="main">
        <div id="headings">
            <h1><?php echo $article->article_title?></h1>
        </div><!-- end headings -->
        
        <div class="content">
            <?php echo $html?>
        </div><!-- end content -->
    </div><!-- end main -->
