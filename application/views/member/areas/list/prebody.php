    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>css/member/stocklist.css" />
    <script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
    
    <?php if($area_data) : ?>
    <script type="text/javascript">
    var min_total_price = <?=$area_data->min_median_house_price; ?>;
    var max_total_price = <?=$area_data->max_median_house_price; ?>;  
    </script>
    <?php endif; ?>
</head>
