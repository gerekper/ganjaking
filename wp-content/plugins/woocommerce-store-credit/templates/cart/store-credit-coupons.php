<?php
/**
 * Store credit coupons.
 *
 * @package WC_Store_Credit/Templates
 * @version 4.2.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var array $coupons An array with the available coupons.
 */
?>
<div class="wc-store-credit-cart-coupons-container">
	<?php if ( ! empty( $coupons ) ) : ?>
		<div class="wc-store-credit-cart-coupons-title woocommerce-info">
			<?php echo wp_kses_post( wc_store_credit_get_cart_title() ); ?>
		</div>

		<div class="wc-store-credit-cart-coupons" style="display:none">
			<?php
			/**
			 * Hook: wc_store_credit_cart_coupons_before.
			 *
			 * @since 4.2.0
			 */
			do_action( 'wc_store_credit_cart_coupons_before' );

			array_map( 'wc_store_credit_cart_coupon', $coupons );

			/**
			 * Hook: wc_store_credit_cart_coupons_after.
			 *
			 * @since 4.2.0
			 */
			do_action( 'wc_store_credit_cart_coupons_after' );
			?>
		</div>
	<?php endif; ?>
</div>
