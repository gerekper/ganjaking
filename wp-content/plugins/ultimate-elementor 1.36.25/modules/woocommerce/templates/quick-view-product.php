<?php
/**
 * WooCommerce - Quick View Product
 *
 * @package UAEL
 */

while ( have_posts() ) :
	the_post();

	$wp_post_id = get_the_ID();

	if ( ! $wp_post_id || ! in_array( get_post_type( $wp_post_id ), array( 'product', 'product_variation' ), true ) ) {
		return $classes;
	}

	$product = wc_get_product( $wp_post_id );

	if ( $product ) {
		$classes[] = 'product';
		$classes[] = wc_get_loop_class();
		$classes[] = $product->get_stock_status();

		if ( $product->is_on_sale() ) {
			$classes[] = 'sale';
		}
		if ( $product->is_featured() ) {
			$classes[] = 'featured';
		}
		if ( $product->is_downloadable() ) {
			$classes[] = 'downloadable';
		}
		if ( $product->is_virtual() ) {
			$classes[] = 'virtual';
		}
		if ( $product->is_sold_individually() ) {
			$classes[] = 'sold-individually';
		}
		if ( $product->is_taxable() ) {
			$classes[] = 'taxable';
		}
		if ( $product->is_shipping_taxable() ) {
			$classes[] = 'shipping-taxable';
		}
		if ( $product->is_purchasable() ) {
			$classes[] = 'purchasable';
		}
		if ( $product->get_type() ) {
			$classes[] = 'product-type-' . $product->get_type();
		}
		if ( $product->is_type( 'variable' ) ) {
			if ( ! $product->get_default_attributes() ) {
				$classes[] = 'has-default-attributes';
			}
			if ( $product->has_child() ) {
				$classes[] = 'has-children';
			}
		}
	}

	$key = array_search( 'hentry', $classes, true );
	if ( false !== $key ) {
		unset( $classes[ $key ] );
	}
	?>
<div class="uael-woo-product">
	<div id="product-<?php echo esc_attr( $wp_post_id ); ?>" class="<?php echo esc_attr( implode( ' ', $classes ) ); ?>">
		<?php do_action( 'uael_woo_quick_view_product_image' ); ?>
		<div class="summary entry-summary">
			<div class="summary-content">
				<?php do_action( 'uael_woo_quick_view_product_summary' ); ?>
			</div>
		</div>
	</div>
</div>
	<?php
endwhile; // end of the loop.
