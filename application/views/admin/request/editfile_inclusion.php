<div class="w_caption" style="background-color: #CCC;height:23px"><a href="javascript:void(0);" class="w_close"><!-- --></a><span class="w_captionText" id="_wicket_window_12"><b>Edit file</b></span></div>

<form class="plain" id="frmFileItem" name="frmFileItem" method="post">
<input type="hidden" id="file_id" name="file_id" value="<?php echo $file->id;?>" />
<div style="text-align: left;padding-left: 50px; padding-right: 50px;">
    
    <div class="left">
        <br/>
        <label>File name:</label>
        <input id="inclusion_document_name" type="text" value="<?php echo $file->document_name?>" name="inclusion_document_name"/>
        <label>Document Description:</label>
        <textarea id="inclusion_document_description" name="inclusion_document_description"><?php echo $file->document_description?></textarea>
    </div>
    
    <div class="clear"></div>
    <br/>
    
    <div>
        <input class="button" type="button" value="Save" id="inclusion_update_files" />
    </div>
    <br/>
</div>
</form>
