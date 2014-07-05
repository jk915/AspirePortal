<h1><?php echo $article->article_title; ?></h1>
<!--<pre><?php print_r($article)?></pre>-->
<dl>
    <?php if($article->article_date != "") : ?>
    <dt>Publication Date</dt>
    <dd><?php echo $this->utilities->iso_to_ukdate($article->article_date); ?></dd>
    <?php endif; ?>
    <?php if($article->last_modification_dtm != "") : ?>
    <dt>Last Modified</dt>
    <dd><?php echo date('d/m/Y', strtotime($article->last_modification_dtm)); ?></dd>
    <?php endif; ?>
    <?php if($article->tags != "") : ?>
    <dt>Tags</dt>
    <dd><?php echo substr($article->tags, 1, strlen($article->tags) - 2); ?></dd>
    <?php endif; ?>
    <?php if($article->www != "") : ?>
    <dt>Web Link</dt>
    <dd><a href="<?php echo addhttp($article->www); ?>" target="_blank">Click here to view</a></dd>
    <?php endif; ?>
    <?php if($article->author != "") : ?>
    <dt>Author</dt>
    <dd><?php echo $article->author; ?></dd>
    <?php endif; ?>
    <?php if($article->source != "") : ?>
    <dt>Source</dt>
    <dd><?php echo $article->source; ?></dd>
    <?php endif; ?>  
    <?php if($article->category_name != "") : ?>
    <dt>Document Type</dt>
    <dd><?php echo $article->category_name; ?></dd>
    <?php endif; ?>          
</dl>

<?php if($article->link_www != "") : ?>
<a class="btn" href="<?php echo $article->link_www; ?>" target="_blank">Download / View Resource</a>
<?php endif; ?>

<a class="btn email_resource" href="javascript:;">Email Resource</a>

<div id="emailResource">
	<label style="float:right;"><input type="checkbox" id="ck_external_users" value="1"/> Send to external users.</label>
	<div class="clear"></div>
	<div class="send_to_users_network">
		<?php echo form_open('media/ajax', array("id" => "frmEmailResourceNetwork", "name" => "frmEmailResourceNW")); ?>
			<?php $logged_user_id = $this->session->userdata["user_id"]; ?>
	        <input type="hidden" name="action" value="email_resource" />
	        <input type="hidden" id="article_id" name="article_id" value="<?php echo $article->article_id; ?>" />
	        
			<label for="email_resource_to">Email Resource To</label>
			<select name="email_resource_to" id="email_resource_to">
				<option value=""></option>
				<?php echo $this->utilities->print_select_options($users, "email", "client_name", $email_logged);?>
			</select>
			
			<label for="email_resource">Email Address <span class="required">*</span></label>
			<input type="text" name="email_resource" value="" class="email_resource" id="email_resource" readonly="readonly"/>
			<p><a class="btn" href="javascript:;" id="submit_email_resource_nw">Send</a></p>
		</form>
	</div>
	<div class="send_to_external_users" style="display:none;">
		<?php echo form_open('media/ajax', array("id" => "frmEmailResourceExternal", "name" => "frmEmailResourceEX")); ?>
	        <input type="hidden" name="action" value="email_resource" />
	        <input type="hidden" id="article_id" name="article_id" value="<?php echo $article->article_id; ?>" />
	        
			<label for="name">Name</label>
			<input type="text" name="name" value="" id="name"/>
			
			<label for="email_resource">Email Address <span class="required">*</span></label>
			<input type="text" name="email_resource" value="" class="email_resource"/>
			<p><a class="btn" href="javascript:;" id="submit_email_resource_ex">Send</a></p>
		</form>
	</div>
</div>

<?php if($article->video_code != "") : ?>    
<div id="modal_video">
<?php echo html_entity_decode($article->video_code); ?>
</div>
<?php else : ?>
	<?php if ($article->hero_image != "") : ?>
<div id="modal_video">
	<img src="<?php echo site_url($article->hero_image.'_detail.jpg'); ?>" width="400" height="300"/>
</div>	
	<?php endif; ?>
<?php endif; ?>

<?php if($article->comments != "") : ?>
<div class="comments"><p><?php echo nl2br($article->comments); ?></p></div>
<?php endif; ?>


