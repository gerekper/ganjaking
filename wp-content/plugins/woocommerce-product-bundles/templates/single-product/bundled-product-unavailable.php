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
 * @version 6.21.0
 */

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

$is_out_of_stock = isset( $custom_product_data[ 'is_out_of_stock' ] ) && 'yes' === $custom_product_data[ 'is_out_of_stock' ];

?><div class="cart unavailable_item" data-title="<?php echo esc_attr( $bundled_item->get_title() ); ?>" data-product_variations="<?php echo wc_esc_json( json_encode( array() ) ); ?>" data-product_title="<?php echo esc_attr( $bundled_item->get_product()->get_title() ); ?>" data-visible="<?php echo $bundled_item->is_visible() ? 'yes' : 'no'; ?>" data-optional_suffix="<?php echo esc_attr( $bundled_item->get_optional_suffix() ); ?>" data-optional="<?php echo $bundled_item->is_optional() ? 'yes' : 'no'; ?>" data-type="<?php echo esc_attr( $bundled_item->get_product()->get_type() ); ?>" data-bundled_item_id="<?php echo esc_attr( $bundled_item->get_id() ); ?>" data-custom_data="<?php echo wc_esc_json( json_encode( $custom_product_data ) ); ?>" data-product_id="<?php echo esc_attr( $bundled_item->get_product()->get_id() ); ?>" data-bundle_id="<?php echo esc_attr( $bundle->get_id() ); ?>">
	<div class="bundled_item_wrap">
		<div class="bundled_item_cart_content">
			<div class="bundled_item_cart_details">
				<p class="bundled_item_unavailable <?php echo $is_out_of_stock ? 'stock out-of-stock' : ''; ?>"><?php
					echo $is_out_of_stock ? esc_html__( 'Out of stock', 'woocommerce' ) : esc_html__( 'Temporarily unavailable', 'woocommerce-product-bundles' );
					if ( $bundled_item->is_limited_subscription() && $bundled_item->user_has_subscription() ) {
						?><p class="limited-subscription-notice notice"><?php esc_html_e( 'You have an active subscription to this product already.', 'woocommerce-subscriptions' ); ?></p><?php
					}
				?></p>
			</div>
		</div>
	</div>
</div>
