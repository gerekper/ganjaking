<?php
/**
 * My Pre-orders
 *
 * Shows the list of pre-order items on the account page
 *
 * @package WC_Pre_Orders/Templates
 * @version 1.4.4
 */
?>

<?php if ( ! empty( $items ) ) : ?>
	<table class="shop_table my_account_pre_orders shop_table_responsive my_account_orders">

		<thead>
			<tr>
				<th class="pre-order-order-number"><span class="nobr"><?php echo esc_html__( 'Order', 'woocommerce-pre-orders' ); ?></span></th>
				<th class="pre-order-title"><span class="nobr"><?php echo esc_html__( 'Product', 'woocommerce-pre-orders' ); ?></span></th>
				<th class="pre-order-status"><span class="nobr"><?php echo esc_html__( 'Status', 'woocommerce-pre-orders' ); ?></span></th>
				<th class="pre-order-release-date"><span class="nobr"><?php echo esc_html__( 'Release date', 'woocommerce-pre-orders' ); ?></span></th>
				<th class="pre-order-actions"></th>
			</tr>
		</thead>

		<tbody>
			<?php
			foreach ( $items as $item ) :
					$preorder = $item['order'];
					$order_id = $preorder->get_id();
					$data     = $item['data'];
				?>
				<tr class="order">
					<td class="order-number" data-title="<?php echo esc_attr__( 'Order', 'woocommerce-pre-orders' ); ?>">
						<?php if ( method_exists( $preorder, 'get_view_order_url' ) ) : ?>
							<a href="<?php echo esc_url( $preorder->get_view_order_url() ); ?>">
								#<?php echo esc_html( $preorder->get_order_number() ); ?>
							</a>
						<?php else : ?>
							<a href="<?php echo esc_url( add_query_arg( 'order', $order_id, get_permalink( wc_get_page_id( 'view_order' ) ) ) ); ?>">
								<?php echo esc_html( $preorder->get_order_number() ); ?>
							</a>
						<?php endif; ?>
					</td>
						<td class="pre-order-title" data-title="<?php echo esc_attr__( 'Product', 'woocommerce-pre-orders' ); ?>">
						<a href="<?php echo esc_attr( get_post_permalink( $data['product_id'] ) ); ?>">
							<?php echo wp_kses_post( $data['name'] ); ?>
						</a>
					</td>
					<td class="pre-order-status" data-title="<?php echo esc_attr__( 'Status', 'woocommerce-pre-orders' ); ?>">
						<?php
						echo esc_html(
							WC_Pre_Orders_Order::get_pre_order_status_to_display( $preorder )
						);
						?>
					</td>
					<td class="pre-order-release-date" data-title="<?php echo esc_attr__( 'Release date', 'woocommerce-pre-orders' ); ?>">
						<?php
						echo esc_html(
							WC_Pre_Orders_Product::get_localized_availability_date( $data['product_id'] )
						);
						?>
					</td>
					<td class="pre-order-actions order-actions">
						<?php foreach ( $actions[ $order_id ] as $key => $action ) : // phpcs:ignore WordPress.WP.GlobalVariablesOverride.Prohibited ?>
						<a href="<?php echo esc_url( $action['url'] ); ?>" class="button <?php echo sanitize_html_class( $key ); ?>"><?php echo esc_html( $action['name'] ); ?></a>
						<?php endforeach; ?>
					</td>
				</tr>
			<?php endforeach; ?>
		</tbody>

	</table>

<?php else : ?>

	<p><?php esc_html_e( 'You have no pre-orders.', 'woocommerce-pre-orders' ); ?></p>

	<?php
endif;
