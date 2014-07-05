<p class="intro">
    <img src="<?=base_url();?>images/admin/i_pages.gif" border="0" width="16" height="18" alt="Article Manager" />&nbsp;<a href="<?=base_url()?>admin/contentmanager<?php if(isset($category_id)) echo "/category/$category_id"; ?>">Back to Content Manager</a><span class="divider">|</span>  
    <img src="<?=base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url()?>admin/menu">Back to Menu</a>  <span class="divider">|</span>  
    <img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add page." /> <a href="<?=base_url()?>admin/contentmanager/add/<?php echo isset($category_id) ? $category_id : ""; ;?>">Add New Item</a>
</p>