Date.format = 'yyyy-mm-dd';
$(document).ready(function(){
    
    editInPlace();    
    
    set_shipping_infos();
    
    $('.date-pick').datePicker();
    
    $("ul.skin2").tabs("div.skin2 > div");
    
    //custom validation rule 
    $.validator.addMethod("select_required", 

        function(value, element) {
                         
            return (value!=-1);
        }, 
        "No item selected."

    );
    
    
    $('#frmProduct').validate({
    
         errorPlacement: function(error, element) {

                error_obj = $('<span>').html(error).insertAfter(element);
                $('<span class="clear">').insertAfter(error_obj);
         } 
    });
    
    $("#require_shipping").live("click", function() {
        set_shipping_infos();	
    });
    
    $("#test_serial_gen").live("click", function() {
       
        //$("#device_id").val("");
        $("#serial_number label").html("");
        if( $("#serial_gen").val() != "0")        
    		$("#sg_test_form").show();
        else
            alert( "Please choose a serial generator");    
    });
    
    $("#submit_test_serial_gen").live("click", function() {
    	 var parameters = {};
            
     	parameters['type'] = 27;
    	parameters['serial_gen'] = $('#serial_gen :selected').val();
       	parameters['device_id'] = $('#device_id').val();  
            
  		blockElement("#sg_test_form");
            
        $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
            
            unblockElement("#sg_test_form");
            $("#serial_number label").html( "The result is: " + data['serial_number']);            
            $("#sg_test_form").show();
            
 		},"json");
    });
    
	$("input.numeric").numeric();         
    
    $('#btnUpdateProduct').click(function(){
        if ($('#frmProduct').valid())
        {
            if($("input[name=radio_hero_image]:checked").length != 0)
            {
                $("input[name=hero_image]").val($("input[name=radio_hero_image]:checked").val());                
            }
           
            if( $( '.css-tabs .current' ).html() == 'Downloads' )
        	{
            	var small_box_length = $( '#files_listing table input.small_box' ).length;
            	var i = 0;
            	
            	if( small_box_length > 0 )
            	{
	    	    	// save the access level
	    	    	$( '#files_listing table input.small_box' ).each(function(){
	    	    		++i;
	    	    		var name 		= jQuery( this ).attr( 'name' );
	    	    		var arr_name 	= name.split( 'level_for_' );
	    	    		var level_id	= jQuery( this ).val();
	    	    		var id 			= arr_name[1];
	    	    		
	    	    		var parameters				= {};
	    	    		parameters['type']			= '18';
	    	    		parameters['id']			= id;
	    	    		parameters['level_id']		= parseInt( level_id );
	    	    		
	    	    		jQuery.post( base_url + 'productmanager/ajaxwork', parameters, function(){
	    	    			
	    	    			if( i == small_box_length )
	    	    				setTimeout( "$('#frmProduct').submit()", 10 );
	    	    			
	    	    			++i;
	    	    		});
	    	    		
	    	    	});
            	}
            	else
            	{
            		$('#frmProduct').submit();
            	}
        	}
            else
            {
            	$('#frmProduct').submit();
            }
        }
    });
    
    $("#product_upload_file").makeAsyncUploader({
                upload_url: base_url +'productmanager/ajaxwork',
                flash_url: base_url + 'flash/admin/swfupload.swf',
                button_image_url: base_url + 'images/admin/selectsave.png',
                button_text: '',
                file_size_limit : '2 MB',
                beforeUpload: 'beforeProductUpload()',
                afterUpload: 'afterProductUpload()',
                debug : false                          
                               
    });  
    
    $("#product_upload_gallery_file").makeAsyncUploader({
        upload_url: base_url +'productmanager/ajaxwork',
        flash_url: base_url + 'flash/admin/swfupload.swf',
        button_image_url: base_url + 'images/admin/selectsave.png',
        button_text: '',
        file_size_limit : '2 MB',
        beforeUpload: 'beforeGalleryUpload()',
        afterUpload: 'afterGalleryUpload()',
        debug : false                          
                       
    });
    
    jQuery( '#product_upload_downloads' ).makeAsyncUploader({
        upload_url: base_url +'productmanager/ajaxwork',
        flash_url: base_url + 'flash/admin/swfupload.swf',
        button_image_url: base_url + 'images/admin/selectsave.png',
        button_text: '',
        file_size_limit : '20 MB',
        beforeUpload: 'beforeProductUploadDownloads()',
        afterUpload: 'afterProductUploadDownloads()',
        debug : false   
    });
    
    $(".download").live("click",function(e){
        
        var href = $(this).attr("href");
        
        var paremeters = {};
        paremeters['type'] = 6;
        paremeters['file'] = href;
        paremeters['product_id'] = $('#product_id').val();  
        
        $.download(base_url + 'productmanager/ajaxwork',paremeters);     
        
        e.preventDefault();
        
    }); 
    
    $("#delete_product_files").live('click',function(){
        
        if ($("input[name='product_filestodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?"))
        {
            var selectedvalues = "";
            $("input[name='product_filestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 7;
            parameters['todelete'] = selectedvalues;
            parameters['product_id'] = $('#product_id').val();  
            
            blockElement("#files_listing");
            
            $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
            
                unblockElement("#files_listing");
                if (data=="ok")
                    refreshProductFiles();
                else
                    alert("Error deleting file(s)");
            });
        }
        
    });
    
    $("#delete_gallery_files").live('click',function(){
        
        if ($("input[name='gallery_filestodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?"))
        {
            var selectedvalues = "";
            $("input[name='gallery_filestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 25;
            parameters['todelete'] = selectedvalues;
            parameters['product_id'] = $('#product_id').val();  
            
            blockElement("#gallery_listing");
            
            $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
            
                unblockElement("#gallery_listing");
                if (data=="ok")
                    refreshGalleryFiles();
                else
                    alert("Error deleting file(s)");
            });
        }
        
    });
    
    $("#delete_download_files").live('click',function(){
        
        if ($("input[name='product_downloadfilestodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?"))
        {
            var selectedvalues = "";
            $("input[name='product_downloadfilestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = '17';
            parameters['todelete'] = selectedvalues;
            parameters['product_id'] = $('#product_id').val();  
            
            blockElement("#files_listing");
            
            $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
            
                unblockElement("#files_listing");
                if (data=="ok")
                	refreshProductDownloadFiles();
                else
                    alert("Error deleting file(s)");
            });
        }
        
    });
    
    $('#frm_Bracket').validate({
        
        errorPlacement: function(error, element) {

               error_obj = $('<span>').html(error).insertAfter(element);
               $('<span class="clear">').insertAfter(error_obj);
        } 
    });
    
    // Add new bracket
    jQuery( '#btn_add_bracket' ).live( 'click', function(){
    	
    	if( jQuery( '#frm_Bracket' ).valid() )
    	{
	    	var parameters					= {};
	    	parameters['type']				= '11';
	    	parameters['product_id']		= jQuery( '#product_id' ).val();
	    	parameters['access_level_id']	= jQuery( '#access_level_id' ).val();
	    	parameters['description']		= jQuery( '#description' ).val();
	    	parameters['price']				= jQuery( '#price' ).val();
	    	parameters['bracket_max']		= jQuery( '#bracket_max' ).val();
	    	
	    	blockElement( '#bracket_listing' );
	    	jQuery.post( base_url + 'productmanager/ajaxwork', parameters, function(data){
	    		if( data.length > 0 )
	    			jQuery( '#bracket_listing' ).html( data );
	    		
	    		unblockElement( '#bracket_listing' );
	    	});
    	}
    });
    
    // delete bracket
    jQuery( 'a.delete_bracket' ).live( 'click', function(e){
    	e.preventDefault();
    	
    	if( confirm( 'Are you sure you want to delete this bracket?' ) )
    	{
	    	var parameters					= {};
	    	parameters['type']				= '10';
	    	parameters['price_id']			= jQuery( this ).attr( 'href' );
	    	parameters['product_id']		= jQuery( '#product_id' ).val();
	    	
	    	blockElement( '#bracket_listing' );
	    	jQuery.post( base_url + 'productmanager/ajaxwork', parameters, function(data){
	    		if( data.length > 0 )
	    			jQuery( '#bracket_listing' ).html( data );
	    		
	    		unblockElement( '#bracket_listing' );
	    	});
    	}
    });
    
    // change level for a bracket
    jQuery( 'select.access_level_id' ).live( 'change', function(){
    	var name = jQuery( this ).attr( 'name' );
    	var array = name.split( 'access_level_id' );
    	
    	var parameters					= {};
    	parameters['type']				= '12';
    	parameters['price_id']			= array[1];
    	parameters['access_level_id']	= jQuery( this ).val();
    	
    	jQuery.post( base_url + 'productmanager/ajaxwork', parameters );
    });
    
    // save the level for downloads
    jQuery( 'input.small_box' ).keypress(function(key){
    	
    	if( key.keyCode == '13' )
    	{
    		var name 		= jQuery( this ).attr( 'name' );
    		var arr_name 	= name.split( 'level_for_' );
    		var level_id	= jQuery( this ).val();
    		var id 			= arr_name[1];
    		
    		var parameters				= {};
    		parameters['type']			= '18';
    		parameters['id']			= id;
    		parameters['level_id']		= parseInt( level_id );
    		
    		jQuery.post( base_url + 'productmanager/ajaxwork', parameters );
    	}
    });
    
    // save the level is exact or not for downloads
    jQuery( 'input.exact_box' ).click(function(){
    	
    	var name 		= jQuery( this ).attr( 'name' );
		var arr_name 	= name.split( 'exact_level_for_' );
		var id 			= arr_name[1];
    	var is_exact	= '0';
		
		if( jQuery( this ).attr( 'checked' ) )
    	{
			is_exact	= '1';
    	}
		
		var parameters				= {};
		parameters['type']			= '19';
		parameters['id']			= id;
		parameters['is_exact']		= is_exact;
		
		jQuery.post( base_url + 'productmanager/ajaxwork', parameters );
    });
    
    $("#view_category").click(function(){
        
        var article_category_id = $("#article_category_id").val();
        
        if(article_category_id != "")        
        {
            window.open( base_url + "articlemanager/category/" + article_category_id);    
            return false
        }
        else
            alert("Please select an article category");
    });
});

