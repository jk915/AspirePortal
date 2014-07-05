<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>Stage</th>            
                <th>Lot</th>            
                <th>Address</th>
                <th>Suburb</th>
                <th>State</th>
                <th>House Price</th>
                <th>Type</th>
                <th>Deisgn</th>
                <th>No. Beds</th>
                <th>No. Cars</th>
                <th>Title Due Date</th>
                <th>View Details</th>   
                <th style="width:20px"></th>             
            </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($properties)
        {
            foreach($properties->result() as $property)
            {
                if($i++ % 2==1) 
                	$rowclass = "admintablerow";
                else
                	$rowclass = "admintablerowalt";
                	
                $property_type = ($property->property_type_id == HOUSE_AND_LAND_TYPE) ? "House &amp; Land" : "Appartment/Town House";
                $design = ($property->property_type_id == HOUSE_AND_LAND_TYPE) ? $property->design : $property->floor_plan_type;
                
                	
                $due_date = $property->title_due_date;
                if(strlen($due_date) != 6)
                	$due_date = "";
                else
                {
					$year = substr($due_date, 0, 4);
					$month = substr($due_date, 4, 2);
					
					$due_date = $month . "/" . $year;
                }
            ?> 
                <tr class="<?=$rowclass;?>">
                    <td class="admintabletextcell"><?php echo $property->stage_no;?></td>
                    <td class="admintabletextcell"><?php echo $property->lot;?></td>
                    <td class="admintabletextcell"><?php echo $property->address;?></td>                
                    <td class="admintabletextcell"><?php echo $property->suburb;?></td>
                    <td class="admintabletextcell"><?php echo $property->state;?></td>
                    <td class="admintabletextcell"><?php echo "$".$property->house_price;?></td>
                    <td class="admintabletextcell"><?php echo $property_type;?></td>
                    <td class="admintabletextcell"><?php echo $design;?></td>
                    <td class="admintabletextcell"><?php echo $property->bedrooms;?></td>
                    <td class="admintabletextcell"><?php echo $property->garage;?></td>
                    <td class="admintabletextcell"><?php echo $due_date; ?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>property_listing/property/<?php echo $property->property_id;?>">View Property</a></td>                    
                    <td class="center"><?php 
                      switch($property->status)
                      {
                          case "sold": 
                               echo "<b>SOLD</b>";    
                          break;  
                          
                          case "reserved":
                               echo "<a href='".$property->property_id."' class='unreserve'>Unreserve</a>";
                          break;      
                      }  
                      ?></td>
                </tr>          
            <?
            }
        }
        ?>
</table>