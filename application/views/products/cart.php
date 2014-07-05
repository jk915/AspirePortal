	<?php  if(isset($cart_products) && !empty($cart_products)){ ?>
	<table>
		<tr>
			<th width="140">Product</th>
			<th width="35">Qty</th> 
			<th>Price</th>
		</tr>
	<?php foreach($cart_products as $item) { ?>
		<tr>
			<td><?php echo $item['name']; ?></td>
			<td><?php echo $item['qty']; ?></td>  
			<td>$<?php echo number_format($item['subtotal'],2,".",""); ?></td>
		</tr>
	<?php } ?>
		<tr>
			<td colspan="2">Subtotal:</td>
			<td>$<?php echo number_format($subtotal,2,".",""); ?></td>
		</tr>
		<tr>
			<td colspan="2">GST:</td>
			<td>$<?php echo $gst; ?></td>
		</tr>
		<tr>
			<td colspan="2">Total:</td>
			<td>$<?php echo $total; ?></td>
		</tr>
	</table>
						
	
	<?php }  else {?>
		<div> <p>Your Shopping Basket is empty.</p></div>
	<?php } ?>
