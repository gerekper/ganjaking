<?php
/**
 * Admin functions.
 *
 * @package WC_Store_Credit/Admin/Functions
 * @since   2.4.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the current screen ID.
 *
 * @since 2.4.0
 *
 * @return string|false The screen ID. False otherwise.
 */
function wc_store_credit_get_current_screen_id() {
	// phpcs:disable WordPress.Security.NonceVerification
	$screen_id = false;

	// It may not be available.
	if ( function_exists( 'get_current_screen' ) ) {
		$screen    = get_current_screen();
		$screen_id = isset( $screen, $screen->id ) ? $screen->id : false;
	}

	// Get the value from the request.
	if ( ! $screen_id && ! empty( $_REQUEST['screen'] ) ) {
		$screen_id = wc_clean( wp_unslash( $_REQUEST['screen'] ) );
	}

	return $screen_id;
	// phpcs:enable WordPress.Security.NonceVerification
}

/**
 * Gets if we are in the Store Credit settings page.
 *
 * @since 4.2.0
 *
 * @return bool
 */
function wc_store_credit_is_settings_page() {
	// phpcs:disable WordPress.Security.NonceVerification
	return (
		is_admin() &&
		isset( $_GET['page'] ) && 'wc-settings' === $_GET['page'] &&
		isset( $_GET['tab'] ) && 'store_credit' === $_GET['tab']
	);
	// phpcs:enable WordPress.Security.NonceVerification
}

/**
 * Gets the specified admin url.
 *
 * @since 2.4.0
 *
 * @param array $extra_params Optional. Additional parameters in pairs key => value.
 * @return string The admin page url.
 */
function wc_store_credit_get_settings_url( $extra_params = array() ) {
	$url = 'admin.php?page=wc-settings&tab=store_credit';

	if ( ! empty( $extra_params ) ) {
		foreach ( $extra_params as $param => $value ) {
			$url .= '&' . esc_attr( $param ) . '=' . rawurlencode( $value );
		}
	}

	return admin_url( $url );
}

/**
 * Gets the menu slug for the 'Send Store Credit' page.
 *
 * @since 3.0.0
 *
 * @return string
 */
function wc_store_credit_get_send_credit_menu_slug() {
	/**
	 * Filters the menu slug for the 'Send Store Credit' page.
	 *
	 * @since 3.0.0
	 *
	 * @param string $slug The menu slug.
	 */
	return apply_filters( 'wc_store_credit_send_credit_menu_slug', 'send-store-credit' );
}

/**
 * Gets the screen ID for the 'Send Store Credit' page.
 *
 * @since 3.0.0
 *
 * @return string
 */
function wc_store_credit_get_send_credit_screen_id() {
	$wc_screen_id = sanitize_title( __( 'WooCommerce', 'woocommerce' ) ); // phpcs:ignore WordPress.WP.I18n
	$slug         = wc_store_credit_get_send_credit_menu_slug();

	/**
	 * Filters the screen ID for the 'Send Store Credit' page.
	 *
	 * @since 3.0.0
	 *
	 * @param string $screen_id The screen ID.
	 */
	return apply_filters( 'wc_store_credit_send_credit_screen_id', "{$wc_screen_id}_page_{$slug}" );
}

/**
 * Gets the dismiss url for a notice.
 *
 * @since 3.2.0
 *
 * @param string $notice   The notice ID.
 * @param mixed  $base_url Optional. Base URL to append the dismiss arguments. Default false.
 * @return string
 */
function wc_store_credit_get_notice_dismiss_url( $notice, $base_url = false ) {
	return wp_nonce_url( add_query_arg( 'wc-hide-notice', $notice, $base_url ), 'woocommerce_hide_notices_nonce', '_wc_notice_nonce' );
}

/**
 * Gets the dismiss link for a notice.
 *
 * @since 3.2.0
 *
 * @param string $notice   The notice ID.
 * @param mixed  $base_url Optional. Base URL to append the dismiss arguments. Default false.
 * @return string
 */
function wc_store_credit_get_notice_dismiss_link( $notice, $base_url = false ) {
	$dismiss_url = wc_store_credit_get_notice_dismiss_url( $notice, $base_url );

	return sprintf(
		'<a class="woocommerce-message-close notice-dismiss" href="%1$s">%2$s</a>',
		esc_url( $dismiss_url ),
		esc_html__( 'Dismiss', 'woocommerce-store-credit' )
	);
}
