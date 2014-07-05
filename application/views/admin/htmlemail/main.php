<body id="contact" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/htmlemail/navigation"); ?>            
            <p><?php echo $message?></p>    
        
            <form class="plain" id="frmHTMLEmail" name="frmHTMLEmail" action="<?=base_url()?>htmlemailmanager" method="post">
                <label for = "project">Project</label> 
                <select id = "project" name="project">
                    <option value="">ALL</option>
                    <?php echo $this->utilities->print_select_options($projects,"project_id","project_name"); ?>
                </select>
                
                <label for="state">State</label> 
                <select id = "project" name="project">
                    <option value="">ALL</option>
                    <?php echo $this->utilities->print_select_options($states,"name","name"); ?>
                </select>
                
                <label for="status">Status</label> 
                <select is="status" name="status">
                    <option value="available">Available</option>
                </select>
                
                <label for="type">Type</label> 
                <select is="type" name="type">
                    <option value="">ALL</option>
                </select>
                <div class="clear"></div>
                <div class="left">
                    <label for="from_price">Price From</label>   
                    <input type = "text" name = "from_price" id="from_price" />
                </div>    
                
                <div class="left" style="padding-left:10px;">
                    <label for="to_price">To</label>   
                    <input type = "text" name = "to_price" id="to_price" />
                </div>                    
           
                <div class="clear"></div>
                <br/>
                <input id="button" type="submit" value="Create Email Template" /><br class="clear"/>                
                
                <br/>
            </form>


    <p><!-- --></p>
    <?php $this->load->view("admin/htmlemail/navigation"); ?>