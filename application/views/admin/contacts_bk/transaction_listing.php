<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<input type="hidden" value="<?=count($property_lawyers)?>" id="files_no" />

<table cellspacing="0" class="cmstable" > 
            <tr>
                <th style="text-align:center">Property Address</th>  
                <th style="text-align:center">Seller</th>
                <th style="text-align:center">Purchaser</th>
                <th style="text-align:center">Date Reserved</th>
				<th style="text-align:center">Status</th>		
            </tr>
<?php /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if($property_lawyers) 

{
	foreach($property_lawyers->result() as $property_lawyers)
    {
		$reserved_date = $property_lawyers->reserved_date;
		$reserved_date = date("d-m-Y", strtotime($reserved_date));
		if($property_lawyers->status == 'reserved' || $property_lawyers->status == 'EOI Payment Pending' || $property_lawyers->status == 'signed' || $property_lawyers->status == 'sold')
		{
?>	
        <tr>
                    <td class="admintabletextcell center"><?php echo $property_lawyers->address;?></td>
					<td class="admintabletextcell center"> 
						<a href="<?php print base_url();?>admin/usermanager/user/<? print $property_lawyers->user_id;?>"><?php echo (!empty($property_lawyers->first_name)) ? $property_lawyers->first_name.' '.$property_lawyers->last_name : 'N/A' ?></a> 
					</td>
					<td class="admintabletextcell center"> 
						<?php echo (!empty($property_lawyers->reserved_first_name)) ? $property_lawyers->reserved_first_name.' '.$property_lawyers->reserved_last_name : 'N/A' ?></a> 
					</td>
                    
					<td class="admintabletextcell center"><?php echo $reserved_date;?></td>
					<td class="admintabletextcell center"><?php echo $property_lawyers->status;?></td>
                </tr>                  
            <?php
			}
        }
   
}
        ?>
</table>