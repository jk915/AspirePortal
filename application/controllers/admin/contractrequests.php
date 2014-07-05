<?php
class Contractrequests extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("contract_requests_model","requestmd");
        $this->load->model("document_model");
        $this->load->helper("form");
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Contract Requests Manager";
        $this->data["page_heading"] = "Contract Requests Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        $filters = array("deleted" => "*");
        
        $total_requests = $this->requestmd->get_list($filters,'id DESC');
        if ($total_requests) {
            $totalItems = $total_requests->num_rows();
        } else {
            $totalItems = 1;
        }
	    $pageno = isset($_GET['p']) ? intval($_GET['p']) : 0;
        $totalPages = ceil($totalItems/$this->records_per_page);
        if ( $pageno > $totalPages ) $pageno = $totalPages;
        if ( $pageno <= 0 ) $pageno = 1;
        $startIndex = ($pageno - 1) * $this->records_per_page;
        $requests = $this->requestmd->get_list($filters,'id DESC', $this->records_per_page, $startIndex);
        $this->data["requests"] = $requests;
        $this->data['pageno'] = $pageno;
        $this->data['totalPages'] = $totalPages;
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/contractrequests/prebody', $this->data); 
        $this->load->view('admin/contractrequests/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    function request($request_id = "")
    {
        $this->data["page_heading"] = "Contract Request Details";
        $this->data['message'] = "";
      
        $postback = $this->tools_model->isPost();
            
        if ($postback) {
            $this->_handlePost($request_id,$missing_fields);
        }
        
        if($request_id != "") //edit
        {      
            $request = $this->requestmd->get_details($request_id);
            if(!$request) {
                $this->error_model->report_error("Sorry, the request could not be loaded.", "Contract Requests/show - the request with a code of '$request_id' could not be loaded");
                return;            
            } else {
                //pass page details
                $this->data["request"] = $request;
                $this->load->model("extraitems_model");
                $filters["quote_id"] = $request->quote_id;
                $this->data["extras"] = $this->extraitems_model->get_list($filters);
            }
        }
        
        //Upload
		$this->data['images'] = $this->document_model->get_files( "contract_document", $request_id, 'order', $count_all );
		$this->data['inclusion_images'] = $this->document_model->get_files( "inclusion_document", $request_id, 'order', $count_all );
		$this->data['plan_images'] = $this->document_model->get_files( "plan_document", $request_id, 'order', $count_all );
		$this->data['count_all'] = $count_all;
        
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Contract Requests Administration Menu";
        $this->data['request_id'] = $request_id;
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/request/prebody.php', $this->data); 
        $this->load->view('admin/request/main.php', $this->data);        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);              
    }
    
    function _handlePost($request_id,&$form_values)
    {
        $this->load->model("quotes_model");
        $this->load->model("extraitems_model");
        $quote_id = isset($_POST["quote_id"]) ? $_POST["quote_id"] : 0;
        $data["total_price"] = isset($_POST["total_price"]) ? $_POST["total_price"] : 0;
         $request["status"] = $_POST["status"];
         $request_id = $this->requestmd->save($request_id,$request);
        $quote_id = $this->quotes_model->save($quote_id,$data);
        $this->quotes_model->delete_extra_item($quote_id);
        if (isset($_POST["extra"]) && sizeof($_POST["extra"]["item_name"])) {
        	foreach ($_POST["extra"]["unit_price"] AS $index=>$unit_price)
        	{
        	    if ($unit_price != 0 && $_POST["extra"]["qty"][$index] != 0 && $_POST["extra"]["total_extra"][$index] != 0) {
        	    	$extra["unit_price"] = $unit_price;
            	    $extra["item_name"] = $_POST["extra"]["item_name"][$index];
            	    $extra["quantity"] = $_POST["extra"]["qty"][$index];
            	    $extra["total"] = $_POST["extra"]["total_extra"][$index];
            	    $extra["quote_id"] = $quote_id;
					$this->extraitems_model->save('',$extra);
        	    }
        	}
        }
        if(!$quote_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the testimonial could not be saved/updated.", "TestimonialManager/testimonial save");
           return;
        }
                        
        redirect("/admin/contractrequests/request/".$request_id);
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        if (isset($_GET["type"])) {
            $type = intval($this->tools_model->get_value("type",0,"get",0,false));	
        } else {
            $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        }
        switch($type)
        {
         	case 1:
                $html = '';
                $filters = array("deleted" => "*");
                $filters["status"] = isset($_GET["status"]) ? $_GET["status"] : "";
//                if (isset($_GET["date_start"]) && $_GET["date_start"] != "") {
//                    $date = explode('/', $_GET["date_start"]);
//                    $filters["date_start"] = $date[2]."-".$date[1]."-".$date[0];
//                } else {
//                    $filters["date_start"] = date("Y-m-d");
//                }
//                if (isset($_GET["date_end"]) && $_GET["date_end"] != "") {
//                    $date = explode('/', $_GET["date_end"]);
//                    $filters["date_end"] = $date[2]."-".$date[1]."-".$date[0];
//                } else {
//                    $filters["date_end"] = date("Y-m-d");
//                }
                $filters["agent_name"] = isset($_GET["agent_name"]) && $_GET["agent_name"] != "Enter Agent Name" ? $_GET["agent_name"] : "";
                $this->load->model("contract_requests_model");
                // Pagination
                $total_requests = $this->requestmd->get_list($filters,'id DESC');
                if ($total_requests) {
                    $totalItems = $total_requests->num_rows();
                } else {
                    $totalItems = 1;
                }
        	    $pageno = isset($_GET['p']) ? intval($_GET['p']) : 0;
                $totalPages = ceil($totalItems/$this->records_per_page);
                if ( $pageno > $totalPages ) $pageno = $totalPages;
                if ( $pageno <= 0 ) $pageno = 1;
                $startIndex = ($pageno - 1) * $this->records_per_page;
                $requests = $this->requestmd->get_list($filters,'id DESC', $this->records_per_page, $startIndex);
         	    $this->load->view('admin/contractrequests/request_listing.php',array('requests'=>$requests,'pageno'=>$pageno,'totalPages'=>$totalPages));
     	    break;
     	    
     	    case 2:
     	        $this->load->library("utilities");   
                $this->load->library("parser");
                $this->load->library('email');
                $this->load->model("email_model");
                $this->load->model("reservation_model");
                $request_id = isset($_POST["request_id"]) ? $_POST["request_id"] : "";
                $message = isset($_POST["message"]) ? $_POST["message"] : false;
                $request = $this->requestmd->get_details($request_id);
                $action = isset($_POST["action"]) ? $_POST["action"] : "";
                $sendmail = false;
                if ($request) {
                	switch($action)
                    {
                        case 'approve' :
                            $data["status"] = "Approved";
                            $email_data = array ( 
                                      "agent_name"     => $request->agent_first_name." ".$request->agent_last_name,
                                      "request_date"     => date("d/m/Y",strtotime($request->request_date)),
                                      "date_of_approval"    => date("d/m/Y"),
                                      "house_design"     => $request->property_design,   
                                      "total_amount"     => "$".number_format($request->total_price, 0, ".", ","),
                                      "url"  => site_url("contract-details/".$request->id)
                            );
                            $sendmail = true;
                            $email_templete = "approve_contract";
                        break;
                        
                        case 'reject' :
                            if ($request->property_id != 0) {
                            	$this->reservation_model->delete($request->property_id);
                            }
                            $data["status"] = "Rejected";
                            $email_data = array (
                                      "agent_name"     => $request->agent_first_name." ".$request->agent_last_name,
                                      "request_date"     => date("d/m/Y",strtotime($request->request_date)),
                                      "date_of_rejection"    => date("d/m/Y"),
                                      "house_design"     => $request->property_design,   
                                      "total_amount"     => "$".number_format($request->total_price, 0, ".", ","),
                                      "reason_for_rejection"  => $message,
                                      "url"  => site_url("contract-details/".$request->id)
                            );
                            $sendmail = true;
                            $email_templete = "reject_contract";
                        break;
                        
                        case 'reopen' :
                            $data["status"] = "Pending";
                        break;
                            
                        default:
                            $data["status"] = false;
                        break;
                    }
                    if ($data["status"]) {
                    	$this->requestmd->save($request_id,$data);
                    	if ($sendmail) {
                    	   $this->email_model->send_email($request->agent_email, $email_templete, $email_data);	
                    	}
                    	echo "OK";
                    } else {
                        echo "FALSE";
                    }
                } else {
                    echo "FALSE";
                }
     	    break;
     	    
     	    case 3:
     	        // Contract Document File Upload
      			
      			// Read in the details of the file that has been uploaded
				$tmp_name = $_FILES["Filedata"]["tmp_name"];
				$name = $_FILES["Filedata"]["name"];
				$name = str_replace(" ","-",$name);
				// Read in the contract
				$request_id = $this->tools_model->get_value("request_id","","post",0,false);
                // Load the contract object
                $request = $this->requestmd->get_details($request_id);
                if(!$request)
                	die();
                if (isset($_POST["upload"]) && $_POST["upload"] == "inclusion") {
                    if(!is_dir(FCPATH.INCLUSION_FILES_FOLDER)) {
        				@mkdir(FCPATH.INCLUSION_FILES_FOLDER, 0777);
        			}
        
        			if(!is_dir(FCPATH.INCLUSION_FILES_FOLDER.$request_id)) {
        				@mkdir(FCPATH.INCLUSION_FILES_FOLDER.$request_id, 0777);
        			}                	
                    
    				// Determine file path and move the temporary file to the final destination.
    				$file_path = FCPATH . INCLUSION_FILES_FOLDER . $request_id . "/" . $name;
    				move_uploaded_file($tmp_name, $file_path);
    				chmod($file_path, 0666);
    
    				// Make sure the upload worked OK.
    				if(!file_exists($file_path)) {
    					echo "error";
    					exit();
    				}
                    // Save the gallery image into the documents table in the database.
    				$file_data =  array(
    					"document_type" => "inclusion_document",
    					"foreign_id" => $request_id,
    					"document_name" => $name,
    					"document_path" => INCLUSION_FILES_FOLDER . $request_id . "/" . $name
    				);
    				$this->document_model->save("", $file_data, $request_id, "inclusion_document", $use_order = TRUE);
				} else if (isset($_POST["upload"]) && $_POST["upload"] == "plan") {
                    if(!is_dir(FCPATH.PLAN_FILES_FOLDER)) {
        				@mkdir(FCPATH.PLAN_FILES_FOLDER, 0777);
        			}
        
        			if(!is_dir(FCPATH.PLAN_FILES_FOLDER.$request_id)) {
        				@mkdir(FCPATH.PLAN_FILES_FOLDER.$request_id, 0777);
        			}                	
                    
    				// Determine file path and move the temporary file to the final destination.
    				$file_path = FCPATH . PLAN_FILES_FOLDER . $request_id . "/" . $name;
    				move_uploaded_file($tmp_name, $file_path);
    				chmod($file_path, 0666);
    
    				// Make sure the upload worked OK.
    				if(!file_exists($file_path)) {
    					echo "error";
    					exit();
    				}
                    // Save the gallery image into the documents table in the database.
    				$file_data =  array(
    					"document_type" => "plan_document",
    					"foreign_id" => $request_id,
    					"document_name" => $name,
    					"document_path" => PLAN_FILES_FOLDER . $request_id . "/" . $name
    				);
    				$this->document_model->save("", $file_data, $request_id, "plan_document", $use_order = TRUE);
				} else {
				    if(!is_dir(FCPATH.CONTRACT_FILES_FOLDER)) {
        				@mkdir(FCPATH.CONTRACT_FILES_FOLDER, 0777);
        			}
        
        			if(!is_dir(FCPATH.CONTRACT_FILES_FOLDER.$request_id)) {
        				@mkdir(FCPATH.CONTRACT_FILES_FOLDER.$request_id, 0777);
        			}                	
                    
    				// Determine file path and move the temporary file to the final destination.
    				$file_path = FCPATH . CONTRACT_FILES_FOLDER . $request_id . "/" . $name;
    				move_uploaded_file($tmp_name, $file_path);
    				chmod($file_path, 0666);
    
    				// Make sure the upload worked OK.
    				if(!file_exists($file_path)) {
    					echo "error";
    					exit();
    				}
                    // Save the gallery image into the documents table in the database.
    				$file_data =  array(
    					"document_type" => "contract_document",
    					"foreign_id" => $request_id,
    					"document_name" => $name,
    					"document_path" => CONTRACT_FILES_FOLDER . $request_id . "/" . $name
    				);
    				$this->document_model->save("", $file_data, $request_id, "contract_document", $use_order = TRUE);
				}
				echo "done";
 	        break;
 	        
 	        case 4:
                $return_data = array();
                $request_id = $this->tools_model->get_value("request_id","","post",0,false);
                if (isset($_POST["upload"]) && $_POST["upload"] == "inclusion") {
                    $return_data["html"] = $this->_refresh_images( $request_id,"inclusion" );
                } else if (isset($_POST["upload"]) && $_POST["upload"] == "plan") {
                    $return_data["html"] = $this->_refresh_images( $request_id,"plan" );
                } else {
                    $return_data["html"] = $this->_refresh_images( $request_id );
                }
                echo json_encode($return_data); 
            break;
            
            case 5:
            	// Delete contract files
                $return_data = array();
                $request_id = intval($this->tools_model->get_value("request_id", 0, "post", 0, false));
                $file_names = $this->tools_model->get_value("todelete", "", "post", 0, false);
                if (isset($_POST["upload"]) && $_POST["upload"] == "inclusion") {
                	if ($file_names!="") {
                        $arr_files = explode(";",$file_names);
                        $this->document_model->delete_files($arr_files, $request_id, "inclusion_document");
                        $suffixes = array("_gallerythumb", "_gallerydetail", "_galleryzoom");
                        foreach($arr_files as $file_to_delete)
                        {
                        	if($file_to_delete != "") {
                        		$full_path = FCPATH . INCLUSION_FILES_FOLDER . $request_id . "/$file_to_delete";
                        		if(file_exists($full_path)) {
    								$this->image->remove_image_set($suffixes, $full_path); 	
    							}		
    						}
                        }
                    }
                    $return_data["html"] = $this->_refresh_images( $request_id,"inclusion" );
                } else if (isset($_POST["upload"]) && $_POST["upload"] == "plan") {
                	if ($file_names!="") {
                        $arr_files = explode(";",$file_names);
                        $this->document_model->delete_files($arr_files, $request_id, "plan_document");
                        $suffixes = array("_gallerythumb", "_gallerydetail", "_galleryzoom");
                        foreach($arr_files as $file_to_delete)
                        {
                        	if($file_to_delete != "") {
                        		$full_path = FCPATH . PLAN_FILES_FOLDER . $request_id . "/$file_to_delete";
                        		if(file_exists($full_path)) {
    								$this->image->remove_image_set($suffixes, $full_path); 	
    							}		
    						}
                        }
                    }
                    $return_data["html"] = $this->_refresh_images( $request_id,"inclusion" );
                } else {
                    if ($file_names!="") {
                        $arr_files = explode(";",$file_names);
                        $this->document_model->delete_files($arr_files, $request_id, "contract_document");
                        $suffixes = array("_gallerythumb", "_gallerydetail", "_galleryzoom");
                        foreach($arr_files as $file_to_delete)
                        {
                        	if($file_to_delete != "") {
                        		$full_path = FCPATH . CONTRACT_FILES_FOLDER . $request_id . "/$file_to_delete";
                        		if(file_exists($full_path)) {
    								$this->image->remove_image_set($suffixes, $full_path); 	
    							}		
    						}
                        }
                    }
                    $return_data["html"] = $this->_refresh_images( $request_id );
                }
                
                echo json_encode($return_data);
            break;
            
            case 6:
                $fileid = $this->tools_model->get_value("fileid", "", "post", 0, false);
                $file = $this->document_model->get_details($fileid);
                $this->data['file'] = $file;
                if (isset($_POST["upload"]) && $_POST["upload"] == "inclusion") {
                    $this->load->view('admin/request/editfile_inclusion', $this->data);
                } else if (isset($_POST["upload"]) && $_POST["upload"] == "plan") {
                    $this->load->view('admin/request/editfile_plan', $this->data);
                } else {
                    $this->load->view('admin/request/editfile', $this->data);
                }
                return;
            break;
            
            case 7:
            	// Update contract files
                $return_data = array();
                $desc = $this->tools_model->get_value("desc", "", "post", 0, false);
                $document_name = $this->tools_model->get_value("document_name", "", "post", 0, false);
                $id = $this->tools_model->get_value("id", "", "post", 0, false);
                if (!empty($desc) && !empty($document_name) && !empty($id)) {
                    $data["document_description"] = $desc;
                    $data["document_name"] = $document_name;
                    $file_id = $this->document_model->save($id, $data);    	
                }
                return;
            break;
        }
    }
    
    function _refresh_images( $request_id,$upload="" )
    {
        $this->data['request_id'] = $request_id;
        if ($upload == "inclusion") {
            $this->data['inclusion_images'] = $this->document_model->get_files( "inclusion_document", $request_id, 'order', $count_all );	
            $this->data["pages_no"] = $count_all / $this->records_per_page; 
            $this->data['count_all'] = $count_all;
            $images = $this->load->view('admin/request/inclusion_file_listing.php',NULL,true);
        } else if ($upload == "plan") {
            $this->data['plan_images'] = $this->document_model->get_files( "plan_document", $request_id, 'order', $count_all );	
            $this->data["pages_no"] = $count_all / $this->records_per_page; 
            $this->data['count_all'] = $count_all;
            $images = $this->load->view('admin/request/plan_file_listing.php',NULL,true);
        } else {
            $this->data['images'] = $this->document_model->get_files( "contract_document", $request_id, 'order', $count_all );
            $this->data["pages_no"] = $count_all / $this->records_per_page; 
            $this->data['count_all'] = $count_all;
            $images = $this->load->view('admin/request/file_listing.php',NULL,true);
        }
        return $images;
    }
}  
