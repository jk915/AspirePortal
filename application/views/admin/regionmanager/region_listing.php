<input type="hidden" value="<?php echo ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th class="sortable" sort="nc_areas.area_id"><a href="javascript:;">ID</a></th> 
                <th class="sortable" sort="nc_areas.area_name"><a href="javascript:;">Region Name</a></th>
                <th>Enabled</th>
                <th>Last Modified</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($regions)
        {
            foreach($regions->result() as $region)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $region->region_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/regionmanager/region/<?php echo $region->region_id;?>"><?php echo $region->region_name;?></a></td>
                    
                    <td class="admintabletextcell"><?php echo ($region->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="admintabletextcell"><?php echo $this->utilities->isodatetime_to_ukdate($region->last_modified); ?></td>
                    <td class="center"><input type="checkbox" name="regionstodelete[]" value="<?php echo $region->region_id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>
<input type="hidden" id="sort_col" name="sort_col" value="" />
<input type="hidden" id="sort_dir" name="sort_dir" value="" />