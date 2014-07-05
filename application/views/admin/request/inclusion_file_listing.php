<input type="hidden" value="<?php echo ( isset( $this->data["pages_no"] ) ? ceil( $this->data["pages_no"] ) : 0 ); ?>" id="pages_no" />
<input type="hidden" value="<?php echo ( isset( $this->data["current_page"] ) ? ceil( $this->data["current_page"] ) : 1 ); ?>" id="current_page" />

<table cellspacing="0" class="cmstable" id="tableFiles"> 
            <tr>
                <th>Name</th>            
                <th style="width: 50%;">Description</th>
                <th style="width: 20px;">Edit</th> 
                <th style="width: 20px;">Delete</th> 
            </tr>
<? /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if( $this->data['inclusion_images'] )
{
    foreach( $this->data['inclusion_images']->result() as $file)
    {
        $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";
        
        ?> 
            <tr class="<?php echo $rowclass ?>">
                <td class="admintabletextcell"><a class="screenshot" target="_blank" href="<?php echo base_url() . $file->document_path; ?>"><?php echo $file->document_name; ?></a></td>
                <td class="admintabletextcell">
                    <?php echo !empty($file->document_description) ? nl2br($file->document_description) : "";?>
                </td>
                <td>
                    <a href="javascript:;" class="btnedit_inclusion" fid="<?php echo $file->id?>">Edit</a>
                </td>
                <td class="center"><input type="checkbox" name="imagestodelete[]" value="<?php echo $file->document_name; ?>" /></td>
            </tr>                  
        <?
    }
}
        ?>
</table>
