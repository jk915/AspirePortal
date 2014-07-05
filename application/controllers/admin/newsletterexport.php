<?php

class newsletterexport extends CI_Controller 
{
    public $data;        // Will be an array used to hold data to pass to the views.
    
    function __construct()
    {
        parent::__construct();
        
        // Create the data array.
        $this->data = array(); 
        
        $this->load->helper('image');
    }
    
    function index()
    {
        // Define page variables
        $this->data["meta_keywords"] = "";
        $this->data["meta_description"] = "";
        $this->data["meta_title"] = "HTML Newsletter Export";
        
        // Load Views
        $this->load->view('admin/header', $this->data);
        $this->load->view('admin/newsletterexport/prebody', $this->data); 
        $this->load->view('admin/newsletterexport/main', $this->data);
        $this->load->view('admin/pre_footer', $this->data); 
        $this->load->view('admin/footer', $this->data); 
    }
    
    function export()
    {
        $type = isset($_POST['type']) ? trim($_POST['type']) : '';
        
        switch ($type) {
        	case 'project':
        		$html = $this->_handleProject();
        		break;
        
        	case 'stocklist':
        	default:
        	    $html = $this->_handleStocklist();
        		break;
        }
//        echo $html;
//        exit();
        $this->output->set_header("HTTP/1.0 200 OK");
        $this->output->set_header("HTTP/1.1 200 OK");
        $this->output->set_header('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT');
        $this->output->set_header("Cache-Control: no-store, no-cache, must-revalidate");
        $this->output->set_header("Cache-Control: post-check=0, pre-check=0");
        $this->output->set_header("Pragma: no-cache");
        $this->output->set_header("Content-type: application/octet-stream");
        $this->output->set_header("Content-Disposition: attachment; filename=\"$type-newsletter-".date('YmdHis').".html\"");
        $this->output->set_output($html);
    }
    
    function _handleStocklist()
    {
        $filters = array();
		$featured = isset($_POST['featured']) ? true : false;
        $nras = isset($_POST['nras']) ? true : false;
        $smsf = isset($_POST['smsf']) ? true : false;
        $new = isset($_POST['new']) ? true : false;
        $status = isset($_POST['status']) ? $_POST['status'] : array();
        $state = isset($_POST['state']) ? intval($_POST['state']) : 0;
        $yield = isset($_POST['yield']) ? intval($_POST['yield']) : 0;
        $orderby = isset($_POST['orderby']) ? trim($_POST['orderby']) : 'state';
        switch ($orderby) {
            case 'state':
                $order_by = 'state_id';
                break;
			
			case 'yield':
                $order_by = 'rent_yield';
                break;		
				
            default:
                $order_by = 'title';
                break;
        }
		
        $this->data['heading'] = 'Aspire Network - Properties';
        
        if ($featured) {
        	$filters['featured'] = 1;
        	$this->data['heading'] = 'Aspire Network - Featured Properties';
        }
        if ($nras) {
        	$filters['nras'] = 1;
        }
        if ($smsf) {
        	$filters['smsf'] = 1;
        }
		if ($new) {
        	$filters['new'] = 1;
        }
		
        if ($state != 0) {
        	$filters['state_id'] = $state;
        }
		if ($yield != 0) {
        	$filters['rent_yield'] = $yield;
        }
		
        if (is_array($status) AND sizeof($status)) {
        	$filters['status'] = $status;
        }
        
        $filters['archived'] = 0;
		
        $this->load->model('property_model');
        $count_all = 0;
        $properties = $this->property_model->get_list($filters, $count_all, $order_by);
        
    	$this->data['properties'] = $properties ? $properties->result() : array();
    	$this->data['total_rows'] = ceil($count_all / 3);
        
        return $this->load->view('admin/newsletterexport/tpl_stocklist', $this->data, true);
    }
    
    function _handleProject()
    {
        $filters = array();
        $featured = isset($_POST['featured']) ? true : false;
        $state = isset($_POST['state']) ? intval($_POST['state']) : 0;
        $orderby = isset($_POST['orderby']) ? trim($_POST['orderby']) : 'state';
        switch ($orderby) {
            case 'state':
                $order_by = 'state_id';
                break;
            default:
                $order_by = 'project_name';
                break;
        }
        
        $this->data['heading'] = 'Aspire Network - Projects';
        
        if ($featured) {
        	$filters['is_featured'] = 1;
        	$this->data['heading'] = 'Aspire Network - Featured Projects';
        }
        if ($state != 0) {
        	$filters['state_id'] = $state;
        }
        
        $filters['archived'] = 0;
        $filters['has_available'] = true;

        $this->load->model('project_model');
        $count_all = 0;
        $projects = $this->project_model->get_list(1, '', '', $count_all, '', $order_by, $filters);  

    	$this->data['projects'] = $projects ? $projects->result() : array();
    	$this->data['total_rows'] = ceil($count_all / 3);
        
        return $this->load->view('admin/newsletterexport/tpl_project', $this->data, true);
    }
    
}