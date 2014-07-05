<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
<input type="hidden" value="<?=count($files)?>" id="files_no" />

<table cellspacing="0" class="cmstable" > 
            <tr>
                <th>File Name</th>  
                <th>Description</th>
                <th>Download</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
			
<div id="formdescription" style="display:none;">
    <label for="description">Description:<span class="requiredindicator">*</span></label>
    <textarea id="newdescription" style="width:400px;"></textarea>
    <input type="hidden" id="description_id"/>
    
    <div class="clear"></div><br />
    <a href="javascript:;" class="button left center savedescription">Save</a>
</div>			
			
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
                    <td class="admintabletextcell"><span class="document_description" id="<?php echo $file->id; ?>">
					<?php 
					if($file->document_description)
					{					
						echo $file->document_description;
					}
					else
					{
						echo '<a href="javascript:;" id="description"  class="'.$file->id.'">click here to add description text </a>';
					}					?></span></td>
                    <td class="admintabletextcell"><a class="download_userfile" href="<?php echo base_url($file_path);?>" type="documents">Click to download</a></td>
                    <td class="center"><input type="checkbox" class="user_docstodelete" value="<?php echo $file->id;?>" /></td>
                </tr>                   
            <?php
        }
    }
}
        ?>
</table>