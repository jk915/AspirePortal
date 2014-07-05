    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>css/member/stocklist.css" />
    <script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
    
    <?php if($project_data) : ?>
    <script type="text/javascript">
    var min_total_price = <?=$project_data->min_total_price; ?>;
    var max_total_price = <?=$project_data->max_total_price; ?>;  
    </script>
    <?php endif; ?>
</head>
