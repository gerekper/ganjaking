<?php
/**
 * Admin functions
 *
 * @package WC_Newsletter_Subscription/Admin/Functions
 * @since   2.8.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets if we are in the plugin settings page or not.
 *
 * @since 2.8.0
 *
 * @return bool
 */
function wc_newsletter_subscription_is_settings_page() {
	// phpcs:disable WordPress.Security.NonceVerification
	return ( is_admin()
		&& isset( $_GET['page'] ) && 'wc-settings' === $_GET['page']
		&& isset( $_GET['tab'] ) && 'newsletter' === $_GET['tab']
	);
	// phpcs:enable WordPress.Security.NonceVerification
}
