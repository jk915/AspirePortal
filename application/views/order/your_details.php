    <h4>Your Details</h4>
    <div id="cart">
        <?php $this->load->view("order/list_cart", array("cart_products" => $cart_products, "read_only" => true));?>
    </div> 
    <form id="user_form" method="post" action="<? echo base_url(). "order/save_your_details" ?>">
    
    	<?php $this->load->view('misc/personal_details'); ?>
    
    	<?php $this->load->view('misc/commerce_details');?>
        
        <h4>Comments</h4>   
        <div>
            <?php echo form_textarea(array('name'=>'comments', 'id'=>'comments', 'rows' => '5', 'cols' => '100'), ifvalue($post, 'comments', ''),'style="width: 400px" maxlength ="250"'); ?>    
        </div>    
        
    </form>
            
    <?php  
    if(isset($cart_products) && !empty($cart_products))
    {    
    ?>
        <div class="clear_cart">
            <input type="button" value="< Back" />
            <input type="hidden" value="<?php echo base_url() . "order/order_details" ?>" />
        </div>

        <div class="checkout">
            <input type="button" value="Next >" />
            <input type="hidden" value="javascript:save_details()" />            
        </div>
    <?php 
    }
    ?>  