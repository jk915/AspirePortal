var page_name = 'property_listing';

$(document).ready(function(){          
 
    $(".porperty_listing_search .button").live('click',function(){
         
         var parameters = {};
         parameters = addParameters(parameters);
        
         parameters['type'] = 3;
        
         refreshListing(page_name, parameters, function(){ init_pagination(page_name) }) 
                  
     });
     
     $(".unreserve").live('click',function(e){
         
        var property_id = $(this).attr("href");
        e.preventDefault();
        if (confirm("Are you sure you want to unreserve this property?"))
        {
            var parameters = {};
            
            parameters['type'] = 7;
            parameters['property_id'] = property_id;
            
            //refreshListing(page_name, parameters, function(){   })
            blockElement('#page_listing');
            $.post(base_url + 'admin/' +page_name + '/ajaxwork', parameters,function() {
                window.location = base_url + 'admin/' +page_name;            
            });
                                   
        }       
         
     });
     
     init_pagination(page_name);     
     
});

function addParameters(parameters)
{
     var project_id = $("#search_estate").val();
     var state_id = $("#search_state").val();
     var price_from = $("#price_from").val();
     var price_to = $("#price_to").val();
     var status = $("#search_status").val();
     var property_type_id = $("#search_property_type").val();
         
     parameters['project_id'] = project_id;
     parameters['state_id'] = state_id;
     parameters['price_from'] = price_from;
     parameters['price_to']   = price_to;
     parameters['status']   = status;
     parameters['property_type_id'] = property_type_id;
    
    return parameters;
}
