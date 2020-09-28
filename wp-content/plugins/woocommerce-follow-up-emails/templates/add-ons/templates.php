<div class="wrap fue-addons-wrap">
	<h2>
		<?php esc_html_e( 'Templates', 'follow_up_emails' ); ?>
		<a class="add-new-h2" href="admin.php?page=followup-emails-templates&action=new"><?php esc_html_e( 'Create New Template', 'follow_up_emails' ); ?></a>
	</h2>

<?php
	require FUE_TEMPLATES_DIR . '/add-ons/notifications.php';

	if ( isset( $_POST['action'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		if ( 'template_create' == $_POST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			include FUE_TEMPLATES_DIR . '/add-ons/template-create.php';
		} elseif ( 'template_upload' == $_POST['action'] ) { // phpcs:ignore WordPress.Security.NonceVerification
			if ( isset( $_FILES['template']['tmp_name'] ) && is_uploaded_file( wp_unslash( $_FILES['template']['tmp_name'] ) ) ) {
				include FUE_TEMPLATES_DIR . '/add-ons/template-upload.php';
			} else {
				show_message( __( 'No file selected or file is too large', 'follow_up_emails' ) );
			}
		}
	} else {
		$template_action = empty( $_GET['action'] ) ? 'list' : sanitize_text_field( wp_unslash( $_GET['action'] ) ); // phpcs:ignore WordPress.Security.NonceVerification

		switch ( $template_action ) {
			case 'uninstall_template':
				include FUE_TEMPLATES_DIR . '/add-ons/templates-uninstall.php';
				break;

			case 'new':
				include FUE_TEMPLATES_DIR . '/add-ons/templates-new.php';
				break;

			default:
				include FUE_TEMPLATES_DIR . '/add-ons/templates-list.php';
				break;
		}
	}
?>

</div>
