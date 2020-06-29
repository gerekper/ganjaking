<?php
$type_options = '';
foreach ( $field_types as $k => $v ) {
	$type_options .= '<option value="' . $k . '"' . selected( $field['type'], $k, false ) . '>' . $v . '</option>' . "\n";
}

$autofill_select = '';
if ( $autofill_options ) {
	$autofill_select = '<select name="_ticket_field_autofill[]" style="width: 254px">' . "\n";
	$autofill_select .= '<option value="none">' . __( 'None', 'woocommerce-box-office' ) . '</option>' . "\n";
	$autofill_select .= '<option value="" disabled="disabled">' . __( '--------', 'woocommerce-box-office' ) . '</option>' . "\n";
	foreach ( $autofill_options as $k => $v ) {
		$autofill_select .= '<option value="' . $k . '"' . selected( $field['autofill'], $k, false ) . '>' . $v . '</option>' . "\n";
	}
	$autofill_select .= '</select>' . "\n";
	$autofill_select .= '<br/><br/>' . "\n";
}

$options_style = '';
if ( ! in_array( $field['type'], array( 'select', 'radio', 'checkbox' ) ) ) {
	$options_style = 'display:none';
}

$email_style = '';
if ( 'email' != $field['type'] ) {
	$email_style = 'display:none';
}
?>
<tr class="<?php echo isset( $row ) ? $row : ''; ?>">
	<td class="field_label"><input type="text" class="input_text" placeholder="<?php esc_attr_e( 'Field Label', 'woocommerce-box-office' ); ?>" name="_ticket_field_labels[]" value="<?php echo esc_attr( $field['label'] ); ?>" required="required" /></td>
	<td class="field_type"><select name="_ticket_field_types[]"><?php echo $type_options; ?></select></td>
	<td class="field_options">
		<?php echo $autofill_select; ?>
		<textarea style="<?php echo $options_style; ?>" placeholder="<?php esc_attr_e( 'Comma-separated list of available options', 'woocommerce-box-office' ); ?>" name="_ticket_field_options[]"><?php echo esc_html( $field['options'] ); ?></textarea>
		<div class="email-options" style="<?php echo $email_style; ?>">
			<span class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Use this email address to contact the ticket holder.', 'woocommerce-box-office' ) ); ?>">
				<?php esc_html_e( 'Contact: ', 'woocommerce-box-office' ); ?>
				<select id="" name="_ticket_field_email_contact[]">
					<option value="yes" <?php selected( $field['email_contact'], 'yes', true ); ?>><?php esc_html_e( 'Yes', 'woocommerce-box-office' ); ?></option>
					<option value="no" <?php selected( $field['email_contact'], 'no', true ); ?>><?php esc_html_e( 'No', 'woocommerce-box-office' ); ?></option>
				</select>
			</span>
			<span class="tips" data-tip="<?php echo wc_sanitize_tooltip( __( 'Use this email address for the ticket holder\'s gravatar.', 'woocommerce-box-office' ) ); ?>">
				<?php esc_html_e( 'Gravatar: ', 'woocommerce-box-office' ); ?>
				<select name="_ticket_field_email_gravatar[]">
					<option value="yes" <?php selected( $field['email_gravatar'], 'yes', true ); ?>><?php esc_html_e( 'Yes', 'woocommerce-box-office' ); ?></option>
					<option value="no" <?php selected( $field['email_gravatar'], 'no', true ); ?>><?php esc_html_e( 'No', 'woocommerce-box-office' ); ?></option>
				</select>
			</span>
		</div>
	</td>
	<td class="field_required" width="1%"><select name="_ticket_field_required[]"><option value="yes" <?php selected( $field['required'], 'yes', true ); ?>><?php esc_html_e( 'Yes', 'woocommerce-box-office' ); ?></option><option value="no" <?php selected( $field['required'], 'no', true ); ?>><?php esc_html_e( 'No', 'woocommerce-box-office' ); ?></option></select></td>
	<td width="1%"><a href="#" class="delete"><?php esc_html_e( 'Delete', 'woocommerce-box-office' ); ?></a></td>
</tr>
