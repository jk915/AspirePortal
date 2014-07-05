    <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>/css/admin/tabs-no-images.css" />
    <link type="text/css" href="<?php echo base_url();?>css/admin/jquery.fancybox.css" rel="stylesheet" />
    <script type="text/javascript" src="<?php echo base_url();?>js/admin/jquery.fancybox.pack.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tools.tabs-1.0.4.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>/js/admin/request.js""></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.js"></script>                     
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfupload.cookies.js"></script>
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery-asyncUpload-0.1.js"></script>   
	<script type="text/javascript" src="<?php echo base_url(); ?>js/admin/download.js"></script>
	<script type="text/javascript">
    	$(document).ready(function() 
    	{
    		
    		$("#tabUpload").click(function()
    		{
    			$("#contract_upload_file").makeAsyncUploader(
    			{
    				upload_url: base_url +'admin/contractrequests/ajaxwork',
    				flash_url: base_url + 'flash/admin/swfupload.swf',
    				button_image_url: base_url + 'images/admin/selectsave.png',
    				button_text: '',
    				file_types: '*.pdf',
    				file_size_limit : '1000 MB',
    				beforeUpload: 'beforeContractUpload("contract_upload_file")',
    				afterUpload: 'afterContractUpload("contract_upload_file")',
    				debug : false
    			});
    		});
    		
    		$("#tabUploadInclusion").click(function()
    		{
    			$("#inclusion_upload_file").makeAsyncUploader(
    			{
    				upload_url: base_url +'admin/contractrequests/ajaxwork',
    				flash_url: base_url + 'flash/admin/swfupload.swf',
    				button_image_url: base_url + 'images/admin/selectsave.png',
    				button_text: '',
    				file_types: '*.pdf',
    				file_size_limit : '1000 MB',
    				beforeUpload: 'beforeInclusionUpload("inclusion_upload_file")',
    				afterUpload: 'afterInclusionUpload("inclusion_upload_file")',
    				debug : false
    			});
    		});
    		
    		$("#tabUploadPlans").click(function()
    		{
    			$("#plan_upload_file").makeAsyncUploader(
    			{
    				upload_url: base_url +'admin/contractrequests/ajaxwork',
    				flash_url: base_url + 'flash/admin/swfupload.swf',
    				button_image_url: base_url + 'images/admin/selectsave.png',
    				button_text: '',
    				file_types: '*.pdf',
    				file_size_limit : '1000 MB',
    				beforeUpload: 'beforePlanUpload("plan_upload_file")',
    				afterUpload: 'afterPlanUpload("plan_upload_file")',
    				debug : false
    			});
    		});
            
            // Handle the event when the admin user wants to print the quote
            // associated with this request.
            
            $("input.print_quote").click(function() {
                var request_id = $("#request_id").val();
                window.location.href = base_url + "postback/admin_print_quote_for_request/" + request_id;
            });
    		
        });
        
        // Contract Upload
        function beforeContractUpload(id)
    	{
    		var request_id = $("#request_id").val();
    		swfu_array[id].addPostParam("type","3");
            swfu_array[id].addPostParam("request_id",request_id);
    		return true;
    	}
    	function afterContractUpload(id)
    	{
    	    refreshContractFiles();
    	    //$('#async_contract_upload_file').hide();
    	    return true;
    	}
    	function refreshContractFiles()
    	{   
    	    var parameters = { };
    	    parameters['type'] = 4;
    	    parameters['request_id'] = $("#request_id").val();
    	    blockElement('#files_listing');
    	    $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data){
    	         $('#page_listing').html(data.html);
    	         unblockElement('#files_listing');
    	    },"json");
    	}
    	
    	$("#delete_files").live('click',function()
        {
            if ($("input[name='imagestodelete[]']:checked",$('#files_listing')).length == 0)
            {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }
    
            if (confirm("Are you sure you want to delete the selected files?"))
            {
    
                var selectedvalues = "";
                $("input[name='imagestodelete[]']:checked",$('#files_listing')).each(function(){
                    selectedvalues += $(this).val() +';';
                });
                var parameters = {};
    
                parameters['type'] = 5;
                parameters['todelete'] = selectedvalues;
                parameters['request_id'] = $("#request_id").val();
    
                blockElement("#files_listing");
    
                $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
                {
                    unblockElement("#files_listing");
                    //$('#async_contract_upload_file').show();
                    $(".contract_upload").show();
                    refreshContractFiles();
                });
            }
        });
        
        $("#update_files").live('click',function(){
            var parameters = {};
            parameters['type'] = 7;
            parameters['desc'] = $("#document_description").val();
            parameters['document_name'] = $("#document_name").val();
            parameters['id'] = $("#file_id").val();
            blockElement("#files_listing");
            $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
            {
                unblockElement("#files_listing");              
                refreshContractFiles();
                $.unblockUI();
            });
        });
        
        $('.btnedit').live('click',function(){
            var fileid;
            var parameters = {};
            $t = $(this);
            fileid = $t.attr("fid");
            parameters['type'] = 6;
            parameters['fileid'] = fileid;
            blockElement("#files_listing");
            $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
            {
                unblockElement("#files_listing");              
                $.blockUI({ message: data }); 
            });
        });
        
        // Inclusion Upload
        function beforeInclusionUpload(id)
    	{
    		var request_id = $("#request_id").val();
    		swfu_array[id].addPostParam("type","3");
    		swfu_array[id].addPostParam("upload","inclusion");
            swfu_array[id].addPostParam("request_id",request_id);
    		return true;
    	}
    	function afterInclusionUpload(id)
    	{
    	    refreshInclusionFiles();
    	    return true;
    	}
    	function refreshInclusionFiles()
    	{   
    	    var parameters = { };
    	    parameters['type'] = 4;
    	    parameters['upload'] = "inclusion";
    	    parameters['request_id'] = $("#request_id").val();
    	    blockElement('#inclusion_files_listing');
    	    $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data){
    	         $('#inclusion_page_listing').html(data.html);
    	         unblockElement('#inclusion_files_listing');
    	    },"json");
    	}
    	
    	$("#inclusion_delete_files").live('click',function()
        {
            if ($("input[name='imagestodelete[]']:checked",$('#inclusion_files_listing')).length == 0)
            {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }
    
            if (confirm("Are you sure you want to delete the selected files?"))
            {
    
                var selectedvalues = "";
                $("input[name='imagestodelete[]']:checked",$('#inclusion_files_listing')).each(function(){
                    selectedvalues += $(this).val() +';';
                });
                var parameters = {};
    
                parameters['type'] = 5;
                parameters['todelete'] = selectedvalues;
                parameters['upload'] = "inclusion";
                parameters['request_id'] = $("#request_id").val();
    
                blockElement("#inclusion_files_listing");
    
                $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
                {
                    unblockElement("#inclusion_files_listing");              
                    refreshInclusionFiles();
                });
            }
        });
        
        $("#inclusion_update_files").live('click',function(){
            var parameters = {};
            parameters['type'] = 7;
            parameters['upload'] = "inclusion";
            parameters['desc'] = $("#inclusion_document_description").val();
            parameters['document_name'] = $("#inclusion_document_name").val();
            parameters['id'] = $("#file_id").val();
            blockElement("#inclusion_files_listing");
            $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
            {
                unblockElement("#inclusion_files_listing");              
                refreshInclusionFiles();
                $.unblockUI();
            });
        });
        
        $('.btnedit_inclusion').live('click',function(){
            var fileid;
            var parameters = {};
            $t = $(this);
            fileid = $t.attr("fid");
            parameters['type'] = 6;
            parameters['upload'] = "inclusion";
            parameters['fileid'] = fileid;
            blockElement("#inclusion_files_listing");
            $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
            {
                unblockElement("#inclusion_files_listing");              
                $.blockUI({ message: data }); 
            });
        });
        
        // Plan Upload
        function beforePlanUpload(id)
    	{
    		var request_id = $("#request_id").val();
    		swfu_array[id].addPostParam("type","3");
    		swfu_array[id].addPostParam("upload","plan");
            swfu_array[id].addPostParam("request_id",request_id);
    		return true;
    	}
    	function afterPlanUpload(id)
    	{
    	    refreshPlanFiles();
    	    return true;
    	}
    	function refreshPlanFiles()
    	{   
    	    var parameters = { };
    	    parameters['type'] = 4;
    	    parameters['upload'] = "plan";
    	    parameters['request_id'] = $("#request_id").val();
    	    blockElement('#plan_files_listing');
    	    $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data){
    	         $('#plan_page_listing').html(data.html);
    	         unblockElement('#plan_files_listing');
    	    },"json");
    	}
    	
    	$("#plan_delete_files").live('click',function()
        {
            if ($("input[name='imagestodelete[]']:checked",$('#plan_files_listing')).length == 0)
            {
                alert('Please click on the checkbox to select the files you want to delete.');
                return;
            }
    
            if (confirm("Are you sure you want to delete the selected files?"))
            {
    
                var selectedvalues = "";
                $("input[name='imagestodelete[]']:checked",$('#plan_files_listing')).each(function(){
                    selectedvalues += $(this).val() +';';
                });
                var parameters = {};
    
                parameters['type'] = 5;
                parameters['todelete'] = selectedvalues;
                parameters['upload'] = "plan";
                parameters['request_id'] = $("#request_id").val();
    
                blockElement("#plan_files_listing");
    
                $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
                {
                    unblockElement("#plan_files_listing");              
                    refreshPlanFiles();
                });
            }
        });
        
        $("#plan_update_files").live('click',function(){
            var parameters = {};
            parameters['type'] = 7;
            parameters['upload'] = "plan";
            parameters['desc'] = $("#plan_document_description").val();
            parameters['document_name'] = $("#plan_document_name").val();
            parameters['id'] = $("#file_id").val();
            blockElement("#plan_files_listing");
            $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
            {
                unblockElement("#plan_files_listing");              
                refreshPlanFiles();
                $.unblockUI();
            });
        });
        
        $('.btnedit_plan').live('click',function(){
            var fileid;
            var parameters = {};
            $t = $(this);
            fileid = $t.attr("fid");
            parameters['type'] = 6;
            parameters['upload'] = "plan";
            parameters['fileid'] = fileid;
            blockElement("#plan_files_listing");
            $.post(base_url + 'admin/contractrequests/ajaxwork', parameters,function(data)
            {
                unblockElement("#plan_files_listing");              
                $.blockUI({ message: data }); 
            });
        });
        
        $('.w_close').live('click',function(e) {
            e.preventDefault();
            $(document).unblock();
        });
    </script>
</head>