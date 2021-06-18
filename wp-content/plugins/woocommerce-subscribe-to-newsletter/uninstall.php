<?php
/**
 * WooCommerce Newsletter Subscription Uninstall
 *
 * Deletes the plugin options.
 *
 * @package WC_Newsletter_Subscription/Uninstaller
 * @version 2.9.0
 */

defined( 'WP_UNINSTALL_PLUGIN' ) || exit;

/**
 * Plugin uninstall script.
 *
 * @since 2.9.0
 */
function wc_newsletter_subscription_uninstall() {
	// Delete the service provider credentials.
	delete_option( 'woocommerce_newsletter_service' );
	delete_option( 'woocommerce_mailchimp_api_key' );
	delete_option( 'woocommerce_cmonitor_api_key' );
	delete_option( 'woocommerce_sendgrid_api_key' );
	delete_option( 'woocommerce_mailerlite_api_key' );
	delete_option( 'woocommerce_activetrail_api_key' );
}
wc_newsletter_subscription_uninstall();
