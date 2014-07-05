<div id="frm_new_listing_email" style="height:280px;">

<?php if ($new_listing_email) : ?>

    <div style="font-size:15px;font-weight:bold;color:#8B0304" class="property_title">
        <?php echo $new_listing_email['0']->user_id.' '.$new_listing_email['0']->first_name.' '.$new_listing_email['0']->last_name; ?>
    </div>
    
    <label for="new_listing_email">Change Notification To:<span class="requiredindicator">*</span></label>           
    
    <select class="new_listing_email" name="new_listing_email">
		<option> Select status </option>
		<option value="1"> Yes </option>
		<option value="0"> No </option>
    </select>
    
    
    <div class="clear"></div><br />
    <input class="button update_new_listing_email" type="button" value="Submit" uid='<?php echo $new_listing_email['0']->user_id;?>'/>
    
<?php endif; ?>

</div>