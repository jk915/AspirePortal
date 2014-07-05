   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/fileuploader.css" />
    <link rel="stylesheet" media="all" type="text/css" href="http://code.jquery.com/ui/1.10.3/themes/smoothness/jquery-ui.css" />
  
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/user.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.validate.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/tools.tabs-1.0.4.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.blockUI.js"></script>
   
   <!--<script type="text/javascript" src="<?php //echo base_url(); ?>/js/admin/jquery-1.9.1.js"></script>-->
   <!--<script type="text/javascript" src="<?php //echo base_url(); ?>/js/admin/jquery-ui.js"></script>-->
  
   <?php if($user) : ?>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileuploader.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script>
   <?php endif; ?>
   <script type="text/javascript">
    jQuery.validator.addMethod("strongpass", function(value, element) {
        if(value.length < 6) {
            return false;    
        }
        
        var mustContainUpper = /[A-Z]/;
        var result = mustContainUpper.exec(value);
        if((result == null) || (result == "")) return false;
        
        var mustContain09 = /[0-9]/;
        result = mustContain09.exec(value);
        if((result == null) || (result == "")) return false; 
        
        return true;       
    }, "Min 6 characters plus at least 1 digit and 1 upper case letter");   
   
	$(document).ready(function() 
	{
	    <?php if($user): ?>
		$("#tabDocument").click(function()
    	{
    	    // Setup hero image uploader
    		var gUploader = new qq.FileUploader(
    		{
    			// pass the dom node (ex. $(selector)[0] for jQuery users)
    			element: document.getElementById('upload_document'),
    			// path to server-side upload script
    			action: base_url + 'admin/usermanager/upload_file/documents',
    		    params: {
                    "user_id" : $("#id").val()
    		    },
    		    sizeLimit: 2100000, // max size 
    		    onComplete: function(id, fileName, responseJSON)
    		    {
        			if(responseJSON.success) {
    					// The upload completed successfully.
    					refreshUserFiles();
        			}
    		    }
    		});			
    	});
		
		var lgUploader = new qq.FileUploader(
        {
            // pass the dom node (ex. $(selector)[0] for jQuery users)
            element: document.getElementById('logo_upload'),
            // path to server-side upload script
            action: base_url + 'admin/usermanager/upload_file/hero_image',
            params: {
              "user_id" : $("#id").val()
            },
            allowedExtensions: ['jpg', 'jpeg', 'png', 'gif'],
            sizeLimit: 2100000, // max size 
            onComplete: function(id, fileName, responseJSON)
            {
            	if(responseJSON.success) {
                    $('#logo_upload').hide();
                    $('#logo_img_upload').show();
                    $('.logo_img').html('<img id="logo_img_upload" src="'+responseJSON.fileName+'" width="250"/>');
                    $('#delete_logo').show();
            	}
            }
        });
        
        $("#delete_logo").live("click",function(e){
            if (confirm('Are you sure you want to delete the logo ?')) {
                var parameters = {};
                parameters['user_id'] = $('#id').val();
                parameters['type'] = 13;
                
                $.post(base_url + 'admin/usermanager/ajaxwork',parameters, function(){
                                               
                    $('#logo_img_upload').hide();
                    $('#logo_upload').show();
                    $('#delete_logo').hide();
                    $('.qq-upload-list').hide();
                   
                });
            }
        });
        
        $("#delete_user_files").live('click',function(){
        
            if ($(".user_docstodelete:checked").length == 0) {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }
            
            if (confirm("Are you sure you want to delete the selected files?")) {
                
                var selectedvalues = "";
                $(".user_docstodelete:checked").each(function(){
                    selectedvalues += $(this).val() +';';
                });
                
                var parameters = {};
                
                parameters['type'] = 15;
                parameters['todelete'] = selectedvalues;
                parameters['user_id'] = $("#id").val();
                
                blockElement("#files_listing");
                
                $.post(base_url + 'admin/usermanager/ajaxwork', parameters,function(data){
                
                    unblockElement("#files_listing");
                    refreshUserFiles();
                    $('.qq-upload-list').hide();
                });
            }
            
        });
        
        $(".download_userfile").live("click",function(e){
        
            var href = $(this).attr("href");
            var document_type = $(this).attr("type");
            
            var paremeters = {};
            paremeters['type'] = 14;
            paremeters['file'] = href;
            paremeters['document_type'] = document_type;
            paremeters['user_id'] = $('#id').val();  
            
            $.download(base_url + 'admin/usermanager/ajaxwork',paremeters);     
            
            e.preventDefault();
            
        });
        <?php endif; ?>
        
    });
    
	function refreshUserFiles()
	{   
	    var parameters = { };
	    parameters['type'] = 11;
	    parameters['user_id'] = $("#id").val();

	    blockElement('#files_listing');
	    $.post(base_url + 'admin/usermanager/ajaxwork', parameters,function(data){
            unblockElement('#files_listing');
            $('#page_listing').html(data);
	    });
	}
    
    
</script>


   <style type="text/css">
        #suburb, #postcode
        {
            width: 120px;
        }
        #product_markup
        {
            width: 130px;
        }

        #permission_tab .left label
        {
            width: 150px;
        }
        .top_margin
        {
            margin-top: 15px;
            padding-bottom: 10px;
        }

        .top_margin label
        {
            font-weight: normal;
        }
        #permission_tab
        {
            min-height: 300px;
        }
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
            height: 80px;
           /* width: 400px;*/
            margin-top: 20px;
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
        SPAN.asyncUploader OBJECT { left:0px; }
   </style>
</head>
