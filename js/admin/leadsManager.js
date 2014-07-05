

var page_name = 'leadsmanager';



$(document).ready(function(){



    $('#delete').live('click',function(){

    

        if ($("input[@name='itemstodelete[]']:checked").length == 0)

        {

            alert('Please click on the checkbox to select the lead that you want to delete.');

            return;

        }





	    if (confirm("Are you sure you want to delete the selected leads?  This action is not reversible and the leads will not be able to access the system afterwards."))

	    {

	        var selectedvalues = "";

	        $("input[@name='itemstodelete[]']:checked").each(function(){

	            selectedvalues += $(this).val() +';';

	        });

    	    

    	    var parameters = {};

    	    

    	    parameters['type'] = 1;

	        parameters['todelete'] = selectedvalues;

	        

    	    refreshListing(page_name, parameters, function(){ init_pagination(page_name) })

	    }

     

	});

    

    $("#lead_search").bind("keypress", function (e) {

         

         if(e.keyCode==13) //enter pressed

         {

               search();                   

         }

     });

     

     $("#name_type_search").bind("change",function(e){

         

         search();   

         

     });

	

	 //$("ul.skin2").tabs("div.skin2 > div");

	 init_pagination(page_name);

	

});



function search()

{

        var searchfor = $("#lead_search").val();

        var name_type = $("#name_type_search").val();

        var parameters = {};



        parameters['type'] = 3;

        parameters['tosearch'] = searchfor;

        parameters['name_type'] = name_type;



        refreshListing(page_name, parameters, function(){ init_pagination(page_name) })    

}



function addParameters(parameters)

{

     var searchfor = $("#lead_search").val();

     var name_type = $("#name_type_search").val();

         

     parameters['tosearch'] = searchfor;

     parameters['name_type'] = name_type;

         

    return parameters;

}