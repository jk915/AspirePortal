
<table width="100%">
    <thead>
        <tr style="color: #513171;">
            <th></th>
            <th>
                Lot
                <?php if($sort_column == 'lot' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="lot" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="lot" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Address
                <?php if($sort_column == 'address' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="address" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="address" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Estate
                <?php if($sort_column == 'project' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="project" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="project" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Price
                <?php if($sort_column == 'total_price' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="total_price" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="total_price" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Design
                <?php if($sort_column == 'design' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="design" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="design" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Size
                <?php if($sort_column == 'total_area' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="total_area" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="total_area" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Land
                <?php if($sort_column == 'internal_area' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="internal_area" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="internal_area" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Bed
                <?php if($sort_column == 'bedrooms' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="bedrooms" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="bedrooms" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Bath
                <?php if($sort_column == 'bathrooms' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="bathrooms" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="bathrooms" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Car
                <?php if($sort_column == 'garage' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="garage" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="garage" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <!--<th>
                Status
                <?php if($sort_column == 'status' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="status" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="status" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>-->
        </tr>
    </thead>
    <tbody>
        <?php if($properties) : ?>
            <?php foreach ($properties->result() AS $index=>$property) : ?>
                <?php
                    //$design = ($property->property_type_id == HOUSE_AND_LAND_TYPE) ? $property->design : $property->floor_plan_type;
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
        <tr <?php echo $index%2 ? 'class="alt '.$property->status.'"' : 'class="'.$property->status.'"'?>>
            <td class="color"></td>
            <td style="text-align:center !important;"><a href="<?php echo site_url("property-detail/".$property->property_id."?d=sl")?>" title="Property detail"><?php echo (($property->lot > 0 ) && ($property->hide_lot != 1)) ? $property->lot : "";?></a></td>
            <td><a href="<?php echo site_url("property-detail/".$property->property_id."?d=sl")?>" title="Property detail"><?php echo (($property->hide_address != 1) && !empty($property->address)) ? $property->address : "";?></a></td>
            <td><?php echo (!empty($property->project_name) && ($property->hide_project != 1)) ? $property->project_name : "";?></td>
            <td><?php echo (($property->total_price > 0 ) && ($property->hide_total_price != 1)) ? "$".number_format($property->total_price, 0, ".", ",") : "";?></td>
            <td><?php echo $property->design;?></td>
            <td><?php echo (($property->total_area > 0) && ($property->hide_total_area != 1)) ? number_format($property->total_area / 9.2762, 1)." sq" : ""?></td>
            <td><?php echo (($property->internal_area > 0) && ($property->hide_internal_area != 1)) ? $property->internal_area ." sqm" : ""?></td>
            <td><?php echo (($property->bedrooms > 0) && ($property->hide_bedrooms != 1)) ? $property->bedrooms : "";?></td>
            <td><?php echo (($property->bathrooms > 0) && ($property->hide_bathrooms != 1)) ? $property->bathrooms : "";?></td>
            <td><?php echo (($property->garage > 0) && ($property->hide_garage != 1)) ? $property->garage : "";?></td>
            <!--<td>
                <?php 
                    switch($property->status)
                    {
                      case "sold": 
                           echo "<b>Sold</b>";    
                      break;  
                      
                      case "reserved":
                           echo "<a href='".$property->property_id."' class='unreserve'>Unreserve</a>";
                      break;
                      
                      case "available":
                           echo "<b>Available</b>";
                      break;
                    }  
                ?>
            </td>-->
        </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <tr>
            <td colspan="11">Sorry, no properties matched your search criteria.</td>
        </tr>
        <?php endif; ?>
    </tbody>
</table>
<?php if($totalPages > 1) :?>
<div class="pagination">
    <?php if( $pageno > 1 ) : ?>
	<a class="page_numbers" href="javascript:;" p="<?php echo (intval($pageno)-1)?>">&lsaquo; <span>PREV</span></a>
    <?php endif; ?>
	<?php for( $i=1; $i<=$totalPages; $i++ ) : ?>
        <?php echo ( $i == $pageno ) ? '<strong>'.$i.'</strong>' : '<a class="page_numbers" p="'.$i.'" href="javascript:;"><span>'.$i.'</span></a>'; ?>
    <?php endfor; ?>
	<?php if( $pageno < $totalPages ) : ?>
        <a class="page_numbers" href="javascript:;" p="<?php echo (intval($pageno)+1)?>"><span>NEXT</span> &rsaquo;</a>
    <?php endif; ?>
</div>
<?php endif; ?>