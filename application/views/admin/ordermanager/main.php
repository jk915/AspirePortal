<body id="pagemanager" >   
   <div id="wrapper">
     
      <?php $this->load->view("admin/navigation");?>

      <div id="content">

      <?php $this->load->view("admin/ordermanager/navigation");?>    

<form class="plain" action="">
    <p><?php echo $name;?>, below is a list of the "orders" that are available on your site. To delete an order click on the checkbox in the delete column.</p>    
        <div class="bottom-margin">
            <div class = "left">
                <label for="search_name">Search by Customer Name</label>
                <input type="text" id="search_name" name="search_name" value="<?php if(isset($search_name)) echo $search_name; ?>" />
            </div>
            
            <div class = "left left-margin ">
                
                <?php $selected_search_status = ""; 
                if(isset($search_status))
                    $selected_search_status = $search_status;    
                ?>
                <label for="search_status">Order Status:</label>
                <?php echo form_dropdown('search_status', $search_status_arr, $selected_search_status,  ' class="short" id="search_status" ' );  ?>
                
            </div>
            
            <div class = "left left-margin">
                <?php                
                $selected_search_period = "";
                if(isset($search_period))
                    $selected_search_period = $search_period;
                ?>
                <label for="search_period">Period</label>
                <?php
                $period_arr = array(
                                'today' => 'Today',
                                'yesterday' => 'Yesterday',
                                'week_to_date' => 'Week to Date',
                                'last_week' => 'Last Week',
                                'month_to_date' => 'Month to Date',
                                'last_month' => 'Last Month',
                                'last_quarter' => 'Last Quarter',
                                'choose' => 'Choose'
                            ); 
                echo form_dropdown('search_period', $period_arr, $selected_search_period,  ' class="short" id="search_period" ' ); 
                ?>
            </div>
                        
            <div class="left left-margin top-margin-order" style="margin-top: 25px;">
                <input class="button" type="button" value="Search" id="search" />
            </div>
            
            <div class="clear"></div>
            
            <div id="choose_date" class="<?php echo ($selected_search_period == "choose") ? '' : 'hidden'; ?>">
                  <div class="left" style="width:280px"> 
                      <label for="start_date">Start Date: &nbsp;</label>    
                      <input type="text" readonly="readonly" class="date-pick dp-applied" value="<?php if($selected_search_period == "choose") echo $start_date;?>" id="start_date" name="start_date" />                          
                  </div>    
                  
                  <div class="left">  
                      <label for="start_date">End Date: &nbsp;</label>      
                      <input type="text" readonly="readonly" class="date-pick dp-applied" value="<?php if($selected_search_period == "choose") echo $end_date;?>" id="end_date" name="end_date" />                          
                  </div>
                  
                  <div class="clear"></div>    
            </div>             
        
        </div>
        <div class="clear"></div>
        <div id="page_listing" style="margin-top: 15px;"> 
            <?php
             $this->load->view('admin/ordermanager/order_listing.php'); 
            ?>
        </div>
        
        <div id="controls">
        
            <div id="page_buttons" class="left" >
                <div id="pagination"></div>
            </div>
            
            <div class="right">
                <input class="button" type="button" value="Delete Orders" id="delete" />
            </div>
            
            <div class="clear"></div>
            
        </div>
        
        <iframe src ="<?php echo base_url().'ordermanager/chart'?>" width="100%" height="800">
            <p>Your browser does not support iframes.</p>
        </iframe>
            
</form>

<?php $this->load->view("admin/ordermanager/navigation");?>    
