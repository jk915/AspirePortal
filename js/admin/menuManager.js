$(document).ready(function(){
    
    $('#addmenu').click(function(e){
    
        e.preventDefault();
        $('#new_menu_div').show('slow');
        $('#new_menu').val('');
    
    });
    
    $('#cancel').click(function(){
    
         $('#new_menu_div').hide('slow');
    
    });
    
    $('#save').click(function(){
    
        var menuname = $('#new_menu').val();
        var website_id = $('#website_id').val();
        
        if (menuname != '')
        {
            var parameters = {};
            parameters['type'] = 3;
            parameters['menu_name'] = menuname;                 
            parameters['website_id'] = website_id;
            
            blockElement('#menus');
            $.post(base_url + 'admin/menumanager/ajaxwork', parameters , function(data){
            
                $('#menus').html(data.html);
                
                unblockElement('#menus');
                
                $('#new_menu_div').hide();
                
                showMessage(data.message);
                
            },
            "json");            
        }
        else
            alert("Please enter a New Menu Name");     
    });
    
     $('#deletemenu').click(function(){
    
        var menu_id = $('#menus').val();
        var website_id = $('#website_id').val();       
         
        if (!menu_id) return;
                         
        if (confirm('Are you sure you want to delete this menu ?'))
        {
            
            var parameters = {};
            parameters['type'] = 4;
            parameters['menu_id'] = menu_id;
            parameters['website_id'] = website_id;
            
            blockElement('#menus');
            $.post(base_url + 'admin/menumanager/ajaxwork', parameters , function(data){
            
                $('#menus').html(data.html);
                
                unblockElement('#menus');
                refreshMenuItems();
                
                showMessage(data.message);
                
            },
            "json");
        }
    
    });
    
    $('#refresh_menus').click(function(){
        refreshMenus();
    });
    
    $('#refresh_items').click(function(){
        refreshMenuItems();
    });
    
    $("#delete_files").live('click',function(){
        
        if ($(":checkbox[@name='itemstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the menu items you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected menu items?"))
        {
            var menu_id = $('#menus').val();
            if (!menu_id) return;
            
            var selectedvalues = "";
            $(":checkbox[@name='itemstodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;            
            
            blockElement("#files_listing");
            
            $.post(base_url + 'admin/menumanager/ajaxwork', parameters,function(data){
            
                unblockElement("#files_listing");
                if (data=="ok")
                {
                    refreshMenuItems();
                    
                }
                else
                    alert("Error deleteing menu item(s)");
            });
        }
        
    });
    
    $("#menus").change(function() {
    
        refreshMenuItems(); 
    });
    
    $('.edit_menuitem').live("click",function(e){
        
        var menu_id = $('#menus').val();
        if (!menu_id) return;
        
        var menu_item_id = $(this).attr("href"); 
        var parameters = {};
        parameters['type'] = 7;
        parameters['menu_id'] = menu_id;
        parameters['menu_item_id'] = menu_item_id;
        
        showMenuItemPopup(parameters);
        
        e.preventDefault();
       
    });
    
    $('#add_menu_item_btn').click(function(){
        
        var menu_id = $('#menus').val();
        if (!menu_id) return;
            
        var add_menu_item = $("#add_menu_item").val();        
        if(add_menu_item !="")    
        {
            var parameters = {};
            parameters['type'] = 7;
            parameters['menu_id'] = menu_id;
            parameters['menu_item_name'] = add_menu_item;
            parameters['menu_item_id'] = "";
            
            showMenuItemPopup(parameters);
        }
        else
            alert("Please enter a menu item name.");
    });
    
    
    $('.expand').live("click",function(e){
       
        
        e.preventDefault();
        
        var href = $(this).attr("href");
        
        var menuid = href.split("|")[0];
        var level = href.split("|")[1];
        
        var tr = $(this).parent().parent();
        
        if (tr.hasClass("expanded"))
        {
            
            $("[class*=expandedchild_"+ menuid +"]").slideUp('fast',function(){
                
                $(this).remove();
                tr.removeClass("expanded");
                
            })
            
            return;
        }
        
        var parameters = {};
        
        parameters['type'] = 9;
        parameters['menu_item_id'] = menuid;
        parameters['level'] = level;
        
        blockElement("#files_listing");
        
        $.post(base_url + 'admin/menumanager/ajaxwork',parameters,function(data){
            
                   unblockElement("#files_listing");   
                   tr.addClass("expanded");
                   tr.after(data.html);
                   
                   $("[class*=expandedchild_]").each(function(){
                       
                       
                       if (!$(this).is(":visible"))
                       {
                           $(this).slideDown('fast',function(){
                               $(this).removeAttr("style");
                           });
                       }  
                       
                   });
            
        },"json");
       
        
        
    });
    
    $("#clonemenu").live("click",function(e){
            
        var menu_id = $('#menus').val();
         
        if (!menu_id)
            alert("Please select a Menu");
            
        var parameters = {};
        
        parameters['type'] = 10;
        parameters['menu_id'] = menu_id;
        
        blockElement('#menus');
        $.post(base_url + 'admin/menumanager/ajaxwork', parameters , function(data){
        
            refreshMenus();   
            refreshMenuItems();                     
        },
        "json");            
                                        
    });
    
});

function showMenuItemPopup(parameters)
{
           $.post(base_url + 'admin/menumanager/ajaxwork',parameters,function(data){
            
            var width = $(document).width();
            var elementWidth = 600;
            var elementHeight = 500;
            
            $.blockUI
            ({
                message: data.html,
                css: { cursor: 'normal', top: '100px', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2) },
                overlayCSS: { cursor: 'normal' },
                centerX: true,
                centerY: true

            });
            
            $('.w_close').click(function(e) {
                          e.preventDefault();
                          $(document).unblock();
            
            });
            
            $('#frmMenuItem').validate();
            
            $('#save_menu_item').click(function(){
                 
                if($('#frmMenuItem').valid())
                {
                    var menu_id = $('#menus').val();
                    if (!menu_id) return;
                    
                    var parameters = $('#frmMenuItem').formToArray();
                    parameters[parameters.length] = {name:'type',value:'8'};
                    parameters[parameters.length] = {name:'menu_id',value:menu_id}
                                                                                                      
                    $.post(base_url + 'admin/menumanager/ajaxwork',parameters,function(data){
                         if(data != '')
                        	 $('.show_error').html(data);
                         else
                         {
	                         $(document).unblock();
	                         refreshMenuItems();
	                         $("#add_menu_item").val("");
                         }
                         
                    });
                }
            });
            
            $("#link_type").change(function(){
                
                show_link_to();
                
            }); 
            
    },"json");
}

