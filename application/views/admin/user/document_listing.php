<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<input type="hidden" value="<?=count($files)?>" id="files_no" />

<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>File Name</th>  
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
                    <td class="admintabletextcell"><?php echo $filename;?></td>
                    <td class="admintabletextcell"><a class="download_userfile" href="<?php echo $filename;?>" type="documents">Click to download</a></td>
                    <td class="center"><input type="checkbox" class="user_docstodelete" value="<?php echo $file->id;?>" /></td>
                </tr>                  
            <?php
        }
    }
}
        ?>
</table>