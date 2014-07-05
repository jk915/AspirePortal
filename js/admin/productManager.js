var page_name = 'productmanager';

$(document).ready(function(){
    
    $("#breadcrumbs").jBreadCrumb();    
    $('#addcategory').live('click',function(e){
    
        e.preventDefault();
        $('#new_category_div').show('slow');
        $('#new_category').val('');
    
    });
    
    $('#cancelcategory').live('click',function(){
    
         $('#new_category_div').hide('slow');
    
    });
    
    $('#savecategory').live('click',function(){
    
        var categoryname = $('#new_category').val();
        var category_code = $('#new_category_code').val();
        
        if ((categoryname != '') && (category_code != ""))
        {
            var parameters = {};
            parameters['type'] = 3;
            parameters['category_name'] = categoryname;
            parameters['category_code'] = category_code;
            parameters['parent_category_id'] = $('#category_id').val();
            
            blockElement('#categories_div');
            $.post(base_url + 'productmanager/ajaxwork', parameters , function(data){
            
                $('#categories_div').html(data.html);
                
                unblockElement('#categories_div');
                
        		$('#new_category').val('');
        		$('#new_category_code').val('');                
                $('#new_category_div').hide();
                
                showMessage(data.message);
                
            },
            "json");
            
        }
        else
            alert("Please enter a category name.");
    });
    
    $('#deletecategory').live('click',function(){
        
        if ($("input[@name='categoriestodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the categories you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected categories?"))
        {
            var selectedvalues = "";
            var i = 0;
            $("input[@name='categoriestodelete[]']:checked").each(function(){
                
                if(i>0) selectedvalues += ';';                                        
                selectedvalues += $(this).val();                
                ++i;
            });
           
            var parameters = {};
            
            parameters['type'] = 4;
            parameters['todelete'] = selectedvalues;
            parameters['parent_category_id'] = $('#category_id').val();            
            
            blockElement('#categories_div');
            
            $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
            
                $('#categories_div').html(data.html);
                
                unblockElement("#categories_div");
                
                if (data.message != "ok")
                   showMessage(data.message); 
            },
            "json");
        }        
    
    });
    
    $("#checkcategories").click(function(){
        
            var checked_status = this.checked;
            
            $("input[name='categoriestodelete[]']").each(function()
            {
                this.checked = checked_status;
            });
     }); 
     
     $("#deleteproduct").live('click',function(){
         
        if ($("input[@name='productstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the products you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected products?"))
        {
            var selectedvalues = "";
            var i = 0;
            $("input[@name='productstodelete[]']:checked").each(function(){
                
                if(i>0) selectedvalues += ';';                                        
                selectedvalues += $(this).val();                
                ++i;
            });
           
            var parameters = {};
            
            parameters['type'] = 9;
            parameters['todelete'] = selectedvalues;
            parameters['category_id'] = $('#category_id').val();
            
            blockElement('#products_div');
            
            $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
                $('#products_div').html(data.html);
                
                unblockElement("#categories_div");
                
                if (data.message != "ok")
                   showMessage(data.message); 
            }, "json");
        }
        
     });

     init_pagination(page_name);
    
});

function showMessage(message)
{
    if ($('#message').length == 0)
    {
        $('<p id="message"></p>').html(message).insertBefore($('#new_category_div'));
    }
    else
    {
        $('#message').html(message);
    }
}

function refreshProducts()
{
    
}
