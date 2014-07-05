   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/style_pagination.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/tabs-no-images.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/fileSelector.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/jscrollpane.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/fileuploader.css" />
   <!-- datePicker required styles -->
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/datePicker.css" />
   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/assignBlocks.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.validate.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.js"></script>   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.cookies.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery-asyncUpload-0.1.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileuploader.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileSelector.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.jscrollpane.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/main.js"></script>   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileSelector.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.datePicker.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/date.js"></script>
   
   <!-- ckeditor -->
   <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/adapters/jquery.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/ckeditorImage.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileManager.js"></script>    
   
   <style type="text/css">
        #upload_file
        {
            width:800px;
        }      
        
        #website_articles
        {
            height: 170px;
        }

        #rows
        {
            float: left;
            width: 320px;
        }

        #rows label
        {
            float: left;
            width: 130px;
        }

        #rows input
        {
            float: left;
        }
        .author
        {
            width: 130px;
            margin-left: 20px;
        }

        .hero_img
        {
            background-color: #F4F4F4;
        }

        .margin_left
        {
            margin-left: 20px!important;
        }
   
        #overlay_text
        {
            height: 250px;
        }
        
        .shorttxt
        {
			width: 150px !important;
        }
   </style>
   
	<script type="text/javascript">
	$(document).ready(function() 
	{
		// Enable the tabs
		$("ul.skin2").tabs("div.skin2 > div");
		
		// Handle the event when the user clicks on the gallery tab.
		$("#tabGallery").click(function()
		{
			var gUploader = new qq.FileUploader(
			{
				// pass the dom node (ex. $(selector)[0] for jQuery users)
				element: document.getElementById('gallery_upload_file'),
				// path to server-side upload script
				action: base_url + 'admin/contentmanager/upload_file/gallery_image',
			    params: {"article_id" : $("#article_id").val()},    
			    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
			    sizeLimit: 210000000, // max size 
			    onComplete: function(id, fileName, responseJSON)
			    {
        			if(responseJSON.success)
        			{
						// The upload completed successfully.
						refreshArticleFiles();
        			}
			    }
			});			
		});
		
		$('.date-pick').datePicker({startDate:'01/01/1987'});
		
		$(".gallery_description").bind("blur", function()
		{
			var doc_id = $(this).attr("title");
			var val = $(this).val();
			
			var params = {};
			params["id"] = doc_id;
			params["document_description"] = $(this).val();
			
			blockElement("#page_listing");
			
			$.post(base_url + "admin/contentmanager/update_document", params, function(data)
			{
				unblockElement("#page_listing");
				
				if(data.status != "OK")
				{
					alert("An error occured during the update.");
				}
			}, "json");
		});
		
		$(".gallery_link").bind("blur", function()
		{
			var doc_id = $(this).attr("title");
			var val = $(this).val();
			
			var params = {};
			params["id"] = doc_id;
			params["link"] = $(this).val();
			
			blockElement("#page_listing");
			
			$.post(base_url + "admin/contentmanager/update_document", params, function(data)
			{
				unblockElement("#page_listing");
				
				if(data.status != "OK")
				{
					alert("An error occured during the update. " + data.status);
				}				
			}, "json");
		});

		// Validation the form upon submission
		$('#frmArticle').validate();
		
		if ($('#hero_image_upload').html() != null) {
		    var hrUploader = new qq.FileUploader(
        	{
        		// pass the dom node (ex. $(selector)[0] for jQuery users)
        			element: document.getElementById('hero_image_upload'),
        			// path to server-side upload script
        			action: base_url + 'admin/contentmanager/upload_file/hero_image',
        		    params: {"article_id" : $("#article_id").val()},
        		    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        		    sizeLimit: 210000000, // max size 
        		    onComplete: function(id, fileName, responseJSON)
        		    {
            			if(responseJSON.success) {
                            $('#hero_image_upload').hide();
                            $('#logo_img').show();
                            $('.article_hero_img').html('<img id="logo_img" src="'+responseJSON.fileName+'" width="250"/>');
                            $('#delete_logo').show();
            			}
        		    }
             });
		}
		
		if ($('#attachment_upload').length > 0) {
		    var attachmentUploader = new qq.FileUploader(
        	{
        		// pass the dom node (ex. $(selector)[0] for jQuery users)
        			element: document.getElementById('attachment_upload'),
        			// path to server-side upload script
        			action: base_url + 'admin/contentmanager/upload_file/attachment',
        		    params: {"article_id" : $("#article_id").val()},
//        		    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
        		    sizeLimit: 21000000, // max size 
        		    onComplete: function(id, fileName, responseJSON)
        		    {
            			if(responseJSON.success) {
                            $('#attachment_upload').hide();
                            $('#attachmentlink').html('<a href="'+responseJSON.url+'">'+responseJSON.filename+'</a>')
                            $('#delete_attachment').show();
            			}
        		    }
             });
		}
		
		$("#video_upload_file").makeAsyncUploader(
		{
			upload_url: base_url +'admin/contentmanager/ajaxwork',
			flash_url: base_url + 'flash/admin/swfupload.swf',
			button_image_url: base_url + 'images/admin/selectsave.png',
			button_text: '',
			file_size_limit : '10 MB',
			beforeUpload: 'beforeVideoUpload("video_upload_file")',
			afterUpload: 'afterVideoUpload("video_upload_file")',
			debug : false
		});
		
		$("#delete_logo").live("click",function(e)
	    {    
	        if (confirm('Are you sure you want to delete the hero image for this article?')) { 
	            
	            var parameters = { };
			    parameters['type'] = 14;
			    parameters['article_id'] = $("#article_id").val();		
				
			    $.post(base_url + 'admin/contentmanager/ajaxwork', parameters,function(data){
			                                           
	               $('#logo_img').hide();
	               $('#hero_image_upload').show();
	               $('#delete_logo').hide();
	               $('#hide_btn').hide();
	               $('.qq-upload-list').hide();
	               
	            });
            }
		});
	     
		$("#delete_attachment").live("click",function(e)
	    {    
	        if (confirm('Are you sure you want to delete this document attachment for this article?')) { 
	            
	            var parameters = { };
			    parameters['type'] = 21;
			    parameters['article_id'] = $("#article_id").val();		
				
			    $.post(base_url + 'admin/contentmanager/ajaxwork', parameters,function(data){
	               $('#attachment_upload').show();
	               $('#delete_attachment').hide();
	               $('#attachmentlink').html('');
	               $('.qq-upload-list').hide();
	               
	            });
            }
		});
	     
	    $( '#hide_logo' ).click(function(){
	    	if( $( this ).attr( 'checked' ) )
	    	{
	    		$( '#logo_img' ).hide();
	    	}
	    	else
	    	{
	    		$( '#logo_img' ).show();
	    	}
	    });
		
		
		
		// If the user has just tabbed out of the article_title field,
		// see if the article_code field is blank.   If it is, create an article code
		// for the user automagically.
		$("#article_title").bind('blur',function()
		{
			// Make sure the current code is blank
			var current_code = $("#article_code").val();

			if(current_code != "")
				return;
				
			// Get the article title
            var code = $("#article_title").val();
            if(code == "")
            	return;	

            // Replace spaces, underscores, punctuation etc.
			code = code.toLowerCase();	// Covert code to upper case
   			code = code.replace(/[ ,_]/g, "-");	// Replace spaces with dashes
			code = code.replace(/[^a-z0-9-]/g, "");	// Replace other punctionation with nothing
			code = code.replace(/--/g, "-");	// Replace double dashses with a single dash
			code = code.replace(/-$/g, "");	// If there's a dash at the end, kill it.
			
			// Set the code.
			$("#article_code").val(code);
		});
		   
        
        $('#tableFiles a.moveup, a.movedown').live('click', function(e) {
        
            e.preventDefault();
            var direction = $(this).attr('href');
            var image_id = $(this).attr('id');
            
            if( image_id > 0 && image_id != 'undedfined' )
            {
                var parameters = {};
                parameters['type'] = 12;
                parameters['article_id'] = $( '#article_id' ).val();
                parameters['doc_id'] = image_id;
                parameters['direction'] = direction;
                
                var current_page = $('.jPag-current').html();
                parameters['current_page'] = current_page;
                
                blockElement('#page_listing');
                
                $.post(base_url + 'admin/contentmanager/ajaxwork', parameters, function(data){
                       
                    $('#page_listing').html(data.html);
                    
                    unblockElement("#page_listing");
                    
                    if (data.message != "ok")
                       showMessage(data.message);                     
                },
                "json");
            }
                  
            
        });
        <?php if((isset($article)) && ($article->hero_image == "")){ ?>
        	$("#async_hero_upload_file").show();
        <?php } else { ?>
        	$("#async_hero_upload_file").hide();
        <?php } ?>
        
        $("#delete_files").live('click',function()
        {
            if ($("input[name='imagestodelete[]']:checked",$('#files_listing')).length == 0)
            {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }

            if (confirm("Are you sure you want to delete the selected files?"))
            {

                var selectedvalues = "";
                $("input[name='imagestodelete[]']:checked",$('#files_listing')).each(function(){
                    selectedvalues += $(this).val() +';';
                });
                var parameters = {};

                parameters['type'] = 13;
                parameters['todelete'] = selectedvalues;
                parameters['article_id'] = $("#article_id").val();

                blockElement("#files_listing");

                $.post(base_url + 'admin/contentmanager/ajaxwork', parameters,function(data)
                {
                    unblockElement("#files_listing");              
                    refreshArticleFiles();
                });
            }
        });
        
        // Handle document save events
        $(".saveDoc").bind("click", function(e)
        {
			e.preventDefault();
			
			// Get the ID of the current document
			var saveID = $(this).attr("id");
			var docID = saveID.substring(saveID.length - 1);
			
			// Make sure all 3 fields for this docID have a value
			// Document Name
			if($("#doc" + docID + "_name").val() == "")
			{
				alert("You must enter a name for this document.");
				$("#doc" + docID + "_name").focus();
				return;
			}
			
			// Document Path
			if($("#doc" + docID + "_file").val() == "")
			{
				alert("You select a file for this document.");
				return;
			}
			
			// Prepare the params array
			var params = {};
			params["type"] = 19;
			params["order"] = docID;
			params["foreign_id"] = $("#article_id").val();
			params["document_type"] = "article_document";		
			params["document_name"] = $("#doc" + docID + "_name").val();	
			params["document_path"] = $("#doc" + docID + "_file").val();
			
			blockElement("#frmArticleDocs");
			
			// Save the icon
			var url = base_url + "admin/contentmanager/ajaxwork";
			$.post(url, params, function(data)
			{
				unblockElement("#frmArticleDocs");
				
				if(data.status != "OK")
				{
					alert(data.message);
				}
				else
				{
					alert("Thank you, the document was saved successfully");
				}
				
			}, "json");					
        })
	});


	function beforeArticleUpload()
	{
		var article_id = $("#article_id").val();
        
		swfu.addPostParam("type","11");
        swfu.addPostParam("article_id",article_id);
		
		return true;
	}
	
	function beforeVideoUpload(id)
	{
		var article_id = $("#article_id").val();
        var article_category_type = $("#parent_category_code").val();

		swfu_array[id].addPostParam("type","20");
		swfu_array[id].addPostParam("article_id", article_id);
        swfu_array[id].addPostParam("article_category_type", article_category_type);

		return true;
	}
	
	function afterArticleUpload()
	{
	    refreshArticleFiles();
	    return true;
	}
	
	function afterVideoUpload(id)
	{
	    alert('Upload completed!')
	    window.location.reload();
	    return true;
	}
	
	function refreshArticleFiles()
	{   
	    var parameters = { };
	    parameters['type'] = 12;
	    parameters['article_id'] = $("#article_id").val();

	    blockElement('#files_listing');
	    $.post(base_url + 'admin/contentmanager/ajaxwork', parameters,function(data){

	         $('#page_listing').html(data.html);
	         unblockElement('#files_listing');
	    },"json");

	    $('.ProgressBar').hide();
	    $('#upload_file_uploading').hide();
	    $('#product_upload_file_uploading').hide();
	    $('#SWFUpload_0').show();
	    $('#SWFUpload_0').css("width","");
	}

	function showMessage(message)
	{
	        if ($('#message').length == 0)
	        {
	            $('<p id="message"></p>').html(message).insertBefore($('#new_category_div'));
	        }
	        else
	        {
	            $('#message').html(message);
	        }
	}
	
	// This method deletes the hero image associated with this article.
	function deleteHero()
	{
		$("#hero-loader").fadeIn();
		
	    var parameters = { };
	    parameters['type'] = 14;
	    parameters['article_id'] = $("#article_id").val();		
		
	    $.post(base_url + 'admin/contentmanager/ajaxwork', parameters,function(data)
	    {
	    	// Hide the ajax loader
	    	$("#hero-loader").fadeOut(); 
	    	if(data.message == "OK")
	    	{
	    		// The image has been deleted - remove it from the UI.
				$("#hero_image").css("display", "none");	
	    	}
	    	else
	    	{
				alert("Sorry, the image could not be deleted");
	    	}
	    },"json");		
	}


   </script> 
</head>

