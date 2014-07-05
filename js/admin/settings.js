$(document).ready(function()
{
    // Handle form validation
    $('#frmSettings').validate();  
    $('#frmSettingsTax').validate();  
    
    // Setup the tabs
    $("ul.skin2").tabs("div.skin2 > div");
    
    $("#button").live('click',function(){
       
       if($('#frmSettings').valid() && $('#frmSettingsTax').valid())     
       {
           //copy from frmSettingsTax form to ftmSettings hidden fields
           $("#frmSettingsTax").find("input,select").each(function(index) {

                var id = "#hidden_" + $(this).attr("name");
                var value = $(this).val();
                
                $(id).val(value);
            });
            
            $('#frmSettings').submit();   
       }
       
    });
    
    $("#update_contacts").live('click',function(){
        
        var contact_notification_values = "";
        var order_notification_values = "";
        
        $("#contact_listing input[name='contact_notification[]']:checked").each(function(){
            contact_notification_values += $(this).val() + ';';
        });
                
        $("#contact_listing input[name='order_notification[]']:checked").each(function(){
            order_notification_values += $(this).val() + ';';
        });
                
        var parameters = {};
            
        parameters['type'] = 4;
        parameters['contact_notification'] = contact_notification_values;
        parameters['order_notification'] = order_notification_values;
        
        blockElement('#contact_listing');
        $.post(base_url + 'admin/settingsmanager/ajaxwork', parameters , function(data){
        
            $('#contact_listing').html(data.html);
            
            unblockElement('#contact_listing');                
        },
        "json");
    });
    
    
    $('#delete').live('click',function(){
    
        if ($("input[name='contactstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the contact you want to delete.');
            return;
        }                 

        if (confirm("Are you sure you want to delete the selected contacts?"))
        {
            var selectedvalues = "";
            $("input[name='contactstodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            
            blockElement('#contact_listing');
            $.post(base_url + 'admin/settingsmanager/ajaxwork', parameters , function(data){
            
                $('#contact_listing').html(data.html);
                
                unblockElement('#contact_listing');                
            },
            "json");
        }
    });
    
    $("#add_contact").live('click',function(){
        
        $('#frmAddContacts').validate(); 
        if($('#frmAddContacts').valid())
        {
            
            var parameters = {};
            parameters['type'] = 3;
            parameters['first_name'] = $("#first_name").val();
            parameters['last_name'] = $('#last_name').val();
            parameters['email'] = $('#contact_email').val();
            //parameters['website_id'] = $('#website_id').val();
            
            blockElement('#contact_listing');
            $.post(base_url + 'admin/settingsmanager/ajaxwork', parameters , function(data){
            
                $('#contact_listing').html(data.html);
                
                unblockElement('#contact_listing');
                
                $("#first_name").val("");
                $("#last_name").val(""); 
                $("#email").val("");
                $("#website_id").val("");
                
            },
            "json");
        }        
        
    });      
      
});