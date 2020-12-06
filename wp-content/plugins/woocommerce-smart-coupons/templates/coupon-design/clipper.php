<?php
/**
 * Smart Coupons design - Clipper
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
<div class="relative p-1 overflow-hidden transition duration-100 ease-in-out transform border-b border-transparent border-dashed hover:border-b hover:border-gray-700 sc-coupon <?php echo esc_attr( $classes ); ?>"
	style="color: var(--sc-color1)" data-coupon_code="<?php echo esc_attr( $coupon_code ); ?>">
	<div>
		<div class="flex items-center gap-2">
			<svg xmlns="http://www.w3.org/2000/svg" style="color: var(--sc-color3)" class="w-5 h-5 -mt-0.5"
				fill="currentColor" stroke="none" viewBox="0 0 24 24">
				<path
					d="M23.4 12.2c-.3 0-.6.3-.6.6v3.5c0 .3.3.6.6.6s.6-.3.6-.6v-3.5c0-.3-.3-.6-.6-.6zm-5.8-6.1h-3.8c-.3 0-.6.3-.6.6s.3.6.6.6h3.8c.3 0 .6-.3.6-.6s-.3-.6-.6-.6zm0 15.5h-3.8c-.3 0-.6.3-.6.6s.3.6.6.6h3.8c.3 0 .6-.3.6-.6 0-.4-.3-.6-.6-.6zm-6.3 0H7.5c-.3 0-.6.3-.6.6s.3.6.6.6h3.8c.3 0 .6-.3.6-.6-.1-.4-.3-.6-.6-.6zM23.4 6.1h-3c-.3 0-.6.3-.6.6s.3.6.6.6h2.5V10c0 .3.3.6.6.6s.6-.3.6-.6V6.7c-.1-.3-.4-.6-.7-.6zm0 12.5c-.3 0-.6.3-.6.6v2.5h-2.7c-.3 0-.6.3-.6.6s.3.6.6.6h3.2c.3 0 .6-.3.6-.6v-3c.1-.5-.2-.7-.5-.7zm-18.5 3H2.2v-2.5c0-.3-.3-.6-.6-.6s-.6.3-.6.6v3c0 .3.3.6.6.6h3.2c.3 0 .6-.3.6-.6.1-.3-.2-.5-.5-.5zM4 10.1c0-.7-.4-1.3-.9-1.6l2.1-1.2 4.3 2.3c.1.1.2.1.3.1.2 0 .4-.1.5-.3.2-.3.1-.6-.2-.8l-3.7-2 3.7-2c.3-.2.4-.5.2-.8-.2-.3-.5-.4-.8-.2L5.2 6 3.1 4.9c.6-.4.9-1 .9-1.7 0-1.1-.9-2-2-2s-2 .9-2 2C0 4 .5 4.6 1.1 5l.1.1 2.9 1.6-3 1.6-.1.1c-.6.4-1 1-1 1.7 0 1.1.9 2 2 2s2-.9 2-2zM2 2.4c.5 0 .9.4.9.9s-.4.8-.9.8-.9-.4-.9-.9.4-.8.9-.8zm-.9 7.7c0-.5.4-.9.9-.9s.9.4.9.9-.4.9-.9.9-.9-.4-.9-.9z">
				</path>
				<path
					d="M11.5 6.1H9.6v1.1h1.9c.3 0 .5-.2.5-.5s-.2-.6-.5-.6zM1.7 16.8c.3 0 .6-.3.6-.6v-2.9H1.1v2.9c0 .3.3.6.6.6z">
				</path>
			</svg>

			<div class="flex-1 space-y-1 leading-tight">
				<div class="inline-flex items-center gap-1 text-sm font-bold leading-none uppercase"
					style="color: var(--sc-color3)">
					<div
						class="inline-flex <?php echo esc_attr( ( true === $is_percent ) ? '' : 'flex-row-reverse' ); ?> items-center">
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $coupon_amount : '' ); ?></span>
						<span><?php echo esc_html( ( ! empty( $coupon_amount ) ) ? $amount_symbol : '' ); ?></span>
					</div>
					<span class="discount-label"><?php echo wp_kses_post( ( ! empty( $coupon_amount ) ) ? $discount_type : __( 'Coupon', 'woocommerce-smart-coupons' ) ); ?></span>
				</div>
				<div class="inline-flex items-center gap-2 text-xs leading-none text-gray-600">
					<span
						class="font-mono uppercase"><?php echo esc_html( $coupon_code ); ?></span><span><?php echo esc_html( $coupon_expiry ); ?></span>
				</div>
			</div>
		</div>
	</div>
</div>
