<?php
/**
 * Smart Coupons design - Deal
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
<div class="max-w-sm relative p-2 pl-2.5 overflow-hidden transition duration-100 ease-in-out transform rounded-md hover:-translate-y-1 hover:shadow sc-coupon <?php echo esc_attr( $classes ); ?>"
	style="color: var(--sc-color1); background-color: var(--sc-color2)"
	data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div class="space-y-1 leading-tight">
		<div class="float-right w-8 h-8 mt-1 mb-2 ml-2 overflow-hidden rounded-md">
			<img src="<?php echo esc_url( $thumbnail_src ); ?>" class="object-cover w-full h-full" />
		</div>
		<div class="flex items-center gap-1 text-sm font-bold leading-none uppercase" style="color: var(--sc-color3)">
			<div
				class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center">
				<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : '' ); ?></span>
				<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></span>
			</div>
			<span class="discount-label"><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></span>
		</div>
		<div class="flex items-center gap-2 text-xs leading-none"
			style="filter: saturate(0.5); color: var(--sc-color3)">
			<span
				class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></span><span><?php echo esc_html( $coupon_expiry ); ?></span>
		</div>
		<div class="flex items-start gap-2">
			<div class="flex-1 space-y-1">
				<div class="text-sm leading-tight">
					<?php echo esc_html( $coupon_description ); ?>
				</div>
			</div>
		</div>
	</div>
</div>