// before upload an image
function beforeProductUpload()
{
    product_id = $("#product_id").val();
    swfu_array[current_swfu_id].addPostParam("type","5");
    swfu_array[current_swfu_id].addPostParam("product_id",product_id);
    
    return true;
} 

// after upload an image
function afterProductUpload()
{
    refreshProductFiles();
    return true;
}

function beforeGalleryUpload()
{
    product_id = $("#product_id").val();
    swfu_array[current_swfu_id].addPostParam("type","22");
    swfu_array[current_swfu_id].addPostParam("product_id",product_id);
    
    return true;
} 

// after upload an image
function afterGalleryUpload()
{
    refreshGalleryFiles();
    return true;
}

//refresh gallery images
function refreshGalleryFiles()
{
    var parameters = {};
    parameters['type'] = 23;
    parameters['product_id'] = $("#product_id").val();
    
    blockElement('#gallery_listing');
    $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
           
         $('#photo_listing').html(data.html);
         unblockElement('#gallery_listing');
    },"json");
    
    $('.ProgressBar').hide();
    $('#upload_file_uploading').hide();
    $('#product_upload_file_uploading').hide();
    $('#SWFUpload_0').show();
    $('#SWFUpload_0').css("width","");
}

// refresh images
function refreshProductFiles()
{
    var parameters = {};
    parameters['type'] = 1;
    parameters['product_id'] = $("#product_id").val();
    
    blockElement('#images_listing');
    $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
           
         $('#page_listing').html(data.html);
         unblockElement('#images_listing');
    },"json");
    
    $('.ProgressBar').hide();
    $('#upload_file_uploading').hide();
    $('#product_upload_file_uploading').hide();
    $('#SWFUpload_0').show();
    $('#SWFUpload_0').css("width","");
}

