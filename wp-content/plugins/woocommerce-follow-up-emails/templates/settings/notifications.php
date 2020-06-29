<?php if (isset($_GET['settings_updated'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e('Settings updated', 'follow_up_emails'); ?></p></div>
<?php endif; ?>

<?php if (isset($_GET['imported'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php esc_html_e('Data imported successfully', 'follow_up_emails'); ?></p></div>
<?php endif; ?>

<?php if (isset($_GET['subscribers_added'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="updated"><p><?php echo esc_html( sprintf( __('%d subscribers added', 'follow_up_emails'), absint($_GET['subscribers_added']) ) ); // phpcs:ignore WordPress.Security.NonceVerification ?></p></div>
<?php endif; ?>

<?php if (isset($_GET['error'])): // phpcs:ignore WordPress.Security.NonceVerification ?>
	<div id="message" class="error"><p><?php echo wp_kses_post( wp_unslash( $_GET['error'] ) ); // phpcs:ignore WordPress.Security.NonceVerification ?></p></div>
<?php endif; ?>