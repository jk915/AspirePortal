var ContactList = function()
{
    var self = this;
    this.paginator = false;
    this.sort_col = "u.status";
    this.sort_dir = "ASC";    
    
    // Entry point.
    this.init = function()
    {
	
		$('#tabs>li').css('display', 'none');
        $('#tabs>li:nth-child(1)').css('display', 'block');
        
        /* tabs */
        $('#tabs > li').hide();
        $('#tabs > li:first-child').show();
        $('#tabNav li:first-child a').addClass('active');
        
        // Set the currently selected list type
        $("#list_type").val($('#tabNav a.active').attr('list_type'));
		
	
		
		
        this.paginator = new Paginator($("#frmSearch"), "div.mainCol");
        
        this.bindEvents();
        
        $("#frmSearch #current_page").val("1");
        
        $("#frmSearch").submit();
    } 
    
    this.bindEvents = function()
    {
	
	
		
        $("#frmSearch").submit(function(e)
        {
            e.preventDefault();
            
            self.doSearch("#frmSearch");    
        });
        
        $("table.listing th").click(function(e)
        {
            var sort_by = $(this).attr("sort");
            
            if(sort_by == self.sort_col)
            {
                if(self.sort_dir == "ASC")
                {
                    self.sort_dir = "DESC";    
                }
                else
                {
                    self.sort_dir = "ASC";    
                }
            }    
            else
            {
                self.sort_col = sort_by;
                self.sort_dir = "ASC";         
            }
            
            self.doSearch("#frmSearch");
        });        
    }
    
    this.doSearch = function(form)
    {
        // If the form search event is being called but not via the paginator,
        // reset the current page number
        if(!self.paginator.paging_changed)
        {
            $("#frmSearch #current_page").val("1"); 
            self.paginator.current_page = 1;  
        }  
        
        $(form).find("#sort_col").val(this.sort_col);
        $(form).find("#sort_dir").val(this.sort_dir);              
        
        var params = $(form).serialize(); 
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the partner listing");
                return;    
            }
            
            $("table.listing tbody").html(data.message);
            
            // Set the total number of records into the form and update the paginator
            $("#count_all").val(data.count_all);

            self.paginator.refresh();
            
        }, "json");
    }
}

