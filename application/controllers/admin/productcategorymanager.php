<?php
class Productcategorymanager extends CI_Controller
{
   public $data;        // Will be an array used to hold data to pass to the views.
   private $records_per_page = ITEMS_PER_PAGE;

   function __construct()
   {
      parent::__construct();

      // Create the data array.
      $this->data = array();

      // Load models, libaries etc
      $this->load->model("product_model");
      $this->load->model("product_category_model");
      $this->load->model("resources_model");
      $this->load->library("utilities");

      // Check for a valid session
      if (!$this->login_model->getSessionData("logged_in"))
      {
      	redirect("login");
      }
   }

   function edit($category_id)
   {
		if(($category_id == "") || (!is_numeric($category_id)))
			show_error("Invalid category ID");

		// Check for a postback
		$postback = $this->tools_model->isPost();

      if ($postback)
      {
      	// The user has submitted the form, handle the post
      	$category_id = $this->_handlePost($category_id);

         // Redirect make to the product category manager
         redirect("/productcategorymanager/edit/".$category_id);
         exit();
		}

		$this->data["category_id"] = $category_id;

		// Load the category
		$category = $this->product_category_model->get_details($category_id);
        $this->data["category"] = $category;

        $this->data['parent_id'] = $category->parent_id;

		if(!$this->data["category"])
			show_error("Sorry, the category could not be loaded");

		// Load all top level categories
		$categories = $this->product_model->get_all_categories($category_id);
		$this->data["categories"] = $categories;


		$this->data["message"] = "You are editing the category '" . $this->data["category"]->name . "'";

		// Load resources needed for the page
		$this->data["order_by_options"] = $this->resources_model->get_list("product_order");

      // Define page variables
      $this->data["meta_keywords"] = "";
      $this->data["meta_description"] = "";
      $this->data["meta_title"] = "Edit Category Category";
      $this->data["robots"] = $this->utilities->get_robots();

      // Load views
      $this->load->view('admin/header', $this->data);
      $this->load->view('admin/productcategory/prebody.php', $this->data);
      $this->load->view('admin/productcategory/main.php', $this->data);
      $this->load->view('admin/pre_footer', $this->data);
      $this->load->view('admin/footer', $this->data);
   }

	
   function _handlePost($category_id)
   {
      // Define the update array with default values
      $data = array( "name"			         => '',
      				 "category_code"	     => '',
                     "enabled"               => '0',
                     "parent_id"		     => '',
                     "meta_title"            => '',
                     "meta_keywords"         => '',
                     "meta_description"      => '',
                     "meta_robots"           => '',
                     "order_by"              => '',
                     "order_dir" 	         => '',
                     "short_description"     => '',
                     "long_description"      => '',
                     "hero_image"            => '',
                     "hero_text"             => '',
                     "category_image"        => '',
                     "category_text"         => '',
                     "background_image"      => '',
                     "text_color"            => ''
          
      );


      // Define the required fields for validation
      $required_fields = array("name");
      $missing_fields = false; // Set to true if fields are missing

      //fill in data array from post values
      foreach($data as $key=>$value)
      {
         $data[$key] = $this->tools_model->get_value($key,$data[$key], "post", 0, true);

         // Ensure that all required fields are present
         if(in_array($key,$required_fields) && $data[$key] == "")
         {
            $missing_fields = true;
            break;
         }
      }

      // If there are missing fields, report the error
      if ($missing_fields)
      {
         $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "productmanager/_HandlerPost update - the product with an id '$category_id' could not be saved");
         return false;
      }

      // If an $category_id is present, do an update, otherwise do an insert
      $category_id = $this->product_category_model->save($category_id, $data);

      if(!$category_id)
      {
         // Something went wrong whilst saving the user data.
         $this->error_model->report_error("Sorry, the category could not be saved/updated.", "productcategorymanager/category save");
         return false;
      }


      // All done - return the category id to the caller
      return $category_id;
   }
}
?>