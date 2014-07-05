var page_name = 'usermanager';
var order_by = "u.first_name";
var order_dir = "asc";    

$(document).ready(function(){     
    $('#delete').live('click',function(){
    
        if ($("input[@name='itemstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the user that you want to delete.');
            return;
        }


	    if (confirm("Are you sure you want to delete the selected users?  This action is not reversible and the users will not be able to access the system afterwards."))
	    {
	        var selectedvalues = "";
	        $("input[@name='itemstodelete[]']:checked").each(function(){
	            selectedvalues += $(this).val() +';';
	        });
    	    
    	    var parameters = {};
    	    
    	    parameters['type'] = 1;
	        parameters['todelete'] = selectedvalues;
	        
    	    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
	    }
     
	});
    
    $("#user_search").bind("keypress", function (e) {
         
         if(e.keyCode==13) //enter pressed
         {
               search();                   
         }
     });
     
     $("#user_type_search, #advisor_id_search, #state_type_search,").bind("change",function(e){
         
         search();   
         
     });
	
	 //$("ul.skin2").tabs("div.skin2 > div");
	 init_pagination(page_name);
     
     bindTableHeaderClicks();
	
});

function search()
{
    var searchfor = $("#user_search").val();
    var user_type = $("#user_type_search").val();
    var advisor_id = $("#advisor_id_search").val();
	var state_type = $("#state_type_search").val();
    
    var parameters = {};
    parameters['type'] = 3;
    parameters['tosearch'] = searchfor;
    parameters['advisor_id'] = advisor_id;
    parameters['user_type'] = user_type;
	parameters['state_type'] = state_type;
    parameters['order_by'] = order_by;
    parameters['order_dir'] = order_dir;

    refreshListing(page_name, parameters, function(){ 
        init_pagination(page_name);
    });   
}

function addParameters(parameters)
{
     var searchfor = $("#user_search").val();
     var user_type = $("#user_type_search").val();
     var advisor_id = $("#advisor_id_search").val();
	 var state_type = $("#state_type_search").val();
         
     parameters['tosearch'] = searchfor;
     parameters['user_type'] = user_type;
	 parameters['state_type'] = state_type;
     parameters['advisor_id'] = advisor_id;
     parameters['order_by'] = order_by;
     parameters['order_dir'] = order_dir;     
         
    return parameters;
}

function bindTableHeaderClicks()
{
    // Handle the event when the user clicks on a column heading.
    $("table th a").live("click", function(e) {
        e.preventDefault();
        
        // What will the new sort column be
        var column = $(this).attr("href");

        // Do we need to sort ascending or descending
        if(column == order_by) {
            if(order_dir == "asc") {
                order_dir = "desc";
            } else {
                order_dir = "asc";    
            }
        } else {
            order_dir = "asc";
        }
        
        order_by = column;
        
        // Invoke the search via the pagination if possible (resetting pagination to 1)
        if($("ul.jPag-pages li").length > 0) {
            $("ul.jPag-pages li:eq(0)").click();
        } else {
            // Pagination is not showing, invoke search directly.
            search();    
        }
    });  
}