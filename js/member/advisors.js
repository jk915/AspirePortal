var AdvisorList = function()
{
    var self = this;
    this.paginator = false;
    this.sort_col = "u.first_name";
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
                alert("Sorry, something went wrong whilst loading the advisor listing");
                return;    
            }
            
            $("table.listing tbody").html(data.message);
            
            // Set the total number of records into the form and update the paginator
            $("#count_all").val(data.count_all);

            self.paginator.refresh();
            
        }, "json");
    }   
}

var AdvisorDetail = function()
{
    var self = this;
    
    // Entry point.
    this.init = function()
    {
        $('input:checkbox').screwDefaultButtons({
             checked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
             unchecked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
             width: 15,
             height: 16
         });  
         
         //$('.success h4').delay(4000).fadeOut('medium');                            
         
         $('table.zebra tr:odd').addClass('alt'); 
         
         this.bindEvents();       
    } 
    
    this.bindEvents = function()
    {
        // Handle form submission event  
        $("#frmAdvisorDetail").submit(function(e)
        {              
            e.preventDefault();
            
            // Make sure the form is valid.
            if(!$(this).validate(
            {
                errorLabelContainer: $("#frmAdvisorDetail div.error"),
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
        
        $('.close-reveal').live('click',function()
        {
        	$(".close-reveal-modal").click();
        });
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
                objApp.redirect("advisors");
                return;
            }
            
            $(".success").show();
            $('.success').delay(4000).fadeOut('medium');
        }, "json");
    }  
}

var uri = objApp.getURI();

if(uri == "advisors")
{
    var objAdvisorList = new AdvisorList();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("paginator.js");
        
        // Setup the advisor object
        objAdvisorList.init(); 
    }    
}
else
{
    var objAdvisorDetail = new AdvisorDetail();

    // Load additional JS libs needed
    window.onload  = function()
    {
        objApp.include("jquery-customCB.js");
        
        // Setup the advisor object
        objAdvisorDetail.init(); 
    }   
}