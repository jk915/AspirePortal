$(document).ready(function()
{
    // Handle click events on extra buttons
    $('.btnAssignBlock').click(function(e) {
        
        e.preventDefault();
        
        side = ($(this).attr("href") == "right") ? "_right" : "_left";
        
        // Get the value of the selected item
        val = $('#blocks_available' + side).val();
        if(val == null)
        {
            alert("Please select a block to assign.");
            return;
        }
        
        // Get the text attribute of the assigned list item.
        text = $('#blocks_available' + side + ' :selected').text();
        
        // Create a new list item on the assigned list.
        $(new Option(text, val)).text(text).appendTo('#blocks' + side);
        
        // Remove the selected option from the list
        $('#blocks_available' + side +' :selected').remove(); 
    });
        
    $('.btnRemoveBlock').click(function(e) {
        
        e.preventDefault();
        
        side = ($(this).attr("href") == "right") ? "_right" : "_left";
        
        // Get the value of the selected item
        val = $('#blocks' + side).val();
        if(val == null)
        {
            alert("Please select a block to remove.");
            return;
        }
        
        // Get the text attribute of the assigned list item.
        text = $('#blocks' + side + ' :selected').text();
        
        // Create a new list item on the assigned list.
        $(new Option(text, val)).appendTo('#blocks_available' + side);
        
        // Remove the selected option from the list
        $('#blocks' + side + ' :selected').remove(); 
    });
    
    // When the assigned blocks select list is clicked up, show the up/down buttons.
    $("#blocks_right").click(function() {
        if($("#blocks_right").length > 0)
            $("#updown_right").fadeIn();
    }); 
    
    $("#blocks_left").click(function() {
        if($("#blocks_left").length > 0)
            $("#updown_left").fadeIn();
    }); 
    
    // Handle the move up event - the user wants to move a block up so
    // it appears before other blocks.
    $('.btnMoveUp').click(function(e) 
    {
        e.preventDefault();
        
        side = ($(this).attr("href") == "right") ? "_right" : "_left";
        
        // In order to move a block up, the user must have selected a block
        // at index 1 or above.
        var current_index = $("#blocks" + side).attr("selectedIndex");
        if(current_index < 1)
        {
            alert("Please select a valid block to move up.");
            return false;
        }
        
        // Get the value / text of the selected element
        val = $('#blocks' + side +' :selected').val();
        text = $('#blocks' + side +' :selected').text();
        
        // Add the element in at a specific index
        var option = $(new Option(text, val)).text(text);  // Added by Andy - the text attribute was not being set in IE 
        $("#blocks" + side + " option:eq(" + (current_index - 1) + ")").before(option);                    
        
        // Remove the selected element
        $('#blocks'+ side +' :selected').remove(); 
        
        // Reselect the option that was selected
        $("#blocks" + side).attr("selectedIndex", current_index - 1);
    });  
    
    // Handle the move down event - the user wants to move a block up so
    // it appears before other blocks.
    $('.btnMoveDown').click(function(e) 
    {
        e.preventDefault();
        
        side = ($(this).attr("href") == "right") ? "_right" : "_left";
        
        // In order to move a block up, the user must have selected a block
        // at index 1 or above.
        var current_index = $("#blocks" + side).attr("selectedIndex");
        var num_items = $("#blocks" + side +" option").size();

        if((current_index + 1) >= num_items)
        {
            alert("Please select a valid block to move down.");
            return false;
        }
        
        // Get the value / text of the selected element
        val = $('#blocks' + side +' :selected').val();
        text = $('#blocks' + side +' :selected').text();
        
        // Add the element in at a specific index
        var option = $(new Option(text, val)).text(text);   // Added by Andy - the text attribute was not being set in IE
        $("#blocks" + side + " option:eq(" + (current_index + 1) + ")").after(option);                    
        
        // Remove the selected element
        $('#blocks' +side + ' :selected').remove(); 
        
        // Reselect the option that was selected
        $("#blocks" + side).attr("selectedIndex", current_index + 1);
    });
    
});

function setAssignedBlocks()
{   
    var csv = "";
    
    $("#blocks_right option").each(function(i){
        if(csv != "") csv += ",";
        csv += $(this).val();                
    });
    
    $("input[name='assigned_blocks_right']").val(csv);
    /*document.frmPage.assigned_blocks_right.value = csv;*/    
    csv = "";
    
    $("#blocks_left option").each(function(i){
        if(csv != "") csv += ",";
        csv += $(this).val();                
    });
    
    $("input[name='assigned_blocks_left']").val(csv);
    /*document.frmPage.assigned_blocks_left.value = csv;*/    
}      