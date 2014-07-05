var page_name = 'propertymanager';        

function toFixed(value, precision) {
    var power = Math.pow(10, precision || 0);
    return String(Math.round(value * power) / power);
}

var formatFloat = function(num) {
    if (num=='') return '';
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num)) num = '0';
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10) cents = '0' + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
    return (((sign) ? '' : '-') + num + '.' + cents);
};

var calculateAmount = function () {
    var land_price = parseInt($("#land_price").val());
    var house_price = parseInt($("#house_price").val());
    if (isNaN(land_price)) land_price = 0;
    if (isNaN(house_price)) house_price = 0;
    var total_price = land_price + house_price;
    $("#total_price").val((total_price != 0) ? parseInt(total_price) : 0);
    $("#total_price").attr("readonly",true);
}

var calculateRentYield = function () {
	var approx_rent = parseInt($('#approx_rent').val());
	var total_price = parseInt($('#total_price').val());
	var rent_yield = ( (approx_rent * 52) / total_price ) * 100;
	rent_yield = toFixed(rent_yield , 1);
	$('#rent_yield').val(rent_yield);
}

$(document).ready(function(){
    
	calculateRentYield();
	
	$('#approx_rent').live('keyup',function(){
		calculateRentYield();
	});
	
    $("#total_price").attr("readonly",false);
    
    $("ul.skin2").tabs("div.skin2 > div");
    
//    $('#frmProperty').validate();
    
    $("#frmProperty #button").live("click",function(){        
                
        if(!$('#frmProperty').valid())
        	alert("Please fill in all required fields");
        else
            $(this).submit();
    });
    
    $("#land_price, #house_price").live("keyup",function(){
        calculateAmount();
    });
    
    $("#tabGallery").click(function()
	{
		// Setup hero image uploader
		var gUploader = new qq.FileUploader(
		{
			// pass the dom node (ex. $(selector)[0] for jQuery users)
			element: document.getElementById('upload_file'),
			// path to server-side upload script
			action: base_url + 'admin/propertymanager/upload_file/gallery_image',
		    params: {
                "property_id" : $("#property_id").val(),
                "doc_id" : false,
                "doc_name" : false
		    },
		    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
		    sizeLimit: 21000000, // max size 
		    onComplete: function(id, fileName, responseJSON)
		    {
    			if(responseJSON.success)
    			{
					// The upload completed successfully.
					refreshFolderFiles();
    			}
		    }
		});			
	});
	
    $("#tabDocument").click(function()
	{
	    // Setup hero image uploader
	    $(".doc_upload_file").each(function(){
	        var doc_id = $(this).attr('did');
        	var dUploader = new qq.FileUploader(
        	{
        		// pass the dom node (ex. $(selector)[0] for jQuery users)
        			element: document.getElementById('doc_upload_file_'+doc_id),
        			// path to server-side upload script
        			action: base_url + 'admin/propertymanager/upload_file/documents',
        		    params: {
                        "property_id" : $("#property_id").val(),
                        "doc_id" : doc_id,
                        "doc_name" : $("#doc_"+ doc_id +"_name").val(),
        		    },    
        		    sizeLimit: 21000000, // max size 
        		    onComplete: function(id, fileName, responseJSON)
        		    {
            			if(responseJSON.success) {
        					$("#docpath_" + doc_id).html(responseJSON.fileName);
                            $("#docpath_" + doc_id).show();
                            $("#delete_doc_" + doc_id).show();
                            $('#doc_upload_file_'+doc_id).hide();
            			}
        		    }
		      });
		});			
	});
    
    $(".download_property").live("click",function(e){
        
        var href = $(this).attr("href");
        
        var paremeters = {};
        paremeters['type'] = 6;
        paremeters['file'] = href;
        paremeters['property_id'] = $('#property_id').val();  
        
        $.download(base_url + 'admin/propertymanager/ajaxwork',paremeters);     
        
        e.preventDefault();
        
    });
    
    $("#delete_files").live('click',function(){
        
        if ($(":checkbox[name='property_imagestodelete[]']:checked").length == 0)                        
        {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?"))
        {
            var foldername = $('#folder').val();         
            if (!foldername) return;
            
            var selectedvalues = "";
            $(":checkbox[name='property_imagestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 7;
            parameters['todelete'] = selectedvalues;
            parameters['folder'] = foldername;
            parameters['property_id'] = $("#property_id").val();
            
            blockElement("#files_listing");
            
            $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            
                unblockElement("#files_listing");
                refreshListing(page_name, parameters, function(){ edit_in_place();});
                $('.qq-upload-list').hide();
            });
        }
        
    });
    
   /* var reservation_dates = new Array("10/12/2009","03/12/2009","22/12/2009");    */
    
    $('.date-pick').datePicker({
                        inline      :true,
                        tablewidth  :870,
                        tableheight :350,
                        customclass :'reservation',
                        startDate : '01/01/1970',
                        hoverClass: '',    
                        renderCallback:function($td, thisDate, month, year)
                        {
                                $td.addClass('disabled');
                                for( var i =0; i<= reservation_dates.length; i++)
                                    if(reservation_dates[i] == thisDate.asString()) 
                                        $td.addClass('selected');
                                   /* else    
                                        $td.addClass('disabled');*/
                        }
    }) .bind(
            'dateSelected',
            function(e, selectedDate, $td)
            {
                $td.removeClass('selected'); 
                return false;
            }
        );
        
        
    $(".del_path").live('click',function(){
      
       var str = $(this).attr("id");
       var doc_id = str.replace("delete_doc_", "");
       $('.qq-upload-list').hide();
       delete_document_path(doc_id);
       
       $("#docpath_" + doc_id).html("");
       $(this).hide();
       
    });
    
    $("#project_id").live( $.browser.msie ? "click" : "change",function(){
        
        var property_id = $("#property_id").val();
        if(property_id != "")
        {
            var project_id = $(this).val();
            
            var parameters = {};
                
            parameters['type'] = 12;
            parameters['project_id'] = project_id;
            parameters['property_id'] = property_id;
            
            blockElement(".css-panes");
            
            $(".specifications").load(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            
                unblockElement(".css-panes");               
               
            });            
        }
        
    });
    
    $('#changestatus').live('click',function(){
        var status = $('.status').val();
        if (status == 'available') {
            $('.status_user_area').hide();
        } else {
            $('.status_user_area').show();
        }
        
        $.fancybox({
            'href' : '#frm_change_status',
        });
    });
    
    $('.status').live('change',function(){
        var status = $(this).val();
        if ((status == 'pending') || (status == 'available')) {
            $('.status_user_area').hide();
        } else {
            $('.status_user_area').show();
        }
    });
    
    $('.advisor_id').live('change',function(){
        var parameters = {};
        parameters['type'] = 9;
        parameters['advisor_id'] = $(this).val();
        blockElement('#frm_change_status');
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            $('.partner_id').html(data.partner_options_html);
            $('.investor_id').html(data.investor_options_html);
            unblockElement('#frm_change_status');
        },"json");
    });
    
    $("#btnApprove").click(function(e) {
        e.preventDefault();
        
        if(!confirm("If you approve this property then it will start appearing in the portal.  Are you sure you wish to proceed?")) return;
        
        var property_id = $("#property_id").val();
        
        var parameters = {};
        parameters['type'] = 15;
        parameters['status'] = "available";
        parameters['property_id'] = property_id;
        
        blockElement("#frmProperty");
        
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data) {
            unblockElement("#frmProperty");
            
            if (data.success) {
                window.location.href = base_url + "admin/propertymanager/property/" + property_id;
            } else {
                alert('Can not update property status.');
            }
        },'json');        
            
    });
    
    $('.updatestatus').live('click',function(){
        var parameters = {};
        var status = $('.status').val();
        
        if ((status == 'pending') || (status == 'available')) {
            parameters['advisor_id'] = -1;
            parameters['partner_id'] = -1;
            parameters['investor_id'] = -1;
        } else {
            if ($('.advisor_id').val() == -1) {
                alert('Advisor field is required.');
                return;
            }
            parameters['advisor_id'] = $('.advisor_id').val();
            parameters['partner_id'] = $('.partner_id').length == null ? -1 : $('.partner_id').val();
            parameters['investor_id'] = $('.investor_id').length == null ? -1 : $('.investor_id').val();
        }
        parameters['type'] = 15;
        parameters['status'] = status;
        parameters['property_id'] = $('.updatestatus').attr('pid');
        blockElement("#frm_change_status");
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            unblockElement("#frm_change_status");
            if (data.success) {
                $('.status_info').html(data.status);
                if (status != 'available') {
                    $('#tabStage').show();
                } else {
                    $('#tabStage').hide();
                }
                $.fancybox.close();
            } else {
                alert('Can not update property status.');
            }
        },'json');
    });
    
    $("#delete").live('click',function(){
        
        if ($(".stage_to:checked").length == 0) {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?")) {
            var selectedvalues = "";
            $(".stage_to:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            var parameters = {};
            parameters['type'] = 17;
            parameters['todelete'] = selectedvalues;
            blockElement(".stagelisting");
            
            $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
                refreshStage();
                unblockElement(".stagelisting");
            });
        }
    });
    
    $("#complete").live('click',function(){
        
        if ($(".stage_to:checked").length == 0) {
            alert('Please click on the checkbox to select the files you want to set completed.');
            return;
        }
        
        if (confirm("Are you sure you want to set completed the selected files?")) {
            var selectedvalues = "";
            $(".stage_to:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            var parameters = {};
            
            parameters['type'] = 18;
            parameters['tocomplete'] = selectedvalues;
            blockElement(".stagelisting");
            
            $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
                refreshStage();
                unblockElement(".stagelisting");
            });
        }
    });
    
    $('#nras').live('click',function(){
        if ($('#nras:checked').val() == 1) 
            $('.nras').show();
        else
            $('.nras').hide();
    });
    
    if ($('#nras:checked').val() == 1) 
        $('.nras').show();
    else
        $('.nras').hide();
        
    $('#titled').live('click',function(){
        if ($('#titled:checked').val() == 1) 
            $('.estimated_date').hide();
        else
            $('.estimated_date').show();
    });
    
    if ($('#titled:checked').val() == 1) 
        $('.estimated_date').hide();
    else
        $('.estimated_date').show();
    
    edit_in_place();
});

