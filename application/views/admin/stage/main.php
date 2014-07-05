<body id="contact" >   
    <div id="wrapper">
        <? $this->load->view("admin/navigation",array('property_id'=>$property_id));?>
        <div id="content">
            <? $this->load->view("admin/stage/navigation"); ?>                        

<form class="plain" id="frmStage" name="frmStage" action="<?=base_url()?>admin/propertymanager/stage/<?=$stage_id?>#tabStage"  method="post">
    <input type="hidden" id="stage_id" value="<?=$stage_id;?>" />

    <h2>Stage Details</h2><br />

    <p><input id="button" type="submit" value="Update Stage" /></p>

    <!-- tabs -->
    <ul class="css-tabs skin2">
        <li><a href="#">Details</a></li>
        <li><a href="#" id="tabDocument">Documents</a></li>
        <li><a href="#" id="tabGallery">Gallery</a></li>                
    </ul>   

    <!-- panes -->
    <div class="css-panes skin2">
        <div style="display:block">
            <div class="left">    <?php //print_r($stage); die(); ?>

                <label for="stage_name">Stage Name: <em style="font-style:italic;"><? echo ($stage_id !="") ? $stage->stage_name : "" ?></em></label>
                <br />
                <label for="datetime_completed">Date Completed:</label>
                <input id="datetime_completed" type="text" value="<? echo ($stage_id !="" && !empty($stage->datetime_completed)) ? $this->utilities->iso_to_ukdate($stage->datetime_completed) : "" ?>" name="datetime_completed" class="date-pick" readonly="readonly"/>
                
                <br />
                <br />
                <label for="stage_status">Status:</label>
                <?php 
                    echo form_dropdown('stage_status', $status_arr, $stage->status,  ' class="short" id="stage_status" ' );  
                ?>
                
                <?php if($stage->status != "completed") : ?>
                <br /><br />
                <label for="next_followup_date">Next Followup Date:</label>
                <input id="next_followup_date" type="text" value="<? echo ($stage_id !="" && !empty($stage->next_followup_date)) ? $this->utilities->iso_to_ukdate($stage->next_followup_date) : "" ?>" name="next_followup_date" class="date-pick" readonly="readonly"/>                
                
                <br /><br />      
                <label for="next_followup_comments">Next Followup Comments:</label>
                 <?php echo form_textarea(array('name'=>'next_followup_comments', 'id'=>'next_followup_comments', 'rows' => '5', 'cols' => '100'), ifvalue($stage, 'next_followup_comments', ''),'style="width: 300px"'); ?>                
                <?php endif; ?>
                
                <br /><br />
                <label for="stage_public">Public:</label>
                <?php 
                    echo form_dropdown('stage_public', $public_arr, $stage->public,  ' class="short" id="stage_public" ' );  
                ?>        
                  
                <br /><br />      
                <label for="stage_note">Note:</label>
                 <?php echo form_textarea(array('name'=>'stage_note', 'id'=>'stage_note', 'rows' => '5', 'cols' => '100'), ifvalue($stage, 'comments', ''),'style="width: 300px" maxlength ="250"'); ?>
                 
                <table cellspacing="0" width="100%" class="left commentlisting top-margin20">
                    <thead>
                        <tr>
                            <th align="left">Note</th>
                            <th width="20%">Date</th>
                        </tr>
                    </thead>
                    <tbody id="notelist">
                        <?php $this->load->view('admin/property/note_list')?>
                    </tbody>
                </table>                 
                
                 <p><input type="button" value="Add New Note" id="addnotebtn" class="button" /></p>
                <div id="formnewcomment" style="display:none;">
                    <input type="hidden" name="property_id" value="<?php echo $property_id?>" />
                    <label for="comment">Note:<span class="requiredindicator">*</span></label>
                    <textarea id="comment" style="width:400px;"></textarea>
                     <label for="note_date">Date:<span class="requiredindicator">*</span></label>
                    <input type="text" readonly="readonly" class="date-choose" value="" id="note_date" name="note_date" />                
                    <input type="hidden" id="comment_id"/>
                    <br /><br />
                    <label>View permission:</label>
                    <label for="advisor">Advisor
                    <input type="checkbox" value="<?=USER_TYPE_ADVISOR?>" name="view[]" id="advisor" /></label>
                    <label for="partner">Partner
                    <input type="checkbox" value="<?=USER_TYPE_PARTNER?>" name="view[]" id="partner" /></label>
                    <label for="investor">Investor
                    <input type="checkbox" value="<?=USER_TYPE_INVESTOR?>" name="view[]" id="investor" /></label> 
                    
                    <div class="clear"></div><br />
                    <input type="button" value="Save" id="savenotebtn" class="button" />
                </div>
                
            </div>
            <div class="clear"></div>
        </div>

        <div class="upload_documents">

            <div class="right" style="padding-right:10px">

                <label for="upload_document">Upload a new document</label>
                <!--<input type="file" name="upload_file" id="upload_file" />    -->
                <div id="upload_document"></div>

            </div>

            <span class="clear"></span>

            <label>Stage Documents</label>

            <div id="documents_listing">

                <div id="page_doc_listing">
                    <? $this->load->view('admin/stage/document_listing',array('files'=>$documents,'pages_no' => count($documents) / $documents_records_per_page)); ?>
                </div>

                <div class="clear"></div>            

                <div id="controls">
                    <div class="right">
                        <input class="button" type="button" value="Delete Selected Files" id="delete_doc_files" />
                    </div>
                </div>    

                <div class="clear"></div>

            </div>

            <div class="clear"></div>

        </div><!-- END fourth tab -->

        <div>

            <div class="right" style="padding-right:10px">

                <label for="upload_file">Upload a new image</label>
                <!--<input type="file" name="upload_file" id="upload_file" />    -->
                <div id="upload_file"></div>

            </div>

            <span class="clear"></span>

            <label>Stage Images</label>

            <br/>                

            <div id="files_listing">

                <div id="page_listing">
                    <? $this->load->view('admin/stage/stage_file_listing',array('files'=>$images,'pages_no' => count($images) / $images_records_per_page)); ?>
                </div>

                <div class="clear"></div>            

                <div id="controls">
                    <div class="right">
                        <input class="button" type="button" value="Delete Selected Files" id="delete_files" />
                    </div>                
                </div>    

                <div class="clear"></div>

            </div>            

        </div><!-- END fifth tab -->

    </div>        


    <div class="clear"></div>
    <br/>
    <br/>
    <label for="heading">&nbsp;</label> 
    <input id="button" type="submit" value="Update Stage" /><br/>                
    <input type="hidden" name="postback" value="1" />
    <input type="hidden" name="id" value="<?=$stage_id?>" />
</form>
<br/>

<? $this->load->view("admin/stage/navigation"); ?>