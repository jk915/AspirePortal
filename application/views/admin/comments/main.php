<script type="text/javascript">
var comments_foreign_id = "<?php echo $foreign_id; ?>";
var comments_foreign_type = "<?php echo $type; ?>";
</script>
<!--<script type="text/javascript" src="<?php echo base_url("js/admin/comments.js"); ?>"></script>-->

<table cellspacing="0" width="100%" class="left commentlisting"> 
<thead>
    <tr>
        <th width="10%">ID</th>
        <th align="left">Comment</th>
        <th width="10%">Delete</th>
    </tr>
</thead>
<tbody>
    <?php $this->load->view("admin/comments/list", array("comments" => $comments)); ?>
</tbody>
</table>

<a href="javascript:;" class="button right center" id="deletecomment">Delete</a>
<a href="javascript:;" class="button right center" id="newcomment">New Comment</a>

<div class="clear"></div>

<div id="formnewcomment" style="display:none;">
    <label for="comment">Comment:<span class="requiredindicator">*</span></label>
    <textarea id="comment" style="width:400px;"></textarea>
    <input type="hidden" id="comment_id"/>
    
    <div class="clear"></div><br />
    <a href="javascript:;" class="button left center savecomment">Save</a>
</div>
