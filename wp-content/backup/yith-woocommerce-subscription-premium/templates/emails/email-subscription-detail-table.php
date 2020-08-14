<?php
/**
 * HTML Template for Subscription Detail
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 */

// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-subscription' ); ?></th>
</tr>
</thead>
<tbody>
<tr>
	<td scope="col" style="text-align:left;">
		<a href="<?php echo esc_url( get_permalink( $subscription->product_id ) ); ?>"><?php echo wp_kses_post( $subscription->product_name ); ?></a><?php echo ' x ' . esc_html( $subscription->quantity ); ?>
		<?php
				$text_align  = is_rtl() ? 'right' : 'left';
				$margin_side = is_rtl() ? 'left' : 'right';
				$order       = wc_get_order( $subscription->order_id );
				$item        = $order->get_item( $subscription->order_item_id );

				wc_display_item_meta(
					$item,
					array(
						'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
					)
				);
				?>
	</td>

	<td scope="col" style="text-align:left;"><?php echo wp_kses_post( wc_price( $subscription->line_total, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
</tr>

</tbody>
<tfoot>
<?php if ( $subscription->line_tax != 0 ) : ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Item Tax:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php echo wp_kses_post( wc_price( $subscription->line_tax, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
	</tr>
<?php endif ?>
<tr>
	<th scope="row"><?php esc_html_e( 'Subtotal:', 'yith-woocommerce-subscription' ); ?></th>
	<td><?php echo wp_kses_post( wc_price( $subscription->line_total + $subscription->line_tax, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
</tr>

<?php
if ( ! empty( $subscription->subscriptions_shippings ) ) :
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Shipping:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php echo wp_kses_post( wc_price( $subscription->subscriptions_shippings['cost'], array( 'currency' => $subscription->order_currency ) ) . sprintf( __( '<small> via %s</small>', 'yith-woocommerce-subscription' ), $subscription->subscriptions_shippings['name'] ) ); ?></td>
	</tr>
	<?php
	if ( ! empty( $subscription->order_shipping_tax ) ) :
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Shipping Tax:', 'yith-woocommerce-subscription' ); ?></th>
			<td colspan="2"><?php echo wp_kses_post( wc_price( $subscription->order_shipping_tax, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
		</tr>
		<?php
	endif;
endif;
?>
<tr>
	<th scope="row"><?php esc_html_e( 'Total:', 'yith-woocommerce-subscription' ); ?></th>
	<td colspan="2"><?php echo wp_kses_post( wc_price( $subscription->subscription_total, array( 'currency' => $subscription->order_currency ) ) ); ?></td>
</tr>
</tfoot>
</table>
