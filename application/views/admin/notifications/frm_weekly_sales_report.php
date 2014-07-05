<div id="frm_weekly_sales_report" style="height:280px;">

<?php if ($weekly_sales_report) : ?>

    <div style="font-size:15px;font-weight:bold;color:#8B0304" class="property_title">
        <?php echo $weekly_sales_report['0']->user_id.' '.$weekly_sales_report['0']->first_name.' '.$weekly_sales_report['0']->last_name; ?>
    </div>
    
    <label for="weekly_sales_report">Change Notification To:<span class="requiredindicator">*</span></label>           
    
    <select class="weekly_sales_report" name="weekly_sales_report">
		<option> Select status </option>
		<option value="1"> Yes </option>
		<option value="0"> No </option>
    </select>
    
    
    <div class="clear"></div><br />
    <input class="button update_weekly_sales_report" type="button" value="Submit" uid='<?php echo $weekly_sales_report['0']->user_id;?>'/>
    
<?php endif; ?>

</div>