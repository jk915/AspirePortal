<?php
/**
* @property CI_Loader $load
* @property CI_Form_validation $form_validation
* @property CI_Input $input
* @property CI_Email $email
* @property CI_DB_active_record $db
* @property CI_DB_forge $dbforge
*/
class History_model extends CI_Model 
{
    function History_model()
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
    public function get_list($table = "", $history_field = "", $limit = "", $page_no = "", &$count_all, $id = "", $foreign_id = "")
    {
        $this->_get_list($table, $history_field, $limit, $page_no, $count_all, $id,true, $foreign_id);
        $count_all = $this->db->count_all_results();
        
        $this->_get_list($table, $history_field, $limit, $page_no, $count_all, $id, false, $foreign_id);
        $query = $this->db->get();
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;        
    } 
    
    function _get_list($table = "", $history_field = "", $limit = "", $page_no = "", $count_all, $id = "", $only_count = false, $foreign_id = "")   
    {
        $this->db->from('nc_history');
        
        if($table != "")
            $this->db->where("table", $table);
        
        if($history_field != "")    
            $this->db->where("field", $history_field);
            
        if($id != "" && is_numeric($id))    
            $this->db->where("id", $id);
            
        if($foreign_id != "" && is_numeric($foreign_id))    
            $this->db->where("foreign_id", $foreign_id);
        
        $this->db->order_by("date", "desc");     
        
        if ($limit != "" && $page_no!= "" && $count_all > $limit)
        {
            $this->db->limit(intval($limit), intval(($page_no-1) * $limit));
        }    
    }
    
    /**
    * @desc The get_details method loads all history of a particular table and a page/article ID as defined by table and foreign_id
    */
    public function get_details($table = "", $foreign_id = "")
    {
        if($table != "")
            $this->db->where("table", $table);
            
        if($foreign_id != "")    
            $this->db->where("foreign_id", $foreign_id);
            
        $query = $this->db->get('nc_history');

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
        $this->db->insert("nc_history", $data);
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
    
}  
?>
