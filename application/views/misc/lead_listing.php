<table width="100%" class="lato" id="wrapperleads">
    <thead>
        <tr style="color: #513171;">
            <th><input type="checkbox" class="chk"/></th>
            <th>
                Lead name
                <?php if($sort_column == 'name' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="name" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="name" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Company name
                <?php if($sort_column == 'company' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="company" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="company" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Location
                <?php if($sort_column == 'location' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="location" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="location" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Status
                <?php if($sort_column == 'status' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="status" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="status" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>No.Reservations</th>
        </tr>
    </thead>
    <tbody id="gridleads">
        <?php if($leads) : ?>
            <?php foreach ($leads->result() AS $index=>$lead) : ?>
        <tr <?php echo $index%2?'class="alt"':''?>>
            <td><input type="checkbox" name="todelete[]" value="<?php echo $lead->id;?>"/></td>
            <td><a href="<?php echo site_url("lead-detail/".$lead->id)?>" title="Lead detail"><?php echo !empty($lead->first_name) ? $lead->first_name." ".$lead->last_name : "";?></a></td>
            <td><?php echo !empty($lead->company_name) ? $lead->company_name : "";?></td>
            <td><?php echo $lead->suburb." ".$lead->state_name;?></td>
            <td><?php echo $lead->status;?></td>
            <td class="center"><?php echo ($lead->reservation_no != 0) ? $lead->reservation_no : "-";?></td>
        </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <tr>
            <td colspan="7">Sorry, no leads matched your search criteria.</td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="6">
                <select name="chooseaction" id="chooseaction">
                    <option value="0">Choose Action</option>
                    <option value="delete">Delete</option>
                </select>
            </td>
        </tr>
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