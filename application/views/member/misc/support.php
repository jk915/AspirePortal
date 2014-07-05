<div id="supportModal" class="reveal-modal">
    <h2 style="text-align:center;">Support Request</h2>
    <?php echo form_open('postback/submit_support_form', array("id" => "frmSupport")) ;?> 
    
        <label for="support_type">Support Type <span class="required">*</span></label>
        <select id="support_type" name="support_type" class="required">
            <option value="">Choose</option>
            <option value="error">Error</option>
            <option value="bugfix">Bug Fix</option>
            <option value="suggestion">Suggestion for improvement</option>
            <option value="question">Question</option>
            <option value="other">Other</option>
        </select>
        
        <label for="priority">Priority <span class="required">*</span></label>
        <select id="priority" name="priority" class="required">
            <option value="">Choose</option>
            <option value="urgent">Urgent</option>
            <option value="high">High</option>
            <option value="medium">Medium</option>
            <option value="low">Low</option>
        </select>   
        
        <label for="description">Description <span class="required">*</span></label>     
        <textarea id="description" name="description" cols="20" rows="5" class="required"></textarea>
        
        <div class="buttons top-margin20">
            <a href="#" class="btn" id="btnSubmitSupport">Send Request</a>
            <a href="#" id="btnCancelSupport" class="close-reveal-modal">X</a>
        </div>
    
    </form> 
</div>
