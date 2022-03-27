<?php
/**
 * @var \GF_Field $field            The current Nested Form field.
 * @var array     $nested_fields    An array of GF_Field objects.
 * @var array     $nested_form      The form object of the nested form.
 * @var array     $nested_field_ids An array of nested field IDs.
 * @var string    $actions          Generated HTML for displaying related entries link.
 */
?>
<div class="gpnf-nested-entries-container-<?php echo $field->formId; ?>-<?php echo $field->id; ?> gpnf-nested-entries-container gpnf-entry-view ginput_container">

	<table class="gpnf-nested-entries">

		<thead>
		<tr>
			<?php foreach ( $nested_fields as $nested_field ) : ?>
				<th class="gpnf-field-<?php echo $nested_field['id']; ?>">
					<?php echo GFCommon::get_label( $nested_field ); ?>
				</th>
			<?php endforeach; ?>
		</tr>
		</thead>

		<tbody>
		<?php foreach ( $entries as $entry ) : ?>
			<?php $field_values = gp_nested_forms()->get_entry_display_values( $entry, $nested_form, $nested_field_ids ); ?>
			<tr>
				<?php foreach ( $nested_fields as $nested_field ) : ?>
					<td class="gpnf-field"><?php echo rgars( $field_values, "{$nested_field['id']}/label" ); ?></td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>

	</table>

</div>
