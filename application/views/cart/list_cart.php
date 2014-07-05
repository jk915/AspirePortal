            <?php  if(isset($cart_products) && !empty($cart_products)){ ?>
                <table>
                    <tr>
                        <th width="300">Product name</th>
                        <th width="90">Qty</th> 
                        <th width="110">Price</th>
                        <th width="110">Total</th>
                        <th width="50"></th>
                    </tr>
                <?php foreach($cart_products as $item) { ?>
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td>
                        <input type="text" id="select_qty_<?php echo $item['rowid'];?>" class="select_qty numeric" value="<?php echo $item['qty'];?>" />    
                        <?php /*
                        <select id="select_qty_<?php echo $item['rowid'];?>">
                            <?php for ($i=1; $i <= 25; $i++)
                                  { ?>
                                    <option value="<?php echo $i; ?>" <?php echo ($item['qty'] == $i) ? "selected='selected'" : ""; ?>  ><?php echo $i; ?></option>
                            <?php } ?> 
                        </select>*/ ?>
                        </td>  
                        <td>$<?php echo number_format($item['price'], 2, ".", ""); ?></td>
                        <td>$<?php echo number_format($item['subtotal'], 2, ".", ""); ?></td>
                        <td>
                            <a id="remove_cart_item_<?php echo $item['rowid'];?>" href="#"><img src="<?php echo base_url();?>images/delete.png"/></a>
                        </td>
                    </tr>
                <?php } ?>
                    <tr>
                        <td colspan="2">Subtotal:</td>
                        <td colspan="3">$<?php echo number_format($subtotal,2,".",""); ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">GST:</td>
                        <td colspan="3">$<?php echo $gst; ?></td>
                    </tr>
                    <tr>
                        <td colspan="2">Total:</td>
                        <td colspan="3">$<?php echo $total; ?></td>
                    </tr>
                </table>
                 
                                     
    
        <?php }  else {?>
            <div> <p style="margin-left: 10px;">Your Shopping Basket is empty</p></div>
        <?php } ?>