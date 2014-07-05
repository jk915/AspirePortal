<?php if ($comments) : ?>
<?php foreach ($comments->result() AS $index=>$comment) : ?>
    <tr id="comment_<?php echo $comment->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
        <td class="admintabletextcell" align="center"><?php echo $comment->id;?></td>
        <td class="admintabletextcell" style="padding-left:12px;">
            <span style="font-weight:bold"><?php echo trim("$comment->first_name $comment->last_name")?></span>
            @ <em style="font-style:italic;"><?php echo date('d/m/Y h:i A', $comment->ts_added)?></em>:<br />
            "<?php echo nl2br($comment->comment)?>"
        </td>
        <td class="center"><input type="checkbox" class="commenttodelete" value="<?php echo $comment->id;?>" /></td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>