var ContactDetail = function()
{
    var self = this;
    var load_number_per_page = 5;
    this.sort_col = "t.due_date";
    this.sort_dir = "ASC";
    
    // Entry point.
    this.init = function()
    {
//        $('input:checkbox').screwDefaultButtons({
//             checked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
//             unchecked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
//             width: 15,
//             height: 16
//         });  
         
         //$('.success h4').delay(4000).fadeOut('medium');                            
         
         $('table.zebra tr:odd').addClass('alt');
         
        // this.bindEvents();       
    }
    
}       
    
    this.bindEvents = function()
    {
	
	
        // Handle form submission event  
        $("#frmContactDetail").submit(function(e)
        {              
            e.preventDefault();
            
            // Make sure the form is valid.
            if(!$(this).validate(
            {
                errorLabelContainer: $("#frmContactDetail div.error"),
                messages: 
                {
                    contacts_name: "Contacts name"
                }
            }).form())
            {
                return false;
            }            
            
            // Form is OK, submit it.
            self.updateUser(this);
        });
        
        
        
    }    
	
	$('#tabNav a').live('click',function(e){
		
            e.preventDefault();
            
            $('#tabNav a').removeClass('active');
            $(this).addClass('active');
            var index = $(this).closest('li').index();
            $('#tabs>li').hide();
            $("#tabs>li").eq(index).show();
            
            // Set the list type
            $("#list_type").val($(this).attr("list_type"));
            
            // Resubmit the search form
            $("#frmSearch").submit();
            
            return false;
        });
        $('.delete_contact').live('click',function(){
		
		
        	var contacts_id = $(this).attr('contacts_id');
        	var ajax_url = $(this).attr('action');
        	var action = $(this).attr('action_name');
        	$.post(ajax_url, {
	    		action: action,
	    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
	    		contacts_id: contacts_id
	    	}, function(data) {
	            if(data.status != "OK")
	            {
	                // The login failed.
	                $(".delete_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
	                $(".delete_error").show();
	                return;    
	            }
	            
	            // delete the user record was successful.
	            if(contacts_id == "")
	            {
	                objApp.redirect("contacts");
	                return;
	            }
	            
	            objApp.redirect("contacts");
	            
	        }, "json");
        });
		
		 $('.close-reveal').live('click',function()
        {
        	$(".close-reveal-modal").click();
        });
        $("#frmDeleteContact").submit(function(e)
        {              
	        var params = $(this).serialize();
	        
	        var contacts_id = $(this).find("#delete_user_id").val();
	        
	        $.post($(this).attr("action"), params, function(data)
	        {
	            if(data.status != "OK")
	            {
	                // The login failed.
	                $("delete_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
	                $("delete_error").show();
	                return;    
	            }
	            
	            // delete the user record was successful.
	            if(contacts_id == "")
	            {
	                objApp.redirect("contacts");
	                return;
	            }
	            
	            objApp.redirect("contacts");
	        }, "json");
        });
        
        
        
        
        
        // $(".delete_contact").live('click',function(e)
        // {
            // var contacts_name = $(this).parent().parent().find("td:eq(1)").text();
            // var contacts_id = $(this).val();
            
            // var checkbox = this;

            // if($(this).is(":checked"))
            // {    
                // // Show the task delete form
                // self.showContactDeleteForm(contacts_id, contacts_name);
                    
                // // Automatically yemove the checked state of the checkbox
                // setTimeout(function()
                // {
                    // $(checkbox).removeAttr("checked");        
                // }, 1000);                    
                
            // }  
        // });
        
       
    
    this.checkEnabled = function()
    {
    	if($("#enabled").is(':checked'))
    	{
    		$(".login_as_this_user").show();  // checked
    	}
	    else
	    {
	    	$(".login_as_this_user").hide();  // unchecked
	    }
    }
    
    this.checkAllProperties = function(ck_all)
    {
    	if ($(ck_all).is(':checked')) {
    		self.updateViewAllProperties(1);
		    $(".stock_permissions").hide();
		} else {
			self.updateViewAllProperties(0);
			var project_id = $('#project_id').val();
			self.loadProjectsPermisson(project_id);
			self.loadPropertyPermisson();
		}
    }
    
    this.updateViewAllProperties = function(value)
    {
    	var value = value ?  parseInt(value) : 0;
    	$.post(base_url+'contacts/ajax', {
    		action: 'update_view_all_property',
    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
    		user_id: $('#user_id').val(),
    		value: value,
    	}, function(data) {
            if(data.status != "OK")
            {
                // The login failed.
                alert(data.message);
                return;
            }
        }, "json");
    }
    
    this.loadProjectsPermisson = function(project_id)
    {
    	var project_id = project_id ? project_id : '';
    	
    	objApp.blockElement('.stock_project_permissions');
	    var user_id = $('#user_id').val();
    	$.post(base_url+'contacts/ajax', {
    		action: 'load_stock_project_permissions',
    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
    		user_id: user_id,
    		project_id: project_id,
    	}, function(data) {
    		objApp.unblockElement('.stock_permissions');
            if(data.status != "OK")
            {
                // The login failed.
                alert(data.message);
                return;
            }
            else
            {
            	$('.stock_project_permissions').html(data.message);
            	$('.stock_permissions').show();
            }
        }, "json");
    }
    
    this.loadPropertyPermisson = function(property_id)
    {
    	var property_id = property_id ? property_id : '';
    	
    	objApp.blockElement('.stock_property_permissions');
	    var user_id = $('#user_id').val();
    	$.post(base_url+'contacts/ajax', {
    		action: 'load_property_permissions',
    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
    		user_id: user_id,
    		property_id: property_id
    	}, function(data) {
    		objApp.unblockElement('.stock_property_permissions');
            if(data.status != "OK")
            {
                // The login failed.
                alert(data.message);
                return;
            }
            else
            {
            	$('.stock_property_permissions').html(data.message);
            }
        }, "json");
    }
    
    this.updateUser = function(form)
    {
        // Hide the error state div.
        $("div.error").hide();
        
        var params = $(form).serialize();
        
        var user_id = $(form).find("#user_id").val();
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                // The login failed.
                $("div.error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
                $("div.error").show();
                return;    
            }
            
            // updating / adding the user record was successful.
            if(user_id == "")
            {
                objApp.redirect("contacts");
                return;
            }
            // Show / hide link login as this user
            self.checkEnabled();
            
            $(".success").show();
            $('.success').delay(4000).fadeOut('medium');
        }, "json");
    }


