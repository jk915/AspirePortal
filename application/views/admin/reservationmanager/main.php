<body id="pagemanager" >   
   <div id="wrapper">
     
      <?php $this->load->view("admin/navigation");?>

      <div id="content">

      <?php $this->load->view("admin/reservationmanager/navigation",array("side"=>"top"));?>    

<form class="plain">
    <p><?php echo $name;?>, below is a list of the properties that have been reserved by agents. To remove the reservation on a property, click on the associated "unreserve" checkbox.</p>    
        <div id="page_listing">
            <?php $this->load->view('admin/reservationmanager/reservation_listing.php',array('reservations'=>$reservations)); ?>
        </div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Remove Reservations" id="delete" />
            </div>
            
        </div>    
        
        <div id="summary_table">
            <?php $this->load->view('admin/reservationmanager/summary_table', array("summary_table" => $summary_table)); ?>
        </div>
</form>

<?php $this->load->view("admin/reservationmanager/navigation", array("side"=>"bottom"));?>    