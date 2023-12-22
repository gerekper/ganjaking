<?php
/**
 * Template Functions
 *
 * @package WC_Store_Credit/Functions
 * @since   4.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Gets the title for the cart coupons section.
 *
 * @since 4.2.0
 *
 * @return string
 */
function wc_store_credit_get_cart_title() {
	$text = get_option( 'wc_store_credit_cart_notice' );

	if ( ! $text ) {
		$text = sprintf(
			'%1$s [link]%2$s[/link]',
			__( 'You have store credit coupons available!', 'woocommerce-store-credit' ),
			__( 'View coupons', 'woocommerce-store-credit' )
		);
	}

	$find_replace = array(
		'[link]'  => '<a class="show-store-credit-coupons" href="#">',
		'[/link]' => '</a>',
	);

	return str_replace( array_keys( $find_replace ), array_values( $find_replace ), $text );
}

if ( ! function_exists( 'wc_store_credit_cart_coupon' ) ) {
	/**
	 * Outputs a Store Credit coupon.
	 *
	 * @since 4.2.0
	 *
	 * @param WC_Coupon $coupon Coupon object.
	 */
	function wc_store_credit_cart_coupon( $coupon ) {
		wc_store_credit_get_template( 'cart/store-credit-coupon.php', array( 'coupon' => $coupon ) );
	}
}
