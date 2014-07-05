<body class="partners">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">

                <ul class="breadcrumbs">
                    <li><a href="<?php echo base_url(); ?>dashboard">Dashboard</a></li>
                    <li>Announcements</li>
                </ul>                

                <div class="sidebar">
                    <?php echo form_open('announcements/ajax', array("id" => "frmSearch", "name" => "frmSearch", "class" => "block")); ?>
                        <h3>Search</h3>
                            <label for="search_term">Keywords</label>
                            <input type="text" name="search_term" value="" id="search_term" class="required" />
                            
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
                    <table cellpadding="0" cellspacing="0" class="listing articlelisting announcementlist">
                        <thead>
                            <tr class="intro">
                                <td colspan="5">Important Information</td>
                            </tr>
                            <tr>
                                <th class="sortable" sort="a.article_date">Date</th>
                                <th class="sortable" sort="a.article_title">Title</th>
								<th class="sortable" sort="a.article_title">Author</th>
                            </tr>
                        </thead>
                        <tbody>
                        <!-- Listing will load here via AJAX -->
                        </tbody>
                    </table> 
                    
                    <?php echo form_open('announcements/ajax', array("id" => "frmDownload", "name" => "frmDownload")); ?>
                        <input type="hidden" id="download_action" name="action" value="download_media" />
                        <input type="hidden" id="download_article_id" name="article_id" value="" />
                    </form>
          
                </div>                
            </div><!-- end main content -->
            
            <div id="article_modal" class="reveal-modal announcement">
                <a class="close-reveal-modal">&#215;</a>
                <div></div>
            </div>