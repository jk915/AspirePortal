var Announcements = function()
{
    var self = this;
    this.paginator = false;
    this.sort_col = "a.article_date";
    this.sort_dir = "DESC";
    this.form = false;
    this.form_prefix = "";
    
    // Entry point.
    this.init = function()
    {
        this.form = $("#frmSearch");
        this.paginator = new Paginator(this.form, "div.mainCol");
        
        this.bindEvents();
        
        $(this.form).find("#current_page").val("1");
        
        $(this.form).submit();
    } 
    
    this.initDashboard = function()
    {
        this.form = $("#frmAnnouncements");
        this.paginator = new Paginator(this.form, "div.mainCol");
        this.form_prefix = "announcements_";
        
        this.bindEvents();
        
        $(this.form).find("#current_page").val("1");
        
        $(this.form).submit();
    }     
    
    this.bindEvents = function()
    {
        $(this.form).submit(function(e)
        {
            e.preventDefault();
            
            self.doSearch();    
        });
        
        $("table.announcementlist th").click(function(e)
        {
            e.preventDefault();
            
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

    this.doSearch = function()
    {
        // If the form search event is being called but not via the paginator,
        // reset the current page number
        if(!self.paginator.paging_changed)
        {
            $(this.form).find("#current_page").val("1"); 
            self.paginator.current_page = 1;  
        }    

        $(this.form).find("#" + this.form_prefix + "sort_col").val(this.sort_col);
        $(this.form).find("#" + this.form_prefix + "sort_dir").val(this.sort_dir);        
        
        $("table.announcementlist tr a").unbind();
        
        var params = $(this.form).serialize(); 
        objApp.blockElement($(this.form));
        
        $.post($(this.form).attr("action"), params, function(data)
        {
            objApp.unblockElement($(self.form));
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the help listing");
                return;    
            }
            
            $("table.announcementlist tbody").html(data.message);
            
            // Set the total number of records into the form and update the paginator
            $("#count_all").val(data.count_all);

            self.paginator.refresh();
            
            self.handleListingClick();            
            
        }, "json");
    }
    
    this.handleListingClick = function()
    {
        $("table.announcementlist tr a").click(function(e)
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
            var action = base_url + "announcements/ajax";
            
            objApp.blockElement("table.announcementlist");
            
            $.post(action, params, function(data)
            {
                objApp.unblockElement("table.announcementlist");
                
                if(data.status != "OK")
                {
                    alert("Sorry, something went wrong whilst sending the download request");
                    return;    
                }
                
                $("#article_modal").css("height", data.height + "px");
                $("#article_modal").css("width", data.width + "px");
                
                $("#article_modal div").html(data.message);
                
                $("#article_modal div.comments").css("height", "220px");
                
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

var uri = objApp.getURI();

if(uri == "announcements")
{
    var objAnnouncements = new Announcements();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("paginator.js");
        objApp.include("jquery.mousewheel.js");
        objApp.include("mwheelIntent.js");
        objApp.include("jScrollPane.js");
        
        // Setup the advisor object
        objAnnouncements.init(); 
    }  
}  
