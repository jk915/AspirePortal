var Media = function()
{
    var self = this;
    this.paginator = false;
    this.sort_col = "a.article_date";
    this.sort_dir = "DESC";
    
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
        
        $("#showHideAreas").click(function(e)
        {
            e.preventDefault();
            
            $("#areaListing").toggle();    
            $("#showHideAreas").hide();
        });
        
        $('.email_resource').live('click',function()
        {
        	$('#emailResource').show();
        	self.selectedUser();
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
    }
    
    this.selectedUser = function()
    {
    	var email_resource = $('#email_resource_to').val();
    	$('#email_resource').val(email_resource);
    	
    	if($('#ck_external_users').is(':checked'))
    	{
    		$(".send_to_external_users").show();  // checked
		    $(".send_to_users_network").hide();  // checked
    	}
		else
		{
			$(".send_to_external_users").hide();  // checked
		    $(".send_to_users_network").show();  // checked
		}
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
        
        $("table.listing tr a").unbind();
        
        var params = $(form).serialize(); 
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the media listing");
                return;    
            }
            
            $("table.listing tbody").html(data.message);
            
            // Set the total number of records into the form and update the paginator
            $("#count_all").val(data.count_all);

            self.paginator.refresh();
            
            self.handleListingClick();            
            
        }, "json");
    }
    
    this.handleListingClick = function()
    {
        $("table.listing tr a").click(function(e)
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
            objApp.blockElement("table.listing");
            
            $.post($(form).attr("action"), params, function(data)
            {
                objApp.unblockElement("table.listing");
                
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
    }   
}

var objMedia = new Media();

// Load additional JS libs needed
window.onload  = function()
{        
    objApp.include("paginator.js");
    objApp.include("jquery.mousewheel.js");
    objApp.include("mwheelIntent.js");
    objApp.include("jScrollPane.js");
    
    // Setup the advisor object
    objMedia.init(); 
}    
