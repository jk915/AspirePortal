(function($){
    loadRequests = function(pageno) {
        var date_start,date_end,agent_name;
        date_start = '';
        date_end = '';
        agent_name = '';
        blockElement('#page_listing');
        $.get(base_url + 'admin/contractrequests/ajaxwork',{
		    type: 1,
            status: $('#stt').val(),
            agent_name: agent_name,
            date_start: date_start,
            date_end: date_end,
            p: pageno ? pageno : 1
        },function(rs){
            $('#page_listing').html(rs);
            unblockElement('#page_listing');
        });
    };
    
    $(function(){
        
        loadRequests();
        
        $('#apply_filter').live('click',function(){
    	    var stt,date_start,date_end,agent_name;
    	    agent_name = $('#agent_name').val();
            date_start = $('#date_start').val();
            date_end = $('#date_end').val();
            if(agent_name == "Enter Agent Name") {
    		    agent_name = "";
    		}
            if(date_end == "Request Date To") {
    		    date_end = "";
    		}
    		if(date_start == "Request Date From") {
    		    date_start = "";
    		}
    		stt = $("#stt").val();
    		blockElement('#page_listing');
    		$.get(base_url + 'admin/contractrequests/ajaxwork',{
    		    type: 1,
                agent_name: agent_name,
                status: stt,
                date_start: date_start,
                date_end: date_end
            },function(rs){
                if(rs) {
                    $('#page_listing').html(rs);
                    unblockElement('#page_listing');
                }
            });
        });
        
        $('a.page_numbers').live('click',function(){
            loadRequests($(this).attr('p'));
        });
    });

})(jQuery);