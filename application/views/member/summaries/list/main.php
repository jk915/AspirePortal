<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">             

                <div class="sidebar">
                    <?php echo form_open('summaries/ajax', array("id" => "frmSearch", "name" => "frmSearch", "class" => "block")); ?>
                        <h3>Search</h3>
                            <label for="searchTerm">Keywords</label>
                            <input type="text" name="search_term" value="" id="search_term" class="required" />                          
                            
                            <input type="submit" value="search" />
                            
                            <input type="hidden" id="sort_col" name="sort_col" value="" />
                            <input type="hidden" id="sort_dir" name="sort_dir" value="" />
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo SUMMARIES_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_summaries" />
                        </form>                                           
                <!-- end sidebar --></div>                
                
                <div class="mainCol">
                    <table cellpadding="0" cellspacing="0" class="listing">
                        <thead>
                            <tr class="intro">
                                <td colspan="7">My summaries<a href="<?php echo base_url(); ?>summaries/detail" class="btn arrow" id="btnAddSummary">add new summary</a></td>
                            </tr>
                            <tr>
                                <th class="sortable" sort="s.created_date">Date</th>
                                <th class="sortable" sort="s.title">Description</th>
                                <th class="sortable" sort="st.name">State</th>
                                <th class="sortable" sort="a.area_name">Area</th>
                                <th class="sortable" sort="p.project_name">Project</th>
                                <th class="sortable" sort="s.prepared_for">Prepared for</th>
                                <!--
                                <th>Delete</th>
                                -->
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table>               
                </div>                
            </div><!-- end main content -->
            
            <!-- Summary Detail Modal -->
            <!-- Summary Delete Modal -->
                        
            
            <?php echo form_open('summaries/ajax', array("id" => "frmLoad", "name" => "frmLoad")); ?>
                <input type="hidden" name="action" value="load_summary" />
                <input type="hidden" id="summary_id" name="summary_id" value="" />                        
            </form>