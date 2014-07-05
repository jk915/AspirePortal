<input type="hidden" value="<?php echo ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th class="sortable" sort="p.project_id"><a href="javascript:;">ID</a></th> 
                <th class="sortable" sort="p.project_name"><a href="javascript:;">Project Name</a></th>
                <th class="sortable" sort="area_name"><a href="javascript:;">Area</a></th>
                <th class="sortable" sort="state"><a href="javascript:;">State</a></th>
                <th class="sortable" sort="p.rate"><a href="javascript:;">Risk</a></th> 
                <th class="sortable" sort="p.is_featured"><a href="javascript:;">Featured</a></th>
                <th class="sortable" sort="p.ck_newsletter"><a href="javascript:;">Newsletter</a></th>                           
                <th>Active</th>
                <th style="width: 20px;"> </th> 
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($projects)
        {
            foreach($projects->result() as $proj)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    <td class="admintabletextcell"><?php echo $proj->project_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/projectmanager/project/<?php echo $proj->project_id;?>"><?php echo $proj->project_name;?></a></td>
                    <td class="admintabletextcell"><?php echo $proj->area_name;?></td>
                    <td class="admintabletextcell"><?php echo $proj->state;?></td>
                    <td class="admintabletextcell"><?php echo $proj->rate;?></td>
                    <td class="admintabletextcell"><?php echo ($proj->is_featured == '1') ? "Yes" : "No";?></td>
                    <td class="admintabletextcell"><?php echo ($proj->ck_newsletter == '1') ? "Yes" : "No";?></td>
                    <td class="admintabletextcell"><?php echo ($proj->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="center"><input type="checkbox" name="projectstodelete[]" value="<?php echo $proj->project_id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>
<input type="hidden" id="sort_col" name="sort_col" value="" />
<input type="hidden" id="sort_dir" name="sort_dir" value="" />