function showMessage(message)
{
        if ($('#message').length == 0)
        {
            $('<p id="message"></p>').html(message).insertBefore($('#new_menu_div'));
        }
        else
        {
            $('#message').html(message);
        }
}

function refreshMenus()
{
        var parameters = {};
        parameters['type'] = 5;
    
        blockElement('#menus');
        $.post(base_url + 'admin/menumanager/ajaxwork', parameters , function(data){
            
                                                        
                $('#menus').html(data.html);
                unblockElement('#menus');
                
            },
            "json");
}

function refreshMenuItems()
{
      var menu_id = $('#menus').val();
      if (!menu_id) return;     
      
      var parameters = {};
      parameters['type'] = 6;
      parameters['menu_id'] = menu_id;
      
      blockElement("#files_listing");
      $.post(base_url + 'admin/menumanager/ajaxwork', parameters , function(data){
            
                $('#files_listing').html(data.html);
                unblockElement('#files_listing');
                
            },
            "json");
}

function show_link_to()
{
        var link_type = $("#link_type").val();
                
        var parameters = {};
        parameters['type'] = 11;
        parameters['link_type'] = link_type;
                            
        $.post(base_url + 'admin/menumanager/ajaxwork', parameters, function(data){              
        
            $("#link_to").html(data.html);
            
        },"json");    
} 