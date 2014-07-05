var page_name = 'regionmanager';
var sort_col = "nc_regions.region_name";
var sort_dir = "ASC";

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='regionstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the region you want to delete.');
            return;
        }


        if (confirm("Are you sure you want to delete the selected regions?"))
        {
            var selectedvalues = "";
            $("input[@name='regionstodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
			
			parameters['region_id'] = $('#region_id_search').val();
			parameters['state_id'] = $('#state_id_search').val();
            
            refreshListing(page_name, parameters, function(){ init_pagination(page_name) })
        }
    });
	
	
	$('#region_id_search').live('change',function(){
         loadProperty();
    });     
     
    $('#state_id_search').live('change',function(){
         loadProperty();
    });
	
	
	$("#region_search").bind("keypress", function (e) {
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
     	parameters['tosearch'] = $("#region_search").val();
     	
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
  
    parameters['region_id'] = $('#region_id_search').val();
    parameters['state_id'] = $('#state_id_search').val();
   
    parameters['status'] = $('#status_search').val();
    parameters['archived'] = archived;
    blockElement('#page_listing');
    refreshListing(page_name, parameters, function(){
        init_pagination(page_name)
    })
    unblockElement('#page_listing');
}

