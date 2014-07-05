<table cellspacing="0" class="cmstable">
    <tr>
        <th>Partners/Agents</th> 
        <th>No. Reservations</th>                                    
        <th>No. Sales</th>
        <th>Last Reservation Date</th>                                    
    </tr>    
    <?php
    $i = 0;
    if($summary_table)
    {
        foreach($summary_table->result() as $row)
        {
            $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $row->first_name . " " . $row->last_name;?></td>
                    <td class="admintabletextcell"><?php echo $row->reservation_no;?></td>
                    <td class="admintabletextcell"><?php echo $row->sales_no;?></td>
                    <td class="admintabletextcell"><?php echo $row->last_reservation_date;?></td>                    
                </tr>          
            <?
        }
    }
    ?>
</table>