<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>ID</th>                            
                <th>Author</th>                            
                <th>Company Name</th>                            
                <th>Order Item</th>                            
                <th style="width: 30px;">Delete</th> 
            </tr>
        <?php         
        $i = 0;
        $num_recs = $testimonials->num_rows();
        if($testimonials)
        {
            foreach($testimonials->result() as $row)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">
                    
                    <td class="admintabletextcell"><?php echo $row->id;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/testimonialmanager/testimonial/<?php echo $row->id;?>"><?php echo $row->author;?></a></td>
                    <td class="admintabletextcell"><?php echo $row->company;?></td>
                    <td class="admintabletextcell">
                        <?php
            				echo '<div class="left">' . $row->order . '</div>';
            
            				if($i > 1)
            				{
            					print '<a onclick="change_order(' . $row->id . ', \'up\');" href="javascript:void(0);" class="arrow-up left left-margin sprite top-margin-sm2"></a>';
            				}
            				
            				if($i < $num_recs)
            				{
            					print '<a onclick="change_order(' . $row->id . ', \'down\');" href="javascript:void(0);" class="arrow-down left left-margin sprite top-margin-sm2"></a>';
            				}
            			?>
                    </td>
                    <td class="center"><input type="checkbox" name="testimonialtodelete[]" value="<?php echo $row->id;?>" /></td>
                </tr>          
            <?
            }
        }
        ?>
</table>