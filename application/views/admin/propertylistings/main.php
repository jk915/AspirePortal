<body>   
   <?php $this->load->view("page_header");?>    
   
   <img id="hPic" src="<?php echo base_url();?>images/h_about.jpg" border="0" width="920" height="140" alt=" " />      
    
   <div id="main">
	 
        <? $this->load->view("admin/propertylistings/navigation",array("side"=>"top"));?>	
                
		<form class="plain" action="#">
			<div id="page_listing">
				<? $this->load->view('admin/propertylistings/property_listing.php',array('properties'=>$properties)); ?>
			</div>	
            
            <div id="controls">
        
                <div id="page_buttons" class="left" >
                    <div id="pagination"></div>
                </div>
                <div class="clear"></div>
                
            </div>    
        </form>
        
   </div>     