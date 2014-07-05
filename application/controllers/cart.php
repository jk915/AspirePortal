<?php
die("OFFLINE");
/***
* Cart Controller
* @author: SIMB Pty Ltd 2009-2010
* 
*/
class Cart extends CI_Controller 
{
   // Define variables
   public $data;    // Array to hold data to pass to pages.  

   /***
    * @desc The Cart constructor checks that the user has logged in
    * and loads any needed views, libraries, etc.
    */
    function Cart()
    {
        parent::__construct();
                        
        // Create the data array.
        $this->data = array();

        // Load models etc
        $this->load->model("menu_model");
        $this->load->model("product_model");        
        $this->load->model("document_model");        
    }
    
    function index()
    {
        $cart_info = $this->product_model->get_cart_info();        
        $this->data = $cart_info;
        $this->data["nav_main"] = $this->menu_model->get_menu_html_extended(1, 11);
        
        //load meta tags
        $this->data["meta_keywords"] = "Commandfusion - Order detail";
        $this->data["meta_description"] = "Commandfusion - Order detail"; 
        $this->data["meta_title"] = "Commandfusion - Order detail";
        
        $this->load->view('header', $this->data);
        $this->load->view('cart/prebody', $this->data); 
        $this->load->view('cart/main', $this->data);
        $this->load->view('pre_footer', $this->data); 
        $this->load->view('footer', $this->data);         
    }            
}
?>
