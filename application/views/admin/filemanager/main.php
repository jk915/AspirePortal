<body id="contact" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>        
		
        <div id="content">

			<? $this->load->view("admin/filemanager/navigation"); ?>			
			<p><?=$message?></p>
			<p class="hint">Note: You may not upload files that are greater than 2MB in size.</p>	
		
   <form class="plain">
		
	    <? $this->load->view("admin/filemanager/main_inner") ?>
	
   </form>


<? $this->load->view("admin/filemanager/navigation"); ?>
