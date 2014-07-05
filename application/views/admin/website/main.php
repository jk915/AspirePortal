<body id="contact" >   
    <div id="wrapper">
    
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/website/navigation"); ?>            
            <?php 
            if(isset($message) && ($message != ""))
            {
                ?>
                <p><?php  echo $message;?></p>    
                <?php
            }
            ?>
            
<form class="plain" id="frmWebsite" name="frmWebsite" action="<?=base_url()?>admin/websitemanager/website/<?=$website_id?>"  method="post">
    <h2>Website Details</h2>    
    
    <?php
        if(isset($website))
        {
               // We're editing and existing website.  Show the tabs.
    ?>
            
    <!-- tabs -->
    <ul class="css-tabs skin2">
        <li><a href="#">Website Details</a></li>        
    </ul>   
    
    <!-- panes -->
    <div class="css-panes skin2">
        <div style="display:block">
    <?php
        }        
    ?>   
            <div class="left" style="width:50%">
             
                <label for="website_name">Website Name:<span class="requiredindicator">*</span></label> 
                <input type="text" name="website_name" class="required" value="<? echo ($website_id !="") ? $website->website_name : "" ?>" />
                
                <label for="url_id">Website ID:<span class="requiredindicator">*</span> e.g. 'au'</label> 
                <input type="text" name="url_id" class="required" value="<? echo ($website_id !="") ? $website->url_id : "" ?>" />
                
                <label for="external_url">External website URL:</label> 
                <input type="text" name="external_url" value="<? echo ($website_id !="" && isset($website->external_url)) ? $website->external_url : "" ?>" />
                
                <label for="lang_id">Language:</label> 
                <select name="lang_id">
                    
                    <?php echo $this->utilities->print_select_options($languages, "lang_id", "language", ($website_id != "") ? $website->lang_id : ""); ?>
                    
                </select><br class="clear" /><br/>
                  
               <input type="checkbox" name="enabled" value="1" class="left" <?php echo ($website_id !="") ? (($website->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Website is enabled</label> 
               <br/>
            </div>
            <div class="left">
            
                <label for="start_date">Start Date:</label> 
                <input name="start_date" id="start_date" class="date-pick dateITA" value="<?php echo (($website_id !="") && ($website->dmy_start_date != "00/00/0000")) ? $website->dmy_start_date : "";?>" /><div class="clear"></div>
                
                <label for="expiry_date">Expiry Date:</label> 
                <input name="expiry_date" id="expiry_date" class="date-pick dateITA" value="<?php echo (($website_id !="") && ($website->dmy_expiry_date != "00/00/0000")) ? $website->dmy_expiry_date : "";?>" /><div class="clear"></div>
                
            </div>
            <div class="clear"></div>       
    <?php
        if(isset($website))
        {
               // We're editing and existing website.  Show the tabs.
    ?>
        </div>
    </div>        
    <?php
        }
    ?>    
    <br/>
    <br/>
              
    <label for="heading">&nbsp;</label> 
    <input id="button" type="button" value="Save Website" /><br/>                

    <input type="hidden" name="postback" value="1" />
    <input type="hidden" name="id" id="website_id" value="<?php echo $website_id?>" />
</form>


<p><!-- --></p>
<?php $this->load->view("admin/website/navigation"); ?>
