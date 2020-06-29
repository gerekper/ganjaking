<?php
/**
 * Popup product template
 *
 * @author  YITH
 * @package YITH WooCommerce Added to Cart Popup
 * @version 1.0.0
 */

if ( ! defined( 'YITH_WACP' ) ) {
	exit;
}

// Get cart.
$cart = WC()->cart->get_cart();
// Get current cart item.
$cart_item = WC()->cart->get_cart_item( $last_cart_item_key );
if ( ! $cart_item ) {
	foreach ( WC()->cart->get_cart_contents() as $key => $item ) {
		$p_id = $product->get_id();
		if ( $item['product_id'] === $p_id || $item['variation_id'] === $p_id ) {
			$cart_item = $item;
			break;
		}
	}
}

?>

<?php if ( $thumb && $product instanceof WC_Product ) : ?>
	<div class="product-thumb">
		<?php
		$thumbnail = $product->get_image( 'yith_wacp_image_size' );
		echo ! $product->is_visible() ? $thumbnail : sprintf( '<a href="%s">%s</a>', esc_url( $product->get_permalink() ), $thumbnail ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
		?>
	</div>
<?php endif; ?>

<?php if ( $product_info ) : ?>
	<div class="info-box">

		<div class="product-info">
			<h3 class="product-title">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>">
					<?php echo $product->get_name(); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped ?>
				</a>
			</h3>
			<span class="product-price">
			<?php
			if ( $product->is_type( 'yith_bundle' ) && class_exists( 'YITH_WCPB_Frontend_Premium' ) && $last_cart_item_key ) {
				$price      = YITH_WCPB_Frontend_Premium()->calculate_bundled_items_price_by_cart( $cart_item );
				$price_html = wc_price( $price );
			} elseif ( $product->is_type( 'gift-card' ) && $cart_item && isset( $cart_item['ywgc_amount'] ) ) {

				$price      = apply_filters( 'yith_ywgc_set_cart_item_price', $cart_item['ywgc_amount'], $cart_item );
				$price_html = wc_price( apply_filters( 'yith_ywgc_get_gift_card_price', $price ) );
			} elseif ( $product->is_type( 'grouped' ) ) {
				foreach ( $quantity as $key => $qty ) {
					if ( ! $qty ) {
						continue;
					}
					$product            = wc_get_product( $key );
					$tot_qty            = intval( $qty ) > 1 ? sprintf( '%s &times; ', $qty ) : '';
					$product_cart_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $product ), $cart_item, $last_cart_item_key );

					$price_html = '<b>' . $product->get_name() . '</b> ' . $tot_qty . $product_cart_price . '<br>';
				}
			} else {
				// Set quantity.
				$quantity           = $quantity > 1 ? sprintf( '%s &times; ', $quantity ) : '';
				$product_cart_price = apply_filters( 'woocommerce_cart_item_price', WC()->cart->get_product_price( $product ), $cart_item, $last_cart_item_key );
				$price_html         = $quantity . $product_cart_price;
			}

			echo $price_html; // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
			?>
		</span>
			<div class="product-variation">

				<?php
				if ( $product->is_type( 'variation' ) && get_option( 'yith-wacp-show-product-variation', 'yes' ) === 'yes' ) :
					$variation_id = is_callable( array( $product, 'get_id' ) ) ? $product->get_id() : $product->variation_id;
					?>
					<div class="product-variation">
						<?php
						if ( isset( $cart_item['variation_id'] ) && $cart_item['variation_id'] === $variation_id ) {
							echo yith_wacp_get_formatted_cart_item_data( $cart_item ); // phpcs:ignore WordPress.Security.EscapeOutput.OutputNotEscaped
						}
						?>
					</div>
				<?php endif; ?>
			</div>
		</div>

		<?php do_action( 'yith_wacp_add_cart_info', $product ); ?>

	</div>
<?php endif; ?>
