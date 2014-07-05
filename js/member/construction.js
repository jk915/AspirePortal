//construction js
var ConstructionList = function()
{
    var self = this;
    var last_stage_id = '';
    
    // Entry point.
    this.init = function()
    {
        this.bindEvents();
    } 
    
    this.bindEvents = function()
    {
        $("a#single_image").fancybox({
            'afterClose': function() {
                
                self.loadDetails(self.last_stage_id);
                
            }
            
        });

        $('.view_stage').live('click',function(){
            var stage_id = this.id;
            self.last_stage_id = stage_id;
            self.loadDetails(stage_id);
        }); 
    }
    this.loadDetails = function(stage_id)
    {
        var parameters = {};
       // blockElement(".sidebar");
       
        parameters['type'] = 1;
        parameters['stage_id'] = stage_id;
        parameters['csrftokenaspire'] = $('input[name="csrftokenaspire"]').val();
        
        $.post(base_url + 'construction/ajax', parameters, function(data){
            //unblockElement(".sidebar");
            if (data.status == 'OK') {
                $('#stage_details').html(data.html);
                
                $.fancybox({
                    'href' : '#stage_details',

                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    }
}

var objConstructionList = new ConstructionList();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        // Setup the ConsntructionList object
        objConstructionList.init(); 
    }  
	
var make_key_date_default =function(){
            $('#description').val('');
            $('#keydate_id').val('');
            $('#estimate_date').val('');
            $('#followup_date').val('');
			
                    
       };	
	
$('#addkeydate').live('click',function(){
		make_key_date_default();
        $.fancybox({
            'href' : '#formaddkeydates',
        });
    }); 	
	  
	var refeshKeydates = function(){
        var property_id =  $("#property_id").val()
        var parameters = {};
        parameters['type'] = 33;
        parameters['property_id'] = property_id;
        $.get(base_url + 'construction/ajaxwork', parameters,function(data){
            if (data) {
                $('.keydatelisting').html(data);    
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
            
        });
    };	
	
	$('.savekeydate').live('click',function(){
		
        var parameters = {};
        var description = $('#description').val();
        var property_id = $('#property_id').val();
        var keydate_id = $('#keydate_id').val();
        var estimate_date = $('#estimate_date').val();
        var followup_date = $('#followup_date').val();
		
        var view = '';
        $("input[name='view[]']:checked").each(function(i) {
        	view +=$(this).val()+",";
        }); 		
		
        if (description.length == 0) {
            alert('Key date description is required');
            return false;
        }
        if (estimate_date.length == 0) {
            alert('Estimated Date is required');
            return false;
        }
		
		if (followup_date.length == 0) {
            alert('Follow Up Date is required');
            return false;
        }

                
        blockElement('.keydatelisting');
        parameters['description'] = description;
        parameters['property_id'] = property_id;
        parameters['keydate_id'] = keydate_id;
        parameters['estimate_date'] = estimate_date;
        parameters['followup_date'] = followup_date;
		parameters['type'] = 32;
        		
        $.get(base_url + 'construction/ajaxwork', parameters,function(data){
            unblockElement('.keydatelisting');
            if (data == 'OK') {
                refeshKeydates();
                $.fancybox.close();
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        });
    });
	
	$('#deletekeydate').live('click',function(){
        if ($(".keydatetodelete:checked").length == 0) {
            alert('Please select at least one comment you want to delete.');
            return;
        }
        if (confirm("Are you sure you want to delete the selected comment(s)?")) {
            var selectedvalues = "";
            var aItems = [];
            $(".keydatetodelete:checked").each(function(){
                var itemID = $(this).val();
                selectedvalues += itemID +';';
                aItems.push(itemID);
            });
            var parameters = {};
            parameters['type'] = 35;
            parameters['todelete'] = selectedvalues;
            $.get(base_url + 'construction/ajaxwork', parameters , function(data)
            {
                if(data == 'OK') {
                    refeshKeydates();
                    alert('Selected comment(s) was removed successfully.');
                } else {
                	alert("Sorry an error occured and your request could not be processed");
                }
            });
        }
    });	
	
	$('.editkeydate').live('click',function(){
		make_key_date_default();	
        var keydate_id = $(this).attr('rel');
        var parameters = {};
        blockElement(".keydatelisting");;
        parameters['type'] = 34;
        parameters['keydate_id'] = keydate_id;
        $.get(base_url + 'construction/ajaxwork', parameters, function(data){
            unblockElement(".keydatelisting");
            if (data.status == 'OK') {
                $('#estimate_date').val(data.estimate_date);
                $('#followup_date').val(data.followup_date);
                $('#description').val(data.description);
                $('#keydate_id').val(data.keydate_id);
				
				$.fancybox({
                    'href' : '#formaddkeydates',
                });
            } else {
                alert("Sorry an error occured and your request could not be processed");
            }
        },'json');
    });
	
	
	    this.blockElement = function(elementSelector)
    {
        $(elementSelector).block(
        {
            message: '<div><img src="'+base_url+'images/admin/ajax-loader-big.gif"/></div>',  
            css: {border:'0px','background-color':'transparent',position:'absolute'},
            overlayCSS: {opacity:0.04,cursor:'pointer',position:'absolute'}
        });
     }

    this.unblockElement = function(elementSelector)
    {
        $(elementSelector).unblock();
    } 