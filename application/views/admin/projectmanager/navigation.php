<div class="intro" style="height:23px">
    <div class="left">
        <img src="<?=base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url();?>admin/menu">Back to Menu</a>  
        <span class="divider">|</span>  
        <img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add project." /> <a href="<?=base_url();?>admin/projectmanager/project">Add New Project</a>
    </div>
    <div class="right">
        <? if(isset($side) && $side == "top"): ?>

            <input type="text" id="project_search" class="box" />

        <? endif;?>
    </div>    
    <div class="clear"></div>
</div>