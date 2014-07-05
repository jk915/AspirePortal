    <h2>My Orders</h2>
    
    <?php if((isset($user_orders)) && ($user_orders)) : ?>    
    <table id="tblMyOrders" style="width: 100% !important">
        <tr>
            <th>Order ID</th>
            <th>Order Date</th>
            <th>Order Amount</th>
            <th>Invoice</th>
        </tr>        
        <?php foreach($user_orders->result() as $row) : ?>
        <tr>
            <td><?php echo $row->id; ?></td>
            <td><?php echo ( isset($post) && ( (ifvalue($post, 'country') == US_COUNTRY_ID) || (ifvalue($post, 'country') == US_MINOR_COUNTRY_ID)) ) ?
              date("m/d/Y h:i A", strtotime($row->created_date))
            : date("d/m/Y h:i A", strtotime($row->created_date)); ?></td>
            <td><?php echo $row->order_total; ?></td>
            <td>
                <?php if($row->invoice != "") :?>
                <a class="download" href="<?php echo $row->id; ?>" title="Download">Download</a>
                <?php endif; ?>
            </td>
        </tr>
        <?php endforeach; ?>               
            
    </table>
    <div>
            
        <div class="orderhistory_back_btn" <?php if( ($page_num - 1) <= 0 ) echo "style='display:none'";?>>
            <input type="button" value="&lt;&lt Back" class="left" />
        </div>   
        
        <div class="orderhistory_next_btn" <?php if( $page_num  >= $max_page_num) echo "style='display:none'";?>>
            <input type="button" value="Next &gt;&gt;" class="right" />            
        </div>         
        <input type="hidden" id="page_num" value="<?php echo $page_num;?>" /> 
        <div class="clear"></div>
    </div>    
    <?php else: ?>
    <p>You have not made any orders yet.</p>
    <?php endif; ?>