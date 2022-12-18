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
		<div class="card card-default">
			<h4 class="card-sub-title text-md text-uppercase m-b-md pb-1"><?php esc_html_e( 'Cart totals', 'woocommerce' ); ?></h4>
			<div id="panel-cart-total">
				<div class="card-body p-0">
					<table class="responsive cart-total" cellspacing="0">
						<tr class="cart-subtotal">
							<th><h4 class="mb-0"><?php esc_html_e( 'Subtotal', 'woocommerce' ); ?></h4></th>
							<td><?php wc_cart_totals_subtotal_html(); ?></td>
						</tr>

						<tr class="<?php echo 0 === count( WC()->shipping()->get_packages() ) ? 'border-bottom-0' : ''; ?>">
							<th colspan="2"><?php do_action( 'woocommerce_before_cart_totals' ); ?></th>
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
							<th><h4 class="text-md mb-0"><?php esc_html_e( 'Total', 'woocommerce' ); ?></h4></th>
							<td><?php wc_cart_totals_order_total_html(); ?></td>
						</tr>
						<?php do_action( 'woocommerce_cart_totals_after_order_total' ); ?>
					</table>

					<div class="wc-proceed-to-checkout">
						<a href="<?php echo esc_url( wc_get_checkout_url() ); ?>" class="btn btn-v-dark w-100 m-t-lg py-3"><?php esc_html_e( 'Proceed to checkout', 'woocommerce' ); ?><i class="vc_btn3-icon fas fa-arrow-right ps-3"></i></a>
					</div>
				</div>
			</div>
		</div>
		<?php do_action( 'woocommerce_after_cart_totals' ); ?>
	</div>
</div>
