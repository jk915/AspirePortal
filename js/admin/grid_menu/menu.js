$(document).ready(function(){ 
    
    $(".menu_icon div").click(function(){        
          
         window.location = $(this).find("a").attr("href");   
         return false;
    });
});