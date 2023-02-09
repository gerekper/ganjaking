<?php
/**
 * Admin View: Cart Items Meta Box
 *
 * @var array $order_items
 * @var string $subtotal
 * @var int $number_items
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

?>

<div class="woocommerce_cart_reports_items_wrapper">
	<?php if ( count( $order_items ) === 0 ) : ?>
		<span style="color:gray;">No Products In The Cart</span>
		<?php av8_tooltip( __(
				'When a customer adds items to a cart, then abandons the cart for a considerable amount of time, the browser often deletes the cart data. The cart still belongs to the customer, but their browser removed the products. :( But hey! This indicates that they came back. And might be ready to purchase. ',
				'woocommerce_cart_reports '
			)
		); ?>

	<?php else: ?>
		<table cellpadding="0" width="100%" cellspacing="0" class="woocommerce_cart_reports_items">
			<thead>
			<tr>
				<th class="thumb" width="60px" style="text-align:left;"></th>
				<th class="sku" style="text-align:left">
					<?php _e( 'SKU', 'woocommerce_cart_reports' ); ?>
				</th>
				<th class="name" style="text-align:left">
					<?php _e( 'Name', 'woocommerce_cart_reports' ); ?>
				</th>
				<th class="price" style="text-align:right">
					<?php _e( 'Price', 'woocommerce_cart_reports' ); ?>
				</th>
				<th class="quantity" style="text-align:right">
					<?php _e( 'Qty', 'woocommerce_cart_reports' ); ?>
				</th>
			</tr>
			</thead>
			<tbody id="cart_items_list">

			<?php
			$loop = 0;
			foreach ( $order_items as $item ) :
				$_product = wc_get_product( $item['product_id'] );

				if ( $loop % 2 == 0 ) {
					$table_color = ' td1 ';
				} else {
					$table_color = ' td2 ';
				}

				$tip = '<strong>' . __( 'Product ID:', 'woocommerce_cart_reports' ) . '</strong> ' . $_product->get_id();
				$tip .= '<br/><strong>' . __( 'Variation ID:', 'woocommerce_cart_reports' ) . '</strong>';

				if ( isset( $item['variation_id'] ) && $item['variation_id'] > 0 ) {
					$tip .= $item['variation_id'];
				} else {
					$tip .= '-';
				}

				$tip .= '<br/><strong>' . __( 'Product SKU:', 'woocommerce_cart_reports' ) . '</strong>';
				if ( $_product->get_sku() ) {
					$tip .= $_product->get_sku();
				} else {
					$tip .= '-';
				}
				?>

				<?php if ( ! isset( $_product ) ) : ?>
				<p>Product not found</p>
			<?php endif; ?>

				<tr class="item <?php echo $table_color; ?>" rel="<?php echo $loop; ?>">
					<td class="thumb">
						<a
							href="<?php esc_attr_e( admin_url( 'post.php?post=' . $_product->get_id() . '&action=edit' ) ); ?>"
							class="help_tip cart-product-thumbnail"
							data-tip="<?php esc_attr_e( $tip ); ?>"
						>
							<?php echo $_product->get_image(); ?>
						</a>
					</td>
					<td class="sku">
						<?php
						if ( $_product->get_sku() ) {
							echo $_product->get_sku();
						} else {
							echo '-';
						}
						?>
						<input
							type="hidden" class="item_id" name="item_id[<?php echo $loop; ?>]"
							value="<?php esc_attr_e( $item['id'] ?? '' ); ?>"
						/>
						<input
							type="hidden" name="item_name[<?php echo $loop; ?>]"
							value="<?php esc_attr_e( $item['id'] ?? '' ); ?>"
						/>
						<?php if ( isset( $item['variation_id'] ) ) : ?>
							<input
								type="hidden" name="item_variation[<?php echo $loop; ?>]"
								value="<?php echo esc_attr( $item['variation_id'] ); ?>"
							/>
						<?php endif; ?>
					</td>
					<td class="name">
						<a href="<?php esc_attr_e( admin_url( 'post.php?post=' . $item['product_id'] . '&action=edit' ) ); ?>"><strong><?php echo $_product->get_title(); ?></strong></a>

						<?php
						if (
							isset( $item['variation'] )
							&& is_array( $item['variation'] )
							&& count( $item['variation'] ) > 0
						) {
							$variation_data = wc_get_formatted_variation( $item['variation'] );
							echo '&nbsp;' . $variation_data;
						}
						?>

					</td>
					<td class="price" style="text-align: right">
						<p>
							<?php
							// if we have the properly filtered price, display it
							// otherwise fall back to the old method
							if ( isset( $item['price'] ) ) {
								echo $item['price'];
							} else {
								echo $_product->get_price_html();
							}
							?>
						</p>
					</td>

					<td class="quantity" style="text-align: right">
						<p>
							<?php echo $item['quantity']; ?>
						</p>
					</td>
				</tr>


				<?php $loop ++; endforeach; ?>
			</tbody>
			<?php if ( $subtotal ): ?>

				<tfoot>
				<tr>
					<th style="text-align: left"><strong><?php _e( 'Subtotal', 'woocommerce' ); ?></strong>
					</th>
					<th colspan="2"></th>
					<th style="text-align: right"><?php echo wc_price( $subtotal ); ?></th>
					<th style="text-align: right"><?php echo $number_items; ?></th>
				</tr>
				</tfoot>
			<?php endif; ?>
		</table>
	<?php endif; ?>
</div>

