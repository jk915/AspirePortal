<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<input type="hidden" value="<?=count($files)?>" id="files_no" />

<table cellspacing="0" class="cmstable" id="areadoclist" > 
        <tr>
			<th>Thumbnail </th>
            <th>Description</th>  
            <th>Download</th>
            <th style="width: 20px;">Delete</th> 
        </tr>
<?php
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
                    <td class="admintabletextcell">
                    	<textarea name="document_description[]" fid="<?php echo $file->id?>" style="width:99%;height:70px;"><?php echo $file->document_description;?></textarea>
                    	<input type="hidden" name="fileid[]" value="<?php echo $file->id?>"/>
                	</td>
                    <td class="left admintabletextcell"><a class="download_area" rel="<?php echo $file->id?>" href="<?php echo $filename;?>" >Click to download</a></td>
                    <td class="center"><input type="checkbox" name="area_imagestodelete[]" value="<?php echo $file->id;?>" /></td>
                </tr>                  
            <?php
        }
    }
}
        ?>
</table>