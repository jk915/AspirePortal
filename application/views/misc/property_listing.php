<table width="100%" class="lato">
    <thead>
        <tr style="color: #513171;">
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
                Suburb
                <?php if($sort_column == 'suburb' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="suburb" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="suburb" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
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
                Lead
            </th>
            <th>
                Status
                <?php if($sort_column == 'status' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="status" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="status" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
        </tr>
    </thead>
    <tbody>
        <?php if($properties) : ?>
            <?php foreach ($properties->result() AS $index=>$property) : ?>
        <tr <?php echo $index%2?'class="alt"':''?>>
            <td><a href="<?php echo site_url("property-detail/".$property->property_id."?d=mp")?>" title="Property detail"><?php echo (($property->lot > 0 ) && ($property->hide_lot != 1)) ? $property->lot : "";?></a></td></a></td>
            <td><a href="<?php echo site_url("property-detail/".$property->property_id."?d=mp")?>" title="Property detail"><?php echo (($property->hide_address != 1) && !empty($property->address)) ? $property->address : "";?></a></td>
            <td><?php echo (!empty($property->suburb) && ($property->hide_suburb != 1)) ? $property->suburb : "";?></td>
            <td><?php echo (!empty($property->project_name) && ($property->hide_project != 1)) ? $property->project_name : "";?></td>
            <td><?php echo (($property->total_price > 0 ) && ($property->hide_total_price != 1)) ? "$".number_format($property->total_price, 0, ".", ",") : "";?></td>
            <td><?php echo !empty($property->lead_first_name) ? "<a href='".site_url("lead-detail/".$property->lead_id)."'>".$property->lead_first_name." ".$property->lead_last_name."</a>" : "";?></a></td>
            <td>
                <?php 
                    switch($property->status)
                    {
                      case "sold": 
                           echo "<b>Sold</b>";    
                      break;  
                      
                      case "reserved":
                           echo "<a href='".site_url("postback/unreserved/".$property->property_id)."' class='unreserve'>Unreserve</a>";
                      break;
                      
                      case "available":
                           echo "<b>Available</b>";
                      break;
                    }  
                ?>
            </td>
        </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <tr>
            <td colspan="7">Sorry, no properties matched your search criteria.</td>
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