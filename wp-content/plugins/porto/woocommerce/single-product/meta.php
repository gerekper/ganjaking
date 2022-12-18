<?php
/**
 * Single Product Meta
 *
 * @version     3.0.0
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

global $product, $porto_settings;
?>
<div class="product_meta">

	<?php do_action( 'woocommerce_product_meta_start' ); ?>

	<?php if ( in_array( 'sku', $porto_settings['product-metas'] ) && wc_product_sku_enabled() && ( $product->get_sku() || $product->is_type( 'variable' ) ) ) : ?>

		<span class="sku_wrapper"><?php esc_html_e( 'SKU:', 'woocommerce' ); ?> <span class="sku"><?php echo ! empty( $sku = $product->get_sku() ) ? esc_html( $sku ) : esc_html__( 'N/A', 'woocommerce' ); ?></span></span>

	<?php endif; ?>

	<?php
	if ( in_array( 'cats', $porto_settings['product-metas'] ) ) :
		echo wc_get_product_category_list( $product->get_id(), ', ', '<span class="posted_in">' . _n( 'Category:', 'Categories:', count( $product->get_category_ids() ), 'woocommerce' ) . ' ', '</span>' );
	endif;
	?>

	<?php
	if ( in_array( 'tags', $porto_settings['product-metas'] ) ) :
		echo wc_get_product_tag_list( $product->get_id(), ', ', '<span class="tagged_as">' . _n( 'Tag:', 'Tags:', count( $product->get_tag_ids() ), 'woocommerce' ) . ' ', '</span>' );
	endif;
	?>

	<?php do_action( 'woocommerce_product_meta_end' ); ?>

</div>
