<input type="hidden" value="<? print ceil($pages_no)?>" id="pages_no" />

    <table cellspacing="0" class="cmstable" > 

			<tr>

                <th>ID</th>
                
				<th>Lead Name</th>			

				<th>Agent Name</th>

                <th>Created Date</th>

				<th style="width: 20px;">Delete</th> 

			</tr>

 		<? /* Setup alternating row colours, using the variable "rowclass" */ 

		$i = 0;

		if($leads)

		{

			foreach($leads->result() as $lead)

			{

				if($i++ % 2==1) $rowclass = "admintablerow";

				else  $rowclass = "admintablerowalt";

			?> 

				<tr class="<? print $rowclass;?>">

					<td class="admintabletextcell"><a href="<?php print base_url();?>admin/leadsmanager/lead/<? print $lead->id;?>"><? print $lead->id;?></a></td>

					<td class="admintabletextcell"><a href="<?php print base_url();?>admin/leadsmanager/lead/<? print $lead->id;?>"><?php print $lead->first_name;?> <?php print $lead->last_name;?></a></td>

					<td class="admintabletextcell"><?php print $lead->agent_first_name;?> <?php print $lead->agent_last_name;?></td>
                    
                    <td class="admintabletextcell"></td>				                    			

					<td class="center"><input type="checkbox" name="itemstodelete[]" value="<?php print $lead->id;?>" /></td>

				</tr>          

			<?

			}

		}

		?>

   </table>

