<?php if ($comments) : ?>
    <?php foreach ($comments->result() AS $index=>$comment) : ?>
        <tr id="acomment_<?php echo $comment->id?>" class="<?php echo $index%2 ? 'admintablerowalt' : 'admintablerow';?>">
            <td class="admintabletextcell" style="padding-left:12px;">
                <span style="font-weight:bold"><?php echo trim("$comment->first_name $comment->last_name")?></span>:<br />
                "<?php echo nl2br($comment->comment)?>"
            </td>
            <td class="admintabletextcell" align="center"><?php echo date('d/m/Y', $comment->ts_added);?></td>
        </tr>
    <?php endforeach; ?>
<?php endif; ?>
