	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/style_pagination.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/datePicker.css" />
	<!-- bread Crumb -->
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/breadcrumb/Base.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/breadcrumb/BreadCrumb.css" />

	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/date.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.datePicker.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/productManager.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/pagination.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.blockUI.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.paginate.js"></script>   
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/fileManager.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery-asyncUpload-0.1.js"></script>
	<!-- bread Crumb -->
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.jBreadCrumb.1.1.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.easing.1.3.js"></script>
	<!-- end bread Crumb -->
   
   
    <script type="text/javascript"> 
    $(document).ready(function() 
    {
		// If the user has just tabbed out of the article_title field,
		// see if the article_code field is blank.   If it is, create an article code
		// for the user automagically.
		$("#new_category").bind('blur',function()
		{
			// Make sure the current code is blank
			var current_code = $("#new_category_code").val();

			if(current_code != "")
				return;
				
			// Get the article title
            var code = $("#new_category").val();
            if(code == "")
            	return;
            		
            //if(category_name != "")
            //	code = category_name + "-" + code;

            // Replace spaces, underscores, punctuation etc.
			code = code.toLowerCase();	// Covert code to upper case
   			code = code.replace(/[ ,_]/g, "-");	// Replace spaces with dashes
			code = code.replace(/[^a-z0-9-]/g, "");	// Replace other punctionation with nothing
			code = code.replace(/--/g, "-");	// Replace double dashses with a single dash
			code = code.replace(/-$/g, "");	// If there's a dash at the end, kill it.
			
			// Set the code.
			$("#new_category_code").val(code);
		});		
    })
    </script>   
   
</head>
