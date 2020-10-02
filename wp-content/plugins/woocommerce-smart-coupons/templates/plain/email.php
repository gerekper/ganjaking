<?php
/**
 * Coupon Email Content
 *
 * @author      StoreApps
 * @version     1.1.0
 * @package     woocommerce-smart-coupons/templates/plain/
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $store_credit_label, $woocommerce_smart_coupon;

echo "=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n";
echo esc_html( wp_strip_all_tags( $email_heading ) );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
?>

<?php
/* translators: %s: Coupon code */
echo sprintf( esc_html__( 'To redeem your discount use coupon code %s during checkout or copy and paste the below URL and hit enter in your browser:', 'woocommerce-smart-coupons' ), esc_html( $coupon_code ) );
if ( ! empty( $message_from_sender ) ) {
	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
	echo esc_html__( 'Message:', 'woocommerce-smart-coupons' );
	echo $message_from_sender; // phpcs:ignore
}
?>

<?php

$coupon = new WC_Coupon( $coupon_code );

if ( $woocommerce_smart_coupon->is_wc_gte_30() ) {
	if ( ! is_object( $coupon ) || ! is_callable( array( $coupon, 'get_id' ) ) ) {
		return;
	}
	$coupon_id = $coupon->get_id();
	if ( empty( $coupon_id ) ) {
		return;
	}
	$coupon_amount    = $coupon->get_amount();
	$is_free_shipping = ( $coupon->get_free_shipping() ) ? 'yes' : 'no';
	$expiry_date      = $coupon->get_date_expires();
	$coupon_code      = $coupon->get_code();
} else {
	$coupon_id        = ( ! empty( $coupon->id ) ) ? $coupon->id : 0;
	$coupon_amount    = ( ! empty( $coupon->amount ) ) ? $coupon->amount : 0;
	$is_free_shipping = ( ! empty( $coupon->free_shipping ) ) ? $coupon->free_shipping : '';
	$expiry_date      = ( ! empty( $coupon->expiry_date ) ) ? $coupon->expiry_date : '';
	$coupon_code      = ( ! empty( $coupon->code ) ) ? $coupon->code : '';
}

$coupon_post = get_post( $coupon_id );

$coupon_data = $woocommerce_smart_coupon->get_coupon_meta_data( $coupon );

$coupon_target              = '';
$wc_url_coupons_active_urls = get_option( 'wc_url_coupons_active_urls' ); // From plugin WooCommerce URL coupons.
if ( ! empty( $wc_url_coupons_active_urls ) ) {
	$coupon_target = ( ! empty( $wc_url_coupons_active_urls[ $coupon_id ]['url'] ) ) ? $wc_url_coupons_active_urls[ $coupon_id ]['url'] : '';
}
if ( ! empty( $coupon_target ) ) {
	$coupon_target = home_url( '/' . $coupon_target );
} else {
	$coupon_target = home_url( '/?sc-page=shop&coupon-code=' . $coupon_code );
}

$coupon_target = apply_filters( 'sc_coupon_url_in_email', $coupon_target, $coupon );

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo esc_url( $coupon_target );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

echo esc_html__( 'Discount:', 'woocommerce-smart-coupons' );
if ( ! empty( $coupon_data['coupon_amount'] ) && 0 !== $coupon_amount ) {
	echo $coupon_data['coupon_amount']; // phpcs:ignore
	echo ' ' . $coupon_data['coupon_type'];  // phpcs:ignore
	if ( 'yes' === $is_free_shipping ) {
		echo esc_html__( ' &amp; ', 'woocommerce-smart-coupons' );
	}
}

if ( 'yes' === $is_free_shipping ) {
	echo esc_html__( 'Free Shipping', 'woocommerce-smart-coupons' );
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo esc_html__( 'Coupon Code:', 'woocommerce-smart-coupons' );
echo esc_html( $coupon_code );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";

$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
if ( ! empty( $coupon_post->post_excerpt ) && 'yes' === $show_coupon_description ) {
	echo esc_html__( 'Description:', 'woocommerce-smart-coupons' );
	echo $coupon_post->post_excerpt; // phpcs:ignore
	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
}

echo esc_html__( 'Expires on:', 'woocommerce-smart-coupons' );
if ( ! empty( $expiry_date ) ) {
	if ( $woocommerce_smart_coupon->is_wc_gte_30() && $expiry_date instanceof WC_DateTime ) {
		$expiry_date = $expiry_date->getTimestamp();
	} elseif ( ! is_int( $expiry_date ) ) {
		$expiry_date = strtotime( $expiry_date );
	}

	if ( ! empty( $expiry_date ) && is_int( $expiry_date ) ) {
		$expiry_time = (int) get_post_meta( $coupon_id, 'wc_sc_expiry_time', true );
		if ( ! empty( $expiry_time ) ) {
			$expiry_date += $expiry_time; // Adding expiry time to expiry date.
		}
	}
	$expiry_date = $woocommerce_smart_coupon->get_expiration_format( $expiry_date );
	echo esc_html( $expiry_date );
} else {
	echo esc_html__( 'Never expires', 'woocommerce-smart-coupons' );
}

echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
echo esc_html__( 'Visit store', 'woocommerce-smart-coupons' );
echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
$site_url = ! empty( $url ) ? $url : home_url();
echo esc_url( $site_url );
if ( ! empty( $from ) ) {
	/* translators: %s: singular name for store credit */
	echo ( ! empty( $store_credit_label['singular'] ) ? sprintf( esc_html__( 'You got this %s', 'woocommerce-smart-coupons' ), esc_html( strtolower( $store_credit_label['singular'] ) ) ) : esc_html__( 'You got this gift card', 'woocommerce-smart-coupons' ) ) . ' ' . esc_html( $from ) . esc_html( $sender );
	echo "\n=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=-=\n\n";
}
