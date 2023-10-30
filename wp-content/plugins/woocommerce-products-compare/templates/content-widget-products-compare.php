<?php
/**
 * Template: Products compare widget.
 *
 * @package WC_Products_Compare/Templates
 * @version 1.3.0
 */

defined( 'ABSPATH' ) || exit;

/**
 * Template vars.
 *
 * @var WC_Product[] $products    The products to compare.
 * @var string       $compare_url The URL to the products compare page.
 */
if ( $products ) : ?>
	<ul>
		<?php foreach ( $products as $product ) : ?>
			<?php $product_name = $product->get_name( 'edit' ); // Use the context 'edit' to get the unfiltered name. ?>
			<li data-product-id="<?php echo esc_attr( $product->get_id() ); ?>">
				<a href="<?php echo esc_url( $product->get_permalink() ); ?>" title="<?php echo esc_attr( $product_name ); ?>" class="product-link">
					<?php echo wp_kses_post( $product->get_image( 'shop_thumbnail' ) ); ?>
					<h3><?php echo esc_html( $product_name ); ?></h3>
				</a>
				<a href="#" title="<?php esc_attr_e( 'Remove Product', 'woocommerce-products-compare' ); ?>" class="remove-compare-product" data-remove-id="<?php echo esc_attr( $product->get_id() ); ?>">
					<?php esc_html_e( 'Remove Product', 'woocommerce-products-compare' ); ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<a href="#" title="<?php esc_attr_e( 'Remove all products', 'woocommerce-products-compare' ); ?>" class="woocommerce-products-compare-remove-all-products">
		<?php esc_html_e( 'Remove all products', 'woocommerce-products-compare' ); ?>
	</a>
<?php else : ?>
	<p class="no-products">
		<?php esc_html_e( 'Add some products to compare.', 'woocommerce-products-compare' ); ?>
	</p>
<?php endif; ?>

<a href="<?php echo esc_url( $compare_url ); ?>" title="<?php esc_attr_e( 'Compare Products', 'woocommerce-products-compare' ); ?>" class="button woocommerce-products-compare-widget-compare-button">
	<?php esc_html_e( 'Compare Products', 'woocommerce-products-compare' ); ?>
</a>
