                <?php $discount_product = isset($item['options']) && array_key_exists("coupon_id", $item['options']); ?>
                    
                    <tr>
                        <td><?php echo $item['name']; ?></td>
                        <td>
                            <?php 
                                
                                if ((isset($read_only) && $read_only == TRUE) || $discount_product) 
                                {
                                    echo $item['qty'];
                                }
                                else
                                {
                                ?>
                                    <input type="text" id="select_qty_<?php echo $item['rowid'];?>" class="select_qty numeric" value="<?php echo $item['qty'];?>" />
                                <?php 
                                }
                                ?>    
                        </td>
                        <td>$<?php echo number_format($item['price'], 2, ".", ""); ?></td>
                        <td>$<?php echo number_format($item['subtotal'], 2, ".", ""); ?> AUD</td>
                        <td>
                            <?php 
                                if (! ((isset($read_only) && $read_only == TRUE) || $discount_product))
                                {
                                ?> 
                                    <a id="remove_cart_item_<?php echo $item['rowid'];?>" href="#"><img src="<?php echo base_url();?>images/delete.png"/></a>
                                <?php
                                 
                                }
                                ?>
                        </td>
                    </tr>