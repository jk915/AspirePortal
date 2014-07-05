<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<input type="hidden" value="<?=count($files)?>" id="files_no" />
<input type="hidden" value="<?=MAX_PROPERTY_IMAGES?>" id="max_property_images" />    

<table cellspacing="0" class="cmstable" > 
            <tr>
				<th>Thumbnail </th>
                <th>File Name</th>  
                <th>Caption</th>          
                <th style="width: 20px;">Hero</th>            
                <th>Download</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
<?php /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if($files)
{
    foreach($files->result() as $file)
    {                                                                                    
        $filename = $file->document_name;
        $file_path = $file->document_path;
        
        if (file_exists($file_path))
        {
            $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";        
            ?> 
                <tr class="<?php echo $rowclass;?>">
					<td> <img src="<?php echo base_url(); ?>/<?php echo $file_path; ?>" width="100px"> </td>
                    <td class="admintabletextcell"><?php echo $filename;?></td>
                    <td class="admintabletextcell"><span id="<?php echo $file->id;?>" class="edit_alt"><?php if(isset($file->extra_data)) echo $file->extra_data; ?></span></td>
                    <td class="center"><input type="radio" name="hero_image" value="<?php echo $filename;?>" <? if($filename == $hero_image) print "checked";?> /></td>
                    <td class="center admintabletextcell"><a class="download_property" href="<?php echo $filename;?>" >Click to download</a></td>
                    <td class="center"><input type="checkbox" name="property_imagestodelete[]" value="<?php echo $file->id;?>" /></td>
                </tr>                  
            <?php
        }
    }
}
        ?>
</table>