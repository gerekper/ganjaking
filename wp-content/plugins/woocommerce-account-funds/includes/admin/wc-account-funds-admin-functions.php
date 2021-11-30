<?php
/**
 * Admin functions
 *
 * @package WC_Account_Funds/Admin/Functions
 * @since   2.3.7
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the current screen ID.
 *
 * @since 2.3.7
 *
 * @return string|false The screen ID. False otherwise.
 */
function wc_account_funds_get_current_screen_id() {
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
 * Gets if we are in the plugin settings page or not.
 *
 * @since 2.6.0
 *
 * @return bool
 */
function wc_account_funds_is_settings_page() {
	// phpcs:disable WordPress.Security.NonceVerification
	return ( is_admin() &&
		isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] &&
		isset( $_GET['tab'] ) && 'account_funds' === $_GET['tab']
	);
	// phpcs:enable WordPress.Security.NonceVerification
}

/**
 * Gets the dismiss url for a notice.
 *
 * @since 2.3.7
 *
 * @param string $notice   The notice ID.
 * @param mixed  $base_url Optional. Base URL to append the dismiss arguments. Default false.
 * @return string
 */
function wc_account_funds_get_notice_dismiss_url( $notice, $base_url = false ) {
	return wp_nonce_url( add_query_arg( 'wc-hide-notice', $notice, $base_url ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' );
}

/**
 * Gets the dismiss link for a notice.
 *
 * @since 2.3.7
 *
 * @param string $notice   The notice ID.
 * @param mixed  $base_url Optional. Base URL to append the dismiss arguments. Default false.
 * @return string
 */
function wc_account_funds_get_notice_dismiss_link( $notice, $base_url = false ) {
	$dismiss_url = wc_account_funds_get_notice_dismiss_url( $notice, $base_url );

	return sprintf(
		'<a class="woocommerce-message-close notice-dismiss" href="%1$s">%2$s</a>',
		esc_url( $dismiss_url ),
		esc_html__( 'Dismiss', 'woocommerce-account-funds' )
	);
}
