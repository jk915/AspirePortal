<body id="pagemanager" >   
   <div id="wrapper">
      
      <?php $this->load->view("admin/navigation");?>

      <div id="content">

      <?php $this->load->view("admin/websitemanager/navigation");?>    

<form class="plain" action="#">
    <p><?php echo $name;?>, below is a list of the websites that are available to your customers.</p>    

    <div id="page_listing">
        <?php $this->load->view('admin/websitemanager/website_listing.php',array('websites'=>$websites)); ?>
    </div> 
    
    <div class="clear"></div>    
    
    <div id="controls">
        <div id="page_buttons" class="left" >
            <div id="pagination"></div>
        </div>
        
        <div class="right">
            <input class="button" type="button" value="Delete Websites" id="delete" />
        </div>
        
        <div class="clear"></div>            
    </div>    
</form>

<?php $this->load->view("admin/websitemanager/navigation");?>    