/********************************* STOCK LIST **********************
*/
var StockList = function()
{
    var self = this;
    this.paginator = false;
    this.lastSlide = null;
    this.searchDelay = 700;
    this.searchImminent = false;
    this.storeLatLongArray = new Array();
    this.sort_col = "p.rent_yield";
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
            }
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
            }
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
            }
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
            }
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
            }
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
            }
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
        
        if(list_type == "list")
        {
            if(status == "available")
            {
                $("table.listing tr.intro td").text("Available Properties");    
            }
            else if(status == "reserved")
            {
                $("table.listing tr.intro td").text("Reserved Properties");    
            } 
            else if(status == "signed")
            {
                $("table.listing tr.intro td").text("Signed Properties");    
            } 
            else if(status == "sold")
            {
                $("table.listing tr.intro td").text("Sold Properties");    
            }   
            else
            {
                $("table.listing tr.intro td").text("All Properties");    
            } 
            
            $("ul.pagination").show();                                            
        }
        else if(list_type == "grid")
        {
            $('ul.propertyListing > li').unbind();
            $("ul.propertyListing").html("");
            $("ul.pagination").show();
        }
        else
        {
            // Hide the pagination in map mode.
            $("ul.pagination").hide();    
        }
        
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

            if(list_type == "list")
            {
                $("table.listing tbody").html(data.message);
                
                // Set the total number of records into the form and update the paginator
                $("#count_all").val(data.count_all);

                self.paginator.refresh();                
            }
            else if(list_type == "grid")
            {
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

                self.paginator.refresh();                                              
            }                        
            else if(list_type == "map")
            {
                self.geoCount = 0;
                
                // Create the google map
                var latlng = new google.maps.LatLng(-27.994167,134.866944);
                
                var myOptions = {
                    zoom: 4,
                    center: latlng,
                    mapTypeId: google.maps.MapTypeId.ROADMAP
                };
                
                objMap = new google.maps.Map(document.getElementById("map"), myOptions);
                
                // Create a geocoder object so we can reverse address strings into lat/long
                var geocoder = new google.maps.Geocoder();
                
                // Loop through all data rows
                rows = data.message;
                x = 0;
                
                for(x = 0; x < rows.length; x++)
                {
                    // Make sure we have address, suburb and postcode at a minimum and then add the store to the map.
                    //if((rows[x].address != "") && (rows[x].suburb != "") && (rows[x].state != ""))
                    //{
                    if((rows[x].lat != "") && (rows[x].lng != ""))
                    {
                        self.addToMap(objMap, geocoder, rows[x]);
                    }
                    //}
                }
            }
        }, "json");
    }
    
    /***
    * addToMap
    * Creates a google friendly address string, and if necessary, uses the Maps Geocoder object to reverse
    * that string into a map lat/lng location.  If the store already has the lat/lon stored in the database then
    * the geocoder is not used.
    */    
    this.addToMap = function(map, geocoder, row)
    {
        // Create the google friendly address string.  This will be reversed into long/latt.
        var address = row.address + "," + row.suburb + "," + row.postcode + "," + row.state + ",Australia"; 
        address = address.replace(/ /g, "+");
        
        // If there is no lat/long stored in the database for this store, use the Google
        // geocoder to determine it.  Otherwise use the db values.
        if((row.lat == null) && (self.geoCount < 10))
        {
            // Setup a request object to pass the address details to geocoder.
            var request = {};
            request["address"] = address;
            request["region"] = "AU";

            // Resolve the address into long/latt
            geocoder.geocode(request, function(geoCoderResults, status) 
            {
                self.geoCount++;
                
                if ((status == "OK") && (geoCoderResults.length > 0))
                {
                    // Geocoding was successful.  Add the point to the map.
                    var location = geoCoderResults[0].geometry.location;
                    
                    // Send a request back to the server to update this property with the resolved lat/lng
                    var form = $("#frmSetLatLng");
                    
                    $(form).find("#property_id").val(row.property_id);
                    $(form).find("#lat").val(location.lat());
                    $(form).find("#lng").val(location.lng());
                    
                    var params = $(form).serialize(); 
                    
                    $.post($(form).attr("action"), params, function(data)
                    {
                        
                    }, "json");
                    
                    //alert(location.lat() + "   " + location.lng());
                    if (self.isExistedLatLong(location))
                    {
                        var ran1 = Math.random();
                        var ran2 = Math.random();
                        var sign1 = 1;
                        
                        if (ran1 < 0.5)
                            sign1 = -1;
                            
                        var sign2 = 1;
                        
                        if (ran2 < 0.5)
                            sign2 = -1;
                            
                        var extra_lat = sign1*(0.5/10000 + ran1/10000);
                        var extra_lng = sign2*(0.5/10000 + ran2/10000);
                        var n_location = new google.maps.LatLng(location.lat()+extra_lat, location.lng()+extra_lng, true);
                        self.addPoint(map, row, n_location);
                    }
                    else
                    {
                        self.addPoint(map, row, location);
                    }                        
                }
            });
        }
        else if(row.lat != null)
        {
            // This store has the lat/long stored in the database already
            var loc = new google.maps.LatLng(row.lat, row.lng, true);
            self.addPoint(map, row, loc);
        }    
    }
    
    /***
    * addPoint
    * Adds a location to the map with the cusomised marker.
    */
    self.addPoint = function(map, row, location)
    {
        // Customise the point icon with the Hosking icon.
        var icon = 'map_point.png';
        switch (row.rate)
        {
			case 'very_low':
        		icon = 'map_point_blue.png';
            	break;
            case 'low':
            	icon = 'map_point_green.png';
            	break;
            case 'medium':
            	icon = 'map_point_yellow.png';
            	break;
            case 'high':
            	icon = 'map_point.png';
            	break;
        	case 'very_high':
        		icon = 'map_point_black.png';
            	break;
        }
        
        var image = new google.maps.MarkerImage(
          base_url + 'images/member/' + icon,
          new google.maps.Size(22,29),
          new google.maps.Point(0,0),
          new google.maps.Point(11,29)
        );

        var shadow = new google.maps.MarkerImage(
          base_url + 'images/member/shadow.png',
          new google.maps.Size(40,29),
          new google.maps.Point(0,0),
          new google.maps.Point(11,29)
        );

        var shape = {
          coord: [15,0,17,1,18,2,19,3,20,4,20,5,21,6,21,7,21,8,21,9,21,10,21,11,21,12,21,13,21,14,21,15,20,16,20,17,19,18,18,19,17,20,15,21,14,22,14,23,13,24,13,25,12,26,12,27,12,28,10,28,10,27,9,26,9,25,9,24,8,23,8,22,6,21,4,20,3,19,2,18,1,17,1,16,0,15,0,14,0,13,0,12,0,11,0,10,0,9,0,8,0,7,0,6,1,5,1,4,2,3,3,2,5,1,6,0,15,0],
          type: 'poly'
        };              
        
        // Create a new marker object                    
        var options = {};
        options["position"] = location;
        options["title"] = "Lot " + row.lot + ", " + row.address;
        options["icon"] = image;
        options["shadow"] = shadow;
        options["shape"] = shape;
                                        
        var marker = new google.maps.Marker(options);
        
        var nras = (row.nras == 1) ? "Yes" : "No";
        var smsf = (row.smsf == 1) ? "Yes" : "No";
        
        // Build the infoWindow to display the store information when the user clicks on the store.
        var contentString = '<div id="mapPointInfo">'+
        '<h3>' + row.lot + ', ' + row.address + '</h3>';
        
        if(row.image != null)
        {
            contentString += '<p><img src="' + row.image + '" width="190" />';    
        }
        
        contentString +=
        '<p>' +
        '<img src="' + base_url + 'images/member/icon-bedrooms.png" width="24" height="14" /> ' + row.bedrooms + ' &nbsp; ' +
        '<img src="' + base_url + 'images/member/icon-bathrooms.png" width="24" height="14" /> ' + row.bathrooms + ' &nbsp; ' +
        '<img src="' + base_url + 'images/member/icon-garage.png" width="24" height="14" /> ' + row.garage +
        '</p>' +
        '<p>' + 
            'Price: $' + row.total_price + '<br/>' +
            'NRAS: ' + nras + '<br/>' +
            'SMSF: ' + smsf + '<br/>' +
            'Rent Yield: ' + row.rent_yield + '%' +
        '</p>' +
        '<p><a class="btn mapButton" href="' + base_url + 'stocklist/detail/' + row.property_id + '">View Details</a></p>' +
        '</div>'
        
        var infowindow = new google.maps.InfoWindow({
            content: contentString
        });                
        
        // Bind the click event for the marker.
        google.maps.event.addListener(marker, 'click', function() 
        {
            // Centre on this location and zoon
            // Zoom the map to this location 
            objMap.panTo(marker.getPosition());
            objMap.setZoom(14);            
            
            infowindow.open(objMap,marker);
        });                 
   
        marker.setMap(objMap);
        self.storeLatLongArray.push({property_id : row.property_id, latLong: location})        
    } 
    
    this.isExistedLatLong = function(location)
    {
        var len = self.storeLatLongArray.length;
    
        for (var i = 0; i < len; i++)
        {
            if (self.storeLatLongArray[i].latLong.equals(location))
                return true;
        }
        
        return false;
    }         
}


