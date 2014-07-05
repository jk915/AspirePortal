<input type="hidden" value="<?php echo ceil($pages_no); ?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>ID</th>                            
                <th>Website Name</th>                            
                <th>URL ID</th>                            
                <th style="width: 20px;">Delete</th> 
            </tr>
        <?php         
        $i = 0;
        if($websites)
        {
            foreach($websites->result() as $row)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    
                    <td class="admintabletextcell"><?php echo $row->website_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/websitemanager/website/<?php echo $row->website_id;?>"><?php echo $row->website_name;?></a></td>
                    <td class="admintabletextcell"><?php echo $row->url_id;?></td>
                    <td class="center"><input type="checkbox" name="websitetodelete[]" value="<?php echo $row->website_id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>