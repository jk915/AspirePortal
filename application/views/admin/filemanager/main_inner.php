    <? if (isset($window) && $window == "popup") :?>
        <div class="w_caption"><a href="javascript:void(0)" class="w_close"></a><span class="w_captionText" id="_wicket_window_12"/></div>
        <input type="hidden" id="window" name="window" value="popup" />
    <? endif;?>

        <div class="left">
            <h2 class="left" style="width:60px">Folders</h2>
                <a id="filemanager_refresh_folders" href="javascript:void(0)" class="left">
                    <img  src="<?=base_url()?>images/admin/refresh.png" />
                </a>
                <br class="clear" />
                <select id="filemanager_folders" name="folders" size="10">                
                    <?php echo $this->utilities->print_select_options_array($folders, false, $selected_folder, "/"); ?>
                </select>                
                <br/><br/>
                <img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add Folder." /> <a id="filemanager_addfolder" href="javascript:void(0)">Add Folder</a>
                <span class="divider">|</span>  
                <img src="<?=base_url();?>images/admin/i_trashcan.png" border="0" width="16" height="18" alt="Delete Folder." /> <a id="filemanager_deletefolder" href="javascript:void(0)">Delete Selected Folder</a>
                <br />
                <br />
                <div id="filemanager_new_folder_div" style="display:none">
                	<label>New Folder Name</label>
                    <input type="text" id="filemanager_new_folder" />
                    
                	<label>Parent Folder</label>
	                <select id="filemanager_parentfolder" name="filemanager_parentfolder">                
	                	<option value="">None</option>
	                    <?php echo $this->utilities->print_select_options_array($folders, false, "", "/"); ?>
	                </select>                   
					
					<div class="clear top-margin">
                    	<input type="button" id="filemanager_save" value="Add Folder" class="smallbutton" />
                    	<input type="button" id="filemanager_cancel" value="Cancel" class="smallbutton" />
                    </div>
                </div>
            </div>
        
        
        <div class="left left-margin" id="right_side">
            <h2 class="left" style="width:120px">Files in folder</h2>
            
            <a id="filemanager_refresh_files" href="javascript:void(0)" class="left">
                        <img src="<?=base_url()?>images/admin/refresh.png" />
             </a>    
                
                
                <div class="right" style="padding-right:10px">
                    <input type="file" name="upload_file" id="filemanager_upload_file" />    
                </div>
                
                <div class="clear"></div>        
                
                <div id="filemanager_files_listing">
                    <? $this->load->view('admin/filemanager/file_listing',array('files'=>$this->data["files"],'pages_no' => $this->data["count_all"] / $this->data["records_per_page"], "window" => isset($window) ? $window : null )); ?>
                </div>
                        
        </div>
        
        <div class="clear"></div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Delete Selected Files" id="filemanager_delete_files" />
            </div>
            
        </div>    
        
    
