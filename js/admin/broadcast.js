

var page_name = 'broadcastmanager';

$( document ).ready(function(){

	// Enable the tabs
	$("ul.skin2").tabs("div.skin2 > div");
	
    $( '#send_to' ).change(function(){
    	
    	if( $( this ).val() == 'Access Level' )
    	{
    		$( '#select_access_level' ).show();
    	}
    	else
    	{
    		$( '#select_access_level' ).hide();
    	}
    });
    
    if( $( '#send_to' ).val() == 'Access Level' )
    	$( '#select_access_level' ).show();
    
    $( '#select_list' ).change(function(){
    	refresh_recipient_listing();
	});
    
    $( '#btn_search' ).live( 'click', function(){
    	refresh_recipient_listing();
    });
    
    $( '#btn_search' ).keypress(function(key){
    	key.preventDefault();
    	if( key.keyCode == '13' )
    		refresh_recipient_listing();
    });
    
    $( 'ul.css-tabs li a' ).click(function(){
    	$( document ).ready(function(){
    		init_pagination(page_name);
    	});
    });
    
    $('#frmBroadcast').validate({
        
        errorPlacement: function(error, element) {

               var error_element_id = element.attr('id')+'_error';
               var error_element = $('#'+error_element_id);
               
               if (error_element.length > 0)
                   error_element.html("aaa");
               else 
                 $('<div id="'+error_element_id+'" class="error">').html(error).insertAfter(element);
               
               
           }
   });
    
    $('#frmBroadcast').submit( function(){
    	
    	if( $('#frmBroadcast').valid() )
    		return true;
    	
    	return false;
    });
    
    // Add/Delete recipient
    $( '.chk_recipient' ).live( 'click', function(){
    	
    	var check_id = $( this ).attr( 'id' );
    	// split the id, to we can get the user id
    	// id[1] will contains the user id
    	var id = check_id.split( 'recipient' );
    	
    	var parameters 				= {};
    	if( $( this ).attr( 'checked' ) )
    		parameters['type']		= '4';
    	else
    		parameters['type']		= '5';
    	
    	parameters['user_id']		= id[1];
    	parameters['broadcast_id']	= $( '#id' ).val();
    	
    	$.post( base_url + page_name +'/ajaxwork', parameters );
    });
    
    // Send broadcast
    $( '#btn_Send_Broadcast' ).live( 'click', function(){
    	
    	if( confirm( 'Are you sure you want to send this broadcast now?' ) )
    	{
	    	var id = $( '#id' ).val();
	    	location.href = base_url + page_name + '/send/' + id;
    	}
    });
    
    // fancybox
    $( "#btn_Send_Preview" ).fancybox({
    	'autoScale'			: false,
    	'width'				: '420',
    	'height'			: '50',
    	'onComplete'		: function(){ $( '#fancy_message' ).html( '' ); },
    	'href'				: '#fancy'
    });
    
    $( '#btn_send_preview_last' ).live( 'click', function(){
    	
    	$( '#fancy_message' ).html( '' );
    	
    	
    	var parameters = {};
    	parameters['type']			= '6';
    	parameters['broadcast_id']	= $( '#id' ).val();
    	parameters['email']			= $( '#preview_email' ).val();
    	
    	$.post( base_url + page_name +'/ajaxwork', parameters, function(data){
    		
    		$( '#fancy_message' ).html( data );
    		
    	});
	
    });
});

function refresh_recipient_listing()
{
	parameters = get_pagination_parameters();
	$( '.jPag-current' ).html( '1' );
	
	refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
	
	/*blockElement( '#page_listing' );
	$.post( base_url + page_name+'/ajaxwork', parameters, function(data){
		$( '#page_listing' ).html( data );
		
		unblockElement( '#page_listing' );
	});*/
}

function get_pagination_parameters()
{
	var parameters = {};
	parameters['type']			= '2';
	parameters['level_id']		= $( '#select_list' ).val();
	parameters['broadcast_id']	= $( '#id' ).val();
	parameters['search']		= $( '#search' ).val();
	
	return parameters;
}
