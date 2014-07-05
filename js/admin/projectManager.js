var page_name = 'projectmanager';

$(document).ready(function(){

    $('#apply').live('click',function(){
        if($("#functions_list option:selected").val() =="" )
        {
            alert('Please choose a function to apply.');
            return;            
        }
        
        if ($("input[@name='projectstodelete[]']:checked").length == 0) {
            alert('Please click on the checkbox to select the project you want to apply.');
            return;
        }
        
        var funtion_selected = $("#functions_list option:selected").val();
        var selectedvalues = "";
        var parameters = {};
		
		parameters['state_id'] = $('#state_id_search').val();
        parameters['area_id'] = $('#area_id_search').val();
		
        
        $("input[@name='projectstodelete[]']:checked").each(function(){
            selectedvalues += $(this).val() +';';
        });        
        
        switch(funtion_selected)
        {
            case "delete":
                if (confirm("Are you sure you want to delete the selected projects?"))
                {
                    parameters['type'] = 1;
                    parameters['todelete'] = selectedvalues;
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
                } 
                break;  
                
            case "feature":    
                if (confirm("Are you sure set FEATURED for the selected projects?"))
                {            
                    parameters['type'] = 20;
                    parameters['action'] = "feature";
                    parameters['tofeature'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break;   
                
            case "unfeature":  
                if (confirm("Are you sure set NOT TO FEATURED for the selected projects?"))
                {                 
                    parameters['type'] = 20;
                    parameters['action'] = "unfeature";
                    parameters['tofeature'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break; 
                
            case "newsletter":    
                if (confirm("Are you sure set ON NEWSLETTER for the selected projects?"))
                {               
                    parameters['type'] = 21;
                    parameters['action'] = "newsletter";
                    parameters['tonewsletter'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break;   
                
            case "unnewsletter":    
                if (confirm("Are you sure set NOT ON NEWSLETTER the selected projects?"))
                {
                    parameters['type'] = 21;
                    parameters['action'] = "unnewsletter";
                    parameters['tonewsletter'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })                      
                }
                
                break;    
                
            case "website":    
                if (confirm("Are you sure set ON WEBSITE for the selected projects?"))
                {            
                    parameters['type'] = 22;
                    parameters['action'] = "website";
                    parameters['towebsite'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break;   
                
            case "unwebsite":    
                if (confirm("Are you sure set NOT ON WEBSITE the selected projects?"))
                {             
                    parameters['type'] = 22;
                    parameters['action'] = "unwebsite";
                    parameters['towebsite'] = selectedvalues;  
                    
                    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })  
                }
                break;                                                         
            
        }
                
    });
	
	
	
	$('#state_id_search').live('change',function(){
         loadProperty();
    });     
     
    $('#area_id_search').live('change',function(){
         loadProperty();
    });
	
	
	
 
     $("#project_search").bind("keypress", function (e) {
         
         if(e.keyCode==13) //enter pressed
         {
             var searchfor = $(this).val();
             var parameters = {};
            
             parameters['type'] = 3;
             parameters['tosearch'] = searchfor;
            
             refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
         }
     });
     
	$("table.cmstable th").live('click',function(e)
	{
        var sort_by = $(this).attr("sort");
        
        if(sort_by == sort_col)
        {
            if(sort_dir == "ASC")
            {
                sort_dir = "DESC";    
            }
            else
            {
                sort_dir = "ASC";    
            }
        }    
        else
        {
            sort_col = sort_by;
            sort_dir = "ASC";         
        }
        
        var parameters = {};
        
        $("#sort_col").val(sort_col);
        $("#sort_dir").val(sort_dir);
        
        parameters['type'] = 3;
        parameters['sort_col'] = $("#sort_col").val();
        parameters['sort_dir'] = $("#sort_dir").val();
     	parameters['tosearch'] = $("#project_search").val();
     	
     	refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
    });
     
	init_pagination(page_name);
    
});


function loadProperty()
{
    var parameters = {};
    var kw = $('#property_search').val();
    
    if($("#archived_search").is(':checked'))
        var archived = 1;
    else
        var archived = 0;

    if (kw == 'Enter Search Keywords') kw = '';
    parameters['type'] = 3;
  
    parameters['state_id'] = $('#state_id_search').val();
    parameters['area_id'] = $('#area_id_search').val();
   
    parameters['status'] = $('#status_search').val();
    parameters['archived'] = archived;
    blockElement('#page_listing');
    refreshListing(page_name, parameters, function(){
        init_pagination(page_name)
    })
    unblockElement('#page_listing');
}
