var SummaryList = function()
{
    var self = this;
    this.paginator = false;
    this.first_load = true;
    this.sort_col = "s.created_date";
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
        
        $("a.showsummary").unbind();    
        $("input.delete").unbind();
        
        var params = $(form).serialize(); 
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the summary listing");
                return;    
            }
            
            $("table.listing tbody").html(data.message);
            
            $("a.showsummary").click(function(e)
            {
                e.preventDefault();
                
                var summary_id = $(this).attr("href");
                
                self.showSummary(summary_id);    
            });
            
            $("input.delete").click(function(e)
            {   
                var summary_id = $(this).val();
                var summary_name = $(this).parent().parent().find("td:eq(0)").text();
                var checkbox = this;

                if($(this).is(":checked"))
                {    
                    // Show the summary delete form
                    self.showSummaryDeleteForm(summary_id, summary_name);
                        
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
                self.checkShowSummary();    
            }
            
        }, "json");
    }
    
}



var SummaryDetail = function()
{
    var self = this;
    
    // Entry point.
    this.init = function()
    {    
         this.bindEvents();       
    }
    
    
    
    this.bindEvents = function()
    {   
        // Handle form submission event  
        $("#frmSummaryDetail").submit(function(e)
        {              
            e.preventDefault();
            // Make sure the form is valid.
            if(!$(this).validate(
            {
                errorLabelContainer: $("#frmLeadDetail div.error"),
                messages: 
                {
                    title: "Title",
                    description: "Description"
                }
            }).form())
            {
                return false;
            }            
            
            // Form is OK, submit it.
            self.saveSummary(this);
        });
        
        $('#state_id').change(function(){
            var params = {}; 
            params.state_id = $(this).val();
            params.action = 'load_areas';
            params.csrftokenaspire = $('input[name="csrftokenaspire"]').val();
            $.post($('#frmSummaryDetail').attr("action"), params, function(data)
            {
                objApp.unblockElement('frmSummaryDetail');
                
                if(data.status != "OK")
                {
                    alert(data.message);
                    return;    
                }
                
                $('#area_id').html(data.html);
                
            }, "json");   
        });
        
        $('#area_id').change(function(){
            var params = {}; 
            params.area_id = $(this).val();
            params.action = 'load_projects';
            params.csrftokenaspire = $('input[name="csrftokenaspire"]').val();
            $.post($('#frmSummaryDetail').attr("action"), params, function(data)
            {
                objApp.unblockElement('frmSummaryDetail');
                
                if(data.status != "OK")
                {
                    alert(data.message);
                    return;    
                }
                
                $('#project_id').html(data.html);
                
            }, "json");   
        });
        
        $('#manual_type').click(function(){
            if ($(this).is(':checked')) {
                $("#prepared_for_manual").show();
                $("#prepared_for").hide();
            } else {
                $("#prepared_for_manual").hide();
                $("#prepared_for").show();
            } 
        });
        
        $('#createDuplicate').click(function(){
            objApp.blockElement('frmSummaryDetail');
            window.history.pushState(base_url, "Summary Detail","/summaries/detail");
            $('#prepared_for').val('');
            $('#summary_id').val('');
            $('.sidebar').hide();
            objApp.unblockElement('frmSummaryDetail');
        });
    }   


    this.saveSummary = function(form)
    {
        // Hide the error state div.
        $("div.error").hide();
        
        var params = $(form).serialize();
        
        var summary_id = $(form).find("#summary_id").val();
        
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
            
            // updating / adding the summary record was successful.
            if(summary_id == "")
            {
                objApp.redirect("summaries");
                return;
            };
            
            $(".success").show();
            $('.success').delay(4000).fadeOut('medium');
        }, "json");
    }
    
    
    $('.delete_summary').live('click',function(){
        var summary_id = $(this).attr('summaryid');
        var ajax_url = $(this).attr('action');
        var action = $(this).attr('action_name');
        $.post(ajax_url, {
            action: action,
            csrftokenaspire: $('input[name="csrftokenaspire"]').val(),
            summary_id: summary_id
        }, function(data) {
            if(data.status != "OK")
            {
                // The login failed.
                $(".delete_error").html('<h4>The Following Error Occured</h4><p>' + data.message + '</p>'); 
                $(".delete_error").show();
                return;    
            }
            
            // delete the user record was successful.
            if(summary_id == "")
            {
                objApp.redirect("summaries");
                return;
            }
            
            objApp.redirect("summaries");
            
        }, "json");
    });
    
    $('.close-reveal').live('click',function()
    {
        $(".close-reveal-modal").click();
    });
}


var uri = objApp.getURI();

if(uri == "summaries")
{
    var objSummaryList = new SummaryList();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("paginator.js");
        
        // Setup the advisor object
        objSummaryList.init(); 
    }  
}
else
{
    var objSummaryDetail = new SummaryDetail();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("jquery-customCB.js");
        
        // Setup the advisor object
        objSummaryDetail.init(); 
    } 
}
