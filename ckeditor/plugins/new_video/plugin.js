var global_editor;

CKEDITOR.plugins.add( 
    'new_video',
    {
        init:function(editor)
        {
            var pluginName = "new_video";
            
            editor.addCommand(
                pluginName,
                {
                    canUndo: true,
                
                    exec: function(){
                        showNewVideoPopup( editor );                        
                    }
                }
            );
                          
            // add button to toolbar
            editor.ui.addButton( 
                pluginName,
                {
                    label: 'Add new video',
                    command: pluginName,
                    icon: this.path + 'images/logo_ckeditor.jpg'
                }
            );
        }
    }
);

function showNewVideoPopup( editor )
{
    var parameters = {};
    parameters['type'] = 4;
    global_editor = editor;
    
    $.post( base_url + 'admin/blockmanager/ajaxwork', parameters, function( data )
    {
        
        var width = $(document).width();
        var elementWidth = 500;
        var elementHeight = 300;
        var scroll_array = getScrollXY();   // defined in js/ckeditorImage.js
        var top = scroll_array[1] + 20;  

        $.blockUI
        ({
            css: { cursor: 'normal', 'padding-bottom': '50px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2), top: top },
            message: data.html,
            overlayCSS: { cursor: 'normal' },
            centerX: true,
            centerY: true

        });   
        
        $('.w_close').click(function(e) {
              e.preventDefault();
              $(document).unblock();
        });
        
        // initialization of tooltip
        //tooltip();
        // initialization of scroll pane
        $('#scroll_page_listing').jScrollPane();
        
        $("#divVideos #button").live("click", function(e)
        {       
            var video_id = $("#video_id", $('#divVideos')).val();
            
            if(video_id != "")
            {                         
                global_editor.insertHtml( ' [[VIDEO-' + video_id + ']] ');
                $('.w_close').click();
            }
            else
                alert("Please enter a Video ID");
                
            return false; 	// We must return false to prevent future events firing on the .live event.
        });        
        
    }, "json");
}


function tooltip(){    
    /* CONFIG */        
        xOffset = 10;
        yOffset = 20;        
        // these 2 variable determine popup's distance from the cursor
        // you might want to adjust to get the right result        
    /* END CONFIG */        
    $("#divVideos a.tooltip").hover(function(e){                                              
        this.t = this.title;
        this.title = "";                                      
        $("body").append("<p id='tooltip'>"+ this.t +"</p>");
        $("#tooltip")
            .css("top",(e.pageY - xOffset) + "px")
            .css("left",(e.pageX + yOffset) + "px")
            .fadeIn("fast");        
    },
    function(){
        this.title = this.t;        
        $("#tooltip").remove();
    });    
    $("a.tooltip").mousemove(function(e){
        $("#tooltip")
            .css("top",(e.pageY - xOffset) + "px")
            .css("left",(e.pageX + yOffset) + "px");
    });            
};