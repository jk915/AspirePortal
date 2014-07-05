                <?php                             
                    $show_img = ($website->website_icon != "" && file_exists(FCPATH . $website->website_icon));
                    echo img( array("src" => base_url(). $website->website_icon, "style" => "max-width:430px; display:". (($show_img) ? "block" : "none"), "id"=>"website_img" ) );
                ?>