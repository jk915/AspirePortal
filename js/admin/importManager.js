$(document).ready(function()
{         
    // Setup hero image uploader
    var gUploader = new qq.FileUploader(
    {
        // pass the dom node (ex. $(selector)[0] for jQuery users)
        element: document.getElementById('upload_file'),
        // path to server-side upload script
        action: base_url + 'admin/importmanager/ajaxwork',
        params: {
            "import_type" : $("input[name='import_type']").val()
        },
        allowedExtensions: ['csv'],
        sizeLimit: 21000000, // max size 
        onSubmit: function(id, fileName)
        {
            gUploader._options.params["import_type"] = $("input[name='import_type']").val();  
        },
        onComplete: function(id, fileName, responseJSON)
        {
            // The upload completed successfully.
            $("#message").html(responseJSON.message);
        }
    });        
});