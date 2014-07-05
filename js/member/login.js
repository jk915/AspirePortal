var Login = function()
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
        
        this.bindEvents();
    } 
    
    this.bindEvents = function()
    {
        
        // Handle the event when the user wants to login
        $("#frmLogin").submit(function(e)
        {
            e.preventDefault();
            
            // Make sure the form is filled in correctly.
            if(!$(this).validate().form())
            {
                alert("Please enter all required fields");
                return;
            }
            
            // Attempt to login.
            self.doLogin(this);       
        });
        
        $("#btnRegister").click(function(e)
        {
            e.preventDefault();
            
            $("#frmRegisterSubmit").unbind();
            
            $('#registerMember').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });  
            
            $("#frmRegisterSubmit").click(function(e)
            {
                var form = $("#frmRegister");
                
                e.preventDefault();
                
                if(!$(form).validate().form())
                {
                    alert("Please enter all required fields");
                    return;    
                }
 
                var params = $(form).serialize();
                
                objApp.blockElement(form);
                
                $.post($(form).attr("action"), params, function(result)
                {
                    objApp.unblockElement(form);   
                    
                    if(result.status != "OK")
                    {
                        alert(result.message);
                        return;        
                    }
                    
                    $(".close-reveal-modal").click();
                    
                    alert("Thank you for registering with the Aspire Network.  Our staff have received your information and will activate your account shortly.  We have sent you an email for confirmation.");

                }, "json");
            });
        });
        
        $("#btnReset").click(function(e)
        {
            e.preventDefault();
            
            $("#frmResetSubmit").unbind();
            
            $('#passwordReset').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
            
            $("#frmResetSubmit").click(function(e)
            {
                var form = $("#frmReset");
                
                e.preventDefault();
                
                if(!$(form).validate().form())
                {
                    alert("Please enter all required fields and ensure your password meets our requirements.");
                    return;    
                }
 
                var params = $(form).serialize();
                
                objApp.blockElement(form);
                
                $.post($(form).attr("action"), params, function(result)
                {
                    objApp.unblockElement(form);   
                    
                    if(result.status != "OK")
                    {
                        alert(result.message);
                        return;        
                    }
                    
                    $(".close-reveal-modal").click();
                    
                    alert("Thank you.  We've just sent you an email with further instructions to activate your new password.");

                }, "json");
            });            
            
        });        
    } 
    
    this.doLogin = function(form)
    {
        var params = {};
        params["csrftokenaspire"] = $(form).find('input[name="csrftokenaspire"]').val();
        params["action"] = "login";
        params["email"] = $(form).find('#email').val();
        params["password"] = $(form).find('#password').val();
        
        objApp.blockElement(form);
        
        $.post(base_url + "login/ajax", params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                // The login failed.
                alert(data.message);
                return;    
            }
            
            // The login was successful, redirect to the dashboard
            objApp.redirect(data.redirect_url);
            
        }, "json");
    }  
}

var objLogin = new Login();

// Load additional JS libs needed
window.onload  = function()
{
    objLogin.init(); 
}