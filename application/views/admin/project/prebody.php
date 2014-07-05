    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/fileuploader.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tiptip.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/jquery.fancybox.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/datePicker.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/reservation_calendar.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/jscrollpane.css" />
    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/fileSelector.css" />

    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.fancybox.pack.js"></script>

    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/project.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.validate.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.js"></script>   
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.cookies.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery-asyncUpload-0.1.js"></script> 
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileuploader.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/pagination.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.datePicker.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/date.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.jscrollpane.js"></script> 
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/fileSelector.js"></script>
    <!-- ckeditor -->
    <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/ckeditor.js"></script> 
    <script type="text/javascript" src="<?php echo base_url(); ?>ckeditor/adapters/jquery.js"></script> 
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/ckeditorImage.js"></script> 
    <!--  end ckeditor -->   
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/main.js"></script>  
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tiptip.js"></script>         

    <style type="text/css">
    .upload_documents object
    {
        width: none !important;
    }
    .upload_documents .doc_name
    {
        font-weight: bold;
    }

    .upload_documents > div
    {
        height: 80px;
        /* width: 400px;*/
        margin-top: 20px;
        padding-right:30px;
    }
    .upload_documents span
    {
        max-width: 150px;
    }

    .upload_documents .line
    {
        height: 2px;
        background-color: #F4F4F4;
        clear: both;
    }
    
    SPAN.asyncUploader OBJECT { left:0px; }
    
    .qq-upload-list li .qq-upload-failed-text {display:none;}
    .qq-upload-list li.qq-upload-fail .qq-upload-failed-text {display:inline;}
    </style> 

    <script type="text/javascript">
    $(document).ready(function($) {
        $('.someClass').tipTip({maxWidth: 300, edgeOffset: 10,keepAlive: true });
        
        $(".cf_inputbox").change(function () {
            var str = "";
            
            $(".cf_inputbox option:selected").each(function (i) {
                str += this.title ;
            });
            
            document.getElementById('tiptip_content').innerHTML=str.replace('Choose Option','');

        }).trigger('change');

        //for Note tab
        $('.date-choose').datePicker({startDate:'01/01/2012'});

        $('#newcomment').live('click',function(){
            $('#comment').val('');
        
            $.fancybox({
                'href' : '#formnewcomment',
            });
        });        

        var refeshComments = function(){
            var property_id =  $("#property_id").val()
            var parameters = {};
            parameters['type'] = 24;
            parameters['property_id'] = property_id;
            
            $.post(base_url + 'admin/projectmanager/ajaxwork', parameters,function(data) {
                if (data) {
                    $('.commentlisting').html(data);    
                } else {
                    alert("Sorry an error occured and your request could not be processed");
                }
            });
        };

        $('.savecomment').live('click',function(){

            var parameters = {};
            var comment = $('#comment').val();
            var project_id = $('#project_id').val();
            var comment_id = $('#comment_id').val();

            if (comment.length == 0) {
                alert('Note is required');
                return false;
            }

            blockElement('.commentlisting');
            parameters['comment'] = comment;
            parameters['project_id'] = project_id;
            parameters['comment_id'] = comment_id;

            parameters['type'] = 23;

            $.post(base_url + 'admin/projectmanager/ajaxwork', parameters,function(data){
                unblockElement('.commentlisting');
                
                if (data == 'OK') {
                    refeshComments();
                    $.fancybox.close();
                } else {
                    alert("Sorry an error occured and your request could not be processed");
                }
            });
        });

        $('#deletecomment').live('click',function() {
            if ($(".commenttodelete:checked").length == 0) {
                alert('Please select at least one comment you want to delete.');
                return;
            }
            
            if (confirm("Are you sure you want to delete the selected comment(s)?")) {
                var selectedvalues = "";
                var aItems = [];
                
                $(".commenttodelete:checked").each(function(){
                    var itemID = $(this).val();
                    selectedvalues += itemID +';';
                    aItems.push(itemID);
                });
        
                var parameters = {};
                parameters['type'] = 25;
                parameters['todelete'] = selectedvalues;
        
                $.post(base_url + 'admin/projectmanager/ajaxwork', parameters , function(data) {
                    if(data == 'OK') {
                        refeshComments();
                        alert('Selected comment(s) was removed successfully.');
                    } else {
                        alert("Sorry an error occured and your request could not be processed");
                    }
                });
            }
        });

        $('.editComment').live('click',function() {
            make_note_default();	
            var comment_id = $(this).attr('rel');
            var parameters = {};
            
            blockElement(".commentlisting");
            
            parameters['type'] = 26;
            parameters['comment_id'] = comment_id;
            
            $.post(base_url + 'admin/projectmanager/ajaxwork', parameters, function(data) {
                unblockElement(".commentlisting");
                
                if (data.status == 'OK') {
                    $('#note_date').val(data.note_date);
                    $('#comment').val(data.comment);
                    $('#comment_id').val(data.comment_id);

                    if(data.advisor != "" ) {
                        $('#advisor').attr('checked', true);
                    }
        
                    if(data.partner != "" ) {
                        $('#partner').attr('checked', true);
                    }
                    
                    if(data.investor != "" ) {
                        $('#investor').attr('checked', true);				
                    }

                    $.fancybox({
                        'href' : '#formnewcomment',
                    });
                } else {
                    alert("Sorry an error occured and your request could not be processed");
                }
            },'json');
        });
        
        $("#btnRegenerateMap").click(function() {
            $("#deletemap").val("1");
            $("#frmProject").submit();    
        });
   });
   </script>
</head>