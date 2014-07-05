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
        if($panel_contacts)
        {
          
			foreach($panel_contacts->result() as $panel_contact)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $panel_contact->contacts_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/contactsmanager/contact/<?php echo $panel_contact->contacts_id;?>"><?php echo $panel_contact->contacts_name;?></a></td>
					<td class="admintabletextcell"><?php echo $panel_contact->billing_address1.' '.$panel_contact->billing_suburb.' '.$panel_contact->billing_postcode; ?></td>
                    
                    <td class="admintabletextcell" style="text-align:center"> <?php print(!empty($panel_contact->billing_phone)) ? $panel_contact->billing_phone : 'N/A'?> </td>
					<td class="admintabletextcell" style="text-align:center"><?php print(!empty($panel_contact->billing_fax)) ? $panel_contact->billing_fax : 'N/A'?></td>
					<td class="admintabletextcell" align="center"><?php echo $panel_contact->num_transactions; ?></td>
					<td class="admintabletextcell" align="center"><?php print ($panel_contact->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="center"><input type="checkbox" name="panel_contactstodelete[]" value="<?php echo $panel_contact->contacts_id;?>" /></td>
                </tr>          
            <?
            }
        }
        
		?>
</table>