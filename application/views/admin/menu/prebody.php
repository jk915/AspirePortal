   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/grid_menu/jquery.bgiframe.js"></script>  
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/grid_menu/jquery.dimensions.js"></script>  
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/grid_menu/jquery.tools.min.js"></script>  
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/grid_menu/menu.js"></script>   
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/grid_menu/tooltip.css" />        
   
   
   <style type="text/css">
      table td:first-child
      {
         width: 200px;
      }
   </style>
   
   <script type="text/javascript">
   $(document).ready(function(){
       
            $(".toolTip").tooltip({
            
                // use single tooltip element for all tips
                tip: '#dynatip', 
                
                // tweak the position
                offset: [120, 150],
                
                // use "slide" effect
                effect: 'slide',
                
                
                
            // add dynamic plugin 
            }).dynamic( {
            
                // customized configuration on bottom edge
                bottom: {
                
                    // slide downwards
                    direction: 'down',
                    
                    // bounce back when closed
                    bounce: true
                }
            });
            
    });

   </script>
</head>