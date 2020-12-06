<?php
/**
 * Smart Coupons design - Cutout
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
<div class="transition duration-100 ease-in-out transform sc-coupon hover:-translate-y-1 <?php echo esc_attr( $classes ); ?>"
	style="color: var(--sc-color1)" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<svg fill="none" viewBox="0 0 24 24" stroke="currentColor"
		class="absolute right-0 z-10 w-6 h-6 transform -rotate-90 top-2 rounded-full p-0.5"
		style="color: var(--sc-color3); background-color: var(--sc-color2)">
		<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
			d="M14.121 14.121L19 19m-7-7l7-7m-7 7l-2.879 2.879M12 12L9.121 9.121m0 5.758a3 3 0 10-4.243 4.243 3 3 0 004.243-4.243zm0-5.758a3 3 0 10-4.243-4.243 3 3 0 004.243 4.243z">
		</path>
	</svg>

	<div class="relative p-2 mr-3 overflow-hidden border-2 border-dashed hover:border-solid" style="
			background-color: var(--sc-color2);
			border-color: var(--sc-color1);
			">
		<div class="space-y-1">
			<div class="flex items-center justify-between mr-2 text-xs" style="opacity: 0.85">
				<div class="flex items-center">
					<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
							d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
						</path>
					</svg>
					<span class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></span>
				</div>
				<div class="flex items-center">
					<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 ml-3 mr-1">
						<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
							d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
					</svg>
					<span class="validity"><?php echo esc_html( $coupon_expiry ); ?></span>
				</div>
			</div>
			<div class="text-lg font-bold" style="color: var(--sc-color3)">
				<div class="flex items-center leading-none">
					<div
						class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center mr-1">
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : '' ); ?></span>
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></span>
					</div>
					<span><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></span>
				</div>
			</div>
			<div class="flex items-start gap-2">
				<div class="flex-1 text-xs leading-tight">
					<span><?php echo esc_html( $coupon_description ); ?>
					</span>
				</div>
				<img src="<?php echo esc_url( $thumbnail_src ); ?>" class="mt-0.5 object-cover rounded-sm w-6 h-6" />
			</div>
		</div>
	</div>
</div>
