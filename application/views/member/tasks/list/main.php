<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                <ul class="breadcrumbs">
                    <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                    <li>My Tasks</li>
                </ul>                

                <div class="sidebar">
                    <?php echo form_open('tasks/ajax', array("id" => "frmSearch", "name" => "frmSearch", "class" => "block")); ?>
                        <h3>Search</h3>
                            <label for="searchTerm">Keywords</label>
                            <input type="text" name="search_term" value="" id="search_term" class="required" />
                            
                            <label for="status">Task Status</label>
                            <select id="status" name="status">   
                                <?php foreach($statuses as $value => $text) : ?>
                                <option value="<?=$value;?>"><?=$text;?></option>
                                <?php endforeach; ?>                                                                                                            
                                <option value="">Show All</option>
                            </select>                              
                            
                            <input type="submit" value="search" />
                            
                            <input type="hidden" id="sort_col" name="sort_col" value="" />
                            <input type="hidden" id="sort_dir" name="sort_dir" value="" />
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo TASKS_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_tasks" />
                        </form>                                           
                <!-- end sidebar --></div>                
                
                <div class="mainCol">
                    <table cellpadding="0" cellspacing="0" class="listing">
                        <thead>
                            <tr class="intro">
                                <td colspan="5">My Tasks<a href="<?php echo base_url(); ?>tasks/detail" class="btn arrow" id="btnAddTask">add new task</a></td>
                            </tr>
                            <tr>
                                <th class="sortable" sort="t.title">Task</th>
                                <th class="sortable" sort="u2.first_name">Assigned To</th>
                                <th class="sortable" sort="t.due_date">Due Date</th>
                                <th class="sortable" sort="t.priority">Priority</th>
                                <th class="sortable" sort="t.status">Status</th>
                                <th>Delete</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table>               
                </div>                
            </div><!-- end main content -->
            
            <!-- Task Detail Modal -->
            <div id="taskDetail" class="reveal-modal">
                 <h2>Task Details</h2>
                 <?php echo form_open('tasks/ajax', array("id" => "frmDetails", "name" => "frmDetails")); ?>
                 
                 	<?php
                    	$utid = $this->session->userdata["user_type_id"];
                    	$logged_user_id = $this->session->userdata["user_id"];
                	?>
                    <input type="hidden" name="action" value="update_task" />
                    <input type="hidden" id="task_id" name="task_id" value="" />
                    <input type="hidden" id="logged_user_id" value="<?php echo $logged_user_id;?>" />
                    
                    <fieldset>
                        <label for="title">Task Title <span class="required">*</span></label>
                        <input type="text" id="title" name="title" value="" class="required" />
                    
                        <label for="due_date">Due Date (dd/mm/yyyy)</label>
                        <input type="text" id="due_date" name="due_date" value="<?php echo date('d/m/Y')?>" readonly="readonly"/>
                        <input type="hidden" id="current_date" value="<?php echo date('d/m/Y')?>"/>
                        
                        <?php //if(in_array($utid, array(USER_TYPE_ADVISOR, USER_TYPE_PARTNER, USER_TYPE_INVESTOR))) : ?>
                        
                        <label for="assign_to">Assigned To</label>
                        <select name="assign_to" id="assign_to">
                        	<option value="">Choose</option>
                        	<?php echo $this->utilities->print_select_options($assign_users, "user_id", "assign_client_name", $logged_user_id);?>
                        </select>
                        
                        <?php //endif; ?>
                        
                        <label for="priority">Priority <span class="required">*</span></label>
                        <?php foreach($priorities as $priority_val => $priority_name) : ?>
                        <input type="radio" name="priority" value="<?php echo $priority_val; ?>" /> <?php echo $priority_name; ?><br>

                        <?php endforeach; ?>
                        
                        <div id="taskCompletedWrapper">
                            <label for="completed" >Task Completed</label>
                            <input type="checkbox" id="status" name="status" value="1" />                                            
                        </div>
                    </fieldset>
                    
                    <fieldset>
                        <label for="description">Task Description</label>
                        <textarea id="description" name="description"></textarea>                        
                    </fieldset>
                    
                    <p style="margin-top: 10px;">
                        <a class="btn inline" id="btnSaveTask">Save Task</a>&nbsp;
                        <!--<a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>-->
                    </p>
                    
                 </form>
                 <a class="close-reveal-modal">&#215;</a>
            </div>
            
            <!-- Task Delete Modal -->
            <div id="taskDelete" class="reveal-modal">
                 <h2>Delete Task</h2>
                 <?php echo form_open('tasks/ajax', array("id" => "frmDelete", "name" => "frmDelete")); ?>
                    <input type="hidden" name="action" value="delete_task" />
                    <input type="hidden" id="task_id" name="task_id" value="" />
                    
                    <p class="confirmMessage">You are about to delete the task "[TASKNAME]".  Are you sure you wish to continue?</p>
                    
                    <p style="margin-top: 10px;">
                        <a class="btn inline" id="btnDeleteTask">Yes, Confirm Delete</a>&nbsp;
                        <a class="btn secondary inline" href="#" onclick="$(this).trigger('reveal:close')">no, cancel</a>
                    </p>
                 
                 </form>
                 <a class="close-reveal-modal">&#215;</a>
            </div>            
            
            <?php echo form_open('tasks/ajax', array("id" => "frmLoad", "name" => "frmLoad")); ?>
                <input type="hidden" name="action" value="load_task" />
                <input type="hidden" id="task_id" name="task_id" value="" />                        
            </form>