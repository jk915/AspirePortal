<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd" >
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
   <title><?php echo htmlspecialchars($meta_title); ?></title>
   <meta name="description" content= "<?php echo $meta_description; ?>" />
   <meta name="keywords" content="<?php echo $meta_keywords; ?>" />

   <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/main.css" />
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/forms.css" />               
   <link rel="stylesheet" type="text/css" media="screen" href="<?php echo base_url(); ?>css/admin/sIFR-screen.css" />
   <link rel="stylesheet" type="text/css" media="print" href="<?php echo base_url(); ?>css/admin/sIFR-print.css" /> 
     
    
   <link rel="Shortcut Icon" type="image/x-icon" href="<?=base_url()?>images/favicon.ico" />
   <script type="text/javascript"> var base_url = '<?=base_url()?>';</script>   
   
   <!--<script type="text/javascript" src="http://portalqa.aspirenetwork.net.au/js/member/jquery-1.7.1.min.js" ></script>-->
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery-1.4.2.min.js" ></script>
   
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/sifr.js" ></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/sifr-config.js" ></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/tooltips.js" ></script>  
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/swfobject.js" ></script>
   <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/main.js" ></script>

	 <script type="text/javascript" src="<?php echo base_url(); ?>js/admin/jquery.reveal.js"></script>
	 	 
	<script type="text/javascript">
        $(document).ready(function(){
            $("#jump_to").change(function(){
                
                if($(this).val() != "")
                {
                    var url = base_url + "admin/" + $(this).val();
                    window.location.replace(url);
                }
                
            });
            
            $("#website_id").change(function(){
               
               $('#website').submit();
                
            });
        
		//By Ajay Taskseveryday
		$('#submitorder').attr('disabled', 'disabled');
		$('#duration').attr('disabled', 'disabled');
		$('#turn_on_off').attr('disabled', 'disabled');
		function updateFormEnabled() {
		
			if(check_type()) {
				
				$('#turn_on_off').attr('disabled', '');
				$('#submitorder').attr('disabled', '');
			}
			else {
				$('#turn_on_off').attr('disabled', 'disabled');
				$("#due_date").css('display', 'none');
			}
		
		
			if (verifySettings()) {
				
				$('#duration').attr('disabled', '');
				
			} else {
				
				$('#duration').attr('disabled', 'disabled');
				
			}
			
			if(verifyduration()) {
				$('#submitorder').attr('disabled', '');
				$("#due_date").css('display', 'block');

				if ($('#mail_type').val() == '1') {
				var now = new Date();
				var wkday = now.getDay(); // 0=Sunday, 1=Monday, etc.
				// if today is SuMoTu, then we move forward 2,1,0 days
				// if today is WeThFrSa, then we move forward 6,5,4,3 days
				var moveby = ( wkday <= 2 ) ? ( 2 - wkday ) : ( 9 - wkday );
				var nextTuesday = new Date( now.getFullYear(), now.getMonth(), now.getDate() + moveby );
				var curr_date = nextTuesday.getDate();
				var curr_month = nextTuesday.getMonth() + 1;
				var curr_year = nextTuesday.getFullYear();
				var due_date = curr_date + "-" + curr_month  + "-" + curr_year;
				var mail_date = document.getElementById("mail_date");
				mail_date.value = due_date;
				}
				else
				{
					var now = new Date();
				var wkday = now.getDay(); // 0=Sunday, 1=Monday, etc.
				// if today is SuMoTu, then we move forward 2,1,0 days
				// if today is WeThFrSa, then we move forward 6,5,4,3 days
				var moveby = ( wkday <= 3 ) ? ( 3 - wkday ) : ( 9 - wkday );
				var nextWednesday = new Date( now.getFullYear(), now.getMonth(), now.getDate() + moveby );
				var curr_date = nextWednesday.getDate();
				var curr_month = nextWednesday.getMonth() + 1;
				var curr_year = nextWednesday.getFullYear();
				var due_date = curr_date + "-" + curr_month  + "-" + curr_year;
				var mail_date = document.getElementById("mail_date");
				mail_date.value = due_date;
				
				}
			} else {
				$("#due_date").css('display', 'none');
				
			}
			

			
			
			if ($('#turn_on_off').val() == '0') {
				$('#duration').attr('disabled', 'disabled');
				$('#submitorder').attr('disabled', 'disabled');
				$("#due_date").css('display', 'none');
			}
		}


		
		function verifySettings() {
			if ($('#turn_on_off').val() == '1') {
				return true;
			} else {
				return false
			}
		}
		
		function verifyduration() {
			if($('#duration').val() != '') {
				return true;
			}
			else {
				return false
			}
		
		}
		
		function check_type() {
			if($('#mail_type').val() != '') {
		
				return true;
			}
			else {
		
				return false
			}
		
		}

		$('#mail_type').change(updateFormEnabled);
		$('#duration').change(updateFormEnabled);

		$('#turn_on_off').change(updateFormEnabled);
					

				$('.first').keyup(function() {
	
		$numbr = $(this).attr('id');
		$numbr_array = $numbr.split("-");
		$num = $numbr_array[1];
		$fnum = $("#f-"+$num).val();	
		$snum = $("#s-"+$num).val();
			
		$addition = (parseInt($snum) - parseInt($fnum))/parseInt($snum)*100;
		$('#r-'+$num).val($addition);
			
	});
		
	$('.second').keyup(function() {
	
		$numbr = $(this).attr('id');
		$numbr_array = $numbr.split("-");
		$num = $numbr_array[1];
		$fnum = $("#f-"+$num).val();	
		$snum = $("#s-"+$num).val();
			
		$addition = (parseInt($snum) - parseInt($fnum))/parseInt($snum)*100;
		$('#r-'+$num).val($addition);
			
	});			
					
});

        
        
    </script>	