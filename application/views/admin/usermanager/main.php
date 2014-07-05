<body id="pagemanager" >   
   <div id="wrapper">
      <?php $this->load->view("admin/navigation");?>
      
      <div id="content">
         <?php $this->load->view("admin/usermanager/navigation",array("side"=>"top"));?>	

         <form class="plain" name="frmUsers" id="frmUsers" action="#">
              <div id="page_listing">
                      <?php $this->load->view('admin/usermanager/user_listing.php',array('users'=>$users)); ?>
              </div>

              <div id="controls">
                      <div id="page_buttons" class="left" >
                              <div id="pagination"></div>
                      </div>

                      <div class="right">
                              <input class="button" type="button" value="Delete Users" id="delete" />
                      </div>

              </div>

              <br clear="all" />
              <?php /*<a href="<?phpphp echo base_url(); ?>usermanager/download/4" class="userdownload sprite"></a>
              <a href="<?phpphp echo base_url(); ?>usermanager/download/4">Member CSV</a>
              */?>
              <div class="top-margin"></div>
        </form>
   

<?php $this->load->view("admin/usermanager/navigation",array("side"=>"bottom"));?>	
