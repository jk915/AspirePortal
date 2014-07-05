<table cellspacing="0" style="width: 600px;"> 
            <tr>
                <th style="width: 120px;">User Group</th>            
                <th>Bracket Min</th>            
                <th>Price</th>
                <th style="width: 20px;">&nbsp;</th> 
            </tr>
<?php /* Setup alternating row colours, using the variable "rowclass" */ 
$i = 0;
if($this->data["pricings"])
{
    foreach($this->data["pricings"]->result() as $pricing)
    {
        $rowclass = ($i++ % 2==1)  ? "admintablerow" : "admintablerowalt";
        
        ?> 
            <tr class="<?php echo $rowclass;?>">
                <td class="admintabletextcell">
                	<select class="access_level_id" name="access_level_id<?php print $pricing->product_price_id; ?>">
						<?php print $this->utilities->print_select_options( $broadcast_access_levels_to, "broadcast_access_level_id", "level", ( !empty( $_POST['access_level_id'.$pricing->broadcast_access_level_id] ) ? $_POST['access_level_id'.$pricing->broadcast_access_level_id] : ( $product_id ? $pricing->broadcast_access_level_id : '' ) ) ); ?>
					</select>
				</td>
                <td class="admintabletextcell"><span id="<?php echo $pricing->product_price_id; ?>" class="edit_quantity" title="Click to edit"><?php echo $pricing->bracket_max; ?></span></td>
                <td class="admintabletextcell">$ <span id="<?php echo $pricing->product_price_id; ?>" class="edit_price"  title="Click to edit"><?php echo $pricing->price;?></span></td>
                <td class="admintabletextcell" align="right"><a href="<?php print $pricing->product_price_id; ?>" class="delete_bracket" title="Click to delete this price"><img src="<?php print base_url(); ?>images/icon_delete.gif" alt="delete" /></a></td>
            </tr>                  
        <?php
        
    }
}
        ?>
</table>