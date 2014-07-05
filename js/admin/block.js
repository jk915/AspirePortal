$(document).ready(function(){

    $('#frmBlock').validate({
    
         errorPlacement: function(error, element) {

                var error_element_id = element.attr('id')+'_error';
                var error_element = $('#'+error_element_id);
                
                if (error_element.length > 0)
                    error_element.html("aaa");
                else 
                  $('<div id="'+error_element_id+'" class="error">').html(error).insertAfter(element);
                
                
            }
    });
    
});
