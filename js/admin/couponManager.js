var page_name = 'couponmanager';

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='coupontodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the coupon you want to delete.');
            return;
        }


        if (confirm("Are you sure you want to delete the selected coupons?"))
        {
            var selectedvalues = "";
            $("input[name='coupontodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            
            refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
        }
    });
     
     init_pagination(page_name);
    
});
