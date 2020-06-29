<?php
/**
 * Popup cart template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
}

?>

	<h3 class="cart-list-title"><?php echo esc_html( apply_filters( 'yith_wacp_cart_popup_title', __( 'Your Cart', 'yith-woocommerce-added-to-cart-popup' ) ) ); ?></h3>

	<table class="cart-list">
		<tbody>
		<?php
		foreach ( WC()->cart->get_cart() as $item_key => $item ) :
			$_product = apply_filters( 'woocommerce_cart_item_product', $item['data'], $item, $item_key );

			if ( $_product && $_product->exists() && $item['quantity'] > 0 && apply_filters( 'woocommerce_cart_item_visible', true, $item, $item_key ) ) :
				$product_permalink = apply_filters( 'woocommerce_cart_item_permalink', $_product->is_visible() ? $_product->get_permalink( $item ) : '', $item, $item_key );
				?>
				<tr class="<?php echo esc_attr( apply_filters( 'woocommerce_cart_item_class', 'single-cart-item', $item, $item_key ) ); ?>">

					<td class="item-remove">
						<?php
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_cart_item_remove_link',
							sprintf(
								'<a href="%s" class="remove yith-wacp-remove-cart" aria-label="%s" data-item_key="%s">X</a>',
								esc_url( yith_wacp_get_cart_remove_url( $item_key ) ),
								__( 'Remove item', 'yith-woocommerce-added-to-cart-popup' ),
								$item_key
							),
							$item_key
						);
						?>
					</td>

					<?php if ( $thumb ) : ?>
						<td class="item-thumb">
							<?php
							$thumbnail = apply_filters( 'woocommerce_cart_item_thumbnail', $_product->get_image(), $item, $item_key );
							echo ! $product_permalink ? $thumbnail : sprintf( '<a href="%s">%s</a>', esc_url( $product_permalink ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							?>
						</td>
					<?php endif; ?>

					<td class="item-info">
						<?php
						// Print the name.
						$_product_name = is_callable( array( $_product, 'get_name' ) ) ? $_product->get_name() : $_product->get_title();
						if ( $_product->is_visible() ) {
							$_product_name_html = '<a class="item-name" href="' . esc_url( $_product->get_permalink() ) . '">' . $_product_name . '</a>';
						} else {
							$_product_name_html = '<span class="item-name">' . $_product_name . '</span>';
						}
						echo apply_filters( 'woocommerce_cart_item_name', $_product_name_html, $item, $item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						// Meta data.
						echo yith_wacp_get_formatted_cart_item_data( $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped

						?>
					</td>

					<td class="item-price">
						<?php
						echo apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $_product ), $item, $item_key ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</td>

					<td class="item-quantity">
						<?php
						if ( $_product->is_sold_individually() ) {
							$product_quantity = '1';
						} else {
							$product_quantity = woocommerce_quantity_input(
								array(
									'input_name'   => "[{$item_key}][qty]",
									'input_value'  => $item['quantity'],
									'max_value'    => $_product->get_max_purchase_quantity(),
									'min_value'    => '0',
									'product_name' => $_product->get_name(),
								),
								$_product,
								false
							);
						}

						echo apply_filters( 'woocommerce_cart_item_quantity', $product_quantity, $item_key, $item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						?>
					</td>

					<td class="item-subtotal">
						<?php
						echo apply_filters( // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
							'woocommerce_cart_item_subtotal',
							WC()->cart->get_product_subtotal( $_product, $item['quantity'] ),
							$item,
							$item_key
						);
						?>
					</td>
				</tr>
				<?php
			endif;
		endforeach;
		?>
		</tbody>
	</table>

<?php
do_action( 'yith_wacp_add_cart_info' );
