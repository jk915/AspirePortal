<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.0 Transitional//EN">
<html>
<head>
    <style type="text/css">
    body
    {
        color: #262626;
        font: 14px/1.4em 'PTSansRegular',sans-serif; 
        background-color: #F2F2F2;   
    }
    
    #wrapper
    {
        width: 612px;    
        margin: 0 auto;
        background-color: #F2F2F2;
    }
    
    .headertop
    {
        background: url('http://portal.aspirenetwork.net.au/images/member/header-bk-tile.png') repeat-x scroll left top #A50D12;    
        padding: 5px;
    }
    
    .headerbottom
    {
        background-color: #A50D12;    
        padding: 5px;
        color: #FFFFFF;
        font-weight: bold;
        font-size: 14pt;
    }
    
    #itemlist
    {
        border-top: 20px solid #F2F2F2;    
    }    
    
    .itemcell
    {
        border: 1px solid #262626;
        width: 195px;
    }
      
    .cellspacer
    {
        padding: 2px;
    } 
    
    table.iteminfo h2
    {
        font-size: 13pt; 
        padding: 0;                   
        margin: 0;
        line-height: 1em;
        font-weight: bold;
        
    }
    
        table.iteminfo h2 a
        {
            text-decoration: none; 
            color: #262626;   
        }
    
    table.iteminfo h3
    {
        font-size: 10pt;
        padding: 0;                   
        margin: 3px 0 0 0;
        line-height: 1em;
        font-weight: bold;                            
    }
    
        table.iteminfo h3 a
        {
            text-decoration: none; 
            color: #A50D12;   
        }    
    
    table.specs
    {
        border-top: 15px solid #F2F2F2;     
    }
    
    .padding-right
    {
        padding-right: 15px;    
    }
    
    h4
    {
        display: block;
        width: 175px;
        text-align: center;
        font-size: 13pt;
        font-weight: bold;    
    }
    
    p.additionalInfo
    {
        font-size: 10pt;
        font-weight: normal;
    }
    
    #footer
    {
        border-top: 15px solid #F2F2F2;        
    }
    
    #footer table td
    {
        color: #FFFFFF;
        font-size: 10pt;
    }
    
        #footer table td a
        {
            text-decoration: none;
            color: #FFFFFF;    
        }

    </style>
</head>
<body>
    <div id="wrapper">
    
        <!-- Header -->
        <table width="612" cellpadding="0" cellspacing="0" align="center">
            <tr>
                <td class="headertop">
                    <img id="logo" width="473" height="115" border="0" alt="Back to the home page." src="http://portal.aspirenetwork.net.au/images/member/logo.png">
                </td>
            </tr>
            <tr>
                <td class="headerbottom">
                    <?php echo $heading?>
                </td>
            </tr>
        </table>
        
        <!-- Stocklist -->
        <table id="itemlist" width="600" cellpadding="0" cellspacing="0" align="center">
    <?php for ($i=0;$i<$total_rows;$i++) : ?>
            <tr>
        <?php
            for ($j=0;$j<3;$j++) :
                $itemIndex = $i*3 + $j;
                $project = isset($projects[$itemIndex]) ? $projects[$itemIndex] : false;
        ?>
            <?php if ($j!=0) : ?>
                <td class="cellspacer">&nbsp;</td>
            <?php endif; ?>
                <td class="itemcell" valign="top">
                <?php if ($project) :
                        $projectURL = site_url("projects/detail/$project->project_id");
                ?>
                    <?php if($project->logo != ""  && file_exists(ABSOLUTE_PATH . $project->logo)) : ?>
                    <?php
                        $src = $project->logo;
                        $resized = image_resize($src, 196, 130);
                    ?>
                    <a href="<?php echo $projectURL?>"><img src="<?php echo $resized;?>" width="195" height="130" alt=" " /></a>
                    <?php else: ?>
                    <a href="<?php echo $projectURL?>"><img src="<?php echo base_url(); ?>images/member/home_default.jpg" width="195" height="130" alt=" " /></a>
                    <?php endif; ?>
                    <table class="iteminfo" width="195" cellpadding="10" cellspacing="0" align="center">
                        <tr>
                            <td>
                                <h2><a href="<?php echo $projectURL?>"><?php echo $project->project_name;?></a></h2>
                                <h3><a href="<?php echo $projectURL?>"><?php echo $project->area_name . ", " . $project->state; ?></a></h3> 
                                <h4>From $<?php echo number_format($project->prices_from, 0, ".", ","); ?></h4>  
                            </td>
                        </tr>
                    </table>
                <?php endif; ?>
                </td>
        <?php endfor; ?>
            </tr>
        <?php if ($i+1<=$total_rows) : ?>
            <tr>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
                <td>&nbsp;</td>
            </tr>
        <?php endif; ?>
    <?php endfor; ?>

        </table>
        
        
        <!-- Footer -->
        <table width="612" cellpadding="0" cellspacing="0" align="center" id="footer">
            <tr>
                <td class="headertop">
                    <table width="602" cellpadding="10" cellspacing="0" align="center">
                        <tr>
                            <td>
                                <img id="logo" width="250" border="0" alt="Back to the home page." src="http://portal.aspirenetwork.net.au/images/member/logo.png">
                            </td>
                            <td>
                                <b>Phone</b> 1300 710 933<br/>
                                <b>Fax</b> 03 8456 6603<br/>
                                <b>Email</b> <a href="mailto:info@aspirenetwork.net.au">info@aspirenetwork.net.au</a><br/>                                
                                &copy; ASPIRE Advisor Network 2012-2013.
                            </td>
                        </tr>
                    </table>
                </td>
            </tr>
        </table>        
        
    </div><!-- end wrapper -->
</body>
</html>