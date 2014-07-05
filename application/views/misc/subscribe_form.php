<p>Let us keep you up to date with all the latest property developments.</p>
<form id="frmSubscribe">
    <div class="mb10"><input type="text" name="first_name" id="sub_first_name" value="Your First Name" onblur="if(this.value=='') this.value='Your First Name';" onfocus="if(this.value=='Your First Name') this.value='';" size="30" /></div>
    <div class="mb10"><input type="text" name="last_name" id="sub_last_name" value="Your Last Name" onblur="if(this.value=='') this.value='Your Last Name';" onfocus="if(this.value=='Your Last Name') this.value='';" size="30" /></div>
    <div class="mb10"><input type="text" name="email" id="sub_email" value="Your Email Address" onblur="if(this.value=='') this.value='Your Email Address';" onfocus="if(this.value=='Your Email Address') this.value='';" size="30" /></div>
    <div class="mb10"><input type="text" name="company_name" id="sub_company_name" value="Your Company Name" onblur="if(this.value=='') this.value='Your Company Name';" onfocus="if(this.value=='Your Company Name') this.value='';" size="30" /></div>
    <div class="mb10"><input type="text" name="phone_number" id="sub_phone_number" value="Your Phone Number" onblur="if(this.value=='') this.value='Your Phone Number';" onfocus="if(this.value=='Your Phone Number') this.value='';" size="30" /></div>
    <div class="mb10">
        <select name="iam" id="iam" style="width:100%;">
            <option value="">I am a:</option>
            <option value="M Homes Partner">M Homes Partner</option>
            <option value="M Homes Client">M Homes Client</option>
        </select>
    </div>
    <span id="msg_subscribe">
    <!--<p><em>We've added you to our list.</em><br />
    <em>Thanks for signing up!</em></p>-->
    </span>
    <a id="btnSubscribe" href="javascript:;"><img src="<?php echo base_url()?>images/btn-register.png"/></a>
</form>