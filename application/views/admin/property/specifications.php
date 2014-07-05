<?php if(isset($property)) : ?>

<div class="left" style="width: 33%;">    

    <label for="bedrooms">No. Bedrooms:<span class="requiredindicator">*</span></label>
    <input id="bedrooms" class="required number" type="text" value="<? echo ($property_id !="" && $property->bedrooms!="-1") ? $property->bedrooms : "" ?>" name="bedrooms"/>   
    
    <label for="bathrooms">No. Bathrooms:<span class="requiredindicator">*</span></label>
    <input id="bathrooms" class="required number" type="text" value="<? echo ($property_id !="" && $property->bathrooms!="-1") ? $property->bathrooms : "" ?>" name="bathrooms"/>   

    <label for="garage">Garage:<span class="requiredindicator">*</span></label>
    <input id="garage" class="required" type="text" value="<? echo ($property_id !="" && $property->garage!="-1") ? $property->garage : "" ?>" name="garage"/>   
    
    <label for="frontage">Frontage:</label>
    <input id="frontage" type="text" value="<? echo ($property_id !="" && $property->frontage!="-1") ? $property->frontage : "" ?>" name="frontage"/>
    
    <label for="nras" style="padding-bottom:10px;">National Rental Affordabilty Scheme<br />(NRAS):</label>
    <input type="radio" value="1" id="nras" name="nras" <? echo ($property_id != '' && $property->nras == 1) ? 'checked="checked"' : '' ?>/> Yes
    <input type="radio" value="0" id="nras" name="nras" <? echo ($property_id != '' && $property->nras == 0) ? 'checked="checked"' : '' ?>/> No
    
    <div class="nras" style="display:none;">
        <label for="nras_provider">NRAS Provider:</label>
        <input id="nras_provider" type="text" value="<? echo ($property_id !="" && $property->nras_provider!="-1") ? $property->nras_provider : "" ?>" name="nras_provider"/>
        
        <label for="nras_rent">NRAS Rent Discount %:</label>
        <input id="nras_rent" type="text" value="<? echo ($property_id !="" && $property->nras_rent!="-1") ? $property->nras_rent : "" ?>" name="nras_rent"/>
        
        <label for="nras_fee">NRAS Fee's Summary:</label>
        <input id="nras_fee" type="text" value="<? echo ($property_id !="" && $property->nras_fee!="-1") ? $property->nras_fee : "" ?>" name="nras_fee"/>
    </div>
    
    
	
	<label for="smsf" style="padding-bottom:10px;">SMSF:</label>
    <input type="radio" value="1" name="smsf" <? echo ($property_id != '' && $property->smsf == 1) ? 'checked="checked"' : '' ?>/> Yes
    <input type="radio" value="0" name="smsf" <? echo ($property_id != '' && $property->smsf == 0) ? 'checked="checked"' : '' ?>/> No 
    
    <label for="study" style="padding-bottom:10px;">Study:</label>
    <input type="radio" name="study" value="1" <? echo ($property_id != '' && $property->study == 1) ? 'checked="checked"' : '' ?>/> Yes
    <input type="radio" name="study" value="0" <? echo ($property_id != '' && $property->study == 0) ? 'checked="checked"' : '' ?>/> No
    
</div>

<div class="left" style="width: 33%;">    

    <label for="land">Land Area (sqm):</label>
    <input id="land" type="text" value="<? echo ($property_id !="" && $property->land != "-1") ? $property->land : "" ?>" name="land"/>   
    
    <label for="house_area">House Area(sqm):</label>
    <input id="house_area" type="text" value="<? echo ($property_id !="" && $property->house_area!="-1") ? $property->house_area : "" ?>" name="house_area"/>   

    <label for="approx_rent">Approximate Weekly Rent $:</label>
    <input id="approx_rent" type="text" value="<? echo ($property_id !="" && $property->approx_rent!="-1") ? $property->approx_rent : "" ?>" name="approx_rent"/>
    
    <label for="rent_yield">Rent Yield:</label>
    <input id="rent_yield" type="text" value="<? echo ($property_id !="" && $property->rent_yield!="-1") ? $property->rent_yield : "" ?>" name="rent_yield" readonly="readonly"/>
    
    <label for="facade">Facade:</label>
    <input id="facade" type="text" value="<? echo ($property_id !="" && $property->facade!="-1") ? $property->facade : "" ?>" name="facade"/>
    
    <label for="internal_comments">Internal Comments:</label>
    <textarea id="internal_comments" name="internal_comments"><? echo ($property_id !="" && $property->internal_comments!="-1") ? $property->internal_comments : "" ?></textarea>
    
    <label for="misc_comments">Misc Comments:</label>
    <textarea id="misc_comments" name="misc_comments"><? echo ($property_id !="" && $property->misc_comments!="-1") ? $property->misc_comments : "" ?></textarea>
</div>
                     
<div class="left" style="width: 33%;">

    <label for="owner_corp">Owner Corp Fees $:</label>
    <input id="owner_corp" class="number" type="text" value="<? echo ($property_id !="" && $property->owner_corp!="-1") ? $property->owner_corp : "" ?>" name="owner_corp"/>
    
    <label for="council_rates">Council Rates:</label>
    <input id="council_rates" class="number" type="text" value="<? echo ($property_id !="" && $property->council_rates!="-1") ? $property->council_rates : "" ?>" name="council_rates"/>
    
    <label for="other_fee_amount">Other Fee's $:</label>
    <input id="other_fee_amount" class="number" type="text" value="<? echo ($property_id !="") ? number_format($property->other_fee_amount,2) : "" ?>" name="other_fee_amount"/>
    
    <label for="other_fee_text">Other Fee's text:</label>
    <input id="other_fee_text" type="text" value="<? echo ($property_id !="") ? $property->other_fee_text : "" ?>" name="other_fee_text"/>
    
    <label for="est_stampduty_on_purchase">Estimated stamp duty on purchase $:</label>
    <input id="est_stampduty_on_purchase" class="number" type="text" value="<? echo ($property_id !="") ? $property->est_stampduty_on_purchase : "" ?>" name="est_stampduty_on_purchase"/>
    
    <label for="estimated_gov_transfer_fee">Estimated Gov. Transfer Fee $:</label>
    <input id="estimated_gov_transfer_fee" class="number" type="text" value="<? echo ($property_id !="") ? $property->estimated_gov_transfer_fee : "" ?>" name="estimated_gov_transfer_fee"/>
    
    <label for="land_price">Land Price $:</label>
    <input id="land_price" type="text" value="<? echo ($property_id !="" && $property->land_price!="-1") ? $property->land_price : "" ?>" name="land_price"/>   
    
    <label for="house_price">House Price $:</label>
    <input id="house_price" type="text" value="<? echo ($property_id !="" && $property->house_price!="-1") ? $property->house_price : "" ?>" name="house_price"/>
    
    <label for="total_price">Total Price $:</label>
    <input id="total_price" type="text" value="<? echo ($property_id !="" && $property->total_price!="-1") ? $property->total_price : "" ?>" name="total_price"/>
    
    <label for="special_features">Special Features:</label>
    <textarea id="special_features" name="special_features"><? echo ($property_id !="" && $property->special_features!="-1") ? $property->special_features : "" ?></textarea>
    
</div>                         

<div class="clear"></div>
<?php endif; ?>