var uri = objApp.getURI();

if(uri == "contacts")
{
    var objContactList = new ContactList();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("paginator.js");
        
        // Setup the partner object
        objContactList.init(); 
    }    
}
else
{
    var objContactDetail = new ContactDetail();

    // Load additional JS libs needed
    window.onload  = function()
    {
        objApp.include("jquery-customCB.js");
        
        // Setup the partner object
        objContactDetail.init(); 
    }   
}

$(function(){
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
        			action: base_url + 'contacts/upload_file/documents',
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
	
});


$(function(){
$("#tabContact").ready(function()
	{
		 // Setup hero image uploader
	     //$(".doc_upload_file").each(function(){
	        var doc_id = $(this).attr('did');
        	var dUploader = new qq.FileUploader(
        	{
        		// pass the dom node (ex. $(selector)[0] for jQuery users)
        			element: document.getElementById('upload_hero_image'),
        			// path to server-side upload script
        			action: base_url + 'contacts/upload_file/hero_image',
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
							$('#delete_logo').show();
            			}
        		    }
		      });
				//});
	});
	
});

$("#delete_logo").live("click",function(e){
            if (confirm('Are you sure you want to delete the logo ?')) {
                var parameters = {};
                parameters['user_id'] = $('#id').val();
                parameters['contacts_id'] = $('#contacts_id').val();
				parameters['type'] = 5;
                
                $.get(base_url + 'contacts/ajaxwork',parameters, function(){
                                               
                    $('#logo_img_upload').hide();
                    $('#logo_upload').show();
                    $('#delete_logo').hide();
                    $('.qq-upload-list').hide();
                   
                });
            }
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
          $.get(base_url + 'contacts/ajaxwork', parameters,function(data){
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
        
        $.get(base_url + 'contacts/ajaxwork', parameters,function(data){
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
        
        if (contact_name.length == 0) {
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

        $.get(base_url + 'contacts/ajaxwork', parameters,function(data){
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
        $.get(base_url + 'contacts/ajaxwork', parameters, function(data){
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
            $.get(base_url + 'contacts/ajaxwork', parameters , function(data)
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
            $.get(base_url + 'contacts/ajaxwork', parameters , function(data)
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
    $.get(base_url + 'contacts/ajaxwork', parameters,function(data){
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
    $.get(base_url + 'contacts/ajaxwork', parameters,function(data){
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
	    parameters['contacts_id'] = $("#contacts_id").val();

	    blockElement('#files_listing');
	    $.get(base_url + 'contacts/ajaxwork', parameters,function(data){
            unblockElement('#files_listing');
            $('#page_listing').html(data);
            setupEditInPlace();
	    });
	}

////////////////////////////////////



function blockObject(obj)
{
     obj.block
     ({
        message: '<div><img src="'+base_url+'images/admin/ajax-loader-big.gif"/></div>',  
        css: {border:'0px','background-color':'transparent',position:'absolute'},
        overlayCSS: {opacity:0.04,cursor:'pointer'}
      });
 }

function unblockObject(obj)
{
    obj.unblock();
}


function blockElement(elementSelector)
{
	$(elementSelector).block(
	{
		message: '<div><img src="'+base_url+'images/admin/ajax-loader-big.gif"/></div>',  
		css: {border:'0px','background-color':'transparent',position:'absolute'},
		overlayCSS: {opacity:0.04,cursor:'pointer',position:'absolute'}
	});
 }

function unblockElement(elementSelector)
{
    $(elementSelector).unblock();
}

function handleJSON_response(data)
{

    if (data.js)
    {
        alert(data.js);
        eval(data.js);
    }

}

/***
* Checks to see if the pass parameter is a js function or not. 
* 
* @param functionToCheck
*/
function isFunction(functionToCheck) 
{
    var getType = {};
    return functionToCheck && getType.toString.call(functionToCheck) === '[object Function]';
}