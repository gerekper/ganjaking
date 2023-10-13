<?php
/**
 * Smart Coupons design - Promotion
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
<div class="transition duration-100 ease-in-out transform border-2 rounded-md hover:scale-105 sc-coupon <?php echo esc_attr( $classes ); ?>"
	style="
			min-height: 6rem;
			background-color: var(--sc-color3);
			color: var(--sc-color1);
			border-color: var(--sc-color1);
		" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div class="relative flex flex-col">
		<svg class="absolute w-24 ml-1 -mt-1" style="
				color: var(--sc-color1);
				filter: drop-shadow(0 0.1rem 0 rgb(80 80 80 / 0.4));
			" fill="currentColor" stroke="none" viewBox="0 0 92.7 88.81" xmlns="http://www.w3.org/2000/svg">
			<path
				d="m92.7 44.4c0 4.76-5.21 8.63-6.61 12.92s.45 10.68-2.24 14.33-9.21 3.84-12.94 6.56-5.79 8.84-10.24 10.29c-4.28 1.39-9.57-2.32-14.32-2.32s-10.04 3.71-14.35 2.32c-4.44-1.45-6.53-7.6-10.23-10.29s-10.2-2.82-12.92-6.56-.8-9.89-2.25-14.33-6.6-8.16-6.6-12.92 5.21-8.62 6.6-12.91c1.45-4.49-.44-10.63 2.25-14.33s9.2-3.84 12.94-6.56 5.79-8.84 10.21-10.29c4.29-1.39 9.58 2.32 14.33 2.32s10-3.71 14.32-2.32c4.45 1.45 6.54 7.6 10.24 10.29s10.22 2.82 12.94 6.56.8 9.88 2.24 14.33c1.42 4.29 6.63 8.16 6.63 12.91z">
			</path>
		</svg>
		<svg class="absolute w-full h-7" style="color: var(--sc-color2)" fill="currentColor" stroke="none"
			viewBox="0 0 230 28" xmlns="http://www.w3.org/2000/svg" preserveAspectRatio="none">
			<path
				d="m230 0v25.72a10.26 10.26 0 0 0 -2.27.87 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 13.64 13.64 0 0 1 -12.08 0 8.61 8.61 0 0 0 -8.24 0 12.17 12.17 0 0 1 -6 1.41 12.21 12.21 0 0 1 -6-1.41 8.61 8.61 0 0 0 -8.24 0 12.13 12.13 0 0 1 -6 1.41 12.25 12.25 0 0 1 -6-1.41 8.59 8.59 0 0 0 -8.23 0 12.21 12.21 0 0 1 -6 1.41 12.17 12.17 0 0 1 -6-1.41 8.06 8.06 0 0 0 -4.12-1.05 8.06 8.06 0 0 0 -4.12 1.05 12.08 12.08 0 0 1 -4.46 1.31v-27.9z">
			</path>
		</svg>
		<div class="z-10 relative flex items-center justify-between w-full pb-0.5 px-1 text-xs h-7"
			style="color: var(--sc-color1); filter: brightness(1.5)">
			<div class="inline-flex items-center">
				<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
						d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
					</path>
				</svg>
				<span class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></span>
			</div>
			<div class="inline-flex items-center">
				<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
						d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				<span class="validity"><?php echo esc_html( $coupon_expiry ); ?></span>
			</div>
		</div>

		<div class="z-10 flex items-start p-1 mt-0.5 gap-2">
			<div class="leading-none mt-0.5" style="
				color: var(--sc-color2);
				filter: drop-shadow(0 0.1rem 0 rgb(80 80 80 / 0.4));
				">
				<div class="w-24 text-center">
					<div class="inline-flex items-center">
						<span class="text-4xl font-semibold"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : $woocommerce_smart_coupon->get_emoji() ); ?></span>
						<sup class="text-lg"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></sup>
					</div>
				</div>
			</div>
			<div class="text-sm leading-tight" style="color: var(--sc-color2)">
				<?php echo esc_html( $coupon_description ); ?>
			</div>
		</div>
	</div>
</div>
