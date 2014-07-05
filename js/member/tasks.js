var TaskList = function()
{
    var self = this;
    this.paginator = false;
    this.first_load = true;
    this.sort_col = "t.due_date";
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
        
        $("#btnAddTask").click(function(e)
        {
            e.preventDefault();
            
            self.showTaskForm();
                       
        });
        
        $("#due_date").datepicker({ dateFormat: 'dd/mm/yy' });
        
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
            
            self.doSearch("#frmSearch"); 
            
        }, "json");       
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
        
        $("a.showtask").unbind();    
        $("input.delete").unbind();
        
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
            
            $("table.listing tbody").html(data.message);
            
            $("a.showtask").click(function(e)
            {
                e.preventDefault();
                
                var task_id = $(this).attr("href");
                
                self.showTask(task_id);    
            });
            
            $("input.delete").click(function(e)
            {   
                var task_id = $(this).val();
                var task_name = $(this).parent().parent().find("td:eq(0)").text();
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
            
            // Set the total number of records into the form and update the paginator
            $("#count_all").val(data.count_all);

            self.paginator.refresh();
            
            if(self.first_load)
            {
                self.first_load = false;
                self.checkShowTask();    
            }
            
        }, "json");
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
                
                self.doSearch("#frmSearch"); 
                
            }, "json");             
        });
    }
    
    
    this.checkShowTask = function()
    {
        var hashTag = objApp.getHashTagFromURI();
        
        if(hashTag == "")
        {
            return;   
        }
        else if(hashTag == "add")
        {
            this.showTaskForm();    
        } 
        else if(!isNaN(hashTag))
        {
            var task_id = hashTag;
            
            this.showTask(task_id);  
        }
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
        
        $("#frmRegisterSubmit").unbind();
        
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
}

var objTaskList = new TaskList();

// Load additional JS libs needed
window.onload  = function()
{        
    objApp.include("paginator.js");
    objApp.include("jquery-customCB.js");
    
    // Setup the advisor object
    objTaskList.init(); 
}    
