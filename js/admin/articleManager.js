$(document).ready(function() 
{
    $("#breadcrumbs").jBreadCrumb(); 
       
   // Bind a click event to the add category button.
   $('#addcategory').click(function(e)
   {
      e.preventDefault();
      $('#new_category_div').show('slow');
      $('#new_category').val('');
      $('#new_category_code').val('');
   });
   
   // Bind a click event to the cancel
   $('#cancel').click(function() 
   {
      $('#new_category_div').hide('slow');
   });
    
	$('#save').live('click',function()
    {
		var categoryname = $('#new_category').val();
		var category_code = $('#new_category_code').val();
		var parent_id = $('#category_id').val();
        
		if ((categoryname != '') && (parent_id != '') && (category_code != ""))
		{
			var parameters = {};
			parameters['type'] = 3;
			parameters['category_name'] = categoryname;                 
			parameters['category_code'] = category_code;                 
			parameters['parent_id'] = parent_id;

			blockElement('#categories');
			
			$.post(base_url + 'articlemanager/ajaxwork', parameters , function(data)
			{
				$('#categories').html(data.html);
				unblockElement('#categories');
				$('#new_category_div').hide();
				showMessage(data.message);
			}, "json");
		}
		else if(categoryname == "")
		{
			alert("Please enter a name for the new category");
			$('#new_category').focus();
		}
		else if(category_code == "")
		{
			alert("Please enter a unique category code for the new category");
			$('#category_code').focus();
		}		
	});
    
	$('#deletecategory').live('click',function()
	{
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
			parameters['category_id'] = $('#category_id').val(); 
            parameters['todelete'] = selectedvalues;

			blockElement('#categories');
			$.post(base_url + 'articlemanager/ajaxwork', parameters , function(data)
			{
				$('#categories').html(data.html);

				unblockElement('#categories');
				
				if( data.products != "" )
					alert(data.products);
				
                if (data.message != "ok")
                   showMessage(data.message); 				

			}, "json");		    
        }
	});
	
	// Handle the event when the user clicks the "Edit Category button"
	$('#editcategory').click(function()
	{
		// Get the id of the selected category
		var category_id = $('#categories').val();
		if (!category_id) 
		{
			alert("Please select a category to edit.")
			return;
		}
		
		// Redirect the user to the category editing screen.
		var url = base_url + "articlecategorymanager/edit/" + category_id;
		window.location = url;
	});	
    
	$('#refresh_categories').click(function(){
		refreshCategories();
	});
    
	$('#refresh_articles').click(function(){
		refreshArticles();
	});
    
    $("#delete_files").live('click',function(){
        
        if ($(":checkbox[@name='itemstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the articles you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected articles?"))
        {
            var category_id = $('#category_id').val();
            
            if (!category_id) return;
            
            var selectedvalues = "";
            $(":checkbox[@name='itemstodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;            
            
            blockElement("#files_listing");
            
            $.post(base_url + 'articlemanager/ajaxwork', parameters,function(data){
            
                unblockElement("#files_listing");
                if (data=="ok")
                {
                    refreshArticles();
                    
                }
                else
                    alert("Error deleteing article(s)");
            });
        }
        
    });
    
    $("#categories").change(function() {
    
        refreshArticles(); 
    });
    
    $('.edit_menuitem').live("click",function(e){
        
        var category_id = $('#categories').val();
        if (!category_id) return;
        
        var article_id = $(this).attr("href"); 
        var parameters = {};
        parameters['type'] = 7;
        parameters['category_id'] = category_id;
        parameters['article_id'] = article_id;
        
        showArticlePopup(parameters);
        
        e.preventDefault();
       
    });
    
    // Bind a click event to the add article button.
    $('#add_article_btn').click(function(){
        
        var category_id = $('#categories').val();
        if (!category_id) return;
            
        var add_article = $("#add_article").val();        
        if(add_article !="")    
        {
            var parameters = {};
            parameters['type'] = 7;
            parameters['category_id'] = category_id;
            parameters['article_title'] = add_article;
            parameters['article_id'] = "";
            
            showArticlePopup(parameters);
        }
        else
            alert("Please enter an article  name.");
    });
    
    $("#article_search").bind("keypress", function (e) {
         
         if(e.keyCode==13) //enter pressed
         {
            search();   
         }
     });

     $("#article_search_button").click(function(){
         
        search();
     });
     
	$("#website_search").bind("change",function(e)
	{
		// Get the id of the selected website
		var website_id = $("#website_search").val();
		
		// Determine the base redirect url.
		var url = base_url + "articlemanager";
		
		// If a valid website has been chosen, redirect back to the index page with
		// the website id defined in the url.
		if(website_id != "")
		{
			url = url + "/index/" + website_id;    
		}
		else
		{
			url = url + "/index/" + 0;	
		}
		
		window.location = url;
	});
    
});

