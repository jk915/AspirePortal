<body>
	<div id="wrapper">
        <div id="header">
            <?php $this->load->view('nav', $this->data); ?>   
        <!-- end header div --></div>
        
        <div id="sidebar" style="">
        	<div class="sideWrapper">
        		<h3>Preview Only</h3>
				<p>You are viewing a preview.</p>
			</div>
        </div>
        
        <div id="main">
        	<h1>
        		<span>Content Preview</span>
        	</h1>
        	
        	<?php echo $html; ?>
        	
        </div>
    </div>