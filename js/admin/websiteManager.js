var page_name = 'websitemanager';

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='websitetodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the website you want to delete.');
            return;
        }


        if (confirm("Are you sure you want to delete the selected websites?"))
        {
            var selectedvalues = "";
            $("input[@name='websitetodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            
            refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
        }
    });
    
    $("#delete_regions").live('click', function(){
       
        if ($("input[@name='regiontodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the region you want to delete.');
            return;
        }


        if (confirm("Are you sure you want to delete the selected regions?"))
        {
            var selectedvalues = "";
            $("input[@name='regiontodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 3;
            parameters['todelete'] = selectedvalues;
            
            blockElement('#region_listing');      
            
            $.post(base_url + page_name + '/ajaxwork', parameters , function(data)
            {                                                
                $('#region_listing').html(data.html);
                unblockElement('#region_listing');      
            }, 
            "json");
        }
        
    });
     
     init_pagination(page_name);
    
});