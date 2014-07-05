<?php
class Menumanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    
    function __construct()
    {
        parent::__construct();
                        
        // Create the data array.
        $this->data = array();            
        
        // Load models
        $this->load->model("menu_model"); 

        //load helper
        $this->load->helper('form');
        $this->load->library("utilities");                
        
        // Check for a valid session
        if (!$this->login_model->getSessionData("logged_in"))
           redirect("login");
           
        // Ensure that the menu cache entries are removed when any menu modification is made
        cache_delete("MAINMENU");       
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Menu Manager";
        $this->data["page_heading"] = "Menu Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        $this->data['message'] = "";
        $this->data["menus"] = $this->menu_model->get_menus();
        $this->data["selected_menu"] = "";
                
        if( $this->data["menus"] && count($this->data["menus"])>0)
        {
            $menu_result = $this->data["menus"]->result();
            $this->data["selected_menu"] = $menu_result[0]->menu_id;            
        }
        
        $this->data["menu_items"] = $this->menu_model->get_list($this->data["selected_menu"],-1,FALSE);
        $this->data["websites"] = $this->website_model->get_list(array());
                        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/menumanager/prebody', $this->data); 
        $this->load->view('admin/menumanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        
        
        switch($type)
        {
            case 1: //delete selected item menu
                //get item ids separated with ";"
                $menu_items_id = $this->tools_model->get_value("todelete","","post",0,false);
    
                
                if ($menu_items_id!="")
                {
                    $arr_ids = explode(";",$menu_items_id);
                    
                    $where_in = "";
                    
                    foreach($arr_ids as $id)
                    {
                        if (is_numeric($id))
                        {
                            if ($where_in != "") $where_in.=",";
                            
                            $where_in .= $id;
                        }
                    }
                    
                    if ($where_in!="")
                    {
                        $this->menu_model->delete_menu_items($where_in);
                        echo "ok";
                    }
                }
                                
                break;    
            case 3: //add new menu
                
                $return_data = array();
                        
                $menu_name = $this->tools_model->get_value("menu_name",0,"post",0,false);
                $website_id = $this->tools_model->get_value("website_id",'',"post",0,false);
                
                //menu name exists ?
                $exists = $this->menu_model->menu_exists($menu_name,true, $website_id);
                
                if (!$exists)
                {
                    //create menu
                    $this->menu_model->add_menu($menu_name, $website_id);
                    
                    $return_data["message"] = "Menu created";
                }
                else
                {
                    $return_data["message"] = "Menu already exists";
                }    


                //refresh menulist                             
                $return_data["html"] = $this->get_menulist();
                
                echo json_encode($return_data);
            
            break;
            
            case 4: //delete menu
                
                $return_data = array();
                        
                $menu_id = $this->tools_model->get_value("menu_id",0,"post",0,false);
                $website_id = $this->tools_model->get_value("website_id",'',"post",0,false);
                
                //menu name exits ?
                $exists = $this->menu_model->menu_exists($menu_id,false, $website_id);
                
                if (!$exists)
                {
                    $return_data["message"] = "Menu not found";
                }
                else
                {
                    $this->menu_model->remove_menu($menu_id);
                    $return_data["message"] = "Menu deleted";
                }    

                //refresh menulist                            
                $return_data["html"] = $this->get_menulist();
                
                echo json_encode($return_data);
            
            break;
            
            case 5: //refresh menu list
                $return_data["html"] = $this->get_menulist();
                
                echo json_encode($return_data);
            break;
            case 6: //refresh menu item list
                $menu_id = $this->tools_model->get_value("menu_id",0,"post",0,false);
                
                $menu_items = $this->menu_model->get_list($menu_id,-1,FALSE);
                $return_data["html"] = $this->load->view('admin/menumanager/menuitem_listing',array('menu_items'=>$menu_items),true); 
                
                echo json_encode($return_data);
            break;
            
            case 7: //get main content of the menu manager page, used for add new menu item
                $menu_id = $this->tools_model->get_value("menu_id",0,"post",0,false);
                $menu_item_id = $this->tools_model->get_value("menu_item_id",0,"post",0,false);
                
                $this->data["menu_item_name"] = $this->tools_model->get_value("menu_item_name",0,"post",0,false);
                $this->data["menu_item_id"] = $menu_item_id;
                
                if($menu_item_id >0)
                {
                    $this->data["menu_item"] = $this->menu_model->get_details("", $menu_item_id);
                    
                    if($this->data["menu_item"])
                    {
                        $link_to = $this->tools_model->get_tables($this->data["menu_item"]->link_type);
                        if($link_to)
                            $this->data["link_to"] = $this->utilities->print_select_options($link_to, "id", "name", $this->data["menu_item"]->link_to ,"Choose");
                        else
                            $this->data["link_to"] = false;    
                    }
                    
                }
                                
                //get parents page
                $this->data["parents"] = $this->menu_model->get_list($menu_id,"",FALSE);
                
                //get category articles
                $this->data["category_articles"] = $this->article_model->get_article_categories($enabled = 1);
                
                $this->data["link_type"] = array(
                    ""         => "Choose",
                    "articles" => "Articles",
                    "external"    => "External Link"
                );
                
                $html = $this->load->view("admin/menumanager/menuitem",$this->data,true);
                
                $return_data = array();
                $return_data["html"] = $html;
                
                echo json_encode($return_data);
            break;
            case 8:    //save menu item
                $menu_item_id = $this->tools_model->get_value("menu_item_id",0,"post",0,false);                
                
                $data = array(
                    "menu_item_name"    => "",
                    "menu_id"           => "",
                    "parent_id"         => "-1",
                    "class"             => "",
                    "menu_order"        => "0",
                    "enabled"           => "0",
                    "category_id"       => "-1",
                    "link_type"         => "",
                    "link_to"           => "",
                	"link"				=> ""
                );
                
                $required_fields = array("menu_item_name","link_type");
                $missing_fields = false;
                
                //fill in data array from post values
                foreach($data as $key=>$value)
                {
                	if($key == "link" )
                	{
                		$tmp_code = '';
                		$link_type_tmp = $this->tools_model->get_value("link_type","","post",0,true);

                		if($link_type_tmp == "articles")
                		{
							$id = $this->tools_model->get_value("link_to","","post",0,true);
                			$tmp = $this->article_model->get_details($id,true);
                			if($tmp)
                				$tmp_code = $tmp->article_code;
                				
                			$data[$key] = $tmp_code;
                		}
                		else if($link_type_tmp == "external")
                		{
							$data["link"] = $this->tools_model->get_value("link", "", "post", 0, true);
							if($data["link"] == "")
							{
								die("Please enter the link destination");
							}
                		}
						else
							die("Unhandled Link Type");
                	}
                    else
                    {
                    	$data[$key] = $this->tools_model->get_value($key,"","post",0,true);	
					}
                    
                    
                    
                    // Ensure that all required fields are present    
                    if(in_array($key,$required_fields) && ($data[$key] == "" || $data[$key] == -1))
                    {
                        $missing_fields = true;
                        break;
                    }
                }
                if ($missing_fields)
                {
                	echo "Please fill in all required fields to continue";
                    //$this->error_model->report_error("Sorry, please fill in all required fields to continue.", "MenuManager/Ajaxworker update - the menu item with an id of '$menu_item_id' could not be saved");
                    return;
                }
         
                //depeding on the $property_id do the update or insert
                $menu_item_id = $this->menu_model->save($menu_item_id,$data);
                if(!$menu_item_id)
                {
                   // Something went wrong whilst saving the user data.
                   $this->error_model->report_error("Sorry, the Menu Item could not be saved/updated.", "MenuManager/Menu Item save");
                   return;
                }
            
            break;
            
            case 9: //expand -> get child items for parent id
            
                $menu_item_id = intval($this->tools_model->get_value("menu_item_id",0,"post",0,false));                
                $menu_items_query = $this->menu_model->get_list("",$menu_item_id,FALSE);
                
                $level = intval($this->tools_model->get_value("level",0,"post",0,false)); 
                
                $level++;               
                
                $child_rows =  $this->load->view('admin/menumanager/menuitem_listing',array('menu_items'=>$menu_items_query,"expand"=>"1","level"=>$level),true);
                
                $return_array = array();
                
                $return_array['html'] = $child_rows;
                echo json_encode($return_array);
            
            break;
            
            case 10:
                 $menu_id = $this->tools_model->get_value("menu_id",0,"post",0,false);
                 
                 $menu_result = $this->menu_model->get_menus($menu_id);
                 if($menu_result)
                 {
                     $menu = $menu_result->row();
                    
                     $name = $menu->name."-Copy";
                     $website_id = $menu->website_id;
                     //add new menu
                     $new_menu_id = $this->menu_model->add_menu($name, $website_id);
                     echo $new_menu_id;
                     
                     //clone menu items
                     $this->menu_model->clone_menu_items($menu_id, $new_menu_id);                     
                     
                 }
            break;
            
            case 11: //change menu item detail
                $link_type = $this->tools_model->get_value("link_type",0,"post",0,false); 
                
                $link_to = $this->tools_model->get_tables($link_type);
                $html = "";
                
                if($link_to)
                {
                    $html = $this->utilities->print_select_options($link_to, "id", "name", "" ,"Choose");
                }
                
                $return_array = array();
                $return_array['html'] = $html;
                
                echo json_encode($return_array);                
                
            break;    
        }
    }
    
    /*return options HTML */
    function get_menulist()     
    {
        $menus = $this->menu_model->get_menus();
        $selected_menu = "";

        if($menus && count($menus)>0)
        {
            $menu_result = $menus->result();
            $selected_menu = $menu_result[0]->menu_id;            
        }
        
        $options_html = $this->utilities->print_select_options($menus,"menu_id","name",$selected_menu);
        
        return $options_html;
    }   
    
}