/************************** STOCK DETAIL ******************************/
var StockDetail = function()
{
    this.numShowing = 3;    // number of thumbnails displayed  
    this.numItems = 0;      // to compensate for index having a base of 0  
    
    var self = this;    // Reference to self/this
    
    // Entry point.
    this.init = function()
    {
	
		$('#floorThumb').live('click',function(){
            $("#floorThumb").colorbox({transition:"fade", open: "false"});
        });
	
        $('.propertyadvisorinfo').live('click',function(){
            $('#propertyAdvisorModal').reveal({
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
        });
        
        $('#commission_comments_form').live('submit',function(e){
            e.preventDefault();
            var $t = $(this);
            var $submit = $(':submit', $t);
            var url = $t.attr('action');
            $submit.attr('disabled',true).val('Saving. Please wait...');
            $.post(url,$t.serializeArray(),function(rs){
                if (rs=='OK') {
                    alert('Saved Changed.');
                    $submit.attr('disabled',false).val('Save Changes');
                } else {
                    alert('Error occured. Please try again!');
                }
            });
        });

        $('#hero').orbit(
        {
            animation: 'fade',
            bullets : true,        
            bulletThumbs: true,
            bulletThumbLocation: base_url + '/' 
        }); 

        /* thumb carousel */ 
        $('ul.orbit-bullets').wrap('<div id="thumbWrapper" />');
        this.numItems = $('ul.orbit-bullets li').length-1; 
        $('.orbit-bullets li:first-child').addClass('activeThumb');
        
        // If there are less than 3 images, hide the prev/next buttons
        if(this.numItems < 4)
        {
            $('a#nextThumb').hide();    
            $('a#prevThumb').hide();
        }
        
        $('#thumbWrapper li').click(function(){                
            $('#floorplan').hide('medium');
        });                 

        // show/hide floorplan
        $('#floorplan').hide();

        /* tabs */
        $('.tabs > li').hide();
        $('.tabs > li:first-child').show();
        $('.tabNav li:first-child a').addClass('active');
        
        $("div.sidebar p:first-child").addClass("imp");
  
        this.bindEvents();
    }
    
    this.validateEmail = function (email) {
        var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
        return re.test(email);
    }
    
    this.bindEvents = function()
    {
        $('#print_brochure').click(function(){
            $('#preparedPrintModalForm').submit();
        });
        $('#print_brochure_property').click(function(e){
            e.preventDefault();
            
            // Show the prepared print form
            $('#preparedPrintModal').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
        });
        
        $('#manual_type').click(function(){
            if ($(this).is(':checked')) {
                $("#prepared_for_manual").show();
                $("#prepared_for").hide();
            } else {
                $("#prepared_for_manual").hide();
                $("#prepared_for").show();
            } 
        });
        
        $('#add_summary').click(function(){
            if ($(this).is(':checked')) {
                $("#summary").show();
            } else {
                $("#summary").hide();
            } 
        });
    
        /***
        * Handle the event when the user clicks on the floorplan
        */
        $('a#floorThumb').click(function(e)
        {
            e.preventDefault();
            $('#floorplan').toggle('medium');
        }); 
                
        // add scrolling action to arrows 
        $('a#nextThumb').click(function(e)
        {
            e.preventDefault();
            
            var moveAmount = 0;
            var activeIndex = $('ul.orbit-bullets').find('li.activeThumb').index();
            
            // update activeIndex
            activeIndex += self.numShowing;    

            if (activeIndex > self.numItems) //ie we're at the end of the list, go back to the start
            {
                activeIndex = 0;
                $('.orbit-bullets').stop().animate({'left': moveAmount}, 'medium');                   
            }            
            else
            {
                var c = activeIndex;                  

                while (c > 0)
                {
                    moveAmount += $('.orbit-bullets').find('li').eq(c).outerWidth();
                    c--;
                }

                moveAmount *= -1; // we want to move to the left
                $('.orbit-bullets').stop().animate({'left': moveAmount}, 'medium'); 
            }

            $('.orbit-bullets li').removeClass('activeThumb');
            $('.orbit-bullets').find('li').eq(activeIndex).addClass('activeThumb');
        });   

        $('a#prevThumb').click(function(e)
        {
            e.preventDefault();
            
            var moveAmount = 0;
            var activeIndex = $('ul.orbit-bullets').find('li.activeThumb').index();

            if (activeIndex < self.numShowing) //ie we're at the start of the list
            {
                activeIndex = (Math.round(self.numItems / self.numShowing) * self.numShowing);

                if (activeIndex > self.numItems)
                {
                    activeIndex -= self.numShowing;
                }

                var c = activeIndex;

                while (c > 0)
                {
                    moveAmount += $('.orbit-bullets').find('li').eq(c).outerWidth();
                    c--;
                }
                
                moveAmount *= -1; // we want to move to the left
                $('.orbit-bullets').stop().animate({'left': moveAmount}, 'medium');                  
            }            
            else
            {                           
                activeIndex -= self.numShowing;                   
                var c = activeIndex;
                c--;

                while (c >= 0)
                {                       
                    moveAmount += $('.orbit-bullets').find('li').eq(c).outerWidth();
                    c--;                                 
                }

                moveAmount *= -1; 
                $('.orbit-bullets').stop().animate({'left': moveAmount}, 'medium'); 
            }

            $('.orbit-bullets li').removeClass('activeThumb');
            $('.orbit-bullets').find('li').eq(activeIndex).addClass('activeThumb');
        });
        
        $('.tabNav a').click(function(e) 
        {
            e.preventDefault();
            
            $('.tabNav a').removeClass('active');
            $(this).addClass('active');
            $('.tabs > li').hide();
            var index = $(this).closest('li').index();
            $(".tabs > li").eq(index).show();
        }); 
        
        $("#btnReserve").click(function(e)
        {
            e.preventDefault();
            
            // Show the property reservation form
            $('#reserveModal').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
        });
        
        $("#btnReservationRequest").click(function(e)
        {
            e.preventDefault();
            
            // Show the property reservation form
            $('#reservationRequestModal').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
        });
        
        $('#cancelReserveBtn,#cancelReservationRequestBtn').click(function(){
            $('#reservationRequestModal a.close-reveal-modal,#reserveModal a.close-reveal-modal').trigger('click');
            return false;
        });
        
        $('#investor_id').change(function(){
            var uid = this.value;
            if (uid=='') {
                $('#reserveForm :input[type="text"],#reserveForm select').val('');
            } else {
                objApp.blockElement('#reserveForm');
                $.post(base_url + 'stocklist/ajax',{
                    csrftokenaspire: $(':hidden[name="csrftokenaspire"]').val(),
                    action: 'get_investor_detail',
                    id: uid
                },function(rs){
                    if (rs.status=='SUCCESS') {
                        for (var i in rs)
                        {
                            $('#'+i).val(rs[i]);
                            
                            if(i == "smsf_purchase")
                            {
                                if(rs[i] == 'Yes')
                                    $('.yes_smsf').attr('checked', 'checked');
                                    
                                if(rs[i] == 'No')
                                {
                                    $('.no_smsf').attr('checked', 'checked'); 
                                }      
                                
                                if(rs[i] == null)                     
                                   $('#'+i).removeAttr('checked');  
                            }                       
                        }
                    } else {
                        alert('Unknown client, please try again!');
                    }
                    objApp.unblockElement('#reserveForm');
                },'json');
            }
        });
        
        $('#submitReserveForm').live('click',function(){
            var clientid = $('#investor_id').val();
            var firstName = $('#first_name').val();
            var lastName = $('#last_name').val();
            var mobile = $('#mobile').val();
            var email = $('#email').val();
            var errors = [];
            
            if (clientid=='') errors.push('+ Client field is required.');
            if (firstName=='') errors.push('+ First name field is required.');
            if (lastName=='') errors.push('+ Last name field is required.');
            if (email=='') errors.push('+ Email field is required.');
            else if (!self.validateEmail(email)) errors.push('+ Email field is invalid.');
            if (mobile=='') errors.push('+ Mobile phone field is required.');
            
            if (errors.length==0) {
                objApp.blockElement('#reserveModal');
                var data = $('#reserveForm').serializeArray();
                
                $.post(base_url + 'stocklist/ajax', data, function(rs) {
                    if (rs.status=='SUCCESS') {
                        alert('Thank you! Your reservation has been submitted successfully.');
                        window.location.reload();
                    } else {
                        alert(rs.message);
                    }
                    objApp.unblockElement('#reserveModal');
                }, 'json');
            } else {
                alert('Please correct the error(s) below:' + "\n" + errors.join("\n"));
            }
        });
        
        $('#submitReservationRequestForm').click(function(){
            var form = $("#reservationRequestForm");
            objApp.blockElement('#reservationRequestModal');
            
            if(!$(form).validate().form())
            {
                alert("Please enter all required fields");
                return;    
            }
            
            var data = $(form).serializeArray();
            
            $.post(base_url + 'stocklist/ajax', data, function(rs) {
                if (rs.status=='SUCCESS') {
                    alert('Thank you! Your reservation request has been sent successfully.');
                    window.location.reload();
                } else {
                    alert(rs.message);
                }
                objApp.unblockElement('#reservationRequestModal');
            }, 'json');
        });
        
        $("#btnaddToFavourites").click(function(e)
        {
            e.preventDefault();
            
            // Show the property reservation form
            $('#addToFavouritesModal').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
        });
        
        $('#submitaddToFavourites').live('click',function()
        {
        	objApp.blockElement('#addToFavouritesModal');
            var data = $('#addToFavouritesForm').serializeArray();
            
            $.post(base_url + 'favourites/ajax', data, function(rs) {
                
            	objApp.unblockElement('#addToFavouritesModal');
            	if (rs.status=='OK') {
                    alert('Thank you! This property has been added to your favourites.');
                    $('#btnaddToFavourites').hide();
                    $(".close-reveal-modal").click();
                } else {
                    $(".add_favourites_error").html('<h4>The Following Error Occured</h4><p>' + rs.message + '</p>'); 
	                $(".add_favourites_error").show();
	                return;
                }
                
            }, 'json');
        });
        
        
        $("#btnremoveFromFavourites").click(function(e)
        {
            e.preventDefault();
            
            // Show the property reservation form
            $('#removeFromFavouritesModal').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
        });
        
        $('#submitRemoveFromFavourites').live('click',function()
        {
            objApp.blockElement('#removeFromFavouritesModal');
            var data = $('#removeFromFavouritesForm').serializeArray();
            
            $.post(base_url + 'favourites/ajax', data, function(rs) {
                
                objApp.unblockElement('#removeFromFavouritesModal');
                if (rs.status=='OK') {
                    alert('Thank you! This property has been removed from your favourites.');
                    $('#btnremoveFromFavourites').hide();
                    $(".close-reveal-modal").click();
                } else {
                    $(".remove_favourites_error").html('<h4>The Following Error Occured</h4><p>' + rs.message + '</p>'); 
                    $(".remove_favourites_error").show();
                    return;
                }
                
            }, 'json');
        }); 

        $('.close-reveal').live('click',function()
        {
        	$(".close-reveal-modal").click();
        });
        
    }
}

var uri = objApp.getURI();

if(uri.indexOf("/detail") < 0)
{
    var objStockList = new StockList();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("paginator.js");
        objApp.include("jquery.blockUI.js");
        objApp.include("jquery.validate.js");
        objApp.include("jquery.rangeslider.js");
        objApp.include("jquery-customCB.js");
        
        // Setup the stock list object
        objStockList.init(); 
        
        // Hide the curtain so we can see everything.
        objApp.hideCurtain();
    }    
}
else
{
    var objStockDetail = new StockDetail();

    // Load additional JS libs needed
    window.onload  = function()
    {
        objApp.include("jquery-customCB.js");
        objApp.include("jquery.orbit-1.2.3.min.js");
        
        // Setup the advisor object
        objStockDetail.init(); 
        
        // Hide the curtain so we can see everything.
        objApp.hideCurtain();        
    }     
}


$('#user_id').live('change',function()
        {
		
        
        	var user_id = $(this).val();
        	if ((user_id != undefined) && (user_id != null) && (user_id != ''))
        	{
        		
			    //var property_id = $('#property_id').val();
	        	$.post(base_url+'stocklist/ajax', {
		    		action: 'assign_stock_permission',
		    		user_id: user_id,
					property_id: property_id ,
		    		csrftokenaspire: $('input[name="csrftokenaspire"]').val()
		    		
		    	},"json");
        	}
        });