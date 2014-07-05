var formatFloat = function(num) {
    if (num=='') return '';
    num = num.toString().replace(/\$|\,/g, '');
    if (isNaN(num)) num = '0';
    sign = (num == (num = Math.abs(num)));
    num = Math.floor(num * 100 + 0.50000000001);
    cents = num % 100;
    num = Math.floor(num / 100).toString();
    if (cents < 10) cents = '0' + cents;
    for (var i = 0; i < Math.floor((num.length - (1 + i)) / 3); i++)
        num = num.substring(0, num.length - (4 * i + 3)) + ',' + num.substring(num.length - (4 * i + 3));
    return (((sign) ? '' : '-') + num + '.' + cents);
};

var calculateAmount = function() {
    var totalExtras = 0;
    var totalExtra = 0;
    $('table tr').each(function(){
        var $t = $(this);
        var $unit_price = $('.unit_price', $t);
        var $qty = $('.qty', $t);
        var unit_price = parseFloat($unit_price.val());
        var qty = parseInt($qty.val());
        if (!unit_price) unit_price = 0;
        if (!qty) qty = 0;
        var $extra = $('.total_extra', $t);
        var $hidden_total_extra = $('.hidden_total_extra', $t);
        totalExtra = unit_price * qty;
        $extra.html((totalExtra != 0) ? "$"+formatFloat(totalExtra) : "");
        $hidden_total_extra.val((totalExtra != 0) ? totalExtra : "");
        totalExtras += unit_price * qty;
    });
    var baseprice = $("#base_price").val();
    var commission = $("#commission_field").val();
    if (!baseprice) baseprice = 0;
    if (!commission) commission = 0;
    var totalIncGST = parseFloat(baseprice) + parseFloat(commission) + parseFloat(totalExtras);
    $('#totalextras').html((totalExtras != 0) ? '$'+formatFloat(totalExtras) : "");
    $('#totalinc').html((totalIncGST != 0) ? '$'+formatFloat(totalIncGST) : "");
    $('#total_price').val(totalIncGST);
};

$(function(){
    calculateAmount();
    
    $('.approve_contract').live('click', function(){
        $.fancybox({
            'maxWidth'	: 450,
    		'maxHeight'	: 150,
    		'autoSize'	: false,
            'href' : '#approve_contract'
        });
    });
    
    $('.reject_contract').live('click', function(){
        $.fancybox({
            'maxWidth'	: 450,
    		'maxHeight'	: 250,
    		'autoSize'	: false,
            'href' : '#reject_contract'
        });
    });
    
    $('.reopen_contract').live('click', function(){
        $.fancybox({
            'maxWidth'	: 450,
    		'maxHeight'	: 150,
    		'autoSize'	: false,
            'href' : '#reopen_contract'
        });
    });
    
    $('.btnreject_ok').live('click',function(){
       var id = $(this).attr("crid");
       var html = '';
       html += '<p><b>Please enter the reason for rejection:</b></p>';
       html += '<textarea id="message"></textarea>';
       html += '<div class="clear"></div></br>'
       html += '<input id="button" type="button" class="btnreject" value="Send" crid="'+id+'" style="width:50px;"/>'
       html += '<input id="button" type="button" class="btnclose" value="Close" style="width:50px;" onClick="parent.jQuery.fancybox.close();"/>'
       $('#reject_contract').html(html);
    });
    
    $('.btnapprove_ok').live('click',function(){
	    var crid;
		crid = $(this).attr("crid");
		blockElement('.actions');
		$.post(base_url + 'admin/contractrequests/ajaxwork',{
		    type: 2,
		    action: 'approve',
            request_id: crid
        },function(rs){
            if(rs == "OK") {
                unblockElement('.actions');
                window.location.href = base_url + 'admin/contractrequests/request/'+crid;
            } else {
                alert("Sorry, the contract request could not be found. Please try again.");
                $.fancybox.close();
                unblockElement('.actions');
                return;
            }
        });
    });
    
    $('.btnreject').live('click',function(){
	    var crid,message;
		crid = $(this).attr("crid");
		message = $('#message').val();
		blockElement('.actions');
		$.post(base_url + 'admin/contractrequests/ajaxwork',{
		    type: 2,
		    action: 'reject',
		    message: message,
            request_id: crid
        },function(rs){
            if(rs) {
                unblockElement('.actions');
                window.location.href = base_url + 'admin/contractrequests/request/'+crid;
            } else {
                alert("Sorry, the contract request could not be found. Please try again.");
                $.fancybox.close();
                unblockElement('.actions');
                return;
            }
        });
    });
    
    $('.btnreopen_ok').live('click',function(){
	    var crid;
		crid = $(this).attr("crid");
		blockElement('.actions');
		$.post(base_url + 'admin/contractrequests/ajaxwork',{
		    type: 2,
		    action: 'reopen',
            request_id: crid
        },function(rs){
            if(rs == "OK") {
                unblockElement('.actions');
                window.location.href = base_url + 'admin/contractrequests/request/'+crid;
            } else {
                alert("Sorry, the contract request could not be found. Please try again.");
                $.fancybox.close();
                unblockElement('.actions');
                return;
            }
        });
    });
    
    $('.qty').live("blur",function(){
        calculateAmount();
	});
	
	$('.unit_price').live("blur",function(){
	    $('.qty').attr({
          price: $(this).val()
        });
        calculateAmount();
	});
    
	$('#base_price').live("blur",function(){
        calculateAmount();
	});
    
    $("ul.skin2").tabs("div.skin2 > div");
    
    $('.btnremoverow').live('click',function(){
        $(this).parents('tr').remove();
        calculateAmount();
    });
    
    $('#btnadditem').click(function(){
        rowCount = $("#listExtra tr").length;
        var html = '<tr>';
            html+=    '<td><input style="width:250px;" type="text" size="20" class="item_name" name="extra[item_name][]"/></td>';
            html+=    '<td><input style="width:100px;" type="text" size="6" class="unit_price" name="extra[unit_price][]"/></td>';
            html+=    '<td><input style="width:100px;" type="text" size="6" class="qty" name="extra[qty][]"/><input type="hidden" name="extra[total_extra][]" class="hidden_total_extra"/></td>';
            html+=    '<td class="total_extra"></td>';
            html+=    '<td><a href="javascript:;" class="btnremoverow" title="Delete extra item."><img src="'+base_url+'images/window_close.png" /></a></td>';
            html+= '</tr>';
        $('#listExtra').append(html);
    });
});