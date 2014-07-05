<style type="text/css">
#content h1 {margin-bottom:20px;}
#content strong {font-weight:bold;}
#content h4 {font-weight:bold;}
#content ul {margin:0 0 1em;padding:0;}
#content li {background:none;}
#content label {padding:0;cursor:pointer;font-weight:normal}
#content label input {vertical-align:middle;}
#content .divider {margin-bottom:10px;}
#exportbtn {color:#FFF;background:#8B0304;font-size:16px;font-weight:bold;padding:7px;cursor:pointer;border:1px solid #222;border-radius:10px;-webkit-border-radius:10px;-moz-border-radius:10px;}
</style>

<script type="text/javascript">
var updateFormState = function() {
    var val = $(':radio[name="type"]:checked').val();
    if (val == 'project') {
        $('.stockonly').hide();
    } else {
        $('.stockonly').show();
    }
}
$(function(){
    updateFormState();
    $(':radio[name="type"]').click(updateFormState);
});
</script>
</head>