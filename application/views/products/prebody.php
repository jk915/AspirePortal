    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/tabs.css" />   
    <!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/nivo-slider.css" /> -->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/galleryModal.css" />    
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/galleriffic.css" /> 
        
    <script type="text/javascript" src="<?php echo base_url(); ?>js/ui.core.js" ></script>      
    <script type="text/javascript" src="<?php echo base_url(); ?>js/ui.tabs.min.js" ></script>    
    <!--<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.nivo.slider.pack.js" ></script>   
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.prettyPhoto.js" ></script>    -->
    <script type="text/javascript" src="<?php echo base_url(); ?>js/ui.accordion.js" ></script> 
    
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.galleriffic.js"></script>   
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.opacityrollover.js"></script>      
    
    <script type="text/javascript" src="<?php echo base_url(); ?>js/cufon-yui.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/futLT_400-futLT_800.font.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/product.js"></script>
    
    <!--[if !IE 7]>
        <style type="text/css">
            #wrapper {display:table;height:100%}
        </style>
    <![endif]-->                
        
    
    <!--[if IE 7]>
        <link rel="stylesheet" type="text/css" media="screen" href="css/iefixes.css" />     
    <![endif]-->  
    
    <script type="text/javascript">  
        $(document).ready( function()
        {            	
        	// When a tab is clicked upon, append the tab # to the url so users can bookmark it.
        	$('#tabs ul li a').click(function () 
        	{
        		location.hash = $(this).attr('href');
        		
        		//window.scrollTo(0, 0);
        	});
        	
        	preselectTab();
        });

        function change_accordion( number )
        {
        	$(".accordion").accordion( "activate" , number );   
        } 
        
        function preselectTab()
        {
        	var url = document.location.href;
        	
        	// Find out if a tab has been preselected in the URL.
        	// If this is the case, the url will contain /[product_name]#tabs-[tabno]
        	// Get the last part of the url
        	var last_slash_pos = url.lastIndexOf("/");
        	
        	if(last_slash_pos > 0)
        	{
        		// Get the last part of the url, i.e. /[product_name]#tabs-[tabno]
				var last_segment = url.substring(last_slash_pos + 1);
				
				// Find the hash + tab selector if there is one.
				var hash_pos = last_segment.lastIndexOf("#"); 
				
				if(hash_pos > 0)
				{
					// Tab selector found.
					var tab_name = last_segment.substring(hash_pos + 1);
					
					var tabIndex = 0;
					
					if(tab_name == "screenshots")
						tabIndex = 1;
					else if(tab_name == "pricing")
						tabIndex = 2;						
					else if(tab_name == "oem")
						tabIndex = 3;	
					else if(tab_name == "downloads")
						tabIndex = 4;											
					
					// If the pricing tab doesn't exist and the user is looking at the OEM or downloads tabs, adjust accordingly.
					var element = document.getElementById("pricing");
					if((tabIndex > 2) && (element == null))
						tabIndex--;

					// Preselect the correct tab
					setTimeout('$("#tabs").tabs("select", ' + tabIndex + ');', 250);
				}
        	}			
        }
    </script>
</head>