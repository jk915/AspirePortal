<body>
	<div id="wrapper">
        <div id="header">
            <?php $this->load->view('nav', $this->data); ?>   
        <!-- end header div --></div>
        
        <div id="sidebar">
        	<?php $this->load->view('products/product_sidebar', $this->data); ?>
        </div>
            
        <div id="main">
            <?php $this->load->view('products/product_body'); ?>
        </div>    

         
    <!-- end wrapper --></div>