<?php
/**
 * Smart Coupons design - Ticket
 *
 * @author      StoreApps
 * @package     WooCommerce Smart Coupons/Templates
 *
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

global $woocommerce_smart_coupon;

?>
<div class="max-w-xs transition duration-100 ease-in-out transform rounded-md sc-coupon hover:-translate-y-1 hover:shadow <?php echo esc_attr( $classes ); ?>"
	style="
			background-color: var(--sc-color1);
			color: var(--sc-color1);
			min-height: 6rem;
		" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<svg class="absolute w-full h-full" style="color: var(--sc-color2)" viewBox="0 0 230 100"
		xmlns="http://www.w3.org/2000/svg" fill="currentColor" stroke="none" preserveAspectRatio="none">
		<path
			d="m221.76 17.82h.31v-2.58h-.31a4.27 4.27 0 0 1 -4.26-4h-205a4.27 4.27 0 0 1 -4.26 4h-.31v2.59h.31a4.28 4.28 0 0 1 4.26 4v.62a4.28 4.28 0 0 1 -4.26 4h-.31v2.59h.31a4.28 4.28 0 0 1 4.26 4v.61a4.28 4.28 0 0 1 -4.26 4h-.31v2.6h.31a4.28 4.28 0 0 1 4.26 4v.61a4.28 4.28 0 0 1 -4.26 4h-.31v2.59h.31a4.28 4.28 0 0 1 4.26 4v.62a4.28 4.28 0 0 1 -4.26 4h-.31v2.59h.31a4.27 4.27 0 0 1 4.26 4v.62a4.28 4.28 0 0 1 -4.26 4h-.31v2.6h.31a4.28 4.28 0 0 1 4.26 4v.61a4.28 4.28 0 0 1 -4.26 4h-.31v2.59h.31a4.27 4.27 0 0 1 4.26 4v.23h205v-.23a4.27 4.27 0 0 1 4.26-4h.31v-3.2h-.31a4.28 4.28 0 0 1 -4.26-4v-.61a4.28 4.28 0 0 1 4.26-4h.31v-2.6h-.31a4.28 4.28 0 0 1 -4.26-4v-.62a4.27 4.27 0 0 1 4.26-4h.31v-2.38h-.31a4.28 4.28 0 0 1 -4.26-4v-.57a4.28 4.28 0 0 1 4.26-4h.31v-2.54h-.31a4.28 4.28 0 0 1 -4.26-4v-.56a4.28 4.28 0 0 1 4.26-4h.31v-2.6h-.31a4.28 4.28 0 0 1 -4.26-4v-.61a4.28 4.28 0 0 1 4.26-4h.31v-2.44h-.31a4.28 4.28 0 0 1 -4.26-4v-.62a4.28 4.28 0 0 1 4.26-3.91z">
		</path>
	</svg>
	<svg class="absolute w-full h-full" style="color: var(--sc-color1)" viewBox="0 0 230 100"
		xmlns="http://www.w3.org/2000/svg" fill="none" stroke="currentColor" preserveAspectRatio="none">
		<g stroke-miterlimit="10">
			<path
				d="m212 77.62a9.68 9.68 0 0 1 1.75-5.55 9.69 9.69 0 0 1 0-11.11 9.68 9.68 0 0 1 0-11.1 9.69 9.69 0 0 1 0-11.11 9.69 9.69 0 0 1 0-11.11 9.69 9.69 0 0 1 -.15-10.89h-197.25a9.72 9.72 0 0 1 -.14 10.89 9.74 9.74 0 0 1 0 11.11 9.74 9.74 0 0 1 0 11.11 9.72 9.72 0 0 1 0 11.1 9.74 9.74 0 0 1 0 11.11 9.71 9.71 0 0 1 0 11.11v.06h197.5v-.06a9.66 9.66 0 0 1 -1.71-5.56z"
				stroke-width="1"></path>
		</g>
	</svg>
	<div class="relative flex items-center my-5 mx-1/12 p-0.5 gap-2">
		<div class="flex-1 text-xs leading-tight -mt-0.5" style="color: var(--sc-color1)">
			<div class="text-sm font-bold uppercase"><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></div>
			<div><?php echo esc_html( $coupon_description ); ?></div>
		</div>
		<div class="leading-none text-center" style="color: var(--sc-color3)">
			<div class="inline-block">
				<div
					class="flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center">
					<span class="text-4xl font-bold"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : $woocommerce_smart_coupon->get_emoji() ); ?></span>
					<sup class="text-lg"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></sup>
				</div>
			</div>
		</div>
	</div>
</div>
