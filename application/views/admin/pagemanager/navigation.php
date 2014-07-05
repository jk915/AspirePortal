<div class="intro" style="height:23px">
    <div class="left">
        <img src="<?php echo base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url();?>menu">Back to Menu</a>
	    <span class="divider">|</span>  
	    <img src="<?php echo base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add page." /> <a href="<?=base_url();?>pagemanager/page">Add New Page</a>
    </div>
    <div class="right">
        <?php if(isset($side) && $side == "top")
           {
        ?>
                <input type="text" id="page_search" class="box" />
                <input type="button" value="Go" id="page_search_button" class="button"/>
                <?php
                if($websites && $websites->num_rows()>0)
                {
                    ?>
                    <select id="website_search">
                        <option value="">Choose a Website</option>
                        <?php echo $this->utilities->print_select_options($websites, "website_id", "website_name"); ?>
                    </select>
                    <?php
                }
                ?>
        
        <? } ?>
    </div>    
    <div class="clear"></div>    
</div>
