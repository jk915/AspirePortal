<input type="hidden" value="<?php echo ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>ID</th>
				<th>Company Name</th> 	
                <th>Business Address</th>                            
                <th>Phone</th>
                <th>Fax</th>
				<th  width="100"># Transaction</th>
                <th>Enabled</th>
				<th>Delete</th>
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        
		
		$i = 0;
        if($builders)
        {
          
			foreach($builders->result() as $builder)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $builder->contacts_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/contactsmanager/contact/<?php echo $builder->contacts_id;?>"><?php echo $builder->company_name;?></a></td>
					<td class="admintabletextcell"><?php echo $builder->billing_address1.' '.$builder->billing_suburb.' '.$builder->billing_postcode;?></td>
                    
                    <td class="admintabletextcell" style="text-align:center"> <?php print(!empty($builder->billing_phone)) ? $builder->billing_phone : 'N/A'?> </td>
					<td class="admintabletextcell" style="text-align:center"><?php print(!empty($builder->billing_fax)) ? $builder->billing_fax : 'N/A'?></td>
					<td class="admintabletextcell" align="center"><?php echo $builder->num_transactions; ?></td>
					<td class="admintabletextcell" align="center"><?php print ($builder->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="center"><input type="checkbox" name="builderstodelete[]" value="<?php echo $builder->contacts_id;?>" /></td>
                </tr>          
            <?
            }
        }
        
		?>
</table>