function getScrollXY() 
{
	var scrOfX = 0, scrOfY = 0;
	
	if( typeof( window.pageYOffset ) == 'number' ) 
	{
		//Netscape compliant
		scrOfY = window.pageYOffset;
		scrOfX = window.pageXOffset;
	} 
	else if( document.body && ( document.body.scrollLeft || document.body.scrollTop ) ) 
	{
		//DOM compliant
		scrOfY = document.body.scrollTop;
		scrOfX = document.body.scrollLeft;
	} 
	else if( document.documentElement && ( document.documentElement.scrollLeft || document.documentElement.scrollTop ) ) 
	{
		//IE6 standards compliant mode
		scrOfY = document.documentElement.scrollTop;
		scrOfX = document.documentElement.scrollLeft;
	}
	
	return [ scrOfX, scrOfY ];
}

var ratio = 0;	// Contains the aspect ratio of the currently selected image.

$(document).ready(function()
{    
    initCkeditor( 'auto' );
    
    // Handle the event when the user clicks on the VIEW PREVIEW button.
    $('.view_preview').click(function(e) 
    {
        e.preventDefault();	// Prevent href click action
        
        // Get the id of the ckeditor area
        
        var textarea_ids			= {};
        var textarea_contents		= {};
        var oEditor					= '';
        
        var i						= 0;
        var div						= $( '.css-panes > div:eq('+$( '#tab_number' ).val()+')' );
        
        // get the preview button names
        $( div ).find( '.view_preview' ).each(function(){
	        textarea_ids[ i ]		= $( this ).attr( 'name' );
	        
	        var input_id			= textarea_ids[ i ].split( '|' );
	        input_id				= input_id[ 2 ];

	        // get the textarea content
	        // Get a handle to the ckeditor instance        
	        oEditor = CKEDITOR.instances[input_id];
	        
	        // if no ckeditor with this id
	        if(!oEditor)
	        {
	        	// get ckeditor with id: textarea_ + id
	        	oEditor = CKEDITOR.instances[ 'textarea_'+input_id ];
	        	
	        	// if no ckeditor with this id
	        	if(!oEditor)
	        	{
	        		// get ckeditor by type
		        	oEditor = CKEDITOR.instances.wysiwyg;
		        	
		        	// if no ckeditor
		        	if(!oEditor)
		        	{
		        		alert("Sorry, we couldn't get a handle on the WYSIWYG editor'");
		    			return;
					}
	        	}
			}
	        
	        // Get the HTML value of the editor.
			textarea_contents[i] = oEditor.getData();
	        ++i;
        });
		
		// Post the HTML to the server
        var parameters 						= {};
        parameters['type'] 					= 1;
        parameters['ids_types_name']		= textarea_ids;
        parameters['htmls'] 				= textarea_contents;
        parameters['table']					= $("#table").val();
        parameters['content_type']			= $( this ).attr( 'rel' );
        
        $.post( base_url + 'global/ajaxwork/preview', parameters, function( data )
        {
        	if(data.message == "OK")
        	{
				// Launch the preview window
				var url = base_url + data.redirect_to;
				window.open (url, "Preview", "menubar=1,resizable=1,width=1024,height=700,scrollbars=1"); 				
        	}
		}, "json");

	});    
    
    $('.view_history').click(function(e) {
      
        e.preventDefault();
       
        var parameters = {};
        parameters['type'] = 1;
        parameters['table'] = $("#table").val();
        parameters['field'] = $("#field").val();
        parameters['history_type'] = $(this).attr("href");
        parameters['id'] = $("input[name=id]").val();   
        
        $.post( base_url + 'global/ajaxwork/view_history', parameters, function( data ){
            
            var width = $(document).width();
            var elementWidth = 980;
            var elementHeight = 500;
            var scroll_array = getScrollXY();
            var top = scroll_array[1] + 20;
            
            $.blockUI
            ({
                message: data.html,
                css: { cursor: 'normal', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2), top: top + "px", position: 'absolute' },
                overlayCSS: { cursor: 'normal' },
                centerX: true,
                centerY: true

            });
            
            $( document ).ready(function(){
            	
            	$('.w_close').click(function(e) {
	                    e.preventDefault();
	                    
	                    var o=CKEDITOR.instances['history_editor'];
	                    if (o) o.destroy();
	                    
	                    $(document).unblock();
	                    
	              });
            	
            	$('textarea.editor').ckeditor();
                
                
                // initialization of scroll pane
                $('#scroll_page_listing').jScrollPane();
                
                $('.history_popup #history_time').change(function(){
                    
	                
	                    var parameters = {};
	                    parameters['type'] = 2;
	                    parameters['history_type'] = $(".history_popup #history_type").val();   
	                    parameters['table'] = $("#table").val();   
	                    parameters['id'] = $(this).val();
	                    
	                    blockElement('#history_editor_div');
	                    
	                    $.post( base_url + 'global/ajaxwork/view_history', parameters, function( data ){
	                        
	                        $('.history_popup textarea.editor').ckeditor(function(){    
	                            
	                            var element_id = this.element.$.id;
	                            var editor = $('#'+element_id).ckeditorGet();

	                            editor.setData(data.html);
	                            
	                            CKEDITOR.instances.history_editor.readOnly( true );
	                            
	                        });   
	                        
	                        unblockElement('#history_editor_div');
	                        
	                    }, "json");
	                });
	                
	                $('.history_popup #button').click(function(){
	                    
	                    if(confirm("Are you sure you want to rollback?"))
	                    {
	                        var parameters = {};
	                        parameters['type'] = 3;
	                        parameters['history_type'] = $(".history_popup #history_type").val();   
	                        parameters['table'] = $("#table").val();   
	                        parameters['id'] = $('.history_popup #history_time').val();
	                        parameters['content'] = CKEDITOR.instances.history_editor.getData();
	                        
	                        if(parameters['id'] != "")
	                        {
	                            $.post( base_url + 'global/ajaxwork/view_history', parameters, function( data ){
	                                
	                                if(data.error == "1")
	                                {
	                                    blockElement('#history_popup');
	                                    $('.w_close').click();     
	                                    window.location = window.location.href;
	                                    
	                                    //window.location.reload(true);                            
	                                }   
	                                else
	                                    alert("There was an error saving your changes. Please try again later.");    
	                            },"json");
	                        }
	                        else
	                            alert("Please select a time");
	                    }
	                });
            });
            
            
            
            
                      
        }, "json");
              
      });      
      
});

