<form id="frmLicense" name="frmLicense" action="#" method="post" class="modalForm">
	<h1>Generate a License</h1>
	
	<p>Please select how many licenses you wish to create.</p>
	
	<dl>
		<dt>Product:</dt>
		<dd><?php echo $product->product_name; ?></dd>
		
		<dt>Available Licenses:</dt>
		<dd id="num_credits"><?php echo $devices->num_credits; ?></dd>	
		
		<dt>No. licenses to generate:</dt>
		<dd>
			<select id="num_licenses" name="num_licenses">
			<?php
				// Output up to 10 device IDs. 
				// Limit the number to the number of available credits.
				$max = ($devices->num_credits > 10) ? 10 : $devices->num_credits;
				
	        	for($v = 1; $v <= $max; $v++)
	        	{
					?>
				<option value="<?php echo $v; ?>"><?php echo $v; ?></option>					
					<?php
	        	}
			?>
			</select>
		</dd>			
	</dl>
	
	<div class="clear"></div>
	
	<hr />	
	<dl id="dlDevices">
        <dt></dt>
        <dd class="header">
            <span>Device ID / Serial</span>
            <span>Device Name</span>            
        </dd>
        
		<dt id="dt1">1</dt>
		<dd id="dd1">
            <input type="text" id="device1" name="device1" class="required device_id" value="" />
            <input type="text" id="device_name1" name="device_name1" class="required" value="" />
        </dd>		
	</dl>
	
	<div class="clear"></div>
	
	<hr />

	<p id="hint">Hint: Hit the tab key after entering each device ID and device name.</p>
	<div id="submit">
		<p>Excellent, you're good to go.  Hit the "Generate Serials" button below to finalise your license creation.</p>
		<input type="button" value="Generate Serials" />
	</div>
		
	
</form>

<script type="text/javascript">
var product_id = "<?php echo $product->product_id; ?>";

// Handle event when the document has loaded.
$(document).ready(function()
{
	// Default number of licenses to show/generate to 1.
	var current_num_licenses = 1;
	
	// Hide the submit button.
	$("#submit").css("display", "none");
	
	// Call uniform on the form fields to make it look sexy.
	$("#frmLicense input, #frmLicense select").uniform();
	
	// Handle the change event when the user changes how many licenses to generate.
	$("#num_licenses").bind("change", function()
	{
		// Hide the submit button.
		$("#submit").css("display", "none");		
		
		// Find out how many licenses we're generating.
		var num_licenses = $("#num_licenses").val();
		
		if((num_licenses == "") || (!IsNumeric(num_licenses)))
			return;

		// Calculate how many fields we need to add or subtract.
		var diff = num_licenses - current_num_licenses;
		
		if(diff > 0)
		{
			// Add more input fields
			for(x = 0; x < diff; x++)
			{
				var dID = current_num_licenses + 1; 
				html = '<dt id="dt' + dID + '">' + dID + '</dt>' +
					'<dd id="dd' + dID + '">' +
                         '<input type="text" id="device' + dID + '" name="device' + dID + '" class="required device_id" value="" />&nbsp;' +
					     '<input type="text" id="device_name' + dID + '" name="device_name' + dID + '" class="required" value="" />' +
                    '</dd>';
					
					
				$("#dlDevices").append(html);
				
				$("#device" + dID).uniform();
				$("#device_name" + dID).uniform();
				
				current_num_licenses++;	
			}
		}
		else if(diff < 0)
		{
			// We need to remove inputs
			var x = current_num_licenses;
			
			// Set the current num licenses to reflect the removal of devices.
			current_num_licenses = current_num_licenses + diff;
			
			while(x > current_num_licenses)
			{
            	// Remove the dd and dt elements for this device.
				$("#dd" + x).remove();
				$("#dt" + x).remove();                
				
				x = x - 1;
			}
		}
        
        //get last number of license (maximum number)        
        var last_num_licenses = $("#num_licenses :last").val();
        var is_ie7 = ($.browser.msie && $.browser.version == '7.0');
                
        //resize popup when the max license is bigger then 8
        if(last_num_licenses >= 8)
        {  
            if(num_licenses >= 8)
            {
                var ie_tmp = 30;
                if (num_licenses == 10) ie_tmp = 40;
                
                var $innerHeight = (515 + (is_ie7 ? ie_tmp : 0 ) - (last_num_licenses - num_licenses) * 10 );
                
                //alert($innerHeight);
                $.colorbox.resize({innerHeight: $innerHeight});
            }
            else
                $.colorbox.resize({innerHeight: (is_ie7) ? 480 : 450 });
        }
          
		 
	});
	
	// When a device input has changed, check if the entered device ID is 
	// in the correct format.
	$("#dlDevices input.device_id").live("change", function() 
	{
		// Hide the submit button.
		$("#submit").css("display", "none");		
		
		// Get the id and value of this text field
		var dID = $(this).attr("id");
		var val = $(this).val();
	
		// Ignore blank values.	
		if(val == "")
			return true;   
			
		// Check if the spinning wheel has already been injected.
		var wheel = $("#" + dID).parent().find("img.device-loader").attr("class");
		if(wheel != undefined)
			return true;
			
		// Remove any images currently in the result area
		$("#" + dID).parent().find("img").remove();  
			
		// Inject an ajax spinny wheel in the correct place
		var html = '<img id="ajax-' + dID + '" src="' + base_url + 'images/admin/ajax-loader.gif" height="16" width="16" class="device-loader" />';
		$("#" + dID).parent().append(html);
		
		// Make an ajax request to see if the entered serial number is correct
        var parameters = {};
        parameters['type'] = 'check_serial';
        parameters['device_id'] = val;
        parameters['product_id'] = product_id;
        
        $.post(base_url + 'account/ajaxwork', parameters , function(data)
        {
        	// Remove the ajax wheel
        	$("#" + dID).parent().find("img").remove();
        	
        	// Default to error icon
        	var icon = '<img src="' + base_url + 'images/cross.png" height="15" width="16" class="device-result result-error" />';
        	
        	var checkAllOK = false;
        	
        	if(data.message == "OK")
        	{
				// Change to OK icon.
				icon = '<img src="' + base_url + 'images/tick.png" height="15" width="16" class="device-result result-ok" />';
				
				// Flag to check to see if we're ready to submit.
				checkAllOK = true;	
        	}
        	
        	// Append the icon
        	$("#" + dID).parent().append(icon);
        	
        	// If the check flag has been set, check to see if all device IDs have been entered OK.
        	if(checkAllOK)
        		checkReadyToGenerate();	

        }, "json");		
	});
   
	$('#dlDevices input[id^=device_name]').live("change", function(){

			var $this = $(this);
			
			if ( trim ($this.val()) == "")
			{
				$this.css("border-color","#ff0000");
				
				
			}
			else
			{
				$this.css("border-color","");
					
			}

			checkReadyToGenerate();
				 
	});
	
	// Handle the event to actually go ahead and create the licenses.
	//$("#submit input[type='button']").live("click", function()
	$("#submit > div").unbind("click").live("click",function() { 

	
		blockElement("#submit");
		
		// Grab the user entered deviceIDs from the UI.
		var deviceIDs = getDeviceIDs();
		var deviceNames = getDeviceNames();
		
		// Make an ajax request to generate all the licenses.
        var parameters = {};
        parameters['type'] = 'create_licenses';
        parameters['device_ids'] = deviceIDs;
        parameters['device_names'] = deviceNames;
        
        parameters['product_id'] = product_id;
        
        $.post(base_url + 'account/ajaxwork', parameters , function(data)
        {
        	unblockElement("#submit");	
        	
        	if(data.message == "OK")
        	{
				// Reload this screen.
				alert("Thank you.  Your licenses have been generated and emailed to you.");
				window.location = base_url + "page/account";
        	}
        	else
        	{
				alert("Sorry, something went wrong whilst trying to generate your licenses.  Please try again later");
        	}
		}, "json");
	});  
});

