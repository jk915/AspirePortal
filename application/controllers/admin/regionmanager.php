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

class Regionmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;  
    private $images_records_per_page = 3;  
    private $doc_type = "region_document";
    
    function __construct()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("region_model");
		$this->load->model("state_model");
        $this->load->model("property_model");
        $this->load->model("document_model");
        $this->load->model('region_meta_model');
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
        $this->data["meta_title"] = "Region Manager";
        $this->data["page_heading"] = "Region Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data["regions"] = $this->region_model->get_list(-1,$this->records_per_page,1,$count_all);
		$this->data["states"] = $this->property_model->get_states(1);	
		$this->data["pages_no"] = $count_all / $this->records_per_page;
		
		//Actual $this->data["regions"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("regions");
        $this->data["projects"] = $this->db->order_by("project_name ASC")->get("projects");	
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/regionmanager/prebody', $this->data); 
        $this->load->view('admin/regionmanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function region($region_id='')
    {
        $this->data['message'] = "";
        $postback = $this->tools_model->isPost();    
        
        if ($postback) {
            $this->_handlePost($region_id);
        }
        
        if($region_id != "") { //edit
            // Load region details
            $region = $this->region_model->get_details($region_id);
            if(!$region) {
                // The region could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the region could not be loaded.", "region/show - the region with an id of '$region_id' could not be loaded");
                return;            
            } else {
                //pass region details
                $this->data["region"] = $region; 
                $this->data["documents"] = $this->document_model->get_list($this->doc_type, $region_id);
                $this->data["metas"] = $this->region_meta_model->get_list(array('region_id'=>$region_id));
                $this->data["links"] = $this->link_model->get_list('region_link', $region_id);
                $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$region_id, 'type' => "region_comment"));
                $this->data["images"] = $this->document_model->get_list("region_gallery", $region_id); 
                $this->data["images_records_per_page"] = $this->images_records_per_page;
            }
        }
        
        if(!$postback)    
            $this->data['message'] = ($region_id == "") ? "To create a new region, enter the region details below." : "You are editing the &lsquo;<b>$region->region_name</b>&rsquo;";
            
        // Define page variables
        $this->data["meta_keywords"] = "region Manager";
        $this->data["meta_description"] = "region Manager";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data["page_heading"] = ($region_id != "" && isset($region)) ? $region->region_name : "region Details";         
        
        $this->data['region_id'] = $region_id;
        $this->data["states"] = $this->property_model->get_states(1);
        
        if($region_id != "") { //edit
            if(!is_dir(FCPATH.REGION_FILES_FOLDER)) //FCPATH
                @mkdir(FCPATH.REGION_FILES_FOLDER ,DIR_WRITE_MODE);   
             
            if(!is_dir(FCPATH.REGION_FILES_FOLDER.$region_id))
                @mkdir(FCPATH.REGION_FILES_FOLDER.$region_id,DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH.REGION_FILES_FOLDER.$region_id."/documents"))
                @mkdir(FCPATH.REGION_FILES_FOLDER.$region_id."/documents",DIR_WRITE_MODE);       
        }
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/region/prebody.php', $this->data); 
        $this->load->view('admin/region/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function _handlePost($region_id)
    {
        $data = array(    
                        "region_name"                   => '',
                        "short_description"             => '',
                        "median_house_price"            => '',
                        "median_unit_price"             => '',
                        "quarterly_growth"              => '',
                        "month12_growth"                => '',
                        "year3_growth"                  => '',
                        "year4_growth"                  => '',
                        "median_growth_this_year"         => '',
                        "weekly_median_advertised_rent" => '',
                        "total_population"              => '',
                        "median_age"                    => '',
                        "number_private_dwellings"      => '',
                        "weekly_median_household_income"=> '',
                        "closest_cbd"=> '',
                        "approx_time_cbd"=> '',
                        "approx_distance_cbd"=> '',
                        "overview"                      => '',
                        "googlemap"                     => '',
                        "enabled"                       => '0',
                        "state_id"                     => '',
                   );
                    
        $required_fields = array("region_name");
        $missing_fields = false;
        
        //fill in data array from post values
        foreach($data as $key=>$value)
        {
            $data[$key] = $this->tools_model->get_value($key,$data[$key],"post",0,true);
            // Ensure that all required fields are present    
            if(in_array($key,$required_fields) && $data[$key] == "") {
                $missing_fields = true;
                break;
            }
        }
        
        // Update Gallery Document Description
    	$document_description = $this->input->post('document_description');
    	$fileids = $this->input->post('fileid');
    	
    	if ($fileids && sizeof($fileids))
    	{
    		foreach ($fileids AS $index=>$fileid)
    		{
    			$doc_data = array(
                    "document_description" => $document_description[$index]
               	);
               	
               	$this->document_model->save($fileid, $doc_data, $region_id);
    		}
    		
    	}
    	
        if ($missing_fields) {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "region Manager/HandlerPost update - the region with an id of '$region_id' could not be saved");
            return;
        }
        
        $edit_region = false;
        
        if(is_numeric($region_id)) {
            $edit_region = true;
        }
        
        $data["last_modified"] = date("Y-m-d H:i:s");
        $data["last_modified_by"] = $this->login_model->getSessionData("id");        
        
        //depeding on the $region_id do the update or insert
        $region_id = $this->region_model->save($region_id,$data);

        if(!$region_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the region could not be saved/updated.", "region Manager/region save");
           return;
        }
        
        if(!$edit_region) {
            $a = $this->document_model->add_default_documents($this->doc_type, $region_id);
        } else {
            //save documents
            $documents = $this->document_model->get_list($this->doc_type,$region_id);            
            if($documents)
            {
                foreach($documents->result() as $doc)
                {
                   $doc_name = $this->tools_model->get_value("doc_".$doc->id."_name","","post",0,false); 
				   $extra_data = $this->tools_model->get_value("doc_".$doc->id."_extra_data","","post",0,false); 
                  
                   $doc_data = array(
                        "document_name" => $doc_name,
                        "extra_data" => $extra_data,
                   );
                   $this->document_model->save($doc->id,$doc_data, $region_id);
                }
            }			
        }
        redirect("/admin/regionmanager/region/$region_id");
    }
    
    function ajaxwork()
    {
       $type = intval($this->tools_model->get_value("type",0,"post",0,false));
       $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
       
        switch($type)
        {
            
            case 1: // Delete regions
            
                // Get region ids separated with ";"
                $region_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($region_ids!="") {
                    $arr_ids = explode(";",$region_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id)) {
                            if ($where_in != "") $where_in.=",";
                            $where_in .= $id;
                        }
                    }
                    
                    if ($where_in!="") {
                        $this->region_model->delete($where_in);
                    }                                        
                }
                
                // Get list of regions                       
                $regions = $this->region_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                // Load view 
                $this->load->view('admin/regionmanager/region_listing',array('regions'=>$regions,'pages_no' => $count_all / $this->records_per_page));
                
                
            break;
            
            case 2: // Page number changed
                
                // Get list of regions                       
                $regions = $this->region_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                // Load view 
                $this->load->view('admin/regionmanager/region_listing',array('regions'=>$regions,'pages_no' => $count_all / $this->records_per_page));                
                
            break;
            
            case 3: // Search for a region
               
                
				$region_id = $this->input->post('region_id'); //actual region
				$state_id = $this->input->post('state_id');
				
				$filters['region_id']= $region_id;
				$filters['state_id']= $state_id;
				
				
				//$search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
				$search_terms = $state_id;
                
                if ($this->input->post("sort_col") && $this->input->post("sort_dir"))
                {
                	$order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");	
                }
                else
                {
                	$order_by = 'nc_regions.region_name ASC';
                }
                
                $current_page = 1;
                //get list of regions
                $regions = $this->region_model->get_list(-1,$this->records_per_page,$current_page,$count_all,$search_terms,$order_by,$builder_id = "",$filters);
               				
				//load view 
                $this->load->view('admin/regionmanager/region_listing',array('regions'=>$regions,'pages_no' => $count_all / $this->records_per_page));
                
            break; 
            
            case 5: // Delete hero_image
            
                $region_id = $this->tools_model->get_value("region_id","","post",0,false);
                //do we have a valid region_id ?
                if (is_numeric($region_id)) {
                    
                    $region_folder = FCPATH;
                    
                    $region_details = $this->region_model->get_details($region_id);
                    
                    if ($region_details) {
                            $hero_image_name = $region_details->region_hero_image;
                            //delete files
                            if (file_exists($region_folder.$hero_image_name)) unlink($region_folder.$hero_image_name);
                            if (file_exists($region_folder.$hero_image_name . "_thumb.jpg")) unlink($region_folder . $hero_image_name . "_thumb.jpg");
                            $this->region_model->save($region_id,array( "region_hero_image"=> "" ));      
                            die("done");
                    }
                    else
                        die("Error: region id not found");
                }
                else
                        die("Error: Not a valid region id");
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
                $region_id = isset($_POST['region_id']) ? $_POST['region_id'] : 0;
                $icon_image = isset($_POST['icon_image']) ? $_POST['icon_image'] : 0;
                $data = array(
                                'name' => $name,
                                'value' => $value,
                                'region_id' => $region_id,
                                'icon_path' => $icon_image
                            );
                
                if (!empty($meta_id)) {
                    $this->region_meta_model->save($meta_id,$data);
                } else {
                    $this->region_meta_model->save('',$data);
                }
                
                echo 'OK';
                exit();

            break;
            
            case 10: // Load Meta Data
            
                $region_id = isset($_POST['region_id']) ? $_POST['region_id'] : 0;
                $metas = $this->region_meta_model->get_list(array('region_id'=>$region_id));
                
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
                        $this->region_meta_model->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;
            
            case 12: // Load form edit meta data
            
                $meta_id = isset($_POST['meta_id']) ? $_POST['meta_id'] : '';
                $meta = $this->region_meta_model->get_details($meta_id);
                if ($meta) {
                    $return_data = array(
                	                       'status' => 'OK',
                	                       'name' => $meta->name,
                	                       'value' => $meta->value,
                	                       'meta_id' => $meta->id,
                	                       'icon_path' => $meta->icon_path
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
                        $status = 'OK';
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
                $region_id = isset($_POST['region_id']) ? $_POST['region_id'] : 0;
                $data = array(
                                'link_type' => 'region_link',
                                'title' => $title,
                                'url' => $url,
                                'foreign_id' => $region_id
                            );
                
                if (!empty($link_id)) {
                    $this->link_model->save($link_id,$data);
                } else {
                    $this->link_model->save('',$data);
                }
                
                echo 'OK';
                exit();
            break;
            
            case 15:
                // Load Links
                $region_id = isset($_POST['region_id']) ? $_POST['region_id'] : 0;
                $links = $this->link_model->get_list('region_link', $region_id);
                
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
                echo $status;
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
                $region_id = isset($_POST['region_id']) ? $_POST['region_id'] : 0;
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $data = array(
                                'type' => 'region_comment',
                                'comment' => $comment,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $region_id,
                                'datetime_added' => date('Y-m-d H:i:s')
                            );
                
                if (!empty($comment_id)) {
                    $this->commentmd->save($comment_id,$data);
                } else {
                    $this->commentmd->save('',$data);
                }
                
                echo 'OK';
                exit();
                
            break;
            
            case 19:
                // Load Comments
                $region_id = isset($_POST['region_id']) ? $_POST['region_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'region_comment','foreign_id'=>$region_id) );
                
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
    
    function upload_file($upload_type, $region_id, $doc_id='', $doc_name='', $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($region_id)))
		{
			die ('{error: "Invalid upload type $upload_type or region id $region_id"}');
		}
		
		// Handle a hero image upload
		if(($upload_type == "hero_image") || ($upload_type == "documents") || ($upload_type == "gallery_image"))
		{
            // Load the region in question
            $region = $this->region_model->get_details($region_id);
            if(!$region)
            {
				die ('{error: "Invalid region"}');	
            }
            
			// Determine the path for where to store the original image and the image set
			$path = ABSOLUTE_PATH . REGION_FILES_FOLDER . $region_id . "/";
				
            if ( !file_exists($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . REGION_FILES_FOLDER . $region_id . "/documents/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            } elseif ($upload_type == 'gallery_image') {
                $path = ABSOLUTE_PATH . REGION_FILES_FOLDER . $region_id . "/images/";
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
					"document_type" => "region_document",
					"foreign_id" => $region_id,
					"document_name" => $doc_name,
					"document_path" => REGION_FILES_FOLDER . $region_id . "/documents/" . $filename
				);
				
                $return_path = REGION_FILES_FOLDER . $region_id . "/documents/" . $filename;
				$this->document_model->save($doc_id, $doc_data, $region_id, "region_document", $use_order = TRUE);				
				
			} elseif ($upload_type == 'hero_image') {
			    
			    $region_folder = FCPATH.REGION_FILES_FOLDER.$region_id."/";
            	$thumb_path = $region_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($region_folder.$filename, $thumb_path, $error_message,THUMB_REGION_WIDTH,THUMB_REGION_HEIGHT);
			    
			    // Update the article with the hero name.
	        	$update_data = array("region_hero_image" => REGION_FILES_FOLDER . $region_id. "/" . $filename);
				$this->region_model->save($region_id, $update_data);
				
				$return_path = site_url(REGION_FILES_FOLDER . $region_id. "/" . $filename);
				
			}  elseif ($upload_type == "gallery_image") {

  			    $property_folder = FCPATH.REGION_FILES_FOLDER.$region_id."/images/";
            	$thumb_path = $property_folder . $filename . "_thumb.jpg";
            	
            	//resize
			    $this->image->create_thumbnail($property_folder.$filename, $thumb_path, $error_message,THUMB_AREA_WIDTH,THUMB_AREA_HEIGHT);
  			    
                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "region_gallery",
					"foreign_id" => $region_id,
					"document_name" => $filename,
					"document_path" => REGION_FILES_FOLDER . $region_id . "/images/" . $filename
				);
                
				$return_path = REGION_FILES_FOLDER . $region_id . "/images/" . $filename;
				
				$this->document_model->save("", $img_data, $region_id, "region_gallery", $use_order = TRUE);				
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
    
    function _refresh_files($region_id)
    {
        //get files

        $files = $this->document_model->get_list("region_gallery", $region_id); 
        $count_all = count($files);

        //load view 
        $this->load->view('admin/region/file_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));        
    }
}