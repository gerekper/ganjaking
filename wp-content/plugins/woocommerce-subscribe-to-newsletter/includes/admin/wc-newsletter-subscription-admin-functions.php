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

/**
 * Gets the current screen ID.
 *
 * @since 3.0.0
 *
 * @return string|false The screen ID. False otherwise.
 */
function wc_newsletter_subscription_get_current_screen_id() {
	$screen_id = false;

	// It may not be available.
	if ( function_exists( 'get_current_screen' ) ) {
		$screen    = get_current_screen();
		$screen_id = isset( $screen, $screen->id ) ? $screen->id : false;
	}

	// Get the value from the request.
	if ( ! $screen_id && ! empty( $_REQUEST['screen'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
		$screen_id = wc_clean( wp_unslash( $_REQUEST['screen'] ) ); // phpcs:ignore WordPress.Security.NonceVerification
	}

	return $screen_id;
}

/**
 * Processes the AJAX request for fetching the provider lists.
 *
 * Use the POST param 'refresh' to force a refresh.
 *
 * @since 3.1.0
 */
function wc_newsletter_subscription_ajax_provider_lists() {
	check_ajax_referer( 'get-newsletter-subscription-lists' );

	$provider = wc_newsletter_subscription_get_provider();

	if ( $provider ) {
		if ( isset( $_POST['refresh'] ) && wc_string_to_bool( sanitize_text_field( wp_unslash( $_POST['refresh'] ) ) ) ) {
			$provider->clear_lists();
		}

		wp_send_json_success( $provider->get_lists() );
	}

	wp_send_json_error();
}
add_action( 'wp_ajax_wc_newsletter_subscription_provider_lists', 'wc_newsletter_subscription_ajax_provider_lists' );
