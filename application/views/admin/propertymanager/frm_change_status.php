<div id="frm_change_status" style="height:280px;">

<?php if ($property) : ?>

    <div style="font-size:15px;font-weight:bold;color:#8B0304" class="property_title">
        <?php echo trim("$property->lot $property->address $property->suburb")?>
    </div>
    
    <label for="status">Change Status To:<span class="requiredindicator">*</span></label>           
    
    <select class="status" name="status">
    <?php foreach ($status as $key => $value) : ?>
        <option value="<?php echo $key?>"<?php echo ($key==$property->status) ? ' selected="selected"' : ''?>><?php echo $value?></option>
    <?php endforeach; ?>
    </select>
    
    <div class="status_user_area" <?php echo ($property->status == 'available') ? 'style="display:none;"' : '';?>>
    
        <label for="advisor_id">Advisor: <span class="requiredindicator">*</span></label>
        <select name="advisor_id" class="advisor_id">
            <option value="-1">- Select Advisor</option>
            <?php if ($advisors) : ?>
                <?php foreach ($advisors->result() AS $advisor) : ?>
                    <option value="<?php echo $advisor->user_id?>" <?php echo ($property->advisor_id == $advisor->user_id) ? 'selected="seleted"' : '' ?>>
                        <?php echo trim($advisor->first_name.' '.$advisor->last_name)?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        
        <label for="partner_id">Partner:</label>
        <select name="partner_id" class="partner_id">
            <?php if ($partners) : ?>
                <?php foreach ($partners->result() AS $partner) : ?>
                    <option value="<?php echo $partner->user_id?>" <?php echo ($property->advisor_id == $partner->user_id) ? 'selected="seleted"' : '' ?>>
                        <?php echo trim($partner->first_name.' '.$partner->last_name)?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        
        <label for="investor_id">Investor:</label>
        <select name="investor_id" class="investor_id">
            <?php if ($investors) : ?>
                <?php foreach ($investors->result() AS $investor) : ?>
                    <option value="<?php echo $investor->user_id?>" <?php echo ($property->advisor_id == $investor->user_id) ? 'selected="seleted"' : '' ?>>
                        <?php echo trim($investor->first_name.' '.$investor->last_name)?>
                    </option>
                <?php endforeach; ?>
            <?php endif; ?>
        </select>
        
    </div>
    <div class="clear"></div><br />
    <input class="button updatestatus" type="button" value="Submit" pid='<?php echo $property->property_id;?>'/>
    
<?php endif; ?>

</div>