            <div class="right">
                
                <?php
                    $show_history = false;
                    if (isset($foreign_id) && is_numeric($foreign_id))
                    {
                        $count_all = 0;
                        $history = $this->history_model->get_list($table, "", "", "", $count_all,"", $foreign_id);
                        $show_history = ($history && $history->num_rows() > 0);
                        
                        if( $name == "block_content" )
                        {
                        	//$show_history = false;
                        }
                    }
                    
                ?>
                <?php 
                if ($show_history)
                {
                    ?>
                        <a href="<?php echo $name;?>" class="view_history"><input type="button" value="View History" id="button" /></a>    
                    <?php
                }
                ?>
                 
                <?php 
                if( !isset($show_preview) || (isset($show_preview) && $show_preview) ) 
                { 
                ?>    
                    <a href="preview-<?php echo $name;?>" class="view_preview">
                	    <input type="button" value="Preview" class="button" style="width: 100px;" />
                    </a>
                <?php
                }
                ?>
                <input type="hidden" id="table" value="<?php echo $table;?>" />  
                <input type="hidden" name="ckeditor_<?php echo $name;?>" />
                
            </div>
            <div class="clear"></div>
            <br/>    
            
            <div>
                <textarea id="<?php echo $id;?>" cols="20" rows="10" name="<?php echo $name;?>" style="width:880px;height:300px" class="editor"><? echo $content; ?></textarea>        
            </div>
            <div class="clear"></div>