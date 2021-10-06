<?php
/**
 * Smart Coupons design - Special
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
<div class="h-24 transition duration-100 ease-in-out transform border border-dashed sc-coupon hover:border-solid <?php echo esc_attr( $classes ); ?>"
	style="
			color: var(--sc-color1);
			border-color: var(--sc-color1);
			background: var(--sc-color2);
		" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div>
		<div class="flex items-start h-24">
			<div class="w-16 h-full overflow-hidden" style="background-color: var(--sc-color1)">
				<img src="<?php echo esc_url( $thumbnail_src ); ?>" class="object-cover w-full h-full" />
			</div>

			<div class="flex-1 h-full p-2 space-y-0.5 border-l border-dashed hover:border-solid"
				style="border-color: var(--sc-color1)">
				<svg class="inline-block h-5" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 61.23 19.23">
					<defs>
						<style>
						.a {
							fill: var(--sc-color3);
						}

						.b {
							fill: none;
							stroke: var(--sc-color3);
							stroke-miterlimit: 10;
							stroke-width: 1.23px;
						}
						</style>
					</defs>
					<path class="a"
						d="M4.77 12.55l1.47-.88a2.63 2.63 0 0 0 2.4 1.52c.9 0 1.55-.34 1.55-1s-.64-1-1.8-1.29c-1.59-.46-3.24-.9-3.24-2.73 0-1.65 1.29-2.65 3.2-2.65a4 4 0 0 1 3.53 1.83l-1.36 1a2.64 2.64 0 0 0-2.18-1.23c-.83 0-1.28.36-1.28.89 0 .7.66 1 1.82 1.28 1.56.42 3.21.91 3.21 2.75 0 1.42-1.08 2.69-3.55 2.69a4 4 0 0 1-3.77-2.18zm8.78-6.86h3.23c1.89 0 3.62.5 3.62 2.84s-1.87 2.88-3.65 2.88h-1.26v3.16h-1.94zm3.27 4.21c1.21 0 1.67-.47 1.67-1.32s-.44-1.29-1.67-1.29h-1.33V9.9zm4.84-4.21h6.05v1.63h-4.09v1.91h3v1.55h-3V13h4.3v1.59h-6.26zm6.88 4.43c0-3.09 1.78-4.6 4-4.6a3.33 3.33 0 0 1 3.6 2.8l-1.85.54c-.29-1-.72-1.72-1.76-1.72-1.35 0-2.07 1.14-2.07 3s.75 3 2.09 3c1 0 1.56-.66 1.87-1.87l1.84.41a3.52 3.52 0 0 1-3.72 3c-2.29.05-4-1.43-4-4.56zm9.26-4.43h1.94v8.88H37.8zm6.3-.04H46l3.14 8.92h-2l-.66-2h-3l-.68 2H41zm1.9 5.52l-1-3-1 3zm4.22-5.48h2V13h3.91v1.59h-5.91z">
					</path>
					<path class="b" d="M0.61 0.61H60.61V18.61H0.61z"></path>
				</svg>

				<div class="text-base font-bold pt-0.5">
					<div class="flex items-center leading-none">
						<div
							class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center mr-1">
							<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : '' ); ?></span>
							<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></span>
						</div>
						<span><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></span>
					</div>
				</div>
				<div class="text-xs leading-tight">
					<?php echo esc_html( $coupon_description ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
