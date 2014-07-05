<div class="intro" style="height:23px">   

    <div class="left">

        <img src="<?=base_url();?>images/admin/i_back.png" border="0" width="16" height="18" alt="Back" />&nbsp;<a href="<?=base_url();?>admin/menu">Back to Menu</a>  

    </div>

    <div class="right">

        

        <?php 

        if(isset($side) && $side == "top")

        { 
        ?>

            <select id="name_type_search">

                <option value="">Name: All</option>

                <option value="lead_name">Lead Name</option>
                
                <option value="agent_name">Agent Name</option>

            </select>

            <input type="text" id="lead_search" class="box" />        

        

        <?php

        }

        ?>

    </div>    

    <div class="clear"></div>

</div>

