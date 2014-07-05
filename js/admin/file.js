$(document).ready(function(){
    
    $("#folders").change(function() {
    
        var selected_folder = $("option:selected", this).val();
        
        var paremeters = {};
        paremeters['type'] = 2;
        paremeters['folder'] = selected_folder;
        
        blockElement('#files_listing');
        $("#files_listing").load(base_url + 'filemanager/ajaxwork', paremeters,function(){
        
           unblockElement('#files_listing');
        });
        
    });
    
});