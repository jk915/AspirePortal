<input type="hidden" value="<?php echo ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>Property</th> 
                <th>Partner Name</th>                            
                <th>Phone</th>
                <th>Date Reserved</th>                
                <th style="width: 20px;">Unreserve</th> 
                <th>Sold</th>
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($reservations)
        {
            foreach($reservations->result() as $row)
            {
                $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";                
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><a href="<?php echo base_url() . "admin/propertymanager/property/" . $row->property_id; ?>"><?php echo $row->project_name ." - ". $row->property;?></a></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url() . "admin/usermanager/user/" .$row->user_id;?>"><?php echo $row->contact_name;?></a></td>
                    <td class="admintabletextcell"><?php echo $row->phone;?></td>
                    <td class="admintabletextcell"><?php echo $row->reservation_date;?></td>
                    <td class="center"><input type="checkbox" name="reservationstodelete[]" value="<?php echo $row->property_id;?>" /></td>
                    <td class="admintabletextcell">
                    <?php if(!$row->sold) : ?>
                    	<a class="sold" href="<?php echo $row->reservation_id; ?>">Flag as sold</a>
                    <?php else: ?>
                    	Has been sold
                    <?php endif; ?>
                    </td>
                </tr>          
            <?
            }
        }
        ?>
</table>