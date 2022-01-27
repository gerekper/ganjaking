<?php
/**
 * Admin functions
 *
 * @package WC_Instagram/Admin/Functions
 * @since   2.0.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the current screen ID.
 *
 * @since 2.0.0
 *
 * @return string|false The screen ID. False otherwise.
 */
function wc_instagram_get_current_screen_id() {
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
 * Gets if we are in the WooCommerce Instagram settings page or not.
 *
 * @since 2.0.0
 *
 * @global string $current_section The current settings section.
 *
 * @return bool
 */
function wc_instagram_is_settings_page() {
	// phpcs:disable WordPress.Security.NonceVerification
	global $current_section;

	$is_settings_page = false;

	$is_integration = (
		is_admin() &&
		isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] &&
		isset( $_GET['tab'] ) && 'integration' === $_GET['tab']
	);

	if ( $is_integration ) {
		$is_settings_page = ( 'instagram' === $current_section || ( isset( $_GET['section'] ) && 'instagram' === $_GET['section'] ) );

		// Fetch the first integration ID.
		if ( ! $is_settings_page && empty( $_GET['section'] ) && empty( $current_section ) ) {
			$integrations = WC()->integrations->get_integrations();

			if ( ! empty( $integrations ) && 'instagram' === current( $integrations )->id ) {
				$is_settings_page = true;
			}
		}
	}

	return $is_settings_page;
	// phpcs:enable WordPress.Security.NonceVerification
}

/**
 * Gets the authorization URL for the specified action.
 *
 * @since 2.1.0
 *
 * @param string $action The action.
 * @return string
 */
function wc_instagram_get_authorization_url( $action ) {
	return wp_nonce_url( wc_instagram_get_settings_url( array( 'action' => $action ) ), 'wc_instagram_' . $action, 'nonce' );
}

/**
 * Gets the dismiss url for a notice.
 *
 * @since 3.0.0
 *
 * @param string $notice   The notice ID.
 * @param mixed  $base_url Optional. Base URL to append the dismiss arguments. Default false.
 * @return string
 */
function wc_instagram_get_notice_dismiss_url( $notice, $base_url = false ) {
	return wp_nonce_url( add_query_arg( 'wc-hide-notice', $notice, $base_url ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' );
}

/**
 * Gets the dismiss link for a notice.
 *
 * @since 3.0.0
 *
 * @param string $notice   The notice ID.
 * @param mixed  $base_url Optional. Base URL to append the dismiss arguments. Default false.
 * @return string
 */
function wc_instagram_get_notice_dismiss_link( $notice, $base_url = false ) {
	$dismiss_url = wc_instagram_get_notice_dismiss_url( $notice, $base_url );

	return sprintf(
		'<a class="woocommerce-message-close notice-dismiss" href="%1$s">%2$s</a>',
		esc_url( $dismiss_url ),
		esc_html__( 'Dismiss', 'woocommerce-instagram' )
	);
}

/**
 * Outputs the loading spinner content.
 *
 * @since 4.0.0
 */
function wc_instagram_loading() {
	echo '<div class="wc-instagram-loading"><div></div><div></div><div></div></div>';
}