function initCkeditor( width_ckeditor )
{
    if ( (typeof width_ckeditor == "undefined") || (width_ckeditor == 'auto'))
    {
        $('textarea.editor').ckeditor(function(){
            /*
            var element_id = this.element.$.id;
            var context = $('#cke_'+element_id);
            */
            
            /*$("a[title='Image']",context).attr("onclick","");
            $("a[title='Image']",context).click(function(){
                
                var editor = $('#'+element_id).ckeditorGet();
                ckeditorAreaImageClicked(editor, $("#"+element_id));           
               
            });*/     
             
         }); 
    }
    else
    {
        $('textarea.editor').ckeditor({width: width_ckeditor});
    }
}
/*function ckeditorAreaImageClicked(editor, textarea)
{
    
    var parameters = {};
    parameters['type'] = 7;
    
    blockObject(textarea);
    
    $.post(base_url+'/filemanager/ajaxwork',parameters,function(data){
              
            unblockObject(textarea);
            var width = $(document).width();
            var elementWidth = 950;
            var elementHeight = 610;

            if ($('#fileManagerPopup').length > 0)
            {
            
                $("#fileManagerPopup").block
                ({
                    message: data.html,
                    css: { cursor: 'normal', top: '100px', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2) },
                    overlayCSS: { cursor: 'normal' },
                    centerX: true,
                    centerY: true

                });
                
                $('#fileManagerPopup').css("position","absolute");
                $('#fileManagerPopup').css("top","20px");
                $('#fileManagerPopup').css("z-index","99999");
                $(".scroll-pane").jScrollPane(); 
            
            }
            else
            {
                $.blockUI
                ({
                    message: data.html,
                    css: { cursor: 'normal', top: '100px', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2) },
                    overlayCSS: { cursor: 'normal' },
                    centerX: true,
                    centerY: true

                });
                
                $(".scroll-pane").jScrollPane(); 
            }
                
                
           $("#filemanager_upload_file").makeAsyncUploader({
                    upload_url: base_url +'filemanager/ajaxwork',
                    flash_url: base_url + 'flash/admin/swfupload.swf',
                    button_image_url: base_url + 'images/admin/selectsave.png',
                    button_text: '',
                    file_size_limit : '20 MB',
                    beforeUpload: 'filemanager_beforeUploadStart()',
                    afterUpload: 'filemanager_afterUploadFinish()'
                    
            });
  
            $('.w_close').click(function(e) {
                    e.preventDefault();
                    
                    if ($('#fileManagerPopup').length > 0)
                    {
                        $("#fileManagerPopup").unblock();
                    }
                    else
                        $(document).unblock();
            
            });

            // When the user changes the width of the image manually,
            // recalculate the height according to the aspect ratio.
			$("#width").change(function() 
			{
				if(ratio > 0)
				{
				
					var width = $("#width").val();
					$("#height").val(Math.floor(width / ratio));
				}
			}); 
			
            // When the user changes the height of the image manually,
            // recalculate the width according to the aspect ratio.
			$("#height").change(function() 
			{
				if(ratio > 0)
				{
				
					var height = $("#height").val();
					$("#width").val(Math.floor(height * ratio));
				}
			});			           
            
            
            $('.select').live('click',function(e)
            {    
                var href = $(this).attr("href");
                var folder = $('#filemanager_folders').val();
			    var file_size = $(this).attr("title");
			   
			    if(file_size != "")
			    {
			    	file_size = file_size.replace(" ", "");
			    	file_size = file_size.replace("(", "");
			    	file_size = file_size.replace(")", "");
			    	
			    	var pos = file_size.indexOf("x");
			    	if(pos > 0)
			    	{
						var width = file_size.substring(0, pos);	
						var height = file_size.substring(pos + 1);
						
						$("#width").val(width);
						$("#height").val(height);
						ratio = width / height;
						
						if(width > 480)
						{
							alert("This image may be too wide to fit into the content area.  Consider resizing the image, or at least setting the width manually to 480 below.");
						}
			    	}
			    }
                   
                
                $('#src').val(folder+'/'+href);
                
                e.preventDefault(); 
                
                $("#src").focus();
                
            });
            
            $('#insert').click(function(){
               
                    
                    var imagehtml = "";
                    
                    var src = base_url + 'files/'+ $('#src').val();
                    
                    if (src == "")
                    {
                        alert('Please select an image.');
                        return;
                    }
                    
                    var alt = $('#alt').val();
                    var height = $('#height').val();
                    var width = $('#width').val();
                    
                    imagehtml = '<img src="'+src+'" ';
                    
                    if (alt!="") imagehtml = imagehtml + ' alt="' + alt + '" ';
                    if (height!="") imagehtml = imagehtml + ' height="' + height + '" ';
                    if (width!="") imagehtml = imagehtml + ' width="' + width + '" ';
                    
                    imagehtml = imagehtml + ' border="0"' +  "/>";
                                      
                    var imageElement = CKEDITOR.dom.element.createFromHtml( imagehtml);
                    editor.insertElement(imageElement);
                   //editor.insertHtml(imagehtml);                                                                
                    
                    if ($('#fileManagerPopup').length > 0)
                    {
                        $("#fileManagerPopup").unblock();
                    }
                    else
                        $(document).unblock();
                
            });
            
            $("#filemanager_folders").change(function() {
    
                var selected_folder = $("option:selected", this).val();
                
                filemanager_refreshFolderFiles(selected_folder);
                
           
            });
            
            sIFR.replace(cg, {
                selector: 'h2'
                    ,css: [
                        '.sIFR-root { text-align: left; font-weight: normal; font-size: 16px; color: #272228; margin: 0; padding: 0;}'
                        ], wmode: 'transparent'
                        });   
                                                                             
    },"json");
  
}*/