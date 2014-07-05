$(document).ready(function()
{
   // Validate the user details form upon submission
   $('#frmUser').validate({});
   
   // Make sure the password field is blank.
   $("#password").val("");

   // Active the tabs
   $("ul.skin2").tabs("div.skin2 > div");
   
	$('#change_email').click(function()
	{
		$('#password').addClass("required");

		if ($('#frmUser').valid())
		{
			var parameters = {};

			parameters['new_password'] = $('#password').val();
			parameters['email_new_password'] = ($('#email_password').is(":checked")) ? 1 : 0 ;
			parameters['user_id'] = $('#id').val();
			parameters['type'] = 4;


			blockObject($('#password').parent());
			$.post(base_url + 'admin/usermanager/ajaxwork', parameters , function(data)
			{
				unblockObject($('#password').parent());
				$('#message').html(data.message).fadeIn('slow'); 
			}, "json");
		}

		$('#password').removeClass("required");

	});

        if ($("#user_type_id").val() == 4) {
            $("#builder").show();
        }

	$("#user_type_id").change(function()
	{   
		//markup();
                if (this.value == 4) {
                    $("#builder").show();
                } else {
                    $("#builder").hide();
                }
	});
   
   	// When the user clicks on the autogenerate link, generate a random password.
	$("a.autogenerate").click(function(e)
	{
		// Prevent default link action.
		e.preventDefault();
		
		// Set the maximum password length
    	var password_len = 6;
    	
    	// Define the valid characters for a password.
    	var validChars = new Array(
    		"a", "b", "c", "d", "e", "f", "g", "h", "i", "j", "k", "l", "m", "n", "o", "p", "q", "r", "s", "t", "u", "v", "w", "x", "y", "z", 
    		"A", "B", "C", "D", "E", "F", "G", "H", "I", "J", "K", "L", "M", "N", "O", "P", "Q", "R", "S", "T", "U", "V", "W", "X", "Y", "Z", 
    		"0", "1", "2", "3", "4", "5", "6", "7", "8", "9", 
    		"*", "#");
    		
    	var pass = "";
    	
    	for(x = 1; x <= password_len; x++)
    	{
			pass += validChars[Math.floor(Math.random() * validChars.length + 1)];
    	}
    	
    	// Set the generated password into the password fields
    	$("#password").val(pass);
    	$("#password_repeat").val(pass);
    	
    	// Show the new password to the user.
    	$("#new_pass span").html(pass);
    	$("#new_pass").removeClass("hidden");
	});
	
	$("a.login_as_this_user").click(function(e)
	{
		if (confirm("Are you sure you want to login as this user?"))
		{
			var parameters = {};
			parameters['user_id'] = $('#id').val();
			parameters['type'] = 16;
			$.post(base_url + 'admin/usermanager/ajaxwork', parameters , function(data)
			{
				if (data.status == 'OK')
					window.location.href= base_url;
				else
					alert(data.message);
					
			}, "json");
		}
	});
	
	//Investor and Lead Select
	$('#keywords').hide();
	if ($("#user_type_id option[value='6']").attr('selected') || $("#user_type_id option[value='7']").attr('selected')) {
		$('#keywords').show();
	}
    $("#user_type_id").change(function(){
		if($(this).val() == "6" || $(this).val() == "7")
            $('#keywords').show();
        else
            $('#keywords').hide();
    });
    
    // Handle remove block button
    $("#btnRemoveBlock").click(function(e) {
        e.preventDefault();
        
        // Set the remove block flag in the form
        $("#remove_block").val("1"); 
        
        // Submit
        $("#frmUser").submit();
    });
});
