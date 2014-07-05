<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<table cellspacing="0" class="cmstable" style="font-size:9pt;"> 
            <tr>
                <th width="4%">
                    Lot
                    <?php if(@$sort_column == 'lot' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="lot" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="lot" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th>
                    Address
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'address' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="address" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="address" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th>
                    Area
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'area' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="area" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="area" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th>
                    Project
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'project' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="project" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="project" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th>
                    Builder
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'builder' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="builder" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="builder" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th width="6%">
                    State
                    <div class="clear"></div>
                    <?php if(@$sort_column == 's.name' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="s.name" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="s.name" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th width="7%">
                    Price
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'total_price' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="total_price" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="total_price" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th width="6%">
                    Featured
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'featured' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="featured" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="featured" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th width="7%">
                    Status
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'status' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="status" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="status" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th>
                    Advisor
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'advisor' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="advisor" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="advisor" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th>
                    Purchaser
                    <div class="clear"></div>
                    <?php if(@$sort_column == 'investor' && @$sort_order == 'asc') : ?>
                        <a href="javascript:;" class="sorting" col="investor" order="desc"><img src="<?php echo base_url()."images/admin/order_arrows_desc.png";?>"/></a>
                    <?php else :?>
                        <a href="javascript:;" class="sorting" col="investor" order="asc"><img src="<?php echo base_url()."images/admin/order_arrows_asc.png";?>"/></a>
                    <?php endif;?>
                </th>
                <th></th> 
            </tr>
        <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($properties) {
            foreach($properties->result() as $property)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?=$rowclass;?>">                    
                    <td class="admintabletextcell">
                        <a href="<?php echo base_url();?>admin/propertymanager/property/<?php echo $property->property_id;?>">
                            <?php if($property->lot != "") echo $property->lot;?>
                        </a>
                    </td>
                    
                    <td class="admintabletextcell">
                        <a href="<?php echo base_url();?>admin/propertymanager/property/<?php echo $property->property_id;?>">
                            <?php echo $property->address;?>
                        </a>
                    </td>
                    
                    <td class="admintabletextcell"><?php echo $property->area_name;?></td>
                    <td class="admintabletextcell"><?php echo $property->project_name;?></td>
                    <td class="admintabletextcell"><?php echo $property->builder_name;?></td>
                    
                    <td class="admintabletextcell">
                        <?php echo $property->state;?>
                    </td>
                    
                    <td class="admintabletextcell">
                        $<?=number_format($property->total_price, 0, ".", ","); ?>
                    </td>
                    
                    <td class="admintabletextcell center">
                        <?php echo $property->featured == 1 ? 'Yes' : 'No'?>
                    </td>
                    
                    <td class="admintabletextcell">
                        <a href="<?php echo site_url("admin/propertymanager/change_status/$property->property_id")?>" class="fancybox fancybox.ajax">
                            <?php echo ucfirst($property->status);?>
                        </a>
                    </td>
                    
                    <td class="admintabletextcell"><?php echo !empty($property->advisor_first_name) ? $property->advisor_first_name.' '.$property->advisor_last_name : 'NA'?></td>
                    <td class="admintabletextcell"><?php echo !empty($property->reserved_first_name) ? $property->reserved_first_name.' '.$property->reserved_last_name : 'NA'?></td>
                    
                    <td class="center"><input type="checkbox" name="propertiestodelete[]" value="<?php echo $property->property_id;?>" /></td>
                </tr>
            <?
            }
        }
        ?>
</table>