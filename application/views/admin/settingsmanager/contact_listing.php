<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>First Name</th>                            
                <th>Last Name</th>
                <th>Email</th>
                <th>Contact Notification</th>
                <th>Order Notification</th>                
                <th style="width: 20px;">Delete</th> 
            </tr>
        <?php /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($contacts)
        {
            foreach($contacts->result() as $row)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $row->first_name;?></td>
                    <td class="admintabletextcell"><?php echo $row->last_name;?></td>
                    <td class="admintabletextcell"><?php echo $row->email;?></td>
                    <td class="admintabletextcell"><input type="checkbox" name="contact_notification[]" value = "<?php echo $row->id;?>" <?php echo (isset($row->contact_notification) && $row->contact_notification == '1') ? "checked='yes'" : "";?> /></td>
                    <td class="admintabletextcell"><input type="checkbox" name="order_notification[]" value = "<?php echo $row->id;?>" <?php echo (isset($row->order_notification) && $row->order_notification == '1') ? "checked='yes'" : "";?> /></td>
                    <td class="center"><input type="checkbox" name="contactstodelete[]" value="<?php echo $row->id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>
