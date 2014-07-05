<?php
class Document_model extends CI_Model 
{
    function Document_model()
    {
        // Call the Model constructor
        parent::__construct();
    }   
    
    function get_list($doc_type = "", $foreign_id = "", $params = array())                        
    {
        if($doc_type != "")
            $this->db->where("document_type",$doc_type);
            
        if($foreign_id != "")    
            $this->db->where("foreign_id",$foreign_id);        
            
        if(isset($params["broadcast_access_level_id"]) && $params["broadcast_access_level_id"] != "")    
        {
            $this->db->where("(
                    (is_exact = 1 && broadcast_access_level_id = ". $params["broadcast_access_level_id"] ." ) OR
                    (is_exact = 0 && broadcast_access_level_id <=". $params["broadcast_access_level_id"] ." )
                )");
        }
        
        if(isset($params["order"]) && $params["order"] != "")
        {
            $this->db->where("order", $params["order"]);
        }                             
         
        $query = $this->db->get("nc_documents");            
        
        if ($query->num_rows() > 0)
        {
            return $query;
        }         
        else
            return false;
        
    }
    
     /**
     * @desc The get_details method loads all properties of a particular property as defined by property_id
     */
    public function get_details($document_id)
    {
        $query = $this->db->get_where('nc_documents',array('id' => $document_id),1);        
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
    }
    
    public function save($doc_id,$data, $foreign_id = "", $document_type= "", $use_order = false)
    {           
        if (is_numeric($doc_id))
        {
            if($foreign_id != "")    
                $this->db->where("foreign_id",$foreign_id);
                
            $this->db->where('id',$doc_id);
            
            $this->db->update('nc_documents',$data);
            
            return $doc_id;
        }
        else
        {                                              
            if($use_order)
            {
                // select max product order
                $this->db->select( "max(`order`) as 'order'" );
                $this->db->from( 'nc_documents' );
                $this->db->where('foreign_id',$foreign_id);
                $this->db->where('document_type',$document_type);
                $query = $this->db->get();
               
                $max_element = $query->first_row();
                
                $data["order"] = ( $max_element->order + 1 );
            }
            
            $this->db->insert('nc_documents',$data);
            return $this->db->insert_id();                  
        }
    }  
    
    function delete($ids, $doc_type = '' )
    {
        if( $doc_type == '' )
            $this->db->where_in("id", $ids);
        else
        {
            $this->db->where('foreign_id', $ids);
            $this->db->where('document_type', $doc_type);
        }
        $this->db->delete("nc_documents");         
    }
    
    function add_default_documents($doc_type, $foreign_id)
    {           
        $sql= "
            INSERT INTO `nc_documents` (`document_type`,`foreign_id`, `document_name`, `document_no`, `document_path`) 
            SELECT '".$doc_type."', ".$foreign_id." ,`name`,`id`,'' FROM `nc_resources` where resource_type = \"".$doc_type."\"";
            
        $this->db->query($sql);                     
    }
    
    function get_document_path($doc_type, $foreign_id, $doc_name = "")
    {
        $this->db->select("document_path");
        
        if($doc_type != "")    
            $this->db->where("document_type",$doc_type);
            
        if($foreign_id != "")    
            $this->db->where("foreign_id",$foreign_id);
            
        if($doc_name != "")    
            $this->db->like("document_name",$doc_name,"left");
            
        $this->db->limit(1);    
            
        $query = $this->db->get("nc_documents");            
        
        // If there is a resulting row
        if ($query->num_rows() > 0)
        {
            $row = $query->row();
            return $row->document_path;
        }         
        else
            return "";
    }
    
