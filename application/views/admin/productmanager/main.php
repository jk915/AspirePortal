<body id="pagemanager" >   
   <div id="wrapper">
     
      <? $this->load->view("admin/navigation");?>

      <div id="content">

      <? $this->load->view("admin/productmanager/navigation");?>    

    <form class="plain" action="#">
    
        <? $this->load->view("admin/productmanager/main_inner") ?>
        
    </form>

<? $this->load->view("admin/productmanager/navigation");?>        