<?php
require_once('classes/fpdf.php');

class MY_FPDF extends FPDF 
{
    var $data = array();
    var $type = '';
    var $showFirstBG = false;
    var $showFirstBGArea = false;

    function setData($data, $type = '')
    {
        $this->data = $data;
        $this->type = $type;
    }
    
    function setType($type)
    {
        $this->type = $type;
    }
    
    function setHeader($header)
    {
        $this->data['HEADER_TEXT'] = $header;
    }
    
    function Header()
    {             
        if ($this->type == 'area') {
            if ($this->PageNo() == 1 OR $this->showFirstBGArea) {
                $background_img = FCPATH . "images/member/brochure-area-background.jpg";
                $this->showFirstBGArea = false;
            } else {
                $background_img = FCPATH . "images/member/brochure-area-background-white.jpg";
            }
            list($width_img, $height_img) = getimagesize($background_img);  
            $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            
            $this->SetFont('PTSansBold','',21);
            $this->SetTextColor(255, 255, 255);
            $this->Text(5, 13, $this->data["HEADER_TEXT"]);
            if (isset($this->data["MEDIAN_HOUSE_PRICE"])) {
                $this->SetFont('PTSans','',11);
                $this->Text(172, 7, 'median house price');
                $this->SetFont('PTSansBold','',21);
                $this->Text(172, 16, $this->data["MEDIAN_HOUSE_PRICE"]);
            }
            
        } elseif ($this->type == 'project') {
            if ($this->PageNo() == 1 OR $this->showFirstBG) {
                $background_img = FCPATH . "images/member/brochure-project-background.jpg";
                $this->showFirstBG = false;
            } else {
                $background_img = FCPATH . "images/member/brochure-project-background-white.jpg";
            }
            list($width_img, $height_img) = getimagesize($background_img);  
            if (file_exists($background_img)) {
                $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            }
            
            $this->SetFont('PTSansBold','',21);
            $this->SetTextColor(255, 255, 255);
            $this->Text(5, 13, $this->data["HEADER_TEXT"]);
            $this->SetFont('PTSansBold','',15);
            $this->Text(5, 27, $this->data["SUB_HEADER_TEXT"]);
            $this->SetFont('PTSans','',11);
            $this->Text(185, 7, 'prices from');
            $this->SetFont('PTSansBold','',21);
            $this->Text(171, 16, $this->data["PRICE_FROM"]);
        } 
		elseif ($this->type == 'state') {
            if ($this->PageNo() == 1 OR $this->showFirstBG) {
                $background_img = FCPATH . "images/member/brochure-project-background.jpg";
                $this->showFirstBG = false;
            } else {
                $background_img = FCPATH . "images/member/brochure-project-background-white.jpg";
            }
            list($width_img, $height_img) = getimagesize($background_img);  
            if (file_exists($background_img)) {
                $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            }
            
            $this->SetFont('PTSansBold','',21);
            $this->SetTextColor(255, 255, 255);
            $this->Text(5, 13, $this->data["HEADER_TEXT"]);
            $this->SetFont('PTSansBold','',15);
            $this->Text(5, 27, $this->data["SUB_HEADER_TEXT"]);
            $this->SetFont('PTSans','',11);
            $this->Text(185, 7, 'prices from');
            $this->SetFont('PTSansBold','',21);
            $this->Text(171, 16, $this->data["PRICE_FROM"]);
        }
		
		elseif ($this->type == 'region') {
            if ($this->PageNo() == 1 OR $this->showFirstBG) {
                $background_img = FCPATH . "images/member/brochure-project-background.jpg";
                $this->showFirstBG = false;
            } else {
                $background_img = FCPATH . "images/member/brochure-project-background-white.jpg";
            }
            list($width_img, $height_img) = getimagesize($background_img);  
            if (file_exists($background_img)) {
                $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            }
            
            $this->SetFont('PTSansBold','',21);
            $this->SetTextColor(255, 255, 255);
            $this->Text(5, 13, $this->data["HEADER_TEXT"]);
            $this->SetFont('PTSansBold','',15);
            $this->Text(5, 27, $this->data["SUB_HEADER_TEXT"]);
            $this->SetFont('PTSans','',11);
            
            if(isset($this->data["PRICE_FROM"])) {
                $this->Text(185, 7, 'prices from');
                $this->SetFont('PTSansBold','',21);
                $this->Text(171, 16, $this->data["PRICE_FROM"]);
            }
        }
		
		elseif ($this->type == 'australia') {
            if ($this->PageNo() == 1 OR $this->showFirstBG) {
                $background_img = FCPATH . "images/member/brochure-project-background.jpg";
                $this->showFirstBG = false;
            } else {
                $background_img = FCPATH . "images/member/brochure-project-background-white.jpg";
            }
            list($width_img, $height_img) = getimagesize($background_img);  
            if (file_exists($background_img)) {
                $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            }
            
            $this->SetFont('PTSansBold','',21);
            $this->SetTextColor(255, 255, 255);
            $this->Text(5, 13, $this->data["HEADER_TEXT"]);
            $this->SetFont('PTSansBold','',15);
            //$this->Text(5, 27, $this->data["SUB_HEADER_TEXT"]);
            $this->SetFont('PTSans','',11);
            //$this->Text(185, 7, 'prices from');
            $this->SetFont('PTSansBold','',21);
            //$this->Text(171, 16, $this->data["PRICE_FROM"]);
        }
        
        else if($this->type == 'title'){
        }
		
		else if($this->type == 'property'){
            $background_img = FCPATH . "images/member/brochure-property-background.jpg";
            list($width_img, $height_img) = getimagesize($background_img);  
            if (file_exists($background_img)) {
                $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            }
            $this->SetFont('PTSansBold','',21);
            $this->SetTextColor(255, 255, 255);
            $this->Text(5, 13, $this->data["HEADER_TEXT"]);
            $this->Text(171, 13, $this->data["PROPERTY_TOTAL_PRICE"]);
            
            $this->SetFont('PTSansBold','',14);
            $this->Text(5, 27, $this->data["SECONDARY_HEADER_TEXT"]);
        }
        
        else if($this->type == 'country'){
            if ($this->PageNo() == 1 OR $this->showFirstBG) {
                $background_img = FCPATH . "images/member/brochure-area-background.jpg";
                $this->showFirstBG = false;
            } else {
                $background_img = FCPATH . "images/member/brochure-area-background-white.jpg";
            }
            list($width_img, $height_img) = getimagesize($background_img);  
            if (file_exists($background_img)) {
                $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            }
            
            
        }
        
        else {
            if ($this->PageNo() == 1 OR $this->showFirstBGArea) {
                $background_img = FCPATH . "images/member/brochure-area-background.jpg";
                $this->showFirstBGArea = false;
            } else {
                $background_img = FCPATH . "images/member/brochure-area-background-white.jpg";
            }
            list($width_img, $height_img) = getimagesize($background_img);  
            $this->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            
            $this->SetFont('PTSansBold','',21);
            $this->SetTextColor(255, 255, 255);
            $this->Text(5, 13, $this->data["HEADER_TEXT"]);
        }
        
       
    }
    
    function Footer()
    {            
        if(defined("DISCLAIMER")) {
            $this->writeDisclaimer(DISCLAIMER);
        } 
    }
    
    public function writeDisclaimer($disclaimer_text) {
        $this->SetFont('PTSansBold','',7);
        $this->SetTextColor(255, 255, 255);
        $this->SetXY(80, 280);
        $this->MultiCell(125, 2, $disclaimer_text);        
    }
    
    function checkImage($sourceImagePath)
    {
        // check if png
        $pathinfo = pathinfo($sourceImagePath);
        
        // get image size
        $data = @getimagesize($sourceImagePath);
        if(!$data) {
            //die("Invalid source image: " . $sourceImagePath);
            return false;    
        }
        
        $width = $data[0];
        $height = $data[1];
        $type = $data[2];
        $attr = $data[3];

        if (isset($pathinfo['extension']) AND strtolower($pathinfo['extension'])=='png') {
        	// png -> convert to jpg
        	$image_p = imagecreatetruecolor($width, $height);
        	$bgWhite = imagecolorallocate($image_p, 255, 255, 255);
        	imagefilledrectangle($image_p, 0, 0, $width, $height, $bgWhite);
        	$image = imagecreatefrompng($sourceImagePath);
        	imagecopyresampled($image_p, $image, 0, 0, 0, 0, $width, $height, $width, $height);
        	$tmpImagePath = $sourceImagePath . '.jpg';
        	imagejpeg($image_p, $tmpImagePath);
        	return $tmpImagePath;
        } else {
            return $sourceImagePath;
        }
    }
    
