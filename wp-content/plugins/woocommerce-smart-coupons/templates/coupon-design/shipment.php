<?php
/**
 * Smart Coupons design - Shipment
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
<div class="transition duration-100 ease-in-out transform border-2 rounded-sm sc-coupon hover:scale-105 <?php echo esc_attr( $classes ); ?>"
	style="
			min-height: 6rem;
			background-color: var(--sc-color2);
			color: var(--sc-color1);
			border-color: var(--sc-color2);
		" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div>
		<div class="flex items-center m-1">
			<div class="flex-1 mx-1 space-y-1">
				<div class="text-lg font-bold">
					<div class="flex items-center leading-none" style="color: var(--sc-color3)">
						<div
							class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center mr-1">
							<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : '' ); ?></span>
							<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></span>
						</div>
						<span><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></span>
					</div>
				</div>
				<div class="text-xs leading-tight">
					<span><?php echo esc_html( $coupon_description ); ?>
					</span>
				</div>
			</div>
			<img src="<?php echo esc_url( $thumbnail_src ); ?>" class="object-cover rounded-md w-15 h-15" />
		</div>

		<div class="absolute bottom-0 flex items-center justify-between w-full py-0.5 px-1 text-xs rounded-sm" style="
				color: var(--sc-color2);
				background-color: var(--sc-color1);
				filter: saturate(0.75);
			">
			<div class="flex items-center">
				<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
						d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
					</path>
				</svg>
				<span class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></span>
			</div>
			<div class="flex items-center">
				<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
						d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				<span class="validity"><?php echo esc_html( $coupon_expiry ); ?></span>
			</div>
		</div>
	</div>
</div>
