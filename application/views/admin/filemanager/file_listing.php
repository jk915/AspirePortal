<div class="scroll-pane" style="width: 620px; height: 300px">   
	<input type="hidden" value="<?=ceil($pages_no)?>" id="pages_no" />
	<table cellspacing="0" class="cmstable" > 
				<tr>
					<th>File Name</th>			
					<th>Image</th>			
					<th>File Type</th>			
					<th>Download</th>
					<th style="width: 20px;">Delete</th>
	                <? if (isset($window) && $window == "popup"):?>
	                    <th style="width: 20px">Select</th> 
	                <? endif; ?>
				</tr>
	<? /* Setup alternating row colours, using the variable "rowclass" */ 
	$i = 0;
	if($files)
	{
		foreach($files as $file)
		{
			if($i++ % 2==1) $rowclass = "admintablerow";
			else  $rowclass = "admintablerowalt";
	        
	        $filename = $file["name"];
	        if(strlen($filename) > 40)
	            $filename = substr($filename, 0, 40) . "...";
	            
	            if(isset($file["width"]))
	            {
					$size = " (" . $file["width"] . "x" . $file["height"] . ")";
	            }
	            else
	            	$size = "";
			?> 
				<tr class="<?=$rowclass;?>">
					<td class="admintabletextcell"><?=$filename . $size;?></td>
					<td class="admintabletextcell"><img src="<?php echo base_url() . "files/" . $selected_folder . "/" . $filename;?>" height="50px"/></td>
					<td class="admintabletextcell"><?=$file["type"];?></td>				
					<td class="admintabletextcell"><a class="download" href="<?=$file["name"]?>">Download</a></td>
					<td class="center"><input type="checkbox" name="filestodelete[]" value="<?=$file["name"];?>" /></td>
	                <? if (isset($window) && $window == "popup"):?>
	                <td class="admintabletextcell"><a class="select" href="<?=$file["name"]?>" title="<?php echo $size; ?>">Select</a></td>                                        
	                <? endif; ?>
				</tr>          		
			<?
		}
	}
			?>
	</table>
</div>