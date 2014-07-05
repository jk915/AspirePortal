$(document).ready(function()

{

   // Validate the user details form upon submission

   $('#frmLead').validate({});


   // Active the tabs

   $("ul.skin2").tabs("div.skin2 > div");

   

	$('#change_email').click(function()

	{

		if ($('#frmLead').valid())

		{

			var parameters = {};

			parameters['lead_id'] = $('#id').val();

			parameters['type'] = 4;





			blockObject($('#password').parent());

			$.post(base_url + 'admin/leadsmanager/ajaxwork', parameters , function(data)

			{

				$('#message').html(data.message).fadeIn('slow'); 

			}, "json");

		}

	});
   

});

