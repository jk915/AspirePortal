<input type="hidden" value="<?php echo ceil($pages_no)?>" id="pages_no" />
<input type="hidden" value="<?php echo count($files)?>" id="files_no" />

<table cellspacing="0" class="cmstable" > 
            <tr>
				<th>Thumbnail </th>
				<th>File Name</th>
                <th>Description</th>
				<th>Download</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
<? /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if($files)
{ 
    foreach($files->result() as $file)
    {
    	$filename = $file->document_name;
        $file_path = $file->document_path;
        
        if (file_exists($file_path))
        {
	        if($i++ % 2==1) $rowclass = "admintablerow";
	        else  $rowclass = "admintablerowalt";
	        ?> 
	            <tr class="<?=$rowclass;?>">
					<td> <img src="<?php echo base_url(); ?>/<?php echo $file_path; ?>" width="100px"> </td>
					<td class="admintabletextcell"><?php echo $filename;?></td>
	                <td class="admintabletextcell">
	                	<textarea name="document_description[]" fid="<?php echo $file->id?>" style="width:99%;height:70px;"><?php echo $file->document_description;?></textarea>
	                	<input type="hidden" name="fileid[]" value="<?php echo $file->id; ?>"/>
                	</td>
	                <td class="admintabletextcell"><a class="download_project" rel="<?php echo $file->id?>" href="<?php echo $filename;?>" >Click to download</a></td>
	                <td class="center"><input type="checkbox" name="project_imagestodelete[]" value="<?php echo $filename;?>" /></td>
	            </tr>                  
	        <?
        }
    }
}
        ?>
</table>