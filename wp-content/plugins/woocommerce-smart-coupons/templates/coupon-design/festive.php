<?php
/**
 * Smart Coupons design - Festive
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
<div class="transition duration-100 ease-in-out transform rounded-md sc-coupon hover:-translate-y-1 hover:scale-105 <?php echo esc_attr( $classes ); ?>"
	style="
			min-height: 6rem;
			color: var(--sc-color1);
			border-color: var(--sc-color1);
			background: var(--sc-color2);
		" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div class="absolute h-full" style="width: 90px; background-color: var(--sc-color1)"></div>
	<svg class="absolute w-full h-full" viewBox="0 0 230 100" preserveAspectRatio="none"
		xmlns="http://www.w3.org/2000/svg">
		<path d="m230 78.63c-12.15 1.83-19.3 9.74-37.59 14.73a41.13 41.13 0 0 0 -13.77 6.64h51.36z" fill="#4896d1">
		</path>
		<path d="m80.22 7.26c1.83 1.33 4.38.64 6 .2 11.75-3 16.35-2.54
			18.16-6.35a5.6 5.6 0 0 0 .42-1.11h-24.8a3.14 3.14 0 0 0 -1.42 1.16 4.3
			4.3 0 0 0 0 3.77 5.1 5.1 0 0 0 1.64 2.33z" fill="#ec1c58"></path>
		<path d="m226.76 10a21 21 0 0 0 3.24-2v-8h-36.46c8.46 8.77 22.54 15.44 33.22 10z" fill="#fcbf12"></path>
		<path d="m0 26.52c4.69-6.22 8.58-16.52 5.2-21.38-1.47-2.08-3.2-2.44-5.2-2.54z" fill="#1abab3"></path>
	</svg>
	<div class="absolute bottom-0 -mb-1.5 transform h-8 w-8 left-14 rounded-sm overflow-hidden"
		style="--transform-rotate: -15deg">
		<img src="<?php echo esc_url( $thumbnail_src ); ?>" class="object-cover w-full h-full" />
		<svg class="hidden w-full h-full" viewBox="0 0 48 48" xmlns="http://www.w3.org/2000/svg" fill="none"
			stroke="currentColor" style="color: var(--sc-color3)">
			<g stroke-linecap="round" stroke-linejoin="round" stroke-width="2">
				<path d="m37.36 22h7.64v25h-42v-25h7.64"></path>
				<path d="m29.76 22h-11.52"></path>
				<path d="m21 22h6v25h-6z"></path>
				<path d="m10.66 22h-.02-7.64-2v-8h12.02"></path>
				<path
					d="m20.13 15h-1.19a15.36 15.36 0 0 1 -1.94 0 9.41 9.41 0 0 1 -7.16-3.54 3.91 3.91 0 0 1 -.79-2.74c.23-2.49 1.14-7.72 4.95-7.72 4.6 0 9.2 5.07 9.9 9">
				</path>
				<path
					d="m14.48 14.56a21.28 21.28 0 0 0 -3.82 7.44 43.07 43.07 0 0 0 -1.66 8l4-3 3 5a30.77 30.77 0 0 1 5.05-15.3">
				</path>
				<path d="m34.98 14h12.02v8h-2-7.64-.02"></path>
				<path
					d="m24.1 10c.7-3.93 5.3-9 9.9-9 3.81 0 4.72 5.23 4.93 7.72a3.91 3.91 0 0 1 -.79 2.74 9.41 9.41 0 0 1 -7.14 3.54 15.36 15.36 0 0 1 -1.92 0h-1.21">
				</path>
				<path
					d="m27 16.7a30.77 30.77 0 0 1 5 15.3l3-5 4 3a43.07 43.07 0 0 0 -1.66-8 21.28 21.28 0 0 0 -3.82-7.44">
				</path>
				<circle cx="24" cy="14" r="4"></circle>
				<path d="m13 7c2.38-3.18 6.66 1.85 8.31 4.05"></path>
				<path d="m26.69 11.05c1.65-2.2 5.93-7.23 8.31-4.05"></path>
			</g>
		</svg>
	</div>

	<div class="relative w-full h-full">
		<div class="flex items-start h-full pt-4">
			<div class="h-full leading-none" style="width: 90px; color: var(--sc-color2)">
				<div class="pt-1 pl-2">
					<div class="inline-flex items-center">
						<span class="text-4xl font-semibold"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : $woocommerce_smart_coupon->get_emoji() ); ?></span>
						<sup class="text-lg"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></sup>
					</div>
					<div class="text-xs uppercase"><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : '' ); ?></div>
				</div>
			</div>
			<div class="flex flex-col justify-between flex-1 px-2 text-xs leading-tight">
				<div class="description mb-1.5">
					<?php echo esc_html( $coupon_description ); ?>
				</div>
				<div class="text-xs leading-tight" style="filter: opacity(0.8)">
					<div class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></div>
					<div class="validity"><?php echo esc_html( $coupon_expiry ); ?></div>
				</div>
			</div>
		</div>
	</div>
</div>
