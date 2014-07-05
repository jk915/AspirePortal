<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
* @property Tools_model $tools_model
* @property property_model $property_model
*/
class Australia extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;  
    private $images_records_per_page = 3;  
    private $doc_type = "Australia_document";
    public $australia_id = '1';
    function __construct()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("region_model");
        $this->load->model("australia_model");
		$this->load->model("state_model");
        $this->load->model("property_model");
        $this->load->model("document_model");
        $this->load->model('australia_meta_model');
        $this->load->model('comment_model', 'commentmd');
        $this->load->model('link_model');
        $this->load->library("utilities");    
        $this->load->library("image");
                
        //if the $ci_session is passed in post, it means the swfupload has made the POST, don't check for login
        $ci_session = $this->tools_model->get_value("ci_session","","post",0,false);
      
        if ($ci_session == "")
        {

           // Check for a valid session
            if (!$this->login_model->getSessionData("logged_in"))            
                redirect("admin/login");       
        }    
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Australia";
        $this->data["page_heading"] = "Australia";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        $this->data["australia"] = $this->australia_model->get_details();
		$australia_id = $this->data["australia"]->australia_id;
        $this->data["regions"] = $this->region_model->get_list(-1,$this->records_per_page,1,$count_all);
		$this->data["states"] = $this->property_model->get_states(1);	
		$this->data["pages_no"] = $count_all / $this->records_per_page;
		
		//$this->data["australia"] = $this->australia_model->get_details();		
		//Actual $this->data["regions"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("regions");
        $this->data["projects"] = $this->db->order_by("project_name ASC")->get("projects");	
		$this->data["metas"] = $this->australia_meta_model->get_list(array('australia_id'=>$australia_id));
        $this->data["links"] = $this->link_model->get_list('australia_link', $australia_id);
        $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$australia_id, 'type' => "australia_comment"));
		$this->data["documents"] = $this->document_model->get_list($this->doc_type, $australia_id);
		$this->data["images"] = $this->document_model->get_list("australia_gallery", $australia_id); 
        $this->data["images_records_per_page"] = $this->images_records_per_page;
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/australia/prebody.php', $this->data); 
        $this->load->view('admin/australia/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
	
	function update_australia()
	{
		
		if($this->input->post('submit'))
		{
		
			$this->data["australia"] = $this->australia_model->get_details();
			$australia_id = $this->data["australia"]->australia_id;
			$overview = $this->input->post('overview');
			$enabled = $this->input->post('enabled');
			$short_description = $this->input->post('short_description');
			$australia_name = $this->input->post('australia_name');
			$last_modified_by = $this->login_model->getSessionData("id");
			$data = array(
				'australia_id' => $australia_id,
				'overview' => $overview,
				'enabled' => $enabled,
				'short_description' => $short_description,
				'australia_name' => $australia_name,
				'last_modified_by' => $last_modified_by
			);
			
			$this->australia_model->save($australia_id,$data);
			redirect('admin/australia','refresh');
		}
	}
	
	function ajaxwork()
    {
       $type = intval($this->tools_model->get_value("type",0,"post",0,false));
       $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
       
        switch($type)
        {
            
            case 5: // Delete hero_image
            
                $australia_id = $this->tools_model->get_value("australia_id","","post",0,false);
                //do we have a valid australia ?
                if (is_numeric($australia_id)) {
                    
                    $australia_folder = FCPATH;
                    
                    $australia = $this->australia_model->get_details();
                    
                    if ($australia) {
                            $hero_image_name = $australia->australia_hero_image;
                            //delete files
                            if (file_exists($australia_folder.$hero_image_name)) unlink($australia_folder.$hero_image_name);
                            if (file_exists($australia_folder.$hero_image_name . "_thumb.jpg")) unlink($australia_folder . $hero_image_name . "_thumb.jpg");
                            $this->australia_model->save($australia_id,array( "australia_hero_image"=> "" ));      
                            die("done");
                    }
                    else
                        die("Error: australia id not found");
                }
                else
                        die("Error: Not a valid australia id");
            break;
            
           case 7: //refresh document path
                $doc_id = intval($this->tools_model->get_value("doc_id",0,"post",0,false));
                
                $doc_details = $this->document_model->get_details($doc_id); 
                
                $document_path = $doc_details->document_path;          
                
                $return_data = array();
                $return_data["doc_id"] = $doc_id;
                $return_data["document_path"] = $document_path;
                
                echo json_encode($return_data);
                
            break;    
            
            case 8: // Delete document path
            
                $doc_id = intval($this->tools_model->get_value("doc_id","","post",0,false));
                $region_id = $this->tools_model->get_value("region_id","","post",0,false);
                
                $this->utilities->add_to_debug("Doc ID: $doc_id, $area_id");
                
                if(($doc_id == "") || ($region_id == "")) {
                	$this->utilities->add_to_debug("regionmanager.php - Missing variables in case 12 - delete: $doc_id, $region_id");            
				}
               
                //do we have a valid area_id ?
                if (is_numeric($region_id)) {
                    $doc_data = array(    
                            "document_path"   => ""
                    ); 
                    
                    $this->document_model->save($doc_id,$doc_data,$area_id);
                }
                     
                $return_data = array();
                $return_data["doc_id"] = json_encode($doc_id);
                
                echo json_encode($return_data);
                
            break;
            
            case 9: // Add new Meta data
            
                $error_message = '';
                $data = array();
                
                $name = isset($_POST['title']) ? $_POST['title'] : '';
                $value = isset($_POST['content']) ? $_POST['content'] : '';
                $meta_id = isset($_POST['meta_id']) ? $_POST['meta_id'] : '';
                $australia_id = isset($_POST['australia_id']) ? $_POST['australia_id'] : 0;
                $data = array(
                                'name' => $name,
                                'value' => $value,
                                'australia_id' => $australia_id
                            );
                
                if (!empty($meta_id)) {
                    $this->australia_meta_model->save($meta_id,$data);
                } else {
                    $this->australia_meta_model->save('',$data);
                }
                
                $return_data = array(
                	                       'status' => 'OK',
                	                   );
                    echo json_encode($return_data);
                exit();

            break;
            
            case 10: // Load Meta Data
            
                $australia_id = isset($_POST['australia_id']) ? $_POST['australia_id'] : 0;
                $metas = $this->australia_meta_model->get_list(array('australia_id'=>$australia_id));
                
                $html  = '<tr>';
                $html .= '    <th width="10%">ID</th>';
                $html .= '    <th align="left">Section Name</th>';
                $html .= '    <th>Action</th>';            
                $html .= '</tr>';
                
                if ($metas) {
                    foreach ($metas->result() AS $meta)
                    {
                        $html .= '<tr>';
                        $html .= '    <td class="admintabletextcell" align="center">'.$meta->id.'</td>';
                        $html .= '    <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="'.$meta->id.'" class="btnedit">'.$meta->name.'</a></td>';
                        $html .= '    <td class="center"><input type="checkbox" class="metatodelete" value="'.$meta->id.'" /></td>';
                        $html .= '</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;
            
            case 11: // Delete Meta Data
            
                $status = 'FAILED';
                $meta_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($meta_ids != "") {
                    $arr_ids = explode(";",$meta_ids);
                    if (sizeof($arr_ids)) {
                        $this->australia_meta_model->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo json_encode($status);
                exit();
            break;
            
            case 12: // Load form edit meta data
            
                $meta_id = isset($_POST['meta_id']) ? $_POST['meta_id'] : '';
                $meta = $this->australia_meta_model->get_details($meta_id);
                if ($meta) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'name' => $meta->name,
                	                       'value' => $meta->value,
                	                       'meta_id' => $meta->id
                	                   );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                	                       'status' => 'FAILED',
                	                   );
                    echo json_encode($return_data);
                }
                
            break;
            
            case 13: // Delete comments
            
                $status = 'FAILED';
                $ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($ids != "") {
                    $arr_ids = explode(";",$ids);
                    if ( sizeof($arr_ids) ) {
                        $this->commentmd->delete($arr_ids);
                        $return_data = array(
                	                       'status' => 'OK',
                	                   );
                    echo json_encode($return_data);
                    }
                }
                echo $status;
                exit();
            break;
            
            case 14: // Add new Link
                $error_message = '';
                $data = array();
                
                $title = isset($_POST['title']) ? $_POST['title'] : '';
                $url = isset($_POST['url']) ? $_POST['url'] : '';
                $link_id = isset($_POST['link_id']) ? $_POST['link_id'] : '';
                $australia_id = isset($_POST['australia_id']) ? $_POST['australia_id'] : 0;
                $data = array(
                                'link_type' => 'australia_link',
                                'title' => $title,
                                'url' => $url,
                                'foreign_id' => $australia_id
                            );
                
                if (!empty($link_id)) {
                    $this->link_model->save($link_id,$data);
                } else {
                    $this->link_model->save('',$data);
                }
                
               $return_data = array(
                	                       'status' => 'OK',
                	                   );
                    echo json_encode($return_data);
                exit();
            break;
            
            case 15:
                // Load Links
                $australia_id = isset($_POST['australia_id']) ? $_POST['australia_id'] : 0;
                $links = $this->link_model->get_list('australia_link', $australia_id);
                
                $html = '<tr>';
                $html.= '<th width="10%">ID</th>';
                $html.= '    <th align="left">Title</th>';
                $html.= '    <th width="50%" align="left">Url</th>';
                $html.= '    <th width="10%">Delete</th>';                
                $html.= '</tr>';
                
                if ($links) {
                    foreach ($links->result() AS $link)
                    {
                        $html .= '<tr>';
                        $html .= '    <td class="admintabletextcell" align="center">'.$link->link_id.'</td>';
                        $html .= '    <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="'.$link->link_id.'" class="editlink">'.$link->title.'</a></td>';
                        $html .= '    <td class="admintabletextcell" style="padding-left:12px;">'.$link->url.'</td>';
                        $html .= '    <td class="center"><input type="checkbox" class="linktodelete" value="'.$link->link_id.'" /></td>';
                        $html .= '</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;
            
            case 16: // Delete Link
            
                $status = 'FAILED';
                $link_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($link_ids != "") {
                    $arr_ids = explode(";",$link_ids);
                    if (sizeof($arr_ids)) {
                        $this->link_model->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo json_encode($status);
                exit();
            break;
            
            case 17: // Edit Link
                
                $link_id = isset($_POST['link_id']) ? $_POST['link_id'] : '';
                $link = $this->link_model->get_details($link_id);
                if ($link) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'title' => $link->title,
                	                       'url' => $link->url,
                	                       'link_id' => $link->link_id
                	                   );
                    echo json_encode($return_data);
                } else {
                    $return_data = array(
                	                       'status' => 'FAILED',
                	                   );
                    echo json_encode($return_data);
                }
            
            break;
            
            case 18: // Add comment
                
                $error_message = '';
                $data = array();
                $australia_id = isset($_POST['australia_id']) ? $_POST['australia_id'] : 0;
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $data = array(
                                'type' => 'australia_comment',
                                'comment' => $comment,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $australia_id,
                                'datetime_added' => date('Y-m-d H:i:s')
                            );
                
                if (!empty($comment_id)) {
                    $this->commentmd->save($comment_id,$data);
                } else {
                    $this->commentmd->save('',$data);
					
                }
                
                $return_data = array(
                	                       'status' => 'OK',
                	                   );
                    echo json_encode($return_data);
                exit();
                
            break;
            
            case 19:
                // Load Comments
                $australia_id = isset($_POST['australia_id']) ? $_POST['australia_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'australia_comment','foreign_id'=>$australia_id) );
                
                $html ='<tr>';
                $html.='    <th width="10%">ID</th>';
                $html.='    <th align="left">Comment</th>';
                $html.='    <th width="10%">Delete</th>';
                $html.='</tr>';
                
                if ($comments) {
                    foreach ($comments->result() AS $index=>$comment)
                    {
                        $html.='<tr id="acomment_'.$comment->id.'" class="'.$index%2 ? 'admintablerowalt' : 'admintablerow'.'">';
                        $html.='    <td class="admintabletextcell" align="center">'.$comment->id.'</td>';
                        $html.='    <td class="admintabletextcell" style="padding-left:12px;">';
                        $html.='        <span style="font-weight:bold">'.trim("$comment->first_name $comment->last_name").'</span>';
                        $html.='        @ <em style="font-style:italic;">'.date('d/m/Y h:i A', $comment->ts_added).'</em>:<br />';
                        $html.='        "'.nl2br($comment->comment).'"';
                        $html.='    </td>';
                        $html.='    <td class="center"><input type="checkbox" class="commenttodelete" value="'.$comment->id.'" /></td>';
                        $html.='</tr>';
                    }
                }
                
                echo $html;
                exit();
            break;
            
            case 20: // refresh gallery file
                $region_id = intval($this->tools_model->get_value("region_id",0,"post",0,false));
                $this->_refresh_files($region_id);
            break;
            
            case 21: // Download image file
                $fid = intval($this->tools_model->get_value("fid",0,"post",0,false)); 
                $file = $this->db->get_where('documents', array(
                            'id' => $fid
                        ))->row();
                if ($file) {
                	$path = FCPATH . $file->document_path;
                    
                    $this->load->helper('file');
                    write_file('text.txt', "path:" . $path, 'a+');
                    
                    if(file_exists($path)) {
                        $this->utilities->download_file($path);
                    }
                }
                
            break;
            
            case 22:
                //get files names separated with ";"
                $files_id = $this->tools_model->get_value("todelete","","post",0,false);
                $region_id = intval($this->tools_model->get_value("region_id",0,"post",0,false));
                $file_names = array();
                $doc_type = "region_gallery";

                if ($files_id!="") {

                    $arr_id_files = explode(";",$files_id);

                    $removed_regions = $this->db->where_in('id',$arr_id_files)
                                            ->get('documents');

                    //delete from documents table

                    $this->document_model->delete($arr_id_files, '');

                    //delete images from folders           

                    if($removed_regions) {
                        foreach($removed_regions->result() as $row) {
                            $file_names[] = $row->document_name;
                            $file_names[] = $row->document_name.'_thumb.jpg';
                        }
                            
                        $this->utilities->remove_file(REGION_FILES_FOLDER.$region_id."/images",$file_names,"");
                    }
                }
            break;
            
        case 23: // save file desc
            $ids = isset($_POST['ids']) ? (array) $_POST['ids'] : array();
            $aDesc = isset($_POST['desc']) ? (array) $_POST['desc'] : array();
            foreach ($ids as $index=>$id)
            {
                $desc = $aDesc[$index];
                $this->db->update('documents', array('document_description'=>$desc), array('id'=>$id));
            }
            echo 'OK';
            break;
            
        }
    }
	
	function upload_file($upload_type, $australia_id, $doc_id='', $doc_name='', $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		$australia_id = $this->australia_id;

		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($australia_id)))
		{
			die ('{error: "Invalid upload type $upload_type or australia id $australia_id"}');
		}
		
		// Handle a hero image upload
		if(($upload_type == "hero_image") || ($upload_type == "documents") || ($upload_type == "gallery_image"))
		{
            // Load the state in question
            $australia = $this->australia_model->get_details();
            if(!$australia)
            {
				die ('{error: "Invalid australia"}');	
            }
            
			// Determine the path for where to store the original image and the image set
           $path = ABSOLUTE_PATH . AUSTRALIA_FILES_FOLDER . $australia_id . "/";
           
			if ( !file_exists($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
			}

            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . AUSTRALIA_FILES_FOLDER . $australia_id . "/documents/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            } elseif ($upload_type == 'gallery_image') {
                $path = ABSOLUTE_PATH . AUSTRALIA_FILES_FOLDER . $australia_id . "/images/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            }
            
         	// Hero Image Upload
         	$result = $this->qqfileuploader->handleUpload($path, $filename, true);
         	
         	if($filename == "")
         	{
				$filename = $this->qqfileuploader->file->getName();
				
         		if($filename == "")
         		{
         			die ('{error: "Could not determine file name"}');
				} 				
         	}
         	
         	$file_path =  $path . $filename;
         	if(!file_exists($file_path))
         	{
				die ('{error: "File did not upload correctly"}');	
         	}
         	
			// Move the temporary file to the final path.
  			chmod($file_path, 0666);
  			$return_path = '';
  			
  			if($upload_type == "documents") {
  			    
				$doc_name = str_replace('+',' ',$doc_name);
				
                // Save the document into the documents table in the database.
				$doc_data =  array(
					"document_type" => "australia_document",
					"foreign_id" => $australia_id,
					"document_name" => $doc_name,
					"document_path" => AUSTRALIA_FILES_FOLDER . $australia_id . "/documents/" . $filename
				);
				
                $return_path = AUSTRALIA_FILES_FOLDER . $australia_id . "/documents/" . $filename;
				$this->document_model->save($doc_id, $doc_data, $australia_id, "australia_document", $use_order = TRUE);				
				
			} elseif ($upload_type == 'hero_image') {
			    
			    $australia_folder = FCPATH.AUSTRALIA_FILES_FOLDER.$australia_id."/";
            	$thumb_path = $australia_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($australia_folder.$filename, $thumb_path, $error_message,THUMB_AUSTRALIA_WIDTH,THUMB_AUSTRALIA_HEIGHT);
			    
			    // Update the article with the hero name.
	        	$update_data = array("australia_hero_image" => AUSTRALIA_FILES_FOLDER . $australia_id. "/" . $filename);
				$this->australia_model->save($australia_id, $update_data);
				
				$return_path = site_url(AUSTRALIA_FILES_FOLDER . $australia_id. "/" . $filename);
				
			}  elseif ($upload_type == "gallery_image") {

  			    $property_folder = FCPATH.AUSTRALIA_FILES_FOLDER.$australia_id."/images/";
            	$thumb_path = $property_folder . $filename . "_thumb.jpg";
            	
            	//resize
			    $this->image->create_thumbnail($property_folder.$filename, $thumb_path, $error_message,THUMB_AUSTRALIA_WIDTH,THUMB_AUSTRALIA_HEIGHT);
  			    
                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "australia_gallery",
					"foreign_id" => $australia_id,
					"document_name" => $filename,
					"document_path" => AUSTRALIA_FILES_FOLDER . $australia_id . "/images/" . $filename
				);
                
				$return_path = AUSTRALIA_FILES_FOLDER . $australia_id . "/images/" . $filename;
				
				$this->document_model->save("", $img_data, $australia_id, "australia_gallery", $use_order = TRUE);				
			}

			$return = array();
			$return["status"] = "OK";
			$return["fileName"] = $return_path;
			$return["success"] = true;	
			
			echo json_encode($return);	
		}
		else
		{
			die ('{error: "Invalid file type"}');
		}	
    }
    
    function _refresh_files($australia_id)
    {
        //get files

        $files = $this->document_model->get_list("australia_gallery", $australia_id); 
        $count_all = count($files);

        //load view 
        $this->load->view('admin/australia/file_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));        
    }
	
}
?>	