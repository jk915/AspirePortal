<body id="contact" >   
    <div id="wrapper">
        
        <? $this->load->view("admin/navigation");?>
        
        <div id="content">

            <? $this->load->view("admin/menumanager/navigation"); ?>            
            <? if($message!=""):?>
                <p><?=$message?></p>  
            <? endif; ?>  
        
   <form class="plain" action="#">
        <div class="right" style="padding-right:10px;">                                                      
             <label for="add_menu_item">Add Menu Item:</label>
             <input type="text" name="add_menu_item" id="add_menu_item" />                        
             <input class="button" type="button" value="Submit" id="add_menu_item_btn" />
        </div>
        <div class="clear"></div>        
        <div class="left">
            
            <h2 class="left">Menus</h2>
            <a id="refresh_menus" href="javascript:void(0)" class="left">
                <img  src="<?=base_url()?>images/admin/refresh.png" />
            </a>

            <br class="clear"/>
            <select id="menus" name="menu" size="10">               
                <? if($menus) :?>
                    <?=$this->utilities->print_select_options($menus,"menu_id","menu_website",$selected_menu); ?>
                <? endif; ?>
            </select>                
            <br/><br/>
            <img src="<?=base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add Menu." /> <a id="addmenu" href="javascript:void(0)">Add New Menu</a>
            <div class="clear"></div>
            <!-- <img src="<?php //echo base_url();?>images/admin/i_add.png" border="0" width="16" height="18" alt="Add Menu." /> <a id="clonemenu" href="javascript:void(0)">Clone Selected Menu</a> -->
            <div class="clear"></div>
            <img src="<?=base_url();?>images/admin/i_trashcan.png" border="0" width="16" height="18" alt="Delete Menu." /> <a id="deletemenu" href="javascript:void(0)">Delete Menu</a>
            <br />
            <br />
            <div id="new_menu_div" style="display:none">
                
                <label for="new_menu">New Menu Name:</label>
                <input type="text" id="new_menu" />     
                
                <label for="website_id">Select Website:</label>
                <?php 
                    if(isset($websites) && $websites)
                    {
                        ?>                  
                        <select id="website_id" name="website_id">
                            <?php echo $this->utilities->print_select_options($websites, "website_id", "website_name"); ?>
                        </select>
                        <?php
                    }
                ?>
                <br/>
                <br/>
                <input type="button" id="save" value="Add Menu" class="smallbutton" />
                <input type="button" id="cancel" value="Cancel" class="smallbutton" />
            </div>
        </div>
                    
        <div class="left left-margin" style="width:630px">
            <h2 class="left" style="width: 150px;">Menu items</h2>
            
            
            <a id="refresh_items" href="javascript:void(0)" class="left">
                <img src="<?=base_url()?>images/admin/refresh.png"  />
            </a>
            
            <div style="clear:both"></div>
            <div id="files_listing">
                   <? $this->load->view('admin/menumanager/menuitem_listing',array('menu_items'=>$menu_items)); ?>
            </div>
                    
        </div>
        
        <div class="clear"></div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Delete Selected Menu" id="delete_files" />
            </div>
            <div class="clear"></div>
            <br/>            
        </div>    
                        
   </form>


<? $this->load->view("admin/menumanager/navigation"); ?>
