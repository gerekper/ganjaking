<?php
/**
 * Order email addresses to vendor.
 *
 * @version 2.1.52
 * @since 2.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$billing = $order->get_formatted_billing_address();

?><table id="addresses" cellspacing="0" cellpadding="0" style="width: 100%; vertical-align: top;" border="0">
	<tr>
		<?php
		/**
		 * Determine if we should show billing information.
		 *
		 * @since 2.1.52
		 * @param boolean  $show_billing Whether to show billing information. Default true.
		 * @param WC_Order $order        Order object.
		 */
		if ( $billing && apply_filters( 'wcpv_email_to_vendor_show_billing', true, $order ) ) :
			?>
			<td class="td" style="text-align:left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" valign="top" width="50%">
				<h3><?php esc_html_e( 'Billing Address', 'woocommerce-product-vendors' ); ?></h3>

				<address class="text">
					<?php
					echo wp_kses_post( $billing );
					?>
				</address>
			</td>
		<?php endif; ?>

		<?php if ( ! wc_ship_to_billing_address_only() && $order->needs_shipping_address() && ( $shipping = $order->get_formatted_shipping_address() ) ) : ?>
			<td class="td" style="text-align:left; font-family: 'Helvetica Neue', Helvetica, Roboto, Arial, sans-serif;" valign="top" width="50%">
				<h3><?php esc_html_e( 'Shipping Address', 'woocommerce-product-vendors' ); ?></h3>

				<p class="text"><?php echo esc_html( $shipping ); ?></p>
			</td>
		<?php endif; ?>
	</tr>
</table>
