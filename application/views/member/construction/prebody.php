<link rel="stylesheet" media="screen" href="<?php echo base_url()?>css/member/construction.css" />
<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/jquery.fancybox.css" />
<link rel="stylesheet" href="<?php echo base_url(); ?>css/member/datePicker.css">
<link rel="stylesheet" href="<?php echo base_url(); ?>css/member/reservation_calendar.css">
<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.fancybox.pack.js"></script>
	
	<script type="text/javascript">
	var make_note_default =function(){
            $('#comment').val('');
            $('#comment_id').val('');
       
       };
	   
    $('#newcomment').live('click',function(){
		make_note_default();
        $.fancybox({
            'href' : '#formnewcomment',
        });
    });        
           	    
    var refeshComments = function(){
        var property_id =  $("#property_id").val()
        var parameters = {};
        parameters['type'] = 29;
        parameters['property_id'] = property_id;
        $.get(base_url + 'construction/ajaxwork', parameters,function(data){
            if (data) {
                $('.commentlisting').html(data);    
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
            
        });
    };
    
    $('.savecomment').live('click',function(){

        var parameters = {};
        var comment = $('#comment').val();
        var property_id = $('#property_id').val();
        var comment_id = $('#comment_id').val();
       		
       
   		
		
        if (comment.length == 0) {
            alert('Note is required');
            return false;
        }
                        
        blockElement('.commentlisting');
        parameters['comment'] = comment;
        parameters['property_id'] = property_id;
        parameters['comment_id'] = comment_id;
        parameters['type'] = 28;
      
		
        $.get(base_url + 'construction/ajaxwork', parameters,function(data){
            unblockElement('.commentlisting');
            if (data == 'OK') {
                refeshComments();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });

    $('#deletecomment').live('click',function(){
        if ($(".commenttodelete:checked").length == 0) {
            alert('Please select at least one comment you want to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected comment(s)?")) {
            var selectedvalues = "";
            var aItems = [];
            $(".commenttodelete:checked").each(function(){
                var itemID = $(this).val();
                selectedvalues += itemID +';';
                aItems.push(itemID);
            });
            var parameters = {};
            parameters['type'] = 30;
            parameters['todelete'] = selectedvalues;
            $.post(base_url + 'construction/ajaxwork', parameters , function(data)
            {
                if(data == 'OK') {
                    refeshComments();
                    alert('Selected comment(s) was removed successfully.');
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            });
        }
    });

    $('.editComment').live('click',function(){
		make_note_default();	
        var comment_id = $(this).attr('rel');
        var parameters = {};
        blockElement(".commentlisting");;
        parameters['type'] = 31;
        parameters['comment_id'] = comment_id;
        $.post(base_url + 'construction/ajaxwork', parameters, function(data){
            unblockElement(".commentlisting");
            if (data.status == 'OK') {
                
                $('#comment').val(data.comment);
                $('#comment_id').val(data.comment_id);
				
                $.fancybox({
                    'href' : '#formnewcomment',
                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
	
	</script>
	
	<script src="http://portalqa.aspirenetwork.net.au/js/admin/jquery-1.4.2.min.js"></script>
	<script src="<?php echo base_url(); ?>js/admin/jquery.datePicker.js"></script>
	<script src="<?php echo base_url(); ?>js/admin/date.js"></script>
	<script type="text/javascript">
	$.noConflict();
	jQuery(document).ready(function($) {
		jQuery('.date-choose').datePicker({startDate:'01/01/2012'});
	});
	</script>
</head>
