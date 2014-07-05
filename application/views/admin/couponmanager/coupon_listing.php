<input type="hidden" value="<?php echo ceil($pages_no); ?>" id="pages_no" />
<table cellspacing="0" class="cmstable">             
    <tr>                
        <th>ID</th>                                            
        <th>Coupon Code</th>                                            
        <th>Start date</th>                                            
        <th>Finish date</th>                
        <th>Discount</th>                
        <th style="width: 20px;">Delete</th>             
    </tr>        
    <?php 
    $i=0; 
    if($coupons) 
    { 
        foreach($coupons->result() as $row)
        {                
            $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";
            ?>                 
            <tr class="<?php echo $rowclass;?>">                                        
                <td class="admintabletextcell">
                    <?php echo $row->coupon_id;?>
                </td>                    
                <td class="admintabletextcell">
                    <a href="<?php echo base_url();?>couponmanager/coupon/<?php echo $row->coupon_id;?>">
                    <?php echo $row->coupon_code;?>
                    </a>
                </td>                    
                <td class="admintabletextcell">
                    <?php echo $row->start_date;?>
                </td>                    
                <td class="admintabletextcell">
                    <?php echo $row->finish_date;?>
                </td>                    
                <td class="admintabletextcell">
                    <?php 
                    switch($row->discount_type)
                    {
                        case 'percentage':
                            echo $row->discount . ' %'; 
                        break;
                        
                        case 'value':
                            echo $row->discount . ' $';
                        break;
                        
                        case 'products':
                            $buy_products = $this->coupon_model->get_discount_prdoucts($row->coupon_id, 'buy');
                            $reward_products = $this->coupon_model->get_discount_prdoucts($row->coupon_id, 'reward');
                            echo "If they buy: ".ifvalue($buy_products, "discount_products")."<br/>the reward is: ".ifvalue($reward_products, "discount_products");
                        break;
                        
                        case 'amount':
                            $reward_products = $this->coupon_model->get_discount_prdoucts($row->coupon_id, 'reward');
                            echo "Order amount >=" . $row->discount."<br/>the reward is: ". ifvalue($reward_products, "discount_products");    
                        break;     
                    }
                    ?>
                </td>                    
                <td class="center">
                    <input type="checkbox" name="coupontodelete[]" value="<?php echo $row->coupon_id;?>" />
                </td>                
            </tr>                      
    <?php 
        } 
    } 
    ?>
</table>