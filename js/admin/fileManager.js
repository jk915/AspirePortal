$(document).ready(function()
{
	//if (typeof jScrollPane != "undefined")
	if($(".scroll-pane").length > 0)
		$(".scroll-pane").jScrollPane(); 
    
    $("#filemanager_folders").change(function() {
    
        var selected_folder = $("option:selected", this).val();
        
        filemanager_refreshFolderFiles(selected_folder);          
   
    });
    
    $('#filemanager_addfolder').live('click',function(e){
    
        e.preventDefault();
        $('#filemanager_new_folder_div').show('slow');
        $('#filemanager_new_folder').val('');
    
    });
    
    $('#filemanager_cancel').live('click',function()
    {
		$('#filemanager_new_folder_div').hide('slow');
    });
    
    // Handle the event when the user clicks the button to actually 
    // add a new folder.
    $('#filemanager_save').live('click',function()
    {
        var foldername = $('#filemanager_new_folder').val();
        var parent_folder = $('#filemanager_parentfolder').val();
        
        if (foldername != '')
        {
           
            var parameters = {};
            parameters['type'] = 3;
            parameters['folder_name'] = foldername;
            parameters['parent_folder'] = parent_folder;
            
            blockElement('#filemanager_folders');
            $.post(base_url + 'admin/filemanager/ajaxwork', parameters , function(data){
            
                $('#filemanager_folders').html(data.html);
                
                unblockElement('#filemanager_folders');
                
                $('#filemanager_new_folder_div').hide();
                
                filemanager_showMessage(data.message);
                filemanager_afterUploadFinish();
                 
                
            },
            "json");
           
        }
    });
    
    $('#filemanager_deletefolder').live('click',function(){
    
        var foldername = $('#filemanager_folders').val();
         
        if (!foldername) return;
    
    
        if (confirm('Are you sure you want to delete this folder ?'))
        {
            
            var parameters = {};
            parameters['type'] = 4;
            parameters['folder_name'] = foldername;
            
            blockElement('#filemanager_folders');
            $.post(base_url + 'admin/filemanager/ajaxwork', parameters , function(data){
            
                $('#filemanager_folders').html(data.html);
                
                unblockElement('#filemanager_folders');
                
                var selected_folder = $("option:selected", $("#filemanager_folders")).val();        
                filemanager_refreshFolderFiles(selected_folder);          
                
                filemanager_showMessage(data.message);
                
            },
            "json");
        }
    
    });
    
    $("#filemanager_upload_file").makeAsyncUploader({
                upload_url: base_url +'admin/filemanager/ajaxwork',
                flash_url: base_url + 'flash/admin/swfupload.swf',
                button_image_url: base_url + 'images/admin/selectsave.png',
                button_text: '',
                file_size_limit : '20 MB',
                beforeUpload:'filemanager_beforeUploadStart()',
                afterUpload:'filemanager_afterUploadFinish()'
        });
        
         
    $("#filemanager_delete_files").live('click',function(){
        
        if ($("input[@name='filestodelete[]']:checked").length == 0)
        {
            alert('Please click on the checkbox to select the files you want to delete.');
            return;
        }
        
        if (confirm("Are you sure you want to delete the selected files?"))
        {
            var foldername = $('#filemanager_folders').val();         
            if (!foldername) return;
            
            var selectedvalues = "";
            $("input[@name='filestodelete[]']:checked").each(function(){
                selectedvalues += $(this).val() +';';
            });
            
            var parameters = {};
            
            parameters['type'] = 1;
            parameters['todelete'] = selectedvalues;
            parameters['folder'] = foldername;
            
            blockElement("#filemanager_files_listing");
            
            $.post(base_url + 'admin/filemanager/ajaxwork', parameters,function(data){
            
                unblockElement("#filemanager_files_listing");
                if (data=="ok")
                    filemanager_afterUploadFinish();
                else
                    alert("Error deleteing file(s)");
            });
        }
        
    });
    
    $('#filemanager_refresh_files').live("click",function(e){
        filemanager_afterUploadFinish();
    });
    
    $('#filemanager_refresh_folders').live("click",function(e){
        filemanager_refreshFolders();
    });
    
    $(".download").live("click",function(e){
        
        var href = $(this).attr("href");
        
        var selected_folder = $("option:selected", ("#filemanager_folders")).val();
        
        var paremeters = {};
        paremeters['type'] = 8;
        paremeters['file'] = selected_folder+'/'+href;
        
        $.download(base_url + 'admin/filemanager/ajaxwork',paremeters);     
       
        e.preventDefault();
        
    });  
    
         
});

function filemanager_showMessage(message)
{
        if ($('#message').length == 0)
        {
            $('<p id="message"></p>').html(message).insertBefore($('#filemanager_new_folder_div'));
        }
        else
        {
            $('#message').html(message);
        }
}

function filemanager_beforeUploadStart()
{
   

    var foldername = $('#filemanager_folders').val();
    if (!foldername)
    {
        alert("Please select a folder");
        return false;
     } 

    swfu.addPostParam("type",'5');
    swfu.addPostParam("folder",foldername);
    
    return true;
}

function filemanager_afterUploadFinish()
{
     var foldername = $('#filemanager_folders').val();
     if (foldername != '')
     {
        filemanager_refreshFolderFiles(foldername);
     }
     
    return true;
}

function filemanager_refreshFolderFiles(selected_folder)
{
    var paremeters = {};
    paremeters['type'] = 2;
    paremeters['folder'] = selected_folder;
    paremeters['window'] = $("#window").val();
    
    blockElement('#filemanager_files_listing');
    $("#filemanager_files_listing").load(base_url + 'admin/filemanager/ajaxwork', paremeters,function(){
    
       unblockElement('#filemanager_files_listing');
       $(".scroll-pane").jScrollPane();
    });
    
    $("#filemanager_upload_file_uploading").hide();
    $('.ProgressBar').hide();
    $('#upload_file_uploading').hide();
    $('#SWFUpload_0').show();
    $('#SWFUpload_0').css("width","");
    
}


function filemanager_refreshFolders()
{
        var parameters = {};
        parameters['type'] = 6;
    
        blockElement('#filemanager_folders');
        $.post(base_url + 'admin/filemanager/ajaxwork', parameters , function(data){
            
                                                        
                $('#filemanager_folders').html(data.html);
                filemanager_afterUploadFinish();
                unblockElement('#filemanager_folders');
                
            },
            "json");
}
