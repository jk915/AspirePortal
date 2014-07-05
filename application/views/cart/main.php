<body>
    <div id="wrapper">
        <div id="header">
            <?php $this->load->view('nav', $this->data); ?>   
        <!-- end header div --></div>
        
        <?php $this->load->view("sidebar");?>
            
        <div id="main">
        	<div id="cart">
            	<?php $this->load->view("cart/list_cart");?>
            	
            </div> 
            
            <?php  
            	if(isset($cart_products) && !empty($cart_products))
            	{
            
            ?>
	             <div class="clear_cart">
	                	<input type="button" value="Clear Cart" />
	             </div>
	                
	              <div class="checkout">
	                	<input type="button" value="Checkout" />
	              </div>
	        <?php 
            	}
            	?>  
            
            
                 
        </div>   
        
       

         
    <!-- end wrapper --></div>