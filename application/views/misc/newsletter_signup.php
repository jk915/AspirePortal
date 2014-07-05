<form id="newsletterForm" action="<?php echo base_url(); ?>postback/subscribe" method="post">
	<fieldset> 
	    <div class="labelGroup"><!-- for inline labels -->
	        <label for="email">Your Email Address</label>
	        <input name="email" id="email" class="required email"  type="text" />  
	    </div><!-- end labelGroup-->
	    <input type="submit" class="submit" value="Join &raquo;" /> 
	</fieldset>
</form>