<input type="hidden" value="<?php echo ceil($this->data["pages_no"])?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>ID</th>
                <th>Created Date</th>                            
                <th>Customer Name</th>
                <th>Status</th>
                <th>Total Amount</th>                               
                <th>Order</th>                
				<th style="width: 20px;">Delete</th> 
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($this->data["orders"])
        {
            foreach($this->data["orders"]->result() as $row)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>ordermanager/order/<?php echo $row->id;?>"><?php echo $row->id;?></a></td>                    
                    <td class="admintabletextcell"><?php echo $row->order_date;?></td>                                    
                    <td class="admintabletextcell"><?php echo $row->first_name.' '.$row->last_name;?></td>
                    <td class="admintabletextcell"><?php echo $row->order_status;?></td>
                    <td class="admintabletextcell"><?php echo "$".$row->order_total;?></td>
                    <td class="center">
                    <?php if($row->invoice != ""){ ?>
                        <a class="download" href="<?php echo $row->invoice; ?>" title="<?php echo $row->invoice; ?>"><img alt="i_PDF.png" src="<?php echo base_url();?>images/i_PDF.png"></a>
                    <?php } ?>    
                    </td>                    
                    <td class="center"><input type="checkbox" name="orderstodelete[]" value="<?php echo $row->id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>
