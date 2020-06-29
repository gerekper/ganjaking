<?php
/**
 * My Pre-orders
 *
 * Shows the list of pre-order items on the account page
 *
 * @author  WooThemes
 * @package WC_Pre_Orders/Templates
 * @version 1.4.4
 */
?>

<?php if ( $show_title ) : ?>
	<h2><?php _e( 'My Pre-Orders', 'wc-pre-orders' ); ?></h2>
<?php endif; ?>

<?php if ( ! empty( $items ) ) : ?>
	<table class="shop_table my_account_pre_orders my_account_orders">

		<thead>
			<tr>
				<th class="pre-order-order-number"><span class="nobr"><?php _e( 'Order', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-title"><span class="nobr"><?php _e( 'Product', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-status"><span class="nobr"><?php _e( 'Status', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-release-date"><span class="nobr"><?php _e( 'Release Date', 'wc-pre-orders' ); ?></span></th>
				<th class="pre-order-actions"></th>
			</tr>
		</thead>

		<tbody>
			<?php foreach ( $items as $item ) :
					$order = $item['order'];
					$order_id = version_compare( WC_VERSION, '3.0', '<' ) ? $order->id : $order->get_id();
					$data  = $item['data'];
				?>
				<tr class="order">
					<td class="order-number" width="1%">
						<?php if ( method_exists( $order, 'get_view_order_url' ) ) : ?>
							<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
								#<?php echo $order->get_order_number(); ?>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( add_query_arg( 'order', $order_id, get_permalink( wc_get_page_id( 'view_order' ) ) ) ); ?>">
								<?php echo $order->get_order_number(); ?>
							</a>
						<?php endif; ?>
					</td>
					<td class="pre-order-title">
						<a href="<?php echo esc_attr( get_post_permalink( $data['product_id'] ) ); ?>">
							<?php echo $data['name']; ?>
						</a>
					</td>
					<td class="pre-order-status" style="text-align:left; white-space:nowrap;">
						<?php echo WC_Pre_Orders_Order::get_pre_order_status_to_display( $order ); ?>
					</td>
					<td class="pre-order-release-date">
						<?php echo WC_Pre_Orders_Product::get_localized_availability_date( $data['product_id'] ); ?>
					</td>
					<td class="pre-order-actions order-actions">
						<?php foreach( $actions[ $order_id ] as $key => $action ) : ?>
						<a href="<?php echo esc_url( $action['url'] ); ?>" class="button <?php echo sanitize_html_class( $key ) ?>"><?php echo esc_html( $action['name'] ); ?></a>
						<?php endforeach; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>

	</table>

<?php else : ?>

	<p><?php _e( 'You have no pre-orders.', 'wc-pre-orders' ); ?></p>

<?php endif;
