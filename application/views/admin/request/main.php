<body id="contact" >   
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/request/navigation"); ?>                        
            
            <form class="plain" id="frmRequest" name="frmRequest" action="<?php echo base_url()?>admin/contractrequests/request/<?php echo $request_id?>"  method="post" enctype="multipart/form-data">
                <input type="hidden" id="id" value="<?php echo $request_id;?>" />
     
<?php

if(isset($request))
{
    // We're editing and existing request.  Show the tabs.
    ?>
                
                <!-- tabs -->
                <ul class="css-tabs skin2">
                    <li><a href="#">Contract Request Details</a></li>
                    <li><a href="#">Additional Items</a></li>
                    <li><a href="#" id="tabUpload">Contract Document</a></li>
                    <!--<li><a href="#" id="tabUploadInclusion">Inclusion List</a></li>
                    <li><a href="#" id="tabUploadPlans">Plans</a></li>-->
                </ul>   
                
                <!-- panes -->
                <div class="css-panes skin2">
                    <div style="display:block">
    <?php
}
?>
                        <div class="left">
                        	<label for="id">Request Number:</label>
                        	<input type="text" value="<?php echo ($request_id !="") ? $request->id : ""; ?>" readonly/>
                        	<div class="clear"></div>
                        	<label for="id">Quote date:</label>
                        	<input type="text" value="<?php echo ($request_id !="") ? date("d-m-Y",strtotime($request->quote_date)) : ""; ?>" readonly/>
                        	<div class="clear"></div>
                        	<label for="id">Agent:</label>
                        	<input type="text" value="<?php echo ($request_id !="") ? $request->agent_first_name." ".$request->agent_last_name : ""; ?>" readonly/>
                        	<div class="clear"></div>
                        	<label for="id">Lead:</label>
                        	<input type="text" value="<?php echo ($request_id !="") ? $request->lead_first_name." ".$request->lead_last_name : ""; ?>" readonly/>
                        	<div class="clear"></div>
                        	<label for="id">Status:</label>
                        	<?php echo form_dropdown_contract_requests_status("status",($request_id !="") ? $request->status : "Open","");?>
                        	<!--<input type="text" value="<?php echo $request->status;?>" readonly id="status"/>-->
                        </div>
                        <div class="left" style="padding-left:20px;">
                        	<label for="id">Project Address:</label>
                        	<input type="text" value="<?php echo $request->address;?>" readonly/>
                        	<div class="clear"></div>
                        	<label for="id">Property Design:</label>
                        	<input type="text" value="<?php echo $request->property_design?>" readonly/>
                        	<div class="clear"></div>
                        	<label for="id">Base Price:</label>
                        	<input type="text" value="<?php echo $request->base_price;?>" readonly id="base_price"/>$
                        	<div class="clear"></div>
                        	<label for="id">Commission:</label>
                        	<input type="text" value="<?php echo $request->commission;?>" readonly id="commission_field"/>$
                        	<input type="hidden" value="<?php echo $request->id;?>" id="request_id"/>
                        	<div class="clear"></div>
                        </div>
                        <div class="left" style="padding-left:50px;">
                        	<div class="clear" style="padding-top:22px;"></div>
                        	<?php if ($request->status == "Pending" && $request->status != "Approved" /*&& $images*/) :?>
                            <input id="button" type="button" class="approve_contract" value="Approve Contract" style="width:150px;"/><br /><br />
                            <?php endif;?>
                            <?php if ($request->status == "Pending" && $request->status != "Rejected") :?>
                        	<input id="button" type="button" class="reject_contract" value="Reject Contract" style="width:150px;"/><br /><br />
                        	<?php endif;?>
                        	<?php if ($request->status == "Approved" || $request->status == "Rejected") : ?>
                        	<input id="button" type="button" class="reopen_contract" value="Reopen Contract" style="width:150px;"/>
                        	<?php endif;?>
                            
                            <input id="button" type="button" class="print_quote" value="Print Quote" style="width:150px;"/>
                        </div>
                        <div class="clear"></div>
                    </div><!-- END first tab -->   
                    <div>
                        <table style="margin:0;border-style:none;" class="left" id="listExtra">
                            <tr>
                                <th>Item name</th>
                                <th>Price($)</th>
                                <th>Quantity</th>
                                <th>Total</th>
                                <th><input type="hidden" id="base_price" value=""/></th>
                            </tr>
                            <?php if ($request_id && $extras) :?>
                                <?php foreach ($extras->result() AS $extra) :?>
                            <tr>
                                <td><input style="width:250px;" type="text" class="item_name" name="extra[item_name][]" value="<?php echo $extra->item_name;?>"/></td>
                                <td><input style="width:100px;" type="text" class="unit_price" name="extra[unit_price][]" value="<?php echo $extra->unit_price;?>"/></td>
                                <td>
                                    <input style="width:100px;" type="text" class="qty" name="extra[qty][]" value="<?php echo $extra->quantity;?>"/>
                                    <input type="hidden" name="extra[total_extra][]" class="hidden_total_extra" value="<?php echo $extra->total;?>"/>
                                </td>
                                <td class="total_extra"><?php echo "$".number_format($extra->total,2)?></td>
                                <td><a href="javascript:;" class="btnremoverow" title="Delete extra item."><img src="<?php echo site_url("images/window_close.png")?>"/></a></td>
                            </tr>            
                                <?php endforeach;?>
                            <?php else: ?>
                            <tr>
                                <td><input style="width:250px;" type="text" size="20" class="item_name" name="extra[item_name][]"/></td>
                                <td><input style="width:100px;" type="text" size="6" class="unit_price" name="extra[unit_price][]"/></td>
                                <td>
                                    <input style="width:100px;" type="text" size="6" class="qty" name="extra[qty][]"/>
                                    <input type="hidden" name="extra[total_extra][]" class="hidden_total_extra"/>
                                </td>
                                <td class="total_extra"></td>
                                <td></td>
                            </tr>
                            <?php endif;?>
                        </table>
                        <div class="clear"></div>
                        <table width="30%" class="left">
                            <tr><td colspan="2"><a href="javascript:;" id="btnadditem">Add new extra item</a></td></tr>
                            <tr>
                                <td>House Design :</td>
                                <td id="house_design"><?php echo ($request_id !="") ? $request->property_design : ""; ?></td>
                            </tr>
                            <tr>
                                <td>Base Price :</td>
                                <td id="baseprice"><?php echo ($request_id !="") ? "$".number_format($request->base_price, 0, ".", ",") : ""; ?></td>
                            </tr>
                            <tr>
                                <td>Commission :</td>
                                <td id="commission"><?php echo ($request_id !="" && $request->commission != 0) ? "$".number_format($request->commission, 0, ".", ",") : ""; ?></td>
                            </tr>
                            <tr>
                                <td>Extra Items :</td>
                                <td id="totalextras"></td>
                            </tr>
                            <tr>
                                <td>Quote Total :</td>
                                <td id="totalinc"></td>
                            </tr>
                            <tr>
                                <td>
                                    <input type="hidden" name="total_price" id="total_price"/>
                                    <input type="hidden" name="quote_id" value="<?php echo $request->quote_id;?>"/>
                                </td>
                            </tr>
                        </table>
                        <div class="clear"></div>
                    </div><!-- END second tab -->
                    <div>
                        <div class="left">
                            <label>Upload Contract Document. </label><br/>
                        </div>
                        <div class="right contract_upload" style="padding-right:10px">
                            <input type="file" name="upload_file" id="contract_upload_file" />
                        </div>
                        <div class="clear"></div>
                        <br/>
                        
                        <div id="files_listing">
                            <div id="page_listing">
                                <?php $this->load->view('admin/request/file_listing'); ?>
                            </div>
                            
                            <div class="clear"></div>
                            <div id="controls">
                                <div class="right">
                                    <input class="button" type="button" value="Delete Selected Files" id="delete_files" />
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div><!-- END tab 3-->
                    <div>
                        <div class="left">
                            <label>Upload Inclusion List </label><br/>
                        </div>
                        <div class="right" style="padding-right:10px">
                            <input type="file" name="upload_file" id="inclusion_upload_file" />
                        </div>
                        <div class="clear"></div>
                        <br/>
                        
                        <div id="inclusion_files_listing">
                            <div id="inclusion_page_listing">
                                <?php $this->load->view('admin/request/inclusion_file_listing'); ?>
                            </div>
                            
                            <div class="clear"></div>
                            <div id="controls">
                                <div class="right">
                                    <input class="button" type="button" value="Delete Selected Files" id="inclusion_delete_files" />
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div><!-- END tab 4-->
                    <div>
                        <div class="left">
                            <label>Upload Plans </label><br/>
                        </div>
                        <div class="right" style="padding-right:10px">
                            <input type="file" name="upload_file" id="plan_upload_file" />
                        </div>
                        <div class="clear"></div>
                        <br/>
                        
                        <div id="plan_files_listing">
                            <div id="plan_page_listing">
                                <?php $this->load->view('admin/request/plan_file_listing'); ?>
                            </div>
                            
                            <div class="clear"></div>
                            <div id="controls">
                                <div class="right">
                                    <input class="button" type="button" value="Delete Selected Files" id="plan_delete_files" />
                                </div>
                            </div>
                            <div class="clear"></div>
                        </div>
                        <div class="clear"></div>
                    </div><!-- END tab 5-->
                </div>
      
                <div class="clear"></div>
    
                <br/><br/>
                          
                <label for="heading">&nbsp;</label> 
                <input id="button" type="submit" value="<? echo ($request_id == "") ? "Create New Request": "Update Request"; ?>" /><br/>                    
            </form>
            
         <br/>
         <?php $this->load->view("admin/request/navigation"); ?>
         <div style="display:none;">
            <div id="approve_contract" align="center">
                <p><b>Approve this Contract Request, are you sure?</b></p>
                <input id="button" type="button" class="btnapprove_ok" crid="<?php echo $request->id;?>" value="Approve"/>
                <input id="button" type="button" class="btnclose" value="No" onClick="parent.jQuery.fancybox.close();"/>
            </div>
            <div id="reject_contract" align="center">
                <p><b>Reject this Contract Request, are you sure?</b></p>
                <input id="button" type="button" class="btnreject_ok" value="Reject" crid="<?php echo $request->id;?>"/>
                <input id="button" type="button" class="btnclose" value="No" onClick="parent.jQuery.fancybox.close();"/>
            </div>
            <div id="reopen_contract" align="center">
                <p><b>Reopen this Contract Request and set it back into a 'Pending' state, are you sure?</b></p>
                <input id="button" type="button" class="btnreopen_ok" crid="<?php echo $request->id;?>" value="Reopen" />
                <input id="button" type="button" class="btnclose" value="No" onClick="parent.jQuery.fancybox.close();"/>
            </div>
         </div>