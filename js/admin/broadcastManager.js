

var page_name = 'broadcastmanager';

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='broadcaststodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the page you want to delete.');
            return;
        }


	    if (confirm("Are you sure you want to delete the selected broadcasts?"))
	    {
	        var selectedvalues = "";
	        $("input[@name='broadcaststodelete[]']:checked").each(function(){
	            selectedvalues += $(this).val() +';';
	        });
    	    
    	    var parameters = {};
    	    
    	    parameters['type'] = 1;
	        parameters['todelete'] = selectedvalues;
	        
    	    refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
	    }
	});
	 
	init_pagination(page_name);
	
	// Refresh by status
	jQuery( 'select.select_status' ).change(function(){
		var id = jQuery( this ).val();
		
		var parameters 			= {};
		parameters['type'] 		= 3;
		parameters['status_id'] = id;
		
		refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
	});
	
});
