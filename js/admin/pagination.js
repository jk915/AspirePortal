
function refreshListing(page_name,parameters,callback)
{   
	var current_page = $('.jPag-current').html();
	
    if (typeof(current_page) === 'undefined' || current_page == null)
            current_page = 1;
	
	parameters['current_page'] = current_page;
    if(page_name == 'productmanager')
        parameters['category_id'] = $('#category_id').val();
    
    if(typeof window.addParameters == 'function') {
        // function exists, so we can now call it
        parameters = addParameters(parameters);
    }
    var temp = $('#pages_no').val();
	$('#page_listing').load(base_url + "admin/" + page_name + '/ajaxwork', parameters, function()
	{
            unblockElement('#page_listing');
            
            if(isFunction(callback)) {
                callback();    
            } else {
                eval(callback);
            }
            
            var c_page = $('.jPag-current').html();
            
            if (typeof(c_page) === 'undefined' || c_page == null || $('#pages_no').val() == 0 || $('#pages_no').val() == 1) {
                 init_pagination(page_name);
            }
            
            if(temp != $('#pages_no').val()) {
                init_pagination(page_name);
            }   
	 });
	  
}


function init_pagination(page_name, type)
{ 
    if ($('#pagination').length == 0)
        return; //no pagination

    if( type == '' || type == 'undefined' || isNaN( type ) || type == null )
    {
    	type = '2';
    }
    else
    	type = parseInt( type );
    
    var page_buttons = 5;
    $('#pagination').parent().html('<div id="pagination"></div>');
    //$('#pagination').html('');    
    
    if ($('#pages_no').val() <= 1)
    {
        return;
    }
    
    $("#pagination").paginate({
					count 		: $('#pages_no').val(),
					start 		: 1,
					display     : ($('#pages_no').val() < page_buttons) ?  $('#pages_no').val() : page_buttons,
					border					: true,
					border_color			: '#fff',
					text_color  			: '#fff',
					background_color    	: 'black',	
					border_hover_color		: '#ccc',
					text_hover_color  		: '#000',
					background_hover_color	: '#fff', 
					images					: false,
					mouse					: 'press',
					onChange: function(page){
					       
					        var parameters = {};
                            if(typeof get_pagination_parameters == 'function')
					        {
                                parameters = get_pagination_parameters();
					        }
					        parameters['type']	= type;
					        refreshListing(page_name,parameters);
					}
				});
}
