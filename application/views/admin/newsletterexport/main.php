<body id="newsletterexport" >
    <div id="wrapper">
        <?php $this->load->view("admin/navigation");?>
    
        <div id="content">
    
            <?php $this->load->view("admin/newsletterexport/navigation");?>
            
            <h1>Export HTML Newsletter</h1>
            
            <form method="post" action="<?php echo site_url('admin/newsletterexport/export')?>">
                <ul class="left" style="width:300px;margin-right:15px;">
                    <li><strong>What would you like to export?</strong></li>
                    <li><label><input type="radio" name="type" checked="checked" value="stocklist" /> Stocklist</label></li>
                    <li><label><input type="radio" name="type" value="project" /> Projects</label></li>
                    <li class="divider"></li>
                    <li><strong>Filters</strong></li>
                    <li><label><input type="checkbox" name="featured" checked="checked" value="1" /> Featured Only</label></li>
                    <li class="stockonly"><label><input type="checkbox" name="nras" value="1" /> NRAS</label></li>
                    <li class="stockonly"><label><input type="checkbox" name="smsf" value="1" /> SMSF</label></li>
					<li class="stockonly"><label><input type="checkbox" name="new" value="1" /> NEW</label></li>
                    <li class="divider"></li>
                    <li><strong>State</strong></li>
                    <li><?php echo form_dropdown_states(1, 'state','')?></li>
                    <li class="divider"></li>
                    <li><strong>Order By</strong></li>
                    <li><?php echo form_dropdown('orderby', array('title'=>'Title','state'=>'State','yield'=>'Yield'),'')?></li>
                </ul>
                
                <ul class="left" style="width:300px;">
                    <li class="stockonly"><strong>Status Options</strong></li>
                    <li class="stockonly"><label><input type="checkbox" name="status[]" checked="checked" value="available" /> Available</label></li>
                    <li class="stockonly"><label><input type="checkbox" name="status[]" value="reserved" /> Reserved</label></li>
                    <li class="stockonly"><label><input type="checkbox" name="status[]" value="signed" /> Signed</label></li>
                    <li class="stockonly"><label><input type="checkbox" name="status[]" value="sold" /> Sold</label></li>
                    <li class="divider"></li>
                    <li>
                        <input type="submit" value="Start Export &raquo;" id="exportbtn"/>
                    </li>
                </ul>
                
                <div class="clear"></div>
            </form>
            
            <?php $this->load->view("admin/newsletterexport/navigation");?>