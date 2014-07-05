var page_name = 'pagemanager';

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='pagestodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the page you want to delete.');
            return;
        }


	    if (confirm("Are you sure you want to delete the selected pages?"))
	    {
	        var selectedvalues = "";
	        $("input[@name='pagestodelete[]']:checked").each(function(){
	            selectedvalues += $(this).val() +';';
	        });
    	    
    	    var parameters = {};
    	    
    	    parameters['type'] = 1;
	        parameters['todelete'] = selectedvalues;
	        
    	    refreshListing(page_name, parameters, function(){init_pagination(page_name)})
	    }
	});
    
    $("#page_search").bind("keypress", function (e) {
         
         if(e.keyCode==13) //enter pressed
         {
            search();   
         }
     });

     $("#page_search_button").click(function(){

            search();
            
     });
     
     $("#website_search").bind("change",function(e){
         
         search();   
         
     });
    
	 init_pagination(page_name);
	
});

function search()
{
       var searchfor = $("#page_search").val();
       var website = $("#website_search").val();
       var parameters = {};
    
       parameters['type'] = 6;
       parameters['tosearch'] = searchfor;
       parameters['website'] = website;
        
       refreshListing(page_name, parameters, function(){init_pagination(page_name)})  
}