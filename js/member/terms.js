var Terms = function()
{
    var self = this;
    
    // Entry point.
    this.init = function()
    {
        this.bindEvents();
    } 
    
    this.bindEvents = function()
    {
        $("#btnAgree").click(function(e)
        {
            e.preventDefault();
            
            if($("#agree_to_terms").is(":checked"))
            {
                self.agree($("#frmAgree"));    
            }
            else
            {
                alert("You must agree to our terms & conditions before proceeding.");
            }
        });
    }
    
    this.agree = function(form)
    {
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
            
            window.location.href = base_url + 'dashboard';

        }, "json");       
    } 
}

var objTerms = new Terms();

// Load additional JS libs needed
window.onload  = function()
{        
    // Setup the advisor object
    objTerms.init(); 
}    
