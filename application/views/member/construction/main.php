<body class="construction">

	<div id="wrapper">
        <?php $this->load->view("member/page_header"); ?>  
                  
    <div id="main">  
        <div class="content">

        <ul class="breadcrumbs">
            <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
            <li><a href="<?php echo base_url(); ?>stocklist">Stocklist</a></li>
            <li><a href="<?php echo base_url(); ?>stocklist/detail/<?=$property->property_id?>">Lot <?php echo $property->lot . ', ' . $property->address;?></a></li>
            <li>Construction Tracker</li>
        </ul>                
        <h1>Construction Tracker</h1>
        <?php if(($property->status != 'pending') && ($property->status != 'available') && ($property->status != 'work in progress')) :	?>
			<table cellspacing="0" width="100%" class=""> 
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
				
					<td class="admintabletextcell" align="center"><?php echo $property->status; ?> </td>
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('stocklist/detail/'.$property->property_id)?>"><?php echo $property->address;?></td>
            
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('projects/detail/'.$property->project_id)?>"><?php echo $property->project_name;?></a>
					</td>	
					
					<td class="admintabletextcell" align="center">
					<a href="<?php echo site_url('areas/detail/'.$property->area_id)?>"><?php echo $property->area_name;?></a></td>
					
					<td class="admintabletextcell" align="center"><?php echo $property->state;?></td>
					
					<td class="admintabletextcell" align="center"><?php echo '$'.''.$property->total_price;?></td>
					
					<?php
					if($property->display_on_front_end != "0")
					{
					?>
                    <td class="admintabletextcell" align="center"><?=$property->builder_name; ?></td>
					<?php
					}
					else
					{
					?>
					<td class="admintabletextcell" align="center">TBA</td>
					<?php
					}
					?>
										
				</tr>	
			</table>  
			
			<form method="post" action="<?php echo base_url(); ?>construction/update_contacts/<?=$property->property_id?>">
			<table cellspacing="0" width="100%" class=""> 
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
						<?php echo !empty($property->advisor_fullname) ? $property->advisor_fullname : 'N/A'?>
					</td>	
					
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('partners/detail/'.$property->partner_id)?>"><?php echo !empty($property->partner_fullname) ? $property->partner_fullname : 'N/A'?></a>
					</td>
					
					<td class="admintabletextcell" align="center">
						<a href="<?php echo site_url('leads/detail/'.$property->investor_id)?>"><?php echo !empty($property->investor_fullname) ? $property->investor_fullname : 'N/A'?></a>
					</td>
					
					<td class="admintabletextcell" align="center">
					<select id="contact_id_Financier" name="contact_id_Financier" class="required" style="width: 100px;">
						<option> Select </option>
					<?php foreach($builders->result() as $builder) : 
						$contact_id = $builder->contacts_id;
					?>
							<option <?php echo ($property->financier_id == $contact_id ? 'selected' : ''); ?> value="<?php echo $contact_id; ?>"><?php echo $builder->first_name.' '.$builder->last_name; ?></option>		
					<?php
							
						endforeach; 
					?>
					</select>
					</td>
					
					<td class="admintabletextcell" align="center">
					<select id="contact_id_Solicitor" name="contact_id_Solicitor" class="required" style="width: 100px;">
						<option> Select </option>
					<?php foreach($builders->result() as $builder) : 
						$contact_id = $builder->contacts_id;
					?>
							<option <?php echo ($property->solicitor_id == $contact_id ? 'selected' : ''); ?> value="<?php echo $contact_id; ?>"><?php echo $builder->first_name.' '.$builder->last_name; ?></option>		
					<?php
							
						endforeach; 
					?>
					</select>
					</td>
					
					<td class="admintabletextcell" align="center">
					<select id="contact_id_others" name="contact_id_others" class="required" style="width: 100px;">
						<option> Select </option>
					<?php foreach($builders->result() as $builder) : 
						$contact_id = $builder->contacts_id;
					?>
							<option <?php echo ($property->other_contact_id == $contact_id ? 'selected' : ''); ?> value="<?php echo $contact_id; ?>"><?php echo $builder->first_name.' '.$builder->last_name; ?></option>		
					<?php
							
						endforeach; 
					?>
					</select>
					</td>
	

				</tr>
				
			<table>
			<p align="right">
			<input type="submit" name="submit" value="Save" class="button btn-blue">
			
			<input type="hidden" name="" value="<?php echo $property->property_id; ?>">
			</p>
			</form>
			
			<h1>Key Dates</h1>
			<table cellspacing="0" width="100%" class="left keydatelisting">
				<tr>
					<th> Id </th>
					<th style="width:350px;"> Description </th>
					<th> Actual Date </th>
					<th> Estimated Date </th>
					<th> Follow Up Date </th>
					
				</tr>
				
				
				<?php if ($keydates) : ?>
				<?php foreach ($keydates->result() AS $index=>$keydate) : ?>
				<tr>
						<!--<a href="javascript:;" rel="<?php //echo $keydate->id; ?>" class="editkeydate" id="editkeydate"> </a>-->					
					<td class="admintabletextcell" align="center"><?php echo $keydate->id;?></td>
					 
					 <td class="admintabletextcell" align="center"><?php echo $keydate->description;?></a></td>
					 <td class="admintabletextcell" align="center"><?php echo date("d-m-Y", strtotime($keydate->datetime_added));?></td>
					 <td class="admintabletextcell" align="center"><?php echo date("d-m-Y", strtotime($keydate->estimated_date));?></td>
					  <td class="admintabletextcell" align="center"><?php echo date("d-m-Y", strtotime($keydate->followup_date));?></td>
					 
				</tr>		
				<?php endforeach; ?>
        <?php endif; ?>
			</table>
			
			<!--<p align="right">
				
				<a href="javascript:;" class="button btn-blue" id="addkeydate">Add Key Date</a>
				<a href="javascript:;" class="button btn-red" id="deletekeydate">Delete</a>
				
			</p>-->
			<table>
				<tr> &nbsp; </tr>
				<tr> &nbsp; </tr>
			</table>
			
			<div id="formaddkeydates" style="display:none; width:280px; height:300px;">
            
                <label for="description">Description:<span class="requiredindicator">*</span></label>
                <textarea id="description" style="width:250px; height:100px;"></textarea>
                 <label for="estimate_date">Estimated Date:<span class="requiredindicator">*</span></label>
                <input type="text" readonly="readonly" class="date-choose" value="" id="estimate_date" name="estimate_date" /> <br/> <br/>  
				<label for="followup_date">Follow Up Date:<span class="requiredindicator">*</span></label>
                <input type="text" readonly="readonly" class="date-choose" value="" id="followup_date" name="followup_date" />				
                <input type="hidden" id="keydate_id"/>
                <input type="hidden" id="property_id" value="<?=$property->property_id?>"/>  
                <div class="clear"></div><br />
                <a href="javascript:;" class="button btn-black savekeydate">Save</a>
                
            </div>
			
			<?php endif;?>
		
        <div class="sidebar">
        <?php echo form_open('construction/ajax', array("id" => "frmSearch", "name" => "frmSearch")); ?>
         <h3><a style="text-decoration: none;" href="<?php echo base_url(); ?>stocklist/detail/<?=$property->property_id?>">Lot <?php echo $property->lot . ', ' . $property->address. ', ' .$property->suburb.' '.$property->pstate;?></a></h3>                      
       
            <table>
            <tr>
                <th>Completed</th>
                <th>Stage</th>
                <th>Completion Date</th>
            </tr>

        <?php 
            //print_r($property_stages->result());die();
            $permission = "-1";
            if(isset($user) && $user)
                $permission = $user->user_type_id;
            
            $completed = "";
            $row = 0;
            if(isset($property_stages) && $property_stages)
            {
                foreach($property_stages->result() as $item)
                {
                    $row ++;
                    if($item->status == "completed")
                        $completed = "Yes";
                    else
                        $completed = "No";
                    $completed = ucfirst($item->status);
        ?>
            <tr <?php if($item->status != "pending") echo 'class="view_stage"'; ?> id="<?=$item->id;?>" <?php if($row  % 2 == 0)    echo  'bgcolor="#EFEFEF"';?>>
                <td class="<?php if($completed == "Yes") echo "tick"; ?>" ><?=$completed;?></td>
                <td><?=$item->stage_name;?></td>
                <td>
                    <?php 
                        if($item->ts_date != "") echo date('d/m/Y',$item->ts_date); 
                        if($item->status != "pending") echo ' View Details';
                    ?>    
                </td>
            </tr>
        <?php            
        
                }
            }
        
        ?>
            </table>
        <!-- end sidebar --></div>                
        
        <div class="mainCol">
            <span class="head3">Construction Notes</span> <a href="javascript:;" class="btn-green" id="newcomment">New Note</a>
            <table cellspacing="0" width="100%" class="left commentlisting"> 
                <th style="width: 20%;">Date</th>
                <th>Comment</th>
			<?php if($comments): ?>
				<?php foreach($comments->result() AS $index=>$comment): ?>
				<tr>
					<td><?=date('d/m/Y', $comment->ts_added);?></td>
					<td><?=nl2br($comment->comment);?></td>
				</tr>
				<?php endforeach; ?>
			<?php endif; ?> 
			</table>               
        </div>      
        
		<div id="formnewcomment" style="display:none; width:250px; height:200px;">
            
                <label for="comment">Note:<span class="requiredindicator">*</span></label>
                <textarea id="comment" style="width:220px; height:100px;"></textarea>
                
                <input type="hidden" id="comment_id"/>
				<input type="hidden" id="property_id" value="<?=$property->property_id?>"/>
                <br /><br />
                              
                <div class="clear"></div><br />
                <a href="javascript:;" class="button btn-black savecomment">Save</a>
                
            </div>
		
		
        <div id="stage_details" style="display:none;">
        
            
        </div>        
                  
    </div><!-- end main content -->