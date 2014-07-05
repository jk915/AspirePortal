   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/tabs-no-images.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/fileSelector.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/jscrollpane.css" />
        
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/assignBlocks.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/articlecategory.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.validate.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.js"></script>   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.cookies.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery-asyncUpload-0.1.js"></script>  
   <!-- ckeditor -->
   <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/adapters/jquery.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/ckeditorImage.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileManager.js"></script>    
   <!--  end ckeditor -->   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/main.js"></script>   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script>

   <!-- fileSelector -->
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileSelector.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.jscrollpane.js"></script>

   <style type="text/css">
    input[type="button"]
    {
        width: 50px;
    }
   </style>
   
   <script type="text/javascript">
   $(document).ready(function() 
   {
		// If the user has just tabbed out of the name field,
		// see if the category_code field is blank.   If it is, create an category code
		// for the user automagically.
		$("#name").bind('blur',function()
		{
			// Make sure the current code is blank
			var current_code = $("#category_code").val();

			if(current_code != "")
				return;
				
			// Get the article title
            var code = $("#name").val();
            if(code == "")
            	return;	

            // Replace spaces, underscores, punctuation etc.
   			code = code.toLowerCase();	// Covert code to upper case
			code = code.replace(/[ ,_]/g, "-");	// Replace spaces with dashes
			code = code.replace(/[\.;!'&%@\(\)]/g, "");	// Replace other punctionation with nothing
			code = code.replace(/--/g, "-");	// Replace double dashses with a single dash
			code = code.replace(/-$/g, "");	// If there's a dash at the end, kill it.
			
			// Set the code.
			$("#category_code").val(code);
		});	   
   })
   </script>

</head>