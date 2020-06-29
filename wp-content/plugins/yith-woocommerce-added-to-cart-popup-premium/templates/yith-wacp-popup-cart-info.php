<?php
/**
 * Popup cart info template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
}

?>

<div class="cart-info">
	<?php if ( $cart_shipping && isset( $cart_info['shipping'] ) ) : ?>
		<div class="cart-shipping">
			<?php esc_html_e( 'Shipping Cost', 'yith-woocommerce-added-to-cart-popup' ); ?>:
			<span class="shipping-cost">
				<?php echo $cart_info['shipping']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>
	<?php endif; ?>

	<?php if ( $cart_tax && isset( $cart_info['tax'] ) ) : ?>
		<div class="cart-tax">
			<?php esc_html_e( 'Tax Amount', 'yith-woocommerce-added-to-cart-popup' ); ?>:
			<span class="tax-cost">
				<?php echo $cart_info['tax']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>
	<?php endif; ?>

	<?php if ( $cart_total && isset( $cart_info['total'] ) ) : ?>
		<?php if ( ! empty( $cart_info['discount'] ) ) : ?>
			<div class="cart-discount">
				<?php esc_html_e( 'Discount', 'yith-woocommerce-added-to-cart-popup' ); ?>:
				<span class="discount-cost">
					<?php echo $cart_info['discount']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</span>
			</div>
		<?php endif; ?>
		<div class="cart-totals">
			<?php echo esc_html( apply_filters( 'yith_wacp_cart_total_label', __( 'Cart Total', 'yith-woocommerce-added-to-cart-popup' ) ) ); ?>:
			<span class="cart-cost">
				<?php echo $cart_info['total']; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
			</span>
		</div>
	<?php endif; ?>
</div>
