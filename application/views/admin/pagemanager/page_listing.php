<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>ID</th>
                <th>Page Name</th>                            
                <th>Page Code</th>
                <th>Enabled</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($pages)
        {
            foreach($pages->result() as $page)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?=$rowclass;?>">
                    <td class="admintabletextcell"><?php echo $page->page_id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>pagemanager/page/<?php echo $page->page_id;?>"><?php echo htmlspecialchars($page->page_title);?></a></td>
                    <td class="admintabletextcell"><?php echo $page->page_code;?></td>
                    <td class="admintabletextcell"><?php echo ($page->enabled == '1') ? "Yes" : "No";?></td>
                    <td class="center"><input type="checkbox" name="pagestodelete[]" value="<?php echo $page->page_id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>