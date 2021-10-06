<?php
/**
 * Smart Coupons design - Custom
 *
 * @author      StoreApps
 * @package     WooCommerce Smart Coupons/Templates
 *
 * @version     1.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $woocommerce_smart_coupon;

if ( empty( $coupon_object ) || ! is_object( $coupon_object ) ) {
	$coupon_object = new WC_Coupon( $coupon_code );
}

?>
<div class="coupon-container <?php echo esc_attr( $woocommerce_smart_coupon->get_coupon_container_classes() ); ?> <?php echo esc_attr( $classes ); ?>" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<?php
	echo '<div class="coupon-content">
            <div class="discount-info">';

	$discount_title = '';

	if ( ! empty( $coupon_amount ) ) {
		$discount_title = ( true === $is_percent ) ? $coupon_amount . '%' : wc_price( $coupon_amount );
	}

	if ( ! empty( $discount_type ) ) {
		$discount_title .= ' ' . $discount_type;
	}

	$discount_title = apply_filters( 'wc_smart_coupons_display_discount_title', $discount_title, $coupon_object );

	if ( $discount_title ) {

		// Not escaping because 3rd party developer can have HTML code in discount title.
	  echo $discount_title; // phpcs:ignore

	}

	echo '</div>';

	echo '<div class="code">' . esc_html( $coupon_code ) . '</div>';

	$show_coupon_description = get_option( 'smart_coupons_show_coupon_description', 'no' );
	if ( ! empty( $coupon_description ) && 'yes' === $show_coupon_description ) {
		echo '<div class="discount-description">' . esc_html( $coupon_description ) . '</div>';
	}

	if ( ! empty( $expiry_date ) ) {
		echo '<div class="coupon-expire">' . esc_html( $expiry_date ) . '</div>';
	}

	echo '</div>';
	?>
</div>
