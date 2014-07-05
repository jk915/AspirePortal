<form id="frmLicense" name="frmLicense" action="#" method="post" class="modalForm">
	<h1><?php echo $product->product_name; ?> Licenses</h1>
	
	<p>Please find below your licenses for <?php echo $product->product_name; ?>.  If you wish to assign a device name to a license, click the 
	relevant "edit" link.</p>
	
	<table id="tblLicenses" cellspacing="0" width="100%" align="center">
		<tr>
			<th>Device ID</th>
			<th>Device Name</th>
			<th>License Date</th>
			<th>Send License</th>
		</tr>
		<?php
	    if($devices)
	    {
			foreach($devices->result() as $row)
			{
				?>
		<tr>
			<td><?php echo $row->user_device_id; ?></td>
			<td>
				<div class="view">
					<span><?php echo $row->device_name; ?></span> <a href="<?php echo $row->device_id; ?>" class="edit-device-name">Edit</a>
				</div>
				
				<div class="edit_device">
					<input type="text" value=""></input> 
					
					<a href="<?php echo $row->device_id; ?>" class="save-device-name">Save</a>
					<a href="<?php echo $row->device_id; ?>" class="cancel-device-name">Cancel</a>
				</div>
				
			</td>
			<td><?php echo $this->utilities->isodatetime_to_userdate($row->last_mod); ?></td>
			<td><a href="<?php echo $row->device_id; ?>" class="send-license">Email this license to me</a></td>
		</tr>				
				<?php
			}
	    }
		?>
	</table>
</form>

<style type="text/css">

.edit_device
{
	display: none;
}

	.edit_device input
	{
		width: 80px;
	}
</style>

<script type="text/javascript">
var product_id = "<?php echo $product->product_id; ?>";


// Handle event when the document has loaded.
$(document).ready(function(){
	
	//edit device name
	$('.edit-device-name').click( function(e){

		e.preventDefault();
		
		var $this = $(this);
		var $parent = $this.parent();
		var $next_div = $parent.next();
		
		var device_id = $this.attr("href");
	
		//hide current div
		$parent.fadeOut();

		//show next div
		$next_div.fadeIn();

		//copy value from span to input
		$next_div.find("input").val( $this.prev().html() );
		
	});

	$('.save-device-name').click( function(e){

		
		e.preventDefault();
		
		var $this = $(this);
		var $parent = $this.parent();
		var $prev_div = $parent.prev();
		var $input = $this.prev();
		var device_id = $this.attr("href");

		if ( trim($input.val()) == "")
			return;

		var parameters = {};

		parameters['type'] = 'change-device-name';
		parameters['device_id'] = device_id;
		parameters['new_name'] = $input.val();


		blockElement("#tblLicenses");
		
		$.post(base_url + 'account/ajaxwork', parameters , function(data){

				if (data.message != "")
				{
					alert(data.message);
				}
				else
				{
					//
					$parent.fadeOut();
					//copy new value
					$prev_div.find("span").html( $input.val() );
					
					$prev_div.fadeIn();
				}
				
				
		        unblockElement("#tblLicenses");

		}, "json");	
		        
		
		
	});

	
	$('.cancel-device-name').click( function(e){

		e.preventDefault();
		
		$this = $(this);

		$this.parent().fadeOut();
		$this.parent().prev().fadeIn();

	});

	$('.send-license').click ( function(e){

		e.preventDefault();
		
		var $this = $(this);
		var device_id = $this.attr("href");
		
		var parameters = {};
		parameters['type'] = "send-license";
		parameters['device_id'] = device_id;

		blockElement("#tblLicenses");
		$.post( base_url + 'account/ajaxwork', parameters, function(data){

			unblockElement("#tblLicenses");
			
			if (data.message == "OK")
				alert("Email sent successfully.");
			
		},"json");
		
		
	});
});

</script>