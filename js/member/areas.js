/********************************* AREA LIST ***********************/
var AreaList = function()
{
    var self = this;
    this.paginator = false;
    this.lastSlide = null;
    this.searchDelay = 700;
    this.searchImminent = false;
    this.storeLatLongArray = new Array();
    this.sort_col = "nc_areas.area_name";
    this.sort_dir = "ASC";
    this.geoCount = 0;
    
    var objMap = '';
    var rows = ''; 
    var x = 0;
    
    // Entry point.
    this.init = function()
    {
        this.paginator = new Paginator($("#frmSearch"), "div.mainCol");
        
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
                alert("Sorry, something went wrong whilst loading the project listing");
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
                    if((rows[x].lat != "") && (rows[x].lng != ""))
                    {                
                        var row = rows[x]; 
                        var loc = new google.maps.LatLng(row.lat, row.lng, true);
                        self.addPoint(map, row, loc);                        
                    }                                
                }                                    
            }            
        }, "json");
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
        options["title"] = row.area_name;
        options["icon"] = image;
        options["shadow"] = shadow;
        options["shape"] = shape;
                                        
        var marker = new google.maps.Marker(options);
        
        // Build the infoWindow to display the store information when the user clicks on the store.
        var contentString = '<div id="mapPointInfo">'+
        '<h3>' + row.area_name + ', ' + row.state + '</h3>';
        
        if(row.image != null)
        {
            contentString += '<p><img src="' + row.image + '" width="190" />';    
        }
        
        contentString +=
        '<p>' + 
            'Median House Price $' + row.median_house_price + '<br/>' +
        '</p>' +
        '<p><a class="btn mapButton" href="' + row.url + ' ">View Details</a></p>' +
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
        self.storeLatLongArray.push({area_id : row.area_id, latLong: location})        
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
var AreaDetail = function()
{
    this.numShowing = 3;    // number of thumbnails displayed  
    this.numItems = 0;      // to compensate for index having a base of 0  
    
    var self = this;    // Reference to self/this
    
    // Entry point.
    this.init = function()
    {
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
    
    this.bindEvents = function()
    {
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

        $("#agentareabtn").click(function(e)
        {
            e.preventDefault();
            
            // Show the property reservation form
            $('#commentModal').reveal(
            {
                 animation: 'fadeAndPop',                   //fade, fadeAndPop, none
                 animationspeed: 300,                       //how fast animtions are
                 closeonbackgroundclick: true,              //if you click background will modal close?
                 dismissmodalclass: 'close-reveal-modal'    //the class of a button or element that will close an open modal
            });
        });
        
        $('#commentForm').live('submit',function(e){
            e.preventDefault();
            var $t = $(this);
            var comment = $(':input[name="comment"]',$t).val();
            if (comment=='') {
                alert('Please enter your comment.');
                return false;
            }
            var url = $t.attr('action');
            objApp.blockElement('#commentForm');
            $.post(url, $t.serializeArray(), function(rs){
                if (rs.status == 'SUCCESS') {
                    $('#commentsList').prepend(rs.html);
                    $(':input[name="comment"]',$t).val('');
                } else {
                    alert(rs.message);
                }
                objApp.unblockElement('#commentForm');
            },'json');
        });
      
    }      
}

var uri = objApp.getURI();

if(uri == "areas")
{
    var objList = new AreaList();

    // Load additional JS libs needed
    window.onload  = function()
    {        
        objApp.include("paginator.js");
        objApp.include("jquery.blockUI.js");
        objApp.include("jquery.validate.js");
        objApp.include("jquery.rangeslider.js");
        objApp.include("jquery-customCB.js");
        
        // Setup the stock list object
        objList.init(); 
        
        // Hide the curtain so we can see everything.
        objApp.hideCurtain();
    }    
}
else
{
var objDetail = new AreaDetail();

// Load additional JS libs needed
	window.onload  = function()
	{
		objApp.include("jquery-customCB.js");
		objApp.include("jquery.orbit-1.2.3.min.js");
		
		// Setup the advisor object
		objDetail.init(); 
		
		// Hide the curtain so we can see everything.
		objApp.hideCurtain();        
	}     
}