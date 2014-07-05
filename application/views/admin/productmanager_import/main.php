<body id="contact" >   
    <div id="wrapper">
        
        <? $this->load->view("admin/navigation");?>
        
        <div id="content">
        
            <? $this->load->view("admin/importmanager/navigation"); ?>            
            <p id="message"></p>    
        
   <form class="plain">
        
        <div class="left import_box">
            <fieldset>
                <legend>Import Type</legend>
                <input type="radio" id="import_type" value="1" class="left" checked="checked" />
                <label for="property_reservation" class="left" style="padding-top:0px">&nbsp;Property Reservations</label>
            </fieldset>
        </div> 
                      
        <div class="clear"></div>
        
        <div class="left import_box">
            <fieldset>
                <legend>Import File</legend>
                <input type="file" name="upload_file" id="upload_file" />    
            </fieldset>
        </div>
        
        <div class="clear"></div>
        <br/>
        
   </form>
   <? $this->load->view("admin/importmanager/navigation"); ?>            