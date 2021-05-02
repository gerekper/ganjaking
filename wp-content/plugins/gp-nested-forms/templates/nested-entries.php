<div class="gpnf-nested-entries-container ginput_container">

	<table class="gpnf-nested-entries">

		<thead>
		<tr>
			<?php foreach ( $nested_fields as $nested_field ) : ?>
				<th class="gpnf-field-<?php echo $nested_field['id']; ?>">
					<?php echo GFCommon::get_label( $nested_field ); ?>
				</th>
			<?php endforeach; ?>
			<th class="gpnf-row-actions"><span class="screen-reader-text">Actions</span></th>
		</tr>
		</thead>

		<tbody data-bind="visible: entries().length, foreach: entries">
		<tr data-bind="attr: { 'data-entryid': id }">
			<?php foreach ( $nested_fields as $nested_field ) : ?>
				<td class="gpnf-field"
					data-bind="html: f<?php echo $nested_field['id']; ?>.label, attr: { 'data-value': f<?php echo $nested_field['id']; ?>.label }"
					data-heading="<?php echo GFCommon::get_label( $nested_field ); ?>"
				>&nbsp;</td>
			<?php endforeach; ?>
			<td class="gpnf-row-actions">
				<ul>
					<li class="edit"><a href="#" data-bind="click: $parent.editEntry"><?php echo $labels['edit_entry']; ?></a></li>
					<li class="delete"><a href="#" data-bind="click: $parent.deleteEntry"><?php echo $labels['delete_entry']; ?></a></li>
				</ul>
			</td>
		</tr>
		</tbody>

		<tbody data-bind="visible: entries().length <= 0">
		<tr class="gpnf-no-entries" data-bind="visible: entries().length <= 0">
			<td colspan="<?php echo $column_count; ?>">
				<?php echo $labels['no_entries']; ?>
			</td>
		</tr>
		</tbody>

	</table>

	<?php echo $add_button; ?>
	<?php echo $add_button_message; ?>

</div>
