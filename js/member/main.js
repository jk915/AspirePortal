// Define application object - holds general helper functions etc.
var App = function()
{
    var self = this;
    
    /***
    * Is called when the document ready event fires.
    */
    this.init = function()
    {
        this.selectActiveNavItem();
        this.bindEvents();
    }
    
    this.bindEvents = function()
    {
        $("#btnSupport").click(function(e) {
            e.preventDefault();
            
            self.showSupportModal();
        });    
    }
    
    /***
    * Selects the active navigation item.
    */
    this.selectActiveNavItem = function()
    {
        var uri = this.getURI();
        $('#nav li a[href*="' + uri + '"]').addClass("active");
    } 
    
    /***
    * Returns the current uri that the visitor is viewing, e.g. "index.php"
    */
    this.getURI = function()
    {
        /*
        var lastSlashPos = window.location.href.lastIndexOf("/");
        
        if(lastSlashPos <= 0)
        {
            return window.location.href;
        }
        
        var uri = window.location.href.substring(lastSlashPos + 1);
        */
        var uri = window.location.href.replace(base_url, "");
        
        // Check for a hash tag and if found strip it from the URI
        var hashTagPos = uri.indexOf("#");
        if(hashTagPos > 0)
        {
            uri = uri.substring(0, hashTagPos);  
        }
        
        if(uri == "") uri = "index.php";
        
        return uri;     
    } 
    
    this.getHashTagFromURI = function()
    {
        var uri = window.location.href;
        var hashTag = "";
        
        // Check for a hash tag and if found strip it from the URI
        var hashTagPos = uri.indexOf("#");
        if(hashTagPos > 0)
        {
            hashTag = uri.substring(hashTagPos + 1);  
        } 
        
        return hashTag;           
    }
    
    /***
    * Redirects the user to the specified uri
    * 
    * @param string uri The uri to redirect the user to (note the base_url is automatically prefixed).
    */
    this.redirect = function(uri)
    {
        window.location.href = base_url + uri;    
    }  
    
    /***
    * Dynamically loads the specified JS library
    * 
    * @param string scriptName - The name of the js library to load.  Must be in the js/members folder
    */
    this.include = function(scriptName)
    { 
        $("body").append('<script type="text/javascript" src="' + base_url + 'js/member/' + scriptName + '"><script>');    
    } 
    
    this.blockElement = function(elementSelector)
    {
        $(elementSelector).block(
        {
            message: '<div><img src="'+base_url+'images/admin/ajax-loader-big.gif"/></div>',  
            css: {border:'0px','background-color':'transparent',position:'absolute'},
            overlayCSS: {opacity:0.04,cursor:'pointer',position:'absolute'}
        });
     }

    this.unblockElement = function(elementSelector)
    {
        $(elementSelector).unblock();
    } 
    
    /****
    * Used on pages that take a while to load and render
    * Hides the ajax loader
    */
    this.hideCurtain = function()
    {
        $("#curtain").hide();
        $("div.inner").css("visibility", "visible");
    } 
    
    this.showSupportModal = function()
    {
        $("#btnSubmitSupport").unbind();
        
        var params = $("#frmGetSupport").serialize();
        
        $.post(base_url + "postback/get_support_form", params, function(data) {
            if(data.status != "OK") return;
            
            // Set the modal code into the ajax modal area.
            $("#ajaxmodal").html(data.message);   
            
            // Now show the modal
            $("#supportModal").reveal({
                dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
            
            $("#btnSubmitSupport").click(function(e) {
                e.preventDefault();
                
                var form = $("#frmSupport");
                
                if($(form).validate().form())
                {
                    self.blockElement("#frmSupport");
                    
                    var params = $(form).serialize();
                    var action = $(form).attr("action");
                    
                    $.post(action, params, function(data) {
                        self.unblockElement("#frmSupport");
                        
                        if(data.status != "OK") {
                            alert(data.message);
                        } else {
                            alert("Thank you for submitting your support request. This will be responded to within 2 working days.");
                            
                            // Close the modal window.
                            $("#btnCancelSupport").click();
                        }    
                    }, "json");
                }
            });
        }, "json");    
    }             
}

// Define the application object
var objApp = new App();

$(document).ready(function()
{
    objApp.init();   
});