// Gets the user entered deviceIDs from all of the input fields and 
// creates a CSV style string.  Pipes  "|" are used as the delimiter.
function getDeviceIDs()
{
	var deviceIDs = "";
	
	$("#dlDevices input.device_id").each(function()	
	{
		if(deviceIDs != "") deviceIDs += "|";
		
		deviceIDs += $(this).val();
	});
	
	return deviceIDs;
}

//Gets the user entered deviceNamess from all of the input fields and 
//creates a CSV style string.  Pipes  "|" are used as the delimiter.
function getDeviceNames()
{
	var deviceNames = "";
	
	$('#dlDevices input[id^=device_name]').each(function()	
	{
		if(deviceNames != "") deviceNames += "|";
		
		deviceNames += $(this).val();
	});
	
	return deviceNames;
}


// Checks to see if all deviceIDs have been entered correctly.
// It simply looks for the green tick icons beside each device ID field.
function checkReadyToGenerate()
{
	var showSubmit = true;  
	
	// Iterate through all device ID fields and determine if the result icon is showing for all of them.
	$("#dlDevices input.device_id").each(function() 
	{
		//var img = $(this).find("img.result-ok").attr("class");
		var img = $(this).next().next();
		
		if(img == undefined || !img.hasClass("result-ok"))
		{
			showSubmit = false;
			return;
		}
	});
    
	//check device names (device name required)
	$('#dlDevices input[id^=device_name]').each(function(i, element){

			if ( trim($(element).val()) == "")
			{
				showSubmit = false;
				$(element).css("border-color","#ff0000");
			}
			
	});

	
	if(showSubmit)
	{
		// Hide the hint
		$("#hint").css("display", "none");
		
		// Show the submit button.
		$("#submit").fadeIn();
	}
	else
	{
		$("#hint").css("display", "block");
		$("#submit").fadeOut();
	}
		
	
	return true;
}
             
// Determines if a value is numeric.
function IsNumeric(input)
{
   return (input - 0) == input && input.length > 0;
}


</script>