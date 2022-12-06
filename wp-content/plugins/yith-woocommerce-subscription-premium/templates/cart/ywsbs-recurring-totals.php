<?php
/**
 * Recurring totals template of YITH WooCommerce Subscription
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @version 2.0.0
 * @author  YITH
 *
 * @var array $recurring_totals Recurring total List.
 */

use function WPML\FP\apply;

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly.
}

?>

<tr class="ywsbs-recurring-totals-items">
	<th><?php esc_html_e( 'Recurring totals', 'yith-woocommerce-subscription' ); ?></th>
	<td>
		<?php
		foreach ( WC()->cart->get_cart_contents() as $cart_item_key => $cart_item ) :

			if ( isset( $cart_item['ywsbs-subscription-info'] ) ) :

				$recurring_period        = YWSBS_Subscription_Helper::get_subscription_period_for_price( $cart_item['data'], $cart_item['ywsbs-subscription-info'] );
				$recurring_price         = YWSBS_Subscription_Helper::get_subscription_recurring_price( $cart_item['data'], $cart_item['ywsbs-subscription-info'] );
				$recurring_price_display = wc_get_price_to_display(
					$cart_item['data'],
					array(
						'qty'   => $cart_item['quantity'],
						'price' => $recurring_price,
					)
				);
				$recurring_tax           = '';

				$rates = WC_Tax::get_rates( $cart_item['data']->get_tax_class(), WC()->cart->get_customer() );
				if( wc_tax_enabled() && ! empty( $rates )  ) {
					if ( 'incl' === get_option( 'woocommerce_tax_display_shop' ) ) {
						$recurring_tax = ' <small class="tax_label">' . WC()->countries->inc_tax_or_vat() . '</small>';
					} else {
						$recurring_tax = ' <small class="tax_label">' . WC()->countries->ex_tax_or_vat() . '</small>';
					}
				}

				$price_html  = '<div class="ywsbs-price">';
				$price_html .= wc_price( $recurring_price_display ) . ' / ' . $recurring_period . ' ' . $recurring_tax;
				$price_html  = apply_filters( 'ywsbs_recurring_price_html', $price_html, $recurring_price, $recurring_period, $cart_item );
				remove_filter( 'woocommerce_product_needs_shipping', array( YWSBS_Subscription_Cart(), 'maybe_not_shippable' ), 100 );
				if ( apply_filters( 'ywsbs_show_ex_shipping', true ) && $cart_item['data']->needs_shipping() ) {
					$price_html .= '<br><small class="tax_label">' . __( '(ex. shipping)', 'yith-woocommerce-subscription' ) . '</small>';
				}
				add_filter( 'woocommerce_product_needs_shipping', array( YWSBS_Subscription_Cart(), 'maybe_not_shippable' ), 100, 2 );

				$price_html .= '</div>';
				$price_html .= '<div class="recurring-price-info">';
				$price_html .= YWSBS_Subscription_Cart()->get_formatted_subscription_total_amount( $cart_item['data'], $cart_item['quantity'], $cart_item['ywsbs-subscription-info'] );
				$price_html .= YWSBS_Subscription_Cart()->get_formatted_subscription_next_billing_date( $cart_item['data'], $cart_item['ywsbs-subscription-info'] );
				$price_html .= '</div>';


				?>
				<div class="recurring-amount"><?php echo wp_kses_post( $price_html ); ?></div>
				<?php
			endif;
		endforeach;
		?>
	</td>
</tr>