    /**
     * @desc get all images from a document type 
     * @param $foreign_id     - int: foreign id
     * @return                 - array: document object
     */
    public function get_files( $document_type = "", $foreign_id = null, $order_by = 'order', &$count_all )
    {
        if( $foreign_id == null ) return FALSE;
        
        // count all images
        $this->db->from( 'nc_documents' );
        $this->db->where( 'document_type', $document_type );
        $this->db->where( 'foreign_id', $foreign_id );
        $count_all = $this->db->count_all_results();
        
        // get all images
        $this->db->from( 'nc_documents' );
        $this->db->where( 'document_type', $document_type );
        $this->db->where( 'foreign_id', $foreign_id );
        $this->db->order_by( $order_by, 'asc' );
        $query = $this->db->get();
        
        if( $query->num_rows() > 0 )
            return $query;
        else
            return FALSE;
    }
    
    /**
     * @desc change sort order for a product/article image ( move down or up )
     * @param     int        $doc_id           - id of document
     * @param     int        $direction        - the drection where to move the image
     *                                       this can be 1 or -1
     * @return
     */
    public function move_file( $doc_id = null, $direction = 1)
    {
        if($doc_id == null)
            return ;
            
        $query = $this->db->get_where('nc_documents', array('id' => $doc_id));
        $new_product = $query->first_row();
        
        // if order for new product order is 1 and we want to move up, return
        if($new_product->order == 1 && $direction == -1)
            return;
        
        // get the other product wich has the new product order number
        $this->db->select( '*' );
        $this->db->from( 'nc_documents' ); 
        $this->db->where( 'order', $new_product->order + $direction );
        $query = $this->db->get();

        //if exists a product with order number wich we want
        if( $query->num_rows() > 0 )
        {
            $old_product = $query->first_row();
            
            // set new product order for this item
            $old_product->order = $new_product->order;
            
            // save old item with new product order
            $this->db->where( 'id', $old_product->id );
            $this->db->update( 'nc_documents', $old_product );
        }
        
        // set the product order for new item
        $new_product->order += $direction;
        
        // save new product order for new item
        $this->db->where('id', $new_product->id);
        $this->db->update('nc_documents', $new_product);
    }
    
    
    /**
     * @desc delete one or more files 
     * @param $arr_files     - array: array with image names wich images will be delete
     * @param $foreign_id     - int: foreign id
     * @return 
     */
    public function delete_files($arr_names = array(), $foreign_id = null, $document_type = "")
    {
       if(count($arr_names)>0 && $foreign_id != null && $document_type != "")
        {
            foreach ( $arr_names as $image_name )
            {    
                // delete from database
                $this->db->where('foreign_id',$foreign_id);
                $this->db->where('document_type',$document_type);
                $this->db->where('document_name',$image_name);
                $this->db->delete('nc_documents');                
            }
        }
    }
    
    function get_floorplan_list($doc_type = "", $foreign_id = "", $show_empty_document_path = TRUE)                        
    {
        if($doc_type != "")
            $this->db->where("document_type", $doc_type);
            
        if($foreign_id != "")    
            $this->db->where("foreign_id", $foreign_id);        
            
        if($show_empty_document_path)    
            $this->db->where("document_path !=","");
            
        $this->db->like('document_name', 'Floorplan');     
         
        $query = $this->db->get("nc_documents");            
        
        if ($query->num_rows() > 0)
        {
            return $query->result();
        }         
        else
            return false;
        
    }
   
    function get_floorplan($doc_type = "", $foreign_id = "", $show_empty_document_path = TRUE)                        
    {
        if($doc_type != "")
            $this->db->where("document_type", $doc_type);
            
        if($foreign_id != "")    
            $this->db->where("foreign_id", $foreign_id);        
            
        if($show_empty_document_path)    
            $this->db->where("document_path !=","");
        if ($doc_type=='property_gallery') {
            $this->db->where_in('LOWER(extra_data)', array('floorplan','floor plan'));
        } else {
            $this->db->where_in('LOWER(document_name)', array('floorplan','floor plan'));
        }
         
        $query = $this->db->get("nc_documents");            
        
        if ($query->num_rows() > 0)
        {
            return $query->row();
        }         
        else
            return false;
        
    }
}