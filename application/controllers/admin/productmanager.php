<?php
class Productmanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $images_records_per_page = 3;
    private $records_per_page = 5;//PRODUCTS_PER_PAGE;
    private $parent_product = -1;
    
    function __construct()
    {
       parent::__construct();
       
       // Create the data array.
       $this->data = array();
              
       // Load models etc
       // Check for a valid session
       $this->load->model("product_model");
       $this->load->model("product_category_model");
       $this->load->model("history_model");
       $this->load->model("document_model");
       $this->load->model("broadcast_model");
       $this->load->model("article_category_model");
       
       // If the $ci_session is passed in post, it means the swfupload has made the POST, don't check for login
       $ci_session = $this->tools_model->get_value("ci_session","","post",0,false);
       
       if ($ci_session == "")
       {
            if (!$this->login_model->getSessionData("logged_in"))
                redirect("login");             
       }
       
    }
   
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Product Manager";
        $this->data["page_heading"] = "Product Manager";
        $this->data["name"] = $this->login_model->getSessionData("firstname");
        
        $this->data['categories'] = $this->product_model->get_categories();
        $this->data['category_id'] = -1; //default category


        $categories = $this->product_model->get_categories($this->parent_product);

        //load items from the category
        $products = $this->product_model->get_products($this->parent_product,$this->records_per_page,1,$count_all);
        $this->data["pages_no"] = $count_all / $this->records_per_page;

        $this->data['categories'] = $categories;  
        $this->data['products'] = $products;

        
        //load views         
        $this->load->view('admin/header', $this->data);
        
        $this->load->view('admin/productmanager/prebody.php', $this->data); 
        $this->load->view('admin/productmanager/main.php', $this->data);
        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    /**
    * @method: product
    * @desc: The product method shows a page with the specified product id.
    * If no id is given, it means it is a new product
    * 
    * @param mixed $product_id - The product id of the page to load.
    */
    function product($product_id = "")
    {
       $this->data['product_id'] = $product_id;
       $this->data['broadcast_access_levels_to'] = $this->broadcast_model->get_all_access_levels_to();
       
       if($product_id != "")
       	$this->data['pricings'] = $this->product_model->get_all_pricings($product_id);
       
       if (is_numeric($product_id) && $product_id > 0)
       {
           //check if the product id does not exists
           if(!$this->product_model->product_exists($product_id))
           {
               $this->error_model->report_error("Product not found.", "ProductManager/Product - the product with a code of '".$product_id."' could not be load");
               return;
           }
           //edit: get product details
           $this->_get_product_details();                        
           
           $product_id = $this->data['product_id'];
           if(!is_dir(ABSOLUTE_PATH."product"))
                @mkdir(ABSOLUTE_PATH."product", 0777);
                
           if(!is_dir(ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id))
                @mkdir(ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id, 0777);
                
           if( !is_dir(ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id.'/files') )
           		@mkdir(ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id.'/files', 0777);
       }
       else
       {
           //new product
            $this->data['product_details'] = array();
            $this->data['product_id'] = "";
       }
       
       //define item properties
        $this->_add_items($product_id);
       
        //handle Postback
        $this->_handlePost();
                                
        //load product page
        $this->_load_product_page($product_id);
    }
    
    function add_product($category_id = -1)
    {
        $this->data['product_id'] = ""; 
        
        //new product
        $this->data['product_details'] = array();
        $this->data['selected_category_id'] = $category_id;
        
        //define item properties
        $this->_add_items($this->data['product_id']);
        
        //handle Postback
        $this->_handlePost();
        
        //load product page
        $this->_load_product_page();
    }
    
    function category($category_id = "")
    {
        if (!is_numeric($category_id))
        {
           $this->error_model->report_error("Sorry, the page could not be loaded.", "Category - the category with a id of '$category_id' could not be loaded"); 
        }
        else
        {
            //load sub categories
            $categories = $this->product_model->get_categories($category_id);
            
            //load items from the category
            $products = $this->product_model->get_products($category_id,$this->records_per_page,1,$count_all);
            $this->data["pages_no"] = $count_all / $this->records_per_page;
        
            $this->data['categories'] = $categories;                 
            $this->data['products'] = $products;                         
        }
        
        $this->data['category_id'] = $category_id;
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Product Manager";
        $this->data["page_heading"] = "Product Manager";
        
         //load views         
        $this->load->view('admin/header', $this->data);
        
        $this->load->view('admin/productmanager/prebody.php', $this->data); 
        $this->load->view('admin/productmanager/main.php', $this->data);
        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
        
    }
    
    function export($category_id)
    {
    	$this->load->helper('file');
        $this->load->dbutil();
        
        // Get all relevant products.
        $query = $this->product_model->get_list(1,$category_id, $for_csv = true);    
        
        // Devine CSV params
        $delimiter = ",";
        $newline = "\r\n";
        
        // Build a CSV recordset
        $data = $this->dbutil->csv_from_result($query, $delimiter, $newline); 
        
        // Define where to write the file.
        $file_path = FCPATH . "product/export/products_".time().".csv";
        
        // Write the file to the file system.
        if(!write_file($file_path, $data))
        	show_error("Couldn't open file: $file_path");
        
        // Download the file.
        $this->utilities->download_file($file_path);
    }
        
    function _handlePost()
    {
        $this->data['message'] = ($this->data["product_id"] == "") ? "To create a new product, enter the product details below." : "You are editing the product &lsquo;<b>".$this->data['product_details']['product_name']."</b>&rsquo;";
        $this->data['productName'] = ($this->data["product_id"] != "" && isset($this->data['product_details']['product_name'])) ?  $this->data['product_details']['product_name'] : ""; 
        
        $require_shipping = $this->input->post("require_shipping");
        
        if($require_shipping != 1) //remove required class for item weight, width, height, depth
        {
            $this->data["weight"]->class = array("numeric"); 
            $this->data["height"]->class = array("numeric"); 
            $this->data["width"]->class  = array("numeric"); 
            $this->data["length"]->class  = array("numeric"); 
        }
            
        if (!$this->tools_model->isPost())
        {
            return;
        }
        
        $missing_fields = false;              
        $sql_data = array();
     
        foreach($_POST as $key=>$value)
        {
            if ((isset($this->data[$key])) && ($key != "product_id"))
            {
				//prepeare data for insert/update
				$sql_data[$key] = $value;

				// Ensure that all required fields are present  
				if($this->data[$key]->isRequired() && $this->data[$key]->isEmpty($value) )
				{
					$missing_fields = true;                      
				}
            }
        }
        
        if ($missing_fields)
        {
            $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "ProductManager/HandlerPost update - the product with a code of '".$this->data["product_id"]."' could not be saved");
            return;
        }
        
        $sql_data["active"] = (isset($_POST["active"])) ? 1 : 0;
        $sql_data["show_on_downloads"] = (isset($_POST["show_on_downloads"])) ? 1 : 0;
        $sql_data["require_shipping"] = (isset($_POST["require_shipping"])) ? 1 : 0;
        $sql_data["image_only"] = (isset($_POST["image_only"])) ? 1 : 0;
        
        $sql_data["hidetab_screenshots"] = (isset($_POST["hidetab_screenshots"])) ? 1 : 0;
        $sql_data["hidetab_oem"] = (isset($_POST["hidetab_oem"])) ? 1 : 0;
        $sql_data["hidetab_downloads"] = (isset($_POST["hidetab_downloads"])) ? 1 : 0;
        
        //depeding on the $product_id do the update or insert
        $product_id = $this->product_model->save($this->data["product_id"],$sql_data);
        
        // Load the product record
        $product = $this->product_model->get_details($this->data["product_id"]);
        if(!$product)
        	die("Couldn't load product record");
        	
        if(($product->article_category_id == null) || (($product->article_category_id <= 0)))
        {
	        //add an article with the same code as the model number
	        
	        $category_name	= $_POST['product_name'];
	        $category_code 	= $_POST['model_number'];
	        $website_id 	= $this->session->userdata("website_id");
	        $parent_id 		= DEFAULT_ARTICLE_CATEGORY_PARENT_ID;
	        
	        $cat = $this->article_model->category_exists_bycode($category_code, $website_id, $parent_id);
			if(!$cat)
			{
				if ( $this->data["product_id"] == "" )
        		{
        			// Add the new article category to pair against this product.
					$new_category_id = $this->article_model->add_category($category_name, $category_code, $website_id, $parent_id, $product_id);
					
					// Assign the category to the product.
					$sql_data = array("article_category_id" => $new_category_id);
					$product_id = $this->product_model->save($this->data["product_id"],$sql_data); 
        		}
				else
		        {
	        		$update = array(
	        							'name'			=> $category_name,
	        							'category_code'	=> $category_code
	        						);
	        		$this->article_model->update_category( $product_id, $update );
		        }  				
			}
		}
        
        //save the page content in history 
        $this->history_model->save_history($table = "products", $product_id);
            
        if($this->data["product_id"] == "") //insert 
            redirect(base_url()."productmanager/product/".$product_id);
        
        if($this->data["product_id"]!="")
        {
           //edit: get product details
           $this->_get_product_details();
           
           //define item properties
           $this->_add_items($this->data["product_id"]);
        }

        redirect(base_url()."productmanager/product/".$product_id);
    }
    
    function _get_product_details()
    {
        //edit: get product details
        $product_details_query = $this->product_model->get_details($this->data["product_id"]);
        
        
        if (!$product_details_query)
        {
            $this->error_model->report_error("Sorry, the page could not be loaded.", "Product - the product with a id of '$product_id' could not be loaded");
        }
        else
        {
           $product_details = $product_details_query->row_array(0);
           
           $this->data['product_details'] = $product_details;
           $this->data['category_id'] = $product_details['product_category_id'];
           $this->data['article_cat_id'] = $product_details['article_category_id']; 
        }
        
        if(!is_dir(ABSOLUTE_PATH."product"))
                mkdir(ABSOLUTE_PATH."product" ,0777);   
               
        $this->data["files"] = $this->document_model->get_list("product_image",$this->data["product_id"]);//$this->utilities->get_files(PRODUCT_FILES_FOLDER.$this->data["product_id"],false,false); //product images                                             
        $this->data["files_no"] = ($this->data["files"] != false) ? $this->data["files"]->num_rows() : 0;
        $this->data["pages_no"] = $this->data["files_no"] / $this->images_records_per_page;

        $this->data["gallery_files"] = $this->document_model->get_list("gallery_image",$this->data["product_id"]);//$this->utilities->get_files(PRODUCT_FILES_FOLDER.$this->data["product_id"],false,false); //product images                                             
        $this->data["gallery_files_no"] = ($this->data["gallery_files"] != false) ? $this->data["gallery_files"]->num_rows() : 0;
        $this->data["gallery_pages_no"] = $this->data["gallery_files_no"] / $this->images_records_per_page;
           
        //get download files
        $this->data["download_files"] = $this->document_model->get_list("product_files",$this->data["product_id"]);
        
        //get selected hero image
        $this->data["selected_hero_image"] = $this->product_model->get_hero_image($this->data["product_id"]);                
    }
    
    function _add_items($product_id = "")
    {
    	// Add hidden fields
        $this->data['product_category_id'] = new Item("product_category_id","hidden", (isset($this->data["category_id"])) ? $this->data["category_id"] : "" );
        $this->data['article_category_id'] = new Item("article_category_id","hidden", (isset($this->data["article_category_id"])) ? $this->data["article_category_id"] : "" );
        
        $serial_gen = new Item("serial_gen", "select", "", "Serial generator"); 
        $serial_gen->class = array();
        $this->data['serial_gen'] = $serial_gen;
        
        // General tab
        $product_name = new Item("product_name","text","","Product Name:");
        $product_name->fieldsize = 100;
        $product_name->class = array("required");
        $this->data['product_name'] =$product_name;
        
         // General tab
        $tags = new Item("tags","text","","Tags:");
        $tags->fieldsize = 100;
        $tags->class = array();
        $this->data['tags'] = $tags;
        
        $model_number = new Item("model_number","text","","Model Number:");
        $model_number->fieldsize = 50;
        $model_number->class = array("required");
        $this->data['model_number'] = $model_number;
        
        /*$price = new Item("price","text","","Special Price($):");
        $price->fieldsize = 50;
        $price->class = array("numeric");
        $this->data['price'] = $price;*/
        
        $rrp_price = new Item("rrp_price","text","","RRP Price($):");
        $rrp_price->fieldsize = 50;
        $rrp_price->class = array("numeric");
        $this->data['rrp_price'] = $rrp_price;
        
        $weight = new Item("weight","text","","Item Weight(kg):");
        $weight->fieldsize = 50;
        $weight->class = array("required","numeric");
        $this->data['weight'] = $weight;
        
        $height = new Item("height","text","","Item Height(mm):");
        $height->fieldsize = 50;
        $height->class = array("required","numeric");
        $this->data['height'] = $height;
        
        $width = new Item("width","text","","Item Width(mm):");
        $width->fieldsize = 50;
        $width->class = array("required","numeric");
        $this->data['width'] = $width;
        
        $length = new Item("length","text","","Item depth(mm):");
        $length->fieldsize = 50;
        $length->class = array("required","numeric");
        $this->data['length'] = $length;
        
        $device_id = new Item("device_id","text","","Device ID:");
        $device_id->fieldsize = 255;
        $device_id->class = array();
        $this->data['device_id'] = $device_id;
        
        $this->data['display_sizes'] = ( !empty($this->data['product_details']['require_shipping']) && $this->data['product_details']['require_shipping'] > 0) ? '' : 'style="display:none"';
        
        $this->data['require_shipping'] = new Item("require_shipping","checkbox","1","Requires shipping");
        
        $this->data['hidetab_screenshots'] = new Item("hidetab_screenshots","checkbox","1","Screenshots");
        $this->data['hidetab_oem'] = new Item("hidetab_oem","checkbox","1","OEM");
        $this->data['hidetab_downloads'] = new Item("hidetab_downloads","checkbox","1","Downloads");
        
        //$this->data['image_only'] = new Item("image_only", "checkbox", "0", "Image Only");
        
        $this->data['active'] = new Item("active","checkbox","1","Product is enabled");
        $this->data['show_on_downloads'] = new Item("show_on_downloads", "checkbox", "1", "Show on downloads page");               
        
        //description TAB
        $short_description = new Item("short_description","wysiwyg","","Short Description / Nav Text");
        $short_description->style = "width:600px; height:100px";
        $short_description->parameter1 = "products";
        $short_description->foreign_id = $product_id;
        $short_description->fieldid = "wysiwyg_short_description";
        $this->data["short_description"] = $short_description;
        
        $description = new Item("description","wysiwyg","","Description / Intro Text");
        $description->style = "width:600px; height:300px";
        $description->parameter1 = "products";
        $description->foreign_id = $product_id;
        $description->fieldid = "wysiwyg_description";
        $this->data["description"] = $description;
        
        $oem_description = new Item("oem_description","wysiwyg","","OEM Description:");
        $oem_description->fieldid = "wysiwyg_oem_description";
        $this->data["oem_description"] = $oem_description;
        
        //images TAB
        
        $this->data["hero_image"] = new Item("hero_image","hidden","");
        
        //pricing tab
        $pricing_description = new Item("pricing_description","wysiwyg","","Pricing Description:");
        $pricing_description->fieldid = "wysiwyg_pricing_description";
        $this->data["pricing_description"] = $pricing_description;
        
        // downloads tab
        $download_caption = new Item("download_caption","text","","Download Caption:");
        $download_caption->fieldsize = 250;
        $download_caption->class = array("long_input");    
        $this->data['download_caption'] = $download_caption;         
        
        $download_link = new Item("download_link","text","","Download Link:");
        $download_link->fieldsize = 250;
        $download_link->class = array("long_input");
        $this->data['download_link'] = $download_link;
        
        $download_text = new Item("download_text","textarea","","Download Text:");
        $download_text->fieldsize = 50;
        $download_link->class = array("long_input");    
        $this->data['download_text'] = $download_text;        
    }
    
    function _load_product_page($product_id = '')
    {
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Product Manager";
        $this->data["page_heading"] = "Product Manager";

        $this->data['categories'] = $this->product_model->get_all_categories();
        if($product_id == '')
            $this->data['category_id'] = -1; //default category
        else
        {
            $details = $this->product_model->get_details($product_id);
            $this->data['category_id'] = $details->row()->product_category_id;
        }
        
        // Load article categories - we need these to link a product to the appropriate
        // article category.
        $this->data["article_categories"] = $this->article_category_model->get_list();
        
       //load views         
        $this->load->view('admin/header', $this->data);
        
        $this->load->view('admin/product/prebody.php', $this->data);
        $this->load->view('admin/product/main.php', $this->data);
        
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);     
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        $category_id = intval($this->tools_model->get_value("category_id",0,"post",0,false));

        switch($type)
        {
            case 1: //refresh product files
                $return_data = array();
                
                $product_id = $this->tools_model->get_value("product_id","","post",0,false);

                $files = $this->document_model->get_list("product_image", $product_id); //$this->utilities->get_files(PRODUCT_FILES_FOLDER.$product_id,false,false); //product images

                if($files)
                {
                    $this->data["files"] = $files;
                    $this->data["files_no"] = (isset($this->data["files"])) ? $this->data["files"]->num_rows() : 0;
                    $this->data["pages_no"] = $this->data["files_no"] / $this->images_records_per_page;
                }

                else
                {
                    $this->data["files"] = $files;
                    $this->data["files_no"] = 0;
                    $this->data["pages_no"] = $this->data["files_no"] / $this->images_records_per_page;
                }
                
                //get hero image
                $this->data["selected_hero_image"] = $this->product_model->get_hero_image($product_id);                
                                                                                 
                $return_data["html"] = $this->load->view('admin/product/file_listing.php',NULL,true); 
                
                echo json_encode($return_data);    
                
            break;

            //page number changed
			case 2:

				//get list of pages
				$products = $this->product_model->get_products($category_id, $this->records_per_page, $current_page,$count_all);

                $data['products'] = $products;
                $data['pages_no'] = $count_all / $this->records_per_page;
                $data['category_id'] = $category_id;

				//load view
				$this->load->view('admin/productmanager/product_listing',$data);

			break;
			
            case 3: //add new category
                $return_data = array();
                        
                $category_name = $this->tools_model->get_value("category_name",0,"post",0,false);
                $category_code = $this->tools_model->get_value("category_code", 0, "post", 0, false);
                $parent_category_id = $this->tools_model->get_value("parent_category_id",-1,"post",0,false);
                
                if(($category_name == "") || ($category_code == ""))
                {
					// We're missing critial data.
 					$return_data["message"] = "Sorry, a new category could not be created.";
		            echo json_encode($return_data);
		            break;  					
                }
            		
				// Is there a category that matches this code for this website?		
				$cat = $this->product_model->category_exists_bycode($category_code, $parent_category_id);
				if($cat)
				{
					// A matching category was found.
 					$return_data["message"] = "This category code already exists.  Please try another.";
		            echo json_encode($return_data);
		            break;  				
				}
				
				$data = array();
				$data["name"] = $category_name;
				$data["category_code"] = $category_code; 
				$data["parent_id"] = $parent_category_id; 
				
				$category_id = $this->product_category_model->save("", $data); 
				    
                if($category_id)
                {
                    // The new category was created successfully.
                    $return_data["message"] = "Category created";
                }
                else
                {
                    $return_data["message"] = "An error occured whilst trying to create the new category.";
                }                            
               
                //reresh category list
                $return_data["html"] = $this->refresh_category_list($parent_category_id);
                
                echo json_encode($return_data);
            break;
            
            case 4: //delete category
                
                $return_data = array();
                
                //get categories separated with ";"
                $categories = $this->tools_model->get_value("todelete","","post",0,false);
                $parent_category_id = $this->tools_model->get_value("parent_category_id",-1,"post",0,false);
                
                if ($categories!="")
                {
                    $arr_categories = explode(";",$categories);
                    $this->product_model->delete_categories($arr_categories);
                    
                    $return_data["message"] = "ok";
                }
                else
                    $return_data["message"] = "Error deleteing categories";
                    
                //reresh category list
                $return_data["html"] = $this->refresh_category_list($parent_category_id);
                
                echo json_encode($return_data);    
            break;
            
            case 5: //upload file (image)
            
                 $product_id = $this->tools_model->get_value("product_id","","post",0,false);
                    
                 $tmp_name = $_FILES["Filedata"]["tmp_name"];
                 $name = $_FILES["Filedata"]["name"];

                 $file_path = ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id."/".$name;                
                 move_uploaded_file($tmp_name, $file_path);
                 
                 chmod($file_path,0777);
                 
                 //create thumb
                 $this->load->library('Image');
                
                 $thumb_image_settings = array(
                                        array("prefix"=>THUMB_SMALL_PREFIX,     "width" => THUMB_SMALL_WIDTH),
                                        array("prefix"=>THUMB_MEDIUM_PREFIX,    "width" => THUMB_MEDIUM_WIDTH),
                                        array("prefix"=>THUMB_LARGE_PREFIX,    "width" => THUMB_LARGE_WIDTH)
                                    );
                                    //array("prefix"=>"tmb_","width"=>115),
                    
                                                                
                 foreach($thumb_image_settings as $setting)
                 {
                    $error_message = ""; 
                    $thumb_path = ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id."/".$setting['prefix'].$name; 
                     
                    $this->image->resize_magick($file_path, $thumb_path, $setting['width'], $height=0, $crop = true, $error_message = "");               
                 }
                 
                 $img_data =  array(
                    "document_type" => "product_image",
                    "foreign_id" => $product_id,
                    "document_name" => $name,
                    "document_path" => PRODUCT_FILES_FOLDER.$product_id."/".$name                
                 );
                 $this->document_model->save("", $img_data);
                 
                 echo "done";
                 
            break;
            
            case 6: //download image
                 
                 $file = urldecode($this->tools_model->get_value("file",0,"post",0,false));
                 $product_id = intval($this->tools_model->get_value("product_id",0,"post",0,false)); 
                 
                 $path = ABSOLUTE_PATH.$file;
                 
                 $this->utilities->download_file($path);
        
            break;
            
            case 7: //delete selected files
                //get files names separated with ";"
                $product_id = $this->tools_model->get_value("product_id","","post",0,false);
                $file_ids = $this->tools_model->get_value("todelete","","post",0,false);
                $remove_files = array();
                
                if ($file_ids!="")
                {
                    $arr_files = explode(";",$file_ids);
                    
                    $files = $this->document_model->get_list("product_image", $product_id);
                    
                    //get files name to delete from hard
                    if($files)
                    {
                        foreach($files->result() as $row)
                        {
                            if( in_array($row->id,$arr_files))
                            {
                                $file_name = basename($row->document_path);        
                                $remove_files[] = $file_name;
                            }
                        }
                    }
                    
                    $this->document_model->delete($arr_files);
                    
                    $this->utilities->remove_file(PRODUCT_FILES_FOLDER.$product_id,$remove_files,"");                    
                    
                    echo "ok";
                }                
            
            break;
            
            //editInPlace
            case 8: //rename description
            
                 $file_id = intval($this->input->post('element_id'));
                 $description = $this->input->post('update_value',true);
                 
                if($file_id > 0)
                {
                    $img_data = array(
                        "document_name" => $description
                    );
                    $this->document_model->save($file_id, $img_data);                    
                }
                echo $description; 
            break;
            
            case 9: //delete products
                $return_data = array();

                //get products separated with ";"
                $products = $this->tools_model->get_value("todelete","","post",0,false);
                $category_id = $this->tools_model->get_value("category_id","","post",0,false);

                if ($products !="")
                {
                    $arr_products = explode(";",$products);
                    $this->product_model->delete_products($arr_products);
                    $this->article_model->delete_category_products($arr_products);
                    $return_data["message"] = "ok";
                }
                else
                    $return_data["message"] = "Error deleteing products";

                //reresh category list
                $return_data["html"] = $this->refresh_product_list($category_id);

                echo json_encode($return_data);

            break;
            
            case 10: //delete price
            	$price_id 	= intval( $this->input->post( 'price_id' ) );
            	$product_id	= intval( $this->input->post( 'product_id' ) );
            	
            	if( $price_id > 0 && $product_id > 0 )
            	{
            		$this->product_model->delete_product_price( $price_id );
            		$this->data['broadcast_access_levels_to'] 	= $this->broadcast_model->get_all_access_levels_to();
            		$this->data['product_id']					= $product_id;
	            	$this->data['pricings'] 					= $this->product_model->get_all_pricings( $product_id );
	            	print $this->load->view( 'admin/product/price_listing', $this->data, TRUE );
            	}
            break;
            
            case 11: //add new bracket
            	$data								= array();
	            $data['broadcast_access_level_id'] 	= intval( $this->input->post( 'access_level_id' ) );
            	$data['product_id']					= intval( $this->input->post( 'product_id' ) );
            	$data['description']				= ( $this->input->post( 'description' ) ? $this->input->post( 'description' ) : '&nbsp;' );
            	$data['bracket_max']				= $this->input->post( 'bracket_max' );
            	$data['price']						= doubleval( $this->input->post( 'price' ) );
            	
            	// insert data into database
            	if( !empty( $data['broadcast_access_level_id'] ) && !empty( $data['product_id'] )
            		&& !empty( $data['description'] ) && !empty( $data['bracket_max'] )
            		&& !empty( $data['price'] ) )
            	{
            		$this->product_model->save_product_price( '', $data );
            		
            		$this->data['broadcast_access_levels_to'] 	= $this->broadcast_model->get_all_access_levels_to();
            		$this->data['product_id']					= $data['product_id'];
	            	$this->data['pricings'] 					= $this->product_model->get_all_pricings( $data['product_id'] );
	            	print $this->load->view( 'admin/product/price_listing', $this->data, TRUE );
            	}
            break;
            
            case 12: //update bracket level
            	$data								= array();
	            $data['broadcast_access_level_id'] 	= intval( $this->input->post( 'access_level_id' ) );
            	$product_price_id					= intval( $this->input->post( 'price_id' ) );
            	
            	// insert data into database
            	if( !empty( $data['broadcast_access_level_id'] ) && !empty( $product_price_id ) )
            	{
            		$this->product_model->save_product_price( $product_price_id, $data );
            	}
            break;
            
            case 13: //upload file for download
            
                 $product_id = $this->tools_model->get_value("product_id","","post",0,false);
                    
                 $tmp_name = $_FILES["Filedata"]["tmp_name"];
                 $name = $_FILES["Filedata"]["name"];

                 //chmod( ABSOLUTE_PATH.PRODUCT_FILES_FOLDER, 0777 );
                 //chmod( ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id, 0777 );
                 //chmod( ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id."/files", 0777 );
                 
                 
                 $file_path = ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id."/files/".$name;                
                 move_uploaded_file($tmp_name, $file_path);
                 chmod($file_path,0777);
                 
                 $file_data =  array(
                    "document_type" => "product_files",
                    "foreign_id" => $product_id,
                    "document_name" => $name,
                    "document_path" => PRODUCT_FILES_FOLDER.$product_id."/files/".$name
                 );
                 $this->document_model->save("", $file_data);
                 
                 echo "done";
                 
            break;
            
            case 14: // delete download file
            	
            break;
            
            //editInPlace
            case 15: //rename description
            
                 $file_id = intval($this->input->post('element_id'));
                 $description = $this->input->post('update_value',true);
                 
                if($file_id > 0)
                {
                    $img_data = array(
                        "document_description" => $description
                    );
                    $this->document_model->save($file_id, $img_data);                    
                }
                echo $description; 
            break;
            
            case 16: // refresh download files
            	$return_data = array();
                
                $product_id = $this->tools_model->get_value("product_id","","post",0,false);

                $this->data["download_files"] = $this->document_model->get_list("product_files", $product_id);

                $return_data["html"] = $this->load->view('admin/product/download_listing.php',NULL,true); 
                echo json_encode($return_data); 
            break;
            
            case 17: //delete selected download files
                //get files names separated with ";"
                $product_id = $this->tools_model->get_value("product_id","","post",0,false);
                $file_ids = $this->tools_model->get_value("todelete","","post",0,false);
                $remove_files = array();
                
                if ($file_ids!="")
                {
                    $arr_files = explode(";",$file_ids);
                    
                    $files = $this->document_model->get_list("product_files", $product_id);
                    
                    //get files name to delete from hard
                    if($files)
                    {
                        foreach($files->result() as $row)
                        {
                            if( in_array($row->id,$arr_files))
                            {
                                $file_name = basename($row->document_path);        
                                $remove_files[] = $file_name;
                            }
                        }
                    }
                    
                    $this->document_model->delete($arr_files);
                    
                    $this->utilities->remove_file(PRODUCT_FILES_FOLDER.$product_id.'/files',$remove_files,"");                    
                    
                    echo "ok";
                }                
            
            break;
            
            case 18: // save access level for download file
            	$file_id = $this->tools_model->get_value("id","","post",0,false);
                $access_level_id = $this->tools_model->get_value("level_id","","post",0,false);
                
                
                $file_data = array( 'broadcast_access_level_id' => $access_level_id );
                
                if( $this->document_model->save( $file_id, $file_data ) )
                	print 'ok';
                else
                	print 'error';
            break;
            
            case 19: // save exact level or not for download file
            	$file_id = $this->tools_model->get_value("id","","post",0,false);
                $is_exact = $this->tools_model->get_value("is_exact","","post",0,false);
                
                
                $file_data = array( 'is_exact' => $is_exact );
                
                if( $this->document_model->save( $file_id, $file_data ) )
                	print 'ok';
                else
                	print 'error';
            break;
            
            case 20: // edit quantity for a bracket
            	$price_id = intval($this->input->post('element_id'));
                $value = $this->input->post('update_value',true);
                 
                if($price_id > 0)
                {
                    $price_data = array(
                        "bracket_max" => $value
                    );
                    $this->product_model->save_product_price($price_id, $price_data);                    
                }
                echo $value; 
            break;
            
            case 21: // edit price for a bracket
            	$price_id = intval($this->input->post('element_id'));
                $value = $this->input->post('update_value',true);
                 
                if($price_id > 0)
                {
                    $price_data = array(
                        "price" => $value
                    );
                    $this->product_model->save_product_price($price_id, $price_data);                    
                }
                echo $value; 
            break;
            
            case 22: //upload file (gallery)
            
                 $product_id = $this->tools_model->get_value("product_id","","post",0,false);
                 
                 
                 $tmp_name = $_FILES["Filedata"]["tmp_name"];
                 $name = $_FILES["Filedata"]["name"];

                 $file_path = ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id."/".$name;                
                 move_uploaded_file($tmp_name, $file_path);
                 
                 chmod($file_path,0777);
                 
                 //create thumb
                 $this->load->library('Image');
                
                 $thumb_image_settings = array(
                                        array("prefix"=>THUMB_SMALL_PREFIX,     "width" => THUMB_SMALL_WIDTH),
                                        array("prefix"=>THUMB_MEDIUM_PREFIX,    "width" => THUMB_MEDIUM_WIDTH),
                                        array("prefix"=>THUMB_LARGE_PREFIX,    "width" => THUMB_LARGE_WIDTH)
                                    );
                                    //array("prefix"=>"tmb_","width"=>115),
                    
                /* foreach($thumb_image_settings as $setting)
                 {  
                    $error_message = ""; 
                    $thumb_path = FCPATH.PRODUCT_FILES_FOLDER.$product_id."/".$setting['prefix'].$name;                
                    $this->image->create_thumbnail_square($file_path,$thumb_path,$error_message,$setting['width']);
                    
                    chmod($thumb_path,0777);                     
                 }*/
                 
         		foreach($thumb_image_settings as $setting)
                 {
                    $error_message = ""; 
                    $thumb_path = ABSOLUTE_PATH.PRODUCT_FILES_FOLDER.$product_id."/".$setting['prefix'].$name; 
                     
                    $this->image->resize_magick($file_path, $thumb_path, $setting['width'], $height=0, $crop = true, $error_message = "");              

                    chmod($thumb_path,0777);   
                 }
                 
                 
                 $img_data =  array(
                    "document_type" => "gallery_image",
                    "foreign_id" => $product_id,
                    "document_name" => $name,
                    "document_path" => PRODUCT_FILES_FOLDER.$product_id."/".$name                
                 );
                 $this->document_model->save("", $img_data);
                 
                 echo "done";                 
            break;
            
            case 23: //refresh gallery files
                $return_data = array();
                
                $product_id = $this->tools_model->get_value("product_id","","post",0,false);

                $files = $this->document_model->get_list("gallery_image", $product_id); //$this->utilities->get_files(PRODUCT_FILES_FOLDER.$product_id,false,false); //product images

                if($files)
                {
                    $this->data["gallery_files"] = $files;
                    $this->data["gallery_files_no"] = (isset($this->data["gallery_files"])) ? $this->data["gallery_files"]->num_rows() : 0;
                    $this->data["gallery_pages_no"] = $this->data["gallery_files_no"] / $this->images_records_per_page;
                }

                else
                {
                    $this->data["gallery_files"] = $files;
                    $this->data["gallery_files_no"] = 0;
                    $this->data["gallery_pages_no"] = $this->data["gallery_files_no"] / $this->images_records_per_page;
                }
                
                $return_data["html"] = $this->load->view('admin/product/gallery_listing.php',NULL,true); 
                
                echo json_encode($return_data);
            break;
            
            //editInPlace
            case 24: //rename gallery description
            
                 $file_id = intval($this->input->post('element_id'));
                 $description = $this->input->post('update_value',true);
                 
                if($file_id > 0)
                {
                    $img_data = array(
                        "document_name" => $description
                    );
                    $this->document_model->save($file_id, $img_data);                    
                }
                echo $description; 
            break;
            
            case 25: //delete selected gallery files
                //get files names separated with ";"
                $product_id = $this->tools_model->get_value("product_id","","post",0,false);
                $file_ids = $this->tools_model->get_value("todelete","","post",0,false);
                $remove_files = array();
                
                if ($file_ids!="")
                {
                    $arr_files = explode(";",$file_ids);
                    
                    $files = $this->document_model->get_list("gallery_image", $product_id);
                    
                    //get files name to delete from hard
                    if($files)
                    {
                        foreach($files->result() as $row)
                        {
                            if( in_array($row->id,$arr_files))
                            {
                                $file_name = basename($row->document_path);        
                                $remove_files[] = $file_name;
                            }
                        }
                    }
                    
                    $this->document_model->delete($arr_files);
                    
                    $this->utilities->remove_file(PRODUCT_FILES_FOLDER.$product_id,$remove_files,"");                    
                    
                    echo "ok";
                }
            break;
            
            //editInPlace
            case 26: //rename document name from downloads tab
            
                 $file_id = intval($this->input->post('element_id'));
                 $description = $this->input->post('update_value',true);
                 
                if($file_id > 0)
                {
                    $img_data = array(
                        "document_name" => $description
                    );
                    $this->document_model->save($file_id, $img_data);                    
                }
                echo $description; 
            break;
            
            //editInPlace
            case 27: //rename document name from downloads tab
            
                 $device_id = $this->input->post('device_id');
                 $serial_gen = $this->input->post('serial_gen');
                 
                 include (FCPATH."serialgen/".$serial_gen.".inc.php");
                 
                 
               	//reresh category list
                $return_data["serial_number"] = generateSerial($device_id);
                
                echo json_encode($return_data);
            break;
        }
    
    }
    
    //reresh category list
    function refresh_category_list($parent_category_id)
    {
       $categories = $this->product_model->get_categories($parent_category_id); 
                
       $this->data['categories'] = $categories;                 
       $this->data['category_id'] = $parent_category_id;
                           
       $category_listing_html = $this->load->view('admin/productmanager/category_listing.php',NULL,true); 
       
       return  $category_listing_html;
    }
    
    //refresh product list
    function refresh_product_list($category_id)
    {
       //load items from the category
       $products = $this->product_model->get_products($category_id);    
        
       $this->data['products'] = $products;                 
                                                                                  
       $product_listing_html = $this->load->view('admin/productmanager/product_listing.php',$this->data,true); 
       
       return  $product_listing_html;
    }
    
    
}

    include("Item.php"); // inlude item class
?>
