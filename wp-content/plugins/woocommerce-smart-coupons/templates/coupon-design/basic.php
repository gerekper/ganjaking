<?php
/**
 * Smart Coupons design - Basic
 *
 * @author      StoreApps
 * @package     WooCommerce Smart Coupons/Templates
 *
 * @version     1.1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>
<div class="relative p-2 overflow-hidden transition duration-100 ease-in-out transform border rounded-md hover:scale-105 sc-coupon <?php echo esc_attr( $classes ); ?>"
	style="
			color: var(--sc-color1);
			background-color: var(--sc-color2);
			border-color: var(--sc-color3);
		" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div>
		<div class="flex items-center">
			<svg xmlns="http://www.w3.org/2000/svg" style="color: var(--sc-color1)" fill="none" viewBox="0 0 24 24"
				stroke="currentColor" class="w-6 h-6">
				<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
					d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
				</path>
			</svg>

			<div class="mx-2 space-y-1 leading-tight">
				<div class="inline-flex items-center space-x-1 text-sm font-bold leading-none uppercase"
					style="color: var(--sc-color3)">
					<div
						class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center">
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : '' ); ?></span>
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></span>
					</div>
					<span class="discount-label"><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></span>
				</div>
				<div class="space-y-1 text-xs leading-none" style="filter: saturate(0.5)">
					<div class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></div>
					<div><?php echo esc_html( $coupon_expiry ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
