<? if (!isset($expand)): ?>
<table cellspacing="0" class="cmstable" >         
<tr>
    <th>Title</th>
    <th>Seq No</th>
    <th>Link</th>            
    <th style="width: 20px;">Enabled</th>
    <th> Delete</th>                
</tr>
<? endif; ?>

<? 
$i = 0;
$trstyle = (isset($expand) && $expand=="1") ? " style='display:none' " : "";
$trlevel = (isset($level)) ? $level : 0;


if($menu_items)
{
    foreach($menu_items->result() as $item)
    {
        
        $expanded = (isset($expand) && $expand=="1") ? " expandedchild_".$item->parent_id : "";
        
        if($i++ % 2==1) 
            $rowclass = "admintablerow".$expanded;
        else  
            $rowclass = "admintablerowalt".$expanded;
            
        //$sign = ($item->submenus>0)? '[+]':'';
        $sign = "&nbsp;&nbsp;&nbsp;";
        
        $indent = str_repeat("&nbsp;&nbsp;",$trlevel);
        
        if($sign=='')  $sign = ($item->parent_id != -1)? '--' : '';
        ?> 
            <tr class="<?=$rowclass;?>" <?=$trstyle?> >
                <td class="admintabletextcell">
                    <?=$indent?>
                    <? if ($item->submenus>0) :?>
                        <a href="<?=$item->menu_item_id;?>|<?=$trlevel?>" class="expand"><?=$sign;?></a>
                    <? endif; ?>
                    
                <a href="<?=$item->menu_item_id;?>" class="edit_menuitem">
                    <? if ($item->submenus == 0 && $item->parent_id != -1) : ?>
                        <img src="<?=base_url()?>/images/admin/arrow.gif"/> 
                    <? endif; ?>
                    <?php echo htmlspecialchars($item->menu_item_name);?>
                </a>
                </td>
                <td class="admintabletextcell"><?php echo $item->menu_order;?></td>
                <td class="admintabletextcell"><?php echo $item->link;?></td>                
                <td class="center"><?php echo ($item->enabled == '1') ? "Yes" : "No";?></td>
                <td class="center"><input type="checkbox" name="itemstodelete[]" value="<?php echo $item->menu_item_id;?>" /></td>                
            </tr>                  
        <?
    }
}
?>

<? if (!isset($expand)): ?>
</table>
<? endif; ?>

        