function change_order(stage_id, direction)
{
   var parameters = {};
   parameters['type'] = 20; // Change stage order
   parameters['stage_id'] = stage_id;
   parameters['property_id'] = $('#property_id').val();
   parameters['direction'] = direction;
   blockElement(".stagelisting");;
   $.post(base_url + 'admin/propertymanager/ajaxwork', parameters , function(data) {
        unblockElement('.stagelisting'); 
        if(data.message == "OK") refreshStage();
        else alert("Sorry an error occured and your request could not be processed");
   }, "json");
}

function refreshStage()
{
    var parameters = {};
    parameters['type'] = 19;
    parameters['property_id'] = $('#property_id').val();
    $(".construction_tracker").load(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
    });
}

function refreshFolderFiles(selected_folder)
{
    var parameters = {};
    parameters['type'] = 5;
    parameters['folder'] = selected_folder;
    parameters['property_id'] = $("#property_id").val();
    
    blockElement('#files_listing');
    $("#page_listing").load(base_url + 'admin/propertymanager/ajaxwork', parameters,function(){
    
       unblockElement('#files_listing');
       edit_in_place();     
    });
    
    $('.ProgressBar').hide();
    $('#upload_file_uploading').hide();
   /* $('#SWFUpload_0').show();*/
    $('#SWFUpload_0').css("width","");      
    
}

