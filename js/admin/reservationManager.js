var page_name = 'reservationmanager';
Date.format = 'dd/mm/YY'; 

$(document).ready(function(){

    $('.date-pick').datePicker({startDate:'01/01/1987'});
    
    $('#delete').live('click',function(){
    
        if ($("input[@name='reservationstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the reservations you want to delete.');
            return;
        }
                
        if (confirm("Are you sure you want to remove the selected reservations?"))
        {
            var selectedvalues = "";
            $("input[@name='reservationstodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
                        
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            parameters['user_search'] = $("#user_search").val();
            parameters['search_period'] = $("#search_period").val();
            parameters['from_date'] = $("#from_date").val();
            parameters['to_date'] = $("#to_date").val();
            
            refreshListing(page_name, parameters, function(){
                init_pagination(page_name); 
                refreshSummaryTable(parameters);
            });           
            
        }
    });
    
     $("#reservation_search").live('click',function(){
                          
             var parameters = {};
            
             parameters['type'] = 3;
             parameters['user_search'] = $("#user_search").val();
             parameters['search_period'] = $("#search_period").val();
             parameters['from_date'] = $("#from_date").val();
             parameters['to_date'] = $("#to_date").val();
            
             refreshListing(page_name, parameters, function(){
                init_pagination(page_name); 
                refreshSummaryTable(parameters);
            });        
     });
     
     $("#search_period").change(function() {
        
        if($("#search_period").val() == "choose")
            $("#choose_date").show();
        else
            $("#choose_date").hide();        
     });
     
    $('.sold').live('click',function(e)
    {
    	e.preventDefault();
    	    
        if (confirm("Are you sure you want to set this property as being sold?"))
        {
   			var reservation_id = $(this).attr("href");
   			if((reservation_id == undefined) || (reservation_id == ""))
   				return false;

                        
            var parameters = {};
            
            parameters['type'] = 5;
            parameters['reservation_id'] = reservation_id;
            parameters['user_search'] = $("#user_search").val();
            parameters['search_period'] = $("#search_period").val();
            parameters['from_date'] = $("#from_date").val();
            parameters['to_date'] = $("#to_date").val();
            
            refreshListing(page_name, parameters, function(){
                init_pagination(page_name); 
                refreshSummaryTable(parameters);
            });           
            
        }
    });     
    
     init_pagination(page_name);           
});

function refreshSummaryTable(parameters)
{
    parameters['type'] = 4;
    blockElement("#summary_table");

    $.post(base_url + 'reservationmanager/ajaxwork', parameters , function(data)
    {
        $('#summary_table').html(data.html);
        unblockElement('#summary_table');                    
    }, "json");
}