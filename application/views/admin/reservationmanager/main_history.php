<body id="pagemanager" >   
   <div id="wrapper">
     
      <?php $this->load->view("admin/navigation");?>

      <div id="content">

      <?php $this->load->view("admin/reservationmanager/navigation_history",array("side"=>"top"));?>    

<form class="plain">
    <p><?php echo $name;?>, below is a complete reservations and sales history listing.</p>    
        <div id="page_listing">
            <?php $this->load->view('admin/reservationmanager/history.php',array('reservations'=>$reservations)); ?>
        </div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Remove Reservations" id="delete" />
            </div>
            
        </div>    
</form>

<?php $this->load->view("admin/reservationmanager/navigation", array("side"=>"bottom"));?>    