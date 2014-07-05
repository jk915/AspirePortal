<body id="contact" >   
    <div id="wrapper">
        
        <? $this->load->view("admin/navigation");?>
        
        <div id="content">
        
            <? $this->load->view("admin/importmanager/navigation"); ?>            
            <p id="message"></p>    
        
   <form class="plain" action="#">
        
        <div class="left import_box">
            <fieldset>
                <legend>Import Type</legend>
                <input type="radio" name="import_type" value="stock" class="left" checked="checked" />
                <label class="left" style="padding-top:0px">&nbsp;Stock Import</label>
            </fieldset>
        </div> 
                      
        <div class="clear"></div>
      
        <div class="left import_box">
            <fieldset>
                <legend>Import File</legend>
                <div id="upload_file"></div>    
            </fieldset>
        </div>
        
        <div class="clear"></div>
        <br/>
        
   </form>
   <? $this->load->view("admin/importmanager/navigation"); ?>            