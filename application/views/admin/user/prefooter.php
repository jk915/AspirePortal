<script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery-1.9.1.js"></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/jquery-ui.js"></script>
   <script type="text/javascript">
   $.noConflict();
   jQuery(document).ready(function() 
	{
		jQuery("#login_expiry_date").datepicker({minDate: 0, dateFormat: "yy/mm/dd"});
	});
   </script>