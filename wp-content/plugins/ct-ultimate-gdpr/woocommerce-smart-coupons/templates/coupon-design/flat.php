<?php
/**
 * Smart Coupons design - Flat
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
<div class="transition duration-100 ease-in-out transform border-2 rounded-md hover:-translate-y-1 hover:shadow sc-coupon <?php echo esc_attr( $classes ); ?>"
	style="
			min-height: 6rem;
			background-color: var(--sc-color2);
			color: var(--sc-color1);
			border-color: var(--sc-color2);
		" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div>
		<div class="flex items-center gap-3 p-2">
			<div class="leading-none">
				<div class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center"
					style="color: var(--sc-color3)">
					<span class="text-4xl font-semibold"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : $woocommerce_smart_coupon->get_emoji() ); ?></span>
					<sup class="text-lg"><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></sup>
				</div>
				<div class="text-xs uppercase"><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : '' ); ?></div>
			</div>
			<div class="text-sm leading-tight">
				<?php echo esc_html( $coupon_description ); ?>
			</div>
		</div>
		<div class="gap-2 border-t border-dotted flex items-center justify-between w-full pb-0.5 pt-1 px-1 text-xs"
			style="
				color: var(--sc-color1);
				filter: saturate(0.75);
				border-color: var(--sc-color3);
			">
			<div class="inline-flex items-center">
				<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1"
					style="color: var(--sc-color3)">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
						d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z">
					</path>
				</svg>
				<span class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></span>
			</div>
			<div class="inline-flex items-center">
				<svg fill="none" viewBox="0 0 24 24" stroke="currentColor" class="w-5 h-5 mr-1"
					style="color: var(--sc-color3)">
					<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1"
						d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
				</svg>
				<span class="validity"><?php echo esc_html( $coupon_expiry ); ?></span>
			</div>
		</div>
	</div>
</div>