function showArticlePopup(parameters)
{
           $.post(base_url + 'articlemanager/ajaxwork',parameters,function(data){
            
            var width = $(document).width();
            var elementWidth = 850;
            var elementHeight = 530;
            
            $.blockUI
            ({
                message: data.html,
                css: {cursor: 'normal', top: '100px', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2)},
                overlayCSS: {cursor: 'normal'},
                centerX: true,
                centerY: true

            });
            
            $('.w_close').click(function(e) {
                          e.preventDefault();
                          $(document).unblock();
            
            });
            
           /* $('.wysiwyg').htmlarea();
            $('#short_description').htmlarea();*/
            
            $('#frmArticle').validate();
            
            $('#save_article').click(function(){
                 
                if($('#frmArticle').valid())
                {
                    
                    /*$('.wysiwyg').htmlarea("updateTextArea");
                    $('#short_description').htmlarea("updateTextArea");*/
                    
                    var category_id = $('#categories').val();
                    if (!category_id) return;
                    
                    var article_id = $('#article_id').val();
                    
                    var parameters = $('#frmArticle').formToArray();
                    parameters[parameters.length] = {name:'type',value:'8'};
                    parameters[parameters.length] = {name:'category_id',value:category_id};
                    parameters[parameters.length] = {name:'article_id',value:article_id};
                                                                                                      
                    $.post(base_url + 'articlemanager/ajaxwork',parameters,function(data){
                        
                         $(document).unblock();
                         refreshArticles();
                         $("#add_article").val("");
                         
                    });
                }
            });
    
        },"json"); 
}

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

function refreshCategories()
{
   var parameters = {};
   parameters['type'] = 5;

   blockElement('#categories');
   $.post(base_url + 'articlemanager/ajaxwork', parameters , function(data)
   {                                             
      $('#categories').html(data.html);
      unblockElement('#categories');      
   }, "json");
}

function refreshArticles()
{
      var category_id = $('#category_id').val();
      if (!category_id) return;
      
      var parameters = {};
      parameters['type'] = 6;
      parameters['category_id'] = category_id;
      
      blockElement("#files_listing");
      $.post(base_url + 'articlemanager/ajaxwork', parameters , function(data){
            
                $('#files_listing').html(data.html);
                unblockElement('#files_listing');
                
            },
            "json");
}

function change_order(article_id, direction)
{
   var parameters = {};
   parameters['type'] = 9; // Change article order
   parameters['article_id'] = article_id;
   parameters['direction'] = direction;

   blockElement("#files_listing");;
   
   $.post(base_url + 'articlemanager/ajaxwork', parameters , function(data)
   {                                             
      unblockElement('#files_listing'); 
      
      if(data.message == "OK")
      {
      	// The reordering was successful, reload the articles list.
      	refreshArticles();
		}
		else
		{
			alert("Sorry an error occured and your request could not be processed");
		}
           
   }, "json");
}

function search()
{
       var searchfor = $("#article_search").val();
       var website = $("#website_search").val();
       var category_id = $('#category_id').val();
       var parameters = {};
    
       parameters['type'] = 10;
       parameters['tosearch'] = searchfor;
       parameters['website'] = website;
       parameters['category_id'] = category_id;
        
       blockElement("#files_listing");
       $.post(base_url + 'articlemanager/ajaxwork', parameters , function(data){
            
                $('#files_listing').html(data.html);
                unblockElement('#files_listing');
                
       },
       "json");
}


function addParameters(parameters)
{
     var searchfor = $("#article_search").val();
     var website = $("#website_search").val();
     var category_id = $('#category_id').val();
         
     parameters['tosearch'] = searchfor;
     parameters['website'] = website;
     parameters['category_id'] = category_id;
         
    return parameters;
}
   