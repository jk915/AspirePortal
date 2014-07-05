<script type="text/javascript">
var contacts_foreign_id = "<?php echo $foreign_id; ?>";
var contacts_foreign_type = "<?php echo $type; ?>";
</script>
<!--<script type="text/javascript" src="<?php echo base_url("js/admin/contacts.js"); ?>"></script>-->

<table cellspacing="0" width="100%" class="left contact_listing"> 
<thead>
    <tr>
        <th width="10%">ID</th>
        <th align="left">Contact Name</th>                            
        <th align="left">Position</th>                            
        <th align="left">Phone</th>
        <th width="10%">Delete</th>                            
    </tr>
</thead>
<tbody>
    <?php $this->load->view("admin/contacts/list", array("contacts" => $contacts)); ?>
</tbody>
</table>

<div class="clear"></div>

<a href="javascript:;" class="button right center" id="deletecontact" style="margin-left:10px;">Delete</a>
<a href="javascript:;" class="button right center" id="addnewcontact">Add new</a>

<div class="clear"></div>

<div id="formaddcontact" style="display:none;">
    <input type="hidden" name="contact_id" id="contact_id"/>
    
    <fieldset class="left" style="width: 290px;">
        <label for="contact_name">Contact Name:<span class="requiredindicator">*</span></label>
        <input type="text" id="contact_name" name="contact_name"/>
        
        <label for="contact_position">Position:</label>
        <input type="text" id="contact_position" name="contact_position"/>                            
        
        <label for="contact_phone">Phone:</label>
        <input type="text" id="contact_phone" name="contact_phone"/>
        
        <label for="contact_mobile">Mobile:</label>
        <input type="text" id="contact_mobile" name="contact_mobile"/>
        
        <label for="contact_fax">Fax:</label>
        <input type="text" id="contact_fax" name="contact_fax"/>
        
        <label for="contact_email">Email:</label>
        <input type="text" id="contact_email" name="contact_email"/>
    </fieldset>
    
    <fieldset class="left" style="width: 270px;"> 
        <label for="contact_address">Address:</label>
        <input type="text" id="contact_address" name="contact_address"/>
        
        <label for="contact_suburb">Suburb:</label>
        <input type="text" id="contact_suburb" name="contact_suburb"/>                       
        
        <label for="contact_postcode">Postcode:</label>
        <input type="text" id="contact_postcode" name="contact_postcode"/>
        
        <label for="contact_state_id">State:</label>
        <select id="contact_state_id" name="contact_state_id">
            <option value="">Choose</option>
            <?php echo $this->utilities->print_select_options($states, "state_id", "name"); ?> 
        </select>                             
        
        <label for="contact_comment">Comment:</label>
        <textarea id="contact_comment" name="contact_comment"></textarea>                                
    </fieldset>                            
    
    <div class="clear"></div><br />
    <a href="javascript:;" class="button left center savecontact">Save</a>
</div>

<div class="clear"></div>