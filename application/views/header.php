<!DOCTYPE html>
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <title><?php echo WEBSITE_NAME . " - " . $meta_title;?></title>
    <link type="text/css" href="<?php echo base_url();?>css/style.css" rel="stylesheet" />
    
    <?php if ( isset($settings['analytics_id']) AND !empty($settings['analytics_id']) ) : ?>
    <script type="text/javascript">
    var _gaq = _gaq || [];
    _gaq.push(['_setAccount', '<?php echo $settings['analytics_id']?>']);
    _gaq.push(['_trackPageview']);
    (function() {
        var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
        ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
        var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
    })();
    </script>
    <?php endif; ?>    