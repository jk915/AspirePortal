<table cellspacing="0" class="cmstable"> 
    <tr>
        <th>Request No.</th> 
        <th>Date</th>
        <th>Quote No.</th>                            
        <th>Agent</th>                            
        <th>Lead</th>
        <th>Total</th>                            
        <th>Status</th>
    </tr>
    <? /* Setup alternating row colours, using the variable "rowclass" */ 
    $i = 0;
    if($requests)
    {
        foreach($requests->result() as $request)
        {
            if($i++ % 2==1) $rowclass = "admintablerow";
            else  $rowclass = "admintablerowalt";
        ?> 
            <tr class="<?php echo $rowclass;?>">
                <td class="admintabletextcell"><a href="<?php echo base_url();?>admin/contractrequests/request/<?php echo $request->id;?>"><?php echo $request->id;?><?php if($request->deleted) echo '*'; ?></a></td>
                <td class="admintabletextcell"><?php echo date("d/m/Y",strtotime($request->quote_date));?></td>
                <td class="admintabletextcell"><?php echo $request->quote_number;?></td>
                <td class="admintabletextcell"><a href="<?php echo site_url("admin/usermanager/user/".$request->agent_id);?>"><?php echo !empty($request->agent_first_name) ? $request->agent_first_name." ".$request->agent_last_name : "";?></a></td>
                <td class="admintabletextcell"><?php echo !empty($request->lead_first_name) ? $request->lead_first_name." ".$request->lead_last_name : "";?></td>
                <td class="admintabletextcell"><?php echo !empty($request->total_price) ? "$".number_format($request->total_price, 0, ".", ",") : "";?></td>
                <td class="admintabletextcell"><?php echo $request->status;?></td>
            </tr>          
        <?
        }
    } else {
    ?>
            <tr>
                <td colspan="7">Sorry, you don't have any contract requests added.</td>
            </tr>  
    <?php }?>
</table>
<?php if($totalPages > 1) :?>
<div id="pagination" class="jPaginate">
    <?php if( $pageno > 1 ) : ?>
	<a class="page_numbers" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204);" href="javascript:;" p="<?php echo (intval($pageno)-1)?>">«</a>
    <?php endif; ?>
    <ul class="jPag-pages">
        <?php for( $i=1; $i<=$totalPages; $i++ ) : ?>
            <?php echo ( $i == $pageno ) ? '<li><span class="jPag-current" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204);">'.$i.'</span></li>' : '<li><a style="color: rgb(255, 255, 255); background-color: black; border: 1px solid rgb(255, 255, 255);" href="javascript:;" p="'.$i.'" class="page_numbers">'.$i.'</a></li>'; ?>
        <?php endfor; ?>
    </ul>
    <?php if( $pageno < $totalPages ) : ?>
    <a class="page_numbers" style="color: rgb(0, 0, 0); background-color: rgb(255, 255, 255); border: 1px solid rgb(204, 204, 204);" href="javascript:;" p="<?php echo (intval($pageno)+1)?>">»</a>
    <?php endif; ?>
</div>
<?php endif;?>