// before upload a download file
function beforeProductUploadDownloads()
{
    product_id = $("#product_id").val();
    swfu_array[current_swfu_id].addPostParam("type","13");
    swfu_array[current_swfu_id].addPostParam("product_id",product_id);
    
    return true;
} 

// ufter upload a download file
function afterProductUploadDownloads()
{
    refreshProductDownloadFiles();
    return true;
}   

//refresh downloads
function refreshProductDownloadFiles()
{
    var parameters = {};
    parameters['type'] = '16';
    parameters['product_id'] = $("#product_id").val();
    
    blockElement('#files_listing');
    $.post(base_url + 'productmanager/ajaxwork', parameters,function(data){
           
         $('#files_listing').html(data.html);
         unblockElement('#files_listing');
         editInPlace();
    },"json");
    
    $('.ProgressBar').hide();
    $('#upload_file_uploading').hide();
    $('#product_upload_file_uploading').hide();
    $('#SWFUpload_0').show();
    $('#SWFUpload_0').css("width","");
}

// edit image name
function editInPlace()
{
    // This example only specifies a URL to handle the POST request to
    // the server, and tells the script to show the save / cancel buttons
    $(".editme").editInPlace({        
        url: base_url + "productmanager/ajaxwork",
        default_text: "(Click here to add decription text)",
        params: 'type=8'        
    });
    
    $(".gallery_editme").editInPlace({        
        url: base_url + "productmanager/ajaxwork",
        default_text: "(Click here to add decription text)",
        params: 'type=24'        
    });
    
    jQuery( '.edit_description' ).editInPlace({        
        url: base_url + "productmanager/ajaxwork",
        default_text: "(Click here to add decription text)",
        params: 'type=15'
    });
    
    jQuery( '.edit_name' ).editInPlace({        
        url: base_url + "productmanager/ajaxwork",
        default_text: "(Click here to add decription text)",
        params: 'type=26'
    });
    
    jQuery( '.edit_price' ).editInPlace({        
        url: base_url + "productmanager/ajaxwork",
        default_text: "(Click here to add price)",
        params: 'type=21'
    });
    
    jQuery( '.edit_quantity' ).editInPlace({        
        url: base_url + "productmanager/ajaxwork",
        default_text: "(Click here to add quantity)",
        params: 'type=20'
    });
}      


function set_shipping_infos()
{
    if( $("#require_shipping").is(':checked') )
    {
        $('#product_sizes input').addClass("required")
        $("#product_sizes").show();
    }
    else
    {
        $('#product_sizes input').removeClass("required")
        $("#product_sizes").hide();
    }
}