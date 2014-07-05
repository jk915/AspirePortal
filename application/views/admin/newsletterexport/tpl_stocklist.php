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
                $property = isset($properties[$itemIndex]) ? $properties[$itemIndex] : false;
        ?>
            <?php if ($j!=0) : ?>
                <td class="cellspacer">&nbsp;</td>
            <?php endif; ?>
                <td class="itemcell" valign="top" width="200">
                <?php if ($property) :
				    $propertyURL = site_url("stocklist/detail/$property->property_id");
                ?>
                    <?php if($property->hero_image != ""  && file_exists("property/" . $property->property_id . "/images/" . $property->hero_image)) : ?>
                    <?php
                        $src = "property/" . $property->property_id . "/images/" . $property->hero_image;
                        $resized = image_resize($src, 196, 130);
                    ?>
                    <a href="<?php echo $propertyURL?>"><img src="<?=$resized;?>" width="195" height="130" alt="" border="0" /></a>
                    <?php else: ?>
                    <a href="<?php echo $propertyURL?>"><img src="<?php echo base_url(); ?>images/member/home_default.jpg" width="195" height="130" alt="" border="0" /></a>
                    <?php endif; ?>
                    <table class="iteminfo" width="195" cellpadding="10" cellspacing="0" align="center">
                        <tr>
                            <td>
                                <h2><a href="<?php echo $propertyURL?>"><?php echo $property->lot . ", " . $property->address;?></a></h2>
                                <h3><a href="<?php echo $propertyURL?>"><?php echo $property->area_name . ", " . $property->state; ?></a></h3> 
                                
                                <table class="specs" width="175" cellpadding="0" cellspacing="0" align="center">
                                    <tr>
                                        <td valign="middle"><img src="<?php echo base_url()?>images/member/icon-bedrooms-drk.png" border="0" width="24" height="14"></td>
                                        <td valign="middle" class="padding-right"><?php echo $property->bedrooms; ?></td>
                                        <td valign="middle"><img src="<?php echo base_url()?>images/member/icon-bathrooms-drk.png" border="0" width="21" height="19"></td>
                                        <td valign="middle" class="padding-right"><?php echo $property->bathrooms; ?></td>
                                        <td valign="middle"><img src="<?php echo base_url()?>images/member/icon-garage-drk.png" border="0" width="25" height="21"></td>
                                        <td valign="middle"><?php echo $property->garage; ?></td>
                                    </tr>
                                </table>
                                
                                <h4>$<?php echo number_format($property->total_price, 0, ".", ","); ?></h4>
                                     
                                <p class="additionalInfo">
                                    <b>NRAS:</b> <?php echo ($property->nras) ? "Yes" : "No"; ?><br>
                                    <b>SMSF:</b> <?php echo ($property->smsf) ? "Yes" : "No"; ?><br>
                                    <?php
								if($property->titled == "1")
								{
								?>
								<b>Titled:</b> <?=($property->titled) ? "Yes" : "No"; ?><br />
								<?php
								}
								else
								{
								?>
								<b>Titled:</b> <?=($property->titled) ? "Yes" : "No"; ?><br />
								<b>Estimated Date:</b><?php echo $property->estimated_date; ?><br/>
								<?php
								}
								?>
                                    <b>House area:</b> <?php echo $property->house_area; ?>sqm<br>
                                    <b>Rent Yield:</b> <?php echo number_format($property->rent_yield, 2); ?>%
                                </p>

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