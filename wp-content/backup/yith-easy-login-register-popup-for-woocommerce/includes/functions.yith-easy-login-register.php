<?php
/**
 * Common plugin functions
 *
 * @author  YITH
 * @package YITH Easy Login & Register Popup For WooCommerce
 * @version 1.0.0
 */

defined( 'YITH_WELRP' ) || exit; // Exit if accessed directly

if ( ! function_exists( 'yith_welrp_replace_placeholder' ) ) {
	/**
	 * Replace plugin placeholder from a string
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @param string $text
	 * @param boolean/WP_User $user
	 * @return string
	 */
	function yith_welrp_replace_placeholder( $text, $user = false ) {

		$placeholders = apply_filters( 'yith_welrp_text_placeholders', [
			'user_email' => $user ? $user->user_email : '',
			'username'   => $user ? ( $user->display_name ? $user->display_name : $user->nickname ) : '',
			'blogname'   => wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES ),
		] );

		foreach ( $placeholders as $key => $value ) {
			$text = str_replace( '[' . $key . ']', $value, $text );
		}

		return $text;
	}
}

if ( ! function_exists( 'yith_welrp_get_std_error_message' ) ) {
	/**
	 * Get standard error message
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @return string
	 */
	function yith_welrp_get_std_error_message() {
		return apply_filters( 'yith_welrp_standard_error_message', __( 'An error has occurred! Please try again.', 'yith-easy-login-register-popup-for-woocommerce' ) );
	}
}

if ( ! function_exists( 'yith_welrp_get_redirect_url_from_posted' ) ) {
	/**
	 * Get request redirect rule
	 *
	 * @since  1.0.0
	 * @author Francesco Licandro <francesco.licandro@yithemes.com>
	 * @return string
	 */
	function yith_welrp_get_redirect_url_from_posted() {
		$origin   = isset( $_POST['origin'] ) ? wc_clean( $_POST['origin'] ) : '';
		$redirect = empty( $_POST['additional'] ) ? wc_get_checkout_url() : home_url( $origin );

		return apply_filters( 'yith_welrp_popup_redirect_url', $redirect );
	}

}