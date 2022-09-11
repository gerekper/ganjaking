<?php //phpcs:ignore WordPress.Files.FileName.NotHyphenatedLowercase
/**
 * Metabox for Subscription History
 *
 * @package YITH WooCommerce Subscription
 * @since   1.0.0
 * @author  YITH
 *
 * @var YWSBS_Subscription $subscription Current subscription.
 * @var array              $history Current subscription.
 */

if ( ! defined( 'ABSPATH' ) || ! defined( 'YITH_YWSBS_VERSION' ) ) {
	exit; // Exit if accessed directly.
}
?>

<div class="subscription_history">
	<?php if ( ! $history ) : ?>
		<p><?php esc_html_e( 'No orders found for this subscription.', 'yith-woocommerce-subscription' ); ?></p>
	<?php else : ?>

		<table class="ywsbs-history-table widefat striped">
			<thead>
			<tr>
				<th><?php esc_html_e( 'List of related order:', 'yith-woocommerce-subscription' ); ?></th>
				<th></th>
				<th><?php esc_html_e( 'Date:', 'yith-woocommerce-subscription' ); ?></th>
				<th><?php esc_html_e( 'Status:', 'yith-woocommerce-subscription' ); ?></th>
				<th><?php esc_html_e( 'Paid on:', 'yith-woocommerce-subscription' ); ?></th>
				<th><?php esc_html_e( 'Amount', 'yith-woocommerce-subscription' ); ?></th>
			</tr>
			</thead>
			<tbody>
			<?php foreach ( $history as $current_order ) : ?>
				<tr>
					<td>
						<a href="
						<?php
						echo esc_url(
							add_query_arg(
								array(
									'post'   => $current_order->get_id(),
									'action' => 'edit',
								),
								admin_url( 'post.php' )
							)
						);
						?>
									"><?php echo esc_html( '#' . $current_order->get_id() ); ?></a>
					</td>
					<td>
						<?php echo esc_html( ywsbs_subscription_order_type( $subscription, $current_order ) ); ?>
					</td>
					<td>
						<?php echo $current_order->get_date_created()->date_i18n( apply_filters( 'woocommerce_admin_order_date_format', __( 'M j, Y', 'yith-woocommerce-subscription' ) ) ); //phpcs:ignore ?>
						</td>
					<td>
						<?php printf( '<mark class="order-status %s"><span>%s</span></mark>', esc_attr( sanitize_html_class( 'status-' . $current_order->get_status() ) ), esc_html( wc_get_order_status_name( $current_order->get_status() ) ) ); ?>
					</td>
					<td>
						<?php
						$paid_date = empty( $current_order->get_date_paid() ) ? '-' : wc_format_datetime( $current_order->get_date_paid() );
						echo esc_html( $paid_date );
						?>
					</td>
					<td>
						<?php echo wc_price( $current_order->get_total(), array( 'currency' => $current_order->get_currency() ) ); //phpcs:ignore ?>
					</td>
				</tr>
			<?php endforeach; ?>
			</tbody>

		</table>
	<?php endif; ?>
</div>
