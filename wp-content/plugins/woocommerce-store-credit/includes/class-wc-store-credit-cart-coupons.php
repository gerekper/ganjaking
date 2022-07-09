<?php
/**
 * Class to handle the store credit cart coupons.
 *
 * @package WC_Store_Credit/Classes
 * @since   4.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * WC_Store_Credit_Cart_Coupons class.
 */
class WC_Store_Credit_Cart_Coupons {

	/**
	 * Constructor.
	 *
	 * @since 4.2.0
	 */
	public function __construct() {
		add_action( 'template_redirect', array( $this, 'apply_coupon_from_url' ) );
		add_action( 'woocommerce_add_to_cart', array( $this, 'apply_coupons_from_session' ), 20 );
		add_action( 'woocommerce_before_cart', array( $this, 'available_coupons_content' ), 5 );
		add_action( 'woocommerce_before_checkout_form', array( $this, 'available_coupons_content' ) );
		add_filter( 'woocommerce_add_to_cart_fragments', array( $this, 'refresh_fragments' ) );
		add_filter( 'woocommerce_update_order_review_fragments', array( $this, 'refresh_fragments' ) );
	}

	/**
	 * Applies a store credit coupon by URL.
	 *
	 * @since 4.2.0
	 */
	public function apply_coupon_from_url() {
		if ( ! isset( $_GET['redeem_store_credit'] ) ) { // phpcs:ignore WordPress.Security.NonceVerification
			return;
		}

		$coupon_code = rawurldecode( wc_clean( wp_unslash( $_GET['redeem_store_credit'] ) ) ); // phpcs:ignore WordPress.Security.NonceVerification

		if ( ! $coupon_code || ! wc_is_store_credit_coupon( $coupon_code ) ) {
			wc_add_notice( __( 'Store credit coupon not found.', 'woocommerce-store-credit' ), 'error' );
			wp_safe_redirect( remove_query_arg( 'redeem_store_credit' ) );
			exit;
		}

		if ( ! WC()->cart || WC()->cart->is_empty() ) {
			WC_Store_Credit_Session::add_coupon( $coupon_code );
			wc_add_notice( __( 'The store credit will be applied after adding some products to the cart.', 'woocommerce-store-credit' ) );
			wp_safe_redirect( wc_get_page_permalink( 'shop' ) );
			exit;
		}

		WC()->cart->apply_coupon( $coupon_code );
		wp_safe_redirect( wc_get_cart_url() );
		exit;
	}

	/**
	 * Applies the store credit coupons stored in session.
	 *
	 * @since 4.2.0
	 */
	public function apply_coupons_from_session() {
		$cart    = WC()->cart;
		$coupons = WC_Store_Credit_Session::get_coupons();

		if ( ! $cart || empty( $coupons ) ) {
			return;
		}

		foreach ( $coupons as $coupon_code ) {
			$cart->apply_coupon( $coupon_code );
		}

		WC_Store_Credit_Session::clear_coupons();
	}

	/**
	 * Gets if display cart notice is enabled.
	 *
	 * @since 4.2.0
	 *
	 * @return bool
	 */
	public function display_cart_notice() {
		return ( is_user_logged_in() && wc_coupons_enabled() && wc_string_to_bool( get_option( 'wc_store_credit_show_cart_notice', 'yes' ) ) );
	}

	/**
	 * Outputs the available coupons.
	 *
	 * @since 4.2.0
	 */
	public function available_coupons_content() {
		if ( ! $this->display_cart_notice() ) {
			return;
		}

		$coupons = wc_store_credit_get_customer_coupons( get_current_user_id() );

		if ( empty( $coupons ) ) {
			return;
		}

		$cart_coupons = WC()->cart->get_applied_coupons();

		// Exclude coupons already applied to the cart.
		foreach ( $coupons as $index => $coupon ) {
			if ( in_array( $coupon->get_code(), $cart_coupons, true ) ) {
				unset( $coupons[ $index ] );
			}
		}

		wc_store_credit_get_template( 'cart/store-credit-coupons.php', compact( 'coupons' ) );
	}

	/**
	 * Filters the cart fragments to be refreshed.
	 *
	 * @since 4.2.0
	 *
	 * @param array $fragments The cart fragments.
	 * @return array
	 */
	public function refresh_fragments( $fragments ) {
		ob_start();

		$this->available_coupons_content();

		$fragments['.wc-store-credit-cart-coupons-container.refresh'] = ob_get_clean();

		return $fragments;
	}
}
