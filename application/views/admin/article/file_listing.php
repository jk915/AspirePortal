<input type="hidden" value="<?php echo ( isset( $this->data["pages_no"] ) ? ceil( $this->data["pages_no"] ) : 0 ); ?>" id="pages_no" />
<input type="hidden" value="<?php echo ( isset( $this->data["current_page"] ) ? ceil( $this->data["current_page"] ) : 1 ); ?>" id="current_page" />

<table cellspacing="0" class="cmstable" id="tableFiles"> 
            <tr>
                <th>File Name</th>            
                <th>Description</th>
                <th>Link</th>
                <th style="width: 60px;">order</th>
                <th style="width: 20px;">Delete</th> 
            </tr>
<? /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if( $this->data['images'] )
{
    foreach( $this->data['images']->result() as $file)
    {
        $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";
        
        ?> 
            <tr class="<?php echo $rowclass; ?>">
                <td class="admintabletextcell"><a class="screenshot" rel="<?php echo base_url() . $file->document_path; ?>"><?php echo $file->document_name; ?></a></td>
                <td class="admintabletextcell"><input title="<?php echo $file->id; ?>" type="text" id="description_<?php echo $file->id; ?>" value="<?php echo $file->document_description; ?>" style="width: 190px;" class="gallery_description" /></td>
                <td class="admintabletextcell"><input title="<?php echo $file->id; ?>" type="text" id="link_<?php echo $file->id; ?>" value="<?php echo $file->link; ?>" style="width: 190px;" class="gallery_link" /></td>
                <td class="left">
                    <?php echo $file->order;?>&nbsp;
                    <?php 
                    if( $file->order >= 2 )
                    {
                    ?>
                        <a href="-1" id="<?php echo $file->id; ?>" title="Move up" class="moveup"></a>&nbsp;
                    <?php 
                    }
                    ?>
                    <?php 
                    if( $file->order < $this->data['count_all'] )
                    {
                    ?>
                        <a href="1" id="<?php echo $file->id; ?>" title="Move down" class="movedown"></a>
                    <?php 
                    }
                    ?>
                </td>
                <td class="center"><input type="checkbox" name="imagestodelete[]" value="<?php echo $file->document_name; ?>" /></td>
            </tr>                  
        <?
    }
}
        ?>
</table>
