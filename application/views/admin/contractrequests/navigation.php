<div class="intro" style="height:23px">
    <div class="left">
        <img src="<?=base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url();?>admin/menu">Back to Menu</a>  
        <!--<span class="divider">|</span>  
        <img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add request." /> <a href="<?=base_url();?>admin/contractrequests/request">Add New Request</a>-->
    </div>
    <div class="right">
        <?php echo form_dropdown_contract_requests_status("",set_value("status","Pending"),"style='width:120px;padding:2px' id='stt'",true);?>
        <input type="text" id="agent_name" value="Enter Agent Name" onblur="if(this.value=='') this.value='Enter Agent Name';" onfocus="if(this.value=='Enter Agent Name') this.value='';" style="width:150px;" />
        <!--<input type="text" id="date_start" value="Request Date From" onblur="if(this.value=='') this.value='Request Date From';" onfocus="if(this.value=='Request Date From') this.value='';" readonly style="width:150px;" />
        <input type="text" id="date_end" value="Request Date To" onblur="if(this.value=='') this.value='Request Date To';" onfocus="if(this.value=='Request Date To') this.value='';" readonly style="width:150px;" />-->
        <input type="button" value="Search >>" id="apply_filter" style="width:80px;padding:2px"/>
    </div>    
    <div class="clear"></div>
</div>