   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/style_pagination.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/tabs-no-images.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/datePicker.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/jscrollpane.css" />
   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileManager.js"></script>
   
   <!-- ckeditor -->
   <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/adapters/jquery.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/ckeditorImage.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileManager.js"></script>    
   <!--  end ckeditor -->   
   <!-- fileSelector -->   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileSelector.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.jscrollpane.js"></script>   
   <!-- end -->
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/date.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.datePicker.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/product.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.validate.js"></script>   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.js"></script>   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery-asyncUpload-0.1.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.numeric.pack.js"></script> 
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.cookies.js"></script>  
   <!-- edit in place -->
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.editinplace.js"></script>
   
   <script type="text/javascript">
   $(document).ready(function() 
   {
        // If the user has just tabbed out of the product_name field,
        // see if the model_number field is blank.   If it is, create a model_number
        // for the user automagically.
        $("#product_name").bind('blur',function()
        {
            // Make sure the current code is blank
            var current_code = $("#model_number").val();

            if(current_code != "")
                return;
                
            // Get the article title
            var code = $("#product_name").val();
            if(code == "")
                return;    

            // Replace spaces, underscores, punctuation etc.
            code = code.toLowerCase();    // Covert code to upper case
            code = code.replace(/[ ,_]/g, "-");    // Replace spaces with dashes
            code = code.replace(/[^a-z0-9-]/g, "");    // Replace other punctionation with nothing
            code = code.replace(/--/g, "-");    // Replace double dashses with a single dash
            code = code.replace(/-$/g, "");    // If there's a dash at the end, kill it.
            
            // Set the code.
            $("#model_number").val(code);
        });       
   })
   </script>      
   
   
   <style type="text/css">
   
   		#bracket_fieldset
   		{
   			float: right;
   			border: 1px solid;
   			margin-top: 0px;
   		}
   		#bracket_fieldset input,
   		#bracket_fieldset select
   		{
   			width: 220px;
   		}
   		
   		#bracket_fieldset input[type="text"]
   		{
   			padding-left: 0px;
   			padding-right: 0px;
   			width: 218px;
   		}
   		
   		td select
   		{
   			width: 120px;
   		}
   
   		.small_box
   		{
   			width: 30px;
   		}
        
        #serial_number
        {
            margin-top: 22px;
            margin-left: 10px;
            float: left;
        }
   </style>
   
           
</head>