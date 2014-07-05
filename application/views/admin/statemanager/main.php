<body id="pagemanager" >   
   <div id="wrapper">
     
      <? $this->load->view("admin/navigation");?>

      <div id="content">

      <? $this->load->view("admin/statemanager/navigation",array("side"=>"top"));?>    

<form class="plain">


		<div id="page_listing">
            <? $this->load->view('admin/statemanager/state_listing.php',array('states'=>$states)); ?>
        </div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Delete States" id="delete" />
            </div>
            
        </div>    
</form>

<? $this->load->view("admin/statemanager/navigation",array("side"=>"bottom"));?>    
