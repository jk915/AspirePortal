<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
*/
class Item_history_model extends CI_Model 
{
    function Item_history_model()
    {
        // Call the Model constructor
          parent::__construct();
    }                         

    /***
    * @method get_list
    * @abstract This metod gets a list of all history from the database. 
    *                                 
    * @returns A list of page or article history 
    */
    public function get_list($limit = "", $page_no = "", &$count_all, $id = "", $foreign_id = "", $order_by = "")
    {
        $this->_get_list($limit, $page_no, $count_all, $id,true, $foreign_id);
        $count_all = $this->db->count_all_results();
        
        $this->_get_list($limit, $page_no, $count_all, $id, false, $foreign_id, $order_by);
        $query = $this->db->get();
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;        
    } 
    
    function _get_list($limit = "", $page_no = "", $count_all, $id = "", $only_count = false, $foreign_id = "", $order_by = "")   
    {
		$select_str = "h.*,"."TRIM(CONCAT( u.first_name , ' ' , u.last_name )) as user_name";
        $this->db->select($select_str,FALSE);
		$this->db->from('nc_item_history h');
        $this->db->join('users u', 'h.user_id = u.user_id','left');
            
        if($id != "" && is_numeric($id))    
            $this->db->where("id", $id);
            
        if($foreign_id != "" && is_numeric($foreign_id))    
            $this->db->where("foreign_id", $foreign_id);
        
        if($order_by != '')
            $this->db->order_by($order_by);   
        
        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }    
    }
    
    /**
    * @desc The get_details method loads all history of a particular table and a page/article ID as defined by table and foreign_id
    */
    public function get_details($foreign_id = "")
    {
        // if($table != "")
            // $this->db->where("table", $table);
            
        if($foreign_id != "")    
            $this->db->where("foreign_id", $foreign_id);
            
        $query = $this->db->get('nc_item_history');

        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    /**
    * @desc The save method insert a new history 
    */
    public function save($data)
    {
        //$this->db->insert_string('nc_history',$data);
        $this->db->insert("nc_item_history", $data);
        return $this->db->insert_id();        
    }
    
    /**
    * @desc The update method update a history 
    */
    public function update($table, $foreign_id, $type, $content)
    {
        $foreign_id_column = "";
        
        switch($table)
        {
            case "pages":
                $foreign_id_column = "page_id";
                break;
            case "articles":
                $foreign_id_column = "article_id";    
                break;
            case "products":
                $foreign_id_column = "product_id";    
                break;   
            case "custom_blocks":
                $foreign_id_column = "block_id";    
                break;                              
            default:
                show_error("History_model::update - unhandled table: $table");   
                break;                                                                        
        }
        
        if($foreign_id_column != "")
            $this->db->where($foreign_id_column, $foreign_id);
        
        $this->db->set($type, $content);    
        $this->db->update($table);
        
        //$this->utilities->add_to_debug("Query: " . $this->db->last_query());
        return true;
    }
    
    function save_history($table, $foreign_id)
    {
        
        //echo "<pre>"; print_r($_POST); echo "</pre>";   
        if(isset($_POST))
        {
            
            //find the ckeditor's from page
            foreach($_POST as $key => $value)
            {
                
                $pos = strpos(strtolower($key), "ckeditor_");
                if($pos !== false)
                {
                    $fckeditor_name = substr($key, 9, strlen($key));
                    
                    if(isset($_POST[$fckeditor_name]) && strlen($_POST[$fckeditor_name]) > 10 )    
                    {
                        $history_data = array(
                            "table" => $table,
                            "foreign_id" => $foreign_id,
                            "content" => $this->input->post($fckeditor_name),
                            "field" => $fckeditor_name                 
                        );
                        
                        
                        $this->save($history_data); 
                        
                    }                
                }
            } 
        }       
    }
    
    //Get the user who change property to EOI Payment Pending, Sold, Reserve, Signed
    function get_previous_user($property_id = '')
    {
        if($property_id == '')
            return false;
    
        $where = "(foreign_id = '".$property_id."') AND (new_value = 'Reserved' OR new_value = 'EOI Deposit Pending' OR new_value = 'Signed' OR new_value = 'Sold')";
        $this->db->where($where);
        // $this->db->where('foreign_id', $property_id);
        // $this->db->where('new_value', 'Reserved');
        // $this->db->or_where('new_value', 'EOI Deposit Pending');
        // $this->db->or_where('new_value', 'Signed');
        // $this->db->or_where('new_value', 'Sold');
        $this->db->order_by('created_dtm DESC');
        $query = $this->db->get('item_history');
        
        // echo $this->db->last_query();die;
        
        if ($query->num_rows() > 0)
        {
           return $query->row();
        }         
        else
            return false;
    }
    
    function get_all_crashed_sales($filter = array(), $order_by = 'created_dtm DESC')
    {
        //get all user company
        if(isset($filter['company_id_search']))
            $this->db->where('user_id', $filter['company_id_search']);
        $this->db->where('user_type_id', USER_TYPE_COMPANY);
        $query = $this->db->get('users');
        
        
        if ($query->num_rows() > 0)
        {
           $result = array();
           foreach($query->result() as $company)
           {
                $rs = $this->get_crashed_sales($company->user_id, $filter);
                if($rs) {
                    foreach($rs as $r)
                    {
                        array_push($result, $r);
                    }
                }
           }
           
           uasort($result, strtolower(str_replace(' ', '_', $order_by)));
           
           return $result;
        }         
        else
            return false;
    }
    
    
    function get_crashed_sales($user_id = '', $filter = array())
    {
        if($user_id == '')
            return false;
        
        $this->load->model("property_model");
        
        $arr_users = array();
        //check type user
        $user = $this->users_model->get_details($user_id);
        array_push($arr_users, $user);
        if($user->user_type_id == USER_TYPE_COMPANY)
        {
            $company = $user;
            //get all staff of this company
            $staffs = $this->users_model->get_staff_of_company($user_id);
            if($staffs) {
                foreach($staffs as $staff)
                {
                    array_push($arr_users, $staff);
                }
            }
        }
        else
        {
            $company = $this->users_model->get_details($user->company_id);
        }
        
        $where = '(';
        foreach($arr_users as $user)
        {
            $where .= "nc_item_history.user_id = ".$user->user_id.' OR ';
        }
        $where = substr($where, 0, strlen($where) - 3 );
        $where .= ')';
        $where .= " AND (new_value = 'Reserved' OR new_value = 'EOI Deposit Pending' OR new_value = 'Signed' OR new_value = 'Sold')";
        
        if(isset($filter['date_from']))
        {
            $date_from = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $filter['date_from'])));
            $where .= " AND (created_dtm >= '".$date_from."')";
        }
        
        if(isset($filter['date_to']))
        {
            $date_to = date('Y-m-d H:i:s', strtotime(str_replace('/', '-', $filter['date_to'])));
            $where .= " AND (created_dtm <= '".$date_to."')";
        }
        
        $this->db->where($where);
        $this->db->where("foreign_type", "property");
        $this->db->join("properties", "item_history.foreign_id = properties.property_id", "inner");
        $this->db->order_by('created_dtm DESC');
        $query = $this->db->get('item_history');
        
        $result = array();
        
        if ($query->num_rows() > 0)
        {
           $save_list_q = array();
           foreach($query->result() as $history)
           {
                //check if there is a change of admin
                $this->db->where('foreign_id', $history->foreign_id);
                $this->db->where('new_value', 'Available');
                $this->db->where('reason !=', '');
                $this->db->where('created_dtm >', $history->created_dtm);
                $this->db->order_by('created_dtm ASC');
                
                $q = $this->db->get('item_history');
                
                if($q->num_rows() > 0)
                {
                    if(!in_array($q->row()->id, $save_list_q))
                    {
                        $save_list_q[] = $q->row()->id;
                        
                        $reason = $q->row()->reason;
                        $r['reserve'] = $history;
                        $r['reason'] = $reason;
                        $r['company'] = $company;
                        $r['staff_member'] = $this->users_model->get_details($history->user_id);
                        $r['property'] = $this->property_model->get_details($history->foreign_id);
                        
                        $result[] = $r;
                    }
                }
           }
        }
        else
            return false;
        
        return $result;
    }
}  
?>
