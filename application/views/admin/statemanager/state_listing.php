<input type="hidden" value="<?php echo ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th class="sortable" sort="nc_region_states.state_id"><a href="javascript:;">ID</a></th> 
                <th class="sortable" sort="nc_region_states.state_name"><a href="javascript:;">State Name</a></th>
                <th>Enabled</th>
                <th>Last Modified</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($states)
        {
            foreach($states->result() as $state)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $state->state_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/statemanager/state/<?php echo $state->state_id;?>"><?php echo $state->state_name;?></a></td>
                    
                    <td class="admintabletextcell"><?php echo ($state->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="admintabletextcell"><?php echo $this->utilities->isodatetime_to_ukdate($state->last_modified); ?></td>
                    <td class="center"><input type="checkbox" name="statestodelete[]" value="<?php echo $state->state_id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>
<input type="hidden" id="sort_col" name="sort_col" value="" />
<input type="hidden" id="sort_dir" name="sort_dir" value="" />