

var page_name = 'accesslevelmanager';

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='accesslevelstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the page you want to delete.');
            return;
        }


	    if (confirm("Are you sure you want to delete the selected access level(s)?"))
	    {
	        var selectedvalues = "";
	        $("input[@name='accesslevelstodelete[]']:checked").each(function(){
	            selectedvalues += $(this).val() +';';
	        });
    	    
    	    var parameters = {};
    	    
    	    parameters['type'] = 1;
	        parameters['todelete'] = selectedvalues;
	        
    	    refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
	    }
	});
	 
	$('.accesslevel input[name=accesslevel_default]').live('click',function(e){
      	
      	var parameters = {};
      	parameters['type'] 					= 3;
        parameters['accesslevel_id'] 		= $(this).val();
        
        refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
    });
	 
	 init_pagination(page_name);
	
});