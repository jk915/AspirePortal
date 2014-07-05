<body class="dashboard">
        <div id="wrapper">
            <?php $this->load->view("member/page_header"); ?>  
                  
            <div id="main">  
                <div class="content">
                    <?=$this->utilities->replaceTags($this, $terms->block_content, $hint = ""); ?>
                    
                    <?php echo form_open('terms/ajax', array("id" => "frmAgree", "name" => "frmAgree")); ?>
                        <p><input type="checkbox" name="agree_to_terms" id="agree_to_terms" value="1" /> <b>I agree</b></p>
                        <a id="btnAgree" class="btn fleft" href="#">Submit</a>
                        <input type="hidden" name="action" value="agree" />
                    </form>
                </div><!-- end main content -->