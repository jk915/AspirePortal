<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                <ul class="breadcrumbs">
                    <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                    <li>My Investors</li>
                </ul>                

                <div class="sidebar">
                    <?php echo form_open('investors/ajax', array("id" => "frmSearch", "name" => "frmSearch", "class" => "block")); ?>
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
                            <input type="hidden" id="action" name="action" value="load_investors" />
                        </form>                                           
                <!-- end sidebar --></div>                
                
                <div class="mainCol">
                    <table cellpadding="0" cellspacing="0" class="listing">
                        <thead>
                            <tr class="intro">
                                <td colspan="5">My Investors
                                <!--
                                <a href="<?php echo base_url(); ?>investors/detail" class="btn arrow">add new investor</a></td>
                                -->
                            </tr>
                            <tr>
                                <th class="sortable" sort="u.first_name">Contact</th>
                                <th class="sortable" sort="u.mobile">Mobile Phone</th> 
                                <th class="sortable" sort="u.company_name">Day's</th>
								<th class="sortable" sort="u.company_name">Last Note</th>
								<th class="sortable" sort="u.company_name">Lead Source</th>
								<th class="sortable" sort="u.status">Status</th>
                                <th class="sortable" sort="days_since_login">Last Login</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table>               
                </div>                
            </div><!-- end main content -->