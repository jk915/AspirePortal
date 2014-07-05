<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>ID</th>                            
                <th>Region Name</th>                            
                <th>Enabled</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
        <?php         
        $i = 0;
        if($regions)
        {
            foreach($regions->result() as $row)
            {
                $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";                
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    
                    <td class="admintabletextcell"><?php echo $row->region_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>websitemanager/region/<?php echo $row->region_id;?>"><?php echo $row->region_name;?></a></td>
                    <td class="admintabletextcell"><?=($row->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="center"><input type="checkbox" name="regiontodelete[]" value="<?php echo $row->region_id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>