<input type="hidden" value="<?php echo ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>ID</th> 
                <th>Builder Name</th>                            
                <th>Enabled</th>
                <th>Last Modified</th>
                <th style="width: 20px;">Delete</th> 
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
                    <td class="admintabletextcell"><?php echo $builder->builder_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/buildermanager/builder/<?php echo $builder->builder_id;?>"><?php echo $builder->builder_name;?></a></td>
                    <td class="admintabletextcell"><?php echo ($builder->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="admintabletextcell"><?php echo $this->utilities->isodatetime_to_ukdate($builder->last_modified); ?></td>
                    <td class="center"><input type="checkbox" name="builderstodelete[]" value="<?php echo $builder->builder_id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>