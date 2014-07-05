<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />  
    <meta http-equiv="content-language" content="en" />
    <title><?php echo WEBSITE_NAME . " - " . $meta_title;?></title>
    
    <link rel="Shortcut Icon" href="<?php echo base_url(); ?>images/member/favicon.ico" /> 
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>css/member/main.css" />
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>css/member/reveal.css" />
    <?php if(file_exists(ABSOLUTE_PATH . "css/member/" . CONTROLLER_NAME . ".css")) : ?>
    <link rel="stylesheet" media="screen" href="<?php echo base_url(); ?>css/member/<?php echo CONTROLLER_NAME; ?>.css" />
    <?php endif; ?>
                 
    <!--[if lt IE 9]>
        <link rel="stylesheet" type="text/css" href="<?php echo base_url(); ?>css/member/ie.css" media="screen" />     
    <![endif]--> 
    
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/jquery-1.7.1.min.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/main.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/jquery.reveal.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/jquery.validate.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/jquery.blockUI.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/fileuploader.js"></script>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/jquery.reveal.js"></script>
    <script type="text/javascript">
    var base_url = "<?php echo base_url(); ?>";
    </script>
    
    <?php if(file_exists(ABSOLUTE_PATH . "js/member/" . CONTROLLER_NAME . ".js")) : ?>
    <script type="text/javascript" src="<?php echo base_url(); ?>js/member/<?php echo CONTROLLER_NAME; ?>.js"></script>
    <?php endif; ?>       
       
    <?php if (defined("OWNERDETAILS_analytics_id") AND (OWNERDETAILS_analytics_id != "")) : ?>
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '<?php echo OWNERDETAILS_analytics_id; ?>']);
    _gaq.push(['_trackPageview']);
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
    <?php endif; ?>        
 