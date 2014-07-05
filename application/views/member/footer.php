            <!-- end main --></div>            
        <!-- end wrapper --></div>         
        <div id="footer">
            <div class="content">
            	<img width="290" height="71" border="0" alt="Back to the home page." src="<?php echo base_url()?>images/member/logo.png" class="logo_footer">
                <p class="headerFont1">&copy; ASPIRE Advisor Network 2012. (LAPPS&#8482;)</p>               
                <div class="contact">
                    <h6 class="headerFont">ASPIRE Advisor Network</h6>
                    <p><em class="headerFont">Phone</em> <em class="headerFont1"><?php  echo (defined("OWNERDETAILS_phone") && OWNERDETAILS_phone != "") ? OWNERDETAILS_phone : '';?></em><br />
                    <em class="headerFont">Fax</em> <em class="headerFont1"><?php echo (defined("OWNERDETAILS_fax") && OWNERDETAILS_fax != "") ? OWNERDETAILS_fax : '';?></em><br />
                    <em class="headerFont">Email</em> <a class="headerFont1" href="mailto:<?php echo (defined("OWNERDETAILS_email") && OWNERDETAILS_email != "") ? OWNERDETAILS_email : '';?>"><?php echo (defined("OWNERDETAILS_email") && OWNERDETAILS_email != "") ? OWNERDETAILS_email : '';?></a></p>
                </div>              
            <!-- end footer content --></div>     
        <!-- end footer --></div> 
        <div id="ajaxmodal"></div>        
    </body>     
</html>   