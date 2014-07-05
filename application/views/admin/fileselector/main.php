<div class="w_caption" style="background-color: #CCC;height:23px"><a href="javascript:void(0);" class="w_close"><!-- --></a><span class="w_captionText" id="_wicket_window_12"><b>File Selection</b></span></div>

<form id="frmSelector" name="frmSelector" action="#">
   <label>Select a Folder: </label>

   <select id="selFolder" name="selFolder" style="width: 240px;">
      <?php echo $this->utilities->print_select_options_array($folders,true,$selected_folder); ?>
   </select>
   
   <div class="clear"></div>
   
   <div id="divFiles" class="top-margin scroll-pane">
      <?php $this->load->view("admin/fileselector/file_listing"); ?>
   </div>
   
   <div class="top-margin-sm"></div>
   
   <label for="file" class="block">Upload a new file into "<span id="span-folder-name"><?php echo $selected_folder; ?></span>"</label>
   <input type="file" name="upload_file" id="upload_file" />    
   
</form>