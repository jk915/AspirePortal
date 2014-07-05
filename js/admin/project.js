var page_name = 'projectmanager';  
var refeshMeta = function(){
    var project_id =  $("#project_id").val()
    var parameters = {};
    parameters['type'] = 16;
    parameters['project_id'] = project_id;
    $.post(base_url + 'admin/projectmanager/ajaxwork', parameters,function(data){
        if (data) {
            $('.metalisting').html(data);    
        } else {
            alert("Sorry an error occured and your request could not be processed");
        }
        
    });
};

$(document).ready(function()
{
	$('#submitbutton,#submitbutton2').click(function(){
        $('#frmProject').submit();
    });
	
    $('#openformadd').live('click',function(){
        $('#heading').val('');
        $('#wysiwyg4').val('');
        $('#meta_id').val('');
        $('#icon_image').val('');
        $.fancybox({
            'href' : '#formaddwrap',
        });
    });
    
    $('.savemeta').live('click',function(){
        var parameters = {};
        var title = $('#heading').val();
        var content = $('#wysiwyg4').val();
        var meta_id = $('#meta_id').val();
        var project_id = $('#project_id').val();
        var icon_image = $('#icon_image').val();
        if (title.length == 0) {
            alert('Section Name is required');
            return false;
        } else if (content.length == 0) {
            alert('Section Content is required');
            return false;
        }
        blockElement('.metalisting');
        parameters['content'] = content;
        parameters['title'] = title;
        parameters['meta_id'] = meta_id;
        parameters['project_id'] = project_id;
        parameters['icon_image'] = icon_image;
        parameters['type'] = 15;
        
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters,function(data){
            unblockElement('.metalisting');
            if (data == 'OK') {
                refeshMeta();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });
    
    $('.btnedit').live('click',function(){
        var meta_id = $(this).attr('rel');
        var parameters = {};
        blockElement(".metalisting");;
        parameters['type'] = 18;
        parameters['meta_id'] = meta_id;
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data){
            unblockElement(".metalisting");
            if (data.status == 'OK') {
            
                $("#upload_file").makeAsyncUploader({
                    upload_url: base_url +'admin/filemanager/ajaxwork',
                    flash_url: base_url + 'flash/admin/swfupload.swf',
                    button_image_url: base_url + 'images/admin/selectsave.png',
                    button_text: '',
                    file_size_limit : '2 MB',
                    debug: false
                        
                });
            
                $('#heading').val(data.name);
                $('#meta_id').val(data.meta_id);
                $('#icon_image').val(data.icon_path);
                
                $.fancybox({
                    'href' : '#formaddwrap'
                });
                $('#wysiwyg4').val(data.value);
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    $('#deletemeta').live('click',function(){
        if ($(".metatodelete:checked").length == 0) {
            alert('Please select at least one section you want to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected sections(s)?")) {
            var selectedvalues = "";
            $(".metatodelete:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            var parameters = {};
            parameters['type'] = 17;
            parameters['todelete'] = selectedvalues;
            blockElement(".metalisting");;
            $.post(base_url + 'admin/projectmanager/ajaxwork', parameters , function(data)
            {                                             
                unblockElement('.metalisting'); 
                if(data == 'OK') {
                    alert('Selected section(s) was removed successfully.');
                	refeshMeta();
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            });
        }
    });
    
    $("ul.skin2").tabs("div.skin2 > div");
    
    // If the user has just tabbed out of the project_name field,
    // see if the project_code field is blank. If it is, create a project code
    // for the user automagically.
    $("#project_name").bind('blur',function()
    {
        // Make sure the current code is blank
        var current_code = $("#project_code").val();

        if(current_code != "")
            return;
            
        // Get the page title
        var code = $(this).val();
        if(code == "")
            return;    

        // Replace spaces, underscores, punctuation etc.      
		code = code.toLowerCase();	// Covert code to upper case
   		code = code.replace(/[ ,_]/g, "-");	// Replace spaces with dashes
		code = code.replace(/[^a-z0-9-]/g, "");	// Replace other punctionation with nothing
		code = code.replace(/--/g, "-");	// Replace double dashses with a single dash
		code = code.replace(/-$/g, "");	// If there's a dash at the end, kill it.        
        
        // Set the code.
        $("#project_code").val(code);
    });    
    
    $("#frmProject #button").live("click",function(e){
       
        if($('#frmProject').valid())
        {
        	// Strip any commas from numeric fields
        	$("#prices_from").val($("#prices_from").val().replace(/[,]/g, ""));
        	
            $(this).submit();
		}
        else
            alert("Please fill in all required fields");    
    });
    
    var lgUploader = new qq.FileUploader(
    {
        // pass the dom node (ex. $(selector)[0] for jQuery users)
        element: document.getElementById('logo_upload'),
        // path to server-side upload script
        action: base_url + 'admin/projectmanager/upload_file/hero_image1',
        params: {
          "project_id" : $("#project_id").val(),
          "doc_id" : false,
          "doc_name" : false,
        },
        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        sizeLimit: 21000000, // max size 
        onComplete: function(id, fileName, responseJSON)
        {
        	if(responseJSON.success) {
                $('#logo_upload').hide();
                $('#logo_img_upload').show();
                $('.logo_img').html('<img id="logo_img_upload" src="'+responseJSON.fileName+'" width="250"/>');
                $('#delete_logo').show();
        	}
        }
    });
    
    var lgprUploader = new qq.FileUploader(
    {
        // pass the dom node (ex. $(selector)[0] for jQuery users)
        element: document.getElementById('logo_print_upload'),
        // path to server-side upload script
        action: base_url + 'admin/projectmanager/upload_file/hero_image2',
        params: {
          "project_id" : $("#project_id").val(),
          "doc_id" : false,
          "doc_name" : false,
        },
        allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        sizeLimit: 21000000, // max size 
        onComplete: function(id, fileName, responseJSON)
        {
        	if(responseJSON.success) {
                $('#logo_print_upload').hide();
                $('#logo_img2_upload').show();
                $('.logo_img2').html('<img id="logo_img2_upload" src="'+responseJSON.fileName+'" width="250"/>');
                $('#delete_logo2').show();
        	}
        }
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
        			action: base_url + 'admin/projectmanager/upload_file/documents',
        		    params: {
        		      "project_id" : $("#project_id").val(),
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
	
	$("#tabGallery").click(function()
	{
		// Setup hero image uploader
		var gUploader = new qq.FileUploader(
		{
			// pass the dom node (ex. $(selector)[0] for jQuery users)
			element: document.getElementById('upload_file'),
			// path to server-side upload script
			action: base_url + 'admin/projectmanager/upload_file/gallery_image',
		    params: {
                "project_id" : $("#project_id").val(),
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
					refreshImages();
    			}
		    }
		});			
	});
     
     $(".download_project").live("click",function(e){
              
        var href = $(this).attr("href");
        
        var current_swfu = "upload_image";
        
        
        var parameters = {};
        parameters['type'] = 8;
        parameters['file'] = href;
        parameters['project_id'] = $('#project_id').val();
        parameters['current_swfu'] = current_swfu;
        
        $.download(base_url + 'admin/projectmanager/ajaxwork',parameters);     
       
        e.preventDefault();
        
    });
    
    
    $("#delete_logo").live("click",function(e){
        
        
        if (confirm('Are you sure you want to delete the logo ?'))
        {   
            var parameters = {};
            parameters['project_id'] = $('#project_id').val();
            parameters['type'] = 5;
            
            $.post(base_url + 'admin/projectmanager/ajaxwork',parameters, function(){
                                           
                $('#logo_img_upload').hide();
                $('#logo_upload').show();
                $('#delete_logo').hide();
                $('.qq-upload-list').hide();
               
            });
        }
        
    });
    
    $("#delete_logo2").live("click",function(e)
    	    {    
    	        if (confirm('Are you sure you want to delete the logo for this project?'))
    	        {   
    	            var parameters = {};
    	            parameters['project_id'] = $('#project_id').val();
    	            parameters['type'] = 14;
    	            
    	            $.post(base_url + 'admin/projectmanager/ajaxwork',parameters, function(){
    	                                           
                        $('#logo_img2_upload').hide();
                        $('#logo_print_upload').show();
                        $('#delete_logo2').hide();
                        $('.qq-upload-list').hide();
    	                
    	            });
    	        }
    	        
    });
    
    $('#save_file_changes').live('click',function(){
        var params = {};
        params['type'] = 19;
        params['desc'] = [];
        params['ids'] = [];
        $(':input[name="document_description[]"]').each(function(){
            var id = $(this).attr('fid');
            var val = $(this).val();
            params['desc'].push(val);
            params['ids'].push(id);
        });
        
        blockElement("#files_listing");
        $.post(base_url + 'admin/projectmanager/ajaxwork',params,function(rs){
            alert('Gallery changes saved.')
            unblockElement("#files_listing");
            refreshImages();
        })
    });
    
    $("#delete_files").live('click',function(){
        
        var p_swfu_id = "upload_image";
        
        if ($(":checkbox[@name='project_imagestodelete[]']:checked").length == 0)                        
        {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?"))
        {
            var project_id = $('#project_id').val();         
            if (!project_id) return;
            
            var selectedvalues = "";
            $(":checkbox[@name='project_imagestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {}; 
            
            parameters['type'] = 9;
            parameters['todelete'] = selectedvalues;
            parameters['project_id'] = project_id;
            parameters['current_swfu_id'] = p_swfu_id;
            
            blockElement("#files_listing");
            
            $.post(base_url + 'admin/projectmanager/ajaxwork', parameters,function(data){
            
                unblockElement("#files_listing");
                refreshImages(p_swfu_id);
                $('.qq-upload-list').hide();
            });
        }
        
    });
    
    $(".del_path").live('click',function(){
      
       var str = $(this).attr("id");
       var doc_id = str.replace("delete_doc_", "");
       $('.qq-upload-list').hide();
       delete_document_path(doc_id);
       
       $("#docpath_" + doc_id).html("");
       $(this).hide();
       
    });   

    $('.moveup, .movedown').live('click', function(e){
        e.preventDefault();
        
        var parameters = {};
        parameters['type'] = 31;
        parameters['project_id'] = $("#project_id").val();
        parameters['brochure_id'] = $(this).parent().parent().attr('brochureid');
        parameters['page'] = $(this).parent().attr('page');
        parameters['change'] = ($(this).attr('class') == 'moveup') ? -1 : 1;
        
        blockElement("#page_list");;
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters , function(data)
        {                                             
            unblockElement('#page_list'); 
            if(data.status == 'OK') {
                refreshBrochureList();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        }, 'json');
    });
     
    $('#asset_category').live('change',function(){
        
        var parameters = {};
        parameters['type'] = 27;
        parameters['asset_category_id'] = $(this).val();
        blockElement('#frmAddBrochure');
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data){
            unblockElement('#frmAddBrochure');
            if (data.status == 'OK') {
                $('#asset_item').html(data.html);    
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    // $('#asset_category').trigger('change');
    
    $('#addNewAsset').live('click', function(){
        var parameters = {};
        parameters['type'] = 28;
        parameters['type_brochure'] = 'Asset';
        parameters['asset_category_id'] = $('#asset_category').val();
        parameters['asset_id'] = $('#asset_item').val();
        parameters['project_id'] = $('#project_id').val();
        blockElement('#frmAddBrochure');
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data){
            unblockElement('#frmAddBrochure');
            if (data.status == 'OK') {
                //refresh page list
                refreshBrochureList();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    $('#addNewAssetCategory').live('click', function(){
        var parameters = {};
        parameters['type'] = 28;
        parameters['type_brochure'] = 'Category';
        parameters['asset_category_id'] = $('#asset_category').val();
        // parameters['asset_id'] = '';
        parameters['project_id'] = $('#project_id').val();
        blockElement('#frmAddBrochure');
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data){
            unblockElement('#frmAddBrochure');
            if (data.status == 'OK') {
                //refresh page list
                refreshBrochureList();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    
    $('#addNewPage').live('click', function(){
        var parameters = {};
        parameters['type'] = 28;
        parameters['type_brochure'] = $('#page_type').val();
        parameters['project_id'] = $('#project_id').val();
        blockElement('#frmAddBrochure');
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data){
            unblockElement('#frmAddBrochure');
            if (data.status == 'OK') {
                //refresh page list
                refreshBrochureList();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    $('#tabBrochure').click(function(e){
        e.preventDefault();
        refreshBrochureList();
        
        
        // Setup hero image uploader
		var broUploader = new qq.FileUploader(
		{
			// pass the dom node (ex. $(selector)[0] for jQuery users)
			element: document.getElementById('uploadNewImage'),
			// path to server-side upload script
			action: base_url + 'admin/projectmanager/upload_file/brochure_image',
		    params: {
                "project_id" : $("#project_id").val(),
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
					refreshBrochureList();
                    addheight += 22;
                }
		    }
		});	
    });
    
    
    $('#deletebrochure').live('click',function(){
        if ($(".deletebrochure:checked").length == 0) {
            alert('Please select at least one section you want to delete.');
            return;
        }
        if (confirm("Delete the selected brochure pages? Are you  sure?")) {
            var selectedvalues = "";
            $(".deletebrochure:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            var parameters = {};
            parameters['type'] = 30;
            parameters['selectedvalues'] = selectedvalues;
            parameters['project_id'] = $("#project_id").val();
            
            
            blockElement("#page_list");;
            $.post(base_url + 'admin/projectmanager/ajaxwork', parameters , function(data)
            {                                             
                unblockElement('#page_list'); 
                if(data.status == 'OK') {
                    refreshBrochureList();
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            }, 'json');
        }
    });
    
    $('.brochure_header').live('blur', function(){
        var parameters = {};
        parameters['type'] = 32;
        parameters['heading'] = $(this).val();
        parameters['brochure_id'] = $(this).parent().parent().attr('brochureid');
        blockElement('#frmAddBrochure');
        $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data){
            unblockElement('#frmAddBrochure');
            if (data.status == 'OK') {
                //refresh page list
                refreshBrochureList();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
});

var addheight = 0;
function refreshBrochureList()
{
    var parameters = {};
    parameters['type'] = 29;
    parameters['project_id'] = $('#project_id').val(); 
    
    blockElement('#page_list');
    
    $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data){
        unblockElement('#page_list');
        if (data.status == 'OK') {
            //refresh page list
            $('.page_list tbody').html(data.html);
            var height_tab = $('table.page_list').height() + 180 + addheight;
            $('#brochure_content').height( height_tab + 'px');
        } else {
            alert("Sorry an error occured and your request could not be processed");
        }
    },'json');
}

function refreshImages(current_id)
{
    var parameters = {};
    parameters['type'] = 7;
    parameters['project_id'] = $('#project_id').val();
    parameters['current_swfu_id'] = current_id;          
    
    var file_listing_el = '#files_listing';
    var page_listing_el = '#page_listing';
    
    blockElement(file_listing_el);
    
    $(page_listing_el).load(base_url + 'admin/projectmanager/ajaxwork', parameters,function(){
    
       unblockElement(file_listing_el);
    });
}

function refresh_document_path(doc_id)
{
    var parameters = {};
    parameters['type'] = 11;
    parameters['doc_id'] = doc_id;
    
    blockElement('.document_' + doc_id);
    
    $.post(base_url + 'admin/projectmanager/ajaxwork', parameters,function(data){
       
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
    parameters['type'] = 12;
    parameters['doc_id'] = doc_id;
    parameters['project_id'] = $('#project_id').val();;
    
    blockElement('.document_' + doc_id);
    
    $.post(base_url + 'admin/projectmanager/ajaxwork', parameters,function(data){
        
        unblockElement('.document_' + data.doc_id);    
        $('#doc_upload_file_'+ data.doc_id).show();
              
    },"json");
}