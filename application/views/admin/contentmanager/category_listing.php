<h2 class="left">Category &nbsp;</h2>
<a id="refresh_categories" href="javascript:void(0)" class="left">
    <img src="<?php print base_url(); ?>images/admin/refresh.png" alt="Click to refresh" />
</a>

<br class="clear"/>

<input type="hidden" id="category_id" value="<?php echo $this->data['category_id']; ?>" />
<table cellspacing="0" width="270" > 
        <tr>
            <th class="center" style="width:15px"><input type="checkbox" id="checkcategories"/></th>            
            <th>Category Name</th>                            
            <th style="width:15px">Edit</th>
        </tr>
         <?php /* Setup alternating row colours, using the variable "rowclass" */ 
        $i = 0;
        if($this->data['categories'])
        {
            foreach($this->data['categories']->result() as $category)
            {
                if($i++ % 2==1) $rowclass = "admintablerow";
                else  $rowclass = "admintablerowalt";
            ?> 
                <tr class="<?php print $rowclass;?>">
                
                    <td class="center"><input type="checkbox" name="categoriestodelete[]" value="<?php print $category->category_id;?>" /></td>
                    <td class="admintabletextcell"><a href="<?php echo base_url(); ?>admin/contentmanager/category/<?php echo $category->category_id;?>"><?php echo $category->name;?></a></td>
                    <td class="center"><a href="<?php echo base_url();?>admin/contentmanager/category_edit/<?php echo $category->category_id;?>">Edit</a></td>
                </tr>          
            <?php
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