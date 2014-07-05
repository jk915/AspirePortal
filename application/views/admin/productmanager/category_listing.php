<input type="hidden" id="category_id" value="<?php echo $this->data['category_id']; ?>" />
<table cellspacing="0" width="270" > 
        <tr>
            <th class="center" style="width:15px"><input type="checkbox" id="checkcategories"/></th>            
            <th>Category Name</th>
            <th style="width:15px">Edit</th>
        </tr>
         <? /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($this->data['categories'])
        {
            foreach($this->data['categories']->result() as $category)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?=$rowclass;?>">
                
                    <td class="center"><input type="checkbox" name="categoriestodelete[]" value="<?php echo $category->product_category_id;?>" /></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url()?>productmanager/category/<?php echo $category->product_category_id;?>"><?php echo $category->name;?></a></td>
                    <td class="center"><a href="<?php echo base_url();?>productcategorymanager/edit/<?php echo $category->product_category_id;?>">Edit</a></td>
                    
                </tr>          
            <?
            }
        }
        else
        {
            ?>
                <tr>
                    <td colspan="2">No categories.</td>
                </tr>
                
            <?php
        }
        ?>
</table>       