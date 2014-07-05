
<body id="contact">
    <div id="wrapper">
        
        <?php $this->load->view("admin/navigation");?>
        
        <div id="content">

            <?php $this->load->view("admin/area/navigation"); ?>                        
            
            <form class="plain" id="frmArea" name="frmArea" action="<?php echo base_url()?>admin/areamanager/area/<?php echo $area_id?>"  method="post">
                <input type="hidden" id="folder" value="<?php echo $area_id;?>" />
                <input type="hidden" id="area_id" value="<?php echo $area_id;?>" />
     
<?php if(isset($area)) : // We're editing and existing area.  Show the tabs. ?>
                
				<br><br>
			    <input id="submitbutton2" class="button right" type="button" value="<? echo ($area_id == "") ? "Create New Area": "Update Area"; ?>" style="margin-top:-40px;" />

                <!-- tabs -->
                <ul class="css-tabs skin2">
                    <li><a href="#" id="tabDetail">Details</a></li>
                    <li><a href="#">HTML Content</a></li>
                    <li><a href="#">Sections</a></li>
                    <li><a href="#">Links</a></li>
                    <li><a href="#" id="tabDocument">Documents</a></li>                    
                    <li><a href="#" id="tabGallery">Gallery</a></li>                    
                    <li><a href="#">Comments</a></li>                    
                    <li><a href="#">Stats</a></li>                    
                </ul>   
                
                <!-- panes -->
                <div class="css-panes skin2">
                    <div style="display:block">
<?php endif; ?>
                        <div class="left" style="width:33%">
							<label for="area_name">Area Name:<span class="requiredindicator">*</span></label>
                    		<input id="area_name" class="required" type="text" value="<? echo ($area_id !="") ? $area->area_name : ""; ?>" name="area_name"/>						
    						
    						<div class="clear"></div>
							
							<label for="postcode">Postcode:</label>
                    		<input id="postcode" type="text" value="<? echo ($area_id !="") ? $area->postcode : ""; ?>" name="postcode"/>						
    						<div class="clear"></div>
							
                            <label for="state_id">State:<span class="requiredindicator">*</span></label>
                            <select id="state_id" name="state_id" class="required">
                                <option value="">Choose</option>
                                <?php 
                                if($states)
                                    echo $this->utilities->print_select_options($states,"state_id","preferredName",($area_id !="") ? $area->state_id : ""); 
                                ?>
                            </select>                              
                            
							<label for="region_id">Region:<span class="requiredindicator">*</span></label>
                            <select id="region_id" name="region_id" class="required">
                                <option value="">Choose</option>
                                <?php 
                                if($regions)
								
                                    echo $this->utilities->print_select_options($regions,"region_id","region_name",($area_id !="") ? $area->region_id : ""); 
                                ?>
                            </select>
                            
<?php if(isset($area)) : ?>
   
                            <div class="clear"></div>                       
                            
                            <label for="area_hero_image">Area Hero Image</label>
                            <div class="hero_img">
                                <?php if (!empty($area->area_hero_image)) : ?>
                              	<img id="area_hero_img" src="<?php  echo base_url().$area->area_hero_image; ?>" width="250" class="<?php echo ($area->area_hero_image == "") ?  "hidden" : ""; ?>" />
                              	<?php endif; ?>
                          	</div>
        					<input class="<?php echo ($area->area_hero_image == "") ?  "hidden" : ""; ?> button" type="button" value="Delete Hero Image" id="delete_hero_image" />
        					<!--<input type="file" name="area_hero_image" id="area_hero_image" class="showif <?php echo (!empty($area->area_hero_image)) ?  "hidden" : ""; ?>" />  -->
        					<div id="hero_image_upload" class="showif <?php echo (!empty($area->area_hero_image)) ?  "hidden" : ""; ?>"></div>
                        
