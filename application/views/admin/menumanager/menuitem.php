<div class="w_caption" style="background-color: #CCC;height:23px"><a href="javascript:void(0);" class="w_close"><!-- --></a><span class="w_captionText" id="_wicket_window_12"><b>Menu Item Detail</b></span></div>

<form class="plain" id="frmMenuItem" name="frmMenuItem" method="post">
<input type="hidden" id="menu_item_id" name="menu_item_id" value="<?=$menu_item_id;?>" />
<div style="text-align: left;padding-left: 50px; padding-right: 50px;">
    
    <div class="left">
        <br/>
        <label for="menu_item_name">Menu Item Name:<span class="requiredindicator">*</span></label>
        <input id="menu_item_name" class="required" type="text" value="<?=($menu_item_id !="") ? $menu_item->menu_item_name : $menu_item_name;?>" name="menu_item_name"/>
         
        <?php /*         
        <label for="link">Link URL:<span class="requiredindicator">*</span></label>
        <input id="link" class="required" type="text" value="<?=($menu_item_id !="") ? $menu_item->link : "";?>" name="link"/>
        */ ?>         
        <label for="parent_id">Parent Page:</label>
        <select id="parent_id" name="parent_id">               
            <option value="-1">None</option>
            <? if($parents) :?>
                <?=$this->utilities->print_select_options($parents,"menu_item_id","menu_item_name",($menu_item_id !="") ? $menu_item->parent_id : ""); ?>
            <? endif; ?>
        </select>
        
        <label for="link_type">Link Type:*</label>
        <?php echo form_dropdown('link_type', $link_type, ($menu_item_id != "") ? $menu_item->link_type : "", 'id="link_type"'); ?>
        
        <label for="link_to">Link To:*</label>
        
        <select id="link_to" name="link_to">
            <?php 
            if(isset($link_to)) 
                echo $link_to ;
            else
            {
            ?>
            <option value="-1">Choose</option>
            <?php
            }
            ?>
        </select>
        
        <input id="link" name="link" type="text" value="<?=($menu_item_id !="") ? $menu_item->link : "";?>" />
                 
        <label for="class">Class / ID (leave this blank unless you're a coder):</label>
        <input id="class" type="text" value="<?=($menu_item_id !="") ? $menu_item->class : "";?>" name="class"/>
        
        <!--      
        <label for="category_id">Load child items from:</label>          
        <select id="category_id" name="category_id">
            <option value="-1">Choose</option>
            <?php 
            /*if($category_articles)     
            {
                foreach($category_articles->result() as $cat)
                {
                    ?>
                    <option value="<?php echo $cat->category_id;?>" <?php echo (($menu_item_id !="") && ($cat->category_id == $menu_item->category_id)) ? "selected" : ""; ?> ><?php echo $cat->name;?></option>
                    <?php
                }
            }*/
            ?>
        </select>
         -->
         
        <label for="class">Sequence Order</label>
        <input id="class" type="text" value="<?php echo ($menu_item_id !="") ? $menu_item->menu_order : "";?>" name="menu_order"/>    
        <br/>
        
        <div class="clear"></div> 
        
         <div class="show_error">
    	</div>
    </div>
    
    <div class="left">    
        <label>&nbsp;</label>
        <input id="enabled" type="checkbox" value="1" name="enabled" class="left" <? echo ($menu_item_id !="") ? (($menu_item->enabled == 1) ? "checked" :"") : "checked" ?> />
        <label class="left" style="padding-top: 0px;" for="enabled">&nbsp;Menu Item is enabled</label>
    </div>
    
    
    
    
    <div class="clear"></div>
    <br/>
    
    <div>
        <input class="button" type="button" value="Save" id="save_menu_item" />
    </div>
    
</div>
</form>

<script type="text/javascript">
$(document).ready(function()
{
	$("#link_type").bind("change", function()
	{
		setLinkType();
	});
	
	setLinkType();
});

function setLinkType()
{
	var link_type = $("#link_type").val();
	
	if(link_type == "external")
	{
		$("#link_to").hide();	
		$("#link").show();
	}
	else
	{
		$("#link").hide();
		$("#link_to").show();
	}
}
</script>