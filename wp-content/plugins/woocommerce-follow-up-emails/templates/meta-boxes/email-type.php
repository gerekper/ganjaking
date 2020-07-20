<?php
/* @var FUE_Email $email */
$types = Follow_Up_Emails::get_email_types();

if ( empty( $email->type ) && ! empty( $_GET['type'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
	$email->type = sanitize_text_field( wp_unslash( $_GET['type'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
}
?>
<input type="hidden" id="email_id" value="<?php echo esc_attr( $email->id ); ?>" />
<select id="email_type" name="email_type" class="select2" data-placeholder="<?php esc_attr_e( 'Please select an email type', 'follow_up_emails' ); ?>" style="width: 100%;" data-nonce="<?php echo esc_attr( wp_create_nonce( 'follow_up_type' ) ); ?>">
	<?php foreach ( $types as $type ) : ?>
	<option value="<?php echo esc_attr( $type->id ); ?>" <?php selected( $email->type, $type->id ); ?>><?php echo esc_html( $type->singular_label ); ?></option>
	<?php endforeach; ?>
</select>

<?php foreach ( $types as $type ) : ?>
	<p class="email-type-description" id="<?php echo esc_attr( $type->id ); ?>_desc" style="display: none;"><?php echo esc_html( $type->short_description ); ?></p>
<?php endforeach; ?>