function refresh_document_path(doc_id)
{
    var parameters = {};
    parameters['type'] = 10;
    parameters['doc_id'] = doc_id;
    
    blockElement('.document_' + doc_id);
    
    $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
       
        unblockElement('.document_' + data.doc_id);    
        $("#docpath_" + data.doc_id).html(data.document_path);
        $("#docpath_" + data.doc_id).show();
        $("#delete_doc_" + data.doc_id).show();
        $('.document_' + data.doc_id + ' .asyncUploader').hide();
        
    },"json");
}

function delete_document_path(doc_id)
{
    var parameters = {};
    parameters['type'] = 11;
    parameters['doc_id'] = doc_id;
    
    blockElement('.document_' + doc_id);
    
    $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
        
        unblockElement('.document_' + data.doc_id);    
        $('#doc_upload_file_'+ data.doc_id).show();
              
    },"json");
}

function edit_in_place()
{
    $(".edit_alt").editInPlace({
        url: base_url + "admin/propertymanager/ajaxwork",
        default_text: "(Click here to add Caption)",
//        save_if_nothing_changed: true,
//        show_buttons: true,
        params: "type=25"
    });
}

$(function(){

    
    $('#submitbutton,#submitbutton2').click(function()
    {
        var form = $("#frmProperty");
        
        if(!$(form).validate().form())
        {
            alert("Please fill in all required fields");
            return;    
        }
        
        $(form).submit();
    });
    
    $(':radio[name="hero_image"]').click(function(){
        var val = this.value;
        $(':hidden[name="hero_image"]').val(val);
    });
})

