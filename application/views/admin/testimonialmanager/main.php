<body id="pagemanager">   
   <div id="wrapper">
      
      <?php $this->load->view("admin/navigation");?>

      <div id="content">

      <?php $this->load->view("admin/testimonialmanager/navigation");?>    

<form class="plain" action="#">

    <div id="page_listing">
        <?php $this->load->view('admin/testimonialmanager/testimonial_listing.php',array('testimonials'=>$testimonials)); ?>
    </div> 
    
    <div class="clear"></div>    
    
    <div id="controls">
        <div id="page_buttons" class="left" >
            <div id="pagination"></div>
        </div>
        
        <div class="right">
            <input class="button" type="button" value="Delete Testimonials" id="delete" />
        </div>
        
        <div class="clear"></div>            
    </div>    
</form>

<?php $this->load->view("admin/testimonialmanager/navigation");?>    