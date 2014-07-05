<!-- Listing will load here via AJAX -->
<?php if (isset($notes) && $notes) : ?>
	<?php foreach ($notes->result() AS $note) : ?>
	<tr>
		<td>
			<a class="shownote" href="<?php echo $note->note_id; ?>">
				<?php echo $this->utilities->iso_to_ukdate($note->note_date); ?>
			</a>
		</td>
		<td><?php echo $note->content; ?></td>
		<td><input class="delete_note" type="checkbox" value="<?php echo $note->note_id; ?>" /></td>
	</tr>
	<?php endforeach; ?>
<?php endif; ?>