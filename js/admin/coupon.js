Date.format = 'dd/mm/YY'; 
    /**
    * Return true, if the value is a valid date, also making this formal check dd/mm/yyyy.
    *
    * @example jQuery.validator.methods.date("01/01/1900")
    * @result true
    *
    * @example jQuery.validator.methods.date("01/13/1990")
    * @result false
    *
    * @example jQuery.validator.methods.date("01.01.1900")
    * @result false
    *
    * @example <input name="pippo" class="{dateITA:true}" />
    * @desc Declares an optional input element whose value must be a valid date.
    *
    * @name jQuery.validator.methods.dateITA
    * @type Boolean
    * @cat Plugins/Validate/Methods
    */
    jQuery.validator.addMethod(
            "dateITA",
            function(value, element) {
                    var check = false;
                    var re = /^\d{1,2}\/\d{1,2}\/\d{4}$/
                    if( re.test(value)){
                            var adata = value.split('/');
                            var gg = parseInt(adata[0],10);
                            var mm = parseInt(adata[1],10);
                            var aaaa = parseInt(adata[2],10);
                            var xdata = new Date(aaaa,mm-1,gg);
                            if ( ( xdata.getFullYear() == aaaa ) && ( xdata.getMonth () == mm - 1 ) && ( xdata.getDate() == gg ) )
                                    check = true;
                            else
                                    check = false;
                    } else
                            check = false;
                    return this.optional(element) || check;
            }, 
            "Please enter a correct date"
    );
$(document).ready(function(){
    
	$('.date-pick').datePicker({startDate:'01/01/1987'});
    // Setup the tabs
    $("ul.skin2").tabs("div.skin2 > div"); 
    $("input.numeric").numeric();
    
    $('#frmCoupon').validate();
    //$("#discount").rules("add", {min: 0, max: 99999 });
    $('#frmCoupon #button').click(function(e){
        
        e.preventDefault();
        
        if ($( '#frmCoupon' ).valid())
        {
            var type = $( 'input[name=discount_type]:checked' ).val();
            
            if( type == 'products' ) 
            {
                //set buy ids
                var buy_ids = ""; 
                $('#buy option').each(function(i) {  
                    buy_ids += $(this).val() + ";";  
                }); 
                
                $("#buy_ids").val(buy_ids);
            }
            
            if( type == 'products' || type == 'amount')
            {            
                //set amount ids
                var reward_ids = "";
                $('#reward option').each(function(i) {  
                    reward_ids += $(this).val() + ";";  
                }); 
                
                $("#reward_ids").val(reward_ids);
                
                if(type == 'amount')
                {
                    $("#discount").val( $("#order_amount").val());                    
                }
            }
            
            if( type == 'value' || type == 'percentage')
                $("#discount").val( $("#discount_num").val());
            
            $( '#frmCoupon' ).submit();    
        }
    });    
    // change the discount type
    change_discount_type();
    
    // if changed discount type, than change the text to show
    $( 'input[name=discount_type]' ).click(function(){
    	change_discount_type();
    });
    
    // change the categories
    $( 'select[name=product_category_id]' ).change(function(){
    	
    	var parameters						= {};
    	parameters['type']					= '3';
    	parameters['product_category_id']	= $( 'select[name=product_category_id]' ).val();
    	//parameters['coupon_id']				= $( '#id' ).val();
    	
    	blockElement( 'input[name=product_id]' );
    	$.post( base_url + 'couponmanager/ajaxwork', parameters, function(data){
    		
    		if( data != '' )
    		{
    			$( '#products' ).html( data );
    		}
    		
    		unblockElement( 'input[name=product_id]' );
    	});
    	
    });
        
    // Handle click events on extra buttons
    $('.buy_div .left .button, .reward_div .left .button').live("click", function(e) {
        
        var side = ($(this).parent().parent().attr("class") == "reward_div") ? "reward" : "buy";
        
        // Get the value of the selected products
        val = $("#products").val();
        if(val == null)
        {
            alert("Please select a product to assign.");
            return;
        }
        
        // Get the text attribute of the products list
        text = $('#products :selected').text();
        
        //check if the value allready exists
        $option = $('#'+ side +' option[value*=' + val + '_]');
        if($option.length > 0)
        {
            var old_value = $option.val();
                        
            var value_array = old_value.split("_");
            
            var quantity = eval(value_array[1]) + 1;            
            var new_text = quantity + "x " + text;
            var new_value = value_array[0] + "_" + quantity;  
            
            $option.html(new_text);
            $option.val(new_value);            
        }
        else
        {
            // Create a new list item on the assigned list.
            $(new Option(text, val +"_1")).text("1x "+ text).appendTo('#' + side);
        }
        
        // Remove the selected option from the list
        //$('#products :selected').remove();
        
        //$("#products option:selected").removeAttr("selected");
    });
    
    $('.buy_div .right .button, .reward_div .right .button').live("click", function(e) {
        
        var side = ($(this).parent().parent().attr("class") == "reward_div") ? "reward" : "buy";
        
        // Get the value of the selected item
        val = $('#' + side).val();
        if(val == null)
        {
            alert("Please select a product to remove.");
            return;
        }
        
        // Get the text attribute of the assigned list item.
        text = $('#' + side + ' :selected').text();
        
        // Create a new list item on the assigned list.
        /*$(new Option(text, val)).appendTo('#products');*/
        
        //check if the value allready exists
        $option = $('#'+ side +' option[value*=' + val + ']');
        if($option.length > 0)
        {                                   
            var old_value = $option.val();
                                    
            var value_array = old_value.split("_");
            var text_array = text.split("x ");
            
            var quantity = eval(value_array[1]) - 1;            
            if( quantity <= 0)
            {
                // Remove the selected option from the list
                $('#' + side + ' :selected').remove(); 
            }
            else
            {
                var new_text = quantity + "x " + text_array[1];
                var new_value = value_array[0] + "_" + quantity;  
                
                $option.html(new_text);
                $option.val(new_value);           
            }
        }
        else
        {
            // Remove the selected option from the list
            $('#' + side + ' :selected').remove(); 
        }
    });
});
function change_discount_type()
{
    var type = $( 'input[name=discount_type]:checked' ).val();
    
    blockElement('#discount_type_div');
    $( '#discount_num' ).removeClass('required numeric');
    $( '#order_amount' ).removeClass('required numeric');
    switch(type)
    {                                          
        case 'percentage':
             $( '#discount_type' ).html( '%' );
             $( '#products_type_div' ).hide();
             $( '#discount_num' ).addClass('required numeric');
             $( '#percentage_value_div' ).show();
        break;
        
        case 'value':
            $( '#discount_type' ).html( '$' );
            $( '#products_type_div' ).hide();
            $( '#discount_num' ).addClass('required numeric');
            $( '#percentage_value_div' ).show();
        break;    
        
        case 'products':
            $( '#enter_amount_div ').hide();
            $( '#select_product_div ').show();
            $( '.buy_div .left .button').show();
            $( '#percentage_value_div' ).hide();                        
            $( '#products_type_div' ).show();            
        break;
                                                         
        case 'amount':
            $( '#select_product_div ').hide();
            $( '.buy_div .left .button').hide();
            $( '#enter_amount_div ').show();                        
            $( '#percentage_value_div' ).hide();
            $( '#order_amount' ).addClass('required numeric');           
            $( '#products_type_div' ).show();            
        break;                
    }     
    unblockElement('#discount_type_div');
    /*    
    if( $( 'input[name=discount_type]:checked' ).val() == 'percentage' )
    	$( '#discount_type' ).html( '%' );
    else
    	$( '#discount_type' ).html( '$' );*/
}