<?php endif; ?>
                            <div class="clear"></div>
                            
                            <div class="left" style="padding-top: 20px;">
    							<input type="checkbox" name="enabled" value="1" class="left" <? echo ($area_id !="") ? (($area->enabled == 1) ? "checked" :"") : "checked" ?>  /><label for="enabled" class="left" style="padding-top:0px">&nbsp;Enabled</label><br />
                                <h3>Actions</h3>
                                <ul>
                                    <li><a href="<?php echo base_url();?>admin/areamanager/brochure/<?php echo $area_id;?>" class="print" target="_blank">Print Area</a></li>
                                </ul>
                            </div>
						</div>
<?php if(isset($area)) : ?>

						<div class="left" style="width:33%">
                            
                            <label for="weekly_median_advertised_rent">Weekly median advertised rent:</label>
                    		<input id="weekly_median_advertised_rent" type="text" value="<? echo ($area_id !="") ? $area->weekly_median_advertised_rent : ""; ?>" name="weekly_median_advertised_rent"/>
        					
        					<div class="clear"></div>
        					
        					<label for="total_population">Total Population:</label>
                    		<input id="total_population" type="text" value="<? echo ($area_id !="") ? $area->total_population : ""; ?>" name="total_population"/>
        					
        					<div class="clear"></div>
        					
        					<label for="median_age">Median Age:</label>
                    		<input id="median_age" type="text" value="<? echo ($area_id !="") ? $area->median_age : ""; ?>" name="median_age"/>
        					
        					<div class="clear"></div>
        					
        					<label for="number_private_dwellings">Number Private dwellings:</label>
                    		<input id="number_private_dwellings" type="text" value="<? echo ($area_id !="") ? $area->number_private_dwellings : ""; ?>" name="number_private_dwellings"/>
        					
        					<div class="clear"></div>
        					
        					<label for="weekly_median_household_income">Weekly Median household income:</label>
                    		<input id="weekly_median_household_income" type="text" value="<? echo ($area_id !="") ? $area->weekly_median_household_income : ""; ?>" name="weekly_median_household_income"/>
        					
        					<label for="closest_cbd">Closest CBD:</label>
                    		<input id="closest_cbd" type="text" value="<? echo ($area_id !="") ? $area->closest_cbd : ""; ?>" name="closest_cbd"/>
        					
        					<label for="approx_time_cbd">Approx time to CBD:</label>
                    		<input id="approx_time_cbd" type="text" value="<? echo ($area_id !="") ? $area->approx_time_cbd : ""; ?>" name="approx_time_cbd"/>
        					
        					<label for="approx_distance_cbd">Approx Distance to CBD:</label>
                    		<input id="approx_distance_cbd" type="text" value="<? echo ($area_id !="") ? $area->approx_distance_cbd : ""; ?>" name="approx_distance_cbd"/>
        					
        					<div class="clear"></div>
						
						</div>
						
						<div class="left" style="width:33%">
                            
                            <label for="googlemap">Google Maps Embed Code</label>
                            <textarea name="googlemap" id="googlemap"><? echo ($area_id !="") ? $area->googlemap : ""; ?></textarea>
                            
                            <?php
                                if(isset($area)) {
                                    $map_image = "area_files/" . $area_id . "/map.png";
                                    $map_image_abs = ABSOLUTE_PATH . "area_files/" . $area_id . "/map.png";
        
                                    if(file_exists($map_image_abs)) {
                                        ?>
                             <img src="<?php echo base_url() . $map_image . "?r=" . rand(9999, 99999999); ?>" width="260" style="padding: 10px 0px;" />
                             <input type="button" class="button" id="btnRegenerateMap" value="Regenerate Map" />
                             <input type="hidden" id="deletemap" name="deletemap" value="0" />
                                        <?php
                                    }
                                }                                    
                            ?>                            
    						
    						<div class="clear"></div>
                            
                            <?php if($area) : ?>
                            <p>Last Modified: <?=$this->utilities->isodatetime_to_ukdate($area->last_modified); ?></p>
                            <?php endif; ?>                            
						
						</div>
						
