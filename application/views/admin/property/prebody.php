	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/jquery.fancybox.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
	<!-- datePicker required styles -->
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/datePicker.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/reservation_calendar.css" />
	<link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/fileuploader.css" />

	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.fancybox.pack.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.validate.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileuploader.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/pagination.js"></script>   
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.datePicker.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/date.js"></script>

	<!-- ckeditor -->
	<script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor.js"></script> 
	<script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/adapters/jquery.js"></script> 
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/ckeditorImage.js"></script> 
	<!--  end ckeditor -->   

	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/main.js"></script>         
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.editinplace.js"></script>

	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/property.js"></script>
	<!--[if IE]><script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery.bgiframe.min.js"></script><![endif]-->   
	
	<script type="text/javascript">
    Date.format = 'dd/mm/yyyy';
    
    var formatFloat = function(num) {
        if (num=='') return '';
        num = num.toString().replace(/\$|\,/g, '');
        if (isNaN(num)) num = '0';
        sign = (num == (num = Math.abs(num)));
        num = Math.floor(num * 100 + 0.50000000001);
        cents = num % 100;
        num = Math.floor(num / 100).toString();
        if (cents < 10) cents = '0' + cents;
        return (((sign) ? '' : '-') + num + '.' + cents);
    };
        
	$(document).ready(function() 
	{
	    $('textarea[name="page_body"]').keyup(function(){
	        var val = this.value;
	        if (val.length > 700) {
	            this.value = val.substr(0,700);
	            $('#remaining').html('0');
	        } else {
	            $('#remaining').html(700 - val.length);
	        }
	    });
        
	   //for Note tab
       $('.date-choose').datePicker({startDate:'01/01/2012'});   
       
       var make_note_default =function(){
            $('#comment').val('');
            $('#comment_id').val('');
            $('#note_date').val('');
            $('#advisor').attr('checked', 'checked');
            $('#partner').attr('checked', 'checked');
            $('#investor').attr('checked', false);
            $('#private_note').attr('checked', false);        
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
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
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
        var note_date = $('#note_date').val();
		var private_note = '';
        var view = '';
        $("input[name='view[]']:checked").each(function(i) {
        	view +=$(this).val()+",";
        }); 		
		
		if (document.getElementById('private_note').checked)
		{
			private_note = 1;
		}
		else
		{
			private_note = 0;
		}
		
        if (comment.length == 0) {
            alert('Note is required');
            return false;
        }
        if (note_date.length == 0) {
            alert('Date is required');
            return false;
        }
                
        blockElement('.commentlisting');
        parameters['comment'] = comment;
        parameters['property_id'] = property_id;
        parameters['comment_id'] = comment_id;
        parameters['note_date'] = note_date;
        parameters['type'] = 28;
        parameters['view'] = view;
		parameters['private_note'] = private_note;
		
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters,function(data){
            unblockElement('.commentlisting');
            if (data == 'OK') {
                refeshComments();
                
                $('.date-choose').dpClearSelected();
                
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
            $.post(base_url + 'admin/propertymanager/ajaxwork', parameters , function(data)
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
        $.post(base_url + 'admin/propertymanager/ajaxwork', parameters, function(data){
            unblockElement(".commentlisting");
            if (data.status == 'OK') {
                $('#note_date').val(data.note_date);
                $('#comment').val(data.comment);
                $('#comment_id').val(data.comment_id);
				
                if(data.advisor != "" )
                    $('#advisor').attr('checked', true);
                if(data.partner != "" )
                    $('#partner').attr('checked', true);
                if(data.investor != "" )
                    $('#investor').attr('checked', true);				
				
                $.fancybox({
                    'href' : '#formnewcomment',
                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
               
    var add_new = ($("#property_id").val() == "") ? true: false;    // Set to true if the user is adding a new listing.
    
    // Handle the event when the user is changing the property status
	$("#status").bind("change", function()
	{
		var status_id = $("#status").val();
		
		if((status_id == "pending") || (status_id == "reserved")) {
			// Show the reserved by div
			$("#divReservedBy").css("display", "block");
		} else {
			// Hide the reserved by div
			$("#divReservedBy").css("display", "none");
		}
	});
    
    if(add_new) {
        
        $("#project_id").bind("change", function() {
            var selected_project = $(this).val();
            
            var params = {};
            params["type"] = 13;
            params["project_id"] = selected_project;
            
            $.post(base_url + "admin/propertymanager/ajaxwork", params, function(data) {
                if(data.status != "OK") {
                    alert("Sorry, an error occured whilst trying to retrieve the project information");
                    return;   
                }
                
                $("#title").val(data.title);
                $("#area_id").val(data.area_id);
                
            }, "json");
        });  
    }
    
    $('.amount').keypress(function(e){
        var code, allowCode, numChars, isInt;
        numChars = [48,49,50,51,52,53,54,55,56,57];
        allowCode = [48,49,50,51,52,53,54,55,56,57, 8,9,37,39,44,46];
        if (e.keyCode) code = e.keyCode;
        else if (e.which) code = e.which;
        if ( $.inArray(code,allowCode) == -1 ) {
            return false;
        } else {
            return true;
        }
    }).blur(function(){
        this.value = formatFloat(this.value);
    });
    
    var updatePercent = function() {
        var itemno = $(this).attr('itemno');
        var total = $('#total_commission').val();
        total = parseFloat(total);
        if (isNaN(total)) total = 0;
        var val = this.value;
        val = parseFloat(val);
        if (isNaN(val)) val = 0;
        if (total != 0) {
            var percent = formatFloat(val/total*100);
        } else {
            var percent = formatFloat(0);
        }
        $('#stage'+itemno+'_percentage').val(percent);
    };
    
    var updatePercents = function() {
        $('.stage_payment').each(updatePercent);
    };
    
    $('.stage_payment').keyup(updatePercent)
        .blur(updatePercent);
        
    $('#total_commission').keyup(updatePercents)
        .blur(updatePercents);

    var loadedTab = window.location.hash ? window.location.hash.substring(1) : '';
    if (loadedTab=='tabStage') {
    	$('#tabStage a').trigger('click');
    }
    
});

</script>

<style type="text/css">
    .upload_documents object
    {
        width: none !important;
    }
    .upload_documents .doc_name
    {
        font-weight: bold;
    }
    
    .upload_documents > div
    {
        height: 50px;
        margin-top: 10px;
        padding-right:30px;
    }
    
    .upload_documents span
    {
        max-width: 150px;
    }
    
    .upload_documents .line
    {
        height: 2px;
        background-color: #F4F4F4;
        clear: both;
    }
    
    .btn-select-save 
    {
        background: url(../../images/admin/btnselectsave.png) top left no-repeat; 
    }
    .hide
    {
        margin-right:10px !important;
        float:right;
    }
    .hide_text
    {
        margin-right:140px !important;
        float:right;
    }
    select
    {
        width:270px;
    }
    .hide_text_project
    {
         float:right;
         margin-right:20px;
    }
    .qq-upload-list .qq-upload-failed-text {display:none;}
    #uploadTabContent {border:1px solid #000;border-width:0 1px 1px;padding:15px 20px}
</style>
    
</head>