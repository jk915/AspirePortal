<body id="pagemanager" >   
   <div id="wrapper">
	 
      <? $this->load->view("admin/navigation",array("side"=>"top"));?>

      <div id="content">

      <? $this->load->view("admin/pagemanager/navigation");?>	

<form class="plain" action="#">
	<p><?php echo $name;?>, below is a list of the web pages that are available on your site.  To edit a page, click on the page name in the left most column.  To delete a page, click on the checkbox in the delete column.</p>
		<div id="page_listing">
			<?php $this->load->view('admin/pagemanager/page_listing.php',array('pages'=>$pages)); ?>
		</div>
		
		<div id="controls">
		
			<div id="page_buttons" class="left" >
				<div id="pagination"></div>
			</div>
			
			<div class="right">
				<input class="button" type="button" value="Delete Pages" id="delete" />
			</div>
			
		</div>	
</form>

<?php $this->load->view("admin/pagemanager/navigation",array("side"=>"bottom"));?>
