    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.paginate.js"></script>      
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/pagination.js"></script>  
     
    <!-- chart -->    
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/plugins/flashchart/json/json2.js"></script>  
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/plugins/flashchart/swfobject.js"></script>  
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/plugins/flashchart/flash_chart.js"></script>  
	
<?php load_charts('order'); ?>

<style type="text/css">

	#chart_holder
	{
		background-color: #F8F8D8;
		-moz-border-radius: 10px;
		-webkit-border-radius: 10px;
		padding-top: 10px;
		padding-bottom: 10px;
		width: 100%;
	}
	label.years
	{
		margin-right: 10px; 
		margin-left: 5px;
		padding-top: 5px;
		margin-bottom: 10px;
	}
	#bar_chart_holder
	{
		width: 460px;
	}
	#pie_chart_holder
	{
		width: 440px;
	}
	#product_bar_chart_holder
	{
		width: 460px;
		margin-top: 15px;
	}
	
	html
	{
		background-color: white!important;
	}

</style>