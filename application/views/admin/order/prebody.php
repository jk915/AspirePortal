</head>
 <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script>  
<script type="text/javascript">

$(".download").live("click",function(e){
        
        var href = $(this).attr("title");
        var parameters = {};
        e.preventDefault();
        
        if(href != "")
        {
            var parameters = {};
            parameters['type'] = 6;
            parameters['file'] = href;        
           
            $.download(base_url + 'ordermanager/ajaxwork',parameters);     
        }
        else
            alert("No pdf generated or the pdf field is empty.")
        
    });

</script>