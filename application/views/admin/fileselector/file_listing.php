      <table id="tblFiles" name="tblFiles" class="cmstable">
         <tr>
            <th>File name</th>
            <?php //if($selected_folder == "Icons"):?>
            <th>Image</th>
            <?php //endif;?>
            <th>File type</th>
            <th>Action</th>
         </tr>
      <?php
         $i = 0;
         
         foreach($files as $file)
         {
            $rowclass = ($i++ % 2==1) ? "admintablerow" : "admintablerowalt";      
            
            $file_name = $file["name"];
            $file_type = $file["type"];
            
            if(strlen($file_name) > 60)
               $file_name = substr($file_name, 0, 60) . "...";
            
            ?>
         <tr class="<?php echo $rowclass; ?>">
            <td><?php echo $file_name; ?></td>
            <?php //if($selected_folder == "Icons"):?>
            <td><img src="<?php echo base_url() . "files/" . $selected_folder . "/" . $file_name;?>" height="50px"/></td>
            <?php //endif;?>
            <td><?php echo $file_type; ?></td>
            <td><input type="button" class="select-file" value="Select" alt="<?php echo htmlspecialchars($file["name"]); ?>" style="width: 100px;" /></td>
         </tr>
            <?php  
         }
      ?>
      </table>