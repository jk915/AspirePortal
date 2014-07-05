<body id="contact" >   
    <div id="wrapper">
    
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/testimonial/navigation"); ?>            
            <?php 
            if(isset($message) && ($message != ""))
            {
                ?>
                <p><?php  echo $message;?></p>    
                <?php
            }
            ?>
            
<form class="plain" id="frmTestimonial" name="frmTestimonial" action="<?=base_url()?>admin/testimonialmanager/testimonial/<?=$testimonial_id?>"  method="post">
    <h2>Testimonial Details</h2>    
    
    <?php if(isset($testimonial)) : ?>
            
    <!-- tabs -->
    <ul class="css-tabs skin2">
        <li><a href="#">Testimonial Details</a></li>        
    </ul>   
    
    <!-- panes -->
    <div class="css-panes skin2">
        <div style="display:block">
    <?php endif; ?>
            <div class="left" style="width:50%">
             
                <label for="author">Author:<span class="requiredindicator">*</span></label> 
                <input type="text" name="author" class="required" value="<? echo ($testimonial_id !="") ? $testimonial->author : "" ?>" />
                
                <label for="company">Company:<span class="requiredindicator">*</span></label> 
                <input type="text" name="company" class="required" value="<? echo ($testimonial_id !="") ? $testimonial->company : "" ?>" />
                
                <label for="quote">Quote:<span class="requiredindicator">*</span></label> 
                <textarea name="quote" class="required"><? echo ($testimonial_id !="" && isset($testimonial->quote)) ? $testimonial->quote : "" ?></textarea>
                
                <label for="order">Order:</label>
                <input type="text" name="order" value="<? echo ($testimonial_id !="") ? $testimonial->order : "" ?>" />
                
                <br class="clear" /><br/>
                  
               <input type="checkbox" name="enabled" value="1" class="left" <?php echo ($testimonial_id !="") ? (($testimonial->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Testimonial is enabled</label> 
               <br/>
            </div>
            <div class="clear"></div>       
    <?php if(isset($testimonial)) : ?>
        </div>
    </div>        
    <?php endif; ?>
    <br/>
    <br/>
              
    <label for="heading">&nbsp;</label> 
    <input id="button" type="submit" value="Save Testimonial" /><br/>                

    <input type="hidden" name="postback" value="1" />
    <input type="hidden" name="id" id="testimonial_id" value="<?php echo $testimonial_id?>" />
</form>


<p><!-- --></p>
<?php $this->load->view("admin/testimonial/navigation"); ?>
