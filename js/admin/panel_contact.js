var page_name = 'contactsmanager';  

$(document).ready(function()
{
	$('#submitbutton,#submitbutton2').click(function(){
        $('#frmBuilder').submit();
    });
	
    $("ul.skin2").tabs("div.skin2 > div");
    
    $("#frmBuilder #button").live("click",function(e){
        
        if($('#frmBuilder').valid()) {
            $(this).submit();
		}
        else {
            alert("Please fill in all required fields");
        }
    });
    
    $("#tabDocument").click(function()
	{
	    // Setup hero image uploader
	     //$(".doc_upload_file").each(function(){
	        var doc_id = $(this).attr('did');
        	var dUploader = new qq.FileUploader(
        	{
        		// pass the dom node (ex. $(selector)[0] for jQuery users)
        			element: document.getElementById('upload_document'),
        			// path to server-side upload script
        			action: base_url + 'admin/contactsmanager/upload_file/documents',
        		    params: {
        		      "contacts_id" : $("#contacts_id").val(),
        		      "doc_id" : doc_id,
        		      "doc_name" : $("#doc_"+ doc_id +"_name").val(),
        		    },    
        		    sizeLimit: 21000000, // max size 
        		    onComplete: function(id, fileName, responseJSON)
        		    {
						refreshUserFiles();
            			if(responseJSON.success) {
        					$("#docpath_" + doc_id).html(responseJSON.fileName);
                            $("#docpath_" + doc_id).show();
                            $("#delete_doc_" + doc_id).show();
                            $('#doc_upload_file_'+doc_id).hide();
            			}
        		    }
		      });
				//});
	});
    
   
     $("#delete_logo").live("click",function(e){
        if (confirm('Are you sure you want to delete the logo ?')) {
            var parameters = {};
            parameters['contacts_id'] = $('#contacts_id').val();
            parameters['type'] = 5;
            
            $.post(base_url + 'admin/contactsmanager/ajaxwork',parameters, function(){
                                           
               $('#builder_logo_img').hide();
               $('#builder_logo_upload').show();
               $('#delete_logo').hide();
               $('.qq-upload-list').html("");
            });
        }
    });
    
    $(".del_path").live('click',function(){
      
       var str = $(this).attr("id");
       var doc_id = str.replace("delete_doc_", "");
       $('.qq-upload-list').html("");
       delete_document_path(doc_id);
       
       $("#docpath_" + doc_id).html("");
       $(this).hide();
       
    });
    
    $('#addnewcontact').live('click',function(){
        $('#contact_name').val('');
        $('#contact_position').val('');
        $('#contact_address').val('');
        $('#contact_suburb').val('');
        $('#contact_postcode').val('');
        $('#contact_state_id').val('');
        $('#contact_phone').val('');
        $('#contact_mobile').val('');
        $('#contact_fax').val('');
        $('#contact_email').val('');
        $('#contact_comment').val('');
        $('#contact_id').val('');
        $.fancybox({
            'href' : '#formaddcontact',
        });
    });
    
    $('#newcomment').live('click',function(){
        $('#comment').val('');
        $.fancybox({
            'href' : '#formnewcomment',
        });
    });
    
    $('.savecomment').live('click',function(){
        var parameters = {};
        var comment = $('#comment').val();
        var contacts_id = $('#contacts_id').val();
        var comment_id = $('#comment_id').val();
        if (comment.length == 0) {
            alert('Comment is required');
            return false;
        }
        blockElement('.commentlisting');
        parameters['comment'] = comment;
        parameters['contacts_id'] = contacts_id;
        parameters['comment_id'] = comment_id;
        parameters['type'] = 15;
        
        $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
            unblockElement('.commentlisting');
            if (data == 'OK') {
                refeshComments();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });
	
	
	var descr_id;
	$('#description').live('click',function(){
        $('#newdescription').val('');
		descr_id=$(this).attr('class');
		$('#description_id').val($(this).attr('class'));
        $.fancybox({
            'href' : '#formdescription',
        });
    });
	
	$('.savedescription').live('click',function(){
        var parameters = {};
        var description = $('#newdescription').val();
        var contacts_id = $('#contacts_id').val();
        var description_id = descr_id;
        if (description.length == 0) {
            alert('Description is required');
            return false;
        }
        blockElement('.documentlisting');
        parameters['description'] = description;
        parameters['contacts_id'] = contacts_id;
        parameters['description_id'] = description_id;
        parameters['type'] = 18;
        
        $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
        unblockElement('.documentlisting');   
            if (data == 'OK') {
                refreshUserFiles();
                $.fancybox.close();
				
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });
	
    
    $('.savecontact').live('click',function()
    {
        var parameters = {};
        
        var name = $('#contact_name').val();
        var address = $('#contact_address').val();
        var position = $('#contact_position').val();
        var postcode = $('#contact_postcode').val();
        var suburb = $('#contact_suburb').val();
        var state_id = $('#contact_state_id').val();
        var phone = $('#contact_phone').val();
        var mobile = $('#contact_mobile').val();
        var fax = $('#contact_fax').val();
        var email = $('#contact_email').val();
        var comment = $('#contact_comment').val();
        var contact_id = $('#contact_id').val();
        var contacts_id = $('#contacts_id').val();
        
        if (first_name.length == 0) {
            alert('Please enter a name for this contact');
            return false;
        }
        
        blockElement('.contact_listing');
        
        parameters['contact_name'] = name;
        parameters['contact_position'] = position;
        parameters['contact_address'] = address;
        parameters['contact_suburb'] = suburb;
        parameters['contact_state_id'] = state_id;
        parameters['contact_postcode'] = postcode;
        parameters['contact_phone'] = phone;
        parameters['contact_mobile'] = mobile;
        parameters['contact_fax'] = fax;
        parameters['contact_email'] = email;
        parameters['contact_comment'] = comment;
        parameters['contact_id'] = contact_id;
        parameters['contacts_id'] = contacts_id;
        parameters['type'] = 10;

        $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
            unblockElement('.contact_listing');
            if (data == 'OK') {
                refreshContacts();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });
    
    $('.editcontact').live('click',function(){
        var contact_id = $(this).attr('rel');
        var parameters = {};
        blockElement(".contact_listing");;
        parameters['type'] = 11;
        parameters['contact_id'] = contact_id;
        $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters, function(data){
            unblockElement(".contact_listing");
            if (data.status == 'OK') {
                $('#contact_name').val(data.name);
                $('#contact_position').val(data.position);
                $('#contact_address').val(data.address);
                $('#contact_postcode').val(data.postcode);
                $('#contact_suburb').val(data.suburb);
                $('#contact_state_id').val(data.state_id);
                $('#contact_phone').val(data.phone);
                $('#contact_mobile').val(data.mobile);
                $('#contact_fax').val(data.fax);
                $('#contact_email').val(data.email);
                $('#contact_comment').val(data.comment);
                $('#contact_id').val(data.contact_id);
                $.fancybox({
                    'href' : '#formaddcontact',
                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
    
    $('#deletecontact').live('click',function(){
        if ($(".contacttodelete:checked").length == 0) {
            alert('Please select at least one contact that you wish to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected contact(s)?")) {
            var selectedvalues = "";
            $(".contacttodelete:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            var parameters = {};
            parameters['type'] = 12;
            parameters['todelete'] = selectedvalues;
            blockElement(".contact_listing");;
            $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters , function(data)
            {                                             
                unblockElement('.contact_listing'); 
                if(data == 'OK') {
                    alert('The selected contact(s) were removed successfully.');
                	refreshContacts();
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
            $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters , function(data)
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
    
});

function refresh_document_path(doc_id)
{
    var parameters = {};
    parameters['type'] = 7;
    parameters['doc_id'] = doc_id;
    
    blockElement('.document_' + doc_id);
    
    $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
       
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
    parameters['type'] = 8;
    parameters['doc_id'] = doc_id;
    parameters['contacts_id'] = $('#contacts_id').val();;
    
    blockElement('.document_' + doc_id);
    
    $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
        
        unblockElement('.document_' + data.doc_id);    
        $('#doc_upload_file_'+ data.doc_id).show();
              
    },"json");
}

function refreshContacts()
{
    var contacts_id =  $("#contacts_id").val()
    var parameters = {};
    parameters['type'] = 9;
    parameters['contacts_id'] = contacts_id;
    $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
        if (data) {
            $('.contact_listing').html(data);    
        } else {
            alert("Sorry an error occured and your request could not be processed");
        }
        
    });
}

var refeshComments = function(){
    var contacts_id =  $("#contacts_id").val()
    var parameters = {};
    parameters['type'] = 14;
    parameters['contacts_id'] = contacts_id;
    $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
        if (data) {
            $('.commentlisting').html(data);    
        } else {
            alert("Sorry an error occured and your request could not be processed");
        }
    });
};

$("#delete_builder_files").live('click',function(){
        
            if ($(".user_docstodelete:checked").length == 0) {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }
            
            if (confirm("Are you sure you want to delete the selected files?")) {
                
                var selectedvalues = "";
                $(".user_docstodelete:checked").each(function(){
                    selectedvalues += $(this).val() +';';
                });
                
                var parameters = {};
                
                parameters['type'] = 8;
                parameters['todelete'] = selectedvalues;
                parameters['contacts_id'] = $("#contacts_id").val();
                
                blockElement("#files_listing");
                
                $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
                
                    unblockElement("#files_listing");
                    refreshUserFiles();
                    $('.qq-upload-list').hide();
                });
				refreshUserFiles();
            }
            
        });
function refreshUserFiles()
	{   
	    var parameters = { };
	    parameters['type'] = 16;
	    parameters['user_id'] = $("#contacts_id").val();

	    blockElement('#files_listing');
	    $.post(base_url + 'admin/contactsmanager/ajaxwork', parameters,function(data){
            unblockElement('#files_listing');
            $('#page_listing').html(data);
            setupEditInPlace();
	    });
	}