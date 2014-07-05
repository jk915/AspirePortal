var return_field;
var backup_value_fancybox;

var is_used_fancybox = false;
// The selectFile method shows the file section dialog to the user
// using blockUI.  The folders and table view is loaded using AJAX.
function selectFile(fieldID)
{    
   // Save the return field for later use
   return_field = fieldID;
                           
   // Load the file selector screen with AJAX
   var parameters = {};
   parameters['type'] = 9;
     
   $.post(base_url + 'admin/filemanager/ajaxwork',parameters,function(data)
   {
      // Setup a modal window to show the folders and files.
      var width = $(document).width();
      var elementWidth = 850;
      var elementHeight = 450;
      
      if($(".blockUI").length > 0)
      {
          $(".blockUI").each( function(){
             
             $(this).removeClass("blockUI").addClass("blockUI1");
          });
      }         
      
      $.blockUI
      ({
          message: data.html,
          css: { cursor: 'normal', top: '100px', height: elementHeight + 'px', width: elementWidth + 'px', margin: '0 auto', left: width / 2 - (elementWidth / 2) },
          overlayCSS: { cursor: 'normal' },
          centerX: true,
          centerY: true
      });

      $('.blockUI .w_close').click(function(e) 
      {
         e.preventDefault();
         $(document).unblock();

      });
      
      $('.select-file').click(function(e) 
      {
          selectButtonClicked(this);
      });      
      
      // Apply the scrollbar
      $(".scroll-pane").jScrollPane();
      
      // After the file selection modal has loaded, find the change event for the folders select/droplist
      $('#selFolder').change(function()
      {
        refreshFiles();
      });  
      
      $("#upload_file").makeAsyncUploader({
                upload_url: base_url +'admin/filemanager/ajaxwork',
                flash_url: base_url + 'flash/admin/swfupload.swf',
                button_image_url: base_url + 'images/admin/selectsave.png',
                button_text: '',
                file_size_limit : '2 MB',
                debug: false
                
        });
        
        if(fieldID == 'icon_image')
        {
            is_used_fancybox = true;
            backup_value_fancybox = $('#wysiwyg4').val();
            $.fancybox.close();
        }
      
   },"json");    
}

function beforeUploadStart()
{
    // Get the name of the selected folder
    var folder_name = $('#selFolder').val();   
    
    if (!folder_name)
    {
        alert("Please select a folder");
        return false;
     } 
    
    swfu.addPostParam("type",'5');
    swfu.addPostParam("folder",folder_name);
    
    //swfu.addPostParam("extra",'5|'+folder_name);   
    
    return true;
}

function afterUploadFinish()
{
    // Get the name of the selected folder 
    var folder_name = $('#selFolder').val();
    if (folder_name != '')
    {
        refreshFiles();
        
        $("#filemanager_upload_file_uploading").hide();
        $('.ProgressBar').hide();
        $('#upload_file_uploading').hide();
        $('#SWFUpload_0').show();
        $('#SWFUpload_0').css("width","");
    }
     
    return true;
}

// The refreshFiles method gets the folder that the user has selected 
// and then loads the files that are in that folder.  The resulting 
// htmn file list is then pushed into the div.
function refreshFiles()
{
   // Get the name of the selected folder
   var folder_name = $('#selFolder').val();
   $("#span-folder-name").html(folder_name);
   
   var parameters = {};
   parameters['type'] = 10;
   parameters['folder_name'] = folder_name;
   
   blockElement("#divFiles");                                    
    
   $.post(base_url + 'admin/filemanager/ajaxwork', parameters, function(data)
   {
      $("#divFiles").html(data.html);
      $(".scroll-pane").jScrollPane();
      
      $('.select-file').click(function(e) 
      {
          selectButtonClicked(this);
      }); 
      unblockElement("#divFiles");                                          
       
   },"json");  
}

// The selectButtonClicked event is fired when the user clicks on a button to select a file.
// We get the corresponding file name (stored in the alt tag of clicked button)
// and then call the method to write the full file path back to the caller.
function selectButtonClicked(clicked_button)
{ 
   var file_name = $(clicked_button).attr("alt");
   writeFileName(file_name);
   
   if(is_used_fancybox)
   {
        $.fancybox({
                        'href' : '#formaddwrap',
                    });
        $('#wysiwyg4').val(backup_value_fancybox);
   }
}

// The selectFile method gets the file name of the selected file
// and writes the full path to the file back to the calling return field.
function writeFileName(file_name)
{
   if(file_name == "")
      return "";
      
   // Get the name of the selected folder
   var folder_name = $('#selFolder').val();   
   
   // Define the full path to the file
   var path = folder_name + "/" + file_name;
   if(return_field == 'parameter')  //modal types: costum
     path = "files/" + path;
  
   // Write the path back to the return field.
   $("#" + return_field).val(path);
   
   // Unblock the UI.
   $('.blockUI .w_close').click();
}
