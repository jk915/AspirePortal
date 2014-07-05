var page_name = 'contactsmanager';

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='builderstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the Panel Solicitor(s) you want to delete.');
            return;
        }

        if (confirm("Are you sure you want to delete the selected Panel Solicitor(s)?"))
        {
            var selectedvalues = "";
            $("input[@name='builderstodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            
            refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
			
        }
    });
    
     $("#builder_search").bind("keypress", function (e) {
         if(e.keyCode==13) //enter pressed
         {
             var searchfor = $(this).val();
             var parameters = {};
            
             parameters['type'] = 3;
             parameters['tosearch'] = searchfor;
            
             refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
         }
     });
     
     init_pagination(page_name);
    
});
