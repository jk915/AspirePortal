var LeadList = function()
{
    var self = this;
    this.paginator = false;
    this.sort_col = "u.status";
    this.sort_dir = "ASC";    
    
    // Entry point.
    this.init = function()
    {
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

var LeadDetail = function()
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
         
         this.bindEvents();       
    }
    
    this.doLoadTasks = function(current_page)
    {
    	current_page ? current_page : 1;
    	objApp.unblockElement('.task_listing');
    	
    	var sort_col = $('.sorttask').attr('sort_col');
    	var sort_dir = $('.sorttask').attr('sort_dir');
    	
    	$.post(base_url+'leads/ajax', {
    		action: 'load_tasks',
    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
    		user_id: $('#user_id').val(),
    		current_page: current_page,
    		sort_col: sort_col,
    		sort_dir: sort_dir
    	}, function(data) {
    		
            objApp.unblockElement('.task_listing');
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the task listing");
                return;    
            }
            
            $(".task_listing tbody").html(data.message);
            
            var limit = parseInt(current_page) * parseInt(load_number_per_page);
            
            if (limit >= data.count_all)
            	$('#load_5_task').hide();
            else
            	$('#load_5_task').show();
            
        }, "json");
    }
    
    this.doLoadNotes = function(current_page)
    {
    	objApp.unblockElement('.note_listing');
    	
    	$.post(base_url+'leads/ajax', {
    		action: 'load_notes',
    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
    		user_id: $('#user_id').val(),
    		current_page: current_page
    	}, function(data) {
    		
            objApp.unblockElement('.note_listing');
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the note listing");
                return;    
            }
            
            $(".note_listing tbody").html(data.message);
            
            var limit = parseInt(current_page) * parseInt(load_number_per_page);
            
            if (limit >= data.count_all)
            	$('#load_5_note').hide();
            else
            	$('#load_5_note').show();
            
        }, "json");
    }
    
    this.clearFormNote = function()
    {
        // Clear form elements
        $("#note_id,#content").val("");
        $("#note_date").val($("#current_date").val());
        
        $("input[type='checkbox']").removeAttr("checked");
    }
    
    this.saveNote = function(form)
    {
        var params = $(form).serialize();
        
        var task_id = $(form).find("#note_id").val();
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert(data.message);
                return;    
            }
            
            // The task was added successfully. 
            // Close the reveal window and reload the task list
            $(".close-reveal-modal").click();
            
            var current_page = parseInt($('#load_5_note').attr('cp'));
            
            self.doLoadNotes(current_page);
            
        }, "json");       
    }
    
    this.clearForm = function()
    {
        // Clear form elements
        $("#task_id,#title,#description").val("");
        $("#due_date").val($("#current_date").val());
        
        //Set default assign_to id
        $("#assign_to").val($("#logged_user_id").val());
        
        // Also clear any checked radio buttons
        $("input[type='radio']").removeAttr("checked");
        $("input[type='checkbox']").removeAttr("checked");
    }
    
    this.saveTask = function(form)
    {
        var params = $(form).serialize();
        
        var task_id = $(form).find("#task_id").val();
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert(data.message);
                return;    
            }
            
            // The task was added successfully. 
            // Close the reveal window and reload the task list
            $(".close-reveal-modal").click();
            
            var current_page = parseInt($('#load_5_task').attr('cp'));
            
            self.doLoadTasks(current_page);
            
        }, "json");       
    }
    
    this.bindEvents = function()
    {
        // Handle form submission event  
        $("#frmLeadDetail").submit(function(e)
        {              
            e.preventDefault();
            
            // Make sure the form is valid.
            if(!$(this).validate(
            {
                errorLabelContainer: $("#frmLeadDetail div.error"),
                messages: 
                {
                    first_name: "First name",
                    last_name: "Last name",
                    company_name: "Company/business name",
                    email: "Email address",
                    billing_state_id: "State"
                }
            }).form())
            {
                return false;
            }            
            
            // Form is OK, submit it.
            self.updateUser(this);
        });
        
        $('.delete_lead').live('click',function(){
        	var user_id = $(this).attr('uid');
        	var ajax_url = $(this).attr('action');
        	var action = $(this).attr('action_name');
        	$.post(ajax_url, {
	    		action: action,
	    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
	    		user_id: user_id
	    	}, function(data) {
	            if(data.status != "OK")
	            {
	                // The login failed.
	                $(".delete_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
	                $(".delete_error").show();
	                return;    
	            }
	            
	            // delete the user record was successful.
	            if(user_id == "")
	            {
	                objApp.redirect("leads");
	                return;
	            }
	            
	            objApp.redirect("leads");
	            
	        }, "json");
        });
        
        $('.update_to_investor').live('click',function(){
        	var user_id = $(this).attr('uid');
        	var ajax_url = $(this).attr('action');
        	var action = $(this).attr('action_name');
        	var email = $('#email').val();
        	$.post(ajax_url, {
	    		action: action,
	    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
	    		user_id: user_id,
	    		email: email
	    	}, function(data) {
	            if(data.status != "OK")
	            {
	                // The login failed.
	                $(".update_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
	                $(".update_error").show();
	                return;    
	            }
	            
	            // delete the user record was successful.
	            if(user_id == "")
	            {
	                objApp.redirect("leads");
	                return;
	            }

	            alert('Convert Lead to Investor successfully.');
	            objApp.redirect("leads");
	            
	        }, "json");
        });
        
        $("#frmDeleteLead").submit(function(e)
        {              
	        var params = $(this).serialize();
	        
	        var user_id = $(this).find("#delete_user_id").val();
	        
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
	            if(user_id == "")
	            {
	                objApp.redirect("leads");
	                return;
	            }
	            
	            objApp.redirect("leads");
	        }, "json");
        });
        
        $("#btnAddTask").click(function(e)
        {
            e.preventDefault();
            
            self.showTaskForm();    
        });
        
        $("a.showtask").live('click',function(e)
        {
            e.preventDefault();
            
            var task_id = $(this).attr("href");
            
            self.showTask(task_id);    
        });
        
        $(".delete_task").live('click',function(e)
        {
            var task_name = $(this).parent().parent().find("td:eq(1)").text();
            var task_id = $(this).val();
            
            var checkbox = this;

            if($(this).is(":checked"))
            {    
                // Show the task delete form
                self.showTaskDeleteForm(task_id, task_name);
                    
                // Automatically yemove the checked state of the checkbox
                setTimeout(function()
                {
                    $(checkbox).removeAttr("checked");        
                }, 1000);                    
                
            }  
        });
        
        $("#btnSaveTask").click(function(e)
        {
            e.preventDefault();
            // Make sure a priority is selected
            var priority = $("input[name='priority']:checked").val();
            if((priority == undefined) || (priority == null))
            {
            	
                alert("Please select a priority for this task");
                return;    
            }                    
            if(!$("#frmDetails").validate().form())
            {
                alert("Please enter all required fields.");
                return;    
            }

            self.saveTask($("#frmDetails"));
        });
        
        $('#load_5_task').live('click',function(e){
        	e.preventDefault();
        	var current_page = parseInt($(this).attr('cp')) + 1;
        	$(this).attr('cp', current_page);
        	self.doLoadTasks(current_page);
        });
        
        $("#btnAddNote").click(function(e)
        {
            e.preventDefault();
            
            self.showNoteForm();
                       
        });
        
        $("a.shownote").live('click',function(e)
        {
            e.preventDefault();
            
            var note_id = $(this).attr("href");
            self.showNote(note_id);    
        });
        
        $(".delete_note").live('click',function(e)
        {
            var note_id = $(this).val();
            
            var checkbox = this;

            if($(this).is(":checked"))
            {    
                // Show the task delete form
                self.showNoteDeleteForm(note_id);
                
                // Automatically yemove the checked state of the checkbox
                setTimeout(function()
                {
                    $(checkbox).removeAttr("checked");        
                }, 1000);
            }  
        });
        
        $("#btnSaveNote").live('click',function(e)
        {
            e.preventDefault();
            // Make sure a priority is selected
            self.saveNote($("#frmNoteDetails"));
        });
        
        $('#load_5_note').live('click',function(e){
        	e.preventDefault();
        	var current_page = parseInt($(this).attr('cp')) + 1;
        	$(this).attr('cp', current_page);
        	self.doLoadNotes(current_page);
        });
        
        $("#due_date").datepicker({ dateFormat: 'dd/mm/yy' });
        $("#note_date").datepicker({ dateFormat: 'dd/mm/yy' });
        
        $("table.task_listing th").live('click',function(e)
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
            
        	$('.sorttask').attr('sort_col', self.sort_col);
    		$('.sorttask').attr('sort_dir', self.sort_dir);
            
            var current_page = parseInt($('#load_5_task').attr('cp'));
            self.doLoadTasks(current_page);
            
        });
        
        $('.close-reveal').live('click',function()
        {
        	$(".close-reveal-modal").click();
        });
        
        $('.open_convert_form').live('click',function(){
        	var first_name = $('#first_name').val();
        	var last_name = $('#last_name').val();
        	$('.leadname').html( first_name + ' ' + last_name );
        });
        
        $('.allow_lead_login').click(function()
        {          
            var first_name = $('#first_name').val();
            var last_name = $('#last_name').val();
            
            $('#allowLeadLogin .leadname').html( first_name + ' ' + last_name );
        }); 
        
        $('a.lead_can_login').live('click',function()
        {    
            if($("#email").val() == "")
            {
                alert("You must first enter the lead's email address and save then update the lead before proceeding.");
                return;
            }
            
            var first_name = $('#first_name').val();
            var form = $("#frmLeadCanLogin");   
            var params = $(form).serialize();
            
            $.post($(form).attr("action"), params, function(data)
            {
                if(data.status != "OK")
                {
                    alert(data.message);
                    return;    
                }
                
                alert("Thank you.  An email has been sent to " + first_name + " with further instructions.");
                
                window.location.href = base_url + "leads";
            }, "json");  
        });
        
        $('.view_all_properties').live('click',function()
        {
        	self.checkAllProperties(this);
        });
        
        $('#project_id').live('change',function()
        {
        	var project_id = $(this).val();
        	if ((project_id != undefined) && (project_id != null) && (project_id != ''))
        	{
        		objApp.blockElement('.stock_project_permissions');
			    var user_id = $('#user_id').val();
	        	$.post(base_url+'leads/ajax', {
		    		action: 'assign_project',
		    		project_id: project_id,
		    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
		    		user_id: user_id
		    	}, function(data) {
		    		objApp.unblockElement('.stock_project_permissions');
		            if(data.status != "OK")
		            {
		                // The login failed.
		                alert(data.message);
		                return;
		            }
		            else
		            {
		            	self.loadProjectsPermisson(project_id);
		            	self.loadPropertyPermisson();
		            }
		        }, "json");
        	}
        });
        
        $('#property_id').live('change',function()
        {
        	var project_id = $('#project_id').val();
        	var property_id = $(this).val();
        	if ((property_id != undefined) && (property_id != null) && (property_id != ''))
        	{
        		objApp.blockElement('.stock_property_permissions');
			    var user_id = $('#user_id').val();
	        	$.post(base_url+'leads/ajax', {
		    		action: 'assign_property',
		    		property_id: property_id,
		    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
		    		user_id: user_id
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
		            	self.loadPropertyPermisson();
		            }
		        }, "json");
        	}
        });
        
        $('.remove_project').live('click',function()
        {
        	var property_permission_id = $(this).attr('rel');
        	var project_id = $(this).attr('pid');
        	objApp.blockElement('.stock_permissions');
        	$.post(base_url+'leads/ajax', {
	    		action: 'remove_project',
	    		property_permission_id: property_permission_id,
	    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
	    		project_id: project_id,
	    		user_id: $('#user_id').val()
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
					self.loadProjectsPermisson();
					self.loadPropertyPermisson();
	            }
	        }, "json");
        });
        
        $('.remove_property').live('click',function()
        {
        	var property_permission_id = $(this).attr('rel');
        	var project_id = $('#project_id').val();
        	objApp.blockElement('.stock_property_permissions');
        	$.post(base_url+'leads/ajax', {
	    		action: 'remove_property',
	    		property_permission_id: property_permission_id,
	    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
	    		user_id: $('#user_id').val()
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
	            	self.loadPropertyPermisson();
	            }
	        }, "json");
        });
        
        $('.view_property_assign').live('click',function(){
        	var project_id = $(this).attr('pid');
        	$("#project_id").find('option').removeAttr("selected");
        	$('#project_id option[value='+project_id+']').attr('selected', 'selected');
        	self.loadPropertyPermisson();
        });
        
        // Handle login as this user
        $('.btnlogin').live('click',function()
        {
            var user_id = $(this).attr('uid');
            var ajax_url = $(this).attr('action');
            var action = $(this).attr('action_name');
            $.post(ajax_url, {
                action: action,
                csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
                user_id: user_id
            }, function(data) {
                if(data.status != "OK")
                {
                    // The login failed.
                    $(".login_as_this_user_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
                    $(".login_as_this_user_error").show();
                    return;
                }
                
                objApp.redirect("");
                
            }, "json");
        });        
        
        var user_id = $('#user_id').val();
        if (user_id != '')
        {
        	self.checkAllProperties($('.view_all_properties'));	
        }
        
        $('.open_remove_login').live('click',function()
        {
        	$('.lead_first_name').html( $('#first_name').val() );
        });
        
        // Handle Remove login privileges
        $('.btnremovelogin').live('click',function()
        {
            var user_id = $(this).attr('uid');
            var ajax_url = $(this).attr('action');
            var action = $(this).attr('action_name');
            $.post(ajax_url, {
                action: action,
                csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
                user_id: user_id
            }, function(data) {
                if(data.status != "OK")
                {
                    // The login failed.
                    $(".remove_login_privileges_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
                    $(".remove_login_privileges_error").show();
                    return;
                }
                
                objApp.redirect("leads/detail/" + user_id);
                
            }, "json");
        }); 
    }
    
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
    	$.post(base_url+'leads/ajax', {
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
    	$.post(base_url+'leads/ajax', {
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
    	$.post(base_url+'leads/ajax', {
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
                objApp.redirect("leads");
                return;
            }
            // Show / hide link login as this user
            self.checkEnabled();
            
            $(".success").show();
            $('.success').delay(4000).fadeOut('medium');
        }, "json");
    }
    
	this.showTask = function(task_id)
    {
        var form = $("#frmLoad");
        $(form).find("#task_id").val(task_id);
        
        var params = $(form).serialize(); 
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the task listing");
                return;    
            }
            
            self.showTaskForm();
                                
            $("#frmDetails").find("#task_id").val(task_id);
            $("#title").val(data.message.title);
            $("#due_date").val(data.message.due_date);
            $("#assign_to").val(data.message.assign_to);
            $("#description").val(data.message.description);
            
            $("input[name='priority'][value='" + data.message.priority + "']").attr("checked", "checked");
            
            // Show the completed checkbox
            $("#taskCompletedWrapper").show();
            
            if(data.message.status == 1)
            {
                $("#frmDetails #status").attr("checked", "checked");    
            }
            else
            {
                $("#frmDetails #status").removeAttr("checked");    
            }
            
        }, "JSON")        
    }

    this.showTaskForm = function()
    {
        // Clear the form
        self.clearForm();
        
        // When adding a new task, do not show the task completed checkbox.
        $("#taskCompletedWrapper").hide();

        $('#taskDetail').reveal(
        {
             animation: 'fadeAndPop',                   //fade, fadeAndPop, none
             animationspeed: 300,                       //how fast animtions are
             closeonbackgroundclick: true,              //if you click background will modal close?
             dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
        });
        
        setTimeout(function()
        {
            /*
            $('input:checkbox').screwDefaultButtons({
                 checked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
                 unchecked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
                 width: 15,
                 height: 16
            });
            */
                                                                 
        }, 500);    
    }
    
    this.showTaskDeleteForm = function(task_id, task_name)
    {
        // Show the task delete form
        $('#taskDelete').reveal(
        {
             animation: 'fadeAndPop',                   //fade, fadeAndPop, none
             animationspeed: 300,                       //how fast animtions are
             closeonbackgroundclick: true,              //if you click background will modal close?
             dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
        });
        
        // Delete
        $("#frmDelete #task_id").val(task_id);        
        $("#frmDelete p.confirmMessage").text('You are about to delete the task "' + task_name + '".  Are you sure you wish to continue?');
        
        // Find the event when the user clicks on the task delete button
        $("#frmDelete #btnDeleteTask").click(function(e)
        {
            e.preventDefault();
            
            var form = $("#frmDelete");
            
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
                
                // The task was delete successfully. 
                // Close the reveal window and reload the task list
                $(".close-reveal-modal").click();
                
                var current_page = parseInt($('#load_5_task').attr('cp'));
                
                self.doLoadTasks(current_page); 
                
            }, "json");           
        });
    }
    
    this.showNote = function(note_id)
    {
        var form = $("#frmLoadNote");
        $(form).find("#note_id").val(note_id);
        
        var params = $(form).serialize(); 
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the note listing");
                return;    
            }
            
            self.showNoteForm();
                                
            $("#frmNoteDetails").find("#note_id").val(note_id);
            $("#note_date").val(data.message.note_date);
            $("#content").val(data.message.content);
            
            if(data.message.enabled == 1)
            {
                $("#frmNoteDetails #enabled").attr("checked", "checked");    
            }
            else
            {
                $("#frmNoteDetails #enabled").removeAttr("checked");    
            }
            
        }, "JSON")        
    }
    
    this.showNoteForm = function()
    {
        // Clear the form
        self.clearFormNote();
        
        $('#noteDetail').reveal(
        {
             animation: 'fadeAndPop',                   //fade, fadeAndPop, none
             animationspeed: 300,                       //how fast animtions are
             closeonbackgroundclick: true,              //if you click background will modal close?
             dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
        });
        
        setTimeout(function()
        {
            /*
            $('input:checkbox').screwDefaultButtons({
                 checked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
                 unchecked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
                 width: 15,
                 height: 16
            });
            */
                                                                 
        }, 500);    
    }
    
    this.showNoteDeleteForm = function(note_id)
    {
        // Show the task delete form
        $('#noteDelete').reveal(
        {
             animation: 'fadeAndPop',                   //fade, fadeAndPop, none
             animationspeed: 300,                       //how fast animtions are
             closeonbackgroundclick: true,              //if you click background will modal close?
             dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
        });
        
        // Delete
        $("#frmNoteDelete #note_id").val(note_id);        
        $("#frmNoteDelete p.confirmMessage").text('You are about to delete the note.  Are you sure you wish to continue?');
        
        // Find the event when the user clicks on the note delete button
        $("#frmNoteDelete #btnDeleteNote").click(function(e)
        {
            e.preventDefault();
            
            var form = $("#frmNoteDelete");
            
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
                
                // The task was delete successfully. 
                // Close the reveal window and reload the task list
                $(".close-reveal-modal").click();
                
                var current_page = parseInt($('#load_5_note').attr('cp'));
                
                self.doLoadNotes(current_page); 
                
            }, "json");             
        });
    }
}

var uri = objApp.getURI();

if(uri == "leads")
{
    var objLeadList = new LeadList();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("paginator.js");
        
        // Setup the partner object
        objLeadList.init(); 
    }    
}
else
{
    var objLeadDetail = new LeadDetail();

    // Load additional JS libs needed
    window.onload  = function()
    {
        objApp.include("jquery-customCB.js");
        
        // Setup the partner object
        objLeadDetail.init(); 
    }   
}