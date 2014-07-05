<?php
    class MY_Controller extends CI_Controller
    {
        // Define variables to hold the details of the logged in user
        protected $user_id;
        protected $user_type_id;
        
        function __construct()
        {
            parent::__construct();
            $this->load->library('usertracking');
            
            // Check for an admin session
            $allow_admin = $this->session->userdata("allow_admin");
            $feuid = $this->session->userdata("frontend_user_id");
            
            if(($allow_admin) && (is_numeric($feuid))) {
                $frontend_user = $this->users_model->get_details($feuid);
                if(!$frontend_user) {
                    show_error("Invalid Front End User ID");
                }
                
                $this->user_id = $feuid;
                $this->user_type_id = $frontend_user->user_type_id;
                $this->session->set_userdata("agreed_to_terms", true);
                
            } else {
            
			    $this->usertracking->track_this();
                // Make sure the user is logged in
                if (!$this->login_model->is_logged_in("user"))
                {
							    
                    redirect("/login");
                    exit();
                }

                $this->user_id = $this->session->userdata["user_id"];
			    
			    $mine = $this->login_model->is_user_enabled($this->user_id);
			    if($mine != '1')
			    {
				    echo "<script type='text/javascript'>alert('Multiple concurrent users detected your account has been disabled'); location.replace('login'); </script>";
				    
				    $this->session->sess_destroy();
			    }
                $this->user_type_id = $this->session->userdata["user_type_id"];
                
                if(defined("RESTRICT_ACCESS"))
                {
                    $allowed_user_types = explode(",", RESTRICT_ACCESS);    
                    if(!in_array($this->user_type_id, $allowed_user_types))
                    {
                        redirect("/dashboard");
                        exit();                    
                    }
                }
                
                $this->terms_page = false;
                
                // Make sure the user has agreed to the terms and conditions. 
                if(isset($_SERVER["REQUEST_URI"]))
                {
                    $uri = $_SERVER["REQUEST_URI"];
                    if((strstr($uri, "/terms")) || (strstr($uri, "/terms/")))
                    {
                        $this->terms_page = true;
                    }
                    else
                    {
                	    $user_logged = $this->users_model->get_details($this->user_id);
                	    if ($user_logged && $user_logged->bypass_disclaimer == 1)
                	    {
                		    $this->session->set_userdata("agreed_to_terms", true);
                	    }
                	    
                        $agreed_to_terms = $this->session->userdata("agreed_to_terms");
                        if(!$agreed_to_terms)
                        {
                            redirect("/terms");      
                        }                
                    }
                } 
            }                      
        }
    }
