<?php
class Ordermanager extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    private $records_per_page = ITEMS_PER_PAGE;
    public $firstname = "";
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array();            
        
        // Load models etc
        $this->load->model("order_model");                
        $this->load->model("product_model");                
        $this->load->model("login_model");                
        $this->load->model("product_model");
        
        // load helpers
        $this->load->helper('order_helper');    
        $this->load->helper('flashchart_helper');    
        
        // Check for a valid session
        if (!$this->login_model->getSessionData("logged_in"))
            redirect("login");   
            
        $this->firstname = $this->login_model->getSessionData("firstname");                              
    }
    
    function index()
    {
        /*$userdata = $this->session->userdata("cms_ordersearch"); 
        $this->data["isAdmin"] = true;      
                        
        /* if the user is a printer we shouldn't let him to delete any orders*/
        /*if ($this->login_model->is_logged_in("cms_user",PRINTER_USER_TYPE_ID))
        {
           $this->data["isAdmin"] = false;           
        } */            
        
        
        $search_name = '';
        
        $search_doc_type = -1;
        
        $search_period = 'week_to_date';
        $current_page = 1;
        $start_date = '';
        $end_date = '';
        $search_status = '';
        
        $this->data["name"] = $this->firstname;
        $this->data["orders"] = $this->order_model->get_list($this->records_per_page,$current_page,$count_all, $search_name, $search_doc_type, $search_period, $start_date, $end_date, $search_status);                              
                
        $this->data["search_status_arr"] = $this->tools_model->get_status();        
        $this->data["pages_no"] = $count_all / $this->records_per_page;
        $this->data["doc_types"] = $this->product_model->get_categories();
        $this->data["search_name"] = $search_name;
        $this->data["search_doc_type"] = $search_doc_type;
        $this->data["search_period"] = $search_period;
        $this->data["start_date"] = $start_date;
        $this->data["end_date"] = $end_date;
        $this->data["search_status"] = $search_status;
        
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Order Manager";
        $this->data["page_heading"] = "Order Manager";        
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/ordermanager/prebody', $this->data); 
        $this->load->view('admin/ordermanager/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    /**
    * @method: order
    * @author: Agnes Konya
    * @version 1.0  
    * 
    * @desc: The order method shows an order with the specified order id.
    * If no id code is given, it means it a new order is going to be created.
    * 
    * @param integer $order_id - The order id of the user to load.
    */
    
    function order($order_id = "")
    {
        $this->data["page_heading"] = "Order Details";
        $this->data['message'] = "";
        $delivery_method = "";
        
        $postback = $this->tools_model->isPost();
        
        if ($postback)
        {
            $this->_handlePost($order_id);
        }
        
        if($order_id != "") //edit
        {
            $order = $this->order_model->get_order_details($order_id);
            if(!$order)
            {
                // The page could not be loaded.  Report and log the error.
                $this->error_model->report_error("Sorry, the order could not be loaded.", "Order/order - the order with an id of '$order_id' could not be loaded");
                return;            
            }
            else
            {
                //pass order details
                $this->data["order"] = $order;
                $this->data["order_items"] = $this->order_model->get_order_item_details("",$order->id);                
                $this->data['states'] = $this->tools_model->get_states();
                $this->data['countries']  = $this->tools_model->get_countries();
                $this->data['currencies'] = $this->resources_model->get_list('currency');
                $delivery_method = $order->delivery_method;
            }
        }
                    
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "Website Administration Order";
        $this->data['order_id'] = $order_id;        
        
        /* if the user is a printer we shouldn't let him to delete any orders*/
        
        $this->data["search_status_arr"] = $this->tools_model->get_status($delivery_method);        
        
        // Load views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/order/prebody.php', $this->data); 
        $this->load->view('admin/order/main.php', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data);        
    
    }
    
    /**
     * @method    build_page_chart
     * @access    private
     * @desc    this method generates the charts and holds are the necessary data for it 
     * @author
     * 
     * @version    1.0
     * @return
     */
    private function build_page_chart( )
    {
        $this->data['years']                = $this->order_model->get_created_years();
        $max_order_year = date('Y');
        
        if($this->data['years']) 
        {
            $result = $this->data['years']->row();
            $max_order_year = $result->year;
            
        }
        $this->data['months']                = array( '-1' => 'All', '01' => 'January', '02' => 'February', '03' => 'March', '04' => 'April', '05' => 'May', '06' => 'June', '07' => 'July', '08' => 'August', '09' => 'September', '10' => 'October', '11' => 'November', '12' => 'December' );
        
        $all_charts                            =  array();
        
        //sales by month bar chart
        $chart                                = array();        
        $chart_values                        = sales_month_barchart( $this->order_model->sales_by_month(), $max );
        
        $chart['title']                        = array( 
                                                    'name'        => 'Sales by month',
                                                    'style'        => '{font: Verdana,Helvetica,Arial,sans-serif; font-size: 15px; font-weight: bold; color: #445028; text-align: center;}' 
                                            );
                                            
        $month                                = array( 'Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
        $chart['x_axis']                    = array(
                                                    'color'        => '#A2ACBA',
                                                    'grid_color'=> '#D7E4A3',
                                                    '3d'        => 2,
                                                    'range'        => array( 'min' => 0, 'max' => 11 ),
                                                    'labels'    => array(
                                                                        'vertical'    => true,
                                                                        'color'        => '#A2ACBA',
                                                                        'label'        => $month
                                                                 ),
                                                    'legend'    => array(
                                                                         'title'        => 'Year '. $max_order_year,
                                                                         'style'        => '{font-size: 14px; color: #778877}'
                                                                 )
        
                                            );                            
        $i = -1;
        $max_tmp = intval( $max/10 );
        while( $max_tmp > 0 )
        {
            $max_tmp = intval($max_tmp/10);
            $i++;
        }
        $i *= -1;
        $step = round( $max/10, $i);
        
        $chart['y_axis']                    = array(
                                                'range'            => array( 'start' => '0', 'end'    => $max + $step, 'step' => $step ),
                                                'stroke'        => 1,
                                                'color'            => '#000000',
                                                'tick_length'    => 7,
                                                'grid_color'    => '#A2ACBA',
                                                'legend'        => array(
                                                                         'title'        => 'Amount',
                                                                         'style'        => '{font-size: 14px; color: #778877}'
                                                                 )
                                            );
                                            
        $chart['bar']                        = array(
                                                    'type'        => '3d',
                                                    'value'        => $chart_values
                                            );
                                            
        $bar_chart                        = array(
                                                    'div_id'    => 'bar_charts',
                                                    'id'        => 'bar',
                                                    'width'        => '470',
                                                    'height'    => '350',
                                                    'data'        =>  plot_chart( $chart )
                                                );
                                                
        $all_charts[]                            = $bar_chart;
        
        //pie chart for product category       
        $chart_values                        = sold_products_piechart( $this->order_model->get_product_sales( date('Y'), '-1' ) );
        $chart                                = array();
        $chart['pie']                        = array(
                                                    'values'    => $chart_values,
                                                    'colours'    => array('#FF3300','#0000CD', '#008000', '#8A2BE2'),
                                                    'tooltip'    => '#val# of #total#<br>#percent# of 100%',
                                                    'animation'    => array( 
                                                                        array( 'name' => 'fade'),
                                                                        array( 'name' => 'bounce', 'distance' => 4 )
                                                                    ),
                                                    'start_angle' => 0,
                                                    'alpha'        => 0.7
                                            );
        $chart['title']                        = array( 
                                                    'name'        => 'Sold Product by Product Category',
                                                    'style'        => '{font: Verdana,Helvetica,Arial,sans-serif; font-size: 15px; font-weight: bold; color: #445028; text-align: center;}' 
                                            );
                                            
        $pie_chart                                = array(
                                                    'div_id'    => 'pie_charts',
                                                    'id'        => 'pie',
                                                    'width'        => '440',
                                                    'height'    => '350',
                                                    'data'        =>  plot_chart( $chart )
                                                );
                                                
        $all_charts[]                            = $pie_chart;
        
        //bar chart for product
        $chart                                = array();        
        $chart_values                         = sold_products_barchart( $this->order_model->get_product_sales( date('Y'), '-1' ), $max, $product_names );
        
        $chart['title']                       = array( 
                                                    'name'        => 'Top 10 products',
                                                    'style'        => '{font: Verdana,Helvetica,Arial,sans-serif; font-size: 15px; font-weight: bold; color: #445028; text-align: center;}' 
                                            );
                                            
        $chart['x_axis']                    = array(
                                                    'color'        => '#A2ACBA',
                                                    'grid_color'   => '#D7E4A3',
                                                    'range'        => array( 'min' => 0, 'max' => 9 ),
                                                    'labels'       => array(
                                                                        'vertical'    => true,
                                                                        'color'        => '#A2ACBA',
                                                                        'label'        => $product_names
                                                                   ),
                                                    'legend'       => array(
                                                                         'title'        => 'Products',
                                                                         'style'        => '{font-size: 14px; color: #778877}'
                                                                   )
                                            );                            
        $i = -1;
        $max_tmp = intval( $max/10 );
        while( $max_tmp > 0 )
        {
            $max_tmp = intval($max_tmp/10);
            $i++;
        }
        $i *= -1;
        $step = round( $max/10, $i);
        
        $chart['y_axis']                    = array(
                                                'range'            => array( 'start' => '0', 'end'    => $max + $step, 'step' => $step ),
                                                'stroke'        => 1,
                                                'color'            => '#000000',
                                                'tick_length'    => 7,
                                                'grid_color'    => '#A2ACBA',
                                                'legend'        => array(
                                                                         'title'        => 'Piece',
                                                                         'style'        => '{font-size: 14px; color: #778877}'
                                                                 )
                                            );
                                            
        $chart['bar']                        = array(
                                                    'type'        => 'glass',
                                                    'value'        => $chart_values
                                            );
                                            
        $product_bar_chart                        = array(
                                                    'div_id'    => 'product_bar_chart',
                                                    'id'        => 'product_bar',
                                                    'width'        => '460',
                                                    'height'    => '350',
                                                    'data'        =>  plot_chart( $chart )
                                                );
                                                
        $all_charts[]                            = $product_bar_chart;  
        
        $this->data['charts']                    = $all_charts;
    }
    
    /**
     * @method    chart
     * @access    public
     * @desc    this method is called by an iframe and holds all the charts 
     * @author
     * 
     * @version    1.0
     * @return
     */
    public function chart()
    {
        $this->data['meta_keywords']        = '';
        $this->data['meta_description']     = '';
        $this->data['meta_title']           = 'Order Manager';
        $this->data['page_heading']         = 'Order Manager';
        $this->data['name']                 = $this->firstname;
        
        //call the chart function
        $this->build_page_chart();
        
        $this->load->view( 'admin/header', $this->data );
        
        //load module view
        $this->load->view( 'admin/ordermanager/chart/prebody', $this->data ); 
        $this->load->view( 'admin/ordermanager/prebody', $this->data ); 
        $this->load->view( 'admin/ordermanager/chart/main', $this->data );
    }
    
    function _handlePost($order_id)
    {
        
            $data = array(  "order_status"       => '',
                            "country"            => '',
                            "currency"           => '',
                            "billing_address1"   => '',
                            "billing_address2"   => '',
                            "billing_suburb"     => '',
                            "billing_postcode"   => '',
                            "billing_state"      => '',
                            "delivery_address1"  => '',
                            "delivery_address2"  => '',
                            "delivery_suburb"    => '',
                            "delivery_postcode"  => '',
                            "delivery_state"     => '',
                            "comments"           => ''   
                        );
            
            $required_fields = array("billing_address1", "billing_suburb","billing_postcode","billing_state", "delivery_address1", "delivery_suburb", "delivery_postcode", "delivery_state");
            $missing_fields = false;
            
            //fill in data array from post values
            foreach($data as $key => $value)
            {
                $data[$key] = $this->tools_model->get_value($key,"","post",0,true);
                
                // Ensure that all required fields are present    
                if(in_array($key,$required_fields) && $data[$key] == "")
                {                       
                    $missing_fields = true;
                    break;
                }
            }
            
            if ($missing_fields)
            {                   
                $this->error_model->report_error("Sorry, please fill in all required fields to continue.", "OrderManager/HandlerPost update - the page with a code of '$order_id' could not be saved");
                return;
            }
            
            //update order
            $sucess_update = $this->order_model->save_order($data,$order_id);
            if(!$sucess_update)
            {
               // Something went wrong whilst saving the user data.
               $this->error_model->report_error("Sorry, the order could not be saved/updated.", "Ordermanager/order save");
               return;
            }
                
            redirect("/ordermanager");
            
    }
    
    //handles all ajax requests within this page
    function ajaxwork()
    {
        $type = intval($this->tools_model->get_value("type",0,"post",0,false));
        $current_page = intval($this->tools_model->get_value("current_page",0,"post",0,false));
        
        switch($type)
        {
            //delete orders
            case 1:
                //get order ids separated with ";"
                $order_ids = $this->tools_model->get_value("todelete","","post",0,false);
                
                if ($order_ids!="")
                {
                    $arr_ids = explode(";",$order_ids);
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
                        $this->order_model->delete($where_in);
                    }
                }
                
                $this->_refresh_order_listing($current_page);
                
                
            break;
            
            //page number changed, refresh orders
            case 2:
                
                $this->_refresh_order_listing($current_page);
                
            break;
            
            //reload the bar charts because the year has changed
            case 3:
                $return_data                        = array();
                $year                                = $this->input->post( 'year' );
                //user registration bar chart
                $chart_values                        = sales_month_barchart( $this->order_model->sales_by_month( $year ), $max );
        
                $chart['title']                        = array( 
                                                            'name'        => 'Sales by month',
                                                            'style'        => '{font: Verdana,Helvetica,Arial,sans-serif; font-size: 15px; font-weight: bold; color: #445028; text-align: center;}' 
                                                    );
                                                    
                $month                                = array( 'Jan', 'Feb', 'Mar', 'Apr', 'Mai', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec' );
                $chart['x_axis']                    = array(
                                                            'color'        => '#A2ACBA',
                                                            'grid_color'=> '#D7E4A3',
                                                            '3d'        => 2,
                                                            'range'        => array( 'min' => 0, 'max' => 11 ),
                                                            'labels'    => array(
                                                                                'vertical'    => true,
                                                                                'color'        => '#A2ACBA',
                                                                                'label'        => $month
                                                                         ),
                                                            'legend'    => array(
                                                                                 'title'        => 'Year '.$year,
                                                                                 'style'        => '{font-size: 14px; color: #778877}'
                                                                         )
                
                                                    );                            
                $i = -1;
                $max_tmp = intval( $max/10 );
                while( $max_tmp > 0 )
                {
                    $max_tmp = intval($max_tmp/10);
                    $i++;
                }
                $i *= -1;
                $step = round( $max/10, $i);
                
                $chart['y_axis']                    = array(
                                                        'range'            => array( 'start' => '0', 'end'    => $max + $step, 'step' => $step ),
                                                        'stroke'        => 1,
                                                        'color'            => '#000000',
                                                        'tick_length'    => 7,
                                                        'grid_color'    => '#A2ACBA',
                                                        'legend'        => array(
                                                                                 'title'        => 'Amount',
                                                                                 'style'        => '{font-size: 14px; color: #778877}'
                                                                         )
                                                    );
                                                    
                $chart['bar']                        = array(
                                                            'type'        => '3d',
                                                            'value'        => $chart_values
                                                    );
                $return_data['bar_chart']                = plot_chart( $chart );
                
                echo json_encode( $return_data );
            break;
            
            case 4:
                $return_data                        = array();
                
                $year                                = $this->input->post( 'year' );
                $month                                = $this->input->post( 'month' );
                
                $chart_values                        = sold_products_piechart( $this->order_model->get_product_sales( $year, $month ) );
                $chart                                = array();
                $chart['pie']                        = array(
                                                            'values'    => $chart_values,
                                                            'colours'    => array('#FF3300','#0000CD', '#008000', '#8A2BE2'),
                                                            'tooltip'    => '#val# of #total#<br>#percent# of 100%',
                                                            'animation'    => array( 
                                                                                array( 'name' => 'fade'),
                                                                                array( 'name' => 'bounce', 'distance' => 4 )
                                                                            ),
                                                            'start_angle' => 0,
                                                            'alpha'        => 0.7
                                                    );
                $chart['title']                        = array( 
                                                            'name'        => 'Sold Product by Product Category',
                                                            'style'        => '{font: Verdana,Helvetica,Arial,sans-serif; font-size: 15px; font-weight: bold; color: #445028; text-align: center;}' 
                                                    );
                
                $return_data['pie_chart']            = plot_chart( $chart );
                echo json_encode( $return_data );
            break;
            
            case 5:
                $return_data                        = array();
                $year                                = $this->input->post( 'year' );
                $month                                = $this->input->post( 'month' );
                
                $chart                                = array();        
                $chart_values                        = sold_products_barchart( $this->order_model->get_product_sales( $year, $month ), $max, $product_names );
                
                $chart['title']                        = array( 
                                                            'name'        => 'Top 10 product',
                                                            'style'        => '{font: Verdana,Helvetica,Arial,sans-serif; font-size: 15px; font-weight: bold; color: #445028; text-align: center;}' 
                                                    );
                                                    
                $chart['x_axis']                    = array(
                                                            'color'        => '#A2ACBA',
                                                            'grid_color'=> '#D7E4A3',
                                                            'range'        => array( 'min' => 0, 'max' => 9 ),
                                                            'labels'    => array(
                                                                                'vertical'    => true,
                                                                                'color'        => '#A2ACBA',
                                                                                'label'        => $product_names
                                                                         ),
                                                            'legend'    => array(
                                                                                 'title'        => 'Products',
                                                                                 'style'        => '{font-size: 14px; color: #778877}'
                                                                         )
                                                    );                            
                $i = -1;
                $max_tmp = intval( $max/10 );
                while( $max_tmp > 0 )
                {
                    $max_tmp = intval($max_tmp/10);
                    $i++;
                }
                $i *= -1;
                $step = round( $max/10, $i);
                
                $chart['y_axis']                    = array(
                                                        'range'            => array( 'start' => '0', 'end'    => $max + $step, 'step' => $step ),
                                                        'stroke'        => 1,
                                                        'color'            => '#000000',
                                                        'tick_length'    => 7,
                                                        'grid_color'    => '#A2ACBA',
                                                        'legend'        => array(
                                                                                 'title'        => 'Piece',
                                                                                 'style'        => '{font-size: 14px; color: #778877}'
                                                                         )
                                                    );
                                                    
                $chart['bar']                        = array(
                                                            'type'        => 'glass',
                                                            'value'        => $chart_values
                                                    );
                
                $return_data['product_bar_chart']    = plot_chart( $chart );
                echo json_encode( $return_data );
            break;
            
            //download pdf
            case 6:
                $file = urldecode($this->tools_model->get_value("file",0,"post",0,false));
                
                if($file != "")
                {
                    $pos = strpos($file,"invoice"); 
                    if ($pos === false)
                        $path = FCPATH."form_pdf/result_pdf/".$file;                    
                    else
                        $path = FCPATH."invoices/".$file;
                            
                    $this->utilities->download_file($path);
                }
            break;                        
            
            // list order
            case 10:
                 $this->_refresh_order_listing($current_page);
            break;                        
        }
    }
    
    function _refresh_order_listing($current_page)
    {
        $search_name = $this->tools_model->get_value("search_name","","post",0,false);
        $search_period = $this->tools_model->get_value("search_period","today","post",0,false);        
        $start_date = $this->tools_model->get_value("start_date","","post",0,false);
        $end_date = $this->tools_model->get_value("end_date","","post",0,false);
        $search_status = $this->tools_model->get_value("search_status","","post",0,false);
        
        //get list of orders
        $orders = $this->order_model->get_list($this->records_per_page,$current_page,$count_all, $search_name, -1, $search_period, $start_date, $end_date, $search_status);                              
        
              
        
        //load view 
        $this->data["orders"] = $orders;
        $this->data["pages_no"] = $count_all / $this->records_per_page;                         
        $this->data["delete_orders"] = true;
                
        //save the search parameters
        $searchData = array(
            "search_name" => $search_name,
            "search_doc_type" => -1,
            "search_period" => $search_period,
            "current_page" => $current_page,
            "start_date" => $start_date,
            "end_date" => $end_date,
            "search_status" => $search_status
        );
        $this->session->set_userdata("cms_ordersearch",$searchData);      
        
        $this->load->view('admin/ordermanager/order_listing',$this->data);
    }
    
    function order_form($order_id)
    {   
        $order = $this->order_model->get_order_details($order_id);
        $order_item = $this->order_model->get_order_item_details("",$order_id);
        
        $document_data = json_decode($order_item->document_data);
        $this->data["document_data"] = $document_data;
        
        $product = $this->product_model->get_details($order_item->product_id);
        $product_row = $product->first_row();
        
        if($product_row->product_category_id != COMPANY_DOCS_ID)
        {
            $this->error_model->report_error("Sorry, this document view could not be loaded.", "ordermanager/order_form/$order_id not exists1");
            return;
        }
        
        $abn_tfn_result = $this->product_model->get_details(ABN_TFN_ID);
                
        if($abn_tfn_result)
        {
            $abn_tfn_row = $abn_tfn_result->first_row();                     
            $this->data["abn_tfn_price"] = $abn_tfn_row->price;
        }
        
        //check if the view for this document is defined            
        if ($product_row->document_view != "")
        {
            $abn_tfn = $this->order_model->check_order_item($order_id, ABN_TFN_ID);
            
            $this->data['abn_tfn'] = $abn_tfn;
            $this->data['categories'] = $this->product_model->get_categories();      
            $this->data["product_id"] = $order_item->product_id;           
            
            //add meta tags
            $this->data["meta_keywords"] = "Orders";
            $this->data["meta_description"] = "Legal E Docs Orders";
            $this->data["meta_title"] = "Legal E Docs Orders";        
                                                                  
            $this->load->view('header', $this->data);
            $this->load->view('document_forms/prebody', $this->data); 
            $this->load->view("document_forms/".$product_row->document_view, $this->data);
            $this->load->view('pre_footer', $this->data); 
            $this->load->view('footer', $this->data);    
        }
        else
        {
            $this->error_model->report_error("Sorry, this document view could not be loaded.", "ordermanager/order_form/$order_id not exists");
            return;
        }
    }
}
?>
