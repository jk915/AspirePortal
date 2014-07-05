<table width="100%" class="lato" id="wrapperrequests">
    <thead>
        <tr style="color: #513171;">
            <th><input type="checkbox" class="chk"/></th>
            <th>
                Req No.
                <?php if($sort_column == 'request_number' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="request_number" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="request_number" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Date
                <?php if($sort_column == 'date' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="date" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="date" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Quote
                <?php if($sort_column == 'quote' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="quote" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="quote" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Lead
                <?php if($sort_column == 'lead' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="lead" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="lead" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Agent
                <?php if($sort_column == 'agent' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="agent" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="agent" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
                <?php endif;?>
            </th>
            <th>
                Total
                <?php if($sort_column == 'total' && $sort_order == 'asc') : ?>
                    <a href="javascript:;" class="sorting" col="total" order="desc"><img src="<?php echo base_url()."images/order_arrows_desc.png";?>"/></a>
                <?php else :?>
                    <a href="javascript:;" class="sorting" col="total" order="asc"><img src="<?php echo base_url()."images/order_arrows_asc.png";?>"/></a>
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
            <th>Download</th>
        </tr>
    </thead>
    <tbody id="gridrequests">
        <?php if($requests) : ?>
            <?php 
                $last_id = -1;
                $docs_open = false;
                $row = 0;
                
                foreach ($requests->result() AS $index=>$request)
                {
                    if($request->id != $last_id)
                    {
                        $last_id = $request->id; 
                        $row++;
                        
                        if($docs_open)
                        {
                            $docs_open = false;
                            ?>
            </td>
        </tr>                            
                            <?php   
                        }
                    ?>
        <tr <?php echo $row%2?'class="alt"':''?>>
            <td><input type="checkbox" name="todelete[]" value="<?php echo $request->id;?>"/></td>
            <td><a href="<?php echo site_url("contract-details/".$request->id)?>" title="view contract request detail."><?php echo !empty($request->id) ? $request->id : "";?></a></td>
            <td><?php echo !empty($request->request_date) ? date("d/m/Y",strtotime($request->request_date)) : "";?></td>
            <td><a href="<?php echo site_url("quote-details/".$request->quote_id)?>" title="view quote detail."><?php echo !empty($request->quote_number) ? $request->quote_number : "";?></a></td>
            <td><a href="<?php echo site_url("lead-detail/".$request->lead_id)?>" title="view lead detail."><?php echo !empty($request->lead_first_name) ? $request->lead_first_name." ".$request->lead_last_name : "";?></td>
            <td><?php echo !empty($request->agent_first_name) ? $request->agent_first_name." ".$request->agent_last_name : "";?></td>
            <td><?php echo !empty($request->total_price) ? "$".number_format($request->total_price, 0, ".", ",") : "";?></td>
            <td><?php echo ($request->status == "OldVersion") ? "Old Version" : $request->status;?></td>
            <?php if(!$docs_open) : ?>
            <td class="center">
            <?php
                $docs_open = true;
                endif; 
            ?>
                <?php if (!empty($request->contract_document_id)) :?>
                <a href="<?php echo site_url("postback/download_contract/".$request->contract_document_id)?>" style="padding-left:5px;"><img src="<?php echo site_url("images/i_pdf.png");?>""/></a>
                <?php else :?>
                -
                <?php endif;?>
            <?php
                    }
                    else
                    {
                        if (!empty($request->contract_document_id))
                        {
                            ?>
                            <a href="<?php echo site_url("postback/download_contract/".$request->contract_document_id)?>" style="padding-left:5px;"><img src="<?php echo site_url("images/i_pdf.png");?>""/></a>
                            <?php
                        }       
                    }
                }
                
                if($docs_open)
                {
                    ?>
            </td>
        </tr>                    
                    <?php
                }
            ?>
        <?php else : ?>
        <tr>
            <td colspan="9">Sorry, no contract requests matched your search criteria.</td>
        </tr>
        <?php endif; ?>
        <tr>
            <td colspan="9">
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