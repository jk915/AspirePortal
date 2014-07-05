<input type="hidden" value="<?php echo ceil($this->data["gallery_pages_no"])?>" id="gallery_pages_no" />
<input type="hidden" value="<?php echo count($this->data["gallery_files"])?>" id="gallery_files_no" />

<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>Image Name</th>            
                <th>Description</th>            
                <th>View</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
<?php /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if($this->data["gallery_files"])
{
    foreach($this->data["gallery_files"]->result() as $file)
    {
        $rowclass = ($i++ % 2==1)  ? "admintablerow" : "admintablerowalt";
        
        if(file_exists(FCPATH.$file->document_path))
        {
            $file_name = basename($file->document_path);
        ?> 
            <tr class="<?php echo $rowclass;?>">
                <td class="admintabletextcell"><?php echo $file_name; ?></td>
                <td class="admintabletextcell"><p class="gallery_editme" id="<?php echo $file->id;?>"><?php echo $file->document_name; ?></p></td>
                <td class="center admintabletextcell"><a href="<?php echo base_url() . $file->document_path;?>" target="_blank" >Click to view</a></td>
                <td class="center"><input type="checkbox" name="gallery_filestodelete[]" value="<?php echo $file->id;?>" /></td>
            </tr>                  
        <?php
        }
    }
}
        ?>
</table>