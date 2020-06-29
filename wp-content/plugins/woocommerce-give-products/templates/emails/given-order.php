<?php
/**
 * Given order email
 *
 * @author        WooCommerce
 * @version       1.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$billing_email = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_email : $order->get_billing_email();
$billing_phone = version_compare( WC_VERSION, '3.0', '<' ) ? $order->billing_phone : $order->get_billing_phone();

?>

<?php do_action( 'woocommerce_email_header', $email_heading ); ?>

<p><?php _e( "You've been gifted this order:", 'woocommerce-give-products' ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, $sent_to_admin, $plain_text ); ?>

<h2><?php echo __( 'Order:', 'woocommerce-give-products' ) . ' ' . $order->get_order_number(); ?></h2>

<table cellspacing="0" cellpadding="6" style="width: 100%; border: 1px solid #eee;" border="1" bordercolor="#eee">
	<thead>
		<tr>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Product', 'woocommerce-give-products' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Quantity', 'woocommerce-give-products' ); ?></th>
			<th scope="col" style="text-align:left; border: 1px solid #eee;"><?php _e( 'Price', 'woocommerce-give-products' ); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php
		$args = array(
			'show_sku' => true,
		);
		if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
			echo $order->email_order_items_table( $args );
		} else {
			echo wc_get_email_order_items( $order, $args );
		}

		?>
	</tbody>
	<tfoot>
		<?php
		$totals = $order->get_order_item_totals();
		if ( $totals ) {
			$i = 0;
			foreach ( $totals as $total ) {
				$i++;
				?><tr>
				<th scope="row" colspan="2" style="text-align:left; border: 1px solid #eee; <?php
				if ( 1 == $i ) {
					echo 'border-top-width: 4px;';
				} ?>"><?php echo $total['label']; ?></th>
				<td style="text-align:left; border: 1px solid #eee; <?php
				if ( 1 == $i ) {
					echo 'border-top-width: 4px;';
				} ?>"><?php echo $total['value']; ?></td>
				</tr><?php
			}
		}
		?>
	</tfoot>
</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, $sent_to_admin, $plain_text ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, $sent_to_admin, $plain_text ); ?>

<h2><?php _e( 'Customer details', 'woocommerce-give-products' ); ?></h2>

<?php if ( $billing_email ) : ?>
	<p><strong><?php _e( 'Email:', 'woocommerce-give-products' ); ?></strong> <?php echo $billing_email; ?></p>
<?php endif; ?>
<?php if ( $billing_phone ) : ?>
	<p><strong><?php _e( 'Tel:', 'woocommerce-give-products' ); ?></strong> <?php echo $billing_phone; ?></p>
<?php endif; ?>

<?php do_action( 'woocommerce_email_footer' ); ?>
