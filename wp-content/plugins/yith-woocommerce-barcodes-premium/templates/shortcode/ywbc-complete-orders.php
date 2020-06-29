<?php
$my_orders_columns = apply_filters( 'yith_ywbc_search_orders_columns', array(
	'order-number' => esc_html__( 'Order', 'yith-woocommerce-barcodes' ),
	'order-date'   => esc_html__( 'Date', 'yith-woocommerce-barcodes' ),
	'order-status' => esc_html__( 'Status', 'yith-woocommerce-barcodes' ),
	'order-total'  => esc_html__( 'Total', 'yith-woocommerce-barcodes' ),
	'barcode'      => esc_html__( 'Code', 'yith-woocommerce-barcodes' ),
	/*'view'         => esc_html__( 'View', 'yith-woocommerce-barcodes' ),*/
) );

if ( $posts ) :
	?>
	
	<div></div>
	<h3><?php esc_html_e( 'Order list', 'yith-woocommerce-barcodes' ); ?></h3>
	<span><?php esc_html_e( 'The following orders have been set to completed', 'yith-woocommerce-barcodes' ); ?></span>
	
	<table class="shop_table shop_table_responsive ywbc-search-by-orders">
		<thead>
		<tr>
			<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
				<th class="<?php echo esc_attr( $column_id ); ?>">
					<span class="nobr"><?php echo esc_html( $column_name ); ?></span></th>
			<?php endforeach; ?>
		</tr>
		</thead>
		
		<tbody>
		<?php foreach ( $posts as $post ) :
			$order = wc_get_order( $post );
			$item_count = $order->get_item_count();
			
			?>
			<tr class="order">
				<?php foreach ( $my_orders_columns as $column_id => $column_name ) : ?>
					<td class="<?php echo esc_attr( $column_id ); ?>" data-title="<?php echo esc_attr( $column_name ); ?>">
						<?php if ( has_action( 'woocommerce_my_account_my_orders_column_' . $column_id ) ) : ?>
							<?php do_action( 'woocommerce_my_account_my_orders_column_' . $column_id, $order ); ?>
						
						<?php elseif ( 'order-number' === $column_id ) : ?>
							<a href="<?php echo esc_url( $order->get_view_order_url() ); ?>">
								<?php echo esc_html_x( '#', 'hash tag before order number', 'yith-woocommerce-barcodes' ) . $order->get_order_number(); ?>
							</a>
						
						<?php elseif ( 'order-date' === $column_id ) :
							$order_date = yit_get_prop( $order, 'order_date' ); ?>
							<time datetime="<?php echo date( 'Y-m-d', strtotime( $order_date ) ); ?>" title="<?php echo esc_attr( strtotime( $order_date ) ); ?>"><?php echo date_i18n( get_option( 'date_format' ), strtotime( $order_date ) ); ?></time>
						
						<?php elseif ( 'order-status' === $column_id ) : ?>
							<?php echo wc_get_order_status_name( $order->get_status() ); ?>
						
						<?php elseif ( 'order-total' === $column_id ) : ?>
							<?php echo sprintf( _n( '%s for %s item', '%s for %s items', $item_count, 'yith-woocommerce-barcodes' ), $order->get_formatted_order_total(), $item_count ); ?>
						
						<?php elseif ( 'barcode' === $column_id ) : ?>
							<?php echo YITH_Barcode::get( yit_get_prop( $order, 'id' ) )->get_display_value(); ?>
						
						<?php elseif ( 'view' === $column_id ) : ?>
							<?php
							echo '<a href="' . esc_url( $order->get_view_order_url() ) . '" class="button">' . esc_html__( 'View', 'yith-woocommerce-barcodes' ) . '</a>';
							
							?>
						<?php endif; ?>
					</td>
				<?php endforeach; ?>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
<?php else: ?>
	<span><?php esc_html_e( 'No order matches the selected criteria', 'yith-woocommerce-barcodes' ); ?></span>

<?php endif;