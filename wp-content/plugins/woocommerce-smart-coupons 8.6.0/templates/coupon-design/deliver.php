<?php
/**
 * Smart Coupons design - Deliver
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
<div class="p-2 overflow-hidden transition duration-100 ease-in-out transform border border-transparent rounded-md hover:border-gray-600 hover:border-dashed sc-coupon <?php echo esc_attr( $classes ); ?>"
	style="color: var(--sc-color1); background-color: var(--sc-color2)"
	data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div class="-mt-1">
		<div class="flex items-center gap-2">
			<div class="flex-shrink-0 mt-1 overflow-hidden rounded-md w-9 h-9">
				<img src="<?php echo esc_url( $thumbnail_src ); ?>" class="object-cover w-full h-full"
					style="filter: saturate(0)" />
			</div>

			<div class="flex-1 space-y-1">
				<div class="inline-flex items-center gap-1 text-sm font-bold leading-4 uppercase"
					style="color: var(--sc-color3)">
					<div
						class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center">
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : '' ); ?></span>
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></span>
					</div>
					<span class="discount-label"><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></span>
				</div>
				<div class="text-xs leading-none">
					<div class="inline-flex items-center whitespace-no-wrap">
						<span class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></span>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>
