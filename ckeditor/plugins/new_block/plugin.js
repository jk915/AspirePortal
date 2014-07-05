var global_editor;  

CKEDITOR.plugins.add( 
	'new_block',
	{
		init:function(editor)
		{
			var pluginName = "new_block";
			
			// add new dialog
			/*CKEDITOR.dialog.add(
				pluginName, 
				this.path + 'dialog/new_block.js'
			);*/
			
			// add command to the plugin
			/*editor.addCommand(
					pluginName, showNewBlockPopup( editor ) //new CKEDITOR.dialogCommand( pluginName )
				);
			*/
			
			editor.addCommand(
				pluginName,
				{
					canUndo: true,
				
					exec: function(){
						showNewBlockPopup( editor );
						//exit(1);
						//new CKEDITOR.dialogCommand( pluginName )
					}
				}
			);
				
			
			
			// add button to toolbar
			editor.ui.addButton( 
				pluginName,
				{
					label: 'Add new block',
					command: pluginName,
					icon: this.path + 'images/logo_ckeditor.png'
				}
			);
		}
	}
);

function showNewBlockPopup( editor )
{
	var parameters = {};
	parameters['type'] = 3;
	global_editor = editor;
	
	$.post( base_url + 'admin/blockmanager/ajaxwork', parameters, function( data ){
		
		var width = $(document).width();
        var elementWidth = 500;
        var elementHeight = 450;
        var scroll_array = getScrollXY();   // defined in js/ckeditorImage.js
        var top = scroll_array[1] + 20;          
        
        $.blockUI
        ({
            message: data.html,
            css: { cursor: 'normal', 'padding-bottom': '50px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2), top: top },
            overlayCSS: { cursor: 'normal' },
            centerX: true,
            centerY: true

        });
        
        $('.w_close').click(function(e) {
              e.preventDefault();
              $(document).unblock();
        });
        
        // initialization of tooltip
        tooltip();
        // initialization of scroll pane
        $('#scroll_page_listing').jScrollPane();
        
        $( 'a.tooltip' ).live("click", function(e){
        	e.preventDefault();
        	var block_name = $(this).attr('href');
        	
        	global_editor.insertHtml( ' [[BLOCK_' + block_name + ']] ');
        	$('.w_close').click();
        	
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
	$("#tableBlocks a.tooltip").hover(function(e){											  
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