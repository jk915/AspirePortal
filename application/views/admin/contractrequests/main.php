<body id="pagemanager" >   
   <div id="wrapper">
     
      <? $this->load->view("admin/navigation");?>

      <div id="content">

      <? $this->load->view("admin/contractrequests/navigation",array("side"=>"top"));?>    

<form class="plain">
    <div id="page_listing">
        <? $this->load->view('admin/contractrequests/request_listing.php',array('requests'=>$requests,'pageno'=>$pageno,'totalPages'=>$totalPages)); ?>
    </div>
    
    <div id="controls">
        <div class="right">
            <!--<input class="button" type="button" value="Delete Requests" id="delete" />-->
        </div>
    </div>    
</form>

<? $this->load->view("admin/contractrequests/navigation",array("side"=>"bottom"));?>    
