	<h4>Payment Details</h4>
    <div id="cart">
        <?php $this->load->view("order/list_cart", array("cart_products" => $cart_products, "read_only" => true));?>
    </div>
     
   <form action="<?php echo base_url()."order/submit_order" ?>" method="post" class="payment_detail_form" id="payment_details_form_id">
	<div class="pay_details">
		<div id="credit_details"><h4>Credit Card details:</h4></div>
		
		<div class="left pay_details_padding" >
             
     		<label for="card_holder">Name on credit card<span class="requiredindicator">*</span></label> 
       		<input type="text" name="card_holder" class="required" value="" id="card_holder" />
       		<div class="clear"></div>
                
      		<label for="card_type">Credit card type<span class="requiredindicator">*</span></label> 
    		<select name="card_type" id="card_type">
    			<option value="1">VISA</option>
    			<option value="2">MASTERCARD</option>
    		</select>
    		<div class="clear"></div>
                
      		<label for="card_number">Card number<span class="requiredindicator">*</span></label> 
       		<input maxlength="16" type="text" id="card_number"  name="card_number" value="" class="required numeric" />
       		<div class="clear"></div>
              
                
            <label for="exp_month">Expiry Date:<span class="requiredindicator">*</span></label> 
       		
       		
	       		<input style="width: 30px;" maxlength="2" class="short_input required" type="text" id="exp_month" name="exp_month"  value="" />
	       		<input style="width: 30px;" maxlength="2" class="short_input required" type="text" id="exp_year" name="exp_year"  value="" />
	       		 
	       		 <label style="padding-left:10px">mm/yy</label>
       		
       		<input type="hidden" name="exp_date" id="exp_date" value="00-00"/>
       		<div class="clear"></div>
                
      		<label for="card_cvv">CVV2 Number<span class="requiredindicator">*</span></label> 
    		<input maxlength="3" type="text" id="card_cvv" name="card_cvv" class="required numeric" value="" />
    		<div class="clear"></div>
    		<div style="text-align: center;">
    			<a href="#" id="submit_order" class="button buttonSubmit"></a>
    		</div>       
 		</div>
 		<div class="clear"></div>
	</div>
	</form>
    <p>Please click the "Make Payment" button to submit your order. Your credit card will be charged for the above amount.</p>
            
    <?php  
    if(isset($cart_products) && !empty($cart_products))
    {    
    ?>
        <div class="clear_cart">
            <input type="button" value="&lt; Back" />
            <input type="hidden" value="<?php echo base_url() . "order/your_details" ?>" />
        </div>

        <div class="checkout">
            <input type="button" value="Make Payment &gt;" />
            <input type="hidden" value="javascript:save_payment()" />            
        </div>
    <?php 
    }
    ?>  