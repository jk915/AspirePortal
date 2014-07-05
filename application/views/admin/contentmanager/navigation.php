<div class="intro">
    <div class="left">
        <img src="<?php echo base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url()?>admin/menu">Back to Menu</a>
        <span class="divider">|</span>  
        <img src="<?php echo base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add page." /> <a href="<?=base_url()?>admin/contentmanager/add/<?php echo isset($category_id) ? $category_id : "";?>">Add New Item</a>
    </div>
    
    <div class="right" style="margin-left: 60px;">
        <? if(isset($side) && $side == "top"): ?>
        <div class="left">
	        <input type="text" id="article_search" class="box" />
	        <input type="button" value="Go" id="article_search_button" class="button"/>
	    </div>
	    <div class="left">
        	<?php //$this->load->view("admin/websites"); ?>
        </div>
        <div class="clear"></div>
        
        <? endif; ?>
    </div> 
       
    <div class="clear"></div>        
</div>