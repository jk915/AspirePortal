<body id="pagemanager" >   
   <div id="wrapper">
     
      <? $this->load->view("admin/navigation");?>

      <div id="content">

      <? $this->load->view("admin/buildermanager/navigation",array("side"=>"top"));?>    

<form class="plain">
        <div id="page_listing">
            <? $this->load->view('admin/buildermanager/builder_listing.php',array('builders'=>$builders)); ?>
        </div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Delete Builders" id="delete" />
            </div>
            
        </div>    
</form>

<? $this->load->view("admin/buildermanager/navigation",array("side"=>"bottom"));?>    
