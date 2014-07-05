<div class="intro">
    <div class="left" style="width:600px">
        <p>Welcome <?php echo $name;?> to the Property Focus property reservation system.</p>
        <p>To view the details of a particular property, including full specifications, photos and foor plans, click on the "View Property" link.</p>
        <p>To refine your search, use the search tool to the right</p>
    </div>
    <div class="right porperty_listing_search" style="width: 310px;">
        <form id="property_search" action="#">
        <? if(isset($side) && $side == "top"): ?>
        
            <h2>Refine your search</h2>
            <div>
            <?php
                if($projects)
                {
                    ?>
                    <label for="search_estate" class="left" style="width:85px">Project/Estate</label> 
                    <select id="search_estate" name="project_id" style="width:210px">
                        <option value="-1">All</option>
                    <?php
                        foreach($projects->result() as $proj)
                        {
                        ?>
                            <option value = "<?php echo $proj->project_id;?>"><?php echo $proj->project_name; ?></option>
                        <?php
                        }
                    ?>
                    </select>
                    <?php
                }
                ?>
                <div class="clear"></div>
                
                <div class="margin-top">
	            <?php
	                if($states)
	                {
	                    ?>
	                    <label for="search_state" class="left" style="width:85px">State</label> 
	                    <select id="search_state" name="state_id" style="width:210px">
	                        <option value="-1">All</option>
	                    <?php
	                        foreach($states->result() as $state)
	                        {
	                        ?>
	                            <option value = "<?php echo $state->state_id;?>" <?php if($state->state_id == $state_id) print 'selected="selected"';?>><?php echo $state->name; ?></option>
	                        <?php
	                        }
	                    ?>
	                    </select>
	                    <?php
	                }
	                ?>
	                <div class="clear"></div> 
	            </div>
	            
                <div class="margin-top">
	            <?php
	                $property_statuses = array("Active", "Sold", "Reserved", "Coming Soon");
	                
	                if(is_array($property_statuses))
	                {
	                    ?>
	                    <label for="search_status" class="left" style="width:85px">Status</label> 
	                    <select id="search_status" name="status" style="width:210px">
	                        <option value="">All</option>
	                    <?php
	                        foreach($property_statuses as $status)
	                        {
	                        ?>
	                            <option value = "<?php echo strtolower($status);?>"><?php echo $status; ?></option>
	                        <?php
	                        }
	                    ?>
	                    </select>
	                    <?php
	                }
	                ?>
	                <div class="clear"></div> 
	            </div>
	            
                <div class="margin-top">

	                <label for="search_property_type" class="left" style="width:85px">Property Type</label> 
	                <select id="search_property_type" name="property_type_id" style="width:210px">
	                    <option value="">All</option>
	                <?php
	                    foreach($property_types->result() as $type)
	                    {
	                    ?>
	                        <option value = "<?php echo $type->property_type_id;?>"><?php echo $type->name; ?></option>
	                    <?php
	                    }
	                ?>
	                </select>

	                <div class="clear"></div> 
	            </div>	            	                           
            </div>
            <div style="margin-top: 10px">
            
                <label for="price_from" class="left" style="width:80px">Price From</label> 
                <input type="text" id="price_from" name="price_from" class="box left" style = "width:70px"/>
                
                <label for="price_to" class="left" style="width:42px; text-align: center;">To</label> 
                <input type="text" id="price_to" name="price_to" class="box" style = "width:70px"/>
                
                <div class="clear"></div>
                
            </div> 
            <div>
                <input type="button" class="button" value="Search" />
            </div>   
            
        <? endif;?>
       </form>
    </div>         
    <div class="clear"></div>
</div>