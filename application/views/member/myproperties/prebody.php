    <script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
    
    <?php if($property_data) : ?>
    <script type="text/javascript">
    var min_total_price = <?=$property_data->min_total_price; ?>;
    var max_total_price = <?=$property_data->max_total_price; ?>;
    var min_bedrooms = <?=$property_data->min_bedrooms; ?>;
    var max_bedrooms = <?=$property_data->max_bedrooms; ?>;
    var min_bathrooms = <?=$property_data->min_bathrooms; ?>;
    var max_bathrooms = <?=$property_data->max_bathrooms; ?>;
    var min_garage = <?=$property_data->min_garage; ?>;
    var max_garage = <?=$property_data->max_garage; ?>;
    var min_house = <?=$property_data->min_land; ?>;
    var max_house = <?=$property_data->max_land; ?>;    
    var min_land = <?=$property_data->min_land; ?>;
    var max_land = <?=$property_data->max_land; ?>;
    var min_yield = <?=number_format($property_data->min_yield, 1); ?>;
    var max_yield = <?=number_format($property_data->max_yield, 1); ?>;    
    </script>
    <?php endif; ?>
</head>
