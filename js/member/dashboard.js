var sort_col = "p.due_date";
var sort_dir = "ASC";
var current_page=1;

var objAnnouncements = null;

var doLoadTasks = function(current_page,id)
{
	objApp.unblockElement('.'+id+'_listing');
	
	$('.'+id+'_listing').attr('sort_col', sort_col);
	$('.'+id+'_listing').attr('sort_dir', sort_dir);
	
	$.post(base_url+'dashboard/ajax', {
		action: 'load_'+id+'',
		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
		user_id: $('#user_id').val(),
		sort_col: sort_col,
		sort_dir: sort_dir
	}, function(data) {
		
        objApp.unblockElement('.'+id+'_listing');
        
        if(data.status != "OK")
        {
            alert("Sorry, something went wrong whilst loading the task listing");
            return;    
        }
        
        $("."+id+"_listing").html(data.message);
        
    }, "json");
}

$(function(){

	$("table.task_listing th").live('click',function(e)
    {
		class_name=$(this).closest('table.listing').attr("class");
		class_name=class_name.replace(" listing","");
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
        
    	$('.'+class_name).attr('sort_col', sort_col);
		$('.'+class_name).attr('sort_dir', sort_dir);
        
        doLoadTasks(1,class_name.replace("_listing",""));
    });
	
    $('.email_resource').live('click',function()
    {
        $('#emailResource').show();
        self.selectedUser();
    });
    
    $('#email_resource_to').live('change',function()
    {
        var email_resource = $('#email_resource_to').val();
        $('#email_resource').val(email_resource);
    });
    
    $('#ck_external_users').live('click',function()
    {
        if($(this).is(':checked'))
        {
            $(".send_to_external_users").show();  // checked
            $(".send_to_users_network").hide();  // checked
        }
        else
        {
            $(".send_to_external_users").hide();  // checked
            $(".send_to_users_network").show();  // checked
        }
    });
    
    $('#submit_email_resource_nw').live('click',function()
    {
        var email_resource = $('#frmEmailResourceNetwork .email_resource').val();
        
        if (email_resource == '')
        {
            alert('Please select or enter an email address.');
            return;
        }
        
        var form = $("#frmEmailResourceNetwork");
        
        // Submit the form. 
        var params = $(form).serialize();
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert(data.message);
                return;    
            }
            
            alert('Resources has been sent to email "'+ email_resource +'" successfully!');
            $('#emailResource').hide();
            
        }, "json");
        
    });
    
    $('#submit_email_resource_ex').live('click',function()
    {
        var email_resource = $('#frmEmailResourceExternal .email_resource').val();
        
        if (email_resource == '')
        {
            alert('Please select or enter an email address.');
            return;
        }
        
        var form = $("#frmEmailResourceExternal");
        
        // Submit the form. 
        var params = $(form).serialize();
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert(data.message);
                return;    
            }
            
            alert('Resources has been sent to email "'+ email_resource +'" successfully!');
            $('#emailResource').hide();
            
        }, "json");
        
    });
	
    $("a.download").click(function(e)
        {
        	$('#emailResourceForm').hide();
        	
            var aclass = $(this).attr("class");
            
            if(aclass == "external")
            {
                // Do nothing
                return true;
            }
            
            e.preventDefault();
            
            // Set the article id into the download form
            var article_id = $(this).attr("href");
            $("#download_article_id").val(article_id);
            
            var form = $("#frmDownload");
            
            var params = $(form).serialize(); 
            objApp.blockElement("table.reservation_listing");
            
            $.post($(form).attr("action"), params, function(data)
            {
                objApp.unblockElement("table.reservation_listing");
                
                if(data.status != "OK")
                {
                    alert("Sorry, something went wrong whilst sending the download request");
                    return;    
                }
                
                $("#article_modal").css("height", data.height + "px");
                $("#article_modal").css("width", data.width + "px");
                
                $("#article_modal div").html(data.message);
                
                $('#article_modal').reveal(
                {
                     animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                     animationspeed: 300,                       //how fast animtions are
                     closeonbackgroundclick: true,              //if you click background will modal close?
                     dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
                }); 
                
                $('#article_modal div.comments').jScrollPane();             

            }, "json");            
        });  
        
        
    objApp.include("jquery.blockUI.js");
    objApp.include("jScrollPane.js");
    objApp.include("paginator.js");
    objApp.include("announcements.js");

    objAnnouncements = new Announcements();
    objAnnouncements.initDashboard();        
}); 
