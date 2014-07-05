<?php
die("OFFLINE");
ob_start();
class Generate_pdf extends CI_Controller 
{
	public $data;		// Will be an array used to hold data to pass to the views.
	
	function Generate_pdf()
	{
		parent::__construct();
		
		// Create the data array.
		$this->data = array();			
		
		// Load models etc
		$this->load->model("pdf_model");		
		$this->load->model("tools_model");		
	}
		
	function index()
	{
		$doc_id = 1;
		
	}
    
    function show($doc_id = 1)
    {
        $postback = $this->tools_model->isPost();
        
        if ($postback)
        {
            $this->_handlePost($doc_id);
        }
        
        $this->data['doc_id'] = $doc_id;
        $this->data['metadata'] = $this->pdf_model->get_metadata($doc_id,"source = 'post'");
        
        $this->load->view("test_form",$this->data);    
    }	
	
	function test()
	{
		$results = array();
        print "Test page";        
        print "pdftk ".ABSOLUTE_PATH."form_pdf/1.pdf fill_form ".ABSOLUTE_PATH."form_pdf/fdf/1_1264850541.fdf output ".ABSOLUTE_PATH."form_pdf/fdf/final1.pdf flatten";
        
        $tmp = exec("pdftk ".ABSOLUTE_PATH."form_pdf/1.pdf fill_form ".ABSOLUTE_PATH."form_pdf/fdf/1_1264850541.fdf output ".ABSOLUTE_PATH."form_pdf/fdf/final1.pdf flatten",$results); 
        print var_dump($results);
        
    	//$path = "e:\\\wamp\\www\\legal_test\\system\\application\\controllers\\fdf\\"; 		
               
		//print $path."pdftk.exe ".$path."1.pdf fill_form ".$path."posted-24.fdf output ".$path."final-vegre.pdf flatten";
		
		//$tmp = exec($path."pdftk.exe ".$path."legal3.pdf fill_form ".$path."posted-24.fdf output ".$path."final-vegre1.pdf flatten",$results); 
		//print var_dump($results);*/
		
		//show_error("Error: The was an error creating pdf. Please try again later.");
	}       
	
	function _handlePost($doc_id)
	{
		
        //get document details
        $doc_details = $this->pdf_model->get_details($doc_id);
        
        if ($doc_details)
        {
        
            $doc_row = $doc_details->first_row();         
        
            require_once 'includes/createFDF.php';            
			    
		    $pdf_file = base_url()."form_pdf/".$doc_row->form_pdf;
            
		    
		    //$_POST["__date_form__"] = date("jS F Y");
            
            //this array will be used to generate the FDF file
		    $arr_pdf = array();
		    
		    
            //get placeholders for the document    
			$pdf_inputs = $this->pdf_model->get_metadata($doc_id, "include_in_fdf = 1");
            
			$subject = "";
			    foreach($pdf_inputs as $row)
			    {
                    
                    //check if the field conntains mulitple placeholders
				    if($row->source != "post")
				    {
					    $subject = $row->source; 
					    preg_match_all('~__(.*?)__~ims', $subject, $matches, PREG_PATTERN_ORDER);				
					    
					    foreach($matches[0] as $match)
						    $subject = str_replace($match,$_POST[$match], $subject);
						    
					    $_POST[$row->pdf_field_name] = $subject;
				    }
				    
				    if(isset($_POST[$row->pdf_field_name]))
					    $arr_pdf[$row->pdf_field_name] = $_POST[$row->pdf_field_name];
				    
			    }
				    
                        
			        // get the FDF file contents
			        $fdf = createFDF($pdf_file,$arr_pdf);

                    
                    $fdf_file = str_replace(".pdf","_",$doc_row->form_pdf) . time().".fdf";
                    $fdf_file_path = ABSOLUTE_PATH. "form_pdf/fdf/". $fdf_file;
                    
			        // Create a file for later use
			        if($fp = fopen($fdf_file_path,'w')){
				        
				        fwrite($fp,$fdf,strlen($fdf));
                        
			        
			        }else{
				        echo 'Unable to create file: '.$fdf_file.'<br><br>';
				        
			        }
			        fclose($fp);
                    
                    /*$fdf_link = base_url()."form_pdf/fdf/".$fdf_file;                    
                    $fdf_link = base_url()."generate_pdf/show_pdf/".$fdf_file;*/
                                        
                    $final_pdf_name = "result_".str_replace(".fdf",".pdf",$fdf_file);
                    $final_pdf_link = base_url()."form_pdf/result_pdf/".$final_pdf_name;
                    
                    
                    $this->_generate_PDF($doc_row->form_pdf, $fdf_file, $final_pdf_name);
                    
                    //echo 'Click <a href="'.$final_pdf_link.'">for the PDF.</a><br/><br/>';
                   
                    //add images
                    $this->_add_logo($doc_id, $final_pdf_name);
        }
	}
    
    
    function _generate_PDF($form_pdf, $fdf_file, $final_pdf_name) 
    {
        
        $results = array();
        
        $tmp = exec("pdftk ".ABSOLUTE_PATH."form_pdf/".$form_pdf." fill_form ".ABSOLUTE_PATH."form_pdf/fdf/".$fdf_file." output ".ABSOLUTE_PATH."form_pdf/result_pdf/".$final_pdf_name." flatten",$results); 
        
        if(count($results) > 0)      
            show_error("Error: The was an error creating pdf. Please try again later.");
    }
    
    function _add_logo($doc_id, $final_pdf_name)
    {
       define('FPDF_FONTPATH','/usr/home/achapman/pdftktest/fpdf16/font/');

       include("fpdf16/fpdf.php");
       include("FPDI_1.3.2/fpdf_tpl.php");
       include("FPDI_1.3.2/fpdi.php");
       
       //get images(logos)             
       $arr_images = array();
       
       $images = $this->pdf_model->get_doc_images($doc_id);
       
       foreach($images as $img)
            $arr_images[$img->page] = $img->image;
              
       // Create a new instandce of the FPDI class and read in the existing document
       $pdf = new FPDI(); 
       $pagecount = $pdf->setSourceFile(ABSOLUTE_PATH."form_pdf/result_pdf/".$final_pdf_name); 
       
       // copy all pages from the old unprotected pdf in the new one
       for ($loop = 1; $loop <= $pagecount; $loop++) {
           
            $tplidx = $pdf->importPage($loop,'/MediaBox');
            $pdf->addPage();
            $pdf->useTemplate($tplidx);     
            $pdf->SetFont('Arial','B',10);        
            
            if(isset($arr_images[$loop]))
            {
                //Logo
                $pdf->Image(ABSOLUTE_PATH.'form_pdf/images/'.$arr_images[$loop],5,5,200);
            }
        }               
       $pdf->Output(ABSOLUTE_PATH."form_pdf/result_pdf/".$final_pdf_name,"F");
       //$pdf->Output('newpdf.pdf', 'D');    
       
       $final_pdf_link = base_url()."form_pdf/result_pdf/".$final_pdf_name;
       echo 'Click <a href="'.$final_pdf_link.'">for the PDF.</a><br/><br/>';
    }
    
    
    function show_pdf($fdf_file)
    {
        
        die("a");
        
        $path = ABSOLUTE_PATH."form_pdf/fdf./".$fdf_file;
        header("Content-type: application/vnd.fdf");
        
        $handle = fopen($path, "r");
        flush();
        readfile($path);
        fclose($handle);
        
    }
}
ob_flush();
/* End of file page.php */
/* Location: ./system/application/controllers/page.php */
?>