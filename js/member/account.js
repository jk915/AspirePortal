var Account = function()
{
    var self = this;
    
    // Entry point.
    this.init = function()
    {
        jQuery.validator.addMethod("strongpass", function(value, element) {
            if(value.length < 6) {
                return false;    
            }
            
            var mustContainUpper = /[A-Z]/;
            var result = mustContainUpper.exec(value);
            if((result == null) || (result == "")) return false;
            
            var mustContain09 = /[0-9]/;
            result = mustContain09.exec(value);
            if((result == null) || (result == "")) return false; 
            
            return true;       
        }, "Min 6 characters plus at least 1 digit and 1 upper case letter");         
        
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
    	$('.change_password').live('click',function(e)
    	{
    		e.preventDefault();
    		
    		// Make sure the form is valid.
            if(!$('#frmChangePassword').validate().form())
            {
                return false;
            }
    		
        	var ajax_url = $('#frmChangePassword').attr("action");
        	var action = $(this).attr('action');
        	var new_password = $('#new_password').val();
        	var re_new_password = $('#re_new_password').val();
        	$.post(ajax_url, {
	    		action: action,
	    		csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
	    		new_password: new_password,
	    		re_new_password: re_new_password
	    	}, function(data) {
	            if(data.status != "OK")
	            {
	                // The login failed.
	                $(".change_password_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
	                $(".change_password_error").show();
	                return;    
	            }
	            $(".change_password_error").hide();
	            $(".change_password_success").show();
            	$('.change_password_success').delay(4000).fadeOut('medium');
	            
	        }, "json");
        });
        
        $('.close-reveal').live('click',function()
        {
        	$('#changePassword').hide();
        	$(".close-reveal-modal").click();
        });
    	
        // Handle form submission event  
        $("#frmAccountDetail").submit(function(e)
        {              
            e.preventDefault();
            
            // Make sure the form is valid.
            if(!$(this).validate(
            {
                errorLabelContainer: $("#frmAccountDetail div.error"),
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
    } 
    
    this.updateUser = function(form)
    {
        // Hide the error state div.
        $("div.error").hide();
        
        var params = $(form).serialize();
        
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
            
            $(".success").show();
            $('.success').delay(4000).fadeOut('medium');
        }, "json");
    }  
}

var objAccount = new Account();

// Load additional JS libs needed
window.onload  = function()
{
    objApp.include("jquery-customCB.js");
    
    // Setup the advisor object
    objAccount.init(); 
}   
