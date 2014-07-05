
$(document).ready(function(){
	//custom validation rule 
    $.validator.addMethod("select_required", 
        function(value, element) {
                         
            return (value!=-1);
        }, 
        "No item selected."
    );
    
    
    $('#frmStore').validate({
    
         errorPlacement: function(error, element) {

                error_obj = $('<span>').html(error).insertAfter(element);
                $('<span class="clear">').insertAfter(error_obj);
         } 
    });
    
    $('#btnUpdateStore').click(function(){
        
        if ( $('#frmStore').valid() )
        {
            $('#frmStore').submit();
        }
    });
    
    // If the user changes the website selection
    // load the applicable states for that website.
    $("#website_id").bind("change", function() 
    {
		get_states();
    })
    
    // If the user has chosen a state from the state picker,
    // set the state text field to its value.
    $("#statepick").bind("change", function() 
    {
		var state = $("#statepick").val();
		if(state != "")
		{
			$("#state").val(state);
		}
    })    
    
    // Load applicable states for current website.
    get_states();
});

function get_states()
{
    // Get the selected website_id
    var website_id = $("#website_id").val(); 
    
    if(website_id != "")
    {
        var parameters = {};
        parameters['type'] = 3;
        parameters['website_id'] = website_id;

        $.post(base_url + 'storemanager/ajaxwork', parameters, function(data)
        {
 			if(data.message == "OK")
 			{
				$("#statepick").html(data.html);	
 			}
        }, "json");			
    }	
}