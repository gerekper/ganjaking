<?php
/**
 * @version 1.0.0
 */
 
if ( ! defined( 'ABSPATH' ) ) {
	exit; // Exit if accessed directly
}

$cat_count = get_the_terms( $product->get_id(), 'product_cat' );
$cat_count = is_array( $cat_count ) ? sizeof( $cat_count ) : 0;
$tag_count = get_the_terms( $product->get_id(), 'product_tag' );
$tag_count = is_array( $tag_count ) ? sizeof( $tag_count ) : 0;

if ( version_compare( WC_VERSION, '3.0', '<' ) ) {
	$categories = $product->get_categories( ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $cat_count, 'woocommerce-store-catalog-pdf-download' ) . ' ', '.</span>' );
	$tags = $product->get_tags( ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $tag_count, 'woocommerce-store-catalog-pdf-download' ) . ' ', '.</span>' ); 
} else {
	$categories = wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', $cat_count, 'woocommerce-store-catalog-pdf-download' ) . ' ', '.</span>' );
	$tags = wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', $tag_count, 'woocommerce-store-catalog-pdf-download' ) . ' ', '.</span>' );
}

?>
<div class="product-meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php _e( 'SKU:', 'woocommerce-store-catalog-pdf-download' ); ?> <span class="sku" itemprop="sku"><?php echo ( $sku = $product->get_sku() ) ? $sku : __( 'N/A', 'woocommerce-store-catalog-pdf-download' ); ?></span>.</span>

	<?php endif; ?>

	<?php echo $categories; ?>

	<?php echo $tags; ?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
