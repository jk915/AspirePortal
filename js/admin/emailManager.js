var page_name = 'emailmanager';

$(document).ready(function(){
    
    $('#frmBlock').validate({
        
        errorPlacement: function(error, element) {

               var error_element_id = element.attr('id')+'_error';
               var error_element = $('#'+error_element_id);
               
               $('<div id="'+error_element_id+'" class="">').html(error).insertAfter(element);
               
               
           }
   });
    
    $('#test_template').click(function(e){
        
        var parameters = {};
        parameters['type'] = 4;
        parameters['current_template'] = $('#current_template').val();
        
        showMenuItemPopup(parameters);
        
        e.preventDefault();
       
    });
    
    //delete the selected email templates
    $('#delete').live('click',function(){
    
        if ($("input[@name='templatestodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the email template you want to delete.');
            return;
        }


        if (confirm("Are you sure you want to delete the selected email template(s)?"))
        {
            var selectedvalues = "";
            $("input[@name='templatestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            
            refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
        }
    });
    /*
    $('#is_html').live('change',function(){
        
        var parameters = {};
        parameters['type']               = 3;
        parameters['is_html']            = 0;
        parameters['email_body']         = $('#email_body').val();
        parameters['current_template']   = $('#current_template').val();
        
        if ( $('#is_html').attr('checked') ) {
            
            parameters['is_html']         = 1;
        }
                
        $.post(base_url + page_name + '/ajaxwork', parameters , function(data){
            
            $('#email_body_div').html( data.html );
        
            if( parameters['is_html'] == 1 )
            {
                
                var o=CKEDITOR.instances.email_body;
                if (o) 
                {
                    CKEDITOR.remove( CKEDITOR.instances.email_body );
                }
                
                CKEDITOR.replace( 'email_body' );
            }
            
        },
        "json");
        
    });
    $('.order_link').live('click',function(e){
        e.preventDefault();    
        var parameters = {};
        parameters['type'] = 10;
        parameters['order_column'] = $(this).attr('href');
        if($(this).attr('href') == $('#order_column').val())
        {
            parameters['order_direction'] = ($('#order_direction').val() == 'ASC') ? 'DESC' : 'ASC';
        }
        else
            parameters['order_direction'] = 'ASC';
        parameters['order_table'] = $('#order_table').val();
        refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
    });      */
    
    
    init_pagination(page_name);
    
});

function add_pagination_parameters()
{
    var parameters = {};
    parameters['order_column'] = $('#order_column').val();
    parameters['order_direction'] = $('#order_dierction').val();
    parameters['order_table'] = $('#order_table').val();
    return parameters;
}


function showMenuItemPopup(parameters)
{
    $.post(base_url + "admin/" + page_name + '/ajaxwork', parameters, function(data){
            
        var width = $(document).width();
        var elementWidth = 665;
        var elementHeight = 400;
        
        scrollTop = $(window).scrollTop();
        top_v  = scrollTop + $(window).height()/2-400/2;
        top_v = $(window).height()/2 - elementHeight/2;
        
        $.blockUI
        ({
            message: data.html,
            css: { cursor: 'normal', top: top_v+'px', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2) },
            overlayCSS: { cursor: 'normal' },
            centerX: true,
            centerY: true

        });
        
        $('.w_close').click(function(e) {
            e.preventDefault();
            $(document).unblock();

        });
        
        $('#frmSendTestMail').validate({
            
            errorPlacement: function(error, element) {

                   var error_element_id = element.attr('id')+'_error';
                   var error_element = $('#'+error_element_id);
                   
                   $('<div id="'+error_element_id+'" class="">').html(error).insertAfter(element);
                   
                   
               }
       });
        
        $('#send_test_mail').click(function(e) {
            
            if( $('#frmSendTestMail').valid() )
                $('#frmSendTestMail').submit();
            
        });
        
    }, 'json');
}