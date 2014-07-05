    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>css/member/stocklist.css" />
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
    var min_house = <?=$property_data->min_house; ?>;
    var max_house = <?=$property_data->max_house; ?>;    
    var min_land = <?=$property_data->min_land; ?>;
    var max_land = <?=$property_data->max_land; ?>;
    var min_yield = <?=number_format($property_data->min_yield, 1, ".", ""); ?>;
    var max_yield = <?=number_format($property_data->max_yield, 1, ".", ""); ?>;    
    </script>
    <?php endif; ?>
    <script type="text/javascript">
    $(function(){
        var genDoc = function() {
            $('#tempForm').html('');
            $('#generateForm :input').clone().appendTo('#tempForm');
            $('#frmSearch input').clone().appendTo('#tempForm');
            //$('#frmSearch select').clone().appendTo('#tempForm');
            
            // For some reason using the :input selector, select VALUES were not being
            // copied to the temp form.  Manually appending them here.
            $('#frmSearch select').each(function() {
                var name = $(this).attr("name");
                var val = $(this).val();

                if(val != "") {
                    $('<input type="hidden" name="' + name + '" value="' + val + '" />').appendTo("#tempForm");
                }
            });
            
            $('#tempForm').attr("target", '_blank');
            
            $('#tempForm').submit();
        };
        $('a.revealcolumns').live('click',function(){
            if (this.rel=='pdf') {
                $('#generate_pdf').show();
                $('#generate_csv').hide();
            } else {
                $('#generate_pdf').hide();
                $('#generate_csv').show();
            }
            $('#columns').reveal();
        });
        $('#generateForm').live('submit',function(e){
            e.preventDefault();
            genDoc();
        });
        $('#generate_csv').live('click',function(){
            $('#generateForm :input[name="type"]').val('csv');
            genDoc();
        });
    });
    </script>
</head>
