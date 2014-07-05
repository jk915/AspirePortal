    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/style_pagination.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/jquery.fancybox.css" />
    <!--<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/property.css" />-->
    
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/propertyManager.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/pagination.js"></script> 
    <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.paginate.js"></script>
	
    <link rel="stylesheet" media="screen" href="http://portalqa.aspirenetwork.net.au/css/member/reveal.css" />
        
    
    </script>
    <link rel="stylesheet" media="screen" href="http://portalqa.aspirenetwork.net.au/css/member/stocklist.css" />
    <script src="http://maps.google.com/maps/api/js?sensor=true" type="text/javascript"></script>
	
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
            if (this.rel=='csv') {
                $('#generate_csv').show();
            } else {
                
                $('#generate_csv').hide();
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
