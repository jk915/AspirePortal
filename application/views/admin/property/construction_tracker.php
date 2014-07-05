<?php if(($property->status != 'pending') && ($property->status != 'available') && ($property->status != 'work in progress')) :	?>
			<table cellspacing="0" width="100%" class="left commentlisting"> 
                <tr>
					<th>Status</th>	
                    <th>Address</th>
                    <th>Project</th>
					<th>Area</th>
                    <th>State</th>
					<th>Price</th>
                    <th>Builder Name</th>
                    
				</tr>
				
				<tr>
				
					<td class="admintabletextcell" align="center"> <a href="#status_area" id="changestatus"><?php echo $property->status; ?></a> </td>
					<td class="admintabletextcell" align="center"><?php echo $property->address;?></td>
            
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('admin/projectmanager/project/'.$property->project_id)?>"><?php echo $property->project_name;?></a>
					</td>	
					
					<td class="admintabletextcell" align="center"><?php echo $property->area_name;?></td>
					
					<td class="admintabletextcell" align="center"><?php echo $property->state;?></td>
					
					<td class="admintabletextcell" align="center"><?php echo '$'.''.$property->total_price;?></td>
					
					<td class="admintabletextcell" align="center"><?php echo $property->builder_name;?></td>
					
				</tr>	
			</table>
			
			<table cellspacing="0" width="100%" class="left commentlisting"> 
                <tr>
						
                    <th>Date Reserved</th>
                    <th>Advisor</th>
					<th>Partner</th>
                    <th>Purchaser</th>
					<th>Financier</th>
                    <th>Solicitor</th>
                    <th>Other Contact</th>
				</tr>

				<tr>
					<td class="admintabletextcell" align="center"><?php echo ($property->reserved_date == '0000-00-00') ?  'N/A' : date("d-m-Y", strtotime($property->reserved_date)); ?></td>
            
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('admin/usermanager/user/'.$property->advisor_id)?>"><?php echo !empty($property->advisor_fullname) ? $property->advisor_fullname : 'N/A'?></a>
					</td>	
					
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('admin/usermanager/user/'.$property->advisor_id)?>"><?php echo !empty($property->partner_fullname) ? $property->partner_fullname : 'N/A'?></a>
					</td>
					
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('admin/usermanager/user/'.$property->advisor_id)?>"><?php echo !empty($property->investor_fullname) ? $property->investor_fullname : 'N/A'?></a>
					</td>
					
					<td class="admintabletextcell" align="center">
					<select id="contact_id_Financier" name="contact_id_Financier" class="required" style="width: 100px;">			
                        <?php if($builders) : ?>
	                        <?php foreach($builders->result() as $builder) : $contact_id = $builder->contacts_id; ?>
		                    <option <?php echo ($property->financier_id == $contact_id ? 'selected' : ''); ?> value="<?php echo $contact_id; ?>"><?php echo $builder->first_name.' '.$builder->last_name; ?></option>		
                            <?php endforeach; ?>
                        <?php endif; ?>
		            </select>
					</td>
					
					<td class="admintabletextcell" align="center">
						<select id="contact_id_Solicitor" name="contact_id_Solicitor" class="required" style="width: 100px;">			
						<?php if($builders) : ?>
							<?php foreach($builders->result() as $builder) : $contact_id = $builder->contacts_id; ?>
							<option <?php echo ($property->solicitor_id == $contact_id ? 'selected' : ''); ?> value="<?php echo $contact_id; ?>"><?php echo $builder->first_name.' '.$builder->last_name; ?></option>		
							<?php endforeach; ?>
						<?php endif; ?>
						</select>
					</td>
					
					<td class="admintabletextcell" align="center">
						<select id="contact_id_others" name="contact_id_others" class="required" style="width: 100px;">			
							<?php if($builders) : ?>
								<?php foreach($builders->result() as $builder) : $contact_id = $builder->contacts_id; ?>
								<option <?php echo ($property->other_contact_id == $contact_id ? 'selected' : ''); ?> value="<?php echo $contact_id; ?>"><?php echo $builder->first_name.' '.$builder->last_name; ?></option>		
								<?php endforeach; ?>
							<?php endif; ?>	
						</select>
					</td>
					
				</tr>
			</table>	
			
			<table cellspacing="0" width="100%" class="left keydatelisting">
				<tr>
					<th> Id </th>
					<th> Description </th>
					<th> Estimated Date </th>
					<th> Actual Date </th>
					<th> Follow Up Date </th>
					<th> Delete </th>
				</tr>
				
				
				<?php if ($keydates) : ?>
				<?php foreach ($keydates->result() AS $index=>$keydate) : ?>
				<tr>
									
					<td class="admintabletextcell" align="center"><a href="javascript:;" rel="<?php echo $keydate->id; ?>" class="editkeydate" id="editkeydate"><?php echo $keydate->id;?></a></td>
					 
					 <td class="admintabletextcell" align="center"><?php echo $keydate->description;?></a></td>
					 
					 <td class="admintabletextcell" align="center"><?php echo date("d-m-Y", strtotime($keydate->estimated_date));?></td>
					 <td class="admintabletextcell" align="center"><?php echo date("d-m-Y", strtotime($keydate->actual_date));?></td>
					  <td class="admintabletextcell" align="center"><?php echo date("d-m-Y", strtotime($keydate->followup_date));?></td>
					 <td class="center"><input type="checkbox" class="keydatetodelete" value="<?php echo $keydate->id;?>" /></td>
				</tr>		
				<?php endforeach; ?>
        <?php endif; ?>
			</table>
			
			<table align="right">
				<a href="javascript:;" class="button right center" id="deletekeydate">Delete</a>
				<a href="javascript:;" class="button right center" id="addkeydate">Add Key Date</a>
				
			</table>
			<table>
				<tr> &nbsp; </tr>
				<tr> &nbsp; </tr>
			</table>
			
			<div id="formaddkeydates" style="display:none;">
            
                <label for="description">Description:<span class="requiredindicator">*</span></label>
                <textarea id="description" style="width:400px;"></textarea>
                 <label for="estimate_date">Estimated Date:<span class="requiredindicator">*</span></label>
                <input type="text" readonly="readonly" class="date-choose" value="" id="estimate_date" name="estimate_date" /> <br/> <br/>  
				<label for="actual_date">Actual Date:<span class="requiredindicator">*</span></label>
                <input type="text" readonly="readonly" class="date-choose" value="" id="actual_date" name="actual_date" /> <br/> <br/> 
				<label for="followup_date">Follow Up Date:<span class="requiredindicator">*</span></label>
                <input type="text" readonly="readonly" class="date-choose" value="" id="followup_date" name="followup_date" />				
                <input type="hidden" id="keydate_id"/>
                                
                <div class="clear"></div><br />
                <a href="javascript:;" class="button left center savekeydate">Save</a>
                
            </div>
			
			<?php endif;?>

