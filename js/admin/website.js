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
    
    $('#frmWebsite').validate();
    
    $('#frmWebsite #button').click(function(e){
                  
        e.preventDefault();
      
        if ($('#frmWebsite').valid())
        {
        	// If the user has entered start and end dates, make sure the end date is after the start date
        	var start_date = $("#start_date").val();
        	var expiry_date = $("#expiry_date").val();
        	var ok = true;
        	
        	if((start_date != "") && (expiry_date != ""))
        	{
                var adata = start_date.split('/');
                var dd = parseInt(adata[0],10);
                var mm = parseInt(adata[1],10);
                var yyyy = parseInt(adata[2],10);
                var date_start = new Date(yyyy,mm-1,dd);  
                
                adata = expiry_date.split('/');
                dd = parseInt(adata[0],10);
                mm = parseInt(adata[1],10);
                yyyy = parseInt(adata[2],10);
                var date_end = new Date(yyyy,mm-1,dd);
                
                if(date_end < date_start)
                {
					alert("The expiry date is before the start date.  Please set the expiry date to be after the start date");
					ok = false;
                }                       	
        	}
        	
        	if(ok)
            	$('#frmWebsite').submit();    
		}
    });
});