<?php if (isset($_GET['saved'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e( 'Settings updated', 'follow_up_emails' ); ?></p></div>
<?php endif; ?>