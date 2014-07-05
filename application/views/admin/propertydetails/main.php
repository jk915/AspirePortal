<body>
    <?php $this->load->view("page_header");?>    
   
    <img id="hPic" src="<?php echo base_url();?>images/h_listings.jpg" border="0" width="920" height="140" alt=" " />      
        
    <div id="main">               
        
         <h1><?php if($property) echo $property->lot." ".$property->address;?> <?php if($property->status == "sold") echo " (Sold)";?></h1> 
         <input type="hidden" id="property_id" value="<?php echo $property_id;?>" />
         <div class="left" style="width:270px">
            <?php if($property_id != "" && ($property->bathrooms != "-1" || $property->bedrooms != "-1" || $property->garage != "-1" || $property->total_area != "-1" || $property->land != "-1")) { ?>
            <h2>Property Specifications</h2>
            <?php } ?>
            
            <?php if($property_id != "" && $property->bedrooms != "-1") { ?>
            <label>No. Bedrooms: <?php echo $property->bedrooms; ?></label>                        
            <?php } ?>            
            
            <?php if($property_id != "" && $property->bathrooms != "-1") { ?>
            <label>No. Bathrooms:  <?php echo $property->bathrooms; ?></label>
            <?php } ?>

            <?php if($property_id != "" && $property->garage != "-1") { ?>
            <label>Garage: <?php echo $property->garage; ?></label>            
            <?php } ?>             
            
            <?php if($property_id != "" && $property->total_area != "-1") { ?>
            <label>Total Area(sqm): <?php echo $property->total_area;?></label>
            <?php } ?>
                
            <?php if($property_id != "" && $property->land != "-1") { ?>                    
            <label>Land(sqm): <?php echo $property->land;?></label>
            <?php } ?>
            
            <?php if($property_id != "" && ($property->land_price != "-1" || $property->house_price != "-1" || $property->total_price != "-1")) { ?>                    
            <div class="top-margin">&nbsp;</div>
            <h2>Pricing &amp; Documents</h2>
            <?php } ?>
            
            <?php if($property_id != "" && $property->land_price != "-1") { ?>                    
            <label>Land Price $: <?php echo $property->land_price; ?></label>
            <?php } ?>
            
            <?php if($property_id != "" && $property->house_price != "-1") { ?>                    
            <label>House Price $: <?php echo $property->house_price; ?></label>
            <?php } ?>
            
            <?php if($property_id != "" && $property->total_price != "-1") { ?>                    
            <label>Total Price $: <?php echo $property->total_price; ?></label>
            <?php } ?>
            
            <div class="documents">
                <?php
                
                if($project_documents)
                {
                    ?>
                    <h2>Project Documents</h2>
                    <?php
                    foreach($project_documents->result() as $doc)
                    {           
                        if($doc->document_path != "")                                               
                        {
                    ?>
                        <div>
                            <a class="download" href="<?php echo $doc->document_path;?>"><input type="button" value="<?php echo $doc->document_name;?>" class="button" /></a>
                        </div>    
                    <?php
                        }
                    }
                }
                ?>
                
                <?php
                if($property_documents)
                {
                    ?>
                    <h2>Property Documents</h2>
                    <?php
                    foreach($property_documents->result() as $doc)
                    {           
                        if($doc->document_path != "")                                               
                        {
                    ?>
                        <div>
                            <a class="download" href="<?php echo $doc->document_path;?>"><input type="button" value="<?php echo $doc->document_name;?>" class="button" /></a>
                        </div>    
                    <?php
                        }
                    }
                }
                ?>
            </div>
            
         </div>
         
         <div class="left">
            
            <div class="right">
                <a href="<?php echo base_url()."brochure/".$property_id;?>" style="border:0px"><input type="button" value="Print Report" class="button" /></a>
            </div>
            
            <div class="clear"></div>
            
            <?php 
                if($images)
                {
                 ?>
                <div class="pikachoose">
                    <ul id="pikachoose">
                        <?php   
                        $max_images = (count($images) > 10) ? 10 : count($images);
                        for($i = 0; $i < $max_images; $i++)            
                        {
                        ?>
                          <li><img src="<?php echo base_url().PROPERTY_FILES_FOLDER.$property_id."/images/".$images[$i];?>" alt="" /></li>  
                        <?php                        
                        } 
                        ?>               
                    </ul>
                 </div>                  
                 <?php
                 } 
             ?>
                
            <?php 
                if($property->status != "sold") 
                { 
                    ?>
                    <div class="<?php echo ($isReserved) ?  "big_button_clicked" : "big_button";?> left" id="reserve"><span><?php echo ($isReserved) ? "Reserved!" : "Reserve this property";?></span></div>
                    <?php 
                }             
            ?>    
            
            <?php 
                if($project_code)
                { 
                    ?>
                    <div class="big_button left"><a href="<?php echo base_url()."project/".$project_code;?>">View Project</a></div>
                    <?php
                }
            ?>
            
            <div class="clear"></div>
         </div>
         <div class="clear"></div>                                          
                  

      <!-- end main div --></div>    
      