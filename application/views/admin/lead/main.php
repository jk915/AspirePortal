<body id="contact" >   
   <div id="wrapper">
            
      <?php $this->load->view("admin/navigation");?>
		
      <div id="content">
		   <?php $this->load->view("admin/lead/navigation"); ?>
         
         <?php if((isset($warning)) && ($warning!="")) : ?>
            <div class="warning"><?=$warning?></div>
         <?php endif; ?>
         
		   <p><?php echo $message?></p>
         <form class="plain" id="frmLead" name="frmLead" action="<?php echo base_url()?>admin/leadsmanager/lead/<?php echo $lead_id?>"  method="post">
            <h2>Lead Properties</h2>
               
            <!-- tabs -->
            <ul class="css-tabs skin2">
               <li><a href="#">Lead Details</a></li>
            </ul>

            <!-- panes -->
            <div class="css-panes skin2">
               <div style="display:block">
            
            <div class="left">
            
                <label for="first_name">First Name:<span class="requiredindicator">*</span></label> 
                <input type="text" name="first_name" id="first_name" class="required" value="<?php print $form_values["first_name"]; ?>"/>
                
                <label for="first_name">Last Name:<span class="requiredindicator">*</span></label> 
                <input type="text" name="last_name" id="last_name" class="required" value="<?php print $form_values["last_name"]; ?>"/>
                
                <label for="email">Email address:<span class="requiredindicator">*</span></label> 
                <input type="text" name="email" id="email" class="required email" value="<?php print $form_values["email"]; ?>"/> 
                
                <label for="acn">ACN:</label> 
                <input type="text" name="acn" id="acn" value="<?php if(isset($lead->acn) && $lead->acn) echo $lead->acn; ?>"/>
                
                <label for="phone">Phone:</label> 
                <input type="text" name="phone" id="phone" class="" value="<?php if(isset($lead->phone)) echo $lead->phone; ?>"/> 
                
                <label for="mobile">Mobile:</label> 
                <input type="text" name="mobile" id="mobile" class="" value="<?php if(isset($lead->mobile)) echo $lead->mobile; ?>"/>
                
                <label for="fax">Fax:</label> 
                <input type="text" name="fax" id="fax" class="" value="<?php if(isset($lead->fax)) echo $lead->fax; ?>"/>
                
                <div class="clear"></div><br/>
            </div>
            <div class="left" style="padding-left: 20px;">
                <label for="legal_entity_name">Company Name:</label> 
                <input type="text" name="legal_entity_name" id="legal_entity_name" class="" value="<?php if(isset($lead->legal_entity_name)) echo $lead->legal_entity_name; ?>"/> 

                <label for="address1">Address 1:</label> 
                <input type="text" name="address1" id="address1" value="<?php if(isset($lead->address1)) echo $lead->address1; ?>"/> 
            
                <label for="address2">Address 2:</label> 
                <input type="text" name="address2" id="address2" value="<?php if(isset($lead->address2)) echo $lead->address2; ?>"/> 
            
                <div class="clear"></div>
                <div class="left right-margin">
                    <label for="suburb">City/Suburb:</label> 
                    <input type="text" name="suburb" id="suburb" value="<?php if(isset($lead->suburb)) echo $lead->suburb; ?>"/> 
                </div>
                <div class="left">
                    <label for="postcode">Postcode/Zip:</label> 
                    <input type="text" name="postcode" id="postcode" value="<?php if(isset($lead->postcode)) echo $lead->postcode; ?>"/> 
                </div>
                <div class="clear"></div>
                
                <label for="country">Country:<span class="requiredindicator">*</span></label> 
                <?php echo form_dropdown_countries('country', $lead->country, 'id="country" class="selector required"'); ?>
                
                <label for="state">State:<span class="requiredindicator">*</span></label> 
                <?php echo form_dropdown_states_by_country('state', $lead->country, $lead->state, 'id="state" class="selector required"'); ?>
            
                <label for="status">Status:<span class="requiredindicator">*</span></label> 
                <?php echo form_dropdown_status('status', $lead->status, 'id="status" class="selector required"'); ?>
            
                <div class="clear"></div>          
            
            </div>

            <div class="left" style="padding-left: 20px;">
            	<label for="status">Agent Name:</span></label> 
                <input disabled="disabled" value="<?=$agent->first_name. " " . $agent->last_name?>" /> 
            </div>
            
            <div class="clear"></div>          
            </div><!-- end user details tab -->
               

            </div><!-- end tabs -->
     
            
            <label>&nbsp;</label> 
            <input id="button" type="submit" value="<? echo ($lead_id == "") ? "Create New Lead": "Update Lead"?>" /><br/>    	    	

            <input type="hidden" name="postback" value="1" />
            <input type="hidden" name="id" id="id" value="<?=$lead_id?>" />
         </form>

         <br/>
         
         <? $this->load->view("admin/lead/navigation"); ?>
