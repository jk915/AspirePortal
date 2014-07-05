<body id="contact" >       
    <div id="wrapper">        
        <?php $this->load->view("admin/navigation");?>        
        <div id="content">            
            <?php $this->load->view("admin/coupon/navigation"); ?>                        
            <p><?php if(isset($message)) echo $message;?><!-- -->&nbsp;</p>    			
            
            <form class="plain" id="frmCoupon" name="frmCoupon" action="<?php echo base_url(); ?>couponmanager/coupon/<?php echo $coupon_id?>"  method="post">
                <h2>Coupon Details</h2>    			            
                
                <div class="left" style="width:30%">			                
                
                    <label for="coupon_code">Coupon code:<span class="requiredindicator">*</span></label> 			                
                    <input maxlength="20" style="width: 140px" type="text" name="coupon_code" id="coupon_code" class="required" value="<? echo ($coupon_id !="") ? $coupon->coupon_code : "" ?>" />			                			                
                    
                    <label for="discount_type">Discount Type:<span class="requiredindicator">*</span></label> 			                
                    <input type="radio" name="discount_type" class="required" value="percentage" <?php echo ($coupon_id !="" && $coupon->discount_type == 'percentage' ? 'checked="checked"' : ( empty( $coupon_id ) ? 'checked="checked"' : "" ) ) ?> />&nbsp;Percentage<div class="clear"></div>			                
                    <input type="radio" name="discount_type" class="required" value="value" <?php echo ($coupon_id !="") && $coupon->discount_type == 'value' ? 'checked="checked"' : "" ?> />&nbsp;Value<div class="clear"></div>                                                        
                    <input type="radio" name="discount_type" class="required" value="products" <?php echo ($coupon_id !="") && $coupon->discount_type == 'products' ? 'checked="checked"' : "" ?> />&nbsp;Products<div class="clear"></div>                                                        
                    <input type="radio" name="discount_type" class="required" value="amount" <?php echo ($coupon_id !="") && $coupon->discount_type == 'amount' ? 'checked="checked"' : "" ?> />&nbsp;Amount<div class="clear"></div>			                			                
                                        
                    <label for="start_date">Start Date:<span class="requiredindicator">*</span></label> 			                
                    <input name="start_date" id="start_date" class="date-pick dateITA required" value="<?php echo ($coupon_id !="") ? $coupon->dmy_start_date : "";?>" />			                
                    <div class="clear"></div>			                			                
                    
                    <label for="finish_date">Finish Date:<span class="requiredindicator">*</span></label> 			                
                    <input name="finish_date" id="finish_date" class="date-pick dateITA required" value="<?php echo ($coupon_id !="") ? $coupon->dmy_finish_date : "";?>" />			                
                                        
                    <div class="clear"></div><br/>
                    <div>
                        <input type="checkbox" value="1" id="coupon_code_required" name="coupon_code_required" class="left" <?php echo (ifvalue($coupon, 'coupon_code_required') == 1) ? 'checked="checked"' : '';?> >
                        <label style="padding-top: 0px;" class="left" for="coupon_code_required">&nbsp;Coupon Code is required</label> 
                    </div>                    
                    <div class="clear"></div><br/>
                    <div>
                        <input type="checkbox" value="1" id="use_multiple_times" name="use_multiple_times" class="left" <?php echo (ifvalue($coupon, 'use_multiple_times') == 1) ? 'checked="checked"' : '';?> >
                        <label style="padding-top: 0px;" class="left" for="use_multiple_times">&nbsp;This coupon can be used multiple<br/>times by the same customer</label> 
                    </div>                    
                    <br/>                                            
                    
                </div>			            
                
                <div class="left" style="width: 70%;">
                    
                    <div class="discount_type_div">			   
                        <div id="percentage_value_div">    
                            <?php $this->load->view('admin/coupon/discount_type/percentage_value.php'); ?>
                        </div>
                        <div id="products_type_div" style="display: none;">    
                            <?php $this->load->view('admin/coupon/discount_type/buy_reward.php'); ?>
                        </div>    
                    </div>
                    <?php /*     	
                    <label for="username">Username</label> 			                
                    <input maxlength="150" type="text" id="username" name="username" class="" value="<?php echo ($coupon_id !="") ? $coupon->username : "" ?>" />			                			                
                    
                    <label>Category</label> 			                
                    <?php echo form_dropdown( 'product_category_id', query2array( $categories, 'product_category_id', 'name', array( '-1' => 'All categories' ) ), ( !empty( $coupon_id ) ? $coupon->product_category_id : '-1' ) ); ?>			                			                
                    
                    <label>Product</label>
                    <?php echo form_dropdown( 'product_id', query2array( $products, 'product_id', 'product_name', array( '-1' => 'All products' ) ), ( !empty( $coupon_id ) ? $coupon->product_id : '-1' ), 'id="products"' ); ?>			                			                
                    
                    <label for="use_max">Maximum number of uses</label> 			                
                    <input type="text" id="use_max" name="use_max" class="numeric" value="<?php echo ($coupon_id !="") ? $coupon->use_max : "" ?>" />			                			                
                    
                    <label for="minimum_order">Minimum Order Value</label> 			                
                    <input type="text" id="minimum_order" name="minimum_order" class="numeric" value="<?php echo ($coupon_id !="") ? $coupon->minimum_order : "" ?>" />			                			                
                    
                    <label for="message">Coupon Message</label> 			                
                    <textarea id="message" name="message" rows="10" cols="20" ><?php echo ($coupon_id !="") ? $coupon->message : "" ?></textarea>			            
                    */ ?>
                </div>			            
                <div class="clear"></div>                 			    
                
                <label for="button">&nbsp;</label> 			    
                <input id="button" type="button" value="Save Coupon" /><br/>                			    
                
                <input type="hidden" name="postback" value="1" />			    
                <input type="hidden" name="id" id="id" value="<?php echo $coupon_id ?>" />			
                <input type="hidden" name="buy_ids" id="buy_ids" value="" />
                <input type="hidden" name="reward_ids" id="reward_ids" value="" />
                <input type="hidden" name="discount" id="discount" value="" />
                
            </form>
            <p><!-- -->&nbsp;</p>
            <?php $this->load->view("admin/coupon/navigation"); ?>