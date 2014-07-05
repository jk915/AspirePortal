<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                <ul class="breadcrumbs">
                    <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                    <li>My Partners</li>
                </ul>                

                <div class="sidebar">
                    <?php echo form_open('partners/ajax', array("id" => "frmSearch", "name" => "frmSearch", "class" => "block")); ?>
                        <h3>Search</h3>
                            <label for="searchTerm">Keywords</label>
                            <input type="text" name="search_term" value="" id="search_term" class="required" />
							<label for="user_status">User Status:</label> 
								<select id="user_status" name="user_status">
								<option value="">Choose</option>
								<option value="active">Active</option>
								<option value="inactive">In-active</option>
								</select>
                            <input type="submit" value="search" /> 
                            
                            <input type="hidden" id="sort_col" name="sort_col" value="" />
                            <input type="hidden" id="sort_dir" name="sort_dir" value="" />                              
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo PARTNERS_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_partners" />
                        </form>                                           
                <!-- end sidebar --></div>                
                
                <div class="mainCol">
                    <table cellpadding="0" cellspacing="0" class="listing">
                        <thead>
                            <tr class="intro">
                                <td colspan="5">My Partners<a href="<?php echo base_url(); ?>partners/detail" class="btn arrow">add new partner</a></td>
                            </tr>
                            <tr>
                            	<th class="sortable" sort="u.first_name">Contact</th>
                                <th class="sortable" sort="u.company_name">Company</th>
                                <th class="sortable" sort="u.mobile">Mobile Phone</th>
                                <?php echo (in_array($user_type_id, array(USER_TYPE_ADVISOR)))?"<th class='sortable' sort='notes_last_created'>Last Note</th>":"<th class='sortable' sort='u.last_sold_date'>Last Sale</th>";?> 
								
                                <th>No. Sales</th>
                                <th class="sortable" sort="days_since_login">Last Login</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table>               
                </div>                
            </div><!-- end main content -->