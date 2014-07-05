var page_name = 'ordermanager';
Date.format = 'yyyy-mm-dd';
var refresh_function    = 'search';

$(document).ready(function(){

    $('.date-pick').datePicker({startDate:'01/01/1970'});
    
    $('#delete').live('click',function(){
    
        if ($("input[@name='orderstodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the order you want to delete.');
            return;
        }


        if (confirm("Are you sure you want to delete the selected orders?"))
        {
        	var parameters = {};
            var selectedvalues = "";
            $("input[@name='orderstodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            
            search( parameters );            
        }
    });
     
    init_pagination(page_name);
     
    $(".download").live("click",function(e){
        
        var href = $(this).attr("title");
        var parameters = {};
        e.preventDefault();
        
        if(href != "")
        {
            var parameters = {};
            parameters['type'] = 6;
            parameters['file'] = href;        
           
            $.download(base_url + 'ordermanager/ajaxwork',parameters);     
        }
        else
            alert("No pdf generated or the pdf field is empty.")
        
    }); 
    
    $("#search").live("click",function(e){
        
        var parameters = {};
        parameters['type'] = 2;
        
        search( parameters );          
        
    });
    
    $("#search_period").change(function() {
        
        if($("#search_period").val() == "choose")
            $("#choose_date").show();
        else
            $("#choose_date").hide();        
    });
    
    $('.order_link').live('click',function(e){
        e.preventDefault();    
        var parameters = {};
        parameters['type'] = 10;
        parameters['order_column'] = $(this).attr('href');
        if($(this).attr('href') == $('#order_column').val())
        {
            parameters['order_direction'] = ($('#order_direction').val() == 'ASC') ? 'DESC' : 'ASC';
        }
        else
            parameters['order_direction'] = 'ASC';
        parameters['order_table'] = $('#order_table').val();
        
        search( parameters );
    });
    
    $('#years').change( function(){
        
        var parameters    = {};
        parameters['type']    = 3;
        parameters['year']    = $(this).val();
        
        blockElement('#block_bar');
        $.post( base_url + page_name +'/ajaxwork', parameters, function( data ){
            load_chart( 'bar_charts', data.bar_chart );
            unblockElement('#block_bar');
        },'json');
    } );
    
    $('#change_date').click( function(){
        var parameters    = {};
        parameters['type']    = 4;
        parameters['year']    = $('#pie_years').val();
        parameters['month']    = $('#pie_months').val();
        
        blockElement('#block_pie');
        $.post( base_url + page_name + '/ajaxwork', parameters, function( data ){
            load_chart( 'pie_charts', data.pie_chart );
            unblockElement('#block_pie');
        },'json');
    } );
    
    $('#change_product_date').click( function(){
        var parameters    = {};
        parameters['type']    = 5;
        parameters['year']    = $('#product_years').val();
        parameters['month']    = $('#product_months').val();
        
        blockElement('#block_product_bar');
        $.post( base_url + page_name + '/ajaxwork', parameters, function( data ){
            load_chart( 'product_bar_chart', data.product_bar_chart );
            unblockElement('#block_product_bar');
        },'json');
    } );
    
});

function addParameters(parameters)
{       
    var search_name = $("#search_name").val();
    if($("#search_doc_type").length > 0)
        var search_doc_type = $("#search_doc_type").val();
    else
        var search_doc_type = "";
            
    var search_period = $("#search_period").val();
    var start_date = $("#start_date").val();
    var end_date = $("#end_date").val();
    if($("#search_status").length > 0)
        var search_status = $("#search_status").val();
    else
        var search_status = "";    
    
    parameters['search_name'] = search_name;        
    parameters['search_doc_type'] = search_doc_type;        
    parameters['search_period'] = search_period;        
    parameters['start_date'] = start_date;        
    parameters['end_date'] = end_date;  
    parameters['search_status'] = search_status;
    
    return parameters;
}

function search(parameters)
{
    parameters = addParameters(parameters);
            
    refreshListing(page_name,parameters, function(){ init_pagination(page_name) })
}