<table cellspacing="0" width="100%" class="left stagelisting"> 
        <tr>
            <th width="10%">Stage</th> 
            <th align="left">Stage</th>
            <th align="center">Status</th>
            <th align="center">Public</th>            
            <th align="left">Next Followup</th>
            <th align="left">Date Completed</th>
            <!--<th width="10%"></th>-->
        </tr>
<?php $i = 0;?>
<?php if ($stages) : ?>
    <?php $num_recs = $stages->num_rows();?>
    <?php foreach ($stages->result() AS $row) : ?>
        <?php
            if($i++ % 2==1) $rowclass = "admintablerow";
            else  $rowclass = "admintablerowalt";
        ?>
        <tr class="<? print $rowclass;?>">
            <td class="admintabletextcell" align="center"><?php echo $row->stage_no;?></td>
            
            <td class="admintabletextcell" style="padding-left:12px;">
                <a href="<?php echo site_url('admin/propertymanager/stage/'.$row->id)?>"><?php echo $row->stage_name;?></a>
            </td>
            
            <td align="center"><?php echo ucwords($row->status);?></td>
            
            <td class="admintabletextcell" style="padding-left:12px;">
                <?php echo $row->public;?>
            </td> 
            
            <td style="padding-left:12px;">
                <?php echo !empty($row->next_followup_date) ? date('d/m/Y',strtotime($row->next_followup_date)) : ''?>
            </td>                        
            
            <td style="padding-left:12px;">
                <?php if ($row->status == "completed") : ?>
                    <?php echo !empty($row->datetime_completed) ? date('d/m/Y',strtotime($row->datetime_completed)) : ''?>
                <?php endif; ?>
            </td>

            <!--<td class="center"><input type="checkbox" class="stage_to" value="<?php echo $row->id;?>" /></td>-->
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
    </table>
    
    <div class="clear"></div>
    
    <!--
    <a href="javascript:;" class="button right center" id="complete" style="margin-left:10px;">Completed</a>
    <a href="javascript:;" class="button right center" id="delete" style="margin-left:10px;">Delete</a>
    -->
    
    <div class="clear"></div>