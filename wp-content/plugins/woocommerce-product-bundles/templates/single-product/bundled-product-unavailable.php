<?php
/**
 * Simple Bundled Product template
 *
 * Override this template by copying it to 'yourtheme/woocommerce/single-product/bundled-product-simple.php'.
 *
 * On occasion, this template file may need to be updated and you (the theme developer) will need to copy the new files to your theme to maintain compatibility.
 * We try to do this as little as possible, but it does happen.
 * When this occurs the version of the template file will be bumped and the readme will list any important changes.
 *
 * @version 5.12.1
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_out_of_stock = isset( $custom_product_data[ 'is_out_of_stock' ] ) && 'yes' === $custom_product_data[ 'is_out_of_stock' ];

?><div class="cart unavailable_item" data-title="<?php echo esc_attr( $bundled_item->get_title() ); ?>" data-product_variations="<?php echo htmlspecialchars( json_encode( array() ) ); ?>" data-product_title="<?php echo esc_attr( $bundled_item->get_product()->get_title() ); ?>" data-visible="<?php echo $bundled_item->is_visible() ? 'yes' : 'no'; ?>" data-optional_suffix="<?php echo $bundled_item->is_optional() && $bundle->contains( 'mandatory' ) ? apply_filters( 'woocommerce_bundles_optional_bundled_item_suffix', __( 'optional', 'woocommerce-product-bundles' ), $bundled_item, $bundle ) : ''; ?>" data-optional="<?php echo $bundled_item->is_optional() ? 'yes' : 'no'; ?>" data-type="<?php echo $bundled_item->get_product()->get_type(); ?>" data-bundled_item_id="<?php echo $bundled_item->get_id(); ?>" data-custom_data="<?php echo esc_attr( json_encode( $custom_product_data ) ); ?>" data-product_id="<?php echo $bundled_item->get_product()->get_id(); ?>" data-bundle_id="<?php echo $bundle->get_id(); ?>">
	<div class="bundled_item_wrap">
		<div class="bundled_item_cart_content">
			<div class="bundled_item_cart_details">
				<p class="bundled_item_unavailable <?php echo $is_out_of_stock ? 'stock out-of-stock' : ''; ?>"><?php
					echo $is_out_of_stock ? __( 'Out of stock', 'woocommerce' ) : __( 'Temporarily unavailable', 'woocommerce-product-bundles' );
				?></p>
			</div>
		</div>
	</div>
</div>
