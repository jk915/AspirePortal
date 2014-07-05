    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/tabs.css" />   
    <!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/nivo-slider.css" /> -->
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/galleryModal.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/my_account.css" />    
    
    <script type="text/javascript" src="<?php echo base_url(); ?>js/ui.core.js" ></script>      
    <script type="text/javascript" src="<?php echo base_url(); ?>js/ui.tabs.min.js" ></script>    
    <!--<script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.nivo.slider.pack.js" ></script>     -->
    <script type="text/javascript" src="<?php echo base_url(); ?>js/jquery.prettyPhoto.js" ></script>  
    <script type="text/javascript" src="<?php echo base_url(); ?>js/ui.accordion.js" ></script>    
    
    <script type="text/javascript" src="<?php echo base_url(); ?>js/cufon-yui.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/futLT_400-futLT_800.font.js"></script>
            
    
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
           /* Cufon.replace('h1');
            Cufon.replace('h2');
            Cufon.replace('h3');                        
            Cufon.replace('h4');  
            Cufon.replace('th');        */     
         
            $("#tabs").tabs();
            
            /*$('#slider').nivoSlider({
                pauseTime: 4000,
                controlNavThumbs: false
            });*/
            
            $("a[rel^='prettyPhoto']").prettyPhoto({
                    theme:'light_rounded'
                    ,overlay_gallery: false
                    ,show_title: true
            });            
            
            
           $(".accordion").accordion(
           {
              collapsible: true, 
              active: false, 
              autoHeight: false,   
              header: "h5"  
           });

           /*var number = $('#accordion_tab').val();
		   $(".accordion").accordion( "activate" , parseInt(number) );*/
           
            // Twitter animations
            $("#twitter").hover(
                function(){
                    $("#twitter_bird").animate({
                        "top": "-7px"
                    }, { duration: 200, queue: false });
                    $("#twitter_shadow").animate({
                        "width": "40px",
                        "left":"-7px",
                        "opacity": "0.5"
                    }, { duration: 200, queue: false });
                },
                function(){
                    $("#twitter_bird").animate({
                        "top": "0px"
                    }, { duration: 200, queue: false });
                    $("#twitter_shadow").animate({
                        "width": "27px",
                        "left":"0px",
                        "opacity": "1"
                    }, { duration: 200, queue: false });
                }
            );
            // RSS animations
            $("#rss").hover(
                function(){
                    $("#rss_icon").animate({
                        "top": "-7px"
                    }, { duration: 200, queue: false });
                    $("#rss_shadow").animate({
                        "width": "40px",
                        "left":"-4px",
                        "opacity": "0.5"
                    }, { duration: 200, queue: false });
                },
                function(){
                    $("#rss_icon").animate({
                        "top": "0px"
                    }, { duration: 200, queue: false });
                    $("#rss_shadow").animate({
                        "width": "27px",
                        "left":"3px",
                        "opacity": "1"
                    }, { duration: 200, queue: false });
                }
            );
        } ); 
    </script> 
</head>