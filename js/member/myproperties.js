/********************************* STOCK LIST **********************
*/
var MyProperties = function()
{
    var self = this;
    this.paginator = false;
    this.lastSlide = null;
    this.searchDelay = 700;
    this.searchImminent = false;
    this.storeLatLongArray = new Array();
    this.sort_col = "p.featured DESC, p.rent_yield";
    this.sort_dir = "DESC";
    this.geoCount = 0;
    
    var objMap = '';
    var rows = ''; 
    var x = 0;
    
    // Entry point.
    this.init = function()
    {
        this.paginator = new Paginator($("#frmSearch"), "div.mainCol");
        
        $('#sliderBeds').noUiSlider('init', 
        {
            scale: [min_bedrooms,max_bedrooms],
            start: [min_bedrooms,max_bedrooms],
            change:
            function()
            {
                // the noUiSlider( 'value' ) method returns an array.
                var values = $(this).noUiSlider( 'value' );
                
                // Set hidden field values
                $("#min_bedrooms").val(values[0]);
                $("#max_bedrooms").val(values[1]);
                
                $(this).parent().find('p .lowerVal').text(values[0]);
                
                $(this).parent().find('p .upperVal').text(values[1]);
                if(values[1] == 5)
                {
                    $(this).parent().find('p .upperVal').append('+');
                }
                
                self.checkDoSearch();
            }, 
        });
        
        
        $('#sliderBaths').noUiSlider('init', 
        {
            scale: [min_bathrooms,max_bathrooms],
            start: [min_bathrooms,max_bathrooms],
            change:
            function()
            {
                // the noUiSlider( 'value' ) method returns an array.
                var values = $(this).noUiSlider( 'value' );
                
                // Set hidden field values
                $("#min_bathrooms").val(values[0]);
                $("#max_bathrooms").val(values[1]);                
                
                $(this).parent().find('p .lowerVal').text(values[0]);
                
                $(this).parent().find('p .upperVal').text(values[1]);
                if(values[1] == 5)
                {
                    $(this).parent().find('p .upperVal').append('+');
                }
                
                self.checkDoSearch();
            }, 
        });          
        
        $('#sliderHouseArea').noUiSlider('init', 
        {
            scale: [min_house,max_house],
            start: [min_house,max_house],
            change:
            function()
            {
                // the noUiSlider( 'value' ) method returns an array.
                var values = $(this).noUiSlider( 'value' );  
                
                // Set hidden field values
                $("#min_house").val(values[0]);
                $("#max_house").val(values[1]);                   
                                      
                $(this).parent().find('p .lowerVal').text(values[0]);                        
                $(this).parent().find('p .upperVal').text(values[1]); 
                
                self.checkDoSearch();                   
            }, 
        });                      
                        
        
        $('#sliderLandArea').noUiSlider('init', 
        {
            scale: [min_land,max_land],
            start: [min_land,max_land],
            change:
            function()
            {
                // the noUiSlider( 'value' ) method returns an array.
                var values = $(this).noUiSlider( 'value' ); 
                
                // Set hidden field values
                $("#min_land").val(values[0]);
                $("#max_land").val(values[1]);                 
                                       
                $(this).parent().find('p .lowerVal').text(values[0]);                        
                $(this).parent().find('p .upperVal').text(values[1]); 
                
                self.checkDoSearch();                  
            }, 
        });     
        
        $('#sliderPrice').noUiSlider('init', 
        {
            scale: [min_total_price,max_total_price],
            start: [min_total_price,max_total_price],
            change:
            function()
            {
                // the noUiSlider( 'value' ) method returns an array.
                var values = $(this).noUiSlider( 'value' ); 
                
                // Set hidden field values
                $("#min_total_price").val(values[0]);
                $("#max_total_price").val(values[1]);                
                                       
                $(this).parent().find('p .lowerVal').text(values[0]);                        
                $(this).parent().find('p .upperVal').text(values[1]);
                
                self.checkDoSearch();                    
            }, 
        });
        
        $('#sliderYield').noUiSlider('init',
        {
            scale: [min_yield * 10,max_yield * 10],
            start: [min_yield * 10,max_yield * 10],
            change:
            function()
            {
                // the noUiSlider( 'value' ) method returns an array.
                var values = $(this).noUiSlider( 'value' ); 
                
                // Set hidden field values
                $("#min_yield").val(values[0] / 10);
                $("#max_yield").val(values[1] / 10);                
                                       
                $(this).parent().find('p .lowerVal').text(values[0] / 10);                        
                $(this).parent().find('p .upperVal').text(values[1] / 10);
                
                self.checkDoSearch();                    
            }, 
        });           
               
        
        $('input:checkbox').screwDefaultButtons(
        {
             checked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
             unchecked: "url(" + base_url + "images/member/frm-su-checkbox.png)",
             width: 15,
             height: 16
        });        
         
        $('input:radio').screwDefaultButtons(
        {
             checked: "url(" + base_url + "images/member/frm-su-radio.png)",
             unchecked: "url(" + base_url + "images/member/frm-su-radio.png)",
             width: 15,
             height: 16
        }); 
         
        $('#tabs>li').css('display', 'none');
        $('#tabs>li:nth-child(1)').css('display', 'block');
        
        /* tabs */
        $('#tabs > li').hide();
        $('#tabs > li:first-child').show();
        $('#tabNav li:first-child a').addClass('active');
        
        // Set the currently selected list type
        $("#list_type").val($('#tabNav a.active').attr('list_type'));
                
        this.bindEvents();
        
        $("#frmSearch #current_page").val("1");
    
        $("#frmSearch").submit();
    } 
    
    this.bindEvents = function()
    {
        $("#frmSearch").submit(function(e)
        {
            e.preventDefault();
            
            self.doSearch("#frmSearch");    
        });
        
        $('div.styledRadio').click(function()
        {
            $("#frmSearch").submit();
        });
        
        $("#frmSearch input[type='text']").keyup(function()
        {
            self.checkDoSearch();    
        });
        
        $("#frmSearch select").change(function()
        {
            $("#frmSearch").submit();    
        });        
        
        $('#tabNav a').click(function(e) 
        {
            e.preventDefault();
            
            $('#tabNav a').removeClass('active');
            $(this).addClass('active');
            var index = $(this).closest('li').index();
            $('#tabs>li').hide();
            $("#tabs>li").eq(index).show();
            
            // Set the list type
            $("#list_type").val($(this).attr("list_type"));
            
            // Resubmit the search form
            $("#frmSearch").submit();
            
            return false;
        });
        
        $("table.listing th").click(function(e)
        {
            var sort_by = $(this).attr("sort");
            
            if(sort_by == self.sort_col)
            {
                if(self.sort_dir == "ASC")
                {
                    self.sort_dir = "DESC";    
                }
                else
                {
                    self.sort_dir = "ASC";    
                }
            }    
            else
            {
                self.sort_col = sort_by;
                self.sort_dir = "ASC";         
            }
            
            self.doSearch("#frmSearch");
        });
    }
    
    this.checkDoSearch = function()
    {
        self.lastSlide = new Date();
        
        if(!this.searchImminent)
        {
            self.searchImminent = true;        
        
            setTimeout(function()
            {
                self.checkSubmit();
            }, self.searchDelay);
        }        
    }
    
    this.checkSubmit = function()
    {
        var now = new Date();
        var diff = now - self.lastSlide;
        
        if(diff > (this.searchDelay - 100))
        {
            $("#frmSearch").submit();
        }
        else
        {
            setTimeout(function()
            {
                self.checkSubmit();    
            }, self.searchDelay);
        }   
    }    
    
    this.doSearch = function(form)
    {
        // If the form search event is being called but not via the paginator,
        // reset the current page number
        if(!self.paginator.paging_changed)
        {
            $("#frmSearch #current_page").val("1"); 
            self.paginator.current_page = 1;  
        }
        
        $(form).find("#sort_col").val(this.sort_col);
        $(form).find("#sort_dir").val(this.sort_dir);
        
        var status = $("#status").val();
        var list_type = $("#list_type").val();
        
        $("ul.pagination").show();                                            
        
        var params = $(form).serialize(); 
        
        objApp.blockElement(form);
        
        $.post($(form).attr("action"), params, function(data)
        {
            objApp.unblockElement(form);
            self.searchImminent = false; 
            
            if(data.status != "OK")
            {
                alert("Sorry, something went wrong whilst loading the stock listing");
                return;    
            }

            $("ul.propertyListing").html(data.message);  
                
            $('.propertyListing>li').mouseenter(function()
            {
                $(this).parent().find('li').not(this).css("opacity", 0.3);
                $(this).find('div.additionalInfo').show('fast');
            }).mouseleave(function(){
                $('.propertyListing>li').css("opacity", 1);
                 $(this).find('div.additionalInfo').hide('fast');

            });
            
            // Set the total number of records into the form and update the paginator
            $("#count_all").val(data.count_all);
            
        }, "json");
    } 
}

var uri = objApp.getURI();

var objMyProperties = new MyProperties();

// Load additional JS libs needed
window.onload  = function()
{        
    objApp.include("paginator.js");
    objApp.include("jquery.rangeslider.js");
    objApp.include("jquery-customCB.js");
    
    // Setup the stock list object
    objMyProperties.init(); 
    
    // Hide the curtain so we can see everything.
    objApp.hideCurtain();
}