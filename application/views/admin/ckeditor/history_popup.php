<div class="w_caption" style="background-color: #CCC;height:23px"><a href="javascript:void(0);" class="w_close"><!-- --></a><span class="w_captionText" id="_wicket_window_12"><b>History</b></span></div>

<div id="scroll_page_listing">
    
    <div class="history_popup">
    
        <input type="hidden" id="history_type" value="<?php echo $history_type;?>" /> 
        
        <label for="history_time">Please select the time:</label>
        <select id = "history_time">        
            <option value="">Choose</option>
            <?php
            if($history)
            {
                foreach($history->result() as $row)        
                {
                    ?>
                    <option value="<?php echo $row->id;?>"><?php echo $row->date;?></option>    
                    <?php  
                }
            } 
            ?>
        </select>
        <input type="button" value="Rollback" id="button" />
        
        <div class="clear"></div>
        <br/>
        <div id = "history_editor_div">
            <div style="margin-left: 25px;">
                <textarea id="history_editor" cols="20" rows="10" style="width:880px;height:300px" class="editor"></textarea>        
            </div>
        </div>    
    </div>    
</div>