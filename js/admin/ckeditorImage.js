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
    initCkeditor();
    
    // Handle the event when the user clicks on the VIEW PREVIEW button.
    $('.view_preview').click(function(e) 
    {
        e.preventDefault();	// Prevent href click action
        
        // Get the id of the ckeditor area
        var code = $(this).attr("href").replace("preview-", "");
        var input_id = "wysiwyg_" + code;

		// Get a handle to the ckeditor instance        
        var oEditor = CKEDITOR.instances[input_id];
        
        if(!oEditor)
        {
        	oEditor = CKEDITOR.instances.wysiwyg;
        	
        	if(!oEditor)
        	{
        		alert("Sorry, we couldn't get a handle on the WYSIWYG editor'");
    			return;
			}
		}
		
		// Get the HTML value of the editor.
		var html = oEditor.getData();
		
		if(html.length < 10)
		{
			alert("Sorry, there doesn't appear to be anything to preview?");
			return;
		}
		
		// Post the HTML to the server
		var article_id = $('#article_code').val();
        var parameters = {};
        parameters['type'] = 1;
        parameters['html'] = html;
        parameters['article_id'] = article_id;

        $.post( base_url + 'admin/preview/ajaxwork', parameters, function( data )
        {
        	if(data.message == "OK")
        	{
				// Launch the preview window
        		var url = "";
        		if( code == "page_body" )
        			url = base_url + "admin/preview/index";
        		else if( code == "block_content" )
        			url = base_url + "admin/preview/index";        			
        		else
        			url = base_url + "admin/preview/article";
        		
				window.open (url, "Preview", "menubar=1,resizable=1,width=1024,height=700,scrollbars=1"); 				
        	}
		}, "json");

	});    
    
    $('.view_history').click(function(e) {
      
        e.preventDefault();
       
        var parameters = {};
        parameters['type'] = 16;
        parameters['table'] = $("#table").val();
        parameters['history_type'] = $(this).attr("href");
        parameters['id'] = $("#article_id").val();   
        
        $.post( base_url + 'admin/contentmanager/ajaxwork', parameters, function( data )
        {
            var width = $(document).width();
            var elementWidth = 980;
            var elementHeight = 500;
            var scroll_array = getScrollXY();
            var top = scroll_array[1] + 20;
            
            $.blockUI
            ({
                message: data.html,
                css: { cursor: 'normal', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2), top: top + "px" },
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
	            
	            $('.history_popup #history_time').change( function(){
	                
	                var parameters = {};
	                parameters['type'] = 17;
	                parameters['history_type'] = $(".history_popup #history_type").val();   
	                parameters['table'] = $("#table").val();   
	                parameters['id'] = $(this).val();
	
	                blockElement('#history_editor_div');
	                
	                $.post( base_url + 'admin/contentmanager/ajaxwork', parameters, function( data ){
	                    
	                    $('.history_popup textarea.editor').ckeditor(function(){    
	                        
	                        var element_id = this.element.$.id;                        
	                        var editor = $('#'+element_id).ckeditorGet();
	                        editor.setData(data.html);
	                        
	                        //CKEDITOR.instances.history_editor.readOnly( true );
	                    
	                    });   
	                    
	                    unblockElement('#history_editor_div');
	                    
	                }, "json");
	            });
	            
	            $('.history_popup #button').click( function(){
	                
	                if(confirm("Are you sure you want to rollback?"))
	                {
	                    var parameters = {};
	                    parameters['type'] = 18;
	                    parameters['history_type'] = $(".history_popup #history_type").val();   
	                    parameters['table'] = $("#table").val();   
	                    parameters['id'] = $('.history_popup #history_time').val();
	                    //parameters['content'] = CKEDITOR.instances.history_editor.getData();
	                    
	                    if(parameters['id'] != "")
	                    {
	                    	$.post( base_url + 'admin/contentmanager/ajaxwork', parameters, function( data )
	                    	{
	                            
	                        	if(data.error == "1")
	                            {
	                                blockElement('#history_popup');
	                                $('.w_close').click();     
	                                window.location = window.location.href;
	                                
	                                //window.location.reload(true);                            
	                            }   
	                            else
	                                alert("There was an error saving your changes. Please try again later.");    
	                        }, "json");
	                    }
	                    else
	                        alert("Please select a time");
	                }
	            });
            
            });
                      
        }, "json");
              
      });      
      
});

function initCkeditor()
{
    $('textarea.editor').ckeditor(function(){
     
        var element_id = this.element.$.id;
        var context = $('#cke_'+element_id);
        
        
        $("a[title='Image']",context).attr("onclick","");
        $("a[title='Image']",context).click(function(){
            
            var editor = $('#'+element_id).ckeditorGet();
            ckeditorAreaImageClicked(editor, $("#"+element_id));           
           
        });     
         
     });
     
     // AC FIX FOR FIREFOX 11+ SECOND EDITOR NOT SHOWING CONTENTS
     // We click the source button twice after a small delay to correct the problem.
     setTimeout(function()
     {
        $("a.cke_button_source:eq(1)").each(function()
        {
            var secondEditor = this;
            $(secondEditor).click();
            
            setTimeout(function()
            {
                $(secondEditor).click();    
            }, 1500);
        });
     }, 700);
      
}

function ckeditorAreaImageClicked(editor, textarea)
{
    
    var parameters = {};
    parameters['type'] = 7;
    
    blockObject(textarea);
    
    $.post(base_url+'/admin/filemanager/ajaxwork',parameters,function(data){
              
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
                    upload_url: base_url +'admin/filemanager/ajaxwork',
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
    
}