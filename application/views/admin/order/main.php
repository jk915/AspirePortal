<body id="contact" >   
    <div id="wrapper">
        
        <? $this->load->view("admin/navigation");?>
        
        <div id="content">

            <? $this->load->view("admin/order/navigation"); ?>                                   
            
            <form class="plain" id="frmOrder" name="frmOrder" action="<?php echo base_url()?>ordermanager/order/<?php echo $order_id?>"  method="post">
                <div class="left">
                    <h2>Order Details</h2>
                    
                    <label for="date">Date:</label> 
                    <input type="text" name="date" id="date" value="<?php echo ($order_id != "") ? $order->order_date : "" ?>" readonly="readonly"/>
            
                    <label for="order_subtotal">Subtotal:</label> 
                    <input type="text" name="order_subtotal" id="order_subtotal" value="<?php echo ($order_id !="") ? $order->order_subtotal : "" ?>" readonly="readonly"/>
                    
                    <label for="order_tax_amount">Tax Amount:</label> 
                    <input type="text" name="order_tax_amount" id="order_tax_amount" value="<?php echo ($order_id !="") ? $order->order_tax_amount : "" ?>" readonly="readonly"/>
                    
                    <label for="order_total">Total:</label> 
                    <input type="text" name="order_total" id="order_total" value="<?php echo ($order_id !="") ? $order->order_total : "" ?>" readonly="readonly"/>
                    
                    <label for="order_status">Order Status:</label>
                    <?php 
                        echo form_dropdown('order_status', $search_status_arr, $order->order_status,  ' class="short" id="order_status" ' );  
                    ?>
                    
                    <?php if($order->order_status == 'completed') { ?>
                    	<br/><br/>
                    	Invoice: 
                    	<a class="download" href="<?php echo $order->invoice; ?>" title="<?php echo $order->invoice; ?>"><img src="<?php echo base_url();?>images/i_PDF.png" alt="i_PDF.png"></a>
                    	
                    <?php } ?> 
                    
                    <?php if( ifvalue( $order, 'payment_status') != "" ) {?> 
                    <label for="payment_status">Payment Status:</label>
                    <span id="payment_status" style="width: 270px; display: block;"><?php echo ifvalue( $order, 'payment_status', '' ); ?></span>         
                    <?php } ?>
                                        
                </div>    
                
                <div class="left left-margin">
                    <h2>Customer Details</h2>    
                                        
                    <label for="email">Email</label>
                    <input type="text" id="email" name="email" value="<?php echo ($order_id !="") ? $order->email : ""; ?>" readonly="readonly" />                    
                    
                    <label for="first_name">Contact First Name:</label> 
                    <input type="text" name="first_name" id="first_name" value="<?php echo ($order_id !="") ? $order->first_name : ""; ?>" readonly="readonly" />
                    
                    <label for="last_name">Contact Last Name:</label>
                    <input type="text" id="last_name" name="last_name" value="<?php echo ($order_id !="") ? $order->last_name : ""; ?>" readonly="readonly" />                

                    <label for="address">Website</label>
                    <input type="text" id="website" name="website" value="<?php echo ($order_id !="") ? ifvalue($order, 'website') : ""; ?>" class="required" />
                    
                    <label for="country">Country:</label>                        
                    <select name="country" id="country" >
                        <?php echo $this->utilities->print_select_options($countries, 'country_id', 'name', ifvalue($order, 'country', '')); ?>
                    </select>                        
                    
                    <label for="currency">Currency:</label>
                    <select name="currency" id="currency">
                            <?php echo $this->utilities->print_select_options($currencies, 'id', 'name', ifvalue($order, 'currency', '')); ?>
                    </select>                      
                                        
                 </div>
                 
                 <div class="left left-margin">
                    <h2>Delivery Method</h2>
                    <p><?php echo ($order->delivery_method == "print") ? "Print and Bind" : $order->delivery_method; ?></p>                    
                 </div>
                 <div class="clear"></div><br/>
                 
                 <hr></hr>
                 <h2>Billing and Delivery addresses</h2>
                 <br/>
                 <?php $this->load->view('misc/commerce_details', array("post" => $order, "no_checkbox" => TRUE));?>
                 
                 <h4>Comments</h4>   
                 <div>
                    <?php echo form_textarea(array('name'=>'comments', 'id'=>'comments', 'rows' => '5', 'cols' => '100'), ifvalue($order, 'comments', ''),'style="width: 400px" maxlength ="250"'); ?>    
                 </div>    
                 <br/>
              
                 <input id="button" type="submit" value="Update Order" />              

            </form>    

            <br/>
            <br/> 
            
            <?php if( isset($order_items) && $order_items ){ ?>
            <div id="order_items">
              
                  <h2>Order Items Info:</h2>
                  
                  <table cellspacing="0" class="cmstable" >
                  
                      <tr class="admintablerowalt">
                          
                          <th>Product Name</th>
                          <th>Quantity</th>
                          <th>Price</th>
                          <th>Total</th>
                          
                      </tr>
                      
                      <?php foreach( $order_items->result() as $order_item){ ?>
                      <tr class="admintablerowalt">
                      
                          <td><?php echo $order_item->product_name; ?></td>
                          <td><?php echo $order_item->quantity; ?></td>
                          <td>$<?php echo $order_item->item_subtotal; ?></td>
                          <td>$<?php echo $order_item->item_total; ?></td>
                          
                      </tr>
                      <?php } ?>
                  
                  </table> 
                  
                  <table class="right" style="width: 200px;" >
                  
                      <tr class="admintablerowalt">
                          <td>Tax:</td>
                          <td style="text-align:right;">$<?php echo ifvalue( $order, 'order_tax_amount', '0' ); ?></td>
                      </tr>
                      <tr class="admintablerowalt">    
                          <td>Sub Total:</td>
                          <td style="text-align:right;">$<?php echo ifvalue( $order, 'order_subtotal', '0' ); ?></td>
                      </tr>
                      <?php 
                      if( ifvalue( $order, 'coupon_code' ) )
                      {
                      ?>
                      <tr class="admintablerowalt">    
                          <td>Discount:</td>
                          <td style="text-align:right;">-$<?php echo ifvalue( $order, 'coupon_discount_value', '0' ); ?></td>
                      </tr>
                      <?php 
                      }
                      ?>
                      <tr class="admintablerowalt">    
                          <td><b>Total:</b></td>
                          <td style="text-align:right;">$<?php echo ifvalue( $order, 'order_total', '0' ); ?></td>
                      </tr>
                  
                  </table>
                  
              
              </div>
              <?php } ?>   
                            
            <p></p>
            <? $this->load->view("admin/order/navigation"); ?>