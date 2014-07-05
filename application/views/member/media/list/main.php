<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                <ul class="breadcrumbs">
                    <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                    <li>Resource Centre</li>
                </ul>                

                <div class="sidebar">
                    <?php echo form_open('media/ajax', array("id" => "frmSearch", "name" => "frmSearch", "class" => "block")); ?>
                        <h3>Search</h3>
                            <label for="search_term">Keywords</label>
                            <input type="text" name="search_term" value="" id="search_term" class="required" />
                            <input type="submit" value="search" />
                            
                            <div style="margin-top: 20px;"></div>
                            
                            <label for="category_id">Document Type</label> 
                            <select name="category_id" id="category_id" class="required">
                            <?php echo $this->utilities->print_select_options($document_types, "category_id", "name", $selected_val = "", $default_text = "Show All"); ?>
                            </select>
                            
                            <label for="state_id">Applicable State</label> 
                            <select name="state_id" id="state_id" class="required">
                            <?php echo $this->utilities->print_select_options($states, "state_id", "name", $selected_val = "", $default_text = "Show All"); ?>
                            </select>  
                            
                            <label for="areas">Applicable Area(s)</label> 
                            <div id="areaListing" class="hidden">
                                <?php if($areas) : ?>
                                    <?php foreach($areas->result() as $area) : ?>
                                <input type="checkbox" class="area" name="areas[]" value="<?php echo $area->area_id; ?>" /><?php echo $area->area_name; ?><br />
                                    <?php endforeach; ?>
                                <p style="padding: 5px 0;">
                                    <a href="javascript:void(0);" onclick="$('input.area').prop('checked', 'checked');">Select All</a> | 
                                    <a href="javascript:void(0);" onclick="$('input.area').removeAttr('checked');">Clear</a>
                                </p>
                            </div>
                            <a href="#" id="showHideAreas">Show Areas</a>
                            <?php endif; ?>
                            
                            <input type="submit" value="search" /> 
                            
                            <input type="hidden" id="count_all" name="count_all" value="0" />  
                            <input type="hidden" id="items_per_page" name="items_per_page" value="<?php echo MEDIA_PER_PAGE; ?>" />
                            <input type="hidden" id="current_page" name="current_page" value="1" />
                            <input type="hidden" id="action" name="action" value="load_media" />
                            <input type="hidden" id="sort_col" name="sort_col" value="" />
                            <input type="hidden" id="sort_dir" name="sort_dir" value="" />
                        </form>                                           
                <!-- end sidebar --></div>                
                
                <div class="mainCol">
                    <table cellpadding="0" cellspacing="0" class="listing articlelisting">
                        <thead>
                            <tr class="intro">
                                <td colspan="5">Media &amp; Resources</td>
                            </tr>
                            <tr>
                                <th width="5%"></th>
                                <th class="sortable" sort="a.article_title">Title</th>
                                <th class="sortable" sort="a.source">Source</th>
                                <th class="sortable" sort="ac.name">Document Type</th>
                                <th class="sortable" sort="a.article_date">Published</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table> 
                    
                    <?php echo form_open('media/ajax', array("id" => "frmDownload", "name" => "frmDownload")); ?>
                        <input type="hidden" id="download_action" name="action" value="download_media" />
                        <input type="hidden" id="download_article_id" name="article_id" value="" />
                    </form>
          
                </div>                
            </div><!-- end main content -->
            
            <div id="article_modal" class="reveal-modal">
                <a class="close-reveal-modal">&#215;</a>
                <div></div>
            </div>