<div class="intro">
    <div class="left">
        <img src="<?=base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url();?>admin/menu">Back to Menu</a>          
    </div>
    <div class="right">
        <? if(isset($side) && $side == "top"): ?>

            <label for="search_period" class="left">Period:&nbsp;</label>
            <select id = "search_period" name="search_period" class="left">
                <option value="all" <?php if(isset($selected_search_period) && $selected_search_period == "all") echo "selected='selected'"; ?>>All</option>
                <option value="today" <?php if(isset($selected_search_period) && $selected_search_period == "today") echo "selected='selected'"; ?>>Today</option>
                <option value="yesterday" <?php if(isset($selected_search_period) && $selected_search_period == "yesterday") echo "selected='selected'"; ?>>Yesterday</option>
                <option value="week_to_date" <?php if(isset($selected_search_period) && $selected_search_period == "week_to_date") echo "selected='selected'"; ?>>This week</option>
                <option value="month_to_date" <?php if(isset($selected_search_period) && $selected_search_period == "month_to_date") echo "selected='selected'"; ?>>This Month</option>
                <option value="this_quarter" <?php if(isset($selected_search_period) &&$selected_search_period == "this_quarter") echo "selected='selected'"; ?>>This Quarter</option>
                <option value="choose" <?php if(isset($selected_search_period) && $selected_search_period == "choose") echo "selected='selected'"; ?>>Pick</option>
            </select>
            
            <label for ="user_search" class="left" style="margin-left: 50px">Partner:&nbsp;</label>
            <input type="text" id="user_search" class="box" />

            <input type="button" class="button" value="Search" id="reservation_search"/>
            <div class="clear"></div>
            
            <div id="choose_date" class="<?php echo (isset($selected_search_period) && $selected_search_period == "choose") ? '' : 'hidden'; ?>">
                <label for ="from_date" class="left">&nbsp;&nbsp;From:&nbsp;</label><input name="from_date" id="from_date" class="date-pick" />
                <label for ="to_date" class="left">To:&nbsp;</label><input name="to_date" id="to_date" class="date-pick" />

                <div class="clear"></div>
            </div>    
        <? endif;?>
    </div>    
    <div class="clear"></div>
</div>