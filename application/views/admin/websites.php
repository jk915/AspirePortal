
        <form name="website" id="website" method="post" action="#">
        <?php 
            // Get the user credentials.    
            $user_id = $this->login_model->getSessionData("id");
            $user_type_id = $this->login_model->getSessionData("user_type_id");
            
            // Load a list of websites that this user has access to.
            $filters = array();
            $filters["enabled"] = 1;
            $filters["deleted"] = 0;
            $websites = $this->website_model->get_list($filters); 
            
            // If there are no websites, show an error as we cannot proceed.
            if(!$websites)
            {
				show_error("Please create a website using the website manager before proceeding");
            }      
            
            $default_website_id = $this->utilities->get_session_website_id();
            ?>
            <select id="website_id" name="website_id">
 				<?php echo $this->utilities->print_select_options($websites, "website_id", "website_name", $default_website_id, $default_text = "Choose a website"); ?>
            </select>
 	
            <input type="hidden" name="website_drop_down" value="1" />
        </form>