<?php endif; ?>
                        
                        <?php if(isset($area)) : ?>  
                        <div class="left" style="width:33%">
                            <label>Brochure Key Facts</label>
                            
                            <label style="width:50%; float: left;">Heading:</label>
                            <label style="width:50%; float: left;">Text:</label>
                            
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_heading1 : ""; ?>" name="key_fact_heading1" class="haft"/>
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_text1 : ""; ?>" name="key_fact_text1" class="haft"/>
                        
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_heading2 : ""; ?>" name="key_fact_heading2" class="haft"/>
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_text2 : ""; ?>" name="key_fact_text2" class="haft"/>
                            
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_heading3 : ""; ?>" name="key_fact_heading3" class="haft"/>
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_text3 : ""; ?>" name="key_fact_text3" class="haft"/>
                            
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_heading4 : ""; ?>" name="key_fact_heading4" class="haft"/>
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_text4 : ""; ?>" name="key_fact_text4" class="haft"/>
                            
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_heading5 : ""; ?>" name="key_fact_heading5" class="haft"/>
                            <input type="text" value="<? echo ($area_id !="") ? $area->key_fact_text5 : ""; ?>" name="key_fact_text5" class="haft"/>
                        </div>
                        <?php endif; ?>	  
                        
						<div class="clear"></div>

           
						
