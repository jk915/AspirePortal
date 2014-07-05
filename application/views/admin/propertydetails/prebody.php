   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>css/listings.css" />  
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/property_details.css" />         
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>css/listingDetail.css" />  
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url();?>css/gallery.css" />      
   <link rel="stylesheet" type="text/css" media="print" href="<?php echo base_url();?>css/print.css" />    
   
   <script type="text/javascript" src="<?php echo base_url();?>js/pikachoose.js"></script>       
   <script type="text/javascript" src="<?php echo base_url();?>js/admin/download.js"></script>       
   
   <script type="text/javascript">
<!--
    $(document).ready(function (){
        
        $("#nav li:first").addClass("active");
        
        $("#pikachoose").PikaChoose({show_captions:false, slide_enabled:true, auto_play: true, thumb_width: '100', thumb_height: '75'});
        
        $(".download").live("click",function(e){
       
           
            e.preventDefault();
            
            var href = $(this).attr("href");
            
            var parameters = {};
            parameters['type'] = 4;
            parameters['file'] = href;
            
            $.download(base_url + 'property_listing/ajaxwork',parameters);     
            
        });  
        
        $("#reserve.big_button").live("click",function(e){
           
            if(confirm("Are you sure you want to reserve this property?"))
            {
                var parameters = {};
                parameters['type'] = 5;
                parameters['property_id'] = $("#property_id").val();        
                
                $.post(base_url + 'property_listing/ajaxwork', parameters,function(data){
                   
                    if (data.indexOf("Error")>=0)
                    {
                        alert(data);
                    }
                    else
                    {
                        $("#reserve span").html("Reserved!");         
                        $("#reserve").removeClass("big_button").addClass("big_button_clicked");                    
                        
                        window.location.href = base_url + "page/thank_you_for_reservation";
                    }
                });
            }
        });
    });
--></script>

    <style type="text/css">
    #main
    {
        width: 920px;
    }
    
    .big_button a
    {
        color: #fff !important;
        border-bottom: 0px !important;
    }
    
    </style>        
             
</head>