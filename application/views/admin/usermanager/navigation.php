<div class="intro" style="height:23px">   
    <div class="left">
        <img src="<?=base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url();?>admin/menu">Back to Menu</a>  
	    <span class="divider">|</span>  
	    <img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add user." /> <a href="<?=base_url();?>admin/usermanager/user">Add New User</a>
    </div><br /><br /><br />
    <div class="left">
        
        <?php 
        if(isset($side) && $side == "top")
        { 
            
			if($states && $states->num_rows()>0)
            {
                ?>
                <select id="state_type_search" style="width: 140px;">
                    <option value="">View State: All</option>
                    <?php echo $this->utilities->print_select_options($states, "state_id", "name"); ?>
                </select>
                <?php
            }
			
			if($user_types && $user_types->num_rows()>0)
            {
                ?>
                <select id="user_type_search" style="width: 140px;">
                    <option value="">User Type: All</option>
                    <?php echo $this->utilities->print_select_options($user_types, "user_type_id", "type"); ?>
                </select>
                <?php
            }
            
            echo form_dropdown_advisors("", "advisor_id_search", "", 'id="advisor_id_search" style="width:200px;"', "Advisor: All");
            
            ?>
            <input type="text" id="user_search" class="box" style="width: 140px;" />        
        
        <?php
        }
        ?>
    </div>  
    <div class="clear"></div>
</div>
<div class="clear" style="height: 20px;"></div>
