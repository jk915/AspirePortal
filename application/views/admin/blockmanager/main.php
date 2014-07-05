<body id="pagemanager" >   
   <div id="wrapper">
	 
      <?php $this->load->view("admin/navigation");?>

      <div id="content">

      <?php $this->load->view("admin/blockmanager/navigation");?>	

<form class="plain" action="#">	
    <div id="page_listing">
            <?php
             $this->load->view('admin/blockmanager/block_listing.php',array('blocks'=>$blocks));
            ?>
    </div>

    <div id="controls">

            <div id="page_buttons" class="left" >
                    <div id="pagination"></div>
            </div>

            <div class="right">
                    <input class="button" type="button" value="Delete Blocks" id="delete" />
            </div>

    </div>
</form>

<?php $this->load->view("admin/blockmanager/navigation");?>	
