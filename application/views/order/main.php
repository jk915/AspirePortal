<body>
    <div id="wrapper">
        <div id="header">
            <?php $this->load->view('nav', $this->data); ?>   
        <!-- end header div --></div>
        
        <?php $this->load->view("sidebar");?>
            
        <div id="main">
            <?php $this->load->view("order/{$order_step_name}"); ?>	        
        </div>                      
         
    <!-- end wrapper --></div>