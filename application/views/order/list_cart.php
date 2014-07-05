            <?php 
            $user_currency = $this->login_model->getSessionData("currency", "user");
            if(isset($cart_products['cart_products']) && !empty($cart_products['cart_products'])){ ?>
                <table>
                    <tr>
                        <th width="300">Product name</th>
                        <th width="90">Qty</th> 
                        <th width="110">Price</th>
                        <th width="110">Total</th>
                        <th width="50"></th>
                    </tr>
                <?php 
                $free_products = array();                
                foreach($cart_products['cart_products'] as $item) 
                {
                    if( $item['id'] > 0)
                    {
                        $this->load->view("order/cart_item", array("item" => $item, "read_only" => (isset($read_only) ? $read_only : FALSE) ));
                    }
                    else
                    {
                        $free_products[] = $item;    
                    }    
                }
                
                foreach($free_products as $item)
                {
                    $this->load->view("order/cart_item", array("item" => $item, "read_only" => (isset($read_only) ? $read_only : FALSE) ));   
                } 
                ?>
                    <tr>
                        <td colspan="3">Subtotal:</td>
                        <td colspan="2">$<?php echo number_format($cart_products['subtotal'], 2, ".", "")." AUD"; ?></td>
                    </tr>
                    <?php if(isset($cart_products['include_gst']) && $cart_products['include_gst'])
                    {
                    ?>    
                    <tr>
                        <td colspan="3">GST:</td>
                        <td colspan="2">$<?php echo $cart_products['gst']." AUD"; ?></td>
                    </tr>
                    <?php
                    }
                    ?>
                    <tr>
                        <td colspan="3">Total:</td>
                        <td colspan="2">$<?php echo $cart_products['total']." AUD"; ?>
                        <?php if($user_currency != "" && $user_currency != "AUD") echo "(~" . $this->utilities->currency_converter($cart_products['total'], 'AUD', $user_currency). " ".$user_currency.")"; ?>
                        </td>
                    </tr>
                </table>
                 
                                     
    
        <?php }  else {?>
            <div> <p>Your Shopping Basket is empty.</p></div>
        <?php } ?>