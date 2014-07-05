<input type="hidden" value="<?php if(isset($pages_no)) echo ceil($pages_no)?>" id="pages_no" />
<input type="hidden" value="<?php if(isset($category_id)) echo $category_id; ?>" id="category_id" />

<?php
if(isset($products) && $products)
{
?>
<table cellspacing="0" class="cmstable" width="700" style="width:600px"> 
            <tr>
                <th>Model No</th>
                <th>Product Name</th>
                <th>Category</th>
                <th>Cost Price</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
            
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
            foreach($products->result() as $product)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php echo $rowclass;?>">

                    <td class="admintabletextcell"><?php echo $product->model_number;?></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url()?>productmanager/product/<? echo $product->product_id;?>"><?php echo $product->product_name;?></a></td>
                    <td class="admintabletextcell"><?php echo $product->name;?></td>
                    <td class="admintabletextcell"><?php echo $product->price;?></td>
                                                                                                     
                    <td class="center"><input type="checkbox" name="productstodelete[]" value="<?php echo $product->product_id;?>" /></td>
                </tr>          
            <?
            }
            ?>
</table>
<?php
}
else
{
    ?>
        <?php if ((isset($category_id)) && ($category_id != -1)) echo  "There are no products in this category yet."; ?>
    <?php
    
}
?>
