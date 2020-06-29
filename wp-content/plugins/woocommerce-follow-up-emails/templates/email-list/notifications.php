<?php if (isset($_GET['created'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e('Follow-up email created', 'follow_up_emails'); ?></p></div>
<?php endif; ?>

<?php
if (isset($_GET['updated'])): // phpcs:ignore WordPress.Security.NonceVerification
	$message = (empty($_GET['message'])) ? __('Follow-up email updated', 'follow_up_emails') : sanitize_text_field( wp_unslash( $_GET['message'])); // phpcs:ignore WordPress.Security.NonceVerification
?>
	<div id="message" class="updated"><p><?php echo esc_html( $message ); ?></p></div>
<?php endif; ?>

<?php if (isset($_GET['deleted'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e('Follow-up email deleted!', 'follow_up_emails'); ?></p></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="error"><p><?php echo esc_html( sanitize_text_field( wp_unslash( $_GET['error'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification ?></p></div>
<?php endif; ?>

<?php if (isset($_GET['manual_sent'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e('Email(s) have been added to the queue', 'follow_up_emails'); ?></p></div>
<?php endif; ?>

<?php do_action('fue_settings_notification'); ?>