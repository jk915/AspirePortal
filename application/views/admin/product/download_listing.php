<table cellspacing="0" class="cmstable" > 
            <tr>
                <th style="width: 200px;">File Name</th>
                <th>Name</th>            
                <th>Description</th>            
                <th style="width: 95px;">Permission Level</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
<?php /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if($this->data["download_files"])
{
    foreach($this->data["download_files"]->result() as $file)
    {
        $rowclass = ($i++ % 2==1)  ? "admintablerow" : "admintablerowalt";
        
        if(file_exists(FCPATH.$file->document_path))
        {
            $file_name = basename($file->document_path);
        ?> 
            <tr class="<?php echo $rowclass;?>">
                <td class="admintabletextcell"><?php echo $file_name; ?></td>
                <td class="admintabletextcell"><p class="edit_name" id="<?php echo $file->id;?>"><?php echo $file->document_name; ?></p></td>
                <td class="admintabletextcell"><p class="edit_description" id="<?php echo $file->id;?>"><?php echo $file->document_description; ?></p></td>
                <td class="admintabletextcell">
                	<input type="text" class="small_box" name="level_for_<?php print $file->id; ?>" value="<?php print ( !empty( $file->broadcast_access_level_id ) ? $file->broadcast_access_level_id : '0' ); ?>" title="Write a level and press enter" />
                	<input type="checkbox" class="exact_box" name="exact_level_for_<?php print $file->id; ?>" <?php print( !empty( $file->is_exact ) ? 'checked="checked"' : '' ); ?> />&nbsp;Exact
                </td>
                <td class="center"><input type="checkbox" name="product_downloadfilestodelete[]" value="<?php echo $file->id;?>" /></td>
            </tr>                  
        <?php
        }
    }
}
        ?>
</table>