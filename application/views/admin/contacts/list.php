<?php $i = 0;?>
<?php if ($contacts) : ?>
<?php foreach ($contacts->result() AS $contact) : ?>
    <?php
        if($i++ % 2==1) $rowclass = "admintablerow";
        else  $rowclass = "admintablerowalt";
    ?>
    <tr class="<? print $rowclass;?>">
        <td class="admintabletextcell" align="center"><?php echo $contact->contact_id;?></td>
        <td class="admintabletextcell" style="padding-left:12px;"><a href="javascript:;" rel="<?php echo $contact->contact_id;?>" class="editcontact"><?php echo $contact->name;?></a></td>
        <td class="admintabletextcell" style="padding-left:12px;"><?php echo $contact->position;?></td>
        <td class="admintabletextcell" style="padding-left:12px;"><?php echo $contact->phone;?></td>
        <td class="center"><input type="checkbox" class="contacttodelete" value="<?php echo $contact->contact_id;?>" /></td>
    </tr>
<?php endforeach; ?>
<?php endif; ?>