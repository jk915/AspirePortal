<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                <ul class="breadcrumbs">
                    <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                    <li>My Contacts</li>
                </ul>                

                <div class="sidebar">
                    <?php echo form_open('contacts/ajax', array("id" => "frmSearch", "name" => "frmSearch", "class" => "block")); ?>
                        <h3>Search</h3>
						
						<label for="searchTerm">Keywords</label>
                            <input type="text" name="search_term" value="" id="search_term" class="required" />
                            <label for="contact_type">Contact Type:</label> 
								<select id="contact_type" name="contact_type">
								<option value="">Choose</option>
								
								<?php echo $this->utilities->print_select_options($contact_types, "contact_type_name", "contact_type_name",$builder->contact_type); ?> 
								</select>
                            <div id="statusOptions" class="hidden">
							    <label for="lead_status">Status</label>
                                <?php echo form_dropdown_lead_status('lead_status', '', 'id="lead_status"', true); ?>
                            </div>
                            <a href="#" id="viewStatusOptions">View Options</a>
                            
                            <input type="submit" value="search" /> 
                            
                            <input type="hidden" id="sort_col" name="sort_col" value="" />
                            <input type="hidden" id="sort_dir" name="sort_dir" value="" />                            
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo PARTNERS_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_contacts" />
                        </form>                                           
                <!-- end sidebar --></div>                
                
                <div class="mainCol">
                    <table cellpadding="0" cellspacing="0" class="listing">
                        <thead>
                            <tr class="intro">
                                <td colspan="5">My Contacts<a href="<?php echo base_url(); ?>contacts/detail" class="btn arrow">add new contact</a></td>
                            </tr>
                            <tr>
                                <th class="sortable" sort="u.name">Name</th>
                                <th class="sortable" sort="u.company_name">Company</th>
                                <th class="sortable" sort="u.state">State</th>
								<th class="sortable" sort="u.contact_type">Contact Type</th>
								<th class="sortable" sort="mobile">Mobile</th>
								
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table>
                </div>                
            </div><!-- end main content -->