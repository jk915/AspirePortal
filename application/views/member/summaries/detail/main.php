<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">
                
                    <?php 
                        $user_id = $this->session->userdata["user_id"];
                        $utid = $this->session->userdata["user_type_id"];
                    ?>                

                    <ul class="breadcrumbs">
                        <li><a href="<?php echo base_url(); ?>summaries">Summaries</a></li>
                        <li>Summary Detail</li>
                    </ul>
                    
                    
					
					<div class="sidebar">
                        
                        <?php if(isset($summary)):?>
                        <h3>Actions</h3>                    
                        <ul>
                            <li><a href="javascript:;" data-reveal-id="deleteSummaryConfirm">Delete Summary</a></li>                     
                            <li><a href="javascript:;" id="createDuplicate">Create Duplicate</a></li>                     
                        </ul>
                        <?php endif;?>
                    </div>                
                    
                    <div class="mainCol"> 
                        <?php echo form_open('summaries/ajax', array("id" => "frmSummaryDetail", "name" => "frmSummaryDetail", "class" => "block")); ?>
                            <input type="hidden" id="summary_id" name="summary_id" value="<?php isset($summary) ? echoifobj($summary, "summary_id") : ""; ?>" />
                            <input type="hidden" id="action" name="action" value="update_summary" />
                            <h3>Summary Detail</h3>  
                            
                            <div class="error"><h4>Please complete the following fields before submitting:</h4></div> 
                            <div class="success"><h4>Your Summary's information was updated successfully.</h4></div>
                            <fieldset>    
                                
                                <label for="title">Title</label>
                                <input type="text" name="title" value="<?php echo isset($summary) ? $summary->title : ""; ?>" id="title" class="title"  />                                
                                
                                <label for="state_id">State</label>
                                <select name="state_id" id="state_id">
                                    <option value="">Choose</option>
                                    <?php echo $this->utilities->print_select_options($states, "state_id", "name", ($summary) ? $summary->state_id : ""); ?>
                                </select>

                                <label for="area_id">Area</label>
                                <select name="area_id" id="area_id">
                                    <option value="">Choose</option>
                                    <?php echo $this->utilities->print_select_options(isset($areas) ? $areas : array(), "area_id", "area_name", isset($summary) ? $summary->area_id : ""); ?>
                                </select>
                                
                                <label for="project_id">Project</label>
                                <select name="project_id" id="project_id">
                                    <option value="">Choose</option>
                                    <?php echo $this->utilities->print_select_options($projects, "project_id", "project_name", isset($summary) ? $summary->project_id : ""); ?>
                                </select>
                                
                                
                            </fieldset> 
                            <fieldset>                          
                            </fieldset>
                            
                            <div class="clear"></div>
                            
                            <label for="description">Description</label>
                            <textarea id="description" name="description" cols="30" rows="6" class="fullwidth"><?php echo isset($summary) ? $summary->description : ""; ?></textarea>
                            
                            
                            <label for="prepared_for">Prepare for</label>
                            <select name="prepared_for" id="prepared_for">
                                <option value="">Choose</option>
                                <?php //echo $this->utilities->print_select_options($partners, "first_name,last_name", "first_name", isset($summary) ? $summary->prepared_for : ""); ?>
                                <?php echo $this->utilities->print_select_options($investors, "first_name,last_name", "first_name", isset($summary) ? $summary->prepared_for : ""); ?>
                                <?php echo $this->utilities->print_select_options($enquiries, "first_name,last_name", "first_name", isset($summary) ? $summary->prepared_for : ""); ?>
                            </select>
                            
                            <input type="text" id="prepared_for_manual" name="prepared_for_manual" style="display: none;"/>
                            <input type="checkbox" id="manual_type" name="manual_type"/> Other
                            
                            <div class="clear"></div>                            
                            
                            <p><a href="<?php echo base_url(); ?>summaries">&laquo; Back</a></p>
                            <input type="submit" value="save changes" />                           
                        </form>
                    </div>                
                </div><!-- end main content -->
                
                
                
                <div id="deleteSummaryConfirm" class="reveal-modal">
                     <h2>Confirmation Required</h2>
                     <p>Are you sure you want to delete this Summary? This action is not reversible.</p>
                     <p>
                        <div class="error delete_error"><h4>Please complete the following fields before submitting:</h4></div>
                        <a class="btn inline delete_summary" href="javascript:;" summaryid="<?php isset($summary) ? echoifobj($summary, "summary_id") : ""; ?>" action="<?php echo site_url('summaries/ajax')?>" action_name="delete_summary">Yes, delete this summary</a>
                        &nbsp;<a class="btn secondary inline close-reveal" href="javascript:;">no, cancel</a>
                     </p>
                     <a class="close-reveal-modal">&#215;</a>
                </div>