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

class Statemanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;  
    private $images_records_per_page = 3;  
    private $doc_type = "state_document";
    
    function __construct()
    {
        parent::__construct();

        // Create the data array.
        $this->data = array();            
        
        // Load models etc        
        $this->load->model("state_model");
        $this->load->model("property_model");
        $this->load->model("document_model");
        $this->load->model('state_meta_model');
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
        $this->data["meta_title"] = "State Manager";
        $this->data["page_heading"] = "State Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data["states"] = $this->state_model->get_list(-1,$this->records_per_page,1,$count_all);

		$this->data["pages_no"] = $count_all / $this->records_per_page;
		
		//Actual $this->data["states"] = $this->db->order_by("name ASC")->where("country_id", 1)->get("states");
        $this->data["projects"] = $this->db->order_by("project_name ASC")->get("projects");	
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/statemanager/prebody', $this->data); 
        $this->load->view('admin/statemanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function state($state_id='')
    {
        $this->data['message'] = "";
        $postback = $this->tools_model->isPost();    
        
        if ($postback) {
            $this->_handlePost($state_id);
        }
        
        if($state_id != "") { //edit
            // Load state details
            $state = $this->state_model->get_details($state_id);
				
            if(!$state) {
                // The state could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the state could not be loaded.", "State/show - the state with an id of '$state_id' could not be loaded");
                return;            
            } else {
                //pass state details
                $this->data["state"] = $state; 
                $this->data["documents"] = $this->document_model->get_list($this->doc_type, $state_id);
                $this->data["metas"] = $this->state_meta_model->get_list(array('state_id'=>$state_id));
                $this->data["links"] = $this->link_model->get_list('state_link', $state_id);
                $this->data['comments'] = $this->commentmd->get_list(array('foreign_id'=>$state_id, 'type' => "state_comment"));
                $this->data["images"] = $this->document_model->get_list("state_gallery", $state_id); 
                $this->data["images_records_per_page"] = $this->images_records_per_page;
            }
        }
        
        if(!$postback)    
            $this->data['message'] = ($state_id == "") ? "To create a new state, enter the state details below." : "You are editing the &lsquo;<b>$state->state_name</b>&rsquo;";
            
        // Define page variables
        $this->data["meta_keywords"] = "state Manager";
        $this->data["meta_description"] = "state Manager";
        $this->data["meta_title"] = "Website Administration Menu";
        $this->data["page_heading"] = ($state_id != "" && isset($state)) ? $state->state_name : "State Details";         
        
        $this->data['state_id'] = $state_id;
        $this->data["states"] = $this->property_model->get_states(1);//actual states
        
        if($state_id != "") { //edit
            if(!is_dir(FCPATH.AREA_FILES_FOLDER)) //FCPATH
                @mkdir(FCPATH.AREA_FILES_FOLDER ,DIR_WRITE_MODE);   
             
            if(!is_dir(FCPATH.AREA_FILES_FOLDER.$state_id))
                @mkdir(FCPATH.AREA_FILES_FOLDER.$state_id,DIR_WRITE_MODE);
                
            if(!is_dir(FCPATH.AREA_FILES_FOLDER.$state_id."/documents"))
                @mkdir(FCPATH.AREA_FILES_FOLDER.$state_id."/documents",DIR_WRITE_MODE);       
        }
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/state/prebody.php', $this->data); 
        $this->load->view('admin/state/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);
    }
    
    function _handlePost($state_id)
    {
        $data = array(    
                        "state_name"                     => '',
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
                        "region_id"                     => '',
                   );
                    
        $required_fields = array("state_name");
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
        $data['overview'] = $this->input->post('overview');//Fixing left alignment is incorrect
        
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
               	
               	$this->document_model->save($fileid, $doc_data, $state_id);
    		}
    		
    	}
    	
        if ($missing_fields) {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "State Manager/HandlerPost update - the state with an id of '$state_id' could not be saved");
            return;
        }
        
        $edit_state = false;
        
        if(is_numeric($state_id)) {
            $edit_state = true;
        }
        
        $data["last_modified"] = date("Y-m-d H:i:s");
        $data["last_modified_by"] = $this->login_model->getSessionData("id");        
        
        //depeding on the $state_id do the update or insert
        $state_id = $this->state_model->save($state_id,$data);

        if(!$state_id) {
           // Something went wrong whilst saving the user data.
           $this->error_model->report_error("Sorry, the state could not be saved/updated.", "State Manager/state save");
           return;
        }
        
        if(!$edit_state) {
            $a = $this->document_model->add_default_documents($this->doc_type, $state_id);
        } else {
            //save documents
            $documents = $this->document_model->get_list($this->doc_type,$state_id);            
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
                   $this->document_model->save($doc->id,$doc_data, $state_id);
                }
            }			
        }
        redirect("/admin/statemanager/state/$state_id");
    }
    
    function ajaxwork()
    {
       $type = intval($this->tools_model->get_value("type",0,"post",0,false));
       $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
       
        switch($type)
        {
            
            case 1: // Delete states
            
                // Get state ids separated with ";"
                $state_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($state_ids!="") {
                    $arr_ids = explode(";",$state_ids);
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id)) {
                            if ($where_in != "") $where_in.=",";
                            $where_in .= $id;
                        }
                    }
                    
                    if ($where_in!="") {
                        $this->state_model->delete($where_in);
                    }                                        
                }
                
                // Get list of states                       
                $states = $this->state_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                // Load view 
                $this->load->view('admin/statemanager/state_listing',array('states'=>$states,'pages_no' => $count_all / $this->records_per_page));
                
                
            break;
            
            case 2: // Page number changed
                
                // Get list of states                       
                $states = $this->state_model->get_list(-1,$this->records_per_page,$current_page,$count_all);
                
                // Load view 
                $this->load->view('admin/statemanager/state_listing',array('states'=>$states,'pages_no' => $count_all / $this->records_per_page));                
                
            break;
            
            case 3: // Search for a state
               
                
				$state_id = $this->input->post('state_id'); //actual state
				$project_id = $this->input->post('project_id');
				
				$filters['state_id']= $state_id;
				$filters['project_id']= $project_id;
				
				
				$search_terms = $this->tools_model->get_value("tosearch","","post",0,false);
                
                if ($this->input->post("sort_col") && $this->input->post("sort_dir"))
                {
                	$order_by = $this->input->post("sort_col") . " " . $this->input->post("sort_dir");	
                }
                else
                {
                	$order_by = 'nc_state.state_name ASC';
                }
                
                $current_page = 1;
                //get list of states
                $states = $this->state_model->get_list(-1,$this->records_per_page,$current_page,$count_all,$search_terms,$order_by,$builder_id = "",$filters);
                //load view 
                $this->load->view('admin/statemanager/state_listing',array('states'=>$states,'pages_no' => $count_all / $this->records_per_page));
                
            break; 
            
            case 5: // Delete hero_image
            
                $state_id = $this->tools_model->get_value("state_id","","post",0,false);
                //do we have a valid state_id ?
                if (is_numeric($state_id)) {
                    
                    $state_folder = FCPATH;
                    
                    $state_details = $this->state_model->get_details($state_id);
                    
                    if ($state_details) {
                            $hero_image_name = $state_details->state_hero_image;
                            //delete files
                            if (file_exists($state_folder.$hero_image_name)) unlink($state_folder.$hero_image_name);
                            if (file_exists($state_folder.$hero_image_name . "_thumb.jpg")) unlink($state_folder . $hero_image_name . "_thumb.jpg");
                            $this->state_model->save($state_id,array( "state_hero_image"=> "" ));      
                            die("done");
                    }
                    else
                        die("Error: state id not found");
                }
                else
                        die("Error: Not a valid state id");
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
                $state_id = $this->tools_model->get_value("state_id","","post",0,false);
                
                $this->utilities->add_to_debug("Doc ID: $doc_id, $state_id");
                
                if(($doc_id == "") || ($state_id == "")) {
                	$this->utilities->add_to_debug("statemanager.php - Missing variables in case 12 - delete: $doc_id, $state_id");            
				}
               
                //do we have a valid state_id ?
                if (is_numeric($state_id)) {
                    $doc_data = array(    
                            "document_path"   => ""
                    ); 
                    
                    $this->document_model->save($doc_id,$doc_data,$state_id);
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
                $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : 0;
                $data = array(
                                'name' => $name,
                                'value' => $value,
                                'state_id' => $state_id
                            );
                
                if (!empty($meta_id)) {
                    $this->state_meta_model->save($meta_id,$data);
                } else {
                    $this->state_meta_model->save('',$data);
                }
                
                echo 'OK';
                exit();

            break;
            
            case 10: // Load Meta Data
            
                $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : 0;
                $metas = $this->state_meta_model->get_list(array('state_id'=>$state_id));
                
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
                        $this->state_meta_model->delete($arr_ids);
                        $status = 'OK';
                    }
                }
                echo $status;
                exit();
            break;
            
            case 12: // Load form edit meta data
            
                $meta_id = isset($_POST['meta_id']) ? $_POST['meta_id'] : '';
                $meta = $this->state_meta_model->get_details($meta_id);
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
                $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : 0;
                $data = array(
                                'link_type' => 'state_link',
                                'title' => $title,
                                'url' => $url,
                                'foreign_id' => $state_id
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
                $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : 0;
                $links = $this->link_model->get_list('state_link', $state_id);
                
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
                $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : 0;
                $comment_id = isset($_POST['comment_id']) ? $_POST['comment_id'] : 0;
                $comment = isset($_POST['comment']) ? $_POST['comment'] : '';
                $cms_user = $this->session->userdata("cms_user");
                $data = array(
                                'type' => 'state_comment',
                                'comment' => $comment,
                                'user_id' => $cms_user['id'],
                                'foreign_id' => $state_id,
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
                $state_id = isset($_POST['state_id']) ? $_POST['state_id'] : 0;
                $comments = $this->commentmd->get_list(array('type'=>'state_comment','foreign_id'=>$state_id) );
                
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
                $state_id = intval($this->tools_model->get_value("state_id",0,"post",0,false));
                $this->_refresh_files($state_id);
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
                $state_id = intval($this->tools_model->get_value("state_id",0,"post",0,false));
                $file_names = array();
                $doc_type = "state_gallery";

                if ($files_id!="") {

                    $arr_id_files = explode(";",$files_id);

                    $removed_states = $this->db->where_in('id',$arr_id_files)
                                            ->get('documents');

                    //delete from documents table

                    $this->document_model->delete($arr_id_files, '');

                    //delete images from folders           

                    if($removed_states) {
                        foreach($removed_states->result() as $row) {
                            $file_names[] = $row->document_name;
                            $file_names[] = $row->document_name.'_thumb.jpg';
                        }
                            
                        $this->utilities->remove_file(AREA_FILES_FOLDER.$state_id."/images",$file_names,"");
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
    
    function upload_file($upload_type, $state_id, $doc_id='', $doc_name='', $filename = "")
    {
    	// Load the qq uploader library
		$this->load->library("qqFileUploader");
		
		// Make sure we have a valid file type to save.
		if(($upload_type == "") || (!is_numeric($state_id)))
		{
			die ('{error: "Invalid upload type $upload_type or state id $state_id"}');
		}
		
		// Handle a hero image upload
		if(($upload_type == "hero_image") || ($upload_type == "documents") || ($upload_type == "gallery_image"))
		{
            // Load the state in question
            $state = $this->state_model->get_details($state_id);
            if(!$state)
            {
				die ('{error: "Invalid state"}');	
            }
            
			// Determine the path for where to store the original image and the image set
            $path = ABSOLUTE_PATH . STATE_FILES_FOLDER . $state_id . "/";
            if ( !file_exists($path) ) {
            	@mkdir($path, DIR_WRITE_MODE);
            }
            if ( !is_dir($path) ) {
     			die ('{error: "Permission denied to create directory."}');
            }
            
            if ($upload_type == 'documents') {
            	$path = ABSOLUTE_PATH . STATE_FILES_FOLDER . $state_id . "/documents/";
                if ( !is_dir($path) ) {
                	@mkdir($path, DIR_WRITE_MODE);
                }
                if ( !is_dir($path) ) {
         			die ('{error: "Permission denied to create directory."}');
                }
            } elseif ($upload_type == 'gallery_image') {
                $path = ABSOLUTE_PATH . STATE_FILES_FOLDER . $state_id . "/images/";
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
					"document_type" => "state_document",
					"foreign_id" => $state_id,
					"document_name" => $doc_name,
					"document_path" => STATE_FILES_FOLDER . $state_id . "/documents/" . $filename
				);
				
                $return_path = STATE_FILES_FOLDER . $state_id . "/documents/" . $filename;
				$this->document_model->save($doc_id, $doc_data, $state_id, "state_document", $use_order = TRUE);				
				
			} elseif ($upload_type == 'hero_image') {
			    
			    $state_folder = FCPATH.STATE_FILES_FOLDER.$state_id."/";
            	$thumb_path = $state_folder . $filename . "_thumb.jpg";
			    $this->image->create_thumbnail($state_folder.$filename, $thumb_path, $error_message,THUMB_AREA_WIDTH,THUMB_AREA_HEIGHT);
			    
			    // Update the article with the hero name.
	        	$update_data = array("state_hero_image" => STATE_FILES_FOLDER . $state_id. "/" . $filename);
				$this->state_model->save($state_id, $update_data);
				
				$return_path = site_url(STATE_FILES_FOLDER . $state_id. "/" . $filename);
				
			}  elseif ($upload_type == "gallery_image") {

  			    $property_folder = FCPATH.STATE_FILES_FOLDER.$state_id."/images/";
            	$thumb_path = $property_folder . $filename . "_thumb.jpg";
            	
            	//resize
			    $this->image->create_thumbnail($property_folder.$filename, $thumb_path, $error_message,THUMB_AREA_WIDTH,THUMB_AREA_HEIGHT);
  			    
                // Save the gallery image into the documents table in the database.
				$img_data =  array(
					"document_type" => "state_gallery",
					"foreign_id" => $state_id,
					"document_name" => $filename,
					"document_path" => STATE_FILES_FOLDER . $state_id . "/images/" . $filename
				);
                
				$return_path = STATE_FILES_FOLDER . $state_id . "/images/" . $filename;
				
				$this->document_model->save("", $img_data, $state_id, "state_gallery", $use_order = TRUE);				
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
    
    function _refresh_files($state_id)
    {
        //get files

        $files = $this->document_model->get_list("state_gallery", $state_id); 
        $count_all = count($files);

        //load view 
        $this->load->view('admin/state/file_listing',array('files'=>$files,'pages_no' => $count_all / $this->records_per_page));        
    }
}