    <label for="lot">Lot:<span class="requiredindicator">*</span><span class="hide_text">Hide</span> <input class="hide" type="checkbox" name="hide_lot" id="hide_lot" value="1" <? echo ($property_id !="" && $property->hide_lot!="0") ? "checked" : "" ?>/> </label>
    <input id="lot" class="required" type="text" value="<? echo ($property_id !="" && $property->lot != "-1") ? $property->lot : ""; ?>" name="lot"/>