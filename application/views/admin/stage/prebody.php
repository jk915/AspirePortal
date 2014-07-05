	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
	<!-- datePicker required styles -->
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/fileuploader.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/jquery.fancybox.css" />
    <!-- datePicker required styles -->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/datePicker.css" />
   
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileuploader.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/pagination.js"></script>   

	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/main.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.editinplace.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.datePicker.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/date.js"></script>

	<!--[if IE]><script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.bgiframe.min.js"></script><![endif]-->   
	
	<script type="text/javascript">
	$(document).ready(function() {
        $("ul.skin2").tabs("div.skin2 > div");
        
        Date.format = 'dd/mm/yyyy';

       $('.date-choose').datePicker({startDate:'01/01/2012'});
        
        $('#addnotebtn').click(function(){
            $.fancybox({
                'href' : '#formnewcomment',
            });
        });
        
        $('#savenotebtn').live('click',function(){
            var $t = $(this);
            var comment = $('#comment').val();
            var date = $('#note_date').val();
            if (comment=='') {
                alert('Note is required');
            } else if (date == '') {
                alert('Date is required');
            } else {
                var view = '';
                $("input[name='view[]']:checked").each(function(i) {
                	view +=$(this).val()+",";
                });
                $t.val('Please wait...').attr('disabled',true);
                $.post(base_url + 'admin/propertymanager/ajaxwork', {
                    comment: comment,
                    note_date: date,
                    type: 28,
                    view: view,
                    getlist: 1,
                    property_id: $('[name="property_id"]').val()
                }, function(rs){
                    $t.val('Save').attr('disabled',false);
                    $.fancybox.close();
                    $('#notelist').html(rs);
                });
            }
        });
        
        $('.date-pick').datePicker({startDate:'01/01/1987'});

        $("#tabGallery").click(function()
    	{
    		// Setup hero image uploader
    		var gUploader = new qq.FileUploader(
    		{
    			// pass the dom node (ex. $(selector)[0] for jQuery users)
    			element: document.getElementById('upload_file'),
    			// path to server-side upload script
    			action: base_url + 'admin/propertymanager/stage_upload_file/gallery_image',
    		    params: {
                    "stage_id" : $("#stage_id").val()
    		    },
    		    allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
    		    sizeLimit: 5100000, // max size 
    		    onComplete: function(id, fileName, responseJSON)
    		    {
        			if(responseJSON.success) {
    					// The upload completed successfully.
    					refreshStageFiles('images');
        			}
    		    }
    		});
    	});
    	
        $("#tabDocument").click(function()
    	{
    	    // Setup hero image uploader
    		var gUploader = new qq.FileUploader(
    		{
    			// pass the dom node (ex. $(selector)[0] for jQuery users)
    			element: document.getElementById('upload_document'),
    			// path to server-side upload script
    			action: base_url + 'admin/propertymanager/stage_upload_file/documents',
    		    params: {
                    "stage_id" : $("#stage_id").val()
    		    },
    		    sizeLimit: 15100000, // max size 
    		    onComplete: function(id, fileName, responseJSON)
    		    {
        			if(responseJSON.success) {
    					// The upload completed successfully.
    					refreshStageFiles('documents');
        			}
    		    }
    		});			
    	});
    	
    	$("#delete_files").live('click',function(){
        
            if ($(":checkbox[name='stage_imagestodelete[]']:checked").length == 0) {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }
            
            if (confirm("Are you sure you want to delete the selected files?")) {
                
                var selectedvalues = "";
                $(":checkbox[name='stage_imagestodelete[]']:checked").each(function(){
                    selectedvalues += $(this).val() +';';
                });
                
                var parameters = {};
                
                parameters['type'] = 21;
                parameters['todelete'] = selectedvalues;
                parameters['stage_id'] = $("#stage_id").val();
                parameters['document_type'] = 'images';
                
                blockElement("#files_listing");
                
                $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
                
                    unblockElement("#files_listing");
                    refreshStageFiles('images');
                    $('.qq-upload-list').hide();
                });
            }
            
        });
        
        $("#delete_doc_files").live('click',function(){
        
            if ($(":checkbox[name='stage_docstodelete[]']:checked").length == 0) {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }
            
            if (confirm("Are you sure you want to delete the selected files?")) {
                
                var selectedvalues = "";
                $(":checkbox[name='stage_docstodelete[]']:checked").each(function(){
                    selectedvalues += $(this).val() +';';
                });
                
                var parameters = {};
                
                parameters['type'] = 21;
                parameters['todelete'] = selectedvalues;
                parameters['stage_id'] = $("#stage_id").val();
                parameters['document_type'] = 'documents';
                
                blockElement("#documents_listing");
                
                $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
                
                    unblockElement("#documents_listing");
                    refreshStageFiles('documents');
                    $('.qq-upload-list').hide();
                });
            }
            
        });
        
        $(".download_stage").live("click",function(e){
        
            var href = $(this).attr("href");
            var document_type = $(this).attr("type");
            
            var paremeters = {};
            paremeters['type'] = 22;
            paremeters['file'] = href;
            paremeters['document_type'] = document_type;
            paremeters['stage_id'] = $('#stage_id').val();  
            
            $.download(base_url + 'admin/propertymanager/ajaxwork',paremeters);     
            
            e.preventDefault();
            
        });
        
        $(".del_path").live('click',function(){
      
           var str = $(this).attr("id");
           var doc_id = str.replace("delete_doc_", "");
           $('.qq-upload-list').hide();
           delete_document_path(doc_id);
           
           $("#docpath_" + doc_id).html("");
           $(this).hide();
           
        });
        
        edit_in_place();
    	
	});
	
	function delete_document_path(doc_id)
    {
        var parameters = {};
        parameters['type'] = 23;
        parameters['doc_id'] = doc_id;
        
        blockElement('.document_' + doc_id);
        
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            
            unblockElement('.document_' + data.doc_id);    
            $('#doc_upload_file_'+ data.doc_id).show();
                  
        },"json");
    }
    
    function refreshStageFiles(document_type)
    {
        var parameters = {};
        parameters['type'] = 24;
        parameters['stage_id'] = $('#stage_id').val();
        parameters['document_type'] = document_type;
        blockElement("#files_listing");
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            unblockElement("#files_listing");
            if (data) {
                if (document_type == 'images') {
                    $('#page_listing').html(data);
                } else {
                    $('#page_doc_listing').html(data);
                }
            } else {
                alert('Can not select stages.');
            }
        });
    }
    
    function edit_in_place()
    {
        $(".edit_alt").editInPlace({
            url: base_url + "admin/propertymanager/ajaxwork",
            default_text: "(Click here to add caption text)",
            params: "type=14"
        });   
    }
    
	</script>
    
    <style type="text/css">
        .upload_documents object
        {
            width: none !important;
        }
        .upload_documents .doc_name
        {
            font-weight: bold;
        }
        
        .upload_documents > div
        {
            height: 50px;
            margin-top: 10px;
            padding-right:30px;
        }
        
        .upload_documents span
        {
            max-width: 150px;
        }
        
        .upload_documents .line
        {
            height: 2px;
            background-color: #F4F4F4;
            clear: both;
        }
        
        .btn-select-save 
        {
            background: url(../../images/admin/btnselectsave.png) top left no-repeat; 
        }
        .hide
        {
            margin-right:10px !important;
            float:right;
        }
        .hide_text
        {
            margin-right:140px !important;
            float:right;
        }
        select
        {
            width:270px;
        }
        .hide_text_project
        {
             float:right;
             margin-right:20px;
        }
    </style>
        
</head>