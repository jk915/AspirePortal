<?php    
    class PDFGenerator
    {
        private $FPDFElements;
        private $fontfamily = "Helvetica";
        private $bordercolor = "255,255,255";
        private $backcolor = "0,0,0";        
        
        public static $tax_amount = "0.00";
        public static $total_price = "0.00";
        public static $subtotal = "0.00";
        public static $paid_text = "";
        public static $include_gst = TRUE;
        
        private $structure = 
            array
            (
                //"[RECT1]"                        =>  array("x"=>"10","y"=>"50","width"=>"191","height"=>"45","style"=>"D","color"=>"51,102,255"),
                //"[RECT2]"                        =>  array("x"=>"10","y"=>"50","width"=>"13","height"=>"45","style"=>"DF","color"=>"51,102,255", "fillcolor"=>"197,214,227"),
                //"[RECT_FOR]"                     =>  array("x"=>"10","y"=>"98","width"=>"191","height"=>"38","style"=>"D","color"=>"51,102,255"),
                //"[RECT5]"                        =>  array("x"=>"10","y"=>"98","width"=>"13","height"=>"38","style"=>"DF","color"=>"51,102,255", "fillcolor"=>"197,214,227"),
                //"[RECT_duedate]"                 =>  array("x"=>"121","y"=>"98","width"=>"23","height"=>"38","style"=>"DF","color"=>"51,102,255", "fillcolor"=>"197,214,227"),
                //"TO"                             =>    array("x"=>"10","y"=>"34","width"=>"14","height"=>"40","align"=>"C"),
                /*
                "[COMPANY_NAME]"                 =>    array("x"=>"144","y"=>"26","width"=>"55","height"=>"17","align"=>"R","fontsize" => "10"),
                "[COMPANY_ADDRESS]"              =>    array("x"=>"144","y"=>"36","width"=>"55","height"=>"6","align"=>"R","fontsize" => "10"),
                "[COMPANY_PHONE]"                =>    array("x"=>"144","y"=>"41","width"=>"55","height"=>"5","align"=>"R","fontsize" => "10"),
                "[COMPANY_FAX]"                  =>    array("x"=>"144","y"=>"46","width"=>"55","height"=>"5","align"=>"R","fontsize" => "10"),
                */
                //"[INVOICE_HEADER]"               =>    array("x"=>"13","y"=>"45","width"=>"60","height"=>"10","fontweight"=>"B","align"=>"L"),
                /*"Invoice Number:"                =>    array("x"=>"13","y"=>"70","width"=>"35","height"=>"20","fontsize"=>"10","align"=>"L","fontweight"=>"B"),
                "[INVOICE_NUMBER]"               =>    array("x"=>"43","y"=>"70","width"=>"60","height"=>"20","fontsize"=>"10","align"=>"L"),
                "Invoice Date:"                  =>    array("x"=>"13","y"=>"74","width" =>"35","height"=>"20","fontsize"=>"10","align"=>"L","fontweight"=>"B"),
                "[INVOICE_DATE]"                 =>    array("x"=>"43","y"=>"74","width"=>"60","height"=>"20","fontsize"=>"10","align"=>"L"),
                */
                //"Invoice To:"                    =>    array("x"=>"17","y"=>"55","width"=>"175","height"=>"5","align"=>"L","fontsize"=>"10","style"=>"U","fontweight"=>"B"), 
                "[CLIENT_ADDRESS]"               =>    array("x"=>"12","y"=>"55","width"=>"175","height"=>"5","align"=>"L","fontsize"=>"10"),
                //"Delivery To:"                   =>    array("x"=>"105","y"=>"55","width"=>"175","height"=>"5","align"=>"L","fontsize"=>"10","style"=>"U","fontweight"=>"B"), 
                //"[DELIVERY_TO]"                  =>    array("x"=>"105","y"=>"61","width"=>"175","height"=>"5","align"=>"L","fontsize"=>"10"),
                /*
                "FOR"                            =>    array("x"=>"10","y"=>"100","width"=>"14","height"=>"5","align"=>"C"),
                "[FOR_COMMENT]"                  =>    array("x"=>"24","y"=>"100","width"=>"90","height"=>"5","align"=>"L"),
                "[PAID_DATE]"                    =>    array("x"=>"114","y"=>"92","width"=>"30","height"=>"20","fontsize"=>"10","align"=>"R"),
                "[DUE_DATE]"                     =>    array("x"=>"144","y"=>"92","width"=>"55","height"=>"20","fontsize"=>"10","align"=>"L"),                
                */
                //"PURCHASE CONFIRMATION"          =>    array("x"=>"17","y"=>"5","width"=>"65","height"=>"20","fontsize"=>"12","align"=>"L","fontweight"=>"B"),
                //"Purchase No:"                   =>    array("x"=>"17","y"=>"35","width"=>"35","height"=>"20","fontsize"=>"10","align"=>"L","fontweight"=>"B"),
                "[INVOICE_NUMBER]"               =>    array("x"=>"153","y"=>"57","width"=>"60","height"=>"20","fontsize"=>"10","align"=>"L"),
                //"Purchase Date:"                 =>    array("x"=>"17","y"=>"39","width" =>"35","height"=>"20","fontsize"=>"10","align"=>"L","fontweight"=>"B"),
                "[INVOICE_DATE]"                 =>    array("x"=>"153","y"=>"63","width"=>"60","height"=>"20","fontsize"=>"10","align"=>"L"),
                "[CUSTOMER_ID]"                  =>    array("x"=>"153","y"=>"69","width"=>"60","height"=>"20","fontsize"=>"10","align"=>"L"),
                
                "[SUBTOTAL]"                     =>    array("x" => "175", "y" => "194", "width" => "27", "height" => "20", "fontsize" => "10", "align" => "R"),                                                                                 
                "[DISCOUNT]"                     =>    array("x" => "175", "y" => "204", "width" => "27", "height" => "20", "fontsize" => "10", "align" => "R"),                                                                                 
                "[TAX_TOTAL]"                    =>    array("x" => "175", "y" => "213", "width" => "27", "height" => "20", "fontsize" => "10", "align" => "R"),                                                                                 
                "[TOTAL]"                        =>    array("x" => "175", "y" => "222", "width" => "27", "height" => "20", "fontsize" => "10", "align" => "R", "textcolor" => "255,255,255"),                                                                              
                "[COMMENTS]"                     =>    array("x" => "11", "y"=> "213", "width" => "135", "height" => "5", "fontsize" => "10", "align" => "L"), 
                
                "[ITEMS]"                        =>    array("x"=>"11","y"=>"98","fontsize"=>"10","fontweight"=>"","color"=>"0,0,0","fillcolor"=>"197,212,251","rowheight"=>6,"fillrows"=>"0", "textcolor_header" => "255,255,255", "textcolor_row" => "0,0,0",
                                                          "columndata"=>    array(
                                                                            array("width"=>"24","headername"=>"Item","align"=>"L"),
                                                                            array("width"=>"77","headername"=>"Product","align"=>"L"),
                                                                            array("width"=>"10","headername"=>"Qty","align"=>"C"),
                                                                            array("width"=>"27","headername"=>"Price","align"=>"R"),                                                                           
                                                                            array("width"=>"25","headername"=>"Tax Amt","align"=>"R"),
                                                                            array("width"=>"28","headername"=>"Total","align"=>"R")                                                                            
                                                                        ))                

            );
        
        private function setBorderColor($pdf,$row)
        {
            $color = array_key_exists("color",$row) ? $row["color"] : "";
            if ($color)
            {
                $colors_rgb = explode(",",$color);
                $pdf->SetDrawColor($colors_rgb[0],$colors_rgb[1],$colors_rgb[2]);
            }
            
            return $color;
        }
        
        private function setFillColor($pdf,$row)
        {    
            $fillcolor = array_key_exists("fillcolor",$row) ? $row["fillcolor"] : "";
            if ($fillcolor!="")
            {   
                $fillcolor_rgb = explode(",",$row["fillcolor"]);
                $pdf->SetFillColor($fillcolor_rgb[0],$fillcolor_rgb[1],$fillcolor_rgb[2]);
            }
            
            return $fillcolor;
        }
                
        private function setTextColor($pdf, $row, $property_name = "") //property_name : textcolor_header, textcolor_row
        {
            $textcolor = array_key_exists($property_name, $row) ? $row[$property_name] : "";
            if($textcolor != "")
            {
                $textcolor_rgb = explode(",",$row[$property_name]);
                $pdf->SetTextColor($textcolor_rgb[0],$textcolor_rgb[1],$textcolor_rgb[2]);
            }
            
            return $textcolor;
        }
        
        public function Generate($data,$items = null,$file = "test.pdf")
        {   
            include("fpdf16/fpdf.php");
            include("FPDI_1.3.2/fpdf_tpl.php");
            include("FPDI_1.3.2/fpdi.php");
            
            if( !is_dir( FCPATH . "invoices") )
                @mkdir(FCPATH . "invoices", 0777);
            
            $pdf_path = FCPATH."invoices/".$file;
       
            $pdf=new FPDF();
            $pdf->AddPage();
            //$pdf->setSourceFile($pdf_path); 
            /*
            $logo = FCPATH."images/logo.png";
            list($width_logo, $height_logo) = getimagesize($logo);  
            
            if (file_exists($logo)) {
            	$pdf->Image($logo, 146, 10, 0, round($height_logo * 0.179),"PNG");
            }*/
            
            //$background_img = FCPATH . "images/invoice_background.jpg"; 
            $background_img = FCPATH . "images/166_invoice-background.jpg";
            list($width_img, $height_img) = getimagesize($background_img);  
            if (file_exists($background_img)) {
                $pdf->Image($background_img, 0, 0, 0, round($height_img * 0.170), "JPG");
            }
     
            $setbackcolor = "";
            $border = "";
            $text = "";                                                                       
                    
            foreach ($this->structure as $key=>$row)
            {
                //items
                if (strcmp($key,"[ITEMS]")==0)
                {
                    $x = $row["x"];
                    $y = $row["y"];
                    $current_x = $x;
                    $current_y = $y;
                    
                    $columndata = $row["columndata"];
                    $rowheight = $row["rowheight"];
                    
                    $fillrows = $row["fillrows"]=="1";
                    $fontsize = $row["fontsize"];
                    $fontweight = $row["fontweight"];
                    
                    //colors
                    $color = $this->setBorderColor($pdf,$row); //border color
                    $fillcolor = $this->setFillColor($pdf,$row); //background color
                    $textcolor = $this->setTextColor($pdf, $row,"textcolor_header"); //text color                    
                    
                    //header
                    /*$pdf->SetTextColor($textcolor);
                    $pdf->SetFont($this->fontfamily,$fontweight,$fontsize);
                    $pdf->SetXY($x,$y);
                    $pdf->SetMargins(0,0,0);                    
                        
                    for ($i=0;$i<count($columndata);$i++)
                    {
                        $pdf->SetXY($current_x,$y);
                        $current_x += $columndata[$i]["width"];
                        $pdf->MultiCell($columndata[$i]["width"],$rowheight,$columndata[$i]["headername"],$color!="",$columndata[$i]["align"],$fillcolor!="");    
                    } */                 
                                         
                    $current_x = $x;
                    $current_y = $y + $rowheight;
                    $prev_item_text = null;
                    //rows
                                        
                    if($items)
                    {
                        //change rows color
                        $textcolor = $this->setTextColor($pdf, $row,"textcolor_row");
                        $pdf->SetDrawColor(118,144,221);
		                $border="0";//"TRB";       
                        foreach ($items as $itemrow)
                        {                               
                            $addrow = ($itemrow[2] != "0"); //the price is not 0
                            
                            if ($addrow)
                            {
                                for ($i=0;$i<count($itemrow);$i++)
                                {
                                    $pdf->SetXY($current_x,$current_y);
                                    $current_x += $columndata[$i]["width"];    
                                    
                                    if ($fillrows || ($i==count($itemrow)-2 && $itemrow[0]==""))
                                    {
                                        $this->setFillColor($pdf,$row);
                                        $pdf->SetFont($this->fontfamily,"B",$fontsize);
                                    }
                                    else
                                    {                                           
                                        //$pdf->SetFillColor(255,255,255);
                                        
                                        if (strtolower($itemrow[3])=="total")
                                            $pdf->SetFont($this->fontfamily,"B",$fontsize);
                                        else
                                            $pdf->SetFont($this->fontfamily,$fontweight,$fontsize);
                                    }
                                    
                                   /* if ($itemrow[$i]=="")
                                    {
                                        if ($prev_item_text!="")
                                            $border = "T";
                                        else
                                            $border = "";
                                    }
                                    else
                                        $border = ($color!="");*/
                                   
                                    
                                    if($i != 0 && strlen($itemrow[0]) > 64)
                                        $pdf->MultiCell($columndata[$i]["width"],$rowheight+8,$itemrow[$i],$border,$columndata[$i]["align"],false);                                                                                            
                                    else
                                        $pdf->MultiCell($columndata[$i]["width"],$rowheight,$itemrow[$i],$border,$columndata[$i]["align"],false);                                                                                            
                                }
                                
                                $newlines = substr_count($itemrow[1],"\n");                                
                                $current_x = $x;
                                $current_y += $rowheight;
                                if(strlen($itemrow[0]) > 64)
                                    $current_y += 8;    
                                $prev_item_text = $itemrow[1];
                            }       
                        //print htmlentities($itemrow[1])."<br/>";
                        $newlines = substr_count($itemrow[1],"\n");
                        //print $newlines."===".$itemrow[1]."<br/>";
                        
                        }
                        /*                         
                        //subtotal
                        $pdf->SetDrawColor(0,0,0);
                        $pdf->SetXY($x,$current_y);
                        $pdf->MultiCell(181,$rowheight,"","T",$align,$setbackcolor);
                        
                        $pdf->SetDrawColor(204,204,204);
                        $pdf->SetXY($x+99,$current_y);
                        $pdf->MultiCell(31,$rowheight,"Goods Total","B","R",$setbackcolor);
                                                
                        $pdf->SetXY($x+100,$current_y);
                        $pdf->MultiCell(81,$rowheight,"$".$this->subtotal,"B","R",$setbackcolor);                        
                        
                        $pdf->SetDrawColor(0,0,0);
                       
                        if(isset($this->include_gst) && $this->include_gst)
                        {
                            //Tax Amount
                            $pdf->SetXY($x,$current_y+6);
                            $pdf->MultiCell(120,$rowheight,"","0",$align,$setbackcolor);
                            
                            $pdf->SetXY($x+99,$current_y+6);
                            $pdf->MultiCell(40,$rowheight,"Postage&Handling","B","L",$setbackcolor);
                            
                            $pdf->SetXY($x+100,$current_y+6);
                            $pdf->MultiCell(81,$rowheight,"$".$this->tax_amount,"B","R",$setbackcolor);
                        }
                        
                        //total
                        $pdf->SetXY($x,$current_y+12);
                        $pdf->MultiCell(120,$rowheight,"","0",$align,$setbackcolor);
                        
                        $pdf->SetXY($x+99,$current_y+12);
                        $total_label = ((isset($this->include_gst) && $this->include_gst) ) ? "Total (inc GST)" : "Total";
                        $pdf->MultiCell(31,$rowheight,$total_label,"","R",$setbackcolor);
                        
                        $pdf->SetXY($x+100,$current_y+12);
                        $pdf->MultiCell(81,$rowheight,"$".$this->total_price,"","R",$setbackcolor);
                        */
                        /*
                        //This invoice has been paid in full.
                        $pdf->SetXY($x,$current_y+27);
                        $pdf->MultiCell(130,$rowheight,"","0",$align,$setbackcolor);
                        
                        $pdf->SetXY($x+80,$current_y+27);
                        $pdf->MultiCell(30,$rowheight,"","","R",$setbackcolor);
                        
                        $pdf->SetXY($x+110,$current_y+27);
                        $pdf->MultiCell(81,$rowheight,$this->paid_text,"","R",$setbackcolor);*/
                          
                    } 
                }
                //if we need to draw a rectangle
                elseif (strpos($key,"[RECT")!==FALSE)
                {
                    $color = $this->setBorderColor($pdf,$row);
                    $fillcolor = $this->setFillColor($pdf,$row);
                    
                    $style =  array_key_exists("style",$row) ? $row["style"]: "D";
                    
                    $pdf->Rect($row["x"],$row["y"],$row["width"],$row["height"],$style);
                
                }
                else
                {  
                    //determining whether the text (the key of the $structure array) is a static text or 
                    //it has to be retrieved from the data source
                    $token_start_position = strpos($key,"[");
                    $real_key = "";
                    if ($token_start_position>=0)
                    {
                        $token = substr($key,$token_start_position,strlen($key)-$token_start_position+1);
                        if (array_key_exists($token,$data))
                        {
                            if ($token_start_position>0)
                                $text = substr($key,0,$token_start_position).$data[$token];
                            else
                                $text = $data[$token];
                        }
                        else
                            $text = $key;
                    }
                    else
                        $text = $key;    
                        
                        
                    $align = array_key_exists("align",$row) ? $row["align"] : "C";    
                    //font
                    $fontweight = array_key_exists("fontweight",$row) ? $row["fontweight"] : "";
                    $fontsize = array_key_exists("fontsize",$row) ? $row["fontsize"] : "12";
                    
                    $textcolor = $this->setTextColor($pdf, $row, "textcolor"); //text color                    
                    
                    if($textcolor == "")    
                        $pdf->SetTextColor(0,0,0);
                        
                    $pdf->SetFont($this->fontfamily,$fontweight,$fontsize);
                    $pdf->SetXY($row["x"],$row["y"]);
                    $pdf->SetMargins(0,0,0);
                
                    $pdf->MultiCell($row["width"],$row["height"],$text,$border,$align,$setbackcolor);
                }
                    
                    
            
            }
            
            $pdf->Output($pdf_path,'F');
        }
    }
?>
