var page_name = 'propertymanager';
$(document).ready(function(){            
     
    loadProperty();
    
    $('#apply').live('click',function(){
        if($("#functions_list option:selected").val() =="" )
        {
            alert('Please choose a function to apply.');
            return;            
        }
        
        if ($("input[@name='propertiestodelete[]']:checked").length == 0) {
            alert('Please click on the checkbox to select the property you want to apply.');
            return;
        }
        
        var funtion_selected = $("#functions_list option:selected").val();
        var selectedvalues = "";
        var parameters = {};
        var kw = $('#property_search').val();
        if($("#archived_search").is(':checked'))
            var archived = 1;
        else
            var archived = 0; 
                            
        parameters['kw'] = kw;
        parameters['state_id'] = $('#state_id_search').val();
        parameters['area_id'] = $('#area_id_search').val();
        parameters['builder_id'] = $('#builder_id_search').val();
        parameters['status'] = $('#status_search').val();
        parameters['archived'] = archived;
                        
        $("input[@name='propertiestodelete[]']:checked").each(function(){
            selectedvalues += $(this).val() +';';
        });        
        
        switch(funtion_selected)
        {
            case "delete":
                if (confirm("Are you sure you want to delete the selected properties?"))
                {
                    parameters['type'] = 1;
                    parameters['todelete'] = selectedvalues;
                   
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) }) 
                }   
                
                break;        
            
            case "archive":    
                if (confirm("Are you sure set ARCHIVED for the selected projects?"))
                {            
                    parameters['type'] = 26;
                    parameters['action'] = "archive";
                    parameters['toarchive'] = selectedvalues;
                   
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break;  
                 
            case "unarchive":  
                if (confirm("Are you sure set NOT TO ARCHIVED for the selected projects?"))
                {                
                    parameters['type'] = 26;
                    parameters['action'] = "unarchive";
                    parameters['toarchive'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break;    
                
            case "feature":    
                if (confirm("Are you sure set FEATURED for the selected projects?"))
                {
                    parameters['type'] = 27;
                    parameters['action'] = "feature";
                    parameters['tofeature'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }   
                break;   
                
            case "unfeature":    
                if (confirm("Are you sure set NOT TO FEATURED for the selected projects?"))
                {              
                    parameters['type'] = 27;
                    parameters['action'] = "unfeature";
                    parameters['tofeature'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break;                                                      
        }

    });

    $("a.clone").click(function(e) {
        e.preventDefault();
        
        if(confirm("Are you sure you wish to clone this property?"))
        {
            window.location.href = $(this).attr("href"); 
        }
    });
    
    $('#state_id_search').live('change',function(){
         loadProperty();
    });     
     
    $('#area_id_search').live('change',function(){
         loadProperty();
    });
     
    $('#builder_id_search').live('change',function(){
         loadProperty();
    });
     
    $('#status_search').live('change',function(){
         loadProperty();
    });
     
    $('#archived_search').live('click',function(){
         loadProperty();
    });
     
    $('#property_search').live('keyup',function(){
         loadProperty();
    });
     
    $('.sorting').live('click',function(){
        var col = $(this).attr("col");
        var order = $(this).attr("order");
        var parameters = {};
        var kw = $('#property_search').val();
        
        if($("#archived_search").is(':checked'))
            var archived = 1;
        else
            var archived = 0;
    
        if (kw == 'Enter Search Keywords') kw = '';
        parameters['type'] = 3;
        parameters['kw'] = kw;
        parameters['col'] = col;
        parameters['order'] = order;
        parameters['state_id'] = $('#state_id_search').val();
        parameters['area_id'] = $('#area_id_search').val();
        parameters['builder_id'] = $('#builder_id_search').val();
        parameters['status'] = $('#status_search').val();
        parameters['archived'] = archived;
        blockElement('#page_listing');
        refreshListing(page_name, parameters, function(){
            init_pagination(page_name)
        })
        unblockElement('#page_listing');
    });
    
    $('.fancybox').fancybox();
    
    // Handle the event when the user changes the status of a property
    $('.status').live('change',function() {
        var status = $(this).val();

        if ((status == 'available') || (status == 'pending')) {
            $('.status_user_area').hide();
        } else {
            $('.status_user_area').show();
        }
    });
    
    $('.advisor_id').live('change',function(){
        var parameters = {};
        parameters['type'] = 9;
        parameters['advisor_id'] = $(this).val();
        blockElement('#frm_change_status');
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            $('.partner_id').html(data.partner_options_html);
            $('.investor_id').html(data.investor_options_html);
            unblockElement('#frm_change_status');
        },"json");
    });
    
    $('.updatestatus').live('click',function(){
        var parameters = {};
        var status = $('.status').val();
        
        if ((status == 'available') || (status == 'pending')) {
            parameters['advisor_id'] = -1;
            parameters['partner_id'] = -1;
            parameters['investor_id'] = -1;
        } else {
            if ($('.advisor_id').val() == -1) {
                alert('Advisor field is required.');
                return;
            }
            parameters['advisor_id'] = $('.advisor_id').val();
            parameters['partner_id'] = $('.partner_id').length == null ? -1 : $('.partner_id').val();
            parameters['investor_id'] = $('.investor_id').length == null ? -1 : $('.investor_id').val();
        }
        parameters['type'] = 15;
        parameters['status'] = status;
        parameters['property_id'] = $('.updatestatus').attr('pid');
        blockElement("#frm_change_status");
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            unblockElement("#frm_change_status");
            if (data.success) {
                loadProperty();
                $.fancybox.close();
            } else {
                alert('Can not update property status.');
            }
        },'json');
    });
     
     //init_pagination(page_name);
 
});

function loadProperty()
{
    var parameters = {};
    var kw = $('#property_search').val();
    
    if($("#archived_search").is(':checked'))
        var archived = 1;
    else
        var archived = 0;

    if (kw == 'Enter Search Keywords') kw = '';
    parameters['type'] = 3;
    parameters['kw'] = kw;
    parameters['state_id'] = $('#state_id_search').val();
    parameters['area_id'] = $('#area_id_search').val();
    parameters['builder_id'] = $('#builder_id_search').val();
    parameters['status'] = $('#status_search').val();
    parameters['archived'] = archived;
    blockElement('#page_listing');
    refreshListing(page_name, parameters, function(){
        init_pagination(page_name)
    })
    unblockElement('#page_listing');
}