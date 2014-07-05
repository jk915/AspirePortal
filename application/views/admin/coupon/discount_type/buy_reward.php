<div class="left">    
    <label for="products">Products</label>
    <?php echo form_dropdown( 'product_id', query2array( $products, 'product_id', 'product_name' ), ifvalue($products, 'product_id', ''), 'id="products" size="18"' ); ?>                                                        
</div>

<div class="left" style="margin-left: 15px;">
    <div class="buy_div">
        <div class="left">
            <input type="button" value="Add &gt;&gt;" class="button" style="margin: 65px 15px 0 0;"/>
        </div>
        <div class="right">
            <div id="select_product_div" style="display: none;">
                <label for="buy">If they buy:</label>
                <select multiple="multiple" id="buy" size="6">
                    <?php 
                    if(isset($buy_products) && $buy_products)
                    {
                        foreach($buy_products as $row)    
                        {
                            ?>
                            <option value="<?php echo $row->discount_product_id;?>"><?php echo $row->dicount_product_name;?></option>
                            <?php
                        }
                    }
                    ?>    
                </select>
                <br/>
                <input type="button" value="&lt;&lt; Remove" class="button" />                     
            </div>
            <div id="enter_amount_div" style="display: none;">
                <label for="order_amount">Order amount &gt;=:</label>                
                <input type="text" id="order_amount" class="" value="<?php echo ifvalue( $coupon, "discount");?>" />
            </div>    
        </div>
        <div class="clear"></div>    
    </div>
    
    <div class="reward_div">
        <div class="left">
            <input type="button" value="Add &gt;&gt;" class="button" style="margin: 65px 20px 0 0;" />
        </div>
        <div class="right">
            <label for="reward">the reward is:</label>
            <select multiple="multiple" id="reward" size="6">
                <?php 
                if(isset($reward_products) && $reward_products)
                {
                    foreach($reward_products as $row)    
                    {
                        ?>
                        <option value="<?php echo $row->discount_product_id;?>"><?php echo $row->dicount_product_name;?></option>
                        <?php
                    }
                }
                ?>    
            </select>
            <br/>
            <input type="button" value="&lt;&lt; Remove" class="button" />                     
        </div>
        <div class="clear"></div>    
    </div>
</div>
<div class="clear"></div>