    <link rel="stylesheet" href="<?php echo base_url(); ?>css/member/reveal.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>css/member/tasks.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>css/member/themes/base/jquery.ui.datepicker.css">
    <link rel="stylesheet" href="<?php echo base_url(); ?>css/member/themes/base/jquery.ui.all.css">
    <script src="<?php echo base_url(); ?>js/member/ui/jquery.ui.core.js"></script>
	<script src="<?php echo base_url(); ?>js/member/ui/jquery.ui.widget.js"></script>
	<script src="<?php echo base_url(); ?>js/member/ui/jquery.ui.datepicker.js"></script>
	<script>
  $(function() {
    
	 $("#login_expiry_date").datepicker({minDate: 0, dateFormat: 'yy/mm/dd' });
  });
  </script>
</head>
