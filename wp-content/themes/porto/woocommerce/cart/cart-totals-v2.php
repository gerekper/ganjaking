<?php
/**
 * Cart totals
 *
 * @version     2.3.6
 */

defined( 'ABSPATH' ) || exit;

$porto_woo_version = porto_get_woo_version_number();
?>

<div class="cart_totals<?php echo WC()->customer->has_calculated_shipping() ? ' calculated_shipping' : ''; ?>">
	<div class="cart_totals_toggle">
		<?php do_action( 'woocommerce_before_cart_totals' ); ?>

		<div class="card card-default">
			<div class="card-header arrow">
				<h2 class="card-title"><a class="accordion-toggle" data-toggle="collapse" href="#panel-cart-total"><?php esc_html_e( 'CART TOTALS', 'porto' ); ?></a></h2>
			</div>
			<div id="panel-cart-total" class="accordion-body collapse show"><div class="card-body">
				<table class="responsive cart-total" cellspacing="0">
					<tr class="cart-subtotal">
						<th><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></th>
						<td><?php wc_cart_totals_subtotal_html(); ?></td>
					</tr>

					<?php
					$codes = WC()->cart->get_coupons();
					?>
					<?php foreach ( $codes as $code => $coupon ) : ?>
						<tr class="cart-discount coupon-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
							<th><?php wc_cart_totals_coupon_label( $coupon ); ?></th>
							<td><?php wc_cart_totals_coupon_html( $coupon ); ?></td>
						</tr>
					<?php endforeach; ?>

					<?php foreach ( WC()->cart->get_fees() as $fee ) : ?>
						<tr class="fee">
							<th><?php echo esc_html( $fee->name ); ?></th>
							<td><?php wc_cart_totals_fee_html( $fee ); ?></td>
						</tr>
					<?php endforeach; ?>

					<?php
					if ( wc_tax_enabled() && ! WC()->cart->display_prices_including_tax() ) :
						$taxable_address = WC()->customer->get_taxable_address();
						$estimated_text  = '';

						if ( WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ) {
							/* translators: %s location. */
							$estimated_text = sprintf( ' <small>' . esc_html__( '(estimated for %s)', 'woocommerce' ) . '</small>', WC()->countries->estimated_for_prefix( $taxable_address[0] ) . WC()->countries->countries[ $taxable_address[0] ] );
						}
						if ( 'itemized' === get_option( 'woocommerce_tax_total_display' ) ) :
							?>
							<?php foreach ( WC()->cart->get_tax_totals() as $code => $tax ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.OverrideProhibited ?>
								<tr class="tax-rate tax-rate-<?php echo esc_attr( sanitize_title( $code ) ); ?>">
									<th><?php echo esc_html( $tax->label ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
									<td data-title="<?php echo esc_attr( $tax->label ); ?>"><?php echo wp_kses_post( $tax->formatted_amount ); ?></td>
								</tr>
							<?php endforeach; ?>
						<?php else : ?>
							<tr class="tax-total">
								<th><?php echo esc_html( WC()->countries->tax_or_vat() ) . $estimated_text; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?></th>
								<td data-title="<?php echo esc_attr( WC()->countries->tax_or_vat() ); ?>"><?php wc_cart_totals_taxes_total_html(); ?></td>
							</tr>
						<?php endif; ?>
					<?php endif; ?>

					<?php do_action( 'woocommerce_cart_totals_before_order_total' ); ?>
					<tr class="order-total">
						<th><?php esc_html_e( 'Grand Total', 'porto' ); ?></th>
						<td><?php wc_cart_totals_order_total_html(); ?></td>
					</tr>
					<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
				</table>
				<?php if ( version_compare( $porto_woo_version, '2.5', '<' ) && WC()->cart->get_cart_tax() ) : ?>
					<p class="wc-cart-shipping-notice"><small>
					<?php
						$cc = WC()->countries->countries[ WC()->countries->get_base_country() ];
						/* translators: %s: Country name */
						$estimated_text = WC()->customer->is_customer_outside_base() && ! WC()->customer->has_calculated_shipping() ? sprintf( ' ' . __( ' (taxes estimated for %s)', 'porto' ), WC()->countries->estimated_for_prefix() . $cc ) : '';
						/* translators: %s: Esitimated text */
						printf( __( 'Note: Shipping and taxes are estimated%s and will be updated during checkout based on your billing and shipping information.', 'porto' ), $estimated_text );
					?>
					</small></p>
				<?php endif; ?>
				<div class="wc-proceed-to-checkout">
					<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-primary btn-block"><?php esc_html_e( 'Proceed to checkout', 'porto' ); ?></a>
				</div>
			</div></div>
		</div>
		<?php do_action( 'woocommerce_after_cart_totals' ); ?>
	</div>
</div>
