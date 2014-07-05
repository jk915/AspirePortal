var page_name = 'areamanager';
var sort_col = "nc_areas.area_name";
var sort_dir = "ASC";

$(document).ready(function(){

    $('#delete').live('click',function(){
    
        if ($("input[@name='areastodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the area you want to delete.');
            return;
        }


        if (confirm("Are you sure you want to delete the selected areas?"))
        {
            var selectedvalues = "";
            $("input[@name='areastodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
			
			parameters['state_id'] = $('#state_id_search').val();
			parameters['project_id'] = $('#project_id_search').val();
            
            refreshListing(page_name, parameters, function(){ init_pagination(page_name, 3) })
        }
    });
	
	
	$('#state_id').live('change',function(){
         doSearch();
    });     
     
    $('#project_id').live('change',function(){
         doSearch();
    });
	
	
	$("#area_search").bind("keypress", function (e) {
        if(e.keyCode==13)  { //enter pressed
            e.preventDefault();
            doSearch();
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

        doSearch();
	});
    
    doSearch();
     
	//init_pagination(page_name);
});

function get_pagination_parameters() {
    parameters = get_params();
    return parameters;
}

function get_params()
{
    var parameters = {};
    parameters['type'] = 3;
    parameters["keywords"] = $('#area_search').val();
    parameters['state_id'] = $('#state_id').val();
    parameters['project_id'] = $('#project_id').val();
    parameters['sort_col'] = $("#sort_col").val();
    parameters['sort_dir'] = $("#sort_dir").val();   
    
    return parameters;    
}


function doSearch()
{    
    parameters = get_params();

    blockElement('#page_listing');
    
    refreshListing(page_name, parameters, function(){
        unblockElement('#page_listing');
        init_pagination(page_name, 3);
    })    
}