<?php if(isset($area)) : ?>

					</div><!-- END first tab -->
					
                    <div>
                    
                        <div class="clear"></div>
                        
                        <label for="short_description">Short Description / Intro</label><br />
                        <textarea id="wysiwyg" cols="20" rows="10" name="short_description" style="width:880px;height:300px" class="editor"><? echo ($area_id !="") ? $area->short_description : "" ?></textarea>
                        <input type="hidden" name="wysiwygWordCount" value="310" />
                        
                        <div class="clear"></div>
                        
                        <label for="overview">Brochure overview</label><br />
                        <textarea id="wysiwyg2" cols="20" rows="10" name="overview" style="width:880px;height:300px" class="editor"><? echo ($area_id !="") ? $area->overview : "" ?></textarea>
                        <input type="hidden" name="wysiwyg2WordCount" value="1480" />
                        
                    </div><!-- END html content tab -->
                    
                    <div>
                        <table cellspacing="0" width="100%" class="left metalisting"> 
                            <tr>
                                <th width="10%">ID</th> 
                                <th align="left">Section Name</th>                            
                                <th width="10%">Delete</th>                            
                            </tr>
                    <?php $i = 0;?>
                    <?php if ($metas) : ?>
                        <?php foreach ($metas->result() AS $meta) : ?>
                            <?php
                                if($i++ % 2==1) $rowclass = "admintablerow";
			                    else  $rowclass = "admintablerowalt";
                            ?>
                            <tr class="<? print $rowclass;?>">
                                <td class="admintabletextcell" align="center"><?php echo $meta->id;?></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="<?php echo $meta->id;?>" class="btnedit"><?php echo $meta->name;?></a></td>
                                <td class="center"><input type="checkbox" class="metatodelete" value="<?php echo $meta->id;?>" /></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                        </table>
                        
                        <div class="clear"></div>
                        
                        <a href="javascript:;" class="button right center" id="deletemeta" style="margin-left:10px;">Delete</a>
                        <a href="javascript:;" class="button right center" id="openformadd">Add new</a>
                        
                        <div class="clear"></div>
                        
                        <!-- FORM ADD META DATA -->
                        <div id="formaddwrap" style="display:none;">
                        
                            <label for="heading">Section Name:<span class="requiredindicator">*</span></label>
                            <input class="heading" type="text" value="" id="heading" style="width:750px"/>
                            <div class="clear"></div>
                            
                            <label for="icon_image">Icon Image</label>
                            
                            <div id="icon_upload_area">
                                <input type="text" readonly="readonly" id="icon_image" name="icon_image"  />
                                <input type="button" id="icon_upload" value="Upload a file" class=" button" onclick="selectFile('icon_image');"/>
                            </div>
                            
                            <label for="content">Section Content:<span class="requiredindicator">*</span></label>
                            <input type="hidden" id="meta_id" value=""/>
                            <textarea id="wysiwyg4" cols="20" rows="10" style="width:880px;height:300px;padding-top:5px;" class="editor"></textarea><br />
                            
                            <div class="clear"></div>
                            <a href="javascript:;" class="button left center savemeta">Save</a>
                            
                        </div>
                        <div class="clear"></div>
                    </div><!-- END section tab -->
                    
                    <div>
                    
                        <table cellspacing="0" width="100%" class="left link_listing"> 
                            <tr>
                                <th width="10%">ID</th> 
                                <th align="left">Title</th>                            
                                <th width="50%" align="left">Url</th>                            
                                <th width="10%">Delete</th>                            
                            </tr>
                    <?php $i = 0;?>
                    <?php if ($links) : ?>
                        <?php foreach ($links->result() AS $link) : ?>
                            <?php
                                if($i++ % 2==1) $rowclass = "admintablerow";
			                    else  $rowclass = "admintablerowalt";
                            ?>
                            <tr class="<? print $rowclass;?>">
                                <td class="admintabletextcell" align="center"><?php echo $link->link_id;?></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="<?php echo $link->link_id;?>" class="editlink"><?php echo $link->title;?></a></td>
                                <td class="admintabletextcell" style="padding-left:12px;"><?php echo $link->url;?></td>
                                <td class="center"><input type="checkbox" class="linktodelete" value="<?php echo $link->link_id;?>" /></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                        </table>
                        
                        <div class="clear"></div>
                        
                        <a href="javascript:;" class="button right center" id="deletelink" style="margin-left:10px;">Delete</a>
                        <a href="javascript:;" class="button right center" id="addnewlink">Add new</a>
                        
                        <div class="clear"></div>
                        
                        <div id="formaddlink" style="display:none;">
                        
                            <label for="link_title">Link Title:<span class="requiredindicator">*</span></label>
                            <input type="text" id="link_title"/>
                            <div class="clear"></div>
                            
                            <label for="url">Url:<span class="requiredindicator">*</span></label>
                            <input type="text" id="url"/>
                            <input type="hidden" id="link_id"/>
                            
                            <div class="clear"></div><br />
                            <a href="javascript:;" class="button left center savelink">Save</a>
                            
                        </div>
                        
                        <div class="clear"></div>
                        
                    </div><!-- END tab web link -->
                    
                    <div class="upload_documents">
                    
                    <?php
            
                    if($documents) :
                        $i = 0;
                        foreach($documents->result() as $doc) :
                        
                            $filename = $doc->document_path;
                    
                            if(strlen($filename) > 40)
                                 $filename = substr($filename, 0, 40) . "...";
                                 
                            $line = "<div class='line'></div>";
                        ?>
                        <div class="document_<?php echo $doc->id;?>">  
                            <div>
	                            <span class="doc_name">Allowed Access:</span>
	                            <input type="radio" name="doc_<?php echo $doc->id;?>_extra_data" value="" <?php echo (empty($doc->extra_data)) ? 'checked="checked"' : ''?> /> All Users
	                            <input type="radio" name="doc_<?php echo $doc->id;?>_extra_data" value="advisors_only" <?php echo ($doc->extra_data=='advisors_only') ? 'checked="checked"' : ''?> /> Advisors Only
	                        </div>
	                        
	                        <div>
	                            <span class="doc_name">Document Name:</span>
	                            <input type="text" id="doc_<?php echo $doc->id;?>_name" name="doc_<?php echo $doc->id;?>_name" value="<?php echo $doc->document_name;?>" />
	                        </div>
                            
                            <div>    
                                <input type="hidden" value="<?php echo $doc->id;?>" id="doc_<?php echo $doc->id;?>_id" /> 
                        
                                <span class="doc_name left" style="padding:15px 39px 0 0;">Attachment:</span><span id="docpath_<?php echo $doc->id;?>" class="<?php echo ($filename == "")? "hidden": ""; ?>"><?php echo $doc->document_path; ?></span>
											
                                <input type="button" name="delete_doc_<?php echo $doc->id;?>" id="delete_doc_<?php echo $doc->id;?>" value="Delete" style="width:70px;margin-top: 5px;" class="<?php echo ($doc->document_path == "")? "hidden del_path": "del_path"; ?> button" />    
								
								<a href="<?php echo base_url($doc->document_path); ?>" id="view_doc" target="_blank" class="<?php echo ($doc->document_path == "")? "hidden": ""; ?>"> View Document </a>
								
                                <!--<input type="file" name="doc_<?php echo $doc->id;?>" id="doc_<?php echo $doc->id;?>" class="<?php echo ($filename != "") ? "hidden" : ""; ?>" />    -->
                                <div id="doc_upload_file_<?php echo $doc->id;?>" <?php echo ($filename != "") ? "hidden" : ""; ?> class="doc_upload_file" did="<?php echo $doc->id;?>" style="width:150px;float:left;padding-top:5px;"></div>
                      
                            </div>                                
                        </div>
                        <?php
                        
                        if($i != ($documents->num_rows() - 1)) echo $line;
                            
                        $i++;
                        endforeach;
                    endif;
                    ?>
                    <br>
					<br>
                    </div><!-- END document tab -->
                    
                    <div>
                        <div class="right" style="padding-right:10px">

                            <label for="upload_file">Upload a new image</label>
                            <!--<input type="file" name="upload_file" id="upload_file" />    -->
                            <div id="upload_file"></div>
            
                        </div>
            
                        <div class="clear"></div>
            
                        <label>Area Images</label>
            
                        <br/>
            
                        <div id="files_listing">
            
                            <div  id="page_listing">
                                <? $this->load->view('admin/area/file_listing',array('files'=>$images,'pages_no' => count($images) / $images_records_per_page)); ?>
                            </div>
            
                            <div class="clear"></div>            
            
                            <div id="controls">
                                <div class="right">
                                    <input class="button" type="button" value="Save Changes" id="save_file_changes" />
                                    <input class="button" type="button" value="Delete Selected Files" id="delete_files" />
                                </div>                
                            </div>    
            
                            <div class="clear"></div>
            
                        </div>
                    </div><!-- END gallery tab -->
                    
                    <div><!-- BEGIN comments tab -->
                        <table cellspacing="0" width="100%" class="left commentlisting"> 
                            <tr>
                                <th width="10%">ID</th>
                                <th align="left">Comment</th>
                                <th width="10%">Delete</th>
                            </tr>
                    <?php if ($comments) : ?>
                        <?php foreach ($comments->result() AS $index=>$comment) : ?>
                            <tr id="acomment_<?php echo $comment->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
                                <td class="admintabletextcell" align="center"><?php echo $comment->id;?></td>
                                <td class="admintabletextcell" style="padding-left:12px;">
                                    <span style="font-weight:bold"><?php echo trim("$comment->first_name $comment->last_name")?></span>
                                    @ <em style="font-style:italic;"><?php echo date('d/m/Y h:i A', $comment->ts_added)?></em>:<br />
                                    "<?php echo nl2br($comment->comment)?>"
                                </td>
                                <td class="center"><input type="checkbox" class="commenttodelete" value="<?php echo $comment->id;?>" /></td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                        </table>
                        <a href="javascript:;" class="button right center" id="deletecomment">Delete</a>
                        <a href="javascript:;" class="button right center" id="newcomment">New Comment</a>
                        
                        <div class="clear"></div>
                        
                        <div id="formnewcomment" style="display:none;">
                        
                            <label for="comment">Comment:<span class="requiredindicator">*</span></label>
                            <textarea id="comment" style="width:400px;"></textarea>
                            <input type="hidden" id="comment_id"/>
                            
                            <div class="clear"></div><br />
                            <a href="javascript:;" class="button left center savecomment">Save</a>
                            
                        </div>
                        
                        <div class="clear"></div>
                    </div><!-- END comments tab -->
                    
					<div>
						<form class="plain" id="" name="" action="<?php echo base_url()?>admin/areamanager/stats"  method="post">
						<table border="0" width="90%">
							<tr>
								<th>Vital Stats </th>
								<th>2006 </th>
								<th>2011 </th>
								<th>Chnage % </th>
							</tr>
							
								<tr>
									<td align="right"> Median Age </td>
									<td align="center"><input type='text' name="2006_Median Age" class='first' id='f-0' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Median Age" class='second' id='s-0' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Median Age" class='result' id='r-0' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Employed FT </td>
									<td align="center"><input type='text' name="2006_Employed FT" class='first' id='f-1' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Employed FT" class='second' id='s-1' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Employed FT" class='result' id='r-1' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4"> Top 5 Occupation </th></tr>
								<tr>
									<td align="right"> Professionals </td>
									<td align="center"><input type='text' name="2006_Professionals" class='first' id='f-2' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Professionals" class='second' id='s-2' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Professionals" class='result' id='r-2' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Clerical & Admin </td>
									<td align="center"><input type='text' name="2006_Clerical_Admin" class='first' id='f-3' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Clerical_Admin" class='second' id='s-3' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Clerical_Admin" class='result' id='r-3' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Managers </td>
									<td align="center"><input type='text' name="2006_Managers" class='first' id='f-4' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Managers" class='second' id='s-4' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Managers" class='result' id='r-4' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Technicians & Trades </td>
									<td align="center"><input type='text' name="2006_Technicians_Trades" class='first' id='f-5' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Technicians_Trades" class='second' id='s-5' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Technicians_Trades" class='result' id='r-5' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Sales </td>
									<td align="center"><input type='text' name="2006_Sales" class='first' id='f-6' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Sales" class='second' id='s-6' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Sales" class='result' id='r-6' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4"> Weekly Household Income </th></tr>
								<tr>
									<td align="right"> Family without Children Median Income </td>
									<td align="center"><input type='text' name="2006_Family without Children Median Income" class='first' id='f-7' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Family without Children Median Income" class='second' id='s-7' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Family without Children Median Income" class='result' id='r-7' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Family With Children Median Income </td>
									<td align="center"><input type='text' name="2006_Family With Children Median Income" class='first' id='f-8' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Family With Children Median Income" class='second' id='s-8' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Family With Children Median Income" class='result' id='r-8' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Less than $600/week Hold Income </td>
									<td align="center"><input type='text' name="2006_Less than_600_week Hold Income" class='first' id='f-9' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Less than _600_week Hold Income" class='second' id='s-9' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Less than 600/week Hold Income" class='result' id='r-9' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> More than $3,000/week Hold Income </td>
									<td align="center"><input type='text' name="2006_More than _3000_week Hold Income" class='first' id='f-10' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_More than _3000_week Hold Income" class='second' id='s-10' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_More than 3000_week Hold Income" class='result' id='r-10' style="width:100px;"/></td>
								</tr>
							<tr> <th colspan="4">Travel to Work </th></tr>
								<tr>
									<td align="right"> Car </td>
									<td align="center"><input type='text' name="2006_Car" class='first' id='f-11' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Car" class='second' id='s-11' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Car" class='result' id='r-11' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Public Transport </td>
									<td align="center"><input type='text' name="2006_Public Transport" class='first' id='f-12' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Public Transport" class='second' id='s-12' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Public Transport" class='result' id='r-12' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4">Family Group </th></tr>
								<tr>
									<td align="right"> Couple </td>
									<td align="center"><input type='text' name="2006_Couple" class='first' id='f-13' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Couple" class='second' id='s-13' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Couple" class='result' id='r-13' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Couple with children </td>
									<td align="center"><input type='text' name="2006_Couple with children" class='first' id='f-14' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Couple with children" class='second' id='s-14' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Couple with children" class='result' id='r-14' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Single Parent family </td>
									<td align="center"><input type='text' name="2006_Single Parent family" class='first' id='f-15' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Single Parent family" class='second' id='s-15' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Single Parent family" class='result' id='r-15' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4"> Household Composition </th></tr>
								<tr>
									<td align="right"> Family Households </td>
									<td align="center"><input type='text' name="2006_Family Households" class='first' id='f-16' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Family Households" class='second' id='s-16' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Family Households" class='result' id='r-16' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Single Person Household </td>
									<td align="center"><input type='text' name="2006_Single Person Household" class='first' id='f-17' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Single Person Household" class='second' id='s-17' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Single Person Household" class='result' id='r-17' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Detached House </td>
									<td align="center"><input type='text' name="2006_Detached House" class='first' id='f-18' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Detached House" class='second' id='s-18' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Detached House" class='result' id='r-18' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Semi Detached </td>
									<td align="center"><input type='text' name="2006_Semi Detached" class='first' id='f-19' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Semi Detached" class='second' id='s-19' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Semi Detached" class='result' id='r-19' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4"> Property Composition</th></tr>
								<tr>
									<td align="right"> 1 Bedroom </td>
									<td align="center"><input type='text' name="2006_1 Bedroom" class='first' id='f-20' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_1 Bedroom" class='second' id='s-20' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_1 Bedroom" class='result' id='r-20' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> 2 Bedroom </td>
									<td align="center"><input type='text' name="2006_2 Bedroom" class='first' id='f-21' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_2 Bedroom" class='second' id='s-21' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_2 Bedroom" class='result' id='r-21' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> 3 Bedroom </td>
									<td align="center"><input type='text' name="2006_3 Bedroom" class='first' id='f-22' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_3 Bedroom" class='second' id='s-22' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_3 Bedroom" class='result' id='r-22' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> 4 or more Bedroom </td>
									<td align="center"><input type='text' name="2006_4 or more Bedroom" class='first' id='f-23' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_4 or more Bedroom" class='second' id='s-23' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_4 or more Bedroom" class='result' id='r-23' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4"> Tenure </th></tr>
								<tr>
									<td align="right"> Owned Outright </td>
									<td align="center"><input type='text' name="2006_Owned Outright" class='first' id='f-24' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Owned Outright" class='second' id='s-24' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Owned Outright" class='result' id='r-24' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Owned with Mortgage </td>
									<td align="center"><input type='text' name="2006_Owned with Mortgage" class='first' id='f-25' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Owned with Mortgage" class='second' id='s-25' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Owned with Mortgage" class='result' id='r-25' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Renting </td>
									<td align="center"><input type='text' name="2006_Renting" class='first' id='f-26' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Renting" class='second' id='s-26' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Renting" class='result' id='r-26' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4"> Rental Affordability </th></tr>
								<tr>
									<td align="right"> Rent Less Than 30% Income </td>
									<td align="center"><input type='text' name="2006_Rent Less Than 30 Income" class='first' id='f-27' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Rent Less Than 30 Income" class='second' id='s-27' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Rent Less Than 30 Income" class='result' id='r-27' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> Rent More Than 30% Income </td>
									<td align="center"><input type='text' name="2006_Rent More Than 30 Income" class='first' id='f-28' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_Rent More Than 30 Income" class='second' id='s-28' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_Rent More Than 30 Income" class='result' id='r-28' style="width:100px;"/></td>
								</tr>
								<tr> <th colspan="4"> Number of Cars Per Household </th></tr>
								<tr>
									<td align="right"> 0 car per household </td>
									<td align="center"><input type='text' name="2006_0 car per household" class='first' id='f-29' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_0 car per household" class='second' id='s-29' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_0 car per household" class='result' id='r-29' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> 1 car per household </td>
									<td align="center"><input type='text' name="2006_1 car per household" class='first' id='f-30' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_1 car per household" class='second' id='s-30' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_1 car per household" class='result' id='r-30' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> 2 car per household </td>
									<td align="center"><input type='text' name="2006_2 car per household" class='first' id='f-31' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_2 car per household" class='second' id='s-31' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_2 car per household" class='result' id='r-31' style="width:100px;"/></td>
								</tr>
							
								<tr>
									<td align="right"> 3+ car per household </td>
									<td align="center"><input type='text' name="2006_3 car per household" class='first' id='f-32' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="2011_3 car per household" class='second' id='s-32' style="width:100px;"/> </td>
									<td align="center"><input type='text' name="change_3 car per household" class='result' id='r-32' style="width:100px;"/></td>
								</tr>
								
						</table>
				<p align="right"><input type="submit" name="update_stats" value="Update Stats"> </p>
					</form>
					</div>
					
                </div>
<?php endif; ?>
                <div class="clear"></div>
    
                <label for="heading">&nbsp;</label> 
                <input id="submitbutton" type="button" class="button" value="<? echo ($area_id == "") ? "Create New Area": "Update Area"; ?>" /><br/>                    
            </form>
            
         <br/>
         <?php $this->load->view("admin/area/navigation"); ?>    