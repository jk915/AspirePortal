<table cellspacing="0" class="cmstable" >         
<tr>
    <th>Title</th>            
    <th>Created</th>
    <th>Last Modified</th>
    <th style="width: 20px;">Enabled</th>    
    <th> Delete</th>                
</tr>
<? 
$i = 0;

if($articles)
{
    foreach($articles->result() as $item)
    {
        
        if($i++ % 2==1) 
            $rowclass = "admintablerow";
        else  
            $rowclass = "admintablerowalt";
            
        ?> 
            <tr class="<?=$rowclass;?>">
                <td class="admintabletextcell">
                    <a href="<?=$item->article_id;?>" class="edit_menuitem">
                        <?=$item->article_title;?>
                    </a>
                </td>
                <td><?=$item->created;?></td>
                <td><?=$item->last_modification;?></td>
                <td class="center"><?=($item->enabled == '1') ? "Yes" : "No";?></td>                
                <td class="center"><input type="checkbox" name="itemstodelete[]" value="<?=$item->article_id;?>" /></td>                
            </tr>                  
        <?
    }
}
?>
</table>