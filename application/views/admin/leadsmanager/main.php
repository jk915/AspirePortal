<body id="pagemanager" >   
   <div id="wrapper">
      <?php $this->load->view("admin/navigation");?>
      <div id="content">
         <?php $this->load->view("admin/leadsmanager/navigation",array("side"=>"top"));?>	
         <form class="plain" name="frmUsers" id="frmUsers" action="#">
            <div id="page_listing">
                <?php $this->load->view('admin/leadsmanager/lead_listing.php',array('leads'=>$leads)); ?>
            </div>
            <div id="controls">
		<div id="page_buttons" class="left" >
                    <div id="pagination"></div>
		</div>
		<div class="right">
                    <input class="button" type="button" value="Delete Leads" id="delete" />
		</div>      
            </div>
            <br clear="all" />
            <div class="top-margin"></div>
        </form>
<?php $this->load->view("admin/leadsmanager/navigation",array("side"=>"bottom"));?>	

