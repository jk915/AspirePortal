    <h4>Order Confirmation</h4>
    <div id="cart">
        <?php $this->load->view("order/list_cart", array("cart_products" => $cart_products));?>
    </div> 
            
    <?php 
     
    if(isset($cart_products['cart_products']) && !empty($cart_products['cart_products']))
    {   
    ?>
        <div class="coupon_code_div" <?php echo (isset($hide_coupon_code) && $hide_coupon_code) ? "style='display:none" : "";?>>
            <p class="left">Coupon code:</p>
            <input type="text" id="coupon_code" name="coupon_code" class="left" style="width: 150px" />
            <input type="button" class="button" value="Submit"> 
            <div class="clear"></div>
        </div>    
        
        <div class="clear_cart">
            <input type="button" value="Clear Cart" />
        </div>

        <div class="checkout">
            <input type="button" value="Checkout >" />
            <input type="hidden" value="<?php echo base_url() . "order/your_details" ?>" />         
        </div>
    <?php 
    }
    ?>  