<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * HTML Template for Subscription Detail
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$current_order = $subscription->get_order();
$item          = $current_order->get_item( $subscription->get( 'order_item_id' ) );

if ( $item ) :
	?>
<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
	<tr>
		<th scope="col"
			style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-subscription' ); ?></th>
		<th scope="col"
			style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Subtotal', 'yith-woocommerce-subscription' ); ?></th>
	</tr>
	</thead>
	<tbody>
	<tr>
		<td scope="col" style="text-align:left;">
			<a href="<?php echo esc_url( get_permalink( $subscription->get_product_id() ) ); ?>"><?php echo wp_kses_post( $subscription->get_product_name() ); ?></a><?php echo ' x ' . esc_html( $subscription->get_quantity() ); ?>
			<?php
			$text_align  = is_rtl() ? 'right' : 'left';
			$margin_side = is_rtl() ? 'left' : 'right';


			wc_display_item_meta(
				$item,
				array(
					'label_before' => '<strong class="wc-item-meta-label" style="float: ' . esc_attr( $text_align ) . '; margin-' . esc_attr( $margin_side ) . ': .25em; clear: both">',
				)
			);
			?>
		</td>

		<td scope="col"
			style="text-align:left;"><?php echo wp_kses_post( wc_price( $subscription->get_line_total(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
	</tr>

	</tbody>
	<tfoot>
	<?php if ( $subscription->get_line_tax() !== 0 ) : ?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Item Tax:', 'yith-woocommerce-subscription' ); ?></th>
			<td><?php echo wp_kses_post( wc_price( $subscription->get_line_tax(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
		</tr>
	<?php endif ?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Subtotal:', 'yith-woocommerce-subscription' ); ?></th>
		<td><?php echo wp_kses_post( wc_price( $subscription->get_line_total() + $subscription->get_line_tax(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
	</tr>

	<?php
	$subscriptions_shippings = $subscription->get_subscriptions_shippings();
	if ( $subscriptions_shippings ) :
		?>
		<tr>
			<th scope="row"><?php esc_html_e( 'Shipping:', 'yith-woocommerce-subscription' ); ?></th>
			<td>
			<?php
				// translators: placeholder: 2 and 3 html tags, 1.shipping name.
				echo wp_kses_post( wc_price( $subscriptions_shippings['cost'], array( 'currency' => $subscription->get_order_currency() ) ) . sprintf( _x( '%2$s via %1$s%3$s', 'placeholder: 2 and 3 html tags, 1.shipping name', 'yith-woocommerce-subscription' ), $subscriptions_shippings['name'], '<small>', '</small>' ) );
			?>
				</td>
		</tr>
		<?php
		if ( ! empty( $subscription->get_order_shipping_tax() ) ) :
			?>
			<tr>
				<th scope="row"><?php esc_html_e( 'Shipping Tax:', 'yith-woocommerce-subscription' ); ?></th>
				<td colspan="2"><?php echo wp_kses_post( wc_price( $subscription->get_order_shipping_tax(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
			</tr>
			<?php
		endif;
	endif;
	?>
	<tr>
		<th scope="row"><?php esc_html_e( 'Total:', 'yith-woocommerce-subscription' ); ?></th>
		<td colspan="2"><?php echo wp_kses_post( wc_price( $subscription->get_subscription_total(), array( 'currency' => $subscription->get_order_currency() ) ) ); ?></td>
	</tr>
	</tfoot>
</table>

<?php endif; ?>
