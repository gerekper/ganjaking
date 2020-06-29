<?php
/**
 * Admin new order email
 *
 * @author YITH
 *
 * @var string   $email_heading
 * @var WC_Email $email
 * @var WC_Order $order
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

do_action( 'woocommerce_email_header', $email_heading, $email );
$order_id           = $order->get_id();
$billing_first_name = $order->get_billing_first_name();
$billing_last_name  = $order->get_billing_last_name();
$order_date         = strtotime( $order->get_date_created() );
?>
	<h3><?php esc_html_e( 'Order Recovered', 'yith-woocommerce-recover-abandoned-cart' ); ?></h3>
	<p><?php printf( esc_html( __( 'You have received an order from %s. The order is as follows:', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $billing_first_name . ' ' . $billing_last_name ) ); ?></p>

<?php do_action( 'woocommerce_email_before_order_table', $order, true, false ); ?>

	<h2>
		<a href="<?php echo esc_url( admin_url( 'post.php?post=' . $order_id . '&action=edit' ) ); ?>"><?php printf( esc_html( __( 'Order #%s', 'yith-woocommerce-recover-abandoned-cart' ) ), esc_html( $order->get_order_number() ) ); ?></a>
		(<?php printf( wp_kses_post( '<time datetime="%s">%s</time>' ), esc_html( date_i18n( 'c', $order_date ) ), esc_html( date_i18n( wc_date_format(), $order_date ) ) ); ?>
		)</h2>

	<table cellspacing="0" cellpadding="6" style="width: 80%; border: 1px solid #eee;" border="1" bordercolor="#eee">
		<thead>
		<tr>
			<th scope="col"
				style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Product', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<th scope="col"
				style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Quantity', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
			<th scope="col"
				style="text-align:left; border: 1px solid #eee;"><?php esc_html_e( 'Price', 'yith-woocommerce-recover-abandoned-cart' ); ?></th>
		</tr>
		</thead>
		<tbody>
			<?php echo wc_get_email_order_items( $order, false ); //phpcs:ignore ?>
		</tbody>
		<tfoot>
		<?php
		if ( $totals = $order->get_order_item_totals() ) {
			$i = 0;

			foreach ( $totals as $total ) {
				$i++;
				?>
				<tr>
				<th scope="row" colspan="2"
					style="text-align:left; border: 1px solid #eee; 
					<?php
					if ( $i == 1 ) {
						echo 'border-top-width: 4px;';}
					?>
					"><?php echo wp_kses_post( $total['label'] ); ?></th>
				<td style="text-align:left; border: 1px solid #eee; 
				<?php
				if ( $i == 1 ) {
					echo 'border-top-width: 4px;';}
				?>
				"><?php echo wp_kses_post( $total['value'] ); ?></td>
				</tr>
				<?php
			}
		}
		?>
		</tfoot>
	</table>

<?php do_action( 'woocommerce_email_after_order_table', $order, true, false ); ?>

<?php do_action( 'woocommerce_email_order_meta', $order, true, false ); ?>

<?php do_action( 'woocommerce_email_customer_details', $order, $sent_to_admin, $plain_text ); ?>


<?php
do_action( 'woocommerce_email_footer', $email );
?>
