
<h1>Step 4 - Status transaction</h1>

<?php /*

<div class="topSpace2">
<?php if($success == 1){ ?>
<p>Thank you for placing an order with us.</p>
<p>Your payment was successful and we have just sent you an email containing your order confirmation  details.</p>
<p><a href="<?php echo base_url()."invoices/".ifvalue($invoice_name, $invoice_name, ""); ?>" title="Download" target="_blank">
Download</a> your order confirmation as a PDF.</p>
<p>We will dispatch your goods within the next business day.</p>
<p>If you have any questions, please contact our sales department  
<?php echo ifvalue($phone, $phone,"") ?> quoting purchase number "<?php echo $order_id ?>".</p>

<?php } else { ?>
	<p>Sorry, your order could not be processed.  
	<a href="<?php echo base_url(1)."order/order_confirmation" ?>">
		Please check your credit card details and try again 
	</a><br/>
<?php 
	 echo $transaction_message;
     ?>
     </p> 
<?php     
} ?>
</div>
*/
?>

<div class="topSpace2">
<?php 
	if ($success == 1)
	{
?>
	<p>Thank you for placing an order with us. Your payment was successful and we have just sent you an email containing your invoice. You may also download your invoice by <a href="<?php echo base_url()."invoices/".ifvalue($invoice_name, $invoice_name, ""); ?>" title="download" target="_blank">clicking here</a>.</p>
	<p>If you have ordered physical goods, we will dispatch them to you within the next business day.</p>
	<p>If you have any questions, please <a href="<?php echo base_url(1)?>page/support">visit our support area</a>.</p>

<?php 
	}
	else
	{
		?>
		<p>
			Sorry, your order could not be processed.<a href="<?php echo base_url(1)."order/order_confirmation" ?>">Please check your credit card details and try again </a>
			<br/>
			<?php echo $transaction_message; ?>
		</p>
	<?php 
	}
?>

</div>

<form action="<?php echo base_url(1)."order/submit_order" ?>" method="post" class="payment_detail_form" id="payment_details_form_id">
	<input maxlength="16" type="hidden" id="card_number"  name="card_number" value="" class="required numeric" />
	<input maxlength="3" type="hidden" id="card_cvv" name="card_cvv" class="required numeric" value="" />
</form>
