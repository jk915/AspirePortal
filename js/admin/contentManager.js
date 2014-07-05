$(document).ready(function() 
{
	$("#breadcrumbs").jBreadCrumb(); 
	
	// If the user has just tabbed out of the article_title field,
	// see if the article_code field is blank.   If it is, create an article code
	// for the user automagically.
	$("#new_category").bind('blur',function()
	{
		// Make sure the current code is blank
		var current_code = $("#new_category_code").val();

		if(current_code != "")
			return;
			
		// Get the article title
        var code = $("#new_category").val();
        if(code == "")
            return;
            	
        //if(category_name != "")
        //	code = category_name + "-" + code;

        // Replace spaces, underscores, punctuation etc.
		code = code.toLowerCase();	// Covert code to upper case
   		code = code.replace(/[ ,_]/g, "-");	// Replace spaces with dashes
		code = code.replace(/[^a-z0-9-]/g, "");	// Replace other punctionation with nothing
		code = code.replace(/--/g, "-");	// Replace double dashses with a single dash
		code = code.replace(/-$/g, "");	// If there's a dash at the end, kill it.
		
		// Set the code.
		$("#new_category_code").val(code);
	});		

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
			
			$.post(base_url + 'admin/contentmanager/ajaxwork', parameters , function(data)
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
			$.post(base_url + 'admin/contentmanager/ajaxwork', parameters , function(data)
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
		var url = base_url + "admin/contentcategorymanager/edit/" + category_id;
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
            
            $.post(base_url + 'admin/contentmanager/ajaxwork', parameters,function(data){
            
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
    
    $("#categories").change(function() 
    {
        refreshArticles(); 
    });
    
    $("#article_search").bind("keypress", function (e) 
    {
         if(e.keyCode==13) //enter pressed
         {
            search();   
         }
     });

     $("#article_search_button").click(function()
     {
        search();
     });
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

function refreshCategories()
{
   var parameters = {};
   parameters['type'] = 5;

   blockElement('#categories');
   $.post(base_url + 'admin/contentmanager/ajaxwork', parameters , function(data)
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
      $.post(base_url + 'admin/contentmanager/ajaxwork', parameters , function(data){
            
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
   
   $.post(base_url + 'admin/contentmanager/ajaxwork', parameters , function(data)
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
	
	$.post(base_url + 'admin/contentmanager/ajaxwork', parameters , function(data)
	{
		$('#files_listing').html(data.html);
		unblockElement('#files_listing');
	}, "json");
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
   