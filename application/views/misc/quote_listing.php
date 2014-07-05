<table width="100%" class="lato" id="wrapperquotes">
    <thead>
        <tr style="color: #513171;">
            <th><input type="checkbox" class="chk"/></th>
            <th>
                Quote No.
                <?php if($sort_column == 'quoteno' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="quoteno" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="quoteno" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <!--<th>
                Lead
                <?php if($sort_column == 'lead' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="lead" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="lead" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>-->
            <th>
                Date
                <?php if($sort_column == 'quotedate' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="quotedate" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="quotedate" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Client
                <?php if($sort_column == 'client' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="client" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="client" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
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
                Amount
                <?php if($sort_column == 'price' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="price" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="price" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
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
        </tr>
    </thead>
    <tbody id="gridquotes">
        <?php if($quotes) : ?>
            <?php foreach ($quotes->result() AS $index=>$quote) : ?>
        <tr <?php echo $index%2?'class="alt"':''?>>
            <td><input type="checkbox" name="todelete[]" value="<?php echo $quote->id;?>"/></td>
            <td><a href="<?php echo site_url("quote-details/".$quote->id)?>" title="Lead detail"><?php echo !empty($quote->quote_number) ? $quote->quote_number : "";?></a></td>
            <!--<td><?php echo !empty($quote->company_name) ? $quote->company_name : ""; ?></td>-->
            <td><?php echo date("d/m/Y",strtotime($quote->quote_date));?></td>
            <td><?php echo !empty($quote->lead_first_name) ? $quote->lead_first_name." ".$quote->lead_last_name : "";?></td>
            <td>
                <?php
                    if (!empty($quote->design)) {
                    	echo $quote->design;
                    } else {
                        echo "NA / Custom";
                    }
                ?>
            </td>
            <td><?php echo !empty($quote->total_price) ? "$".number_format($quote->total_price, 0, ".", ",") : ""?></td>
            <td><?php echo $quote->status;?></td>
        </tr>
            <?php endforeach; ?>
        <?php else : ?>
        <tr>
            <td colspan="7">Sorry, no quotes matched your search criteria.</td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="7">
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