var make_key_date_default =function(){
            $('#description').val('');
            $('#keydate_id').val('');
            $('#estimate_date').val('');
                    
       };

$('#addkeydate').live('click',function(){
		make_key_date_default();
        $.fancybox({
            'href' : '#formaddkeydates',
        });
    });        
           	    
    
	var refeshKeydates = function(){
        var property_id =  $("#property_id").val()
        var parameters = {};
        parameters['type'] = 33;
        parameters['property_id'] = property_id;
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            if (data) {
                $('.keydatelisting').html(data);    
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
            
        });
    };
    
    $('#estimate_date').live('change',function() {
	$('#followup_date').val($('#estimate_date').val());
	});
	
	$('#actual_date').live('change',function() {

		var followup_date = $('#actual_date').val();
		var d = new Date(followup_date);

		var jaffa = d.setDate(d.getDate()-3);
		var nd = new Date(jaffa);
		var curr_date = nd.getDate();
		var curr_month = nd.getMonth() + 1; //Months are zero based
		if (curr_date < 10) 
		{
		curr_date = '0' + curr_date; 
		}
		if (curr_month < 10) 
		{
		curr_month = '0' + curr_month; 
		}
		var curr_year = nd.getFullYear();
		var new_date = curr_month + "/" + curr_date  + "/" + curr_year;
		$('#followup_date').val(new_date);

	});
	
    $('.savekeydate').live('click',function(){
        var parameters = {};
		
        var description = $('#description').val();
        var property_id = $('#property_id').val();
        var keydate_id = $('#keydate_id').val();
        var estimate_date = $('#estimate_date').val();
        var actual_date = $('#actual_date').val();
        var followup_date = $('#followup_date').val();
		
        if (description.length == 0) {
            alert('Key date description is required');
            return false;
        }
        if (estimate_date.length == 0) {
            alert('Estimated Date is required');
            return false;
        }
		
	
                
        blockElement('.keydatelisting');
        parameters['description'] = description;
        parameters['property_id'] = property_id;
        parameters['keydate_id'] = keydate_id;
        parameters['estimate_date'] = estimate_date;
        parameters['followup_date'] = followup_date;
        parameters['actual_date'] = actual_date;
       		
		parameters['type'] = 32;
       		
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            unblockElement('.keydatelisting');
            if (data == 'OK') {
                refeshKeydates();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });

    $('#deletekeydate').live('click',function(){
        if ($(".keydatetodelete:checked").length == 0) {
            alert('Please select at least one comment you want to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected comment(s)?")) {
            var selectedvalues = "";
            var aItems = [];
            $(".keydatetodelete:checked").each(function(){
                var itemID = $(this).val();
                selectedvalues += itemID +';';
                aItems.push(itemID);
            });
            var parameters = {};
            parameters['type'] = 35;
            parameters['todelete'] = selectedvalues;
            $.post(base_url + 'admin/propertymanager/ajaxwork', parameters , function(data)
            {
                if(data == 'OK') {
                    refeshKeydates();
                    alert('Selected comment(s) was removed successfully.');
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            });
        }
    });

    $('.editkeydate').live('click',function(){
		make_key_date_default();	
        var keydate_id = $(this).attr('rel');
        var parameters = {};
        blockElement(".keydatelisting");;
        parameters['type'] = 34;
        parameters['keydate_id'] = keydate_id;
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters, function(data){
            unblockElement(".keydatelisting");
            if (data.status == 'OK') {
                $('#estimate_date').val(data.estimate_date);
                $('#actual_date').val(data.actual_date);
                $('#followup_date').val(data.followup_date);
                $('#description').val(data.description);
                $('#keydate_id').val(data.keydate_id);
				
				$.fancybox({
                    'href' : '#formaddkeydates',
                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });