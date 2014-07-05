<input type="hidden" value="<? print ceil($pages_no)?>" id="pages_no" />
    <table cellspacing="0" class="cmstable" > 
			<tr>			
				<th><a href="u.first_name">Name</a></th>
                <th><a href="u.company_name">Company</a></th>
                <th><a href="s.name">State</a></th>
                <th><a href="ut.type">Type</a></th>
                <th><a href="adv.first_name">Advisor</a></th>
                <th><a href="own.first_name">Partner</a></th>
                <th><a href="Floor((NOW() - u.last_logged_dtm) / 86400)">Login Days</a></th>
                <th><a href="u.disabled_date">Disabled Date</a></th>
                <th><a href="u.enabled">Enabled</a></th>                   				
				<th style="width: 20px;">Delete</th> 
			</tr>
 		<? /* Setup alternating row colours, using the variable "rowclass" */ 
		$i = 0;
		if($users)
		{
			foreach($users->result() as $user)
			{
				if($i++ % 2==1) $rowclass = "admintablerow";
				else  $rowclass = "admintablerowalt";
			?> 
				<tr class="<? print $rowclass;?>">
					<td class="admintabletextcell"><a href="<?php print base_url();?>admin/usermanager/user/<? print $user->user_id;?>"><?php print $user->first_name;?> <?php print $user->last_name;?></a></td>
                    <td class="admintabletextcell"><? print $user->company_name;?></a></td>
					<td class="admintabletextcell"><?php print $user->billing_state;?></td>				     
                    <td class="admintabletextcell"><?php print $user->type;?></td>                                   
                    <td class="admintabletextcell"><?php print (!empty($user->advisor)) ? $user->advisor : "Admin";?></td>
                    <td class="admintabletextcell"><?php print (!empty($user->owner)) ? $user->owner : "NA";?></td>
                    <td class="admintabletextcell"><?php print ($user->days_since_login < 700) ? $user->days_since_login : "NA";?></td>
                    <td class="admintabletextcell"><?php print (!empty($user->disabled_date)) ? date("d/m/Y", strtotime($user->disabled_date)) : "NA";?></td>                                   
                    <td class="admintabletextcell"><?php print ($user->enabled == '1') ? "Yes" : "No";?></td>
					<td class="center"><input type="checkbox" name="itemstodelete[]" value="<?php print $user->user_id;?>" /></td>
				</tr>          
			<?
			}
		}
		?>
   </table>