    function decodeText($str)
    {
        $str = str_replace('&rdquo;','"',$str);
        $str = str_replace('&ldquo;','"',$str);
        $str = str_replace('&amp;','&',$str);
        $str = str_replace('&raquo;','>>',$str);
        $str = str_replace('&laquo;','<<',$str);
        $str = str_replace('&copy;','©',$str);
        $str = str_replace('&rsquo;',"'",$str);
        $str = str_replace('&lsquo;',"'",$str);
        $str = str_replace('&bull;',"+",$str);
        $str = str_replace('&nbsp;'," ",$str);
        $str = str_replace('* ', "+ ",$str);
        $str = str_replace(chr(194), "", $str);
        return $str;
    }
}
    
    function _process_country($data, &$pdf = false)
    {
        $pdf->setData($data, 'country');
        $pdf->AddPage();
        
        
        $pdf->SetFillColor(130, 27, 28);
        $pdf->Rect(0,20, 210, 10,'FD');
        
        $pdf->SetFont('PTSansBold','',21);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Text(5, 13, $pdf->data["HEADER_TEXT"]);
        $pdf->SetFont('PTSansBold','',15);
        $pdf->Text(5, 27, $pdf->data["SUB_HEADER_TEXT"]);
        
        $pdf->SetFillColor(130, 27, 28);
        $pdf->Rect(0,140, 210, 10,'FD');
        
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFont('PTSansBold','',15);
        $pdf->Text(5, 146, $pdf->data["SUB_HEADER_TEXT2"]);
        
        
        $pdf->SetFont('PTSans','',10);
        $pdf->SetTextColor(0, 0, 0);
        $y = 35;
        $x = 5;
        $pdf->SetXY($x, $y);
        $pdf->MultiCell(200, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($data['AUSTRALIA']->overview) ), ENT_QUOTES))), 0, "J");
        
        $y += 120;
        $pdf->SetXY($x, $y);

        $overview = $data['STATE']->overview;
        $overview = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($overview) ), ENT_QUOTES)));
        //$overview = preg_replace("/[\n\r] /","\n", $overview, ENT_QUOTES);
        
        $pdf->MultiCell(200, 4, $pdf->decodeText($overview), 0, "J");
    }
    
    function _process_title($data, &$pdf = false)
    {
        $pdf->AddPage();
        if ((!empty($pdf->data['AGENT_LOGO'])) && (file_exists($pdf->data['AGENT_LOGO']))) {
            $img_path = $pdf->checkImage($pdf->data['AGENT_LOGO']);
            
            list($width, $height, $type, $attr) = getimagesize($img_path);
            
            if($width > $height) {
                // The image is wider than it is high
                $ratio = $height / $width;
                $height = 150 * $ratio;
                $top = ($height > 75) ? 20 : 40;
                
                $pdf->Image($img_path, 30, $top, 150, $height);
            } else if($width == $height) {
                $pdf->Image($img_path, 30, 20, 150, 150);
            } else {
                // The image is higher than it is wide.
                $ratio = $width / $height;   
                $width = 100 * $ratio;   
                
                // Figure out the left offset to center the image.
                $left = (210 - $width) / 2;
                $pdf->Image($img_path, $left, 15, $width, 100);
            }             
                
            // logo size
            /*
            list($width, $height, $type, $attr) = getimagesize($agentLogoPath);
            $logoAreaWidth = 85;
            $logoAreaHeight = 85;
            if ($width/$height > $logoAreaWidth/$logoAreaHeight) {
                $logoWidth = $logoAreaWidth;
                $logoHeight = ($height/$width) * $logoWidth;
            } else {
                $logoHeight = $logoAreaHeight;
                $logoWidth = ($width/$height) * $logoHeight;
            }
            
            $pdf->Image($agentLogoPath, (210 - $logoWidth)/2, 35, $logoWidth, $logoHeight);
            */
        }
        
        
        
        
        $y = 125;
        $pdf->SetFillColor(166, 42, 44);
        $pdf->Rect(0,$y, 210, 50,'FD');
        
        $pdf->SetDrawColor(0, 0, 0);
        $pdf->Rect(5,5, 210 - 10, 297 - 10);
        
        $pdf->SetFont('PTSansBold','',40);
        $pdf->SetTextColor(255, 255, 255);
        $str_w = $pdf->GetStringWidth('Property Summary');
        $y += 20;
        $pdf->Text(105 - $str_w/2, $y, 'Property Summary');
        $pdf->SetFont('PTSansBold','',20);
        $str_w = $pdf->GetStringWidth($data['FULL_ADDRESS']);
        $y += 20;
        $pdf->Text(105 - $str_w/2, $y, $data['FULL_ADDRESS']);
        
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('PTSansBold','',15);
        $y += 25;
        $x = 20;
        $pdf->Text($x, $y, "Prepared For: " . $data['PREPARED_FOR']);
        
        $pdf->SetFont('PTSans','',14);
        $y += 10;
        $pdf->Text($x, $y, "By");
        if($data['CONTACT_NAME'] != '')
        {
            $y += 10;
            $pdf->Text($x, $y, $data['CONTACT_NAME']);
        }
        if($data['CONTACT_COMPANY_NAME'] != '')
        {
            $y += 10;
            $pdf->Text($x, $y, $data['CONTACT_COMPANY_NAME']);
        }
        if($data['CONTACT_MOBILE'] != '')
        {
            $y += 10;
            $pdf->Text($x, $y, $data['CONTACT_MOBILE']);
        }
        if($data['CONTACT_EMAIL'] != '')
        {
            $y += 10;
            $pdf->Text($x, $y, $data['CONTACT_EMAIL']);
        }
        $pdf->SetFont('PTSans','',10);
        $y += 25;
        $x = 20;
        
        $currentdate  = mktime(0, 0, 0, date("m")  , date("d"), date("Y")); 
        
        $str_vaid_date = "Valid till: ".date('d/m/Y', $currentdate + 7*60*60*24);
        $str_w = $pdf->GetStringWidth($str_vaid_date);
        $pdf->Text((210 - $str_w)/2, $y, $str_vaid_date);
        
        
        
        if ((!empty($pdf->data['PROPERTY_LOGO'])) && (file_exists($pdf->data['PROPERTY_LOGO']))) {
            $agentLogoPath = $pdf->checkImage($pdf->data['PROPERTY_LOGO']);
            // echo $agentLogoPath;die;
            // logo size
            list($width, $height, $type, $attr) = getimagesize($agentLogoPath);
            $logoAreaWidth = 90;
            $logoAreaHeight = 60;
            if ($width/$height > $logoAreaWidth/$logoAreaHeight) {
                $logoWidth = $logoAreaWidth;
                $logoHeight = ($height/$width) * $logoWidth;
            } else {
                $logoHeight = $logoAreaHeight;
                $logoWidth = ($width/$height) * $logoHeight;
            }
            
            $pdf->Image($agentLogoPath, 110, 183, $logoWidth, $logoHeight);
        }
        
        $pdf->_endpage();
    }

    function make_property_brochure($data, &$pdf = false)
    {
        // Create new PDF, A4 is default size, in Portraight, using mm as the unit.
        // A4 is 210 mm wide X 297 mm high.
        
        // If the PDF object hasn't already been created, create it.
        if(!$pdf)
        {
            $pdf = new MY_FPDF('P', 'mm');
        }
        
        
        $pdf->SetMargins(0, 35, 0);
        $pdf->SetAutoPageBreak(true, 20);
        $pdf->AddFont('PTSans');
        $pdf->AddFont('PTSansBold');
           
        if($data['BROCHURE']) {
            
            foreach($data['BROCHURE']->result() as $page)
            {
                if($page->type == 'title')
                {
                    $pdf->setData($data['TITLE_DATA'], 'title');
                    _process_title($data['TITLE_DATA'], $pdf);
                    
                }
                else if($page->type == 'property')
                {
                    $pdf->setData($data, 'property');
                    _process_property($data['PROPERTY_DATA'], $pdf);  
                }
                else if($page->type == 'project')
                {
                    _process_property_project($data['PROJECT_DATA'], $pdf);
                }
                else if($page->type == 'area')
                {
                    $pdf->showFirstBG = true;
                    _process_property_area($data['AREA_DATA'], $pdf);
                }
                else if($page->type == 'country')
                {
                    _process_country($data['COUNTRY_DATA'], $pdf);
                }
                else if($page->type == 'region')
                {
                    _process_property_region($data['REGION_DATA'], $pdf);
                }
                else if($page->type == 'summary')
                {
                    _process_summary($data['SUMMARY_DATA'], $pdf);
                }
                else if($page->type == 'manual')
                {
                    $pdf->setHeader($page->heading);
                    _process_property_manual($page, $pdf);
                }
                else if($page->type == 'floorplan')
                {
                    $pdf->setHeader($page->heading);
                    if(isset($data['FLOORPLAN_DATA']['IMG_URL'])) {
                        _process_property_floorplan($data['FLOORPLAN_DATA'], $pdf);
                    }
                }
            }  
        }
        
		$pdf->Output($data["SAVE_PATH"], $data["PDF_DEST"]);
        return true;
    }
    
    function _process_property($data, &$pdf)
    {
        // $pdf->setData($data, 'property');
        $pdf->AddPage();
        
        // Output property image
        if(($data["HERO_IMAGE"] != "") && (file_exists($data["HERO_IMAGE"]))) {
            $pdf->Image($pdf->checkImage($data["HERO_IMAGE"]), 0, 30.5, 140.5, 91);
        }
        
        /*
        if( isset($data["PLAN_IMAGE"]) AND !empty($data["PLAN_IMAGE"]) ) {
            $y = 125;
            $image = $data["PLAN_IMAGE"];
            $path = ABSOLUTE_PATH . $image->document_path;
            if( file_exists($path) ) {
                $pdf->Image($pdf->checkImage($path), 5, $y, 90, 75);
            }
        }
        */
        
        if (isset($data['PROPERTY_PHOTO_1'])) {
            if (file_exists($data['PROPERTY_PHOTO_1'])) {
            	$pdf->Image($pdf->checkImage($data['PROPERTY_PHOTO_1']), 3, 125, 65, 53);
            }
        }
        
        if (isset($data['PROPERTY_PHOTO_2'])) {
            if (file_exists($data['PROPERTY_PHOTO_2'])) {
            	$pdf->Image($pdf->checkImage($data['PROPERTY_PHOTO_2']), 72, 125, 65, 53);
            }
        }
        
        $y = 30.5;
        $pdf->SetY($y);
        
        // Out bedbathgarage icons
        $bbgIcon = FCPATH . "images/member/brochure-bedbathgarage.jpg";
        $pdf->Image($bbgIcon, 140.75, $y, 69);
        
        $y += 6;
        $pdf->SetY($y);
        $pdf->SetFont('PTSansBold','',12);
        $pdf->SetTextColor(108, 108, 108);
        $pdf->Text(158, $y, $data["PROPERTY_NOBEDROOMS"]);
        $pdf->Text(180, $y, $data["PROPERTY_NOBATHROOMS"]);
        $pdf->Text(203, $y, $data["PROPERTY_NOGARAGES"]);
        
        // NRAS, SMSF, RISK
        $y += 5;
        $pdf->SetY($y);
        if ($data['PROPERTY_NRAS']) {
            $tickIcon = FCPATH . "images/member/brochure-tick.jpg";
            $pdf->Image($tickIcon, 146, $y, 4);
            $pdf->SetFontSize(12);
            $pdf->Text(151, $y+3, 'NRAS');
        }
        if ($data['PROPERTY_SMSF']) {
            $tickIcon = FCPATH . "images/member/brochure-tick.jpg";
            $x = $data['PROPERTY_NRAS'] ? 170 : 146;
            $pdf->Image($tickIcon, $x, $y, 4);
            $pdf->SetFontSize(12);
            $pdf->Text($x+5, $y+3, 'SMSF');
        }
        
        if ($data['PROPERTY_NRAS']) {
        	if ($data['PROPERTY_SMSF']) {
        		$x = 194;
        	} else {
        	    $x = 171;
        	}
        } else {
        	if ($data['PROPERTY_SMSF']) {
        		$x = 171;
        	} else {
        	    $x = 146;
        	}
        }
        $rateIcon = FCPATH . "images/member/{$data['PROPERTY_RISK']}.jpg";
        $pdf->Image($rateIcon, $x, $y-1, 9);
        
        // Specifications
        $y += 10;
        $pdf->SetY($y);
        $specificationImage = FCPATH . "images/member/brochure-specifications.jpg";
        $pdf->Image($specificationImage, 140.75, $y, 69);
        $y += 18;
        $pdf->SetY($y);
        
        foreach ($data['PROPERTY_SPECIFICATIONS'] as $label=>$text)
        {
            $pdf->SetTextColor(2,2,2);
            $pdf->SetFont('PTSansBold','',11);
            $pdf->Text(145, $y, $label);
            if (in_array($label, array(/*'Contract Type',*/'Internal Comments','Misc Comments','NRAS Fee Summary','Special Features'))) {
                $pdf->SetFont('PTSans','',11);
                $pdf->SetXY(144, $y+1);
                $pdf->MultiCell(65, 4, $pdf->decodeText($text), 0, "L");
                $y = $pdf->GetY() + 5;
            } else {
                $pdf->SetFont('PTSans','',11);
                $pdf->Text(175, $y, $pdf->decodeText($text));
                $y += 6;
            }
        }
        // end specifications
        
        // Output Overview Heading
        $y = 190;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(5, $y, "Overview");
        
        $pdf->SetY($y+3);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(5, $pdf->GetY());
        $data["PROPERTY_SHORT_DESCRIPTION"] = substr($data["PROPERTY_SHORT_DESCRIPTION"],0, 700);
        $pdf->MultiCell(130, 5, $pdf->decodeText($data["PROPERTY_SHORT_DESCRIPTION"]), 0, "J");
        
        $data = $pdf->data["TITLE_DATA"]; 
        
        // Add Agent logo and agent details at the bottom
        if ((!empty($data['AGENT_LOGO'])) && (file_exists($data['AGENT_LOGO']))) {
            $agentLogoPath = $pdf->checkImage($data['AGENT_LOGO']);

            // logo size
            list($width, $height, $type, $attr) = getimagesize($agentLogoPath);
            $logoAreaWidth = 65;
            $logoAreaHeight = 15;
            if ($width/$height > $logoAreaWidth/$logoAreaHeight) {
                $logoWidth = $logoAreaWidth;
                $logoHeight = ($height/$width) * $logoWidth;
            } else {
                $logoHeight = $logoAreaHeight;
                $logoWidth = ($width/$height) * $logoHeight;
            }
            
            $pdf->Image($agentLogoPath, 5, 257, $logoWidth, $logoHeight);
        } else {
            //$logoPath = FCPATH . 'images/member/meticon-logo.jpg';
            //$pdf->Image($logoPath, 5, 257, 0, 15);
        }
        
        $pdf->SetFont('PTSans','',10);
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetXY(75, 257);
        $pdf->MultiCell(50, 4, $data['CONTACT_INFO'], 0, "L");  

        
        $y = 261;
        if($pdf->data['CONTACT_PHONE'] != "")
        {
            $pdf->SetFont('PTSansBold','',10);
            $pdf->Text(133, $y, 'Phone:');
            $pdf->SetFont('PTSans','',10);
            $pdf->Text(147, $y, $pdf->data['CONTACT_PHONE']);
        }
        
        if($data['CONTACT_MOBILE'] != "")
        {
            $y += 4;
            $pdf->SetFont('PTSansBold','',10);
            $pdf->Text(133, $y, 'Mobile:');
            $pdf->SetFont('PTSans','',10);
            $pdf->Text(147, $y, $data['CONTACT_MOBILE']);
        }
        
        if($data['CONTACT_EMAIL'] != "")
        {
            $y += 4;
            $pdf->SetFont('PTSansBold','',10);
            $pdf->Text(133, $y, 'Email:');
            $pdf->SetFont('PTSans','',10);
            $pdf->Text(147, $y, $data['CONTACT_EMAIL']);
        }        
        
    }
    
    function _process_property_area($data, &$pdf)
    {
        $pdf->SetMargins(0, 25, 0);
        $pdf->setData($data, 'area');
        
        $pdf->AddPage();

        $y = 32;
        
        if( $data["AREA_MAP_IMAGE"] ) {
            if( file_exists($data["AREA_MAP_IMAGE"]) ) {
                $pdf->Image($pdf->checkImage($data["AREA_MAP_IMAGE"]), 5, $y, 200, 120);
            }
        }
        
        // Output Area Overview Heading
        $y += 130;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(4, $y, "Area Overview");
        
        $pdf->SetY($y);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(4, $pdf->GetY()+3);
        $pdf->MultiCell(130, 5, $pdf->decodeText($data["AREA_OVERVIEW"]), 0, "J");
    
        $aMultiLines = array(
            'Average Annual Growth',
            'Weekly Median Advertised Rent',
            'Weekly Median Household Income',
            'Approx Distance to CBD',
            'No. Private Dwellings',
            'Approx time to CBD',
        );
        $prevLabel = '';
        
        unset($data['AREA_SPECIFICATIONS']['Closest CBD']);
        
        foreach ($data['AREA_MANUAL_KEYFACTS'] as $label=>$text)
            $data['AREA_SPECIFICATIONS'][$label] = $text;
        
        $count = count($data['AREA_SPECIFICATIONS']) + count($aMultiLines);
        $spec_height = $count * 5 + 5;
        
        $y = 162;
        
        //$pdf->SetFillColor(56, 56, 56);
        $pdf->SetFillColor(130, 27, 28);
        $pdf->Rect(141 ,$y - 5 , 64, 115,'F');
        
        $y += 5;
    
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(255, 255, 255);
        $pdf->Text(145, $y, "Key Facts");
        $y += 8;
        $labelY = $y;
        
        foreach ($data['AREA_SPECIFICATIONS'] as $label=>$text)
        {
            $pdf->SetTextColor(255,255,255);
            $pdf->SetFont('PTSansBold','',10);
            if (in_array($label, $aMultiLines)) {
                $pdf->SetXY(144, $labelY-4);
                $pdf->MultiCell(42, 4, $label, 0, "L");
                $labelY = $pdf->GetY() + 6;
            } else {
                $pdf->Text(145, $labelY, $label);
                $labelY += 6;
            }
            $pdf->SetFont('PTSans','',10);
            if (in_array($label, $aMultiLines)) {
                $pdf->Text(187, $labelY-7, $text);
            } else {
                if (in_array($prevLabel, $aMultiLines)) {
                    $pdf->Text(187, $labelY-6, $text);
                    $y = $labelY;
                } else {
                    $pdf->Text(187, $y, $text);
                    $y += 6;
                }
            }
            $prevLabel = $label;
        }
        // end specifications
        if(count($data['AREA_MOREINFO']) > 0) {
            
            $pdf->AddPage();
            $pdf->SetY(32);
        
            foreach($data['AREA_MOREINFO'] as $s)
            {
                $height = 0;
                //$dpi = 0;
                
                $y_start = $pdf->GetY();
                
                if($y_start > 250) {
                    $pdf->AddPage();
                    $pdf->SetY(32);   
                    $y_start = $pdf->GetY();
                }          
  
                
                if($s->icon_path != '')
                {
                    $img_path = FCPATH . 'files/'.$s->icon_path;
                    list($width, $height, $type, $attr) = getimagesize($img_path);
                    //$dpi = get_dpi($img_path);
                    $iconAreaWidth = 30;
                    $iconAreaHeight = 30;
                    if ($width/$height > $iconAreaWidth/$iconAreaHeight) {
                        $iconWidth = $iconAreaWidth;
                        $iconHeight = ($height/$width) * $iconWidth;
                    } else {
                        $iconHeight = $iconAreaHeight;
                        $iconWidth = ($width/$height) * $iconHeight;
                    }
                    if (file_exists($img_path)) {
                        $pdf->Image($img_path, 10, $pdf->GetY() + 5, $iconWidth, $iconHeight);
                    }
                }
                
                $pdf->SetFont('PTSansBold','',16);
                $pdf->SetTextColor(129, 0, 15);
                $pdf->Text(41, $pdf->GetY() + 10, $s->name);
                
                $pdf->SetTextColor(0, 0, 15);
                $pdf->SetFont('PTSans','',11);
                
                $y_start = $pdf->GetY() + 13;
                $pdf->SetXY(40, $y_start);
                $pdf->MultiCell(160, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($s->value) ), ENT_QUOTES))), 0, "J");
                
                // If an icon was output, is the icon higher than the text?
                // If so, we need to manually adjust the Y var
                if($height > 0) {
                    //$dpi_y = $dpi[1];
                    $dpi_y = 300;
                    $img_height_mm = (25.4 / $dpi_y) * $height;   // 25.4 mm per inch                     
                    $y_end = $pdf->GetY();
                    
                    // Sometimes the section text spans the next page, in which case the y end position can be 
                    // less than the y start.  In this case, we don't need to adjust the brochure height.
                    if($y_end > $y_start) {
                        $text_height = $y_end - $y_start; 
                        
                        if($img_height_mm > $text_height) {
                            $diff = $img_height_mm - $text_height; 

                            $pdf->SetY($pdf->GetY() + $diff); 
                        } 
                    }
                }              
            }
        }
    }
    
    function _process_area($data, &$pdf)
    {
        $pdf->SetMargins(0, 25, 0);
        $pdf->setData($data, 'area');
        
        $pdf->AddPage();

        $y = 32;
        // ************************ Sidebar *****************************
        // *************************************************************
        // Output Overview Heading
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(144, $y, "Overview");
        
        $pdf->SetY($y+3);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(144, $pdf->GetY());
        $pdf->MultiCell(62, 5, $pdf->decodeText($data["AREA_SHORT_DESCRIPTION"]), 0, "J");

        // Specifications
        $y = $pdf->GetY();
        $y += 5;
        $pdf->SetY($y);
        $specificationImage = FCPATH . "images/member/brochure-specifications.jpg";
        $pdf->Image($specificationImage, 140.75, $y, 68.5);
        $y += 18;
        $pdf->SetY($y);
        
        $labelY = $y;
        $aMultiLines = array(
            'Average Annual Growth',
            'Weekly Median Advertised Rent',
            'Weekly Median Household Income',
            'Approx Distance to CBD',
            'No. Private Dwellings',
            'Approx time to CBD',
        );
        $prevLabel = '';
        foreach ($data['AREA_SPECIFICATIONS'] as $label=>$text)
        {
            $pdf->SetTextColor(2,2,2);
            $pdf->SetFont('PTSansBold','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->SetXY(144, $labelY-4);
                $pdf->MultiCell(42, 4, $label, 0, "L");
                $labelY = $pdf->GetY() + 6;
            } else {
                $pdf->Text(145, $labelY, $label);
                $labelY += 6;
            }
            $pdf->SetFont('PTSans','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->Text(187, $labelY-7, $text);
            } else {
                if (in_array($prevLabel, $aMultiLines)) {
                    $pdf->Text(187, $labelY-6, $text);
                    $y = $labelY;
                } else {
                    $pdf->Text(187, $y, $text);
                    $y += 6;
                }
            }
            $prevLabel = $label;
        }
        // end specifications
        
        // ************************ Main *****************************
        // *************************************************************
        // Output area image
        if(($data["HERO_IMAGE"] != "") && (file_exists($data["HERO_IMAGE"]))) {
            $pdf->Image($pdf->checkImage($data["HERO_IMAGE"]), 0, 22, 140.5, 91);
            $y = 119;
        } else {
            $y = 27;
        }

        if( $data["AREA_MAP_IMAGE"] ) {
            if( file_exists($data["AREA_MAP_IMAGE"]) ) {
                $pdf->Image($pdf->checkImage($data["AREA_MAP_IMAGE"]), 5, $y, 90, 75);
            }
        }
        if( isset($data["AREA_PHOTO_1"]) ) {
            if( file_exists($data["AREA_PHOTO_1"]) ) {
                $pdf->Image($pdf->checkImage($data["AREA_PHOTO_1"]), 98, $y, 40, 35);
            }
        }
        if( isset($data["AREA_PHOTO_2"]) ) {
            if( file_exists($data["AREA_PHOTO_2"]) ) {
                $pdf->Image($pdf->checkImage($data["AREA_PHOTO_2"]), 98, $y + 40, 40, 35);
            }
        }
        
        // Output Area Overview Heading
        $y += 85;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(4, $y, "Area Overview");
        
        $pdf->SetY($y);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(4, $pdf->GetY()+3);
        $pdf->MultiCell(130, 5, $pdf->decodeText($data["AREA_OVERVIEW"]), 0, "J");
                  
        if(count($data['AREA_MOREINFO']) > 0) {
            
            //$pdf->AddPage();
            //$pdf->SetY(32);
        
            foreach($data['AREA_MOREINFO'] as $s)
            {
                $height = 0;
                //$dpi = 0;
                
                $y_start = $pdf->GetY();
                
                if($y_start > 250) {
                    $pdf->AddPage();
                    $pdf->SetY(32);   
                    $y_start = $pdf->GetY();
                }          
  
                
                if($s->icon_path != '')
                {
                    $img_path = FCPATH . 'files/'.$s->icon_path;
                    list($width, $height, $type, $attr) = getimagesize($img_path);
                    //$dpi = get_dpi($img_path);
                    $iconAreaWidth = 30;
                    $iconAreaHeight = 30;
                    if ($width/$height > $iconAreaWidth/$iconAreaHeight) {
                        $iconWidth = $iconAreaWidth;
                        $iconHeight = ($height/$width) * $iconWidth;
                    } else {
                        $iconHeight = $iconAreaHeight;
                        $iconWidth = ($width/$height) * $iconHeight;
                    }
                    if (file_exists($img_path)) {
                        $pdf->Image($img_path, 10, $pdf->GetY() + 5, $iconWidth, $iconHeight);
                    }
                }
                
                $pdf->SetFont('PTSansBold','',16);
                $pdf->SetTextColor(129, 0, 15);
                $pdf->Text(41, $pdf->GetY() + 10, $s->name);
                
                $pdf->SetTextColor(0, 0, 15);
                $pdf->SetFont('PTSans','',11);
                
                $y_start = $pdf->GetY() + 13;
                $pdf->SetXY(40, $y_start);
                $pdf->MultiCell(160, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($s->value) ), ENT_QUOTES))), 0, "J");
                
                // If an icon was output, is the icon higher than the text?
                // If so, we need to manually adjust the Y var
                if($height > 0) {
                    //$dpi_y = $dpi[1];
                    $dpi_y = 300;
                    $img_height_mm = (25.4 / $dpi_y) * $height;   // 25.4 mm per inch                     
                    $y_end = $pdf->GetY();
                    
                    // Sometimes the section text spans the next page, in which case the y end position can be 
                    // less than the y start.  In this case, we don't need to adjust the brochure height.
                    if($y_end > $y_start) {
                        $text_height = $y_end - $y_start; 
                        
                        if($img_height_mm > $text_height) {
                            $diff = $img_height_mm - $text_height; 

                            $pdf->SetY($pdf->GetY() + $diff); 
                        } 
                    }
                }              
            }
        }  
    }
	
	
	function _process_state($data, &$pdf)
    {
        $pdf->SetMargins(0, 25, 0);
        $pdf->setData($data, 'area');
        
        $pdf->AddPage();

        $y = 32;
        // ************************ Sidebar *****************************
        // *************************************************************
        // Output Overview Heading
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(144, $y, "Overview");
        
        $pdf->SetY($y+3);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(144, $pdf->GetY());
        $pdf->MultiCell(62, 5, $pdf->decodeText($data["STATE_SHORT_DESCRIPTION"]), 0, "J");

        // Specifications
        $y = $pdf->GetY();
        $y += 5;
        $pdf->SetY($y);
        $specificationImage = FCPATH . "images/member/brochure-specifications.jpg";
        $pdf->Image($specificationImage, 140.75, $y, 68.5);
        $y += 18;
        $pdf->SetY($y);
        
        $labelY = $y;
        $aMultiLines = array(
            'Average Annual Growth',
            'Weekly Median Advertised Rent',
            'Weekly Median Household Income',
            'Approx Distance to CBD',
            'No. Private Dwellings',
            'Approx time to CBD',
        );
        $prevLabel = '';
        foreach ($data['STATE_SPECIFICATIONS'] as $label=>$text)
        {
            $pdf->SetTextColor(2,2,2);
            $pdf->SetFont('PTSansBold','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->SetXY(144, $labelY-4);
                $pdf->MultiCell(42, 4, $label, 0, "L");
                $labelY = $pdf->GetY() + 6;
            } else {
                $pdf->Text(145, $labelY, $label);
                $labelY += 6;
            }
            $pdf->SetFont('PTSans','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->Text(187, $labelY-7, $text);
            } else {
                if (in_array($prevLabel, $aMultiLines)) {
                    $pdf->Text(187, $labelY-6, $text);
                    $y = $labelY;
                } else {
                    $pdf->Text(187, $y, $text);
                    $y += 6;
                }
            }
            $prevLabel = $label;
        }
        // end specifications
        
        // ************************ Main *****************************
        // *************************************************************
        // Output area image
        if(($data["HERO_IMAGE"] != "") && (file_exists($data["HERO_IMAGE"]))) {
            $pdf->Image($pdf->checkImage($data["HERO_IMAGE"]), 0, 22, 140.5, 91);
            $y = 119;
        } else {
            $y = 27;
        }

        if( $data["STATE_MAP_IMAGE"] ) {
            if( file_exists($data["STATE_MAP_IMAGE"]) ) {
                $pdf->Image($pdf->checkImage($data["STATE_MAP_IMAGE"]), 5, $y, 90, 75);
            }
        }
        if( isset($data["STATE_PHOTO_1"]) ) {
            if( file_exists($data["STATE_PHOTO_1"]) ) {
                $pdf->Image($pdf->checkImage($data["STATE_PHOTO_1"]), 98, $y, 40, 35);
            }
        }
        if( isset($data["STATE_PHOTO_2"]) ) {
            if( file_exists($data["STATE_PHOTO_2"]) ) {
                $pdf->Image($pdf->checkImage($data["STATE_PHOTO_2"]), 98, $y + 40, 40, 35);
            }
        }
        
        // Output STATE Overview Heading
        $y += 85;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(4, $y, "State Overview");
        
        $pdf->SetY($y);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(4, $pdf->GetY()+3);
        $pdf->MultiCell(200, 5, $pdf->decodeText($data["STATE_OVERVIEW"]), 0, "J");
        
        // if (sizeof($data['STATE_MOREINFO'])) {
            // $pdf->AddPage();
            // // more information
            // foreach ($data['STATE_MOREINFO'] AS $index=>$item)
            // {
                // $y = $pdf->GetY() + 10;
                
                // $pdf->SetFont('PTSansBold','',16);
                // $pdf->SetTextColor(129, 0, 15);
                // $pdf->Text(4, $y, $item->name);
                
                // $pdf->SetTextColor(0, 0, 15);
                // $pdf->SetFont('PTSans','',11);
                // $pdf->SetXY(4, $y+3);
                // $pdf->MultiCell(200, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($item->value) ), ENT_QUOTES))), 0, "J");
            // }
        // }
    }
	
	function _process_region($data, &$pdf)
    {
        $pdf->SetMargins(0, 25, 0);
        $pdf->setData($data, 'area');
        
        $pdf->AddPage();

        $y = 32;
        // ************************ Sidebar *****************************
        // *************************************************************
        // Output Overview Heading
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(144, $y, "Overview");
        
        $pdf->SetY($y+3);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(144, $pdf->GetY());
        $pdf->MultiCell(62, 5, $pdf->decodeText($data["REGION_SHORT_DESCRIPTION"]), 0, "J");

        // Specifications
        $y = $pdf->GetY();
        $y += 5;
        $pdf->SetY($y);
        $specificationImage = FCPATH . "images/member/brochure-specifications.jpg";
        $pdf->Image($specificationImage, 140.75, $y, 68.5);
        $y += 18;
        $pdf->SetY($y);
        
        $labelY = $y;
        $aMultiLines = array(
            'Average Annual Growth',
            'Weekly Median Advertised Rent',
            'Weekly Median Household Income',
            'Approx Distance to CBD',
            'No. Private Dwellings',
            'Approx time to CBD',
        );
        $prevLabel = '';
        foreach ($data['REGION_SPECIFICATIONS'] as $label=>$text)
        {
            $pdf->SetTextColor(2,2,2);
            $pdf->SetFont('PTSansBold','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->SetXY(144, $labelY-4);
                $pdf->MultiCell(42, 4, $label, 0, "L");
                $labelY = $pdf->GetY() + 6;
            } else {
                $pdf->Text(145, $labelY, $label);
                $labelY += 6;
            }
            $pdf->SetFont('PTSans','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->Text(187, $labelY-7, $text);
            } else {
                if (in_array($prevLabel, $aMultiLines)) {
                    $pdf->Text(187, $labelY-6, $text);
                    $y = $labelY;
                } else {
                    $pdf->Text(187, $y, $text);
                    $y += 6;
                }
            }
            $prevLabel = $label;
        }
        // end specifications
        
        // ************************ Main *****************************
        // *************************************************************
        // Output area image
        if(($data["HERO_IMAGE"] != "") && (file_exists($data["HERO_IMAGE"]))) {
            $pdf->Image($pdf->checkImage($data["HERO_IMAGE"]), 0, 22, 140.5, 91);
            $y = 119;
        } else {
            $y = 27;
        }

        if( $data["REGION_MAP_IMAGE"] ) {
            if( file_exists($data["REGION_MAP_IMAGE"]) ) {
                $pdf->Image($pdf->checkImage($data["REGION_MAP_IMAGE"]), 5, $y, 90, 75);
            }
        }
        if( isset($data["REGION_PHOTO_1"]) ) {
            if( file_exists($data["REGION_PHOTO_1"]) ) {
                $pdf->Image($pdf->checkImage($data["REGION_PHOTO_1"]), 98, $y, 40, 35);
            }
        }
        if( isset($data["REGION_PHOTO_2"]) ) {
            if( file_exists($data["REGION_PHOTO_2"]) ) {
                $pdf->Image($pdf->checkImage($data["REGION_PHOTO_2"]), 98, $y + 40, 40, 35);
            }
        }
        
        // Output Region Overview Heading
        $y += 85;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(4, $y, "Region Overview");
        
        $pdf->SetY($y);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(4, $pdf->GetY()+3);
        $pdf->MultiCell(200, 5, $pdf->decodeText($data["REGION_OVERVIEW"]), 0, "J");
        
        // if (sizeof($data['STATE_MOREINFO'])) {
            // $pdf->AddPage();
            // // more information
            // foreach ($data['STATE_MOREINFO'] AS $index=>$item)
            // {
                // $y = $pdf->GetY() + 10;
                
                // $pdf->SetFont('PTSansBold','',16);
                // $pdf->SetTextColor(129, 0, 15);
                // $pdf->Text(4, $y, $item->name);
                
                // $pdf->SetTextColor(0, 0, 15);
                // $pdf->SetFont('PTSans','',11);
                // $pdf->SetXY(4, $y+3);
                // $pdf->MultiCell(200, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($item->value) ), ENT_QUOTES))), 0, "J");
            // }
        // }
    }
	
    function _process_property_region($data, &$pdf)
    {
        $pdf->setData($data, 'region');
        
        //$pdf->decodeText(
        $text = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags($data['REGION']->overview))));
        $text = $pdf->decodeText($text);
        if(strlen($text) < 15) {
            return;
        } 
        
        $text2 = preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $data['REGION']->short_description ), ENT_QUOTES)));       
        
        $pdf->AddPage();
        
        //Overview
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(5, 35); 
        
        $pdf->MultiCell(200, 5, $text2, 0, "J");
        $pdf->SetXY(5, $pdf->GetY() + 5);  
        $pdf->MultiCell(200, 5, $text, 0, "J");
        
    
        if(array_key_exists("REGION_METAS", $data)) {
            foreach($data['REGION_METAS'] as $s) {
                if($s->icon_path != '') {
                    $img_path = FCPATH . 'files/'.$s->icon_path;
                    list($width, $height, $type, $attr) = getimagesize($img_path);
                    $iconAreaWidth = 30;
                    $iconAreaHeight = 30;
                    if ($width/$height > $iconAreaWidth/$iconAreaHeight) {
                        $iconWidth = $iconAreaWidth;
                        $iconHeight = ($height/$width) * $iconWidth;
                    } else {
                        $iconHeight = $iconAreaHeight;
                        $iconWidth = ($width/$height) * $iconHeight;
                    }
                    
                    if (file_exists($img_path)) {
                        $pdf->Image($img_path, 10, $pdf->GetY() + 5, $iconWidth, $iconHeight);
                    }
                }
                
                $pdf->SetFont('PTSansBold','',16);
                $pdf->SetTextColor(129, 0, 15);
                $pdf->Text(41, $pdf->GetY() + 10, $s->name);
                
                $pdf->SetTextColor(0, 0, 15);
                $pdf->SetFont('PTSans','',11);
                $pdf->SetXY(40, $pdf->GetY() + 13);
                $pdf->MultiCell(160, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($s->value) ), ENT_QUOTES))), 0, "J");
            }
        }
    }
	
	function _process_australia($data, &$pdf)
    {
        $pdf->SetMargins(0, 25, 0);
        $pdf->setData($data, 'australia');
        
        $pdf->AddPage();

        $y = 35;
        // ************************ Sidebar *****************************
        // *************************************************************
        // Output Overview Heading
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(144, $y, "Overview");
        
        $pdf->SetY($y+3);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(144, $pdf->GetY());
        $pdf->MultiCell(62, 5, $pdf->decodeText($data["AUSTRALIA_SHORT_DESCRIPTION"]), 0, "J");

        // Specifications
        $y = $pdf->GetY();
        $y += 5;
        $pdf->SetY($y);
        $specificationImage = FCPATH . "images/member/brochure-specifications.jpg";
        $pdf->Image($specificationImage, 140.75, $y, 68.5);
        $y += 18;
        $pdf->SetY($y);
        
        $labelY = $y;
        $aMultiLines = array(
            'Average Annual Growth',
            'Weekly Median Advertised Rent',
            'Weekly Median Household Income',
            'Approx Distance to CBD',
            'No. Private Dwellings',
            'Approx time to CBD',
        );
        $prevLabel = '';
        foreach ($data['AUSTRALIA_SPECIFICATIONS'] as $label=>$text)
        {
            $pdf->SetTextColor(2,2,2);
            $pdf->SetFont('PTSansBold','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->SetXY(144, $labelY-4);
                $pdf->MultiCell(42, 4, $label, 0, "L");
                $labelY = $pdf->GetY() + 6;
            } else {
                $pdf->Text(145, $labelY, $label);
                $labelY += 6;
            }
            $pdf->SetFont('PTSans','',11);
            if (in_array($label, $aMultiLines)) {
                $pdf->Text(187, $labelY-7, $text);
            } else {
                if (in_array($prevLabel, $aMultiLines)) {
                    $pdf->Text(187, $labelY-6, $text);
                    $y = $labelY;
                } else {
                    $pdf->Text(187, $y, $text);
                    $y += 6;
                }
            }
            $prevLabel = $label;
        }
        // end specifications
        
        // ************************ Main *****************************
        // *************************************************************
        // Output australia image
        if(($data["HERO_IMAGE"] != "") && (file_exists($data["HERO_IMAGE"]))) {
            $pdf->Image($pdf->checkImage($data["HERO_IMAGE"]), 0, 30.5, 140.5, 91);
            $y = 119;
        } else {
            $y = 27;
        }

        if( $data["AUSTRALIA_MAP_IMAGE"] ) {
            if( file_exists($data["AUSTRALIA_MAP_IMAGE"]) ) {
                $pdf->Image($pdf->checkImage($data["REGION_MAP_IMAGE"]), 5, $y, 90, 75);
            }
        }
        if( isset($data["AUSTRALIA_PHOTO_1"]) ) {
            if( file_exists($data["AUSTRALIA_PHOTO_1"]) ) {
                $pdf->Image($pdf->checkImage($data["AUSTRALIA_PHOTO_1"]), 98, $y, 40, 35);
            }
        }
        if( isset($data["AUSTRALIA_PHOTO_2"]) ) {
            if( file_exists($data["AUSTRALIA_PHOTO_2"]) ) {
                $pdf->Image($pdf->checkImage($data["AUSTRALIA_PHOTO_2"]), 98, $y + 40, 40, 35);
            }
        }
        
        // Output Australia Overview Heading
        $y += 85;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(4, $y, "Australia Overview");
        
        $pdf->SetY($y);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(4, $pdf->GetY()+3);
        $pdf->MultiCell(200, 5, $pdf->decodeText($data["AUSTRALIA_OVERVIEW"]), 0, "J");
        
        
    }
	
	function _process_summary($data, &$pdf)
    {
        if($data['ADD_SUMMARY'] != 'on')
            return;
        // $pdf->setHeader($data['SUMMARY']);
        $pdf->SetMargins(0, 25, 0);
        $pdf->setData($data, 'property_summary');
        
        $pdf->AddPage();

        $y = 35;
        // ************************ Sidebar *****************************
        // *************************************************************
        // Output Overview Heading
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(10, $pdf->GetY());
        $pdf->MultiCell(190, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($data['SUMMARY']->description) ), ENT_QUOTES))), 0, "J");
    }
    
    function _process_property_manual($data, &$pdf)
    {   
        $pdf->setType('property_manual');
        $pdf->SetMargins(0, 35, 0);
        $pdf->AddPage();
        
        // Output area image
        $img_path = FCPATH . $data->image;
        
        if( file_exists($img_path) ) {
            $img_path = $pdf->checkImage($img_path);
            list($width, $height, $type, $attr) = getimagesize($img_path);
            
            if($width > $height) {
                $ratio = $height / $width;
                $height = 200 * $ratio;
                $top = (256 - $height) / 2;
                $pdf->Image($img_path, 5, $top + 22, 200, $height);
            } else if($width == $height) {
                $pdf->Image($img_path, 5, 200, 200, $height);
            } else {
                // The image is higher than it is wide.
                // Figure out the width when the image is full screen height.
                $ratio = $width / $height;    

                $width = 246 * $ratio;   
                
                // Figure out the left offset to center the image.
                /*
                $left = (210 - $width) / 2;
                $pdf->Image($img_path, $left, 27, $width, 246);
                $width = 250 * $ratio;   
                */
                
                // Figure out the left offset to center the image.
                $left = (210 - $width) / 2;
                $pdf->Image($img_path, $left, 25, $width, 250);
            }
        }
    }
    
    function _process_property_floorplan($data, &$pdf)
    {   
        $pdf->setType('property_floorplan');
        $pdf->SetMargins(0, 35, 0);
        $pdf->AddPage();
        
        // Output area image
        $img_path = FCPATH . $data['IMG_URL'];
        $img_path2 = FCPATH . strtolower($data['IMG_URL']);
        if( $img_path || $img_path2) {
            if (file_exists($img_path)) {
                $img_path = $pdf->checkImage($img_path);
            } else if (file_exists($img_path2)) {
                $img_path = $pdf->checkImage($img_path2);    
            } else {
                // No valid image path
                return;    
            }
            
            list($width, $height, $type, $attr) = getimagesize($img_path);
            
            if($width > $height) {
                // The image is wider than it is high
                $ratio = $height / $width;
                $height = 200 * $ratio;
                $top = (256 - $height) / 2;
                $pdf->Image($img_path, 5, $top + 22, 200, $height);
            } else if($width == $height) {
                $pdf->Image($img_path, 5, 200, 200, $height);
            } else {
                // The image is higher than it is wide.
                // Figure out the width when the image is full screen height.
                $ratio = $width / $height;   
                $width = 246 * $ratio; 
                
                if($width > 210) {
                    $width = 200;
                }  
                
                // Figure out the left offset to center the image.
                $left = (210 - $width) / 2;
                $pdf->Image($img_path, $left, 27, $width, 246);
            }            
            
        }
    }
    
    function _process_property_project($data, &$pdf)
    {
        $pdf->setData($data, 'area');
        $pdf->SetMargins(0, 35, 0);
        $pdf->AddPage();
        $y = 36;
        // ************************ Sidebar *****************************
        // *************************************************************
        // Output Overview Heading
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(114, $y, "Overview");
        
        $pdf->SetY($y);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(114, $y+3);
        $pdf->MultiCell(92, 5, $pdf->decodeText($data["PROJECT_SHORT_DESCRIPTION"]), 0, "J");

        // ************************ Main *****************************
        // *************************************************************
        // Output area image
        
        
        
        if ((!empty($pdf->data['HERO_IMAGE'])) && (file_exists($pdf->data['HERO_IMAGE']))) {
            // get the path to the image
            $imagePath = $pdf->checkImage($pdf->data['HERO_IMAGE']);;
            if(!empty($imagePath)) {
                list($pdfImageWidth, $pdfImageHeight) = calculateImageSize($imagePath, 100, 70);
                $pdf->Image($imagePath, 5, 32, $pdfImageWidth, $pdfImageHeight);
            }
        }
        
        if((isset($data["HERO_IMAGE"])) && ($data["HERO_IMAGE"] != "") && (file_exists($data["HERO_IMAGE"]))) {
            $y = 110;
        } else {
            $y = 35;
        }
        
        if( $data["PROJECT_MAP_IMAGE"] ) {
            if( file_exists($data["PROJECT_MAP_IMAGE"]) ) {
                $pdf->Image($pdf->checkImage($data["PROJECT_MAP_IMAGE"]), 85, $pdf->GetY() > 107 ? $pdf->GetY() : 107, 120, 80);
            }
        }
        
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(5, $y, "Quick Facts");
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(5, $y + 3);
        // 
        
        // Find out if the quick facts are in bullet format or not.
        $pos_begin = strpos($data["QUICK_FACTS"], '<li>');
        if($pos_begin !== FALSE) {
            // There are bullets.
            $pos_end = strpos($data["QUICK_FACTS"], '</li>', $pos_begin + 1);
                
            $img_bullet = FCPATH . "images/member/bullet.jpg";;
            
            while($pos_end !== FALSE) {
                $str = substr($data["QUICK_FACTS"], $pos_begin, $pos_end - $pos_begin);
                $pdf->Image($img_bullet, 5, $pdf->GetY() + 3);
                
                
                // $pdf->MultiCell(80, 4, $pdf->decodeText($data["QUICK_FACTS"]), 0, "J");
                $pdf->SetXY(8, $pdf->GetY() + 2);
                $pdf->MultiCell(70, 4, $pdf->decodeText(preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $str ), ENT_QUOTES)))), 0, "J");
                
                $pos_begin = strpos($data["QUICK_FACTS"], '<li>', $pos_end + 1);
                if($pos_begin !== FALSE)
                    $pos_end = strpos($data["QUICK_FACTS"], '</li>', $pos_begin + 1);
                else
                    $pos_end = FALSE;
            }            
            
        }
        else {
            $pos_end = FALSE;
            
            // There are no bullets so just output the quick facsts as normal text.
            $pdf->SetXY(1, $y + 3);
            $pdf->MultiCell(80, 4, strip_tags(str_replace("<br />", "\n", $pdf->decodeText($data["QUICK_FACTS"]))), 0, "J");
        }

        $pdf->SetY(200);
        
        foreach($data['PROJECT_MOREINFO'] as $s)
        {
            $height = 0;
            //$dpi = 0;
            
            $y_start = $pdf->GetY();

            if($y_start > 250) {
                $pdf->AddPage();
                $pdf->SetY(32);   
                $y_start = $pdf->GetY();
            }          

            if($s->icon_path != '')
            {
                $img_path = FCPATH . 'files/'.$s->icon_path;
                list($width, $height, $type, $attr) = getimagesize($img_path);
                //$dpi = get_dpi($img_path);
                $iconAreaWidth = 30;
                $iconAreaHeight = 30;
                if ($width/$height > $iconAreaWidth/$iconAreaHeight) {
                    $iconWidth = $iconAreaWidth;
                    $iconHeight = ($height/$width) * $iconWidth;
                } else {
                    $iconHeight = $iconAreaHeight;
                    $iconWidth = ($width/$height) * $iconHeight;
                }
                if (file_exists($img_path)) {
                    $pdf->Image($img_path, 10, $pdf->GetY() + 5, $iconWidth, $iconHeight);
                }
            }
            
            $pdf->SetFont('PTSansBold','',16);
            $pdf->SetTextColor(129, 0, 15);
            $pdf->Text(41, $pdf->GetY() + 10, $s->name);
            
            $pdf->SetTextColor(0, 0, 15);
            $pdf->SetFont('PTSans','',11);
            
            $y_start = $pdf->GetY() + 13;
            $pdf->SetXY(40, $y_start);
            $pdf->MultiCell(160, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($s->value) ), ENT_QUOTES))), 0, "J");
            
            // If an icon was output, is the icon higher than the text?
            // If so, we need to manually adjust the Y var
            if($height > 0) {
                //$dpi_y = $dpi[1];
                $dpi_y = 300;
                $img_height_mm = (25.4 / $dpi_y) * $height;   // 25.4 mm per inch                     
                $y_end = $pdf->GetY();
                $text_height = $y_end - $y_start; 
                
                if($y_end > $y_start) {
                    if($img_height_mm > $text_height) {
                        $diff = $img_height_mm - $text_height; 

                        $pdf->SetY($pdf->GetY() + $diff); 
                    } 
                }
            }

        }
        
    }
    
    function _process_project($data, &$pdf)
    {
        $pdf->setData($data, 'project');
        $pdf->SetMargins(0, 35, 0);
        $pdf->AddPage();
        
        $y = 39;
        // ************************ Sidebar *****************************
        // *************************************************************
        // Output Overview Heading
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(129, 0, 15);
        $pdf->Text(144, $y, "Overview");
        
        $pdf->SetY($y);
        
        $pdf->SetTextColor(0, 0, 15);
        $pdf->SetFont('PTSans','',11);
        $pdf->SetXY(144, $y+3);
        $pdf->MultiCell(62, 5, $pdf->decodeText($data["PROJECT_SHORT_DESCRIPTION"]), 0, "J");

        // ************************ Main *****************************
        // *************************************************************
        // Output area image
        if((isset($data["HERO_IMAGE"])) && ($data["HERO_IMAGE"] != "") && (file_exists($data["HERO_IMAGE"]))) {
            $pdf->Image($pdf->checkImage($data["HERO_IMAGE"]), 0, 31, 140, 91);
            $y = 127;
        } else {
            $y = 35;
        }
        
        if( isset($data["PROJECT_PHOTO_1"]) ) {
            if( file_exists($data["PROJECT_PHOTO_1"]) ) {
                $pdf->Image($pdf->checkImage($data["PROJECT_PHOTO_1"]), 5, $y, 40, 35);
            }
        }
        if( isset($data["PROJECT_PHOTO_2"]) ) {
            if( file_exists($data["PROJECT_PHOTO_2"]) ) {
                $pdf->Image($pdf->checkImage($data["PROJECT_PHOTO_2"]), 50, $y, 40, 35);
            }
        }
        if( isset($data["PROJECT_PHOTO_3"]) ) {
            if( file_exists($data["PROJECT_PHOTO_3"]) ) {
                $pdf->Image($pdf->checkImage($data["PROJECT_PHOTO_3"]), 95, $y, 40, 35);
            }
        }
        
        $pdf->SetY($y+35);
        $start_page = $pdf->PageNo();
        
        // more information
        foreach ($data['PROJECT_MOREINFO'] AS $index => $item)
        {
            if(($index == 1) && ($pdf->PageNo() == $start_page)) {
                $pdf->AddPage();
            }
            
            $width = 130;
            if($pdf->PageNo() > $start_page) {
                $width = 200;    
            }            
            
            $y = $pdf->GetY() + 10;
            
            $pdf->SetFont('PTSansBold','',16);
            $pdf->SetTextColor(129, 0, 15);
            $pdf->Text(4, $y, $item->name);
            
            $pdf->SetTextColor(0, 0, 15);
            $pdf->SetFont('PTSans','',11);
            $pdf->SetXY(4, $y+3);
            $pdf->MultiCell($width, 5, preg_replace("/[\t]/","", trim(html_entity_decode(strip_tags( $pdf->decodeText($item->value) ), ENT_QUOTES))), 0, "J");
        }
        
        $pdf->AddPage();
        
        // Table
        if (sizeof($data['PROJECT_PROPERTIES'])) {
            $y = $pdf->GetY();
            $y += 5;
            $pdf->SetFont('PTSansBold','',16);
            $pdf->SetTextColor(129, 0, 15);
            $pdf->Text(4, $y, 'Currently Available');
            $y += 3;
            
            // print table header text
            $curPage = $pdf->PageNo();
            $tableHeaderBg = FCPATH . 'images/member/brochure-table-header-bg.jpg';
            $pdf->Image($tableHeaderBg, 4, $y, 200);
            $y += 5;
            $pdf->SetFont('PTSansBold','',10.5);
            $pdf->SetTextColor(0, 0, 15);
            $pdf->Text(8, $y, 'Address');
            $pdf->Text(56, $y, 'Area');
            $pdf->Text(71, $y, 'State');
            $pdf->Text(82, $y, 'Estate');
            $pdf->Text(110, $y, 'Price');
            $pdf->Text(130, $y, 'Type');
            $pdf->Text(142, $y, 'Size');
            $pdf->Text(154, $y, 'Land');
            $pdf->Text(166, $y, 'Yield');
            $pdf->Text(178, $y, 'NRAS');
            $pdf->Text(190, $y, 'SMSF');
            $pdf->SetFont('PTSans','',11);
            $pdf->Text(142, $y+3, 'sqm');
            $pdf->Text(155, $y+3, 'sqm');
            $y += 8;
            $pdf->SetY($y);
//            if ($y > 217) {
//                $pdf->AddPage();
//            	$y = $y - 217;
//                $pdf->SetY($y);
//            }
            foreach ($data['PROJECT_PROPERTIES'] AS $property)
            {
                if ($y > 217) {
                    $pdf->AddPage();
                	$y = $y - 217 + 25;
                    $pdf->SetY($y);
                }
                if ($curPage != $pdf->PageNo()) {
                    $curPage = $pdf->PageNo();
                    $tableHeaderBg = FCPATH . 'images/member/brochure-table-header-bg.jpg';
                    $y = $pdf->GetY();
                    $pdf->Image($tableHeaderBg, 4, $y, 200);
                    $y += 5;
                    $pdf->SetFont('PTSansBold','',10.5);
                    $pdf->SetTextColor(0, 0, 15);
                    $pdf->Text(8, $y, 'Address');
                    $pdf->Text(56, $y, 'Area');
                    $pdf->Text(71, $y, 'State');
                    $pdf->Text(82, $y, 'Estate');
                    $pdf->Text(110, $y, 'Price');
                    $pdf->Text(130, $y, 'Type');
                    $pdf->Text(142, $y, 'Size');
                    $pdf->Text(154, $y, 'Land');
                    $pdf->Text(166, $y, 'Yield');
                    $pdf->Text(178, $y, 'NRAS');
                    $pdf->Text(190, $y, 'SMSF');
                    $pdf->SetFont('PTSans','',11);
                    $pdf->Text(142, $y+3, 'sqm');
                    $pdf->Text(155, $y+3, 'sqm');
                    $y += 8;
                    $pdf->SetY($y);
                }
                $maxY = 0;
                $pdf->SetFont('PTSans','',10);
                $pdf->SetXY(8, $y);
                $pdf->MultiCell(46, 4, "Lot $property->lot, $property->address", 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(55, $y);
                $pdf->MultiCell(14, 4, $property->area_name, 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(71, $y);
                $pdf->MultiCell(10, 4, $property->state_code, 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(81, $y);
                $pdf->MultiCell(25, 4, $property->project_name, 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(110, $y);
                $pdf->MultiCell(19, 4, '$'.number_format($property->total_price, 0, ".", ","), 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
//                $pdf->SetXY(130, $y);
//                $pdf->MultiCell(11, 4, $property->property_type);
                $pdf->SetXY(142, $y);
                $pdf->MultiCell(11, 4, $property->house_area, 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(154, $y);
                $pdf->MultiCell(11, 4, $property->land, 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(165, $y);
                $pdf->MultiCell(13, 4, number_format($property->rent_yield, 2) . '%', 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(178, $y);
                $pdf->MultiCell(11, 4, ($property->nras) ? "Yes" : "No", 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $pdf->SetXY(190, $y);
                $pdf->MultiCell(11, 4, ($property->smsf) ? "Yes" : "No", 0, 'L');
                if ($maxY < $pdf->GetY()) $maxY = $pdf->GetY();
                $tableLine = FCPATH . 'images/member/brochure-table-end.jpg';
                $pdf->Image($tableLine, 4, $maxY+2, 200);
                $y = $maxY + 5;
            }
        }
    }   
    
    function make_area_brochure($data, &$pdf = false)
    {
        if(!$pdf) {
            $pdf = new MY_FPDF('P', 'mm');
        }
        
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddFont('PTSans');
        $pdf->AddFont('PTSansBold');
        
        _process_area($data, $pdf);
        //_process_property_area($data, $pdf);
        
        // save
        $pdf->Output($data["SAVE_PATH"], $data["PDF_DEST"]);
        return true;
    }
    
	function make_region_brochure($data, &$pdf = false)
    {
        if(!$pdf) {
            $pdf = new MY_FPDF('P', 'mm');
        }
        
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddFont('PTSans');
        $pdf->AddFont('PTSansBold');
        
        _process_region($data, $pdf);
        
        // save
        $pdf->Output($data["SAVE_PATH"], $data["PDF_DEST"]);
        return true;
    }
	
	function make_australia_brochure($data, &$pdf = false)
    {
        if(!$pdf) {
            $pdf = new MY_FPDF('P', 'mm');
        }
        
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddFont('PTSans');
        $pdf->AddFont('PTSansBold');
        
        _process_australia($data, $pdf);
        
        // save
        $pdf->Output($data["SAVE_PATH"], $data["PDF_DEST"]);
        return true;
    }
	
    function make_project_brochure($data, &$pdf=false)
    {
        if(!$pdf) {
            $pdf = new MY_FPDF('P', 'mm');
        }
        
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddFont('PTSans');
        $pdf->AddFont('PTSansBold');
        
        _process_project($data, $pdf);
        
        $pdf->Output($data["SAVE_PATH"], $data["PDF_DEST"]);
        return true;
    }
    
	function make_state_brochure($data, &$pdf = false)
    {
        if(!$pdf) {
            $pdf = new MY_FPDF('P', 'mm');
        }
        
        $pdf->SetAutoPageBreak(true, 25);
        $pdf->AddFont('PTSans');
        $pdf->AddFont('PTSansBold');
        
        _process_state($data, $pdf);
        
        // save
        $pdf->Output($data["SAVE_PATH"], $data["PDF_DEST"]);
        return true;
    }
	
    function make_quote($data)
    {
        require('classes/fpdf.php');
        
        // Create new PDF, A4 is default size, in Portraight, using mm as the unit.
        // A4 is 210 mm wide X 297 mm high.
        $pdf=new FPDF('P', 'mm');
       $pdf->AliasNbPages();
        $pdf->AddPage();
       $pdf->setAutoPageBreak(false);
        
        $pdf->AddFont('PTSansBold');
        $pdf->AddFont('PTSans');
        
        // ************************ Header *****************************
        // *************************************************************
        
        $pdf->SetFont('PTSansBold','',18);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Text(10, 10, "Quotation");
        $y = 10;
        $logo = FCPATH . "images/718_quote-logo.png";
        list($width_img, $height_img) = getimagesize($logo);  
        if (file_exists($logo)) {
            $pdf->Image($logo, 140, 5, 60, 30, "PNG");
        }
        
        $y += 5;
        $fields = array("QUOTE_NO", "QUOTE_DATE");
        $fields_captions = array("Quote No:", "Issue Date:");
        
        $pdf->SetFont('PTSansBold','',12);
        $y_start = $y;
        $y_start_text = $y;
        foreach($fields_captions as $field)
        {
            $y += 7;
            $pdf->Text(10, $y, $field);            
        }
        
        $y = $y_start;
        $pdf->SetFont('PTSansBold','',14);
        $pdf->SetTextColor(0, 0, 0);
        foreach($fields as $field)
        {
            $y += 7;
            $value = $data[$field];
            $pdf->Text(35, $y, $value);
        }
        
        $y += 12;
        $line = FCPATH . "images/716_quote-divider-line.png";
        if (file_exists($line)) {
            $pdf->Image($line, 10, $y, 190, 1, "PNG");
        }
        
        $y += 12;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(81, 50, 113);
        $pdf->Text(10, $y, "Quote To");
        
        $y += 10;
        $y_start = $y;
        $pdf->SetFont('PTSansBold','',12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->Text(10, $y, $data["LEAD_COMPANY1"]);
        
        $fields = array("LEAD_NAME1", "LEAD_ADDRESS1", "LEAD_ADDRESS1_LINE3", "LEAD_COUNTRY1", "LEAD_PHONE1");
        $pdf->SetFont('PTSans','',12);
        $pdf->SetTextColor(0, 0, 0);
        foreach($fields as $field)
        {
            $y += 7;
            $value = $data[$field];
            $pdf->Text(10, $y, $value);
        }
        if ($data["LEAD2"]) {
            $fields = array("LEAD_NAME2", "LEAD_ADDRESS2", "LEAD_ADDRESS2_LINE3", "LEAD_COUNTRY2", "LEAD_PHONE2");
            $pdf->SetFont('PTSans','',12);
            $pdf->SetTextColor(0, 0, 0);
            foreach($fields as $field)
            {
                $y_start += 7;
                $value = $data[$field];
                $pdf->SetXY(100,$y_start);
                $pdf->Cell(100,0,$value,0,0,'R');
            }
        }
        
        $y += 7;
        $line = FCPATH . "images/716_quote-divider-line.png";
        if (file_exists($line)) {
            $pdf->Image($line, 10, $y, 190, 1, "PNG");
        }
        
        $y += 12;
        $y_start = $y;
        $y_start_text = $y;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(81, 50, 113);
        $pdf->Text(10, $y, "Property Details");
        $y += 2;
        $fields = array("LOT", "ADDRESS1", "ADDRESS2");
        $pdf->SetFont('PTSans','',12);
        $pdf->SetTextColor(0, 0, 0);
        foreach($fields as $field)
        {
            $y += 7;
            $value = $data[$field];
            $pdf->Text(10, $y, $value);
        }
        $y_start += 5;        
        if ($data["CUSTOM_DESIGN"] && isset($data["DEVELOPMENT_PROJECT"])) {
           $fields = array("PROJECT_NAME", "HOUSE_DESIGN");
            $fields_captions = array("Project:", "Design:");
           $xx = 150;
        } else if ($data["CUSTOM_DESIGN"] && !isset($data["DEVELOPMENT_PROJECT"])) {
           $fields = array("HOUSE_DESIGN");
            $fields_captions = array("Design:");
           $xx = 150;
        } else {
           $fields = array("PROJECT_NAME", "HOUSE_DESIGN");
            $fields_captions = array("Project:", "Design:");
           $xx = 145;
        }
        
        $pdf->SetFont('PTSansBold','',12);
        $pdf->SetTextColor(0, 0, 0);
        foreach($fields_captions as $field)
        {
            $y_start += 8;
            $pdf->Text($xx, $y_start, $field);            
        }
        $y_start_text += 5;
        $pdf->SetFont('PTSans','',12);
        $pdf->SetTextColor(0, 0, 0);
        foreach($fields as $field)
        {
            $y_start_text += 7;
            $value = $data[$field];
            $pdf->SetXY(100,$y_start_text);
            $pdf->Cell(100,0,$value,0,0,'R');
            //$pdf->Text(160, $y_start_text, $value);
        }
        
        $y += 7;
        $line = FCPATH . "images/716_quote-divider-line.png";
        if (file_exists($line)) {
            $pdf->Image($line, 10, $y, 190, 1, "PNG");
        }
        
        $y += 12;
        $pdf->SetFont('PTSansBold','',16);
        $pdf->SetTextColor(81, 50, 113);
        $pdf->Text(10, $y, "Cost Summary");
        if (sizeof($data["EXTRAS"])) {
            $y += 10;
            $extras = $data["EXTRAS"];            
            $fields = array("PROJECT_NAME", "HOUSE_DESIGN");
            $fields_captions = array("Item name", "Unit Price", "Qty", "Total");
            $x = 10;
            $pdf->SetFont('PTSansBold','',12);
            $pdf->SetTextColor(0, 0, 0);
           $cell_no = 0;
            $cell_widths = array("90", "39", "20", "39");
            foreach($fields_captions as $field)
        {

               $pdf->SetXY($x, $y);
                $pdf->Cell($cell_widths[$cell_no], 8, "  " . $field, 1, 0, 'L');
                //$pdf->Text($x, $y, $field);
            $x += $cell_widths[$cell_no];
                $cell_no++;
            }
            $pdf->SetFont('PTSans','',12);
            $pdf->SetTextColor(0, 0, 0);
            $i = 1;
            $totalextra = count($extras);            

        foreach($extras as $extra)
            {
                $y += 8;
               $x = 10;
                $cell_no = 0;
                foreach ($extra AS $item)
            {
                    $pdf->SetXY($x, $y);
                    if ($i != $totalextra) {
                        $pdf->Cell($cell_widths[$cell_no],8,"  ".$item,'LR',0,'L');
                    } else {
                        $pdf->Cell($cell_widths[$cell_no],8,"  ".$item,'LRB',0,'L');
                    }
                    //$pdf->Text($x, $y, $item);
                    $x += $cell_widths[$cell_no];
                   $cell_no++;

            }

            $i ++;
                
            }
        } else {
            $y += 10;
            $pdf->SetFont('PTSans','',12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Text(10, $y, "No additional items.");
        }
        
        $y += 10;
        $y_start = $y;
        $y_start_text = $y;        
        $fields = array("BASE_PRICE", "TOTAL_EXTRAS", "TOTAL", "GST");
       if ($data["CUSTOM_DESIGN"]) {
            $fields_captions = array("Design Price:", "Extra Items:", "Total:", "Gst Amount:");
           $xx = 130;
        } else {
           $fields_captions = array("Base Price:", "Extra Items:", "Total:", "Gst Amount:");    
            $xx = 142;
       }

    $pdf->SetFont('PTSansBold','',12);

    $pdf->SetTextColor(0, 0, 0);

    foreach($fields_captions as $field)

    {

        $y_start += 7;
            
        $pdf->Text($xx, $y_start, $field);            

    }

    

    $pdf->SetFont('PTSans','',12);

    $pdf->SetTextColor(0, 0, 0);

    foreach($fields as $field)

    {

        $y_start_text += 7;

        $value = $data[$field];

        $pdf->Text(180, $y_start_text, $value);

    }

       

       if (!$data["NEW_PAGE_EXTRA"]) {

           $y = $y_start_text + 7;

        $line = FCPATH . "images/716_quote-divider-line.png";

           if (file_exists($line)) {

               $pdf->Image($line, 10, $y, 190, 1, "PNG");

           }

        $y += 12;

        $pdf->SetFont('PTSansBold','',16);

        $pdf->SetTextColor(81, 50, 113);

        $pdf->Text(10, $y, "Signature(s)");

        

        $y += 7;

        $pdf->SetTextColor(0, 0, 0);

        $pdf->SetFont('PTSans','',12);

        $pdf->SetXY(10, $y);

        $pdf->MultiCell( 185, 5, $data["SIGNATURE"], 0, "L");

        

        $y = $pdf->GetY() + 15;

        $y_start = $y;

        $line = FCPATH . "images/717_quote-sign-line.png";

           if (file_exists($line)) {

               $pdf->Image($line, 10, $y, 80, 0.5, "PNG");

           }

           if (!empty($data["LEAD_NAME2"])) {

               if (file_exists($line)) {

                   $pdf->Image($line, 120, $y, 80, 0.5, "PNG");

               }    

           }

           

           $y += 10;

           $pdf->SetFont('PTSansBold','',12);

        $pdf->SetTextColor(0, 0, 0);

        $pdf->Text(10, $y, $data["LEAD_NAME1"]);

        

        if (!empty($data["LEAD_NAME2"])) {

               $pdf->SetFont('PTSansBold','',12);

            $pdf->SetTextColor(0, 0, 0);

            $pdf->Text(120, $y, $data["LEAD_NAME2"]);

        }
            //Page footer
           $pdf->SetY(285);
            $pdf->SetFont('Arial','I',8);
           $pdf->SetTextColor(128);
            $pdf->Cell(0,10,'Page '.$pdf->PageNo().' of {nb}',0,0,'C');
        } else {                //Page footer
            $pdf->SetY(285);
           $pdf->SetFont('Arial','I',8);
            $pdf->SetTextColor(128);
           $pdf->Cell(0,10,'Page '.$pdf->PageNo().' of {nb}',0,0,'C');
            $y = 20;
            $pdf->AddPage();
           $pdf->setAutoPageBreak(false);
            $pdf->setAutoPageBreak(false);
            $pdf->SetFont('PTSansBold','',16);
            $pdf->SetTextColor(81, 50, 113);
            $pdf->Text(10, $y, "Signature(s)");
            
            $y += 5;
            $pdf->SetTextColor(0, 0, 0);
            $pdf->SetFont('PTSans','',12);
            $pdf->SetXY(10, $y);
            $pdf->MultiCell( 185, 5, $data["SIGNATURE"], 0, "L");
            
            $y = $pdf->GetY() + 7;
            $y_start = $y;
            $line = FCPATH . "images/717_quote-sign-line.png";
            if (file_exists($line)) {
                $pdf->Image($line, 10, $y, 80, 0.5, "PNG");
            }
            
            if (!empty($data["LEAD_NAME2"])) {
                if (file_exists($line)) {
                    $pdf->Image($line, 120, $y, 80, 0.5, "PNG");
                }    
            }
            
            $y += 10;
            $pdf->SetFont('PTSansBold','',12);
            $pdf->SetTextColor(0, 0, 0);
            $pdf->Text(10, $y, $data["LEAD_NAME1"]);
            if (!empty($data["LEAD_NAME2"])) {
                $pdf->SetFont('PTSansBold','',12);
                $pdf->SetTextColor(0, 0, 0);
                $pdf->Text(120, $y, $data["LEAD_NAME2"]);
            }
        //Page footer
            $pdf->SetY(285);
           $pdf->SetFont('Arial','I',8);
            $pdf->SetTextColor(128);
           $pdf->Cell(0,10,'Page '.$pdf->PageNo().' of {nb}',0,0,'C');

       }
        
    $pdf->Output($data["SAVE_PATH"]);

       return true;

}



function check_if_image( $extension )
{
    if( $extension == "jpg" )
        return true;
        
    if( $extension == "gif" )
        return true;

    if( $extension == "png" )
        return true;

    if( $extension == "peg" )
        return true;

    return false;
}

class FPDF_MultiCellTable extends FPDF 
{
    var $widths;
    var $aligns;
    var $counter = 0;
    var $headings;
    
    function setHeading($data)
    {
        $this->headings = $data;
    }
    
    function printHeading()
    {
        $this->SetFont('PTSansBold','',13);
        $this->Row($this->headings, true);
        $this->SetFont('PTSans','',11);
    }
    
    function SetWidths($w)
    {
        //Set the array of column widths
        $this->widths=$w;
    }
    
    function SetAligns($a)
    {
        //Set the array of column alignments
        $this->aligns=$a;
    }
    
    function Row($data, $isHeading=false)
    {
        if (!$isHeading) {
            $this->counter++;
        }
        //Calculate the height of the row
        $nb=0;
        for($i=0;$i<count($data);$i++)
            $nb=max($nb,$this->NbLines($this->widths[$i],$data[$i]));
        if ($isHeading) {
            $h=5*$nb+4;
        } else {
            $h=5*$nb+2;
        }
        //Issue a page break first if needed
        $this->CheckPageBreak($h);
        
        //Draw the cells of the row
        for($i=0;$i<count($data);$i++)
        {
            $w=$this->widths[$i];
            $a=isset($this->aligns[$i]) ? $this->aligns[$i] : 'L';
            //Save the current position
            $x=$this->GetX();
            $y=$this->GetY();
            //Draw the border & Fill the cell
            if ($isHeading) {
                $this->SetFillColor(165, 13, 18);
                $this->SetTextColor(255, 255, 255);
            } else {
                $this->SetTextColor(51, 51, 51);
                if ($this->counter%2) {
                    $this->SetFillColor(255, 255, 255);
                } else {
                    $this->SetFillColor(235, 235, 235);
                }
            }
            $this->SetDrawColor(255, 255, 255);
            $this->Rect($x,$y,$w,$h,'FD');
            //Print the text
            if ($isHeading) {
                $this->SetXY($x+1,$y+2);
            } else {
                $this->SetXY($x+1,$y+1);
            }
            $this->MultiCell($w-2,5,$data[$i],0,$a);
            //Put the position to the right of the cell
            $this->SetXY($x+$w,$y);
        }
        //Go to the next line
        $this->Ln($h);
    }
    
    function CheckPageBreak($h)
    {
        //If the height h would cause an overflow, add a new page immediately
        if($this->GetY()+$h>$this->PageBreakTrigger) {
            $this->AddPage($this->CurOrientation);
            $this->printHeading();
        }
    }
    
    function NbLines($w,$txt)
    {
        //Computes the number of lines a MultiCell of width w will take
        $cw=&$this->CurrentFont['cw'];
        if($w==0)
            $w=$this->w-$this->rMargin-$this->x;
        $wmax=($w-2*$this->cMargin)*1000/$this->FontSize;
        $s=str_replace("\r",'',$txt);
        $nb=strlen($s);
        if($nb>0 and $s[$nb-1]=="\n")
            $nb--;
        $sep=-1;
        $i=0;
        $j=0;
        $l=0;
        $nl=1;
        while($i<$nb)
        {
            $c=$s[$i];
            if($c=="\n")
            {
                $i++;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
                continue;
            }
            if($c==' ')
                $sep=$i;
            $l+=$cw[$c];
            if($l>$wmax)
            {
                if($sep==-1)
                {
                    if($i==$j)
                        $i++;
                }
                else
                    $i=$sep+1;
                $sep=-1;
                $j=$i;
                $l=0;
                $nl++;
            }
            else
                $i++;
        }
        return $nl;
    }
}

/***
* Calculates the size to render an image on the PDF, given a specified maximum width and height.
* 
* @param string $imagePath - The path to the image
* @param mixed $maxImageWidth
* @param mixed $maxImageHeight
*/
function calculateImageSize($imagePath, $maxImageWidth, $maxImageHeight)
{
    if((!empty($imagePath)) || (!file_exists($imagePath))) {
        return false;
    }
    
    $pdfImageWidth = $maxImageWidth;          
    $pdfImageHeight = $maxImageHeight;

    // Get the actual dimensions of the image
    list($width, $height, $type, $attr) = getimagesize($imagePath);
    
    // Is the image in landscape
    if($width > $height) {
        // The image is landscape
        $pdfImageWidth = $maxImageWidth;
        
        // Calculate how high the image is going to be on the PDF
        $ratio = $height / $width;
        $tempHeight = $pdfImageHeight * $ratio;
        $pdfImageHeight = $tempHeight;

    } else if($width == $height) {  // Is this a SQUARE image
        // This is a simple case - set height to max height and width to max height
        $pdfImageHeight = $maxImageHeight; 
        $pdfImageWidth = $maxImageHeight;
    } else {
        // Image is in portraight
        $pdfImageHeight = $maxImageHeight; // Set default height to max height
        
        // Calculate how wide the image is going to be on the PDF
        $ratio = $width / $height;
        $tempWidth = $pdfImageWidth * $ratio;
        $pdfImageWidth = $tempWidth; 
    }  
    
    return array($pdfImageWidth, $pdfImageHeight);       
}

function get_dpi($filename){
    $a = fopen($filename,'r');
    $string = fread($a,20);
    fclose($a);

    $data = bin2hex(substr($string,14,4));
    $x = substr($data,0,4);
    $y = substr($data,4,4);

    return array(hexdec($x),hexdec($y));
}