var page_name = 'testimonialmanager';

$(function(){
    $('#delete').live('click',function(){
    
        if ($("input[@name='testimonialtodelete[]']:checked").length == 0) {
            alert('Please click on the checkbox to select the testimonial you want to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected testimonials?")) {
            var selectedvalues = "";
            $("input[@name='testimonialtodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            blockElement("#page_listing");;
            $.post(base_url + 'admin/testimonialmanager/ajaxwork', parameters , function(data)
            {                                             
                unblockElement('#page_listing'); 
                if(data == 1) {
                	// The reordering was successful, reload the testimonials list.
                	refreshTestimonials();
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            }, "json");
        }
    });
});

function change_order(testimonial_id, direction)
{
   var parameters = {};
   parameters['type'] = 2; // Change testimonial order
   parameters['testimonial_id'] = testimonial_id;
   parameters['direction'] = direction;
   blockElement("#page_listing");
   $.post(base_url + 'admin/testimonialmanager/ajaxwork', parameters , function(data)
   {                                             
        unblockElement('#page_listing'); 
        if(data.message == "OK") {
        	// The reordering was successful, reload the testimonials list.
        	refreshTestimonials();
        } else {
        	alert("Sorry an error occured and your request could not be processed");
        }
   }, "json");
}

function refreshTestimonials()
{
    var parameters = {};
    parameters['type'] = 3; // refresh testimonial order
    blockElement("#page_listing");
    $.post(base_url + 'admin/testimonialmanager/ajaxwork', parameters , function(data){
    $('#page_listing').html(data.html);
    unblockElement('#page_listing');
    },
    "json");
}