var page_name = 'statemanager';  
var refeshMeta = function(){
    var state_id =  $("#state_id").val()
    var parameters = {};
    parameters['type'] = 10;
    parameters['state_id'] = state_id;
    $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
        if (data) {
            $('.metalisting').html(data);    
        } else {
            alert("Sorry an error occured and your request could not be processed");
        }
        
    });
};

var refeshLinks = function(){
    var state_id =  $("#state_id").val()
    var parameters = {};
    parameters['type'] = 15;
    parameters['state_id'] = state_id;
    $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
        if (data) {
            $('.link_listing').html(data);    
        } else {
            alert("Sorry an error occured and your request could not be processed");
        }
        
    });
};

var refeshComments = function(){
    var state_id =  $("#state_id").val()
    var parameters = {};
    parameters['type'] = 19;
    parameters['state_id'] = state_id;
    $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
        if (data) {
            $('.commentlisting').html(data);    
        } else {
            alert("Sorry an error occured and your request could not be processed");
        }
        
    });
};

$(function(){
    
	$('#submitbutton,#submitbutton2').click(function(){
        $('#frmState').submit();
    });
	
    $("ul.skin2").tabs("div.skin2 > div");
    
    $("#frmState #button").live("click",function(e){
        
        if($('#frmState').valid()) {
            $(this).submit();
		}
        else {
            alert("Please fill in all required fields");
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
        			action: base_url + 'admin/statemanager/upload_file/documents',
        		    params: {
        		      "state_id" : $("#state_id").val(),
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
			action: base_url + 'admin/statemanager/upload_file/gallery_image',
		    params: {
                "state_id" : $("#state_id").val(),
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
					refreshGalleryFiles();
    			}
		    }
		});			
	});
	
	var hrUploader = new qq.FileUploader(
	{
		// pass the dom node (ex. $(selector)[0] for jQuery users)
			element: document.getElementById('hero_image_upload'),
			// path to server-side upload script
			action: base_url + 'admin/statemanager/upload_file/hero_image',
		    params: {
		      "state_id" : $("#state_id").val(),
		      "doc_id" : false,
		      "doc_name" : false,
		    },
		    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
		    sizeLimit: 21000000, // max size 
		    onComplete: function(id, fileName, responseJSON)
		    {
    			if(responseJSON.success) {
                    $('#hero_image_upload').hide();
                    $('#state_hero_img').show();
                    $('.hero_img').html('<img id="state_hero_img" src="'+responseJSON.fileName+'" width="250"/>');
                    $('#delete_hero_image').show();
    			}
		    }
     });
     
//     $(".download_area").live("click",function(e){
//              
//        var href = $(this).attr("href");
//        var fid = this.rel;
//        
//        var current_swfu = ($('.css-tabs a[class="current"]').html() == "Gallery 1") ? "upload_image" : "upload_image2";
//        
//        var parameters = {};
//        parameters['type'] = 8;
//        parameters['file'] = href;
//        parameters['fid'] = fid;
//        parameters['area_id'] = $('#area_id').val();
//        parameters['current_swfu'] = current_swfu;
//        
//        $.download(base_url + 'admin/areamanager/ajaxwork',parameters);     
//       
//        e.preventDefault();
//        
//    });
    
    $("#delete_hero_image").live("click",function(e){
        if (confirm('Are you sure you want to delete the image ?')) {
            var parameters = {};
            parameters['state_id'] = $('#state_id').val();
            parameters['type'] = 5;
            
            $.post(base_url + 'admin/statemanager/ajaxwork',parameters, function(){
               $('#state_hero_img').remove();
               $('#hero_image_upload').show();
               $('#delete_hero_image').hide();
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
    
    $('#openformadd').live('click',function(){
        $('#heading').val('');
        $('#wysiwyg4').val('');
        $('#meta_id').val('');
        $.fancybox({
            'href' : '#formaddwrap',
        });
    });
    
    $('#addnewlink').live('click',function(){
        $('#link_title').val('');
        $('#url').val('');
        $('#meta_id').val('');
        $.fancybox({
            'href' : '#formaddlink',
        });
    });
    
    $('#newcomment').live('click',function(){
        $('#comment').val('');
        $.fancybox({
            'href' : '#formnewcomment',
        });
    });
    
    $('.savemeta').live('click',function(){
        var parameters = {};
        var title = $('#heading').val();
        var content = $('#wysiwyg4').val();
        var meta_id = $('#meta_id').val();
        var state_id = $('#state_id').val();
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
        parameters['state_id'] = state_id;
        parameters['type'] = 9;
        
        $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
            unblockElement('.metalisting');
            if (data == 'OK') {
                refeshMeta();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });
    
    $('.savelink').live('click',function(){
        var parameters = {};
        var title = $('#link_title').val();
        var url = $('#url').val();
        var link_id = $('#link_id').val();
        var state_id = $('#state_id').val();
        if (title.length == 0) {
            alert('Link title is required');
            return false;
        } else if (url.length == 0) {
            alert('Url is required');
            return false;
        }
        blockElement('.link_listing');
        parameters['title'] = title;
        parameters['url'] = url;
        parameters['link_id'] = link_id;
        parameters['state_id'] = state_id;
        parameters['type'] = 14;
        
        $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
            unblockElement('.link_listing');
            if (data == 'OK') {
                refeshLinks();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });
    
    $('.savecomment').live('click',function(){
        var parameters = {};
        var comment = $('#comment').val();
        var state_id = $('#state_id').val();
        var comment_id = $('#comment_id').val();
        if (comment.length == 0) {
            alert('Comment is required');
            return false;
        }
        blockElement('.commentlisting');
        parameters['comment'] = comment;
        parameters['state_id'] = state_id;
        parameters['comment_id'] = comment_id;
        parameters['type'] = 18;
        
        $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
            unblockElement('.commentlisting');
            if (data == 'OK') {
                refeshComments();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
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
            parameters['type'] = 11;
            parameters['todelete'] = selectedvalues;
            blockElement(".metalisting");;
            $.post(base_url + 'admin/statemanager/ajaxwork', parameters , function(data)
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
    
    $('#deletelink').live('click',function(){
        if ($(".linktodelete:checked").length == 0) {
            alert('Please select at least one section you want to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected link(s)?")) {
            var selectedvalues = "";
            $(".linktodelete:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            var parameters = {};
            parameters['type'] = 16;
            parameters['todelete'] = selectedvalues;
            blockElement(".link_listing");;
            $.post(base_url + 'admin/statemanager/ajaxwork', parameters , function(data)
            {                                             
                unblockElement('.link_listing'); 
                if(data == 'OK') {
                    alert('Selected web link(s) was removed successfully.');
                	refeshLinks();
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            });
        }
    });
    
    $('#deletecomment').live('click',function(){
        if ($(".commenttodelete:checked").length == 0) {
            alert('Please select at least one comment you want to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected comment(s)?")) {
            var selectedvalues = "";
            var aItems = [];
            $(".commenttodelete:checked").each(function(){
                var itemID = $(this).val();
                selectedvalues += itemID +';';
                aItems.push(itemID);
            });
            var parameters = {};
            parameters['type'] = 13;
            parameters['todelete'] = selectedvalues;
            $.post(base_url + 'admin/statemanager/ajaxwork', parameters , function(data)
            {
                if(data == 'OK') {
                    refeshComments();
                    alert('Selected comment(s) was removed successfully.');
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            });
        }
    });
    
    $('.btnedit').live('click',function(){
        var meta_id = $(this).attr('rel');
        var parameters = {};
        blockElement(".metalisting");;
        parameters['type'] = 12;
        parameters['meta_id'] = meta_id;
        $.post(base_url + 'admin/statemanager/ajaxwork', parameters, function(data){
            unblockElement(".metalisting");
            if (data.status == 'OK') {
                $('#heading').val(data.name);
                $('#wysiwyg4').val(data.value);
                $('#meta_id').val(data.meta_id);
                $.fancybox({
                    'href' : '#formaddwrap',
                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    $('.editlink').live('click',function(){
        var link_id = $(this).attr('rel');
        var parameters = {};
        blockElement(".link_listing");;
        parameters['type'] = 17;
        parameters['link_id'] = link_id;
        $.post(base_url + 'admin/statemanager/ajaxwork', parameters, function(data){
            unblockElement(".link_listing");
            if (data.status == 'OK') {
                $('#link_title').val(data.title);
                $('#url').val(data.url);
                $('#link_id').val(data.link_id);
                $.fancybox({
                    'href' : '#formaddlink',
                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    $(".download_area").live("click",function(e){
        e.preventDefault();
        
        var href = $(this).attr("href");
        var fid = this.rel;
        var paremeters = {};
        paremeters['type'] = 21;
        paremeters['fid'] = fid;
        
        $.download(base_url + 'admin/statemanager/ajaxwork',paremeters);
        
        
    });
    
    $('#save_file_changes').live('click',function(){
        var params = {};
        params['type'] = 23;
        params['desc'] = [];
        params['ids'] = [];
        $(':input[name="document_description[]"]').each(function(){
            var id = $(this).attr('fid');
            var val = $(this).val();
            params['desc'].push(val);
            params['ids'].push(id);
        });
        
        blockElement("#files_listing");
        $.post(base_url + 'admin/statemanager/ajaxwork',params,function(rs){
            alert('Gallery changes saved.')
            unblockElement("#files_listing");
            refreshGalleryFiles();
        })
    });
    
    $("#delete_files").live('click',function(){
        
        if ($(":checkbox[name='state_imagestodelete[]']:checked").length == 0)                        
        {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?"))
        {
            var selectedvalues = "";
            $(":checkbox[name='state_imagestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 22;
            parameters['todelete'] = selectedvalues;
            parameters['state_id'] = $("#state_id").val();
            
            blockElement("#files_listing");
            
            $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
            
                unblockElement("#files_listing");
                $('.qq-upload-list').hide();
                refreshGalleryFiles();
            });
        }
        
    });
    
    $('#region_id').change(function(){
        $('#state_name').val(states[$(this).val()]);
    });
    
});

function delete_document_path(doc_id)
{
    var parameters = {};
    parameters['type'] = 8;
    parameters['doc_id'] = doc_id;
    parameters['state_id'] = $('#state_id').val();
    
    blockElement('.document_' + doc_id);
    
    $.post(base_url + 'admin/statemanager/ajaxwork', parameters,function(data){
        
        unblockElement('.document_' + data.doc_id);    
        $('#doc_upload_file_'+ data.doc_id).show();
    },"json");
}

function refreshGalleryFiles(selected_folder)
{
    var parameters = {};
    parameters['type'] = 20;
    parameters['state_id'] = $("#state_id").val();
    
    blockElement('#files_listing');
    $("#page_listing").load(base_url + 'admin/statemanager/ajaxwork', parameters,function(){
       unblockElement('#files_listing');
    });
}