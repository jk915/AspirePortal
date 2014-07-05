   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/jHtmlArea.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
   
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/lead.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.validate.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/tools.tabs-1.0.4.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.blockUI.js"></script>
   <script type="text/javascript">
	$(document).ready(function() 
	{
        
        $('#country').change( function(){
            
            var parameters    = {};
            parameters['type']    = 4;
            parameters['country_id'] = $(this).val();
            
            $.post( base_url + "admin/leadsmanager/ajaxwork", parameters, function( data ){
                $('#state').html(data.html);
            },'json');
        } );
    });
    
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

   </style